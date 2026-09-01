<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, OPTIONS');
  header('Access-Control-Allow-Headers: Authorization, Content-Type');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
  }

  require __DIR__ . '/connect_ckd101g1.php';


  // ---------- 1. 先確認有沒有帶 token 進來 ----------
  $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
  $token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

  if ($token === '') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => '請先登入再檢舉。'], JSON_UNESCAPED_UNICODE);
    exit();
  }


  // ---------- 2. 讀前端送來的欄位 ----------
  $raw = file_get_contents('php://input');
  error_log('[book_thought_report] 原始 body：' . $raw);
  $body = json_decode($raw, true);

  $thoughtId = $body['b_thought_id'] ?? null;

  $reason       = $body['reason'] ?? '';
  $reasonDetail = trim($body['reason_detail'] ?? '');

  $validReasons = ['人身攻擊', '廣告垃圾資訊', '不當內容', '抄襲 / 侵權'];


  // ---------- 3. 所有檢查做完，才動第一筆資料 ----------
  if (!$thoughtId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '缺少心得編號。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  if (!in_array($reason, $validReasons, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請選擇檢舉原因。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  if (mb_strlen($reasonDetail) > 500) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '補充說明不可超過 500 個字。'], JSON_UNESCAPED_UNICODE);
    exit();
  }


  try {
    // ---------- 4. token 換成 user_id ----------
    $userStmt = $pdo->prepare("SELECT user_id FROM member WHERE session_token = ?");
    $userStmt->execute([$token]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => '登入已過期，請重新登入。'], JSON_UNESCAPED_UNICODE);
      exit();
    }
    $reporterId = (int) $user['user_id'];


    // ---------- 5. 這則心得存在嗎？順便查出「被檢舉人是誰」 ----------
    $thoughtStmt = $pdo->prepare("SELECT user_id FROM book_thoughts WHERE b_thought_id = ?");
    $thoughtStmt->execute([$thoughtId]);
    $thought = $thoughtStmt->fetch(PDO::FETCH_ASSOC);

    if (!$thought) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => '這則心得不存在，可能已經被刪除。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    $reportedUserId = (int) $thought['user_id'];

    if ($reportedUserId === $reporterId) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => '不能檢舉自己的心得。'], JSON_UNESCAPED_UNICODE);
      exit();
    }


    // ---------- 6. 同一個人不要對同一則心得檢舉兩次 ----------
    $dupStmt = $pdo->prepare("SELECT report_id FROM report WHERE reporter_id = ? AND b_thought_id = ?");
    $dupStmt->execute([$reporterId, $thoughtId]);

    if ($dupStmt->fetch(PDO::FETCH_ASSOC)) {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '你已經檢舉過這則心得了。'], JSON_UNESCAPED_UNICODE);
      exit();
    }


    // ---------- 7. 寫進去 ----------
    $insert = $pdo->prepare(
      "INSERT INTO report (reporter_id, reported_user_id, b_thought_id, target_type, reason, reason_detail)
       VALUES (?, ?, ?, '心得', ?, ?)"
    );
    $insert->execute([
      $reporterId,
      $reportedUserId,
      $thoughtId,
      $reason,
      $reasonDetail !== '' ? $reasonDetail : null,
    ]);

    echo json_encode([
      'success' => true,
      'report_id'    => (int) $pdo->lastInsertId(),
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[book_thought_report] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '檢舉失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
