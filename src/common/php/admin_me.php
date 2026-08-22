<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, OPTIONS');
  header('Access-Control-Allow-Headers: Authorization');

  if($_SERVER["REQUEST_METHOD"]==="OPTIONS"){
    exit();
  }

  require 'connect_ckd101g1.php';

  $authHeader=$_SERVER["HTTP_AUTHORIZATION"] ?? $_SERVER["REDIRECT_HTTP_AUTHORIZATION"] ?? "";

  $token=str_starts_with($authHeader,'Bearer ') ? substr($authHeader,7):'';
  if($token===''){
    http_response_code(401);
    echo json_encode(['success'=>false,'message'=>'未登入。']);
    exit();
  }
  try {
    $stmt=$pdo->prepare(
      "SELECT staff_name,staff_account
      FROM staff
      WHERE session_token = :token"
    );
    $stmt->execute(['token'=>$token]);
    $staff=$stmt->fetch(PDO::FETCH_ASSOC);
    if(!$staff){
      http_response_code(401);
      echo json_encode(['success'=>false,'message'=>'登入已失效，請重新登入。'],JSON_UNESCAPED_UNICODE);
      exit();
    }
    echo json_encode(['success'=>true,'staff'=>$staff],JSON_UNESCAPED_UNICODE);

  }catch(PDOException $e){
    error_log('[admin_me] '.$e->getMessage());
    $isLocal=str_contains($_SERVER['HTTP_HOST'],"localhost");
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$isLocal ?('查詢失敗 ：'.$e->getMessage()): '系統忙碌中，請稍後再試。'],JSON_UNESCAPED_UNICODE);

  }
?>