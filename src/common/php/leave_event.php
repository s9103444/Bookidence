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
    $leaveMyEvent= $body['leaveMyEvent']??'';

    $checkStmt = $pdo->prepare("SELECT deadline FROM event WHERE event_id = :leaveMyEvent");
    $checkStmt->execute(['leaveMyEvent' => $leaveMyEvent]);
    $eventRow = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$eventRow) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => '找不到這筆讀書會活動，可能已經被處理過了。']);
        exit();
    }

    if (strtotime(date('Y-m-d')) > strtotime($eventRow['deadline'])) {
        echo json_encode(['success' => false, 'message' => '活動報名已截止，無法退出活動。']);
        exit();
    }
    
    $stmt=$pdo->prepare("
    DELETE FROM event_registration
    WHERE event_id=:leaveMyEvent AND user_id=:myId ");

    $stmt->execute(['myId'=>$member['user_id'], 'leaveMyEvent'=>$leaveMyEvent]);



   if($stmt->rowCount()===0){
    // 如果受影響的行數 = 0（沒找到要修改/刪除的資料） 
    http_response_code(404);
    echo json_encode(['success'=> false, 'message'=>'找不到這筆讀書會活動，可能已經被處理過了。']);
    exit();
   }


 echo json_encode(['success'=> true]);





   
?>