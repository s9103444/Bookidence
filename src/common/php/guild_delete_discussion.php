<?php
	// 在公會討論區刪除自己發過的留言/回覆

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, OPTIONS');
	header('Access-Control-Allow-Headers: Authorization');

	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		exit();
	}

	require 'connect_ckd101g1.php';

	try {


	} catch (PDOException $e) {
		http_response_code(500);
		echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
	}
?>