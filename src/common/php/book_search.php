<?php
  header('Content-Type: application/json; charset=utf8');
  // 這是 CORS（跨來源資源共享）設定，允許任何網域的網頁透過 JavaScript（如 fetch/AJAX）呼叫這支 API。
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET');

  require 'connect_ckd101g1.php';

  //定義關鍵字條件
  $keyword = $_GET['keyword'] ?? '';
  $likeKeyWord = "%$keyword%";

  try{
    $stmt = $pdo->prepare("SELECT * FROM book WHERE b_status = '已上架' and (title LIKE ? OR author LIKE ? OR isbn LIKE ?)");
    $stmt -> execute([$likeKeyWord, $likeKeyWord, $likeKeyWord]);
    $book_data = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $book_data], JSON_UNESCAPED_UNICODE);
  }catch(PDOException $e){
    http_response_code(500);
    echo json_encode(['error' => '查詢失敗：' . $e->getMessage()]);
  }
 
?>