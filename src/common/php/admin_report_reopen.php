<?php
  require __DIR__ . '/admin_bootstrap.php';

  $raw = file_get_contents('php://input');
  error_log('[report_reopen] 原始 body：' . $raw);
  $body = json_decode($raw, true);

  $reportId = (int)($body['report_id'] ?? 0);

  if ($reportId === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '系統忙碌中，請稍後再試。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $check = $pdo->prepare(
      "SELECT report_id, target_type, b_thought_id, message_id, reported_user_id, status
         FROM report WHERE report_id = ?"
    );
    $check->execute([$reportId]);
    $report = $check->fetch(PDO::FETCH_ASSOC);

    if (!$report) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => '找不到這筆檢舉。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    if ($report['status'] === '尚未處理') {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '這筆檢舉還沒處理過。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    $targetId = $report['b_thought_id'] ?? $report['message_id'];
    $column   = $report['target_type'] === '心得' ? 'b_thought_id' : 'message_id';


    $pdo->beginTransaction();


    // ---------- ① 先記下這批檢舉單有哪些 ----------
    // 退回之後 status 就變了，撤銷處分時要靠這批 id 去找
    $idsStmt = $pdo->prepare(
      "SELECT report_id FROM report WHERE $column = ? AND status <> '尚未處理'"
    );
    $idsStmt->execute([$targetId]);
    $ids = $idsStmt->fetchAll(PDO::FETCH_COLUMN);

    $placeholders = implode(',', array_fill(0, count($ids), '?'));


    // ---------- ② 檢舉單退回待處理 ----------
    // 四個欄位一起清 NULL，只清狀態的話畫面會留著上次判決的殘影
    $back = $pdo->prepare(
      "UPDATE report
          SET status = '尚未處理', action_taken = NULL, resolution_notes = NULL,
              staff_account = NULL, resolved_at = NULL
        WHERE report_id IN ($placeholders)"
    );
    $back->execute($ids);


    // ---------- ③ 撤銷這次判決開出的處分 ----------
    // 用 UPDATE 標記撤銷，不是 DELETE —— 紀錄要留著才查得到曾經判過什麼。
    // 已經撤銷過的不要再蓋一次時間，所以加 revoked_at IS NULL
    $revoke = $pdo->prepare(
      "UPDATE moderation_action
          SET revoked_at = NOW(), revoked_by = ?
        WHERE report_id IN ($placeholders) AND revoked_at IS NULL"
    );
    $revoke->execute(array_merge([$staff['staff_account']], $ids));

    $revoked = $revoke->rowCount();


    // ---------- ④ 如果撤銷的是停權，要把人放出來 ----------
    // 判錯了收回，他不該還被擋在門外。token 不用補，重新登入就會有新的
    $wasSuspended = $pdo->prepare(
      "SELECT 1 FROM moderation_action
        WHERE report_id IN ($placeholders) AND action_type = '停權' LIMIT 1"
    );
    $wasSuspended->execute($ids);

    if ($wasSuspended->fetch()) {
      $unban = $pdo->prepare("UPDATE member SET account_status = '正常' WHERE user_id = ?");
      $unban->execute([$report['reported_user_id']]);
    }


    // ---------- ⑤ 有撤銷處分才通知被檢舉人 ----------
    // 他收過「你被警告了」，現在不算數了要讓他知道。
    // 沒開過處分就不用通知 —— 他根本不知道發生過什麼事
    if ($revoked > 0) {
      $notify = $pdo->prepare(
        "INSERT INTO notification (user_id, type, content) VALUES (?, 'SYSTEM_MESSAGE', ?)"
      );
      $notify->execute([
        $report['reported_user_id'],
        '你先前收到的處分已撤銷，不計入違規紀錄。造成困擾敬請見諒。',
      ]);
    }


    $pdo->commit();

    echo json_encode([
      'success'  => true,
      'reopened' => count($ids),
      'revoked'  => $revoked,
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    error_log('[admin_report_reopen] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '退回失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
