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

    $body=json_decode(file_get_contents('php://input'),true);

     $cancelEvent=$body['cancelEvent'];

    $checkStmt = $pdo->prepare("SELECT event_date FROM event WHERE event_id = :cancelEvent AND organizer_user_id = :myId");
    $checkStmt->execute(['cancelEvent' => $cancelEvent, 'myId' => $member['user_id']]);
    $eventRow = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$eventRow) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => '找不到這筆讀書會活動，可能已經被處理過了。']);
        exit();
    }
    $daysUntilEvent = (strtotime($eventRow['event_date']) - strtotime(date('Y-m-d'))) / 86400;
    
    if ($daysUntilEvent <= 7) {
        echo json_encode(['success' => false, 'message' => '活動即將於一週內開始，無法取消活動。']);
        exit();
    }
    
    $stmt=$pdo->prepare("
    UPDATE event
    SET event_status='已取消'
    WHERE organizer_user_id=:myId AND event_id=:cancelEvent

    ");

    $stmt->execute(['myId'=>$member['user_id'], 'cancelEvent'=>$cancelEvent]);



   if($stmt->rowCount()===0){
    // 如果受影響的行數 = 0（沒找到要修改/刪除的資料） 
    http_response_code(404);
    echo json_encode(['success'=> false, 'message'=>'找不到這筆讀書會活動，可能已經被處理過了。']);
    exit();
   }


 echo json_encode(['success'=> true]);




?>