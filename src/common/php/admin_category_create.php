<?php
    require __DIR__ . '/admin_bootstrap.php';

    $raw = file_get_contents('php://input');
    error_log('[admin_category_create] 原始 body：' . $raw);
    $body = json_decode($raw, true);

    $name = trim($body['name'] ?? '');

    if ( $name === '' ) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '分類為必填。'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    try{
        $check = $pdo->prepare("SELECT bcg_name FROM book_category WHERE bcg_name = ?");
        $check->execute([$name]);
    
        if($check->fetch()){
            http_response_code(409);
            echo json_encode(['success' => false, 'message' =>  "{$name}已在清單內，請至書籍分類管理確認"], JSON_UNESCAPED_UNICODE);
            exit();
        }
    
        $stmt = $pdo->prepare(
            "INSERT INTO book_category (bcg_name)
                VALUES (?)"
            );
        $stmt->execute([$name]);
        $categoryId = (int)$pdo->lastInsertId();
        echo json_encode(['success' => true,'bcg_id' => $categoryId,'bcg_name' => $name], JSON_UNESCAPED_UNICODE);
        
    }catch(PDOException $e){
        error_log('[admin_category_create] ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '新增失敗。'], JSON_UNESCAPED_UNICODE);
    }
?>