<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET');
  header('Access-Control-Allow-Headers: Authorization, Content-Type');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
  }

  require 'connect_ckd101g1.php';

  //抓出乾淨的token  
  $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
  $token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

  if ($token === '') {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => '未登入。']);
      exit();
  }

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
    $status = $_GET['status'] ?? '';
    if($status == '全部藏書' || $status == ""){
      $stmt2 = $pdo->prepare("SELECT book.*, book_collection.r_status FROM book_collection JOIN book ON book_collection.book_id = book.book_id WHERE book_collection.user_id = ?" );
      //execute一定要吃陣列，即使只有一個值也要包起來
      $stmt2 -> execute([$memberId]);
      $book_data = $stmt2 ->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode(['data' => $book_data], JSON_UNESCAPED_UNICODE);
    }else{
      $stmt3 = $pdo->prepare("SELECT book.*, book_collection.r_status FROM book_collection JOIN book ON book_collection.book_id = book.book_id WHERE book_collection.user_id = ? AND book_collection.r_status = ?");
      //execute一定要吃陣列，即使只有一個值也要包起來
      $stmt3 -> execute([$memberId,$status]);
      $book_data = $stmt3 ->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode(['data' => $book_data], JSON_UNESCAPED_UNICODE);
    }
   
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
}

?>