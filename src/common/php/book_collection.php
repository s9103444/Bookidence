<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, DELETE, PUT');
  header('Access-Control-Allow-Headers: Authorization, Content-Type');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
  }

  require 'connect_ckd101g1.php';

  // === 以下這段不管 POST 還是 DELETE 都要跑，所以放在外面 ===
  //抓出乾淨的token  
  $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
  $token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

  if ($token === '') {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => '未登入。']);
      exit();
  }
  //表格類form、new URLSearchParams({...})，有繞過JS的才要用$_POST
  //只要前端是送 JSON 字串（JSON.stringify），後端就要用 php://input + json_decode；
  $requestBody = file_get_contents('php://input');
  $body = json_decode($requestBody, true);
  $book_id = $body['book_id'] ?? '';
  $status = $body['r_status'] ?? '';

  //先確認是哪一個會員送出的請求
  try {
    $stmt =  $pdo->prepare("SELECT user_id FROM member WHERE session_token = ?");
    $stmt -> execute([$token]);
    $member = $stmt -> fetch(PDO::FETCH_ASSOC);

    if(!$member){
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => '查無此資料']);
      exit();
    }
    
    $memberId = $member['user_id'];

    // === 從這裡開始，兩個動作分流 ===
    if($_SERVER['REQUEST_METHOD']==='DELETE'){
      $stmt2 = $pdo->prepare("DELETE FROM book_collection WHERE user_id = ? AND book_id = ?");
      $stmt2 -> execute([$memberId, $book_id]);
      echo json_encode(['success' => true, 'message' => '移除成功！']);
    }else if($_SERVER['REQUEST_METHOD']==='POST'){
      $stmt3 = $pdo->prepare("INSERT INTO book_collection(user_id, book_id, r_status, added_at) VALUES (?, ?, ?, NOW())");
      $stmt3 -> execute([$memberId, $book_id, '未閱讀']);
      echo json_encode(['success' => true, 'message' => '加入成功！']);
    }else if($_SERVER['REQUEST_METHOD']==='PUT'){
      if($status === ''){
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '狀態不得為空']);
        exit();
      }
      $stmt4 = $pdo->prepare("UPDATE book_collection SET r_status = ? WHERE user_id = ? AND book_id = ?");
      $stmt4 -> execute([$status, $memberId, $book_id]);
      echo json_encode(['success' => true, 'message' => '更新成功！']);
    }
  }catch(PDOException $e){
        http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
  }
?>