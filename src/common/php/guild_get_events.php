<?php
	// 查詢某公會現有的讀書會活動

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');

	require 'connect_ckd101g1.php';

	try {
		$eventId = $_GET['event_id'] ?? null;
		$guildId = $_GET['guild_id'] ?? null;

		if($eventId){
			$stmt = $pdo->prepare(
				"SELECT e.event_id, e.event_type, e.event_date, e.event_time, e.event_end_time, e.meeting_url, e.event_location, e.description, e.max_participants, e.deadline, b.title AS book_title, b.author AS book_author, b.publisher AS book_publisher, b.isbn AS book_isbn, b.p_date AS book_p_date, b.bc_image, g.guild_name, g.guild_avatar, om.member_code AS organizer_member_code, om.nickname AS organizer_name, om.user_id AS organizer_user_id, lm.member_code AS leader_member_code, lm.nickname AS leader_name, lm.user_id AS leader_user_id, (SELECT COUNT(*) FROM event_registration er WHERE er.event_id = e.event_id) AS participant_count
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
            echo json_encode(['success' => true, 'event' => $event],JSON_UNESCAPED_UNICODE);
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