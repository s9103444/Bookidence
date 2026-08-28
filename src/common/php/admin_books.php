<?php
  require __DIR__ . '/admin_bootstrap.php';

  $keyword = trim($_GET['keyword'] ?? '');
  $status  = $_GET['status'] ?? '';
  $where  = [];
  $params = [];
  $perPage=3;
  $page=max(1,(int)($_GET['page']??1));
  $offset=($page-1)*$perPage;


  if ($keyword !== '') {
    $where[] = '(b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ?)';
    $like = "%$keyword%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
  }
  if ($status !== '') {
    $where[] = 'b.b_status = ?';
    $params[] = $status;
  }
  if(count($where)>0){
    $whereSql = 'WHERE ' . implode(' AND ', $where);
  }else{
    $whereSql='';
  }

  try {
    $countStmt=$pdo->prepare(
      "SELECT COUNT(*) FROM book AS b
        $whereSql");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();
    $stmt = $pdo->prepare(
      "SELECT b.book_id, b.book_display_id, b.isbn, b.title, b.author,
          b.publisher, b.bc_image, b.p_date, b.b_status,
          GROUP_CONCAT(cat.bcg_name ORDER BY cat.bcg_id) AS categories
        FROM book AS b
        LEFT JOIN book_categorys AS link ON b.book_id = link.book_id
        LEFT JOIN book_category AS cat ON link.bcg_id = cat.bcg_id
        $whereSql
        GROUP BY b.book_id
        ORDER BY b.book_id DESC
        LIMIT $perPage OFFSET $offset"
    );
    $stmt->execute($params);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $books,'total'=>$total,'perPage'=>$perPage], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[admin_books] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
  }
?>