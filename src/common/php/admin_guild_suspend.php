<?php
  require __DIR__ . '/admin_bootstrap.php';

  $body = json_decode(file_get_contents('php://input'), true) ?? [];

  $guildId = (int)($body['guild_id'] ?? 0);
  $reason  = trim($body['reason'] ?? '');

  if ($guildId === 0 || $reason === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請填寫停權原因。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("UPDATE guild SET guild_status = '停權' WHERE guild_id = ?");
    $stmt->execute([$guildId]);

    $logStmt = $pdo->prepare(
      "INSERT INTO guild_moderation_log (guild_id, staff_account, action_type, reason)
       VALUES (?, ?, '停權', ?)"
    );
    $logStmt->execute([$guildId, $staff['staff_account'], $reason]);

    $pdo->commit();

    echo json_encode(['success' => true, 'staff_name' => $staff['staff_name']], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    error_log('[admin_guild_suspend] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '停權失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
