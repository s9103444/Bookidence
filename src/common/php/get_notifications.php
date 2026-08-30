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


    $stmt=$pdo->prepare("
    SELECT notifi_id,user_id,type,content,sent_at,is_read
    FROM notification
    WHERE user_id=:myId
    ORDER BY sent_at DESC

    ");
    $stmt->execute(['myId'=>$member['user_id']]);
    $getNotice=$stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success'=>true,'getNotice'=>$getNotice],JSON_UNESCAPED_UNICODE);



?>