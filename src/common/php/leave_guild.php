<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, OPTIONS');
  header('Access-Control-Allow-Headers: Authorization, Content-Type');

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

    $body=json_decode(file_get_contents('php://input'),true);

    $deleteGuild=$body['deleteGuild'] ?? '';

    $stmt=$pdo->prepare("
    UPDATE guildmember
    SET member_status='自行退出'
    WHERE user_id=:user_id AND guild_id=:guild_id");


    $stmt->execute(['user_id'=>$member['user_id'],'guild_id'=>$deleteGuild]);


    if($stmt->rowCount()=== 0){
        http_response_code(404);
        echo json_encode(['success'=> false ,'message'=>'找不到這筆讀書公會，可能已經被處理過了。']);
         exit();

    }

    echo json_encode(['success' => true]);

?>