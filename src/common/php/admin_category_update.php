<?php
    require __DIR__ . '/admin_bootstrap.php';

    $raw = file_get_contents('php://input');
    error_log('[admin_category_update] 原始 body：' . $raw);
    $body = json_decode($raw, true);

    $name = trim($body['name'] ?? '');
    $id= (int)($body['bcg_id'] ?? 0);

    if ( $name === '' ) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '分類為必填。'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    try{
        $checkId = $pdo->prepare("SELECT bcg_id FROM book_category WHERE bcg_id = ?");
        $checkId->execute([$id]);
    
        if(!$checkId->fetch()){
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => '這個分類已不存在。'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $checkName = $pdo->prepare("SELECT bcg_id FROM book_category WHERE bcg_name = ? AND bcg_id != ?");
        $checkName->execute([$name,$id]);
    
        if($checkName->fetch()){
            http_response_code(409);
            echo json_encode(['success' => false, 'message' =>  "{$name}已在清單內。"], JSON_UNESCAPED_UNICODE);
            exit();
        }
    
        $stmt = $pdo->prepare(
            " UPDATE book_category SET bcg_name = ? WHERE bcg_id = ?"
            );
        $stmt->execute([$name,$id]);
        echo json_encode(['success' => true,'bcg_id' => $id,'bcg_name' => $name], JSON_UNESCAPED_UNICODE);
        
    }catch(PDOException $e){
        error_log('[admin_category_update] ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '修改失敗。'], JSON_UNESCAPED_UNICODE);
    }
?>