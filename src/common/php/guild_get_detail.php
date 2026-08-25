<?php
	// 查詢公會基本資料（名稱/簡介/公告/頭貼/背景/目前讀的書）

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');

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

		echo json_encode(['success' => true, 'guild' => $guild], JSON_UNESCAPED_UNICODE);


	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
	}
?>