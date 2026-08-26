<?php
  // 建立讀書公會：建立公會本體 + 第一筆讀書紀錄 + 章節分段，並把建立者設為會長

  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, OPTIONS');
  header('Access-Control-Allow-Headers: Authorization');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
  }

  require 'connect_ckd101g1.php';

  $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
  $token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

  if ($token === '') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => '未登入。']);
    exit();
  }

  $guildName = trim($_POST['guild_name'] ?? '');
  $intro = trim($_POST['intro'] ?? '');
  $announcement = trim($_POST['announcement'] ?? '');
  $bookId = $_POST['book_id'] ?? null;
  $segmentsJson = $_POST['segments'] ?? null;

  if ($guildName === '' || mb_strlen($guildName) > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '公會名稱不可為空，且不可超過 100 個字。']);
    exit();
  }

  if (mb_strlen($intro) > 500) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '公會簡介不可超過 500 個字。']);
    exit();
  }

  if (!$bookId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請選擇第一本讀物。']);
    exit();
  }

  $segments = json_decode($segmentsJson ?? '[]', true);
  if (!is_array($segments) || count($segments) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請至少設定一個討論板。']);
    exit();
  }

  if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請上傳公會頭像。']);
    exit();
  }

  if ($_FILES['avatar']['size'] > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '頭像檔案大小不可超過 5MB。']);
    exit();
  }

  $allowedExt = ['jpg', 'jpeg', 'png'];
  $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));

  if (!in_array($ext, $allowedExt, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '頭像檔案格式僅支援 jpg / png。']);
    exit();
  }

  try {
    $userStmt = $pdo->prepare("SELECT user_id FROM member WHERE session_token = :token");
    $userStmt->execute(['token' => $token]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => '登入已過期，請重新登入。']);
      exit();
    }
    $userId = (int) $user['user_id'];

    $bookStmt = $pdo->prepare("SELECT book_id FROM book WHERE book_id = :id");
    $bookStmt->execute(['id' => $bookId]);
    if (!$bookStmt->fetch()) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => '選擇的書籍不存在。']);
      exit();
    }

    $uploadDir = __DIR__ . '/../uploads/guild-avatars';
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }
    $avatarFilename = 'guild-avatars/' . uniqid('guild_', true) . '.' . $ext;

    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], __DIR__ . '/../uploads/' . $avatarFilename)) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => '頭像上傳失敗。']);
      exit();
    }

    $pdo->beginTransaction();

    $insertGuild = $pdo->prepare(
      "INSERT INTO guild (guild_code, book_id, guild_name, founded_at, guild_avatar, intro, approval_required, member_count, guild_status, guild_skin, announcement)
       VALUES ('PENDING', :book_id, :guild_name, CURDATE(), :avatar, :intro, 0, 1, '正常', '', :announcement)"
    );
    $insertGuild->execute([
      'book_id' => $bookId,
      'guild_name' => $guildName,
      'avatar' => $avatarFilename,
      'intro' => $intro,
      'announcement' => $announcement !== '' ? $announcement : null,
    ]);
    $guildId = (int) $pdo->lastInsertId();

    $guildCode = 'GLD' . str_pad((string) $guildId, 7, '0', STR_PAD_LEFT);
    $updateCode = $pdo->prepare("UPDATE guild SET guild_code = :code WHERE guild_id = :id");
    $updateCode->execute(['code' => $guildCode, 'id' => $guildId]);

    $insertMember = $pdo->prepare(
      "INSERT INTO guildmember (user_id, guild_id, permission_level, member_status)
       VALUES (:user_id, :guild_id, '會長', '在會中')"
    );
    $insertMember->execute(['user_id' => $userId, 'guild_id' => $guildId]);

    $lastDueDate = end($segments)['dueDate'] ?? date('Y-m-d');
    $insertRecord = $pdo->prepare(
      "INSERT INTO guildrecord (book_id, guild_id, record_date, end_date)
       VALUES (:book_id, :guild_id, CURDATE(), :end_date)"
    );
    $insertRecord->execute([
      'book_id' => $bookId,
      'guild_id' => $guildId,
      'end_date' => $lastDueDate,
    ]);
    $recordId = (int) $pdo->lastInsertId();

    $insertSegment = $pdo->prepare(
      "INSERT INTO segment (record_id, start_chapter, end_chapter, expected_end_date, sort_order)
       VALUES (:record_id, :start_chapter, :end_chapter, :expected_end_date, :sort_order)"
    );
    foreach ($segments as $index => $segment) {
      $insertSegment->execute([
        'record_id' => $recordId,
        'start_chapter' => $segment['startChapter'],
        'end_chapter' => $segment['endChapter'],
        'expected_end_date' => $segment['dueDate'],
        'sort_order' => $index + 1,
      ]);
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'guild_id' => $guildId], JSON_UNESCAPED_UNICODE);
  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '建立公會失敗：' . $e->getMessage()]);
  }
?>
