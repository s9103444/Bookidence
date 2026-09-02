<?php
  require __DIR__ . '/admin_bootstrap.php';

  $guildId = (int)($_GET['guild_id'] ?? 0);
  $eventId = (int)($_GET['event_id'] ?? 0);

  if ($guildId === 0 || $eventId === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '缺少 guild_id 或 event_id 參數。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    // join event 是為了確認這個 event_id 真的屬於這個 guild_id，不能亂帶別的公會的場次號來查
    $stmt = $pdo->prepare(
      "SELECT m.user_id, m.member_code, m.nickname, er.submitted_at
       FROM event_registration er
       JOIN member m ON er.user_id = m.user_id
       JOIN event e ON er.event_id = e.event_id
       WHERE e.event_id = ? AND e.guild_id = ?
       ORDER BY er.submitted_at ASC"
    );
    $stmt->execute([$eventId, $guildId]);
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $registrations], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[admin_guild_event_registrations] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗。'], JSON_UNESCAPED_UNICODE);
  }
?>
