<?php
// 撈正在讀某一本書的公會，給書籍詳情頁的「這些公會正在讀…」使用。
//
// GET ?book_id=1
// 只回狀態正常的公會，成員多的排前面。不用登入。

  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type');

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

  try {
    $stmt = $pdo->prepare(
      "SELECT g.guild_id, g.guild_name, g.guild_avatar, g.member_count, b.title
         FROM guild AS g
         JOIN book AS b ON g.book_id = b.book_id
        WHERE g.book_id = ? AND g.guild_status = '正常'
        ORDER BY g.member_count DESC"
    );
    $stmt->execute([$bookId]);
    $guilds = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
      'success' => true,
      'data'    => $guilds,
      'total'   => count($guilds),
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[guilds_reading_book] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗。'], JSON_UNESCAPED_UNICODE);
  }
?>
