<?php
  require __DIR__ . '/admin_bootstrap.php';

  $body = json_decode(file_get_contents('php://input'), true) ?? [];

  $guildId      = (int)($body['guild_id'] ?? 0);
  $leaderUserId = (int)($body['leader_user_id'] ?? 0);
  $deputyUserId = isset($body['deputy_user_id']) && $body['deputy_user_id'] !== ''
    ? (int)$body['deputy_user_id']
    : null;

  if ($guildId === 0 || $leaderUserId === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請選擇會長。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  if ($deputyUserId !== null && $deputyUserId === $leaderUserId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '副會長不能跟會長是同一人。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $checkStmt = $pdo->prepare(
      "SELECT user_id FROM guildmember WHERE guild_id = ? AND user_id = ? AND member_status = '在會中'"
    );

    $checkStmt->execute([$guildId, $leaderUserId]);
    if (!$checkStmt->fetch()) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => '選擇的會長不是這個公會的在會成員。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    if ($deputyUserId !== null) {
      $checkStmt->execute([$guildId, $deputyUserId]);
      if (!$checkStmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '選擇的副會長不是這個公會的在會成員。'], JSON_UNESCAPED_UNICODE);
        exit();
      }
    }

    $pdo->beginTransaction();

    // 先全部重設回一般，沒被這次選上的幹部才會正確退回一般成員
    $resetStmt = $pdo->prepare(
      "UPDATE guildmember SET permission_level = '一般' WHERE guild_id = ? AND member_status = '在會中'"
    );
    $resetStmt->execute([$guildId]);

    $setLeaderStmt = $pdo->prepare(
      "UPDATE guildmember SET permission_level = '會長' WHERE guild_id = ? AND user_id = ?"
    );
    $setLeaderStmt->execute([$guildId, $leaderUserId]);

    if ($deputyUserId !== null) {
      $setDeputyStmt = $pdo->prepare(
        "UPDATE guildmember SET permission_level = '副會長' WHERE guild_id = ? AND user_id = ?"
      );
      $setDeputyStmt->execute([$guildId, $deputyUserId]);
    }

    $pdo->commit();

    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    error_log('[admin_guild_assign] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '指派失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
