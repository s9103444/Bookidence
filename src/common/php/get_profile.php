
<?php

  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, OPTIONS');
  header('Access-Control-Allow-Headers: Authorization,Content-Type');

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

    $stmt= $pdo->prepare(
        "SELECT user_id
        FROM member
        WHERE session_token=:token");
    $stmt->execute(['token'=> $token]);
    $member= $stmt->fetch(PDO::FETCH_ASSOC);
  

    if(!$member){
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => '登入已失效']);
      exit();
    }
    //抓取會員資料
    $stmt= $pdo->prepare(
        "SELECT member_code,nickname, bio,email, account_status	, created_at
        FROM member
        WHERE user_id=:myId");
    $stmt-> execute(['myId'=>$member['user_id']]);

    $profile= $stmt->fetch(PDO::FETCH_ASSOC);

   
        echo json_encode(['success'=>true,
        'profile'=> $profile
        ],JSON_UNESCAPED_UNICODE);
  
?>