<?php
  require __DIR__ . '/admin_bootstrap.php';

  $body = json_decode(file_get_contents('php://input'), true) ?? [];

  $guildId = (int)($body['guild_id'] ?? 0);
  $reason  = trim($body['reason'] ?? '');

  if ($guildId === 0 || $reason === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請填寫刪除原因。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $pdo->beginTransaction();

    // 刪除只是把公會狀態改成已解散，不動 guildmember，保留歷史成員名單供稽核
    $stmt = $pdo->prepare("UPDATE guild SET guild_status = '已解散' WHERE guild_id = ?");
    $stmt->execute([$guildId]);

    $logStmt = $pdo->prepare(
      "INSERT INTO guild_moderation_log (guild_id, staff_account, action_type, reason)
       VALUES (?, ?, '刪除', ?)"
    );
    $logStmt->execute([$guildId, $staff['staff_account'], $reason]);

    $pdo->commit();

    echo json_encode(['success' => true, 'staff_name' => $staff['staff_name']], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    error_log('[admin_guild_delete] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '刪除失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
