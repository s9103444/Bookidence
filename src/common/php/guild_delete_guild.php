<?php
	// 刪除公會，只有現任會長本人能操作

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, OPTIONS');
	header('Access-Control-Allow-Headers: Authorization');

	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		exit();
	}

	require 'connect_ckd101g1.php';
	require 'guild_auth.php';

	try {
		$guildId = $_POST['guild_id'] ?? null;
		if(!$guildId){
			echo json_encode(['success' => false, 'message' => '缺乏公會ID參數']);
			exit();
		}

		$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
		$token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';
		if ($token === '') {
			http_response_code(401);
			echo json_encode(['success' => false, 'message' => '未登入。']);
			exit();
		}

		$callerPermission = requireGuildMember($pdo, $guildId, $token);
            requireGuildRole($callerPermission, ['會長'], '只有會長能解散公會。');

		$stmt = $pdo->prepare("UPDATE guild SET guild_status = '已解散' WHERE guild_id = :guild_id");
		$stmt->execute(['guild_id' => $guildId]);

		echo json_encode(['success' => true, 'message' => '公會已解散']);

	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
	}
?>