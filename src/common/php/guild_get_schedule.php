<?php
	// 查詢公會目前的書籍排程段落（章節分段、預計完讀日期）

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');

	require 'connect_ckd101g1.php';

	try {
		$guildId = $_GET['guild_id'] ?? null;

		if (!$guildId) {
			echo json_encode(['success' => false, 'message' => '缺少 guild_id 參數']);
			exit();
		}

		$recordStmt = $pdo->prepare(
			"SELECT r.record_id, r.book_id, r.record_date, r.end_date,
			b.title, b.author, b.publisher, b.bc_image, b.description, b.isbn, b.p_date
			FROM guild g
			JOIN guildrecord r ON r.guild_id = g.guild_id AND r.book_id = g.book_id
			JOIN book b ON r.book_id = b.book_id
			WHERE g.guild_id = :guild_id
			ORDER BY r.record_id DESC
			LIMIT 1"
		);

		$recordStmt->execute(['guild_id' => $guildId]);
		$record = $recordStmt->fetch(PDO::FETCH_ASSOC);

		if (!$record) {
			// 這個公會還沒有任何讀書紀錄
			echo json_encode(['success' => true, 'record' => null, 'segments' => []], JSON_UNESCAPED_UNICODE);
			exit();
		}

		$segmentStmt = $pdo->prepare(
			"SELECT segment_id, start_chapter, end_chapter, expected_end_date, sort_order
			FROM segment
			WHERE record_id = :record_id
			ORDER BY sort_order"
		);
		$segmentStmt->execute(['record_id' => $record['record_id']]);
		$segments = $segmentStmt->fetchAll(PDO::FETCH_ASSOC);

		$categoryStmt = $pdo->prepare(
			"SELECT bc.bcg_name
			FROM book_categorys bcs
			JOIN book_category bc ON bc.bcg_id = bcs.bcg_id
			WHERE bcs.book_id = :book_id"
		);
		$categoryStmt->execute(['book_id' => $record['book_id']]);
		$categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

		echo json_encode(['success' => true, 'record' => $record, 'segments' => $segments, 'categories' => $categories], JSON_UNESCAPED_UNICODE);

	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
	}
?>