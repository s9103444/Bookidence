<?php
  require __DIR__ . '/admin_bootstrap.php';

  $keyword = trim($_GET['keyword'] ?? '');
  $status  = $_GET['status'] ?? '';
  $id      = (int)($_GET['id'] ?? 0);

  $perPage = 5;
  $page    = max(1, (int)($_GET['page'] ?? 1));
  $offset  = ($page - 1) * $perPage;

  $kwWhere  = [];
  $kwParams = [];

  if ($keyword !== '') {
    $kwWhere[] = '(f.ap_title LIKE ? OR f.ap_author LIKE ? OR f.isbn LIKE ? OR m.nickname LIKE ?)';

    $like = "%$keyword%";

    $kwParams[] = $like;
    $kwParams[] = $like;
    $kwParams[] = $like;
    $kwParams[] = $like;
  }

  $where  = $kwWhere;
  $params = $kwParams;

  if ($status !== '') {
    $where[] = 'f.ap_status = ?';
    $params[] = $status;
  }

  if ($id !== 0) {
    $where[] = 'f.book_ap_id = ?';
    $params[] = $id;
  }

  if (count($where) > 0) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
  } else {
    $whereSql = '';
  }

  if (count($kwWhere) > 0) {
    $kwWhereSql = 'WHERE ' . implode(' AND ', $kwWhere);
  } else {
    $kwWhereSql = '';
  }

  try {
    $countStmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM book_application_form AS f
         JOIN member AS m ON f.user_id=m.user_id
         $whereSql");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $stmt = $pdo->prepare(
      "SELECT f.book_ap_id, f.isbn, f.ap_title, f.ap_author, f.book_url,
              f.application_reason, f.created_at, f.ap_status,
              f.staff_account, f.resolved_at, f.reject_reason,
              m.nickname, m.member_code, s.staff_name
         FROM book_application_form AS f
         JOIN member AS m ON f.user_id=m.user_id
         LEFT JOIN staff AS s ON f.staff_account = s.staff_account
         $whereSql
         ORDER BY f.created_at DESC
         LIMIT $perPage OFFSET $offset"
    );
    $stmt->execute($params);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countsStmt = $pdo->prepare(
      "SELECT f.ap_status, COUNT(*) AS c
         FROM book_application_form AS f
         JOIN member AS m ON f.user_id=m.user_id
         $kwWhereSql
         GROUP BY f.ap_status");
    $countsStmt->execute($kwParams);
    $rows = $countsStmt->fetchAll(PDO::FETCH_ASSOC);

    $counts = ['待處理' => 0, '已核准' => 0, '已駁回' => 0];

    foreach ($rows as $row) {
      $counts[$row['ap_status']] = (int)$row['c'];
    }

    echo json_encode([
      'success' => true,
      'data'    => $applications,
      'total'   => $total,
      'perPage' => $perPage,
      'counts'  => $counts,
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    error_log('[admin_applications] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '查詢失敗。'], JSON_UNESCAPED_UNICODE);
  }
?>
