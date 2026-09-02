<?php
  require __DIR__ . '/admin_bootstrap.php';

  // 處分編號的格式跟檢舉編號一致，前後台都讀這個欄位
  $reportNoSql = "CONCAT(DATE_FORMAT(r.created_at, '%y%m%d'), '-', r.report_id)";

  $userId = resolveUserId($pdo, $_GET['user_id'] ?? '');

  if ($userId === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '系統忙碌中，請稍後再試。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $who = $pdo->prepare("SELECT user_id, nickname, member_code, account_status FROM member WHERE user_id = ?");
    $who->execute([$userId]);
    $member = $who->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => '找不到這位會員。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    // staff JOIN 兩次：一次拿處理人、一次拿撤銷的人。撤銷多半是 NULL 所以走 LEFT JOIN。
    // report 也是 LEFT JOIN —— 管理員可以不經檢舉主動處分（report_id 允許 NULL）
    $stmt = $pdo->prepare(
      "SELECT a.action_id, a.action_type, a.reason, a.created_at,
              a.report_id, a.revoked_at,
              doer.staff_name    AS staff_name,
              revoker.staff_name AS revoked_by_name,
              CASE WHEN a.report_id IS NULL THEN NULL ELSE $reportNoSql END AS report_no
         FROM moderation_action AS a
         LEFT JOIN staff  AS doer    ON a.staff_account = doer.staff_account
         LEFT JOIN staff  AS revoker ON a.revoked_by    = revoker.staff_account
         LEFT JOIN report AS r       ON a.report_id     = r.report_id
        WHERE a.target_user_id = ?
        ORDER BY a.created_at DESC, a.action_id DESC"
    );
    $stmt->execute([$userId]);
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 累計處分次數：只算警告和停權，而且被撤銷的不算。
    // 「解除停權」是還他清白、「刪除內容」罰的是那則內容不是這個人
    $punishCount = 0;

    foreach ($actions as $a) {
      if ($a['revoked_at'] === null && ($a['action_type'] === '警告' || $a['action_type'] === '停權')) {
        $punishCount++;
      }
    }

    echo json_encode([
      'success' => true,
      'member'  => $member,
      'data'    => $actions,
      'summary' => [
        'punish_count' => $punishCount,
        'total_count'  => count($actions),
      ],
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[admin_member_actions] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗。'], JSON_UNESCAPED_UNICODE);
  }
?>
