<?php
	// 查詢公會基本資料（名稱/簡介/公告/頭貼/背景/目前讀的書），有帶登入 token 的話一併回傳目前登入者在這個公會的權限等級

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: Authorization');

	require 'connect_ckd101g1.php';

	try {
		$guildId = $_GET['guild_id'] ?? null;

		if (!$guildId) {
			echo json_encode(['success' => false, 'message' => '缺少 guild_id 參數']);
			exit();
		}

		$stmt = $pdo->prepare(
			"SELECT guild_name, intro, announcement, guild_avatar, guild_skin, member_count
			FROM guild
			WHERE guild_id = :guild_id"
		);
		$stmt->execute(['guild_id' => $guildId]);
		$guild = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$guild) {
			echo json_encode(['success' => false, 'message' => '找不到這個公會']);
			exit();
		}

		// 有帶 token 的話，順便查目前登入的人在這個公會的權限等級（不是成員或沒登入就是 null）
		$viewerPermissionLevel = null;
		$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
		$token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

		if ($token !== '') {
			$viewerStmt = $pdo->prepare(
				"SELECT gm.permission_level
				FROM guildmember gm
				JOIN member m ON gm.user_id = m.user_id
				WHERE gm.guild_id = :guild_id AND gm.member_status = '在會中' AND m.session_token = :token"
			);
			$viewerStmt->execute(['guild_id' => $guildId, 'token' => $token]);
			$viewerPermissionLevel = $viewerStmt->fetchColumn() ?: null;
		}
		$guild['viewer_permission_level'] = $viewerPermissionLevel;

		echo json_encode(['success' => true, 'guild' => $guild], JSON_UNESCAPED_UNICODE);


	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
	}
?>