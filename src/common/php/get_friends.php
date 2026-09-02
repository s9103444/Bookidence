
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
    //我被邀請加入好友
    $stmt= $pdo->prepare(
        "SELECT member.user_id,member.nickname, member.bio, member.member_code, member.last_online_at
        FROM friendship
        JOIN member ON member.user_id=friendship.user_id_a
        WHERE rel_status = '申請中' AND friendship.user_id_b=:myId");
        $stmt-> execute(['myId'=>$member['user_id']]);

    $incomingRequests= $stmt->fetchAll(PDO::FETCH_ASSOC);

    //我送出邀請
    $stmt= $pdo->prepare(
        "SELECT member.user_id,member.nickname, member.bio, member.member_code, member.last_online_at
        FROM friendship
        JOIN member ON member.user_id=friendship.user_id_b
        WHERE rel_status = '申請中' AND friendship.user_id_a=:myId");
        $stmt-> execute(['myId'=>$member['user_id']]);

    $sentRequests= $stmt->fetchAll(PDO::FETCH_ASSOC);

    //互相為好友(user_id_a為我的朋友)
    $stmt=$pdo->prepare(
        "SELECT member.user_id,member.nickname,member.bio, member.member_code, member.last_online_at
        FROM friendship
        JOIN member ON member.user_id= friendship.user_id_a
        WHERE rel_status='已成為好友' AND friendship.user_id_b=:myId");
    $stmt->execute(['myId'=>$member['user_id']]);
    $friendAsA=$stmt->fetchAll(PDO::FETCH_ASSOC);

      //互相為好友(user_id_b為我的朋友)
    $stmt=$pdo->prepare(
        "SELECT member.user_id,member.nickname,member.bio, member.member_code, member.last_online_at
        FROM friendship
        JOIN member ON member.user_id= friendship.user_id_b
        WHERE rel_status='已成為好友' AND friendship.user_id_a=:myId");
    $stmt->execute(['myId'=>$member['user_id']]);
    $friendAsB=$stmt->fetchAll(PDO::FETCH_ASSOC);
   


    $friends=array_merge($friendAsA,$friendAsB);

   
        echo json_encode(['success'=>true,
        'friends'=> $friends,
        'incomingRequests'=> $incomingRequests,
        'sentRequests'=>$sentRequests
        ],JSON_UNESCAPED_UNICODE);
  
?>