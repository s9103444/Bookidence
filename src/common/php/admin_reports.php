<?php
  require __DIR__ . '/admin_bootstrap.php';

  $keyword = trim($_GET['keyword'] ?? '');
  $status  = $_GET['status'] ?? '';
  $type    = $_GET['type'] ?? '';
  $id      = (int)($_GET['id'] ?? 0);

  $perPage = 10;
  $page    = max(1, (int)($_GET['page'] ?? 1));
  $offset  = ($page - 1) * $perPage;

  error_log("[admin_reports] 收到 keyword=$keyword status=$status type=$type id=$id page=$page");

  // ── 條件先各自準備好，等一下按需要組合 ────────────────────────
  // 每個條件都是 [SQL 片段, 參數陣列] 一對，沒填就是 null

  $kwCond = null;

  if ($keyword !== '') {
    $like = "%$keyword%";

    $kwSql = '(author.nickname LIKE ? OR reporter.nickname LIKE ? OR t.bth_content LIKE ? OR d.content LIKE ?';
    $kwParams = [$like, $like, $like, $like];

    // 編號顯示成 260901-7，管理員可能整串貼進來，也可能只打 7。
    // 去掉開頭的 #，有 - 就取最後一個 - 右邊那段（左邊是日期，不是編號）
    $kwId = ltrim($keyword, '#');

    if (str_contains($kwId, '-')) {
      $kwId = substr($kwId, strrpos($kwId, '-') + 1);
    }

    // 是數字才掛編號條件。用 OR 是因為這一串本來就是「打哪個都找得到」
    if (ctype_digit($kwId)) {
      $kwSql     .= ' OR r.report_id = ?';
      $kwParams[] = (int)$kwId;
    }

    $kwCond = [$kwSql . ')', $kwParams];
  }

  $statusCond = $status !== '' ? ['r.status = ?',      [$status]] : null;
  $typeCond   = $type   !== '' ? ['r.target_type = ?', [$type]]   : null;
  $idCond     = $id     !== 0  ? ['r.report_id = ?',   [$id]]     : null;

  // 把要用的條件接成一句 WHERE，順便把參數照順序疊好。
  // 一個都沒有就回傳空字串（沒有 WHERE 那一行）
  function buildWhere(array $conds) {
    $sql    = [];
    $params = [];

    foreach ($conds as $cond) {//看不懂這邊的邏輯
      if ($cond === null) continue;
      $sql[]  = $cond[0];
      $params = array_merge($params, $cond[1]);
    }

    if (count($sql) === 0) return ['', []];

    return ['WHERE ' . implode(' AND ', $sql), $params];
  }

  // 資料用一組條件，兩組統計各用一組 —— 三組不一樣，不要合併
  [$whereSql, $params] = buildWhere([$kwCond,$statusCond,$typeCond,$idCond]);

  [$statusCountSql, $statusCountParams] = buildWhere([$typeCond,$kwCond]);

  [$typeCountSql, $typeCountParams] = buildWhere([$statusCond,$kwCond]);

  // ── 五張表都要接進來，三句查詢共用同一段 JOIN ──────────────────
  // author / reporter 是同一張 member 表 JOIN 兩次，靠別名分辨是哪一邊的人。
  // 心得走 t → b 拿書名，留言走 d → sg → gr → g 拿公會名，兩條路都可能整條是 NULL，
  // 所以除了兩個人以外一律 LEFT JOIN（用 JOIN 的話另一種檢舉會整筆消失）
  $joinSql = "
      FROM report AS r
      JOIN member AS author   ON r.reported_user_id = author.user_id
      JOIN member AS reporter ON r.reporter_id      = reporter.user_id
      LEFT JOIN book_thoughts   AS t  ON r.b_thought_id = t.b_thought_id
      LEFT JOIN book            AS b  ON t.book_id      = b.book_id
      LEFT JOIN guilddiscussion AS d  ON r.message_id   = d.message_id
      LEFT JOIN segment         AS sg ON d.segment_id   = sg.segment_id
      LEFT JOIN guildrecord     AS gr ON sg.record_id   = gr.record_id
      LEFT JOIN guild           AS g  ON gr.guild_id    = g.guild_id
      LEFT JOIN staff           AS s  ON r.staff_account = s.staff_account
  ";

  try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) $joinSql $whereSql");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    // COALESCE = 從左到右挑第一個不是 NULL 的。
    // 心得檢舉時 d.content 是 NULL，留言檢舉時 t.bth_content 是 NULL，
    // 永遠只有一邊有值，所以合得起來。
    $stmt = $pdo->prepare(
      "SELECT r.report_id,
              CONCAT(DATE_FORMAT(r.created_at, '%y%m%d'), '-', r.report_id) AS report_no,
              r.target_type, r.reason, r.reason_detail,
              r.status, r.action_taken, r.resolution_notes,
              r.created_at, r.resolved_at,
              r.reporter_id, r.reported_user_id,
              author.nickname AS reported_name, author.member_code AS reported_code,
              author.account_status AS reported_status,
              reporter.nickname AS reporter_name, reporter.member_code AS reporter_code,
              COALESCE(t.bth_content, d.content) AS content,
              b.book_id, b.title AS book_title,
              g.guild_name,
              s.staff_name
       $joinSql
       $whereSql
       ORDER BY r.created_at DESC, r.report_id DESC
       LIMIT $perPage OFFSET $offset"
    );
    $stmt->execute($params);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ⚠️ 統計查詢不能加 LIMIT，它一列代表一種狀態不是一筆資料
    $statusStmt = $pdo->prepare(
      "SELECT r.status, COUNT(*) AS c $joinSql $statusCountSql GROUP BY r.status"
    );
    $statusStmt->execute($statusCountParams);

    $counts = ['尚未處理'=>0,'檢舉成立'=>0,'檢舉不成立'=>0];

    foreach ($statusStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $counts[$row['status']] = (int)$row['c'];
    }

    $typeStmt = $pdo->prepare(
      "SELECT r.target_type, COUNT(*) AS c $joinSql $typeCountSql GROUP BY r.target_type"
    );
    $typeStmt->execute($typeCountParams);

    $typeCounts = ['心得'=>0,'留言'=>0];

    foreach ($typeStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $typeCounts[$row['target_type']] = (int)$row['c'];
    }

    echo json_encode([
      'success'    => true,
      'data'       => $reports,
      'total'      => $total,
      'perPage'    => $perPage,
      'counts'     => $counts,
      'typeCounts' => $typeCounts,
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[admin_reports] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗。'], JSON_UNESCAPED_UNICODE);
  }
?>
