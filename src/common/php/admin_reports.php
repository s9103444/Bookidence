<?php
  require __DIR__ . '/admin_bootstrap.php';

  // 檢舉編號的格式只寫這一次，撈資料和搜尋都用它
  $reportNoSql = "CONCAT(DATE_FORMAT(r.created_at, '%y%m%d'), '-', r.report_id)";

  // 「一則內容」怎麼認：類型 ＋ 內容編號。兩個都要 ——
  // 心得 1 號和留言 1 號是兩個不同的東西，只看編號會被當成同一則
  $contentKey = "r.target_type, COALESCE(r.b_thought_id, r.message_id)";

  $keyword = trim($_GET['keyword'] ?? '');
  $status  = $_GET['status'] ?? '';
  $type    = $_GET['type'] ?? '';
  $id      = (int)($_GET['id'] ?? 0);
  // 有傳這個參數就一定要篩。前端如果送來不是數字的東西，(int) 會變成 0，
  // 而 user_id 從 1 開始 —— 撈不到正是對的答案（沒有這個人），不能當成「不篩」
  $reportedRaw = $_GET['reported'] ?? '';
  $reported    = (int)$reportedRaw;

  $perPage = 10;
  $page    = max(1, (int)($_GET['page'] ?? 1));
  $offset  = ($page - 1) * $perPage;

  error_log("[admin_reports] 收到 keyword=$keyword status=$status type=$type id=$id page=$page");

  // ── 條件先各自準備好，等一下按需要組合 ────────────────────────
  // 每個條件都是 [SQL 片段, 參數陣列] 一對，沒填就是 null

  $kwCond = null;

  if ($keyword !== '') {
    $like = "%$keyword%";

    // 管理員可能貼完整編號 260901-8，也可能只打日期 260901 找那天的，
    // 所以整串去跟編號模糊比對（開頭的 # 是顯示用的，先去掉）
    $likeNo = '%' . ltrim($keyword, '#') . '%';

    $kwCond = [
      "(author.nickname LIKE ? OR reporter.nickname LIKE ?
        OR t.bth_content LIKE ? OR d.content LIKE ?
        OR $reportNoSql LIKE ?)",
      [$like, $like, $like, $like, $likeNo],
    ];
  }

  $statusCond = $status !== '' ? ['r.status = ?',      [$status]] : null;
  $typeCond   = $type   !== '' ? ['r.target_type = ?', [$type]]   : null;
  $idCond     = $id     !== 0  ? ['r.report_id = ?',   [$id]]     : null;
  $whoCond    = $reportedRaw !== '' ? ['r.reported_user_id = ?', [$reported]] : null;

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
  [$whereSql, $params] = buildWhere([$kwCond,$statusCond,$typeCond,$idCond,$whoCond]);

  [$statusCountSql, $statusCountParams] = buildWhere([$typeCond,$kwCond,$whoCond]);

  [$typeCountSql, $typeCountParams] = buildWhere([$statusCond,$kwCond,$whoCond]);

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
    $countStmt = $pdo->prepare("SELECT COUNT(DISTINCT $contentKey) $joinSql $whereSql");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    // COALESCE = 從左到右挑第一個不是 NULL 的。
    // 心得檢舉時 d.content 是 NULL，留言檢舉時 t.bth_content 是 NULL，
    // 永遠只有一邊有值，所以合得起來。
    $stmt = $pdo->prepare(
      "SELECT MIN(r.report_id) AS report_id,
              MIN($reportNoSql) AS report_no,
              COUNT(*) AS report_count,
              r.target_type,
              GROUP_CONCAT(DISTINCT r.reason ORDER BY r.reason SEPARATOR '、') AS reason,
              MAX(r.reason_detail) AS reason_detail,
              MAX(r.status) AS status,
              MAX(r.action_taken) AS action_taken,
              MAX(r.resolution_notes) AS resolution_notes,
              MIN(r.created_at) AS created_at,
              MAX(r.resolved_at) AS resolved_at,
              MAX(r.reporter_id) AS reporter_id,
              MAX(r.reported_user_id) AS reported_user_id,
              MAX(author.nickname) AS reported_name, MAX(author.member_code) AS reported_code,
              MAX(author.account_status) AS reported_status,
              MAX(reporter.nickname) AS reporter_name, MAX(reporter.member_code) AS reporter_code,
              MAX(COALESCE(t.bth_content, d.content)) AS content,
              MAX(b.book_id) AS book_id, MAX(b.title) AS book_title,
              MAX(g.guild_name) AS guild_name,
              MAX(s.staff_name) AS staff_name
       $joinSql
       $whereSql
       GROUP BY $contentKey
       ORDER BY MAX(r.created_at) DESC, MIN(r.report_id) DESC
       LIMIT $perPage OFFSET $offset"
    );
    $stmt->execute($params);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ⚠️ 統計查詢不能加 LIMIT，它一列代表一種狀態不是一筆資料
    $statusStmt = $pdo->prepare(
      "SELECT r.status, COUNT(DISTINCT $contentKey) AS c $joinSql $statusCountSql GROUP BY r.status"
    );
    $statusStmt->execute($statusCountParams);

    $counts = ['尚未處理'=>0,'檢舉成立'=>0,'檢舉不成立'=>0];

    foreach ($statusStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $counts[$row['status']] = (int)$row['c'];
    }

    $typeStmt = $pdo->prepare(
      "SELECT r.target_type, COUNT(DISTINCT $contentKey) AS c $joinSql $typeCountSql GROUP BY r.target_type"
    );
    $typeStmt->execute($typeCountParams);

    $typeCounts = ['心得'=>0,'留言'=>0];

    foreach ($typeStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $typeCounts[$row['target_type']] = (int)$row['c'];
    }

    // 詳情頁要顯示被檢舉人的前科，列表不需要，所以只有 ?id= 撈單筆時才算
    $detail = null;

    if ($id !== 0 && count($reports) > 0) {
      $userId = $reports[0]['reported_user_id'];

      // 累計處分次數：只算警告和停權（刪除內容罰的是那則內容不是這個人），
      // 而且被撤銷的不算 —— 判錯了收回來，等於沒發生過
      $ps = $pdo->prepare(
        "SELECT COUNT(*) FROM moderation_action
          WHERE target_user_id = ? AND action_type IN ('警告','停權') AND revoked_at IS NULL"
      );
      $ps->execute([$userId]);

      // 過往被判成立的檢舉數，不含現在看的這一筆
      $us = $pdo->prepare(
        "SELECT COUNT(*) FROM report
          WHERE reported_user_id = ? AND status = '檢舉成立' AND report_id <> ?"
      );
      $us->execute([$userId, $id]);

      $detail = [
        'punish_count' => (int)$ps->fetchColumn(),
        'upheld_count' => (int)$us->fetchColumn(),
      ];
    }

    echo json_encode([
      'success'    => true,
      'data'       => $reports,
      'total'      => $total,
      'perPage'    => $perPage,
      'counts'     => $counts,
      'typeCounts' => $typeCounts,
      'detail'     => $detail,
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[admin_reports] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗。'], JSON_UNESCAPED_UNICODE);
  }
?>
