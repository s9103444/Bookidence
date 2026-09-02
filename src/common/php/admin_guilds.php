<?php
  require __DIR__ . '/admin_bootstrap.php';

  $keyword = trim($_GET['keyword'] ?? '');
  $status  = $_GET['status'] ?? '';

  $perPage = 10;
  $page    = max(1, (int)($_GET['page'] ?? 1));
  $offset  = ($page - 1) * $perPage;

  $where  = [];
  $params = [];

  if ($keyword !== '') {
    $where[]  = '(g.guild_name LIKE ? OR g.guild_code LIKE ? OR leader.nickname LIKE ?)';
    $like     = "%$keyword%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
  }

  if ($status !== '') {
    $where[]  = 'g.guild_status = ?';
    $params[] = $status;
  }

  $whereSql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

  // 會長暱稱、關鍵字搜尋都要靠這個 join 才找得到
  $joinSql = "
    FROM guild g
    LEFT JOIN guildmember gm ON gm.guild_id = g.guild_id AND gm.permission_level = '會長' AND gm.member_status = '在會中'
    LEFT JOIN member leader ON leader.user_id = gm.user_id
  ";

  try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) $joinSql $whereSql");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $stmt = $pdo->prepare(
      "SELECT g.guild_id, g.guild_code, g.guild_name, g.founded_at, g.member_count, g.guild_status,
              leader.nickname AS leader_nickname
       $joinSql
       $whereSql
       ORDER BY g.guild_id DESC
       LIMIT $perPage OFFSET $offset"
    );
    $stmt->execute($params);
    $guilds = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 狀態分頁籤的計數：忽略 status 篩選本身，但保留關鍵字篩選
    $countWhere  = [];
    $countParams = [];

    if ($keyword !== '') {
      $countWhere[]  = '(g.guild_name LIKE ? OR g.guild_code LIKE ? OR leader.nickname LIKE ?)';
      $like          = "%$keyword%";
      $countParams[] = $like;
      $countParams[] = $like;
      $countParams[] = $like;
    }

    $countWhereSql = count($countWhere) > 0 ? 'WHERE ' . implode(' AND ', $countWhere) : '';

    $statusStmt = $pdo->prepare("SELECT g.guild_status, COUNT(*) AS c $joinSql $countWhereSql GROUP BY g.guild_status");
    $statusStmt->execute($countParams);

    $statusCounts = ['正常' => 0, '停權' => 0, '已解散' => 0];

    foreach ($statusStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $statusCounts[$row['guild_status']] = (int)$row['c'];
    }

    echo json_encode([
      'success'      => true,
      'data'         => $guilds,
      'total'        => $total,
      'perPage'      => $perPage,
      'statusCounts' => $statusCounts,
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[admin_guilds] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗。'], JSON_UNESCAPED_UNICODE);
  }
?>
