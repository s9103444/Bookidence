<?php
	// 查詢某公會現有的讀書會活動

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');

	require 'connect_ckd101g1.php';

	try {
		$guildId = $_GET['guild_id'] ?? null;
		if(!$guildId){
			echo json_encode(['success' => false, 'message' => '缺少guild_id參數']);
			exit();
		}

		$stmt = $pdo->prepare(
			"SELECT e.event_id, e.event_type, e.event_date, e.event_time, e.event_end_time,
			e.meeting_url, e.event_location,
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