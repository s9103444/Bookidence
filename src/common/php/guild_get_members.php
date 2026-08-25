<?php
	// 查詢公會成員列表（JOIN member 表帶出暱稱等資料）

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');

	require 'connect_ckd101g1.php';

	try {
		$guildId = $_GET['guild_id'] ?? null;
		$status = $_GET['status'] ?? '在會中';

		if (!$guildId) {
			echo json_encode(['success' => false, 'message' => '缺少 guild_id 參數']);
			exit();
		}

		$stmt = $pdo->prepare(
			"SELECT m.member_code, m.nickname, gm.permission_level
			FROM guildmember gm
			JOIN member m ON gm.user_id = m.user_id
			WHERE gm.guild_id = :guild_id AND gm.member_status = :status
			ORDER BY FIELD(gm.permission_level, '會長', '副會長', '一般')"
		);
		$stmt->execute(['guild_id' => $guildId, 'status' => $status]);
		$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

		echo json_encode(['success' => true, 'members' => $members], JSON_UNESCAPED_UNICODE);


	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
	}
?>