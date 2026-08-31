<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET');

  require 'connect_ckd101g1.php';
  
  $category = $_GET['category'] ?? '';
  $keyword = $_GET['keyword'] ?? '';
  $likeKeyWord = "%$keyword%";

  try{
    if($category === 'guild'){
      $stmt = $pdo->prepare("SELECT * FROM guild JOIN book ON guild.book_id = book.book_id WHERE book.b_status = '已上架' AND (book.title LIKE ? OR guild_name LIKE ? OR guild_code LIKE ?)");
      $stmt -> execute([$likeKeyWord, $likeKeyWord, $likeKeyWord]);
      $data = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }
    else if($category === 'book'){
       $stmt2 = $pdo->prepare("SELECT book.book_id, book.title, book.author, book.isbn, book.bc_image, book.publisher, book.p_date, GROUP_CONCAT(cat.bcg_name) AS categories
       FROM book
       LEFT JOIN book_categorys link ON book.book_id = link.book_id
       LEFT JOIN book_category cat ON link.bcg_id = cat.bcg_id
       WHERE book.b_status = '已上架' AND (book.title LIKE ? OR book.author LIKE ? OR book.isbn LIKE ?)
       GROUP BY book.book_id");
       $stmt2 -> execute([$likeKeyWord, $likeKeyWord, $likeKeyWord]);
       $data = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);
    }
    else if($category === 'user'){
       $stmt3 = $pdo->prepare("SELECT member.user_id, member.nickname, member.member_code, GROUP_CONCAT(bc.bcg_name) AS categories FROM member
       LEFT JOIN member_book_categorys mbc
       ON mbc.user_id = member.user_id
       LEFT JOIN book_category bc
       ON mbc.bcg_id = bc.bcg_id
       WHERE member.member_code LIKE ? OR nickname LIKE ?
       GROUP BY member.user_id");
       $stmt3 -> execute([$likeKeyWord, $likeKeyWord]);
       $data = $stmt3 -> fetchAll(PDO::FETCH_ASSOC);
    }
    else{
       http_response_code(400);
        echo json_encode(['error' => '缺少或不支援的 category 參數']);
        exit();
    }
    echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE);
    
  }catch(PDOException $e){
    http_response_code(500);
    echo json_encode(['error' => '查詢失敗：' . $e->getMessage()]);

  }
?>