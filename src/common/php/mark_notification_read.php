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

    $body= json_decode(file_get_contents('php://input'),true);
    $notifiId=$body['notifiId']?? null;
    
    if($notifiId){ //針對指定的該則標記
    $stmt=$pdo->prepare("
    UPDATE notification
    SET is_read = 1 
    WHERE user_id=:myId AND notifi_id=:notifiId
    ");

    $stmt->execute(['notifiId'=>$notifiId,'myId'=>$member['user_id']]);

    }else{ //全部標記
    $stmt=$pdo->prepare("
    UPDATE notification
    SET is_read = 1 
    WHERE user_id=:myId ");
    $stmt->execute(['myId'=>$member['user_id']]);
    }

    if($stmt->rowCount()===0){
        http_response_code(404);
        echo json_encode(['success'=>false,'message'=>'找不到這筆資料，可能已經被處理過了。']);
        exit();
    }
    echo json_encode(['success' => true]);



?>