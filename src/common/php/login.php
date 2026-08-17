<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
  }

  require 'connect_yuyusql.php';

  $body = json_decode(file_get_contents('php://input'), true);
  $email = trim($body['email'] ?? '');
  $password = (string) ($body['password'] ?? '');

  if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請輸入 E-mail 與密碼。']);
    exit();
  }

  try {
    $stmt = $pdo->prepare(
      "SELECT user_id, member_code, nickname, email, password,
              bio, account_status, total_exp
       FROM member
       WHERE email = :email"
    );
    $stmt->execute(['email' => $email]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$member || !password_verify($password, $member['password'])) {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => 'E-mail 或密碼錯誤。']);
      exit();
    }

    if ($member['account_status'] !== '正常') {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => '此帳號已被停權。']);
      exit();
    }

    $token = bin2hex(random_bytes(32));
    $update = $pdo->prepare("UPDATE member SET session_token = :token WHERE user_id = :user_id");
    $update->execute(['token' => $token, 'user_id' => $member['user_id']]);

    unset($member['password']);
    echo json_encode(['success' => true, 'token' => $token, 'user' => $member], JSON_UNESCAPED_UNICODE);
  } catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
  }
?>
