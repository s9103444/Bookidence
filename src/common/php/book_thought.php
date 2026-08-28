<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET,POST,DELETE');
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
    $bookId = $_GET['book_id'] ?? '';

    if($_SERVER['REQUEST_METHOD'] === 'GET'){
      if($bookId===''){//列出草稿區用的，撈book_thoughts的資料出來，GET是''時啟動!!
        $stmt = $pdo->prepare("SELECT book.bc_image, book.title, book_thoughts.book_id, book_thoughts.updated_at FROM book_thoughts JOIN book ON book_thoughts.book_id = book.book_id WHERE user_id = ? AND bth_status = '儲存草稿'");
        $stmt -> execute([$memberId]);
        $bookThoughtsData = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['data' => $bookThoughtsData],JSON_UNESCAPED_UNICODE);
      }else{
        $stmt = $pdo->prepare("SELECT * FROM book_thoughts WHERE user_id = ? AND book_id = ?");
        $stmt -> execute([$memberId, $bookId]);
        $bookThoughtData = $stmt -> fetch(PDO::FETCH_ASSOC);
        echo json_encode(['data' => $bookThoughtData],JSON_UNESCAPED_UNICODE);
      }
    } else if (
      $_SERVER['REQUEST_METHOD'] === 'POST'){
      $requestBody = file_get_contents('php://input');
      $body = json_decode($requestBody, true);
      $bookId = $body['book_id'] ?? '';
      $bookContent = $body['bth_content'] ?? '';
      $bookThoughtStatus = $body['bth_status'] ?? '非公開';

      if($bookId == '' || $bookContent == ''){
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '書籍與心得內容不得為空']);
        exit();
      }

      $stmt2 = $pdo -> prepare("INSERT INTO book_thoughts (user_id, book_id, bth_content, bth_status) VALUES(?, ?, ?, ?) ON DUPLICATE KEY UPDATE bth_content = VALUES(bth_content),bth_status = VALUES(bth_status)");
      $stmt2 -> execute([$memberId, $bookId, $bookContent, $bookThoughtStatus]);

      echo json_encode(['success' => true, 'message' => '心得已儲存']);
    } else if (
      $_SERVER['REQUEST_METHOD'] === 'DELETE'
    ){
      $requestBody = file_get_contents('php://input');
      $body = json_decode($requestBody,true);
      $bookId = $body['book_id'] ?? '';

      $stmt3 = $pdo -> prepare("DELETE FROM book_thoughts WHERE user_id = ? AND book_id = ?");
      $stmt3 -> execute([$memberId, $bookId]);

      echo json_encode(['success' => true, 'message' => '心得已刪除']);
    }
 

  }catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '操作失敗：資料庫查詢失敗']);
}
?>