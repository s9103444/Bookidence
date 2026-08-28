<?php
    
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST,GET,OPTIONS');
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
        "SELECT guild.guild_id,guild.guild_code,guild.guild_name,guild.guild_avatar,guild.book_id,book.title
        FROM guildmember
        JOIN guild ON guild.guild_id=guildmember.guild_id
        JOIN book ON book.book_id= guild.book_id
        WHERE guildmember.user_id=:myId AND guildmember.member_status='在會中' ");

        $stmt-> execute(['myId'=>$member['user_id']]);

    $myguilds= $stmt->fetchAll(PDO::FETCH_ASSOC);

   
        echo json_encode(['success'=>true,
        'myguilds'=> $myguilds
        ],JSON_UNESCAPED_UNICODE);
  
  








?>