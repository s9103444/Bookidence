<?php
	// 公會設定－更新名稱/簡介/公告/頭貼/背景/外觀，只有會長和副會長能操作

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, OPTIONS');
	header('Access-Control-Allow-Headers: Authorization');

	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		exit();
	}

	require 'connect_ckd101g1.php';

	$guildIdForAuth = $_POST['guild_id'] ?? null;
	if ($guildIdForAuth) {
		$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
		$token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';
		if ($token === '') {
			http_response_code(401);
			echo json_encode(['success' => false, 'message' => '未登入。']);
			exit();
		}

		$callerStmt = $pdo->prepare(
			"SELECT gm.permission_level
			FROM guildmember gm
			JOIN member m ON gm.user_id = m.user_id
			WHERE gm.guild_id = :guild_id AND m.session_token = :token"
		);
		$callerStmt->execute(['guild_id' => $guildIdForAuth, 'token' => $token]);
		$callerPermission = $callerStmt->fetchColumn();

		if (!in_array($callerPermission, ['會長', '副會長'], true)) {
			http_response_code(403);
			echo json_encode(['success' => false, 'message' => '只有會長或副會長能操作這個功能。']);
			exit();
		}
	}

	function handleGuildImageUpload($fileKey, $dbColumn, $folder, $pdo, $guildId, &$errorMessage){
		if(!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK){
			return null; // 這次請求根本沒夾檔案(例如只是存名稱)，不算錯誤，直接跳過
		}
		if($_FILES[$fileKey]['size'] > 5 * 1024 * 1024){
			$errorMessage = '檔案大小不可超過 5MB';
			return false;
		}
		$allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
		$ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION));

		if(!in_array($ext, $allowedExt, true)){
			$errorMessage = '檔案格式僅支援 jpg / png / webp';
			return false;
		}

		$uploadDir = __DIR__ . '/uploads/' . $folder;
		if(!is_dir($uploadDir)){
			mkdir($uploadDir,0755, true);
		}

		$filename = $folder . '/' . uniqid('guild_', true) . '.' . $ext;
            if(!move_uploaded_file($_FILES[$fileKey]['tmp_name'], __DIR__ . '/uploads/' . $filename)){
                    $errorMessage = '圖片上傳失敗';
                    return false;
            }

            // 新檔案存成功後，查出這個公會目前的舊路徑，刪掉舊檔避免資料夾一直堆積垃圾檔
            $oldStmt = $pdo->prepare("SELECT $dbColumn FROM guild WHERE guild_id = :guild_id");
            $oldStmt->execute(['guild_id' => $guildId]);
            $oldPath = $oldStmt->fetchColumn();
            if ($oldPath) {
                    $oldFile = __DIR__ . '/uploads/' . $oldPath;
                    if (is_file($oldFile)) {
                            unlink($oldFile);
                    }
            }
            return $filename;
	}

	try {
		$guildId = $_POST['guild_id'] ?? null;

    if (!$guildId) {
        echo json_encode(['success' => false, 'message' => '缺少 guild_id 參數']);
        exit();
    }

    $fields = [];
    $params = ['guild_id' => $guildId];

    if (isset($_POST['name'])) {
        $fields[] = 'guild_name = :name';
        $params['name'] = $_POST['name'];
    }
    if (isset($_POST['intro'])) {
        $fields[] = 'intro = :intro';
        $params['intro'] = $_POST['intro'];
    }
    if (isset($_POST['announcement'])) {
        $fields[] = 'announcement = :announcement';
        $params['announcement'] = $_POST['announcement'];
    }

	$errorMessage = '';
	$avatarFilename  = handleGuildImageUpload('avatar', 'guild_avatar', 'guild-avatars', $pdo, $guildId, $errorMessage);
	if($avatarFilename=== false){
		echo json_encode(['success' => false, 'message' => $errorMessage]);
		exit();
	}
	if($avatarFilename){
		$fields[] = 'guild_avatar = :avatar';
		$params['avatar'] = $avatarFilename;
	}

	$skinFilename = handleGuildImageUpload('skin', 'guild_skin', 'guild-skins', $pdo, $guildId, $errorMessage);
	if($skinFilename === false){
		echo json_encode(['success' => false, 'message' => $errorMessage]);
		exit();
	}
	if($skinFilename){
		$fields[] = 'guild_skin = :skin';
		$params['skin'] = $skinFilename;
	}

    if (!$fields) {
        echo json_encode(['success' => false, 'message' => '沒有要更新的欄位']);
        exit();
    }

    $sql = "UPDATE guild SET " . implode(', ', $fields) . " WHERE guild_id = :guild_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => '更新成功']);

	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
	}
?>