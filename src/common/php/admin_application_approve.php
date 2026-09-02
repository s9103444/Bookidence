<?php
  require __DIR__ . '/admin_bootstrap.php';

  $raw = file_get_contents('php://input');
  error_log('[application_approve] 原始 body：' . $raw);
  $body = json_decode($raw, true);

  $id          = (int)($body['book_ap_id'] ?? 0);
  $publisher   = trim($body['publisher'] ?? '');
  $description = trim($body['description'] ?? '');
  $categories  = $body['categories'] ?? [];

  $pDate = trim($body['p_date'] ?? '');
  $pDate = $pDate === '' ? null : $pDate;

  $bcImage = trim($body['bc_image'] ?? '');
  $bcImage = $bcImage === '' ? null : $bcImage;

  if ($id === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '系統忙碌中，請稍後再試。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  if ($publisher === '' || $description === '' || $pDate === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '出版社、出版日期、書籍簡介為必填。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  if (count($categories) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請至少選一個分類。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $check = $pdo->prepare(
      "SELECT user_id, isbn, ap_title, ap_author, ap_status
         FROM book_application_form WHERE book_ap_id = ?"
    );
    $check->execute([$id]);
    $application = $check->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => '找不到這筆申請。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    if ($application['ap_status'] !== '待處理') {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '這筆申請已經處理過了。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    $exists = $pdo->prepare("SELECT book_id FROM book WHERE isbn = ?");
    $exists->execute([$application['isbn']]);

    if ($exists->fetch()) {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '這組 ISBN 已經在書庫裡了，請改用駁回並註明原因。'], JSON_UNESCAPED_UNICODE);
      exit();
    }


    $pdo->beginTransaction();


    // ---------- ① 產生書籍編號 ----------
    $max = $pdo->query("SELECT MAX(book_display_id) FROM book")->fetchColumn();
    $next = $max ? (int)substr($max, 2) + 1 : 1;
    $displayId = 'BK' . str_pad($next, 8, '0', STR_PAD_LEFT);


    // ---------- ② 建一本書，直接上架 ----------
    $insertBook = $pdo->prepare(
      "INSERT INTO book (book_display_id, title, author, isbn, publisher, description, p_date, b_status, bc_image)
       VALUES (?,?,?,?,?,?,?,'已上架',?)"
    );

    $insertBook->execute([$displayId,$application['ap_title'],$application['ap_author'],$application['isbn'],$publisher,$description,$pDate,$bcImage]);

    $bookId = (int)$pdo->lastInsertId();


    // ---------- ③ 掛分類 ----------
    $placeholders = implode(',', array_fill(0, count($categories), '?'));

    $catStmt = $pdo->prepare("SELECT bcg_id FROM book_category WHERE bcg_name IN ($placeholders)");
    $catStmt->execute($categories);
    $categoryIds = $catStmt->fetchAll(PDO::FETCH_COLUMN);

    $link = $pdo->prepare("INSERT INTO book_categorys (book_id, bcg_id) VALUES (?, ?)");

    foreach ($categoryIds as $bcgId) {
      $link->execute([$bookId, $bcgId]);
    }


    // ---------- ④ 申請單標記已核准 ----------
    $mark = $pdo->prepare(
      "UPDATE book_application_form
          SET ap_status = '已核准', staff_account = ?, resolved_at = NOW()
        WHERE book_ap_id = ?"
    );
    $mark->execute([$staff['staff_account'], $id]);


    // ---------- ⑤ 通知申請人 ----------
    $content = "您推薦的《{$application['ap_title']}》已通過審核，現在可以在書庫看到它了。";

    $notify = $pdo->prepare(
      "INSERT INTO notification (user_id, type, notifi_title, content)
       VALUES (?, 'SYSTEM_MESSAGE', ?, ?)"
    );
    $notify->execute([$application['user_id'], '你推薦的書籍已上架', $content]);


    $pdo->commit();

    echo json_encode([
      'success'         => true,
      'book_id'         => $bookId,
      'book_display_id' => $displayId,
      'staff_name'      => $staff['staff_name'],
      'staff_account'   => $staff['staff_account'],
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    error_log('[admin_application_approve] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '核准失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
