<?php
	// 儲存讀書排程段落：新增/修改/刪除段落，前端整批一次送出

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, OPTIONS');
	header('Access-Control-Allow-Headers: Authorization');

	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		exit();
	}

	require 'connect_ckd101g1.php';

	try {
		$guildId = $_POST['guild_id'] ?? null;
		$segmentsJson = $_POST['segments'] ?? null;

		if (!$guildId || !$segmentsJson) {
			echo json_encode(['success' => false, 'message' => '缺少 guild_id 或 segments 參數']);
			exit();
		}

		$segments = json_decode($segmentsJson, true);

		$recordStmt = $pdo->prepare(
			"SELECT r.record_id
			FROM guild g
			JOIN guildrecord r ON r.guild_id = g.guild_id AND r.book_id = g.book_id
			WHERE g.guild_id = :guild_id
			ORDER BY r.record_id DESC
			LIMIT 1"
		);
		$recordStmt->execute(['guild_id' => $guildId]);
		$record = $recordStmt->fetch(PDO::FETCH_ASSOC);

		if (!$record) {
			echo json_encode(['success' => false, 'message' => '找不到目前的讀書紀錄']);
			exit();
		}

		$recordId = $record['record_id'];

		$pdo->beginTransaction();

		$deleteStmt = $pdo->prepare("DELETE FROM segment WHERE record_id = :record_id");
		$deleteStmt->execute(['record_id' => $recordId]);

		$insertStmt = $pdo->prepare(
			"INSERT INTO segment (record_id, start_chapter, end_chapter, expected_end_date, sort_order)
			VALUES (:record_id, :start_chapter, :end_chapter, :expected_end_date, :sort_order)"
		);

		foreach ($segments as $index => $segment) {
			$insertStmt->execute([
				'record_id' => $recordId,
				'start_chapter' => $segment['startChapter'],
				'end_chapter' => $segment['endChapter'],
				'expected_end_date' => $segment['dueDate'],
				'sort_order' => $index + 1,
			]);
		}

		$pdo->commit();

		echo json_encode(['success' => true, 'message' => '排程已儲存']);


	} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
	}
?>