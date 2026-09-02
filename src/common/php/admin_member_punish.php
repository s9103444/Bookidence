<?php
  require __DIR__ . '/admin_bootstrap.php';

  $raw = file_get_contents('php://input');
  error_log('[member_punish] 原始 body：' . $raw);
  $body = json_decode($raw, true);

  $userId = resolveUserId($pdo, $body['user_id'] ?? '');
  $type   = $body['action_type'] ?? '';
  $reason = trim($body['reason'] ?? '');

  $WARN    = '警告';
  $SUSPEND = '停權';
  $RESTORE = '解除停權';

  if ($userId === 0 || !in_array($type, [$WARN, $SUSPEND, $RESTORE], true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '系統忙碌中，請稍後再試。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  // 會留紀錄又會通知對方的動作要收集原因；解除停權是還他清白，不強制
  if ($type !== $RESTORE && $reason === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請填寫處分原因。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  if (mb_strlen($reason) > 500) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '處分原因不可超過 500 字。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $who = $pdo->prepare("SELECT user_id, nickname, account_status FROM member WHERE user_id = ?");
    $who->execute([$userId]);
    $member = $who->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => '找不到這位會員。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    $isSuspended = $member['account_status'] === '停權';

    if ($type === $SUSPEND && $isSuspended) {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '這個帳號已經是停權狀態了。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    if ($type === $RESTORE && !$isSuspended) {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '這個帳號目前沒有被停權。'], JSON_UNESCAPED_UNICODE);
      exit();
    }


    $pdo->beginTransaction();


    // ---------- ① 記一筆處分 ----------
    // report_id 是 NULL —— 這是管理員主動處分，不是從檢舉來的。
    // 只新增不覆寫：解除停權是「再記一筆」，不是把停權那筆抹掉，
    // 這樣「被停權過幾次」才查得到
    $log = $pdo->prepare(
      "INSERT INTO moderation_action
              (target_user_id, staff_account, report_id, action_type, reason, created_at)
       VALUES (?, ?, NULL, ?, ?, NOW())"
    );
    $log->execute([$userId, $staff['staff_account'], $type, $reason !== '' ? $reason : null]);


    // ---------- ② 停權／解除停權要改帳號狀態 ----------
    // 停權同時清掉 token：其他 API 認人只看 session_token、不看 account_status，
    // 不清的話他手上那個分頁還能繼續用
    if ($type === $SUSPEND) {
      $pdo->prepare("UPDATE member SET account_status = '停權', session_token = NULL WHERE user_id = ?")
          ->execute([$userId]);
    } elseif ($type === $RESTORE) {
      $pdo->prepare("UPDATE member SET account_status = '正常' WHERE user_id = ?")
          ->execute([$userId]);
    }


    // ---------- ③ 通知本人 ----------
    // 文案固定，管理員填的原因只留在 moderation_action 給後台看。
    // 主動處分的原因多半是「累計違規」「判定為行銷帳號」這種內部判斷，
    // 直接貼給使用者看容易出現後台術語，而且這類對象多半不會來問。
    // 走檢舉流程的處分不一樣 —— 那邊的通知有帶檢舉原因（四選一的分類）
    if ($type === $WARN) {
      $title = '你收到一次警告';
      $text  = '你的帳號收到一次警告，請遵守社群規範。再次違規可能導致帳號停權。';
    } elseif ($type === $SUSPEND) {
      $title = '你的帳號已停權';
      $text  = '你的帳號已停權，將無法登入平台。';
    } else {
      $title = '你的帳號已恢復';
      $text  = '你的帳號停權已解除，現在可以正常使用。';
    }

    $pdo->prepare(
      "INSERT INTO notification (user_id, type, notifi_title, content)
       VALUES (?, 'SYSTEM_MESSAGE', ?, ?)"
    )->execute([$userId, $title, $text]);


    $pdo->commit();

    echo json_encode([
      'success'    => true,
      'action'     => $type,
      'staff_name' => $staff['staff_name'],
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    error_log('[admin_member_punish] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '操作失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
