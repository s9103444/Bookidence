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
        "SELECT book_id, title, author, publisher, bc_image, description, isbn, p_date
        FROM book
        WHERE book_id = :book_id"
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