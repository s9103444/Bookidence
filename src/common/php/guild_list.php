<?php
  // 查詢所有正常狀態的公會列表（給前台瀏覽公會頁用），公會標籤透過目前讀的書反推分類

  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, OPTIONS');

  require 'connect_ckd101g1.php';

  try {
    $stmt = $pdo->query(
      "SELECT g.guild_id, g.guild_name, g.intro, g.guild_avatar, g.member_count,
              b.title AS current_book_title,
              GROUP_CONCAT(DISTINCT bc.bcg_name) AS tags
       FROM guild g
       JOIN book b ON b.book_id = g.book_id
       LEFT JOIN book_categorys bcj ON bcj.book_id = b.book_id
       LEFT JOIN book_category bc ON bc.bcg_id = bcj.bcg_id
       WHERE g.guild_status = '正常'
       GROUP BY g.guild_id
       ORDER BY g.member_count DESC"
    );
    $guilds = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $guilds], JSON_UNESCAPED_UNICODE);
  } catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
  }
?>
