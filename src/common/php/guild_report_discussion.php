<?php
	// 檢舉公會討論區的一則留言/回覆

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, OPTIONS');
	header('Access-Control-Allow-Headers: Authorization, Content-Type');

	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		exit();
	}

	require 'connect_ckd101g1.php';
	require 'guild_auth.php';

	$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
	$token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

	if ($token === '') {
		http_response_code(401);
		echo json_encode(['success' => false, 'message' => '未登入。']);
		exit();
	}

	$body = json_decode(file_get_contents('php://input'), true);

	$messageId = $body['message_id'] ?? null;
	$reason = $body['reason'] ?? '';
	$reasonDetail = trim($body['reason_detail'] ?? '');

	$validReasons = ['人身攻擊', '廣告垃圾資訊', '不當內容', '抄襲 / 侵權'];

	if (!$messageId) {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => '缺少 message_id 參數。']);
		exit();
	}

	if (!in_array($reason, $validReasons, true)) {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => '檢舉原因不正確。']);
		exit();
	}

	if (mb_strlen($reasonDetail) > 500) {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => '補充說明不可超過 500 個字。']);
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
            "SELECT d.user_id, gr.guild_id
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
            $reportedUserId = (int) $msg['user_id'];

            if ($reportedUserId === $userId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => '不能檢舉自己的留言。']);
            exit();
            }

            requireGuildMember($pdo, $msg['guild_id'], $token);

		$insert = $pdo->prepare(
			"INSERT INTO report (reporter_id, reported_user_id, message_id, target_type, reason, reason_detail)
			VALUES (:reporter_id, :reported_user_id, :message_id, '留言', :reason, :reason_detail)"
		);
		$insert->execute([
			'reporter_id' => $userId,
			'reported_user_id' => $reportedUserId,
			'message_id' => $messageId,
			'reason' => $reason,
			'reason_detail' => $reasonDetail !== '' ? $reasonDetail : null,
		]);

		echo json_encode(['success' => true, 'report_id' => (int) $pdo->lastInsertId()], JSON_UNESCAPED_UNICODE);

	} catch (PDOException $e) {
		http_response_code(500);
		echo json_encode(['success' => false, 'message' => '檢舉失敗：' . $e->getMessage()]);
	}
?>
