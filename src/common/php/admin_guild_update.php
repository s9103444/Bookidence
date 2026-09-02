<?php
  require __DIR__ . '/admin_bootstrap.php';

  $body = json_decode(file_get_contents('php://input'), true) ?? [];

  $guildId   = (int)($body['guild_id'] ?? 0);
  $guildName = trim($body['guild_name'] ?? '');
  $intro     = trim($body['intro'] ?? '');

  if ($guildId === 0 || $guildName === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '公會名稱為必填。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $stmt = $pdo->prepare("UPDATE guild SET guild_name = ?, intro = ? WHERE guild_id = ?");
    $stmt->execute([$guildName, $intro, $guildId]);

    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[admin_guild_update] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '儲存失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
