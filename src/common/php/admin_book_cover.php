<?php
  require __DIR__ . '/admin_bootstrap.php';

  $file = $_FILES['cover'] ?? null;

  if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請選擇一張封面圖片。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  if ($file['size']>5*1024*1024) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '封面圖片不可超過 5MB。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

  if (!in_array($ext, $allowedExt, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '封面只接受 jpg / png / webp。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  // 檔名自己產生，不沿用使用者傳來的名字
  $filename = uniqid() . '.' . $ext;
  $relativePath = 'book-covers/' . $filename;
  $target = __DIR__ . '/uploads/' . $relativePath;

  if (!move_uploaded_file($file['tmp_name'], $target)) {
    error_log('[admin_book_cover] 搬移失敗：' . $target);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '封面上傳失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  echo json_encode(['success' => true, 'bc_image' => $relativePath], JSON_UNESCAPED_UNICODE);
?>
