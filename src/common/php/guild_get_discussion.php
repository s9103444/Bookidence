<?php
	// 查詢公會討論區分段章節討論串（含巢狀回覆）

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, OPTIONS');
	header('Access-Control-Allow-Headers: Authorization');

	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		exit();
	}

	require 'connect_ckd101g1.php';

	try {
		$segmentId = $_GET['segment_id'] ?? null;

		if (!$segmentId) {
			echo json_encode(['success' => false, 'message' => '缺少 segment_id 參數']);
			exit();
		}

		$contextStmt = $pdo->prepare(
			"SELECT s.segment_id, s.start_chapter, s.end_chapter, s.expected_end_date, s.sort_order,
			        gr.guild_id, gr.book_id,
			        g.guild_name, g.guild_avatar,
			        b.title, b.author, b.publisher, b.bc_image, b.isbn, b.p_date
			FROM segment s
			JOIN guildrecord gr ON gr.record_id = s.record_id
			JOIN guild g ON g.guild_id = gr.guild_id
			JOIN book b ON b.book_id = gr.book_id
			WHERE s.segment_id = :segment_id"
		);
		$contextStmt->execute(['segment_id' => $segmentId]);
		$context = $contextStmt->fetch(PDO::FETCH_ASSOC);

		if (!$context) {
			http_response_code(404);
			echo json_encode(['success' => false, 'message' => '找不到這個段落。']);
			exit();
		}

		// 有帶登入 token 的話解出目前使用者，讓自己被檢舉、審核中的留言仍然看得到
		$currentUserId = null;
		$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
		$token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';
		if ($token !== '') {
			$viewerStmt = $pdo->prepare("SELECT user_id FROM member WHERE session_token = :token");
			$viewerStmt->execute(['token' => $token]);
			$viewer = $viewerStmt->fetch(PDO::FETCH_ASSOC);
			if ($viewer) {
				$currentUserId = (int) $viewer['user_id'];
			}
		}

		$msgStmt = $pdo->prepare(
			"SELECT d.message_id, d.parent_message_id, d.user_id, d.posted_at, d.content,
			        m.nickname, m.member_code, m.bio, gm.permission_level,
			        (SELECT COUNT(*) FROM guilddiscussion_like gl WHERE gl.message_id = d.message_id) AS like_count,
			        EXISTS (
			          SELECT 1 FROM guilddiscussion_like gl2
			          WHERE gl2.message_id = d.message_id AND gl2.user_id = :current_user_id
			        ) AS is_liked_by_me,
			        EXISTS (
			          SELECT 1 FROM report r
			          WHERE r.message_id = d.message_id AND r.status <> '檢舉不成立'
			        ) AS is_under_review
			FROM guilddiscussion d
			JOIN member m ON m.user_id = d.user_id
			LEFT JOIN guildmember gm ON gm.user_id = d.user_id AND gm.guild_id = :guild_id
			WHERE d.segment_id = :segment_id
			ORDER BY d.posted_at ASC, d.message_id ASC"
		);
		$msgStmt->execute([
			'segment_id' => $segmentId,
			'guild_id' => $context['guild_id'],
			'current_user_id' => $currentUserId,
		]);
		$rows = $msgStmt->fetchAll(PDO::FETCH_ASSOC);

		// 審核中(被檢舉且尚未判定不成立)的留言，只有本人看得到，其他人完全看不到
		$messages = [];
		foreach ($rows as $row) {
			$isUnderReview = (bool) $row['is_under_review'];
			$isOwner = $currentUserId !== null && (int) $row['user_id'] === $currentUserId;
			if ($isUnderReview && !$isOwner) {
				continue;
			}
			$row['message_id'] = (int) $row['message_id'];
			$row['parent_message_id'] = $row['parent_message_id'] !== null ? (int) $row['parent_message_id'] : null;
			$row['user_id'] = (int) $row['user_id'];
			$row['like_count'] = (int) $row['like_count'];
			$row['is_liked_by_me'] = (bool) $row['is_liked_by_me'];
			$row['is_under_review'] = $isUnderReview;
			$messages[] = $row;
		}

		echo json_encode(['success' => true, 'context' => $context, 'messages' => $messages], JSON_UNESCAPED_UNICODE);

	} catch (PDOException $e) {
		http_response_code(500);
		echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
	}
?>