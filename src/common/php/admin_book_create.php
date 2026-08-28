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
  error_log('[book_create] ' . json_encode($body, JSON_UNESCAPED_UNICODE));
  $title=trim(($body['title'] ?? ''));
  $author = trim($body['author'] ?? '');
  $isbn=$body['isbn'] ?? '';
  $publisher = trim($body['publisher'] ?? '');
  $pDate     = trim($body['p_date'] ?? '');
  $status = trim($body['b_status'] ?? '');
  $pDate = $pDate === '' ? null : $pDate;

  if ( $title === '' || $author==='' || $isbn ==='') {
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
    $check = $pdo->prepare("SELECT book_id FROM book WHERE isbn = ?");
    $check->execute([$isbn]);

    if ($check->fetch()) {        
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '這組 ISBN 已入庫，請至書籍列表確認'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    $pdo->beginTransaction();//多表寫入保護資料庫

    $max = $pdo->query("SELECT MAX(book_display_id) FROM book")->fetchColumn();
    $next = $max ? (int)substr($max, 2) + 1 : 1;
    $displayId = 'BK' . str_pad($next, 8, '0', STR_PAD_LEFT);

    $stmt = $pdo->prepare(
      
      "INSERT INTO book (book_display_id, title, author, isbn, publisher, p_date, b_status)
        VALUES (?,?,?,?,?,?,?)"
    );

    $stmt->execute([$displayId,$title,$author,$isbn,$publisher,$pDate,$status]);
    $bookId = (int)$pdo->lastInsertId();

    $placeholders = implode(',', array_fill(0, count($categories), '?'));

    $catStmt = $pdo->prepare("SELECT bcg_id FROM book_category WHERE bcg_name IN ($placeholders)");
    $catStmt->execute($categories);
    $categoryIds = $catStmt->fetchAll(PDO::FETCH_COLUMN);

    error_log('[book_create] 分類編號：' . json_encode($categoryIds));

    $insert = $pdo->prepare("INSERT INTO book_categorys (book_id, bcg_id) VALUES (?, ?)");

    foreach ($categoryIds as $id) {
      $insert->execute([$bookId,$id ]);
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'book_id' => $bookId, 'book_display_id' => $displayId], JSON_UNESCAPED_UNICODE);


  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    error_log('[admin_book_create] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '新增失敗。'], JSON_UNESCAPED_UNICODE);
  }
?>