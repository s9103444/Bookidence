<?php
  require __DIR__ . '/admin_bootstrap.php';

  $guildId = (int)($_GET['id'] ?? 0);

  if ($guildId === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '缺少公會編號。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $guildStmt = $pdo->prepare(
      "SELECT g.guild_id, g.guild_code, g.guild_name, g.intro, g.founded_at, g.member_count, g.guild_status,
              b.title AS current_book_title
       FROM guild g
       LEFT JOIN book b ON g.book_id = b.book_id
       WHERE g.guild_id = ?"
    );
    $guildStmt->execute([$guildId]);
    $guild = $guildStmt->fetch(PDO::FETCH_ASSOC);

    if (!$guild) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => '找不到這個公會。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    // 目前這筆讀書紀錄（最新一筆）算「已完讀書目數」時要排除掉
    $currentRecordStmt = $pdo->prepare(
      "SELECT record_id FROM guildrecord WHERE guild_id = ? ORDER BY record_id DESC LIMIT 1"
    );
    $currentRecordStmt->execute([$guildId]);
    $currentRecordId = $currentRecordStmt->fetchColumn();

    $completedStmt = $pdo->prepare(
      "SELECT COUNT(*) FROM guildrecord WHERE guild_id = ? AND record_id != ?"
    );
    $completedStmt->execute([$guildId, $currentRecordId ?: 0]);
    $guild['completed_books_count'] = (int)$completedStmt->fetchColumn();

    // 停權中／已刪除才需要撈最近一筆對應的處分紀錄，拿來顯示「誰、何時、原因」
    $guild['suspend_log'] = null;
    $guild['delete_log']  = null;

    if ($guild['guild_status'] === '停權') {
      $logStmt = $pdo->prepare(
        "SELECT l.staff_account, s.staff_name, l.reason, l.created_at
         FROM guild_moderation_log l
         LEFT JOIN staff s ON l.staff_account = s.staff_account
         WHERE l.guild_id = ? AND l.action_type = '停權'
         ORDER BY l.created_at DESC LIMIT 1"
      );
      $logStmt->execute([$guildId]);
      $guild['suspend_log'] = $logStmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } elseif ($guild['guild_status'] === '已解散') {
      $logStmt = $pdo->prepare(
        "SELECT l.staff_account, s.staff_name, l.reason, l.created_at
         FROM guild_moderation_log l
         LEFT JOIN staff s ON l.staff_account = s.staff_account
         WHERE l.guild_id = ? AND l.action_type = '刪除'
         ORDER BY l.created_at DESC LIMIT 1"
      );
      $logStmt->execute([$guildId]);
      $guild['delete_log'] = $logStmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // 成員列表：發言數／有沒有被檢舉紀錄，都只算在這個公會的討論範圍內
    $membersStmt = $pdo->prepare(
      "SELECT gm.user_id, gm.permission_level, gm.joined_at,
              m.member_code, m.nickname,
              (SELECT COUNT(*) FROM guilddiscussion d
                 JOIN segment sg ON d.segment_id = sg.segment_id
                 JOIN guildrecord gr ON sg.record_id = gr.record_id
               WHERE gr.guild_id = gm.guild_id AND d.user_id = gm.user_id) AS message_count,
              EXISTS(
                SELECT 1 FROM report r
                  JOIN guilddiscussion d2 ON r.message_id = d2.message_id
                  JOIN segment sg2 ON d2.segment_id = sg2.segment_id
                  JOIN guildrecord gr2 ON sg2.record_id = gr2.record_id
                WHERE gr2.guild_id = gm.guild_id AND r.target_type = '留言' AND r.reported_user_id = gm.user_id
              ) AS flagged
       FROM guildmember gm
       JOIN member m ON gm.user_id = m.user_id
       WHERE gm.guild_id = ? AND gm.member_status = '在會中'
       ORDER BY FIELD(gm.permission_level, '會長', '副會長', '一般'), gm.joined_at"
    );
    $membersStmt->execute([$guildId]);
    $members = $membersStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($members as &$member) {
      $member['flagged'] = (bool)$member['flagged'];
    }
    unset($member);

    // 活動列表：「報名人數」是 event_registration 的數量，不是真的出席人數
    $eventsStmt = $pdo->prepare(
      "SELECT e.event_id, e.event_type, e.event_date, e.event_time, e.description, e.max_participants,
              (SELECT COUNT(*) FROM event_registration er WHERE er.event_id = e.event_id) AS registered_count
       FROM event e
       WHERE e.guild_id = ? AND e.event_status = '正常'
       ORDER BY e.event_date DESC, e.event_time DESC"
    );
    $eventsStmt->execute([$guildId]);
    $events = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);

    // 被檢舉的留言：只給稽核用，一般日常留言不歸後台管
    $messagesStmt = $pdo->prepare(
      "SELECT r.report_id, r.reason, r.created_at AS reported_at,
              d.message_id, d.posted_at, d.content,
              au.user_id AS author_user_id, au.nickname AS author_nickname,
              gm.permission_level AS author_permission_level
       FROM report r
       JOIN guilddiscussion d ON r.message_id = d.message_id
       JOIN segment sg ON d.segment_id = sg.segment_id
       JOIN guildrecord gr ON sg.record_id = gr.record_id
       JOIN member au ON d.user_id = au.user_id
       LEFT JOIN guildmember gm ON gm.user_id = au.user_id AND gm.guild_id = gr.guild_id
       WHERE gr.guild_id = ? AND r.target_type = '留言'
       ORDER BY r.created_at DESC"
    );
    $messagesStmt->execute([$guildId]);
    $messages = $messagesStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
      'success'  => true,
      'guild'    => $guild,
      'members'  => $members,
      'events'   => $events,
      'messages' => $messages,
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[admin_guild_detail] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗。'], JSON_UNESCAPED_UNICODE);
  }
?>
