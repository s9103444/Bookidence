<?php
  require __DIR__ . '/admin_bootstrap.php';

  try{
    $sql=$pdo->query(
      "SELECT c.bcg_id, c.bcg_name, COUNT(bc.book_id) AS book_count
      FROM book_category AS c
      LEFT JOIN book_categorys AS bc ON c.bcg_id = bc.bcg_id
      GROUP BY c.bcg_id,c.bcg_name
      ORDER BY c.bcg_id"
    );
    $category=$sql->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $category], JSON_UNESCAPED_UNICODE);
  }catch (PDOException $e){
    error_log('[admin_categories] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '分類載入失敗，請稍後再試'], JSON_UNESCAPED_UNICODE);
  }
?>