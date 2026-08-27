<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, OPTIONS');
  header('Access-Control-Allow-Headers: Authorization, Content-Type');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
  }

  require 'connect_ckd101g1.php';
  require 'admin_auth.php';

  $body = json_decode(file_get_contents('php://input'), true);
  error_log(print_r($body,true)) ;
  $bookId = (int)($body['book_id'] ?? 0);
  $title=trim(($body['title'] ?? ''));
  $author = trim($body['author'] ?? '');
  $isbn=$body['isbn'] ?? '';
  $publisher = trim($body['publisher'] ?? '');
  $pDate     = trim($body['p_date'] ?? '');
  $status = trim($body['b_status'] ?? '');

  if ($bookId <= 0 || $title === '' || $author==='' || $isbn ==='') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '書名、作者、ISBN 為必填。'], JSON_UNESCAPED_UNICODE);
    exit();
  }


  $categories = $body['categories'] ?? [];

  if (count($categories) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請至少選一個分類。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $check = $pdo->prepare("SELECT book_id FROM book WHERE book_id = ?");
      $check->execute([$bookId]);

    if (!$check->fetch()) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => '找不到這本書。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    $pdo->beginTransaction();//多表寫入保護資料庫

    $stmt = $pdo->prepare(
      
      "UPDATE book
        SET title = ?, author = ?, isbn = ?, publisher = ?, p_date = ?, b_status = ?
        WHERE book_id = ?"
    );
    $stmt->execute([$title, $author, $isbn, $publisher, $pDate, $status, $bookId]);

    $placeholders = implode(',', array_fill(0, count($categories), '?'));

    $catStmt = $pdo->prepare("SELECT bcg_id FROM book_category WHERE bcg_name IN ($placeholders)");
    $catStmt->execute($categories);
    $categoryIds = $catStmt->fetchAll(PDO::FETCH_COLUMN);

    error_log('[book_update] 分類編號：' . json_encode($categoryIds));
    $pdo->prepare("DELETE FROM book_categorys WHERE book_id = ?")->execute([$bookId]);

    $insert = $pdo->prepare("INSERT INTO book_categorys (book_id, bcg_id) VALUES (?, ?)");

    foreach ($categoryIds as $id) {
      $insert->execute([$bookId,$id ]);
    }

    $pdo->commit();

    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);


  } catch (PDOException $e) {
    $pdo->rollBack();
    error_log('[admin_book_update] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '更新失敗。'], JSON_UNESCAPED_UNICODE);
  }
?>