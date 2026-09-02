<?php
	// 查詢某公會現有的讀書會活動

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Authorization');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit();
    }

	require 'connect_ckd101g1.php';

	try {
		$eventId = $_GET['event_id'] ?? null;
		$guildId = $_GET['guild_id'] ?? null;

		if($eventId){
			$stmt = $pdo->prepare(
				"SELECT e.event_id, e.event_type, e.event_date, e.event_time, e.event_end_time, e.meeting_url, e.event_location, e.description, e.max_participants, e.deadline, e.book_id, b.title AS book_title, b.author AS book_author, b.publisher AS book_publisher, b.isbn AS book_isbn, b.p_date AS book_p_date, b.bc_image, g.guild_name, g.guild_avatar, om.member_code AS organizer_member_code, om.nickname AS organizer_name, om.user_id AS organizer_user_id, lm.member_code AS leader_member_code, lm.nickname AS leader_name, lm.user_id AS leader_user_id, (SELECT COUNT(*) FROM event_registration er WHERE er.event_id = e.event_id) AS participant_count
                FROM event e
                JOIN book b ON e.book_id = b.book_id
                JOIN guild g ON e.guild_id = g.guild_id
                JOIN member om ON e.organizer_user_id = om.user_id
                JOIN member lm ON e.leader_user_id = lm.user_id
                WHERE e.event_id = :event_id"
		);

		$stmt->execute(['event_id' => $eventId]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$event){
			echo json_encode(['success' => false, 'message' => '找不到這個活動']);
            exit();
			}
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
            $token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

            $viewerUserId = null;
            if ($token !== '') {
                $viewerStmt = $pdo->prepare("SELECT user_id FROM member WHERE session_token = :token");
                $viewerStmt->execute(['token' => $token]);
                $viewer = $viewerStmt->fetch(PDO::FETCH_ASSOC);
                if ($viewer) {
                    $viewerUserId = $viewer['user_id'];
                }
            }

            $event['is_organizer'] = $viewerUserId !== null && $viewerUserId == $event['organizer_user_id'];

            $registeredStmt = $pdo->prepare("SELECT 1 FROM event_registration WHERE event_id = :event_id AND user_id = :user_id");
            $registeredStmt->execute(['event_id' => $eventId, 'user_id' => $viewerUserId]);
            $event['is_registered'] = (bool) $registeredStmt->fetchColumn();

            $categoryStmt = $pdo->prepare(
                "SELECT bc.bcg_name
                FROM book_categorys bcs
                JOIN book_category bc ON bc.bcg_id = bcs.bcg_id
                WHERE bcs.book_id = :book_id"
            );
            $categoryStmt->execute(['book_id' => $event['book_id']]);
            $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

            $participantStmt = $pdo->prepare(
                "SELECT m.user_id, m.member_code, m.nickname, gm.permission_level
                FROM event_registration er
                JOIN member m ON m.user_id = er.user_id
                JOIN event e ON e.event_id = er.event_id
                JOIN guildmember gm ON gm.user_id = er.user_id AND gm.guild_id = e.guild_id
                WHERE er.event_id = :event_id
                ORDER BY FIELD(gm.permission_level, '會長', '副會長', '一般')"
            );
            $participantStmt->execute(['event_id' => $eventId]);
            $participants = $participantStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'event' => $event, 'categories' => $categories, 'participants' => $participants],JSON_UNESCAPED_UNICODE);
            exit();
            }

            if(!$guildId){
				echo json_encode(['success' => false, 'message' => '缺少guild_id參數']);
                exit();
				}

            $stmt = $pdo->prepare(
				"SELECT e.event_id, e.event_type, e.event_date, e.event_time, e.event_end_time, e.meeting_url, e.event_location,
                b.title AS book_title, b.author AS book_author, b.bc_image,
                (SELECT COUNT(*) FROM event_registration er WHERE er.event_id = e.event_id) AS participant_count
                FROM event e
                JOIN book b ON e.book_id = b.book_id
                WHERE e.guild_id = :guild_id AND e.event_status = '正常' AND e.event_date >= CURDATE()
                ORDER BY e.event_date, e.event_time"
				);
            $stmt->execute(['guild_id' => $guildId]);
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'events' => $events], JSON_UNESCAPED_UNICODE);

	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
	}
?>