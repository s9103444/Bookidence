<?php
// 查詢單本書籍詳情，給公會[了解此書] 以及 我的專屬書房[查看書籍] 按鈕使用

header('Content-Type: application/json; charset=utf8');
header('Access-Control-Allow-Origin: *');

require 'connect_ckd101g1.php';

try{
    $bookId = $_GET['book_id'] ?? null;
    if(!$bookId){
        echo json_encode(['success' => false, 'message' => '缺少 book_id 參數']);
        exit();
    }

    $bookStmt = $pdo->prepare(
        "SELECT b.book_id, b.title, b.author, b.publisher, b.bc_image, b.description, b.isbn, b.p_date,
            (SELECT COUNT(*) FROM book_thoughts WHERE book_id = b.book_id AND bth_status = '公開') AS reviewCount,
            (SELECT COUNT(*) FROM book_collection WHERE book_id = b.book_id) AS collectCount
        FROM book AS b
        WHERE b.book_id = :book_id"
    );
    $bookStmt->execute(['book_id' => $bookId]);
    $book = $bookStmt->fetch(PDO::FETCH_ASSOC);

    if(!$book){
        echo json_encode(['success' => false, 'message' => '找不到這本書']);
        exit();
    }

    $categoryStmt = $pdo->prepare(
        "SELECT bc.bcg_name
        FROM book_categorys bcs
        JOIN book_category bc ON bc.bcg_id = bcs.bcg_id
        WHERE bcs.book_id = :book_id"
    );
    $categoryStmt->execute(['book_id' => $bookId]);
    $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(['success' => true, 'book' => $book, 'categories' => $categories],JSON_UNESCAPED_UNICODE);

}catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
}
?>