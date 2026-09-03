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
        // 多帶「這則被判過檢舉成立嗎」，書房才知道要不要擋住「改成公開」。
        // 判成立的心得前台已經看不到了（book_thoughts_list.php 用 NOT EXISTS 排除），
        // 但作者在書房還看得到自己的，所以要在這裡告訴他為什麼不能重新公開
        $stmt = $pdo->prepare(
          "SELECT t.*,
                  (SELECT r.reason FROM report r
                    WHERE r.b_thought_id = t.b_thought_id AND r.status = '檢舉成立'
                    ORDER BY r.resolved_at DESC LIMIT 1) AS taken_down_reason
             FROM book_thoughts t
            WHERE t.user_id = ? AND t.book_id = ?"
        );
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

      // 被判檢舉成立的心得不能重新公開。內容還是可以改、也可以存成非公開／草稿 ——
      // 擋的只有「改成公開」這條路。前端也會擋，但那只管體驗，這一層才管安全
      if ($bookThoughtStatus === '公開') {
        $blockStmt = $pdo->prepare(
          "SELECT r.reason
             FROM report r
             JOIN book_thoughts t ON r.b_thought_id = t.b_thought_id
            WHERE t.user_id = ? AND t.book_id = ? AND r.status = '檢舉成立'
            LIMIT 1"
        );
        $blockStmt->execute([$memberId, $bookId]);
        $blocked = $blockStmt->fetchColumn();

        if ($blocked) {
          http_response_code(403);
          echo json_encode([
            'success' => false,
            'message' => "這則心得因{$blocked}違反社群規範已下架，無法重新公開。",
          ], JSON_UNESCAPED_UNICODE);
          exit();
        }
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