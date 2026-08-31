<?php
  require __DIR__ . '/admin_bootstrap.php';

  $raw = file_get_contents('php://input');
  error_log('[application_update] 原始 body：' . $raw);
  $body = json_decode($raw, true);

  $id     = (int)($body['book_ap_id'] ?? 0);
  $title  = trim($body['ap_title'] ?? '');
  $author = trim($body['ap_author'] ?? '');

  $isbn = $body['isbn'] ?? '';
  $isbn = str_replace('-', '', $isbn);
  $isbn = str_replace(' ', '', $isbn);

  $bookUrl = trim($body['book_url'] ?? '');
  $bookUrl = $bookUrl === '' ? null : $bookUrl;

  if ($id === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '系統忙碌中，請稍後再試。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  if ($title === '' || $author === '' || $isbn === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '書名、作者、ISBN 為必填。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $check = $pdo->prepare("SELECT ap_status FROM book_application_form WHERE book_ap_id = ?");
    $check->execute([$id]);
    $application = $check->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => '找不到這筆申請，可能已經被處理掉了。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    if ($application['ap_status'] !== '待處理') {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '這筆申請已經處理過了，不能再修改內容。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    $dup = $pdo->prepare("SELECT book_ap_id FROM book_application_form WHERE isbn = ? AND book_ap_id != ?");
    $dup->execute([$isbn, $id]);

    if ($dup->fetch()) {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '這組 ISBN 已經有另一筆申請了。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    $stmt = $pdo->prepare(
      "UPDATE book_application_form
          SET ap_title = ?, ap_author = ?, isbn = ?, book_url = ?
        WHERE book_ap_id = ?"
    );
    $stmt->execute([$title, $author, $isbn, $bookUrl, $id]);

    echo json_encode(['success' => true, 'isbn' => $isbn], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[admin_application_update] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '儲存失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
