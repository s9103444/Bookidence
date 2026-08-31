<?php
  header('Content-Type: application/json; charset=utf8');
  // 這是 CORS（跨來源資源共享）設定，允許任何網域的網頁透過 JavaScript（如 fetch/AJAX）呼叫這支 API。
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET');

  require 'connect_ckd101g1.php';

  //定義關鍵字條件
  $keyword = $_GET['keyword'] ?? '';
  $likeKeyWord = "%$keyword%";

  // 有帶 page 才切頁，沒帶就照舊一次全部回（書房的搜尋沒有帶 page）
  $perPage = 10;
  $page = (int)($_GET['page'] ?? 0);
  $limitSql = '';

  if ($page > 0) {
    $offset = ($page - 1) * $perPage;
    $limitSql = "LIMIT $perPage OFFSET $offset";
  }

  try{
    // 數總數這句不 JOIN：篩選條件只用到 book 的欄位，JOIN 進來反而會讓多分類的書被重複計算
    $countStmt = $pdo->prepare(
      "SELECT COUNT(*) FROM book
        WHERE b_status = '已上架'
          and (title LIKE ? OR author LIKE ? OR isbn LIKE ? OR book_id IN (
      SELECT link2.book_id FROM book_categorys AS link2
      JOIN book_category AS cat2 ON link2.bcg_id = cat2.bcg_id
      WHERE cat2.bcg_name LIKE ?
    ))");
    $countStmt -> execute([$likeKeyWord, $likeKeyWord, $likeKeyWord, $likeKeyWord]);
    $total = (int)$countStmt -> fetchColumn();

    $stmt = $pdo->prepare(
      "SELECT b.*, GROUP_CONCAT(cat.bcg_name ORDER BY cat.bcg_id) AS categories
         FROM book AS b
         LEFT JOIN book_categorys AS link ON b.book_id = link.book_id
         LEFT JOIN book_category AS cat ON link.bcg_id = cat.bcg_id
        WHERE b.b_status = '已上架'
          and (b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ? OR b.book_id IN (
      SELECT link2.book_id FROM book_categorys AS link2
      JOIN book_category AS cat2 ON link2.bcg_id = cat2.bcg_id
      WHERE cat2.bcg_name LIKE ?
    ))
        GROUP BY b.book_id
        $limitSql");
    $stmt -> execute([$likeKeyWord, $likeKeyWord, $likeKeyWord, $likeKeyWord]);
    $book_data = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $book_data, 'total' => $total, 'perPage' => $perPage], JSON_UNESCAPED_UNICODE);
  }catch(PDOException $e){
    http_response_code(500);
    echo json_encode(['error' => '查詢失敗：' . $e->getMessage()]);
  }
?>
