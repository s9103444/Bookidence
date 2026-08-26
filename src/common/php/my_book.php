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
      $keyword = trim($_GET['keyword'] ?? '');

      $where  = ['book_collection.user_id = ?'];
      $params = [$memberId];

      //設定如果是非全域搜索的情況，
      if ($status !== '' && $status !== '全部藏書') {
      $where[] = 'book_collection.r_status = ?';
      $params[] = $status;
  }

  if ($keyword !== '') {
      $where[] = '(book.title LIKE ? OR book.author LIKE ? OR book.isbn LIKE ?)';
      $like = "%$keyword%";
      $params[] = $like;
      $params[] = $like;
      $params[] = $like;
  }

  $whereSql = 'WHERE ' . implode(' AND ', $where);

  $stmt2 = $pdo->prepare(
    "SELECT book.*, book_collection.r_status, GROUP_CONCAT(book_category.bcg_name) AS categories
    FROM book_collection
    JOIN book ON book_collection.book_id = book.book_id
    LEFT JOIN book_categorys ON book.book_id = book_categorys.book_id
    LEFT JOIN book_category ON book_categorys.bcg_id = book_category.bcg_id
    $whereSql
    GROUP BY book.book_id");

    $stmt2->execute($params);
    $book_data = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['data' => $book_data], JSON_UNESCAPED_UNICODE);
  
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
}

?>