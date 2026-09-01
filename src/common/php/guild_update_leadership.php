<?php
	// 「賦予副會長 + 轉移會長」，只有現任會長本人能操作

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, OPTIONS');
	header('Access-Control-Allow-Headers: Authorization');

	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		exit();
	}

	require 'connect_ckd101g1.php';

	try {
		$guildId = $_POST['guild_id'] ?? null;
		$memberCode = $_POST['member_code'] ?? null;
		$newRole = $_POST['new_role'] ?? null; // '會長' 或 '副會長'

		if (!$guildId || !$memberCode || !$newRole) {
			echo json_encode(['success' => false, 'message' => '缺少參數']);
			exit();
		}

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
		$callerStmt->execute(['guild_id' => $guildId, 'token' => $token]);
		$callerPermission = $callerStmt->fetchColumn();

		if ($callerPermission !== '會長') {
			http_response_code(403);
			echo json_encode(['success' => false, 'message' => '只有會長能操作這個功能。']);
			exit();
		}

		if ($newRole === '會長') {
			$pdo->beginTransaction();

			// 原本的會長降為一般會員
			$demoteStmt = $pdo->prepare(
				"UPDATE guildmember SET permission_level = '一般'
				WHERE guild_id = :guild_id AND permission_level = '會長'"
			);
			$demoteStmt->execute(['guild_id' => $guildId]);

			// 新人升為會長
			$promoteStmt = $pdo->prepare(
				"UPDATE guildmember gm
				JOIN member m ON gm.user_id = m.user_id
				SET gm.permission_level = '會長'
				WHERE gm.guild_id = :guild_id AND m.member_code = :member_code"
			);
			$promoteStmt->execute(['guild_id' => $guildId, 'member_code' => $memberCode]);

			$pdo->commit();
		} else {
			$stmt = $pdo->prepare(
				"UPDATE guildmember gm
				JOIN member m ON gm.user_id = m.user_id
				SET gm.permission_level = :new_role
				WHERE gm.guild_id = :guild_id AND m.member_code = :member_code"
			);
			$stmt->execute(['guild_id' => $guildId, 'member_code' => $memberCode, 'new_role' => $newRole]);
		}

		echo json_encode(['success' => true, 'message' => '身份已更新']);


	} catch (PDOException $e) {
		if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
		echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
	}
?>