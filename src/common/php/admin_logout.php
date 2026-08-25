<?php
    header('Content-Type: application/json; charset=utf8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Authorization');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
    }

    require 'connect_ckd101g1.php';

    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    $token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

    if($token!=''){
        try{
            $stmt=$pdo->prepare("UPDATE staff SET session_token=NULL WHERE session_token=:token");
            $stmt->execute(['token'=>$token]);
        }catch(PDOException $e){
            error_log('[admin_logout] ' .$e->getMessage());

        }
    }
    echo json_encode(['success'=>true],JSON_UNESCAPED_UNICODE);
?>
    
