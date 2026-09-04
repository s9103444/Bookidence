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

    $oldPassword=$body['oldPassword']?? '';
    //使用者剛在表單輸入的「舊密碼」
    $newPassword=$body['newPassword']?? '';
    //使用者剛在表單輸入的「新密碼」

    // 先確認舊密碼對不對
    $stmt=$pdo->prepare("
    SELECT password 
    FROM member
    WHERE user_id=:user_id
    ");
    $stmt->execute(['user_id'=>$member['user_id']]);
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    //從資料庫讀取查詢結果。PDO::FETCH_ASSOC 表示將結果抓取為一個「關聯陣列」（名稱對應資料庫欄位名）。

    if(!password_verify($oldPassword,$row['password'])){
        http_response_code(401);
        echo json_encode(['success'=>false,'message'=>'舊密碼不正確' ]);
        exit();
    }
    

    // 舊密碼對了，才更新成新密碼（一樣要加密）
    $hashed= password_hash($newPassword,PASSWORD_DEFAULT);
    //password_hash(要加密的密碼, 使用的加密演算法)，PASSWORD_DEFAULT是固定的寫法
    
    $stmt=$pdo->prepare("
    UPDATE member
    SET password=:password
    WHERE user_id=:myId
    ");

    $stmt->execute(['password'=>$hashed, 'myId'=>$member['user_id']]);

    echo json_encode(['success'=>true,'message'=>'成功修改密碼']);



?>