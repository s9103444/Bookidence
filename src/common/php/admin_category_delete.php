<?php
    require __DIR__ . '/admin_bootstrap.php';

    $raw = file_get_contents('php://input');
    error_log('[admin_category_delete] 原始 body：' . $raw);
    $body = json_decode($raw, true);

    $id = (int)($body['bcg_id'] ?? 0);

    try{
        $checkBookNum = $pdo->prepare("SELECT COUNT(*) FROM book_categorys WHERE bcg_id = ?");
        $checkBookNum->execute([$id]);
        $bookCount = (int)$checkBookNum->fetchColumn();

        if ($bookCount > 0) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => "還有 {$bookCount} 本書正在使用這個分類，請先調整書籍分類。"], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $pdo->beginTransaction();

        $delPreference = $pdo->prepare("DELETE FROM member_book_categorys WHERE bcg_id = ?");
        $delPreference->execute([$id]);
        $removedPreferences = $delPreference->rowCount();

        $delCategory = $pdo->prepare("DELETE FROM book_category WHERE bcg_id = ?");
        $delCategory->execute([$id]);

        if ($delCategory->rowCount() === 0) {
            $pdo->rollBack();
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => '這個分類已不存在。'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'bcg_id' => $id,
            'removed_preferences' => $removedPreferences,
        ], JSON_UNESCAPED_UNICODE);

    }catch(PDOException $e){
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('[admin_category_delete] ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '刪除失敗。'], JSON_UNESCAPED_UNICODE);
    }
?>
