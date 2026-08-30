<?php
	// 在公會討論區編輯自己發過的留言/回覆內容

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
	$content = trim($body['content'] ?? '');

	if (!$messageId) {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => '缺少 message_id 參數。']);
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

		$check = $pdo->prepare("SELECT user_id FROM guilddiscussion WHERE message_id = :id");
		$check->execute(['id' => $messageId]);
		$row = $check->fetch(PDO::FETCH_ASSOC);

		if (!$row) {
			http_response_code(404);
			echo json_encode(['success' => false, 'message' => '留言不存在。']);
			exit();
		}
		if ((int) $row['user_id'] !== $userId) {
			http_response_code(403);
			echo json_encode(['success' => false, 'message' => '只能編輯自己的留言。']);
			exit();
		}

		$reviewStmt = $pdo->prepare(
			"SELECT 1 FROM report WHERE message_id = :id AND status <> '檢舉不成立' LIMIT 1"
		);
		$reviewStmt->execute(['id' => $messageId]);
		if ($reviewStmt->fetch()) {
			http_response_code(403);
			echo json_encode(['success' => false, 'message' => '這則留言正在審核中，無法編輯。']);
			exit();
		}

		$update = $pdo->prepare("UPDATE guilddiscussion SET content = :content WHERE message_id = :id");
		$update->execute(['content' => $content, 'id' => $messageId]);

		echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);

	} catch (PDOException $e) {
		http_response_code(500);
		echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
	}
?>