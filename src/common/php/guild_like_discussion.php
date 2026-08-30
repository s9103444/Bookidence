<?php
	// 對公會討論區的一則留言/回覆按讚或取消讚(toggle)

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, OPTIONS');
	header('Access-Control-Allow-Headers: Authorization, Content-Type');

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

	$body = json_decode(file_get_contents('php://input'), true);
	$messageId = $body['message_id'] ?? null;

	if (!$messageId) {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => '缺少 message_id 參數。']);
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

		$msgStmt = $pdo->prepare(
			"SELECT gr.guild_id
			 FROM guilddiscussion d
			 JOIN segment s ON s.segment_id = d.segment_id
			 JOIN guildrecord gr ON gr.record_id = s.record_id
			 WHERE d.message_id = :id"
		);
		$msgStmt->execute(['id' => $messageId]);
		$msg = $msgStmt->fetch(PDO::FETCH_ASSOC);

		if (!$msg) {
			http_response_code(404);
			echo json_encode(['success' => false, 'message' => '留言不存在。']);
			exit();
		}

		$memberStmt = $pdo->prepare(
			"SELECT 1 FROM guildmember WHERE user_id = :user_id AND guild_id = :guild_id AND member_status = '在會中'"
		);
		$memberStmt->execute(['user_id' => $userId, 'guild_id' => $msg['guild_id']]);
		if (!$memberStmt->fetch()) {
			http_response_code(403);
			echo json_encode(['success' => false, 'message' => '你不是這個公會的成員。']);
			exit();
		}

		$checkStmt = $pdo->prepare("SELECT 1 FROM guilddiscussion_like WHERE message_id = :id AND user_id = :uid");
		$checkStmt->execute(['id' => $messageId, 'uid' => $userId]);

		if ($checkStmt->fetch()) {
			$pdo->prepare("DELETE FROM guilddiscussion_like WHERE message_id = :id AND user_id = :uid")
				->execute(['id' => $messageId, 'uid' => $userId]);
			$liked = false;
		} else {
			$pdo->prepare("INSERT INTO guilddiscussion_like (message_id, user_id) VALUES (:id, :uid)")
				->execute(['id' => $messageId, 'uid' => $userId]);
			$liked = true;
		}

		$countStmt = $pdo->prepare("SELECT COUNT(*) FROM guilddiscussion_like WHERE message_id = :id");
		$countStmt->execute(['id' => $messageId]);
		$likeCount = (int) $countStmt->fetchColumn();

		echo json_encode(['success' => true, 'liked' => $liked, 'like_count' => $likeCount], JSON_UNESCAPED_UNICODE);

	} catch (PDOException $e) {
		http_response_code(500);
		echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
	}
?>
