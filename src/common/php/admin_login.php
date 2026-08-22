<?php
    header('Content-Type:application/json; charset=utf8');
    header('Access-Control-Allow-Origin:*');
    header('Access-Control-Allow-Methods:POST,OPTIONS');
    header('Access-Control-Allow-Headers:Content-Type');
    
    if($_SERVER["REQUEST_METHOD"]==='OPTIONS'){
        exit();
    }

    require 'connect_ckd101g1.php';
    $body=json_decode(file_get_contents('php://input'),true);
    $account=trim($body['account']??'');
    $password=(string)($body['password']??'');

    if($account===''||$password===''){
        http_response_code(400);
        echo json_encode(['success'=>false,'message'=>'請輸入帳號與密碼。'],JSON_UNESCAPED_UNICODE);exit();
    }

    try{
        $stmt=$pdo->prepare(
            "SELECT staff_account,staff_name,password
            FROM staff
            WHERE staff_account=:account"
        );
        $stmt->execute(['account'=>$account]);
        $staff=$stmt->fetch(PDO::FETCH_ASSOC);
        if(!$staff || !password_verify($password,$staff['password'])){
            http_response_code(401);
            echo 
            json_encode(['success'=>false,'message'=>'帳號密碼錯誤。'],JSON_UNESCAPED_UNICODE);exit();
        }
        $token=bin2hex(random_bytes(32));
        $update=$pdo->prepare("UPDATE staff SET session_token=:token WHERE staff_account=:account");
        $update->execute(['token'=>$token,'account'=>$account]);
        unset($staff['password']);
        echo
        json_encode(['success'=>true,'token'=>$token,'staff'=>$staff],JSON_UNESCAPED_UNICODE);
    }catch(PDOException $e){
        error_log('[admin_login] '.$e->getMessage());
        $isLocal=str_contains($_SERVER['HTTP_HOST'],"localhost");
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>$isLocal ?('查詢失敗 ：'.$e->getMessage()): '系統忙碌中，請稍後再試。'],JSON_UNESCAPED_UNICODE);
    }

?>