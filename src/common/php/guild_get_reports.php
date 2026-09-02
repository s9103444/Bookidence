<?php
     // 查詢公會的檢舉事件（僅留言檢舉），guild_id=列表 / report_id=詳情

    header('Content-Type: application/json; charset=utf8');
    header('Access-Control-Allow-Origin: *');

    require 'connect_ckd101g1.php';

    try{
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        $token = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';
        if ($token === '') {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => '未登入。']);
            exit();
        }

        $reportId = $_GET['report_id'] ?? null;

        if($reportId){
            $stmt = $pdo->prepare(
                "SELECT r.report_id, gr.guild_id,
                CONCAT(DATE_FORMAT(r.created_at, '%y%m%d'), '-', r.report_id) AS report_no,
                r.reason, r.reason_detail, r.created_at, r.status, reporter.nickname AS reporter_name, reporter.member_code AS reporter_code, reported.nickname AS reported_name, reported.member_code AS reported_code, reported.user_id AS reported_user_id, gd.content AS quote_content
                FROM report r
                JOIN guilddiscussion gd ON gd.message_id = r.message_id
                JOIN segment s ON s.segment_id = gd.segment_id
                JOIN guildrecord gr ON gr.record_id = s.record_id
                JOIN member reporter ON reporter.user_id = r.reporter_id
                JOIN member reported ON reported.user_id = r.reported_user_id
                WHERE r.report_id = :report_id AND r.target_type = '留言'"
            );
            $stmt->execute(['report_id' => $reportId]);
            $report = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$report){
                echo json_encode(['success' => false, 'message' => '找不到這筆檢舉紀錄']);
                exit();
            }

            $viewerStmt = $pdo->prepare(
                "SELECT gm.permission_level
                FROM guildmember gm
                JOIN member m ON gm.user_id = m.user_id
                WHERE gm.guild_id = :guild_id AND gm.member_status = '在會中' AND m.session_token = :token"
            );
            $viewerStmt->execute(['guild_id' => $report['guild_id'], 'token' => $token]);
            if (!$viewerStmt->fetchColumn()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => '你不是這個公會的會員，無法查看檢舉紀錄。']);
                exit();
            }

            unset($report['guild_id']);
            echo json_encode(['success' => true, 'report' => $report], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $guildId = $_GET['guild_id'] ?? null;

        if(!$guildId){
            echo json_encode(['success' => false, 'message' => '缺少 guild_id 參數']);
            exit();
        }

        $viewerStmt = $pdo->prepare(
            "SELECT gm.permission_level
            FROM guildmember gm
            JOIN member m ON gm.user_id = m.user_id
            WHERE gm.guild_id = :guild_id AND gm.member_status = '在會中' AND m.session_token = :token"
        );
        $viewerStmt->execute(['guild_id' => $guildId, 'token' => $token]);
        if (!$viewerStmt->fetchColumn()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => '你不是這個公會的會員，無法查看檢舉紀錄。']);
            exit();
        }

        $listStmt = $pdo->prepare(
            "SELECT r.report_id, r.reason, r.created_at, r.status, reporter.nickname AS reporter_name, reported.nickname AS reported_name
            FROM report r
            JOIN guilddiscussion gd ON gd.message_id = r.message_id
            JOIN segment s ON s.segment_id = gd.segment_id
            JOIN guildrecord gr ON gr.record_id = s.record_id
            JOIN member reporter ON reporter.user_id = r.reporter_id
            JOIN member reported ON reported.user_id = r.reported_user_id
            WHERE gr.guild_id = :guild_id AND r.target_type = '留言'
            ORDER BY r.created_at DESC"
        );
        $listStmt->execute(['guild_id' => $guildId]);
        $reports = $listStmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'reports' => $reports],JSON_UNESCAPED_UNICODE);
    }catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
}
?>