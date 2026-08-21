<?php
	// 查詢公會討論區分段章節討論串（含巢狀回覆）

	header('Content-Type: application/json; charset=utf8');
	header('Access-Control-Allow-Origin: *');

	require 'connect_ckd101g1.php';

	try {


	} catch (PDOException $e) {
		http_response_code(500);
		echo json_encode(['success' => false, 'message' => '查詢失敗：' . $e->getMessage()]);
	}
?>