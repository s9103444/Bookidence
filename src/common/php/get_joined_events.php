<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST,OPTIONS');
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


     $stmt= $pdo->prepare(
        "SELECT event_registration.event_id,event_registration.user_id,event_registration.submitted_at,event.deadline,guild.guild_name,guild.guild_code,guild.guild_avatar,book.title
        FROM event_registration
        JOIN event ON event.event_id = event_registration.event_id
        JOIN guild ON guild.guild_id = event.guild_id
        JOIN book ON book.book_id = event.book_id
        WHERE user_id=:myId ");
    $stmt-> execute(['myId'=>$member['user_id']]);

    $myJoinedEvents= $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success'=>true,
    'myJoinedEvents'=> $myJoinedEvents
    ],JSON_UNESCAPED_UNICODE);


?>