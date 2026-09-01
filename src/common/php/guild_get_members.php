<?php
	// 查詢公會成員列表（JOIN member 表帶出暱稱等資料），如果有帶登入 token 會一併回傳目前登入者是不是這個公會的成員

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, OPTIONS');
	header('Access-Control-Allow-Headers: Authorization');

	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		exit();
	}

	require 'connect_ckd101g1.php';

	try {
		$guildId = $_GET['guild_id'] ?? null;
		$status = $_GET['status'] ?? '在會中';

		if (!$guildId) {
			echo json_encode(['success' => false, 'message' => '缺少 guild_id 參數']);
			exit();
		}

		$stmt = $pdo->prepare(
			"SELECT gm.user_id, m.member_code, m.nickname, m.bio, gm.permission_level
			FROM guildmember gm
			JOIN member m ON gm.user_id = m.user_id
			WHERE gm.guild_id = :guild_id AND gm.member_status = :status
			ORDER BY FIELD(gm.permission_level, '會長', '副會長', '一般')"
		);
		$stmt->execute(['guild_id' => $guildId, 'status' => $status]);
		$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// 有帶 token 的話，順便查目前登入的人是不是這個公會「在會中」的成員、以及他的權限等級，讓前端不用自己比對 user_id
		$viewerIsMember = false;
		$viewerPermissionLevel = null;
		$viewerMemberCode = null;
		$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
		$token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

		if ($token !== '') {
			$viewerStmt = $pdo->prepare(
				"SELECT gm.permission_level, m.member_code
				FROM guildmember gm
				JOIN member m ON gm.user_id = m.user_id
				WHERE gm.guild_id = :guild_id AND gm.member_status = '在會中' AND m.session_token = :token"
			);
			$viewerStmt->execute(['guild_id' => $guildId, 'token' => $token]);
			$viewer = $viewerStmt->fetch(PDO::FETCH_ASSOC);
			if ($viewer) {
				$viewerIsMember = true;
				$viewerPermissionLevel = $viewer['permission_level'];
				$viewerMemberCode = $viewer['member_code'];
			}
		}

		echo json_encode(
			[
				'success' => true,
				'members' => $members,
				'viewer_is_member' => $viewerIsMember,
				'viewer_permission_level' => $viewerPermissionLevel,
				'viewer_member_code' => $viewerMemberCode,
			],
			JSON_UNESCAPED_UNICODE
		);


	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
	}
?>
