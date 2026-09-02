<?php
  require __DIR__ . '/admin_bootstrap.php';

  $raw = file_get_contents('php://input');
  error_log('[application_reject] 原始 body：' . $raw);
  $body = json_decode($raw, true);

  $id     = (int)($body['book_ap_id'] ?? 0);
  $reason = trim($body['reject_reason'] ?? '');

  if ($id === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '系統忙碌中，請稍後再試。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  if ($reason === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請填寫駁回原因，申請人會看到這段文字。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $check = $pdo->prepare(
      "SELECT user_id, ap_title, ap_status FROM book_application_form WHERE book_ap_id = ?"
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

    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
      "UPDATE book_application_form
          SET ap_status = '已駁回', reject_reason = ?, staff_account = ?, resolved_at = NOW()
        WHERE book_ap_id = ?"
    );
    $stmt->execute([$reason, $staff['staff_account'], $id]);

    $content = "您推薦的《{$application['ap_title']}》未通過審核。原因：{$reason}";

    $notify = $pdo->prepare(
      "INSERT INTO notification (user_id, type, notifi_title, content)
       VALUES (?, 'SYSTEM_MESSAGE', ?, ?)"
    );
    $notify->execute([$application['user_id'], '你的書籍推薦未通過', $content]);

    $pdo->commit();

    echo json_encode([
      'success'       => true,
      'staff_name'    => $staff['staff_name'],
      'staff_account' => $staff['staff_account'],
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    error_log('[admin_application_reject] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '駁回失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
