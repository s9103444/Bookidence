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
    
    //傳入參數的陣列
    //myId 是 SQL 語句中的佔位符名稱
    //$member['user_id'] 是實際要傳入的值

    $myguilds= $stmt->fetchAll(PDO::FETCH_ASSOC);
    //執行一個資料庫查詢，並將所有結果以關聯陣列的格式存入 $myguilds 變數中。
    //fetchAll() 是一個方法，用來一次取出所有的查詢結果（不像 fetch() 只取一筆）
    //PDO::FETCH_ASSOC 指定結果的格式為「關聯陣列」（associative array）

   
    echo json_encode(['success'=>true,
    'myguilds'=> $myguilds
    ],JSON_UNESCAPED_UNICODE);

    //將一個陣列轉換成 JSON 格式並輸出，並保持中文/Unicode 字符不被轉義。
    // json_encode  通常用來傳送資料給前端（JavaScript）
  
  








?>