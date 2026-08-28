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
		$pdo->beginTransaction();

		$eventStmt = $pdo->prepare("SELECT event_status, max_participants, deadline FROM event WHERE event_id = :event_id FOR UPDATE");
		$eventStmt->execute(['event_id' => $eventId]);
		$event = $eventStmt->fetch(PDO::FETCH_ASSOC);
		
		if (!$event) {
			$pdo->rollBack();
			echo json_encode(['success' => false, 'message' => '找不到這個活動']);
			exit();
		}

		if ($event['event_status'] !== '正常') {
			$pdo->rollBack();
			echo json_encode(['success' => false, 'message' => '這個活動已經取消']);
			exit();
		}

		$today = date('Y-m-d');
		if ($event['deadline'] < $today) {
			$pdo->rollBack();
			echo json_encode(['success' => false, 'message' => '已超過報名截止時間']);
			exit();
		}

		$checkStmt = $pdo->prepare("SELECT 1 FROM event_registration WHERE event_id = :event_id AND user_id = :user_id");
		$checkStmt->execute(['event_id' => $eventId, 'user_id' => $userId]);
		if ($checkStmt->fetch()) {
			$pdo->rollBack();
			echo json_encode(['success' => false, 'message' => '你已經報名過這個活動了']);
			exit();
		}

		$countStmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM event_registration WHERE event_id = :event_id");
		$countStmt->execute(['event_id' => $eventId]);
		$currentCount = $countStmt->fetch(PDO::FETCH_ASSOC)['cnt'];

		if ($currentCount >= $event['max_participants']) {
			$pdo->rollBack();
			echo json_encode(['success' => false, 'message' => '報名人數已額滿']);
			exit();
		}

		$insertStmt = $pdo->prepare("INSERT INTO event_registration (event_id, user_id) VALUES (:event_id, :user_id)");
		$insertStmt->execute(['event_id' => $eventId, 'user_id' => $userId]);

		$pdo->commit();
		echo json_encode(['success' => true, 'message' => '報名成功']);


	} catch (PDOException $e) {
		if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
}
?>