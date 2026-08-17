<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');

  require 'connect_yuyusql.php';

  try {
    // password 不對外輸出
    $stmt = $pdo->query(
      "SELECT user_id, member_code, nickname, report_count, email, bio,
              account_status, created_at, achieve_id, total_exp
       FROM member"
    );
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $members], JSON_UNESCAPED_UNICODE);
  } catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => '查詢失敗：' . $e->getMessage()]);
  }
?>
