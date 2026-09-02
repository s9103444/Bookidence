<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, OPTIONS');
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

  try {
    $stmt = $pdo->prepare(
      "SELECT user_id, member_code, nickname, email,
              bio, account_status, total_exp, avatar_url
      FROM member
      WHERE session_token = :token"
    );
    $stmt->execute(['token' => $token]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    // 查不到人代表這個 token 已經失效（被停權清掉、在別的裝置重新登入、
    // 或資料庫換過）。fetch() 找不到會回 false，不擋的話那個 false 會被當成
    // 使用者資料吐出去，前端的 restoreSession() 就永遠偵測不到要登出
    if (!$member) {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => '登入已失效。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    echo json_encode(['success' => true, 'user' => $member], JSON_UNESCAPED_UNICODE);
  } catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
  }
?>
