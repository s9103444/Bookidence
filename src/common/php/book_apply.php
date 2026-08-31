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
    echo json_encode(['success' => false, 'message' => '請先登入再申請。'], JSON_UNESCAPED_UNICODE);
    exit();
  }


  // ---------- 2. 讀前端送來的欄位 ----------
  $raw = file_get_contents('php://input');
  error_log('[book_apply] 原始 body：' . $raw);
  $body = json_decode($raw, true);

  $title  = trim($body['title'] ?? '');
  $author = trim($body['author'] ?? '');

  $isbn = $body['isbn'] ?? '';
  $isbn = str_replace('-', '', $isbn);
  $isbn = str_replace(' ', '', $isbn);

  $bookUrl = trim($body['book_url'] ?? '');
  $reason  = trim($body['application_reason'] ?? '');

  $bookUrl = $bookUrl === '' ? null : $bookUrl;

  if ($title === '' || $author === '' || $isbn === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '書名、作者、ISBN 為必填。'], JSON_UNESCAPED_UNICODE);
    exit();
  }


  try {
    // ---------- 3. 用 token 換出這個人是誰 ----------
    $stmt = $pdo->prepare("SELECT user_id FROM member WHERE session_token = ?");
    $stmt->execute([$token]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => '登入已失效，請重新登入。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    $userId = $member['user_id'];


    // ---------- 4. 這本書已經在書庫了嗎？ ----------
    $inLibrary = $pdo->prepare("SELECT book_id FROM book WHERE isbn = ?");
    $inLibrary->execute([$isbn]);
    $book = $inLibrary->fetch(PDO::FETCH_ASSOC);

    if ($book) {
      http_response_code(409);
      echo json_encode([
        'success' => false,
        'message' => '這本書已經在書庫囉。',
        'book_id' => $book['book_id'],
      ], JSON_UNESCAPED_UNICODE);
      exit();
    }


    // ---------- 5. 已經有人推薦過了嗎？ ----------
    $applied = $pdo->prepare("SELECT book_ap_id FROM book_application_form WHERE isbn = ?");
    $applied->execute([$isbn]);

    if ($applied->fetch()) {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '這本書已經有人推薦過了。'], JSON_UNESCAPED_UNICODE);
      exit();
    }


    // ---------- 6. 都沒撞到，寫進去 ----------
    $insert = $pdo->prepare(
      "INSERT INTO book_application_form (isbn, user_id, ap_title, ap_author, book_url, application_reason)
       VALUES (?,?,?,?,?,?)"
    );

    $insert->execute([$isbn, $userId, $title, $author, $bookUrl, $reason]);

    echo json_encode(['success' => true, 'message' => '已收到您的申請。'], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[book_apply] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '送出失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
