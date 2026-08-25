<?php
	// 公會設定－更新名稱/簡介/公告/頭貼/背景/外觀，只有會長和副會長能操作

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

    if (!$guildId) {
        echo json_encode(['success' => false, 'message' => '缺少 guild_id 參數']);
        exit();
    }

    $fields = [];
    $params = ['guild_id' => $guildId];

    if (isset($_POST['name'])) {
        $fields[] = 'guild_name = :name';
        $params['name'] = $_POST['name'];
    }
    if (isset($_POST['intro'])) {
        $fields[] = 'intro = :intro';
        $params['intro'] = $_POST['intro'];
    }
    if (isset($_POST['announcement'])) {
        $fields[] = 'announcement = :announcement';
        $params['announcement'] = $_POST['announcement'];
    }

    if (!$fields) {
        echo json_encode(['success' => false, 'message' => '沒有要更新的欄位']);
        exit();
    }

    $sql = "UPDATE guild SET " . implode(', ', $fields) . " WHERE guild_id = :guild_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => '更新成功']);

	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
	}
?>