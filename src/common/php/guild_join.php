<?php
  // 加入公會：已經是成員就不重複計數，否則寫入/更新 guildmember 並把 member_count +1

  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, OPTIONS');
  header('Access-Control-Allow-Headers: Authorization');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
  }

  require 'connect_ckd101g1.php';

  $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
  $token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

  if ($token === '') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => '未登入。']);
    exit();
  }

  $guildId = $_POST['guild_id'] ?? null;

  if (!$guildId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '缺少 guild_id 參數']);
    exit();
  }

  try {
    $userStmt = $pdo->prepare("SELECT user_id FROM member WHERE session_token = :token");
    $userStmt->execute(['token' => $token]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => '登入已過期，請重新登入。']);
      exit();
    }
    $userId = (int) $user['user_id'];

    $guildStmt = $pdo->prepare("SELECT guild_id FROM guild WHERE guild_id = :id AND guild_status = '正常'");
    $guildStmt->execute(['id' => $guildId]);
    if (!$guildStmt->fetch()) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => '找不到這個公會。']);
      exit();
    }

    $pdo->beginTransaction();

    $memberStmt = $pdo->prepare(
      "SELECT member_status FROM guildmember WHERE user_id = :user_id AND guild_id = :guild_id"
    );
    $memberStmt->execute(['user_id' => $userId, 'guild_id' => $guildId]);
    $existing = $memberStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing && $existing['member_status'] === '在會中') {
      $pdo->commit();
      echo json_encode(['success' => true, 'message' => '你已經是這個公會的成員了。']);
      exit();
    }

    $upsertStmt = $pdo->prepare(
      "INSERT INTO guildmember (user_id, guild_id, permission_level, member_status)
       VALUES (:user_id, :guild_id, '一般', '在會中')
       ON DUPLICATE KEY UPDATE permission_level = '一般', member_status = '在會中'"
    );
    $upsertStmt->execute(['user_id' => $userId, 'guild_id' => $guildId]);

    $updateCountStmt = $pdo->prepare("UPDATE guild SET member_count = member_count + 1 WHERE guild_id = :guild_id");
    $updateCountStmt->execute(['guild_id' => $guildId]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => '加入成功']);
  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '加入公會失敗：' . $e->getMessage()]);
  }
?>
