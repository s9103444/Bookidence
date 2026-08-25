<?php
	// 審核申請中成員／踢出成員，只有會長、副會長能操作

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
		$action = $_POST['action'] ?? null; //踢出公會，申請中，拒絕加入

		if (!$guildId || !$memberCode || !$action) {
			echo json_encode(['success' => false, 'message' => '缺少參數']);
			exit();
		}

		if($action === 'kick'){
			$stmt = $pdo->prepare(
				"UPDATE guildmember gm
				JOIN member m ON gm.user_id = m.user_id
				SET gm.member_status = '已踢出'
				WHERE gm.guild_id = :guild_id AND m.member_code = :member_code"
			);
		
		$stmt->execute(['guild_id' => $guildId, 'member_code' => $memberCode]);
	}elseif ($action === 'approve'){
		$stmt = $pdo->prepare(
			"UPDATE guildmember gm
			JOIN member m ON gm.user_id = m.user_id
			SET gm.member_status = '在會中'
			WHERE gm.guild_id = :guild_id AND m.member_code = :member_code"
		);
		$stmt->execute(['guild_id' => $guildId, 'member_code' => $memberCode]);
	} elseif ($action === 'reject') {
		$stmt = $pdo->prepare(
			"DELETE gm FROM guildmember gm
			JOIN member m ON gm.user_id = m.user_id
			WHERE gm.guild_id = :guild_id AND m.member_code = :member_code"
		);
    $stmt->execute(['guild_id' => $guildId, 'member_code' => $memberCode]);
	}else{
		echo json_encode(['success' => false, 'message' => '不明的操作類型']);
    exit();
	}
	echo json_encode(['success' => true, 'message' => '操作成功']);

		

	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
	}
?>