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
	require 'guild_auth.php';

	try {
		$guildId = $_POST['guild_id'] ?? null;
		$segmentsJson = $_POST['segments'] ?? null;

		if (!$guildId || !$segmentsJson) {
			echo json_encode(['success' => false, 'message' => '缺少 guild_id 或 segments 參數']);
			exit();
		}

		$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
		$token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';
		if ($token === '') {
			http_response_code(401);
			echo json_encode(['success' => false, 'message' => '未登入。']);
			exit();
		}

		$callerPermission = requireGuildMember($pdo, $guildId, $token);
            requireGuildRole($callerPermission, ['會長', '副會長'], '只有會長或副會長能操作這個功能。');

		$segments = json_decode($segmentsJson, true);

		$previous = null;
		foreach ($segments as $segment) {
			$start = $segment['startChapter'] ?? '';
			$end = $segment['endChapter'] ?? '';
			$dueDate = $segment['dueDate'] ?? '';

			if ($start === '' || $end === '') {
				echo json_encode(['success' => false, 'message' => '請輸入章節範圍']);
				exit();
			}
			if ((int)$start < 1 || (int)$end < 1) {
				echo json_encode(['success' => false, 'message' => '章節不能小於 1']);
				exit();
			}
			if ((int)$end < (int)$start) {
				echo json_encode(['success' => false, 'message' => '結束章節不能小於開始章節']);
				exit();
			}
			if (!$dueDate) {
				echo json_encode(['success' => false, 'message' => '請選擇預計完讀日期']);
				exit();
			}
			if ($previous !== null) {
				if ((int)$start <= (int)$previous['end']) {
					echo json_encode(['success' => false, 'message' => '章節範圍不能與前一個討論板重複']);
					exit();
				}
				if ($dueDate < $previous['dueDate']) {
					echo json_encode(['success' => false, 'message' => '完讀日期不能早於前一個討論板']);
					exit();
				}
			}
			$previous = ['end' => $end, 'dueDate' => $dueDate];
		}

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

		$existingStmt = $pdo->prepare("SELECT segment_id FROM segment WHERE record_id = :record_id");
		$existingStmt->execute(['record_id' => $recordId]);
		$existingIds = $existingStmt->fetchAll(PDO::FETCH_COLUMN);

		$pdo->beginTransaction();

		$submittedIds = [];
		foreach ($segments as $segment) {
			if (isset($segment['id']) && in_array($segment['id'], $existingIds)) {
				$submittedIds[] = $segment['id'];
			}
		}

		$idsToDelete = array_diff($existingIds, $submittedIds);
		$deleteStmt = $pdo->prepare("DELETE FROM segment WHERE segment_id = :segment_id");
		foreach ($idsToDelete as $segmentId) {
			try {
				$deleteStmt->execute(['segment_id' => $segmentId]);
			} catch (PDOException $e) {
				$pdo->rollBack();
				echo json_encode(['success' => false, 'message' => '有討論板底下已經有留言，無法刪除']);
				exit();
			}
		}

		$updateStmt = $pdo->prepare(
			"UPDATE segment SET start_chapter = :start_chapter, end_chapter = :end_chapter, expected_end_date = :expected_end_date, sort_order = :sort_order
			WHERE segment_id = :segment_id"
		);
		$insertStmt = $pdo->prepare(
			"INSERT INTO segment (record_id, start_chapter, end_chapter, expected_end_date, sort_order)
			VALUES (:record_id, :start_chapter, :end_chapter, :expected_end_date, :sort_order)"
		);

		foreach ($segments as $index => $segment) {
			$isExisting = isset($segment['id']) && in_array($segment['id'], $existingIds);
			if ($isExisting) {
				$updateStmt->execute([
					'start_chapter' => $segment['startChapter'],
					'end_chapter' => $segment['endChapter'],
					'expected_end_date' => $segment['dueDate'],
					'sort_order' => $index + 1,
					'segment_id' => $segment['id'],
				]);
			} else {
				$insertStmt->execute([
					'record_id' => $recordId,
					'start_chapter' => $segment['startChapter'],
					'end_chapter' => $segment['endChapter'],
					'expected_end_date' => $segment['dueDate'],
					'sort_order' => $index + 1,
				]);
			}
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