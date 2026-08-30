<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET');

  require 'connect_ckd101g1.php';
  $userId = $_GET['user_id'] ?? '';

  if ($userId === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '缺少 user_id']);
    exit();
  }
   try {
    $stmt = $pdo->prepare(" SELECT ua.appear_id, a.type FROM user_appear ua JOIN appear a ON ua.appear_id = a.appear_id WHERE ua.user_id = ? ");
      $stmt -> execute([$userId]);
      $data = $stmt -> fetchAll(PDO::FETCH_ASSOC);

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


  } catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
  }
?>