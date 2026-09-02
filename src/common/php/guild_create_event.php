<?php
	// 建立新的讀書會活動

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

		if($token === ''){
			echo json_encode(['success' => false, 'message' => '未登入']);
			exit();
		}

		$guildId = $_POST['guild_id'] ?? null;
		$eventType = $_POST['event_type'] ?? null;
		$eventDate = $_POST['event_date'] ?? null;
		$eventTime = $_POST['event_time'] ?? null;
		$eventEndTime = $_POST['event_end_time'] ?? null;
		$location = $_POST['location'] ?? null;
		$description = $_POST['description'] ?? null;
		$maxParticipants = $_POST['max_participants'] ?? null;
		$deadline = $_POST['deadline'] ?? null;
		$leaderMemberCode = $_POST['leader_member_code'] ?? null;

		if(!$guildId || !$eventType || !$eventDate || !$eventTime || !$eventEndTime || !$location || !$description || !$maxParticipants || !$deadline || !$leaderMemberCode){
			echo json_encode(['success' => false, 'message' => '缺乏參數']);
			exit();
		}

		// 用 token 查出發起人
		$organizerStmt = $pdo->prepare("SELECT user_id FROM member WHERE session_token = :token");
		$organizerStmt->execute(['token' => $token]);
		$organizer = $organizerStmt->fetch(PDO::FETCH_ASSOC);

		if(!$organizer){
			echo json_encode(['success' => false, 'message' => '登入已失效，請重新登入']);
			exit();
		}
		$organizerUserId = $organizer['user_id'];

		$membershipStmt = $pdo->prepare(
			"SELECT 1 FROM guildmember WHERE guild_id = :guild_id AND user_id = :user_id AND member_status = '在會中'"
		);
		$membershipStmt->execute(['guild_id' => $guildId, 'user_id' => $organizerUserId]);
		if (!$membershipStmt->fetchColumn()) {
			http_response_code(403);
			echo json_encode(['success' => false, 'message' => '你不是這個公會的會員，無法建立活動。']);
			exit();
		}

		// 用領讀人的 member_code 查 user_id
		$leaderStmt = $pdo->prepare("SELECT user_id FROM member WHERE member_code = :member_code");
		$leaderStmt->execute(['member_code' => $leaderMemberCode]);
		$leader = $leaderStmt->fetch(PDO::FETCH_ASSOC);

		if(!$leader){
			echo json_encode(['success' => false, 'message' => '找不到領讀人']);
			exit();
		}
		$leaderUserId = $leader['user_id'];

		// 查公會目前的書
		$guildStmt = $pdo->prepare("SELECT book_id FROM guild WHERE guild_id = :guild_id");
		$guildStmt->execute(['guild_id' => $guildId]);
		$guild = $guildStmt->fetch(PDO::FETCH_ASSOC);

		if(!$guild){
			echo json_encode(['success' => false, 'message' => '找不到公會']);
			exit();
		}
		$bookId = $guild['book_id'];
		// 依線上/線下決定要存 meeting_url 還是 event_location
		$meetingUrl = str_contains($eventType, '線上') ? $location : null;
		$eventLocation = str_contains($eventType, '線下') ? $location : null;

		$insertStmt = $pdo->prepare("INSERT INTO event (guild_id, book_id, organizer_user_id, leader_user_id, event_type, event_date, event_time, event_end_time, meeting_url, event_location, description, max_participants, deadline, event_status)
		VALUES (:guild_id, :book_id, :organizer_user_id, :leader_user_id, :event_type, :event_date, :event_time, :event_end_time, :meeting_url, :event_location, :description, :max_participants, :deadline, '正常')");
		$insertStmt->execute([
			'guild_id' => $guildId,
			'book_id' => $bookId,
			'organizer_user_id' => $organizerUserId,
			'leader_user_id' => $leaderUserId,
			'event_type' => $eventType,
			'event_date' => $eventDate,
			'event_time' => $eventTime,
			'event_end_time' => $eventEndTime,
			'meeting_url' => $meetingUrl,
			'event_location' => $eventLocation,
			'description' => $description,
			'max_participants' => $maxParticipants,
			'deadline' => $deadline,
		]);

		echo json_encode(['success' => true, 'message' => '活動已建立']);


	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
	}
?>