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
      //「驗證完身份（確定 token 有效、$member 有值）之後」，接著才讀取前端傳來的 fromUserId——這個順序是合理的：先確認「你是誰」，再處理「你想做什麼」。

    $body=json_decode(file_get_contents('php://input'),true);
    
    // PHP 讀取「這次請求 body 原始內容」的方式，json_decode(..., true) 把它從 JSON 字串轉成 PHP 陣列。
    //這行的作用是「把這次請求 body 的 JSON 內容，轉成一個 PHP 陣列，存進 $body」

    $deleteUserId=$body['deleteUserId'] ??'';
    //意思是「我預期前端會送一個 JSON，裡面有一個叫 fromUserId 的欄位」——這是後端單方面訂出來的規則，前端目前還沒有真的去呼叫這支 API、也還沒有送出符合這個格式的請求。

    $stmt=$pdo->prepare("
    DELETE FROM friendship 
    WHERE ((user_id_a=:deleteUserId AND user_id_b=:myId) OR(user_id_b=:deleteUserId AND user_id_a=:myId)) AND rel_status='已成為好友' ");

    $stmt->execute(['deleteUserId'=>$deleteUserId,'myId'=>$member['user_id']]);

    if($stmt->rowCount()===0){
      http_response_code(404);
      echo json_encode(['success'=>false,'message'=>'找不到這筆邀請，可能已經被處理過了。']);
      exit();

    }

    echo json_encode(['success' => true]);



?>