<?php
  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
  }

  require 'connect_ckd101g1.php';

  $body = json_decode(file_get_contents('php://input'), true);

  $email = trim($body['email'] ?? '');
  $password = (string) ($body['password'] ?? '');
  $nickname = trim($body['nickname'] ?? '');
  $categoryIds = $body['selectedCategoryIds'] ?? [];
  $genderInput = $body['selectedGender'] ?? '';
  $hairAppearId = (string) ($body['selectedHairColorId'] ?? '');
  $skinAppearId = (string) ($body['selectedSkinColorId'] ?? '');
  $eyeAppearId = (string) ($body['selectedEyeColorId'] ?? '');

  $genderMap = [
    'female' => ['cn' => '女', 'appearId' => 'g01'],
    'male' => ['cn' => '男', 'appearId' => 'g02'],
  ];

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'E-mail 格式錯誤。']);
    exit();
  }

  if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '密碼長度至少需要 6 個字元。']);
    exit();
  }

  if ($nickname === '' || mb_strlen($nickname) > 10) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '暱稱不可為空，且不可超過 10 個字。']);
    exit();
  }

  if (!isset($genderMap[$genderInput])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '性別選項不正確。']);
    exit();
  }

  if (!is_array($categoryIds)) {
    $categoryIds = [];
  }
  $categoryIds = array_values(array_unique(array_map('intval', $categoryIds)));

  try {
    $checkEmail = $pdo->prepare("SELECT user_id FROM member WHERE email = :email");
    $checkEmail->execute(['email' => $email]);
    if ($checkEmail->fetch()) {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '此 E-mail 已被註冊。']);
      exit();
    }

    $genderCn = $genderMap[$genderInput]['cn'];
    $genderAppearId = $genderMap[$genderInput]['appearId'];

    $appearCheck = $pdo->prepare(
      "SELECT appear_id FROM appear WHERE appear_id = :id AND type = :type AND gender = :gender"
    );

    $appearCheck->execute(['id' => $hairAppearId, 'type' => '髮色', 'gender' => $genderCn]);
    if (!$appearCheck->fetch()) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => '髮色選項不正確。']);
      exit();
    }

    $appearCheck->execute(['id' => $skinAppearId, 'type' => '膚色', 'gender' => $genderCn]);
    if (!$appearCheck->fetch()) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => '膚色選項不正確。']);
      exit();
    }

    $appearCheck->execute(['id' => $eyeAppearId, 'type' => '瞳色', 'gender' => $genderCn]);
    if (!$appearCheck->fetch()) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => '瞳色選項不正確。']);
      exit();
    }

    if (!empty($categoryIds)) {
      $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
      $categoryCheck = $pdo->prepare("SELECT bcg_id FROM book_category WHERE bcg_id IN ($placeholders)");
      $categoryCheck->execute($categoryIds);
      $validIds = $categoryCheck->fetchAll(PDO::FETCH_COLUMN);
      if (count($validIds) !== count($categoryIds)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '閱讀偏好選項不正確。']);
        exit();
      }
    }

    $pdo->beginTransaction();

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32));

    $insertMember = $pdo->prepare(
      "INSERT INTO member (member_code, nickname, email, password, session_token)
       VALUES ('PENDING', :nickname, :email, :password, :token)"
    );
    $insertMember->execute([
      'nickname' => $nickname,
      'email' => $email,
      'password' => $hashedPassword,
      'token' => $token,
    ]);
    $userId = (int) $pdo->lastInsertId();

    $memberCode = 'MKD' . str_pad((string) $userId, 8, '0', STR_PAD_LEFT);
    $updateCode = $pdo->prepare("UPDATE member SET member_code = :code WHERE user_id = :id");
    $updateCode->execute(['code' => $memberCode, 'id' => $userId]);

    if (!empty($categoryIds)) {
      $insertCategory = $pdo->prepare(
        "INSERT INTO member_book_categorys (user_id, bcg_id) VALUES (:user_id, :bcg_id)"
      );
      foreach ($categoryIds as $bcgId) {
        $insertCategory->execute(['user_id' => $userId, 'bcg_id' => $bcgId]);
      }
    }

    $insertAppear = $pdo->prepare(
      "INSERT INTO user_appear (user_id, appear_id) VALUES (:user_id, :appear_id)"
    );
    foreach ([$genderAppearId, $hairAppearId, $skinAppearId, $eyeAppearId] as $appearId) {
      $insertAppear->execute(['user_id' => $userId, 'appear_id' => $appearId]);
    }

    $pdo->commit();

    $stmt = $pdo->prepare(
      "SELECT user_id, member_code, nickname, email, bio, account_status, total_exp
       FROM member
       WHERE user_id = :id"
    );
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'token' => $token, 'user' => $user], JSON_UNESCAPED_UNICODE);
  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '註冊失敗：' . $e->getMessage()]);
  }
?>
