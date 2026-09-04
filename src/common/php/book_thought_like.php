<?php
// 書籍心得公開區的按讚。
//
// POST   b_thought_id  → 按讚（重複按不算錯）
// DELETE b_thought_id  → 收回讚
//
// 一律回傳這則心得最新的讚數，前端直接拿去顯示，不用自己 +1／-1 猜。

  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');
  header('Access-Control-Allow-Headers: Authorization, Content-Type');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
  }

  require 'connect_ckd101g1.php';

  $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
  $token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

  if ($token === '') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => '未登入。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  $body = json_decode(file_get_contents('php://input'), true);
  $thoughtId = (int)($body['b_thought_id'] ?? 0);

  if ($thoughtId === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '缺少心得編號。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $me = $pdo->prepare("SELECT user_id FROM member WHERE session_token = ?");
    $me->execute([$token]);
    $member = $me->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => '登入已失效，請重新登入。'], JSON_UNESCAPED_UNICODE);
      exit();
    }
    $userId = $member['user_id'];

    // 心得不在就直接回 404 —— 否則 INSERT 會撞外鍵回 500，錯誤訊息看不出原因
    $check = $pdo->prepare("SELECT 1 FROM book_thoughts WHERE b_thought_id = ?");
    $check->execute([$thoughtId]);

    if (!$check->fetchColumn()) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => '這則心得不存在。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // 重複按讚維持原本那筆就好，不報錯（PK 是 b_thought_id + user_id）
      $ins = $pdo->prepare(
        "INSERT INTO book_thought_like (b_thought_id, user_id)
         VALUES (?, ?)
         ON DUPLICATE KEY UPDATE liked_at = liked_at"
      );
      $ins->execute([$thoughtId, $userId]);
      $liked = true;
    } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
      $del = $pdo->prepare("DELETE FROM book_thought_like WHERE b_thought_id = ? AND user_id = ?");
      $del->execute([$thoughtId, $userId]);
      $liked = false;
    } else {
      http_response_code(405);
      echo json_encode(['success' => false, 'message' => '不支援的請求方式。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    $count = $pdo->prepare("SELECT COUNT(*) FROM book_thought_like WHERE b_thought_id = ?");
    $count->execute([$thoughtId]);

    echo json_encode([
      'success'    => true,
      'liked'      => $liked,
      'like_count' => (int)$count->fetchColumn(),
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[book_thought_like] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '操作失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
