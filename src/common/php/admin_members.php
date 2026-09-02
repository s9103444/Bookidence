<?php
    require __DIR__ . '/admin_bootstrap.php';


    
    //撈全部會員的基本資料
    $stmt=$pdo->prepare("
    SELECT user_id, member_code,created_at,account_status,nickname,email
    FROM member
    ");
    $stmt->execute();
    $members=$stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success'=>true,'member'=>$members],JSON_UNESCAPED_UNICODE);

    







?>