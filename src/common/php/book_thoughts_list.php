<?php
// 撈某一本書的公開心得，給書籍詳情頁的心得區使用。
//
// GET ?book_id=1
// 不用登入也看得到。有帶 token 的話，自己檢舉過的那幾則會被排除掉。

  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, OPTIONS');
  header('Access-Control-Allow-Headers: Authorization, Content-Type');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
  }

  require __DIR__ . '/connect_ckd101g1.php';

  $bookId = (int)($_GET['book_id'] ?? 0);

  if ($bookId === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '缺少 book_id 參數。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
  $token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

  try {
    $viewerId = null;

    if ($token !== '') {
      $me = $pdo->prepare("SELECT user_id FROM member WHERE session_token = ?");
      $me->execute([$token]);
      $row = $me->fetch(PDO::FETCH_ASSOC);
      $viewerId = $row ? $row['user_id'] : null;
    }

    // 沒登入時「我按過讚嗎」一律 0；有登入才用子查詢比對
    $likedSelect = '0';
    $params = [$bookId];
    $excludeSql = '';

    if ($viewerId !== null) {
      $likedSelect = "(SELECT COUNT(*) FROM book_thought_like AS lm
                        WHERE lm.b_thought_id = t.b_thought_id AND lm.user_id = ?)";
      array_unshift($params, $viewerId);

      $excludeSql = "AND NOT EXISTS (
        SELECT 1 FROM report AS r
         WHERE r.b_thought_id = t.b_thought_id AND r.reporter_id = ?
      )";
      $params[] = $viewerId;
    }

    $stmt = $pdo->prepare(
      "SELECT t.b_thought_id, t.bth_content, t.updated_at,
              m.user_id, m.nickname, m.member_code, m.avatar_url,
              (SELECT COUNT(*) FROM book_thought_like AS la
                WHERE la.b_thought_id = t.b_thought_id) AS like_count,
              $likedSelect AS liked_by_me
         FROM book_thoughts AS t
         JOIN member AS m ON t.user_id = m.user_id
        WHERE t.book_id = ? AND t.bth_status = '公開'
          AND NOT EXISTS (
            SELECT 1 FROM report AS r2
             WHERE r2.b_thought_id = t.b_thought_id AND r2.status = '檢舉成立'
          )
          $excludeSql
        ORDER BY t.updated_at DESC"
    );
    $stmt->execute($params);
    $thoughts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
      'success' => true,
      'data'    => $thoughts,
      'total'   => count($thoughts),
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[book_thoughts_list] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗。'], JSON_UNESCAPED_UNICODE);
  }
?>
