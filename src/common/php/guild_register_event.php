<?php
	// 會員報名參加讀書會活動

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, OPTIONS');
	header('Access-Control-Allow-Headers: Authorization');

	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		exit();
	}

	require 'connect_ckd101g1.php';

	try {
		$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
		$token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

		if ($token === '') {
			echo json_encode(['success' => false, 'message' => '未登入']);
			exit();
		}

		$eventId = $_POST['event_id'] ?? null;

		if (!$eventId) {
			echo json_encode(['success' => false, 'message' => '缺少 event_id 參數']);
			exit();
		}

		$userStmt = $pdo->prepare("SELECT user_id FROM member WHERE session_token = :token");
		$userStmt->execute(['token' => $token]);
		$user = $userStmt->fetch(PDO::FETCH_ASSOC);

		if (!$user) {
			echo json_encode(['success' => false, 'message' => '登入已失效，請重新登入']);
			exit();
		}
		$userId = $user['user_id'];

		$checkStmt = $pdo->prepare("SELECT 1 FROM event_registration WHERE event_id = :event_id AND user_id = :user_id");
		$checkStmt->execute(['event_id' => $eventId, 'user_id' => $userId]);
		if ($checkStmt->fetch()) {
			echo json_encode(['success' => false, 'message' => '你已經報名過這個活動了']);
			exit();
		}

		$insertStmt = $pdo->prepare("INSERT INTO event_registration (event_id, user_id) VALUES (:event_id, :user_id)");
		$insertStmt->execute(['event_id' => $eventId, 'user_id' => $userId]);

		echo json_encode(['success' => true, 'message' => '報名成功']);


	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
	}
?>