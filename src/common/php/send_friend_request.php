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

    $toUserId=$body['toUserId']?? '';
    //$body 是「前端這次 fetch 送過來的整包 JSON 資料」，$body['toUserId'] 是想從這包資料裡，取出某一個特定欄位（欄位名稱是 toUserId）的值。?? '' 則是「如果这個欄位不存在，就給一個空字串當預設值，避免噴錯」。

    //所以這一行的意思是：「假設前端會送一個 JSON，裡面有一個叫 toUserId 的欄位，把它的值存到 PHP 變數 $toUserId 裡」——這個欄位名稱是後端單方面訂出來的規則，前端等一下寫 fetch 的時候，body: JSON.stringify({...}) 裡面的 key 要跟這裡完全一樣，兩邊才對得起來。


    try {
    // 你覺得「可能會出錯」的程式碼放這裡

    $stmt=$pdo->prepare("
    INSERT INTO friendship (user_id_a, user_id_b, rel_status)
    VALUES (:myId, :toUserId, '申請中')
    ");

    $stmt->execute(['toUserId'=>$toUserId,'myId'=>$member['user_id'] ]);

    echo json_encode(['success'=>true,
    'toUserId'=>$toUserId
    ],JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // 如果 try 裡面真的丟出例外，就會跳到這裡執行
    http_response_code(409);
    echo json_encode(['success'=>false,'message'=>'已經邀請過了']);
}







?>