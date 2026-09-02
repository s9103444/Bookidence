<?php
  require __DIR__ . '/admin_bootstrap.php';

  $body = json_decode(file_get_contents('php://input'), true) ?? [];

  $guildId = (int)($body['guild_id'] ?? 0);

  if ($guildId === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '缺少公會編號。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $checkStmt = $pdo->prepare("SELECT guild_status FROM guild WHERE guild_id = ?");
    $checkStmt->execute([$guildId]);
    $currentStatus = $checkStmt->fetchColumn();

    if ($currentStatus !== '停權') {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '這個公會目前不是停權狀態。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("UPDATE guild SET guild_status = '正常' WHERE guild_id = ?");
    $stmt->execute([$guildId]);

    $logStmt = $pdo->prepare(
      "INSERT INTO guild_moderation_log (guild_id, staff_account, action_type, reason)
       VALUES (?, ?, '解除停權', NULL)"
    );
    $logStmt->execute([$guildId, $staff['staff_account']]);

    $pdo->commit();

    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    error_log('[admin_guild_restore] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '解除停權失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
