<?php
  require __DIR__ . '/admin_bootstrap.php';

  $raw = file_get_contents('php://input');
  error_log('[application_reopen] 原始 body：' . $raw);
  $body = json_decode($raw, true);

  $id = (int)($body['book_ap_id'] ?? 0);

  if ($id === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '系統忙碌中，請稍後再試。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $check = $pdo->prepare("SELECT ap_status FROM book_application_form WHERE book_ap_id = ?");
    $check->execute([$id]);
    $application = $check->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => '找不到這筆申請。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    if ($application['ap_status'] !== '已駁回') {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '只有已駁回的申請可以重新審核。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    $stmt = $pdo->prepare(
      "UPDATE book_application_form
          SET ap_status = '待處理', reject_reason = NULL, staff_account = NULL, resolved_at = NULL
        WHERE book_ap_id = ?"
    );
    $stmt->execute([$id]);

    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[admin_application_reopen] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '操作失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
