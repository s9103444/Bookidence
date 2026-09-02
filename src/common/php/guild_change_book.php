<?php
	// 更換公會目前讀的書：結束舊的讀書紀錄、開一筆新的 guildrecord(讀書紀錄)，並更新 guild.book_id(書籍id)

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
		$newBookId = $_POST['book_id'] ?? null;

		if (!$guildId || !$newBookId) {
			echo json_encode(['success' => false, 'message' => '缺少 guild_id 或 book_id 參數']);
			exit();
		}

		$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
		$token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';
		if ($token === '') {
			http_response_code(401);
			echo json_encode(['success' => false, 'message' => '未登入。']);
			exit();
		}

		$callerStmt = $pdo->prepare(
			"SELECT gm.permission_level
			FROM guildmember gm
			JOIN member m ON gm.user_id = m.user_id
			WHERE gm.guild_id = :guild_id AND gm.member_status = '在會中' AND m.session_token = :token"
		);
		$callerStmt->execute(['guild_id' => $guildId, 'token' => $token]);
		$callerPermission = $callerStmt->fetchColumn();

		if (!in_array($callerPermission, ['會長', '副會長'], true)) {
			http_response_code(403);
			echo json_encode(['success' => false, 'message' => '只有會長或副會長能操作這個功能。']);
			exit();
		}

		$guildStmt = $pdo->prepare("SELECT book_id FROM guild WHERE guild_id = :guild_id");
		$guildStmt->execute(['guild_id' => $guildId]);
		$guild = $guildStmt->fetch(PDO::FETCH_ASSOC);

		if (!$guild) {
			echo json_encode(['success' => false, 'message' => '找不到這個公會']);
			exit();
		}
		$oldBookId = $guild['book_id'];

		$pdo->beginTransaction();
		$closeStmt = $pdo->prepare(
			"UPDATE guildrecord SET end_date = CURDATE()
			WHERE guild_id = :guild_id AND book_id = :old_book_id
			ORDER BY record_id DESC LIMIT 1"
		);
		$closeStmt->execute(['guild_id' => $guildId, 'old_book_id' => $oldBookId]);

		$insertStmt = $pdo->prepare(
			"INSERT INTO guildrecord (book_id, guild_id, record_date, end_date)VALUES (:book_id, :guild_id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY))"
		);
		$insertStmt->execute(['book_id' => $newBookId, 'guild_id' => $guildId]);

		$updateGuildStmt = $pdo->prepare("UPDATE guild SET book_id = :book_id WHERE guild_id = :guild_id");
		$updateGuildStmt->execute(['book_id' => $newBookId, 'guild_id' => $guildId]);

		$pdo->commit();

echo json_encode(['success' => true, 'message' => '更新目前在讀的書']);


	} catch (PDOException $e) {
		if ($pdo->inTransaction()) {
			$pdo->rollBack();
		}
		echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
	}
?>