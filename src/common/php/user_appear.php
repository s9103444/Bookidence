<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET,POST');
  header('Access-Control-Allow-Headers: Authorization, Content-Type');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
  }

  require 'connect_ckd101g1.php';

  $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
  $token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

  if ($token === '') {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => '未登入。']);
      exit();
  }
  
   try {
    $stmt =  $pdo->prepare("SELECT user_id FROM member WHERE session_token = ?");
    $stmt -> execute([$token]);
    $member = $stmt -> fetch(PDO::FETCH_ASSOC);

    if(!$member){
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => '查無此資料']);
      exit();
    }
    $memberId = $member['user_id'];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $body = json_decode(file_get_contents('php://input'), true);
      
      $genderRaw = $body['gender'] ?? '';
      $hairId = $body['selectedHair']  ?? '';
      $eyesId = $body['selectedEyes'] ?? '';
      $skinId = $body['selectedSkin'] ?? '';

      $genderMap = [
        'female' => 'g01',
        'male' => 'g02',
      ];
      $genderId = $genderMap[$genderRaw] ?? '';
      
      $stmt3 = $pdo->prepare("DELETE FROM user_appear WHERE user_id = ?");
      $stmt4 = $pdo->prepare("INSERT INTO user_appear (user_id, appear_id) VALUES (?, ?)");

      $pdo->beginTransaction();
      $stmt3 -> execute([$memberId]);
      $appearIds = [$genderId, $hairId, $eyesId, $skinId];
      foreach ($appearIds as $appearId) {
      $stmt4 -> execute([$memberId, $appearId]);
      }
      $pdo->commit();

      echo json_encode(['success' => true, 'data' => '更新成功']);


    }else{
      $stmt2 = $pdo->prepare(" SELECT ua.appear_id, a.type FROM user_appear ua JOIN appear a ON ua.appear_id = a.appear_id WHERE ua.user_id = ? ");
      $stmt2 -> execute([$memberId]);
      $data = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);

      $typeMap = [
      '性別' => 'gender',
      '髮色' => 'hair',
      '瞳色' => 'eyes',
      '膚色' => 'skin',
      ];

      $genderMap = [
      'g01' => 'female',
      'g02' => 'male',
      ];
      $result = [];

      foreach ($data as $row) {
      $key = $typeMap[$row['type']];

      if ($row['type'] === '性別') {
          $result[$key] = $genderMap[$row['appear_id']];
      } else {
          $result[$key] = $row['appear_id'];
      }
      }
      echo json_encode(['success' => true, 'data' => $result]);
    }
    
    }catch(PDOException $e){
        http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
  }

?>