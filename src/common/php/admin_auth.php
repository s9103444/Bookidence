<?php
// 後台 API 的登入檢查。
//
// 怎麼用：在 require 完 connect 之後，加一行 require 'admin_auth.php'; 就有了。
//   require 'connect_ckd101g1.php';
//   require 'admin_auth.php';
//
// 沒登入或 token 失效的話，這支會直接回 401 並中斷程式，
//
// 通過檢查的話，$staff 裡面會有這位管理員的 staff_account 和 staff_name，
// 需要記錄「誰做的」時候直接用。
//
// 陷阱：一定要放在 connect 後面，這支會用到它建立的 $pdo。

  

  $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
  $token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

  if ($token === '') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => '未登入。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $stmt = $pdo->prepare("SELECT staff_account, staff_name FROM staff WHERE session_token = ?");
    $stmt->execute([$token]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    error_log('[admin_auth] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '系統忙碌中，請稍後再試。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  if (!$staff) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => '登入已失效，請重新登入。'], JSON_UNESCAPED_UNICODE);
    exit();
  }
?>
