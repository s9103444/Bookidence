<?php
	// 在公會討論區發表新留言或回覆某則留言（有帶 parent_message_id 就是回覆）

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

	$segmentId = $body['segment_id'] ?? null;
	$content = trim($body['content'] ?? '');
	$parentMessageId = $body['parent_message_id'] ?? null;

	if (!$segmentId) {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => '缺少 segment_id 參數。']);
		exit();
	}

	if ($content === '' || mb_strlen($content) > 2000) {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => '留言內容不可為空，且不可超過 2000 個字。']);
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

		$segmentStmt = $pdo->prepare(
			"SELECT gr.guild_id
			 FROM segment s
			 JOIN guildrecord gr ON gr.record_id = s.record_id
			 WHERE s.segment_id = :segment_id"
		);
		$segmentStmt->execute(['segment_id' => $segmentId]);
		$segment = $segmentStmt->fetch(PDO::FETCH_ASSOC);

		if (!$segment) {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => '找不到這個段落。']);
			exit();
		}

		$memberStmt = $pdo->prepare(
			"SELECT 1 FROM guildmember WHERE user_id = :user_id AND guild_id = :guild_id AND member_status = '在會中'"
		);
		$memberStmt->execute(['user_id' => $userId, 'guild_id' => $segment['guild_id']]);
		if (!$memberStmt->fetch()) {
			http_response_code(403);
			echo json_encode(['success' => false, 'message' => '你不是這個公會的成員。']);
			exit();
		}

		if ($parentMessageId !== null) {
			$parentStmt = $pdo->prepare(
				"SELECT parent_message_id FROM guilddiscussion WHERE message_id = :id AND segment_id = :segment_id"
			);
			$parentStmt->execute(['id' => $parentMessageId, 'segment_id' => $segmentId]);
			$parent = $parentStmt->fetch(PDO::FETCH_ASSOC);

			if (!$parent) {
				http_response_code(400);
				echo json_encode(['success' => false, 'message' => '找不到要回覆的留言。']);
				exit();
			}
			if ($parent['parent_message_id'] !== null) {
				http_response_code(400);
				echo json_encode(['success' => false, 'message' => '不能回覆一則回覆。']);
				exit();
			}
		}

		$insert = $pdo->prepare(
			"INSERT INTO guilddiscussion (segment_id, parent_message_id, user_id, content, photo)
			 VALUES (:segment_id, :parent_message_id, :user_id, :content, '')"
		);
		$insert->execute([
			'segment_id' => $segmentId,
			'parent_message_id' => $parentMessageId,
			'user_id' => $userId,
			'content' => $content,
		]);
		$messageId = (int) $pdo->lastInsertId();

		$postedAtStmt = $pdo->prepare("SELECT posted_at FROM guilddiscussion WHERE message_id = :id");
		$postedAtStmt->execute(['id' => $messageId]);
		$postedAt = $postedAtStmt->fetchColumn();

		echo json_encode(['success' => true, 'message_id' => $messageId, 'posted_at' => $postedAt], JSON_UNESCAPED_UNICODE);

	} catch (PDOException $e) {
		http_response_code(500);
		echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
	}
?>