<?php
// 後台 API 的開場白。每一支後台 API 的第一行都寫這個，就有了：
//   require __DIR__ . '/admin_bootstrap.php';
//
// 它一次幫你做四件事：
//   1. 宣告這支 API 回傳的是 JSON，而且允許前端跨網域打過來
//   2. 瀏覽器送來的試探性請求（OPTIONS）直接放行，不往下跑
//   3. 連上資料庫，之後就有 $pdo 可以用
//   4. 檢查管理員有沒有登入，沒登入直接回 401 並中斷；
//      有登入的話 $staff 裡就有他的 staff_account 和 staff_name
//
// 所以 require 完這一行之後，$pdo 和 $staff 都可以直接用。
//
// 陷阱：上面那四件事的順序不能動。連資料庫要在檢查登入之前（檢查會用到 $pdo），
// OPTIONS 的放行要在檢查登入之前（不然瀏覽器的試探請求會被擋成 401，
// 整支 API 從網頁完全打不通，而且 log 裡什麼都看不到）。

  header('Content-Type: application/json; charset=utf-8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
  header('Access-Control-Allow-Headers: Authorization, Content-Type');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
  }

  require __DIR__ . '/connect_ckd101g1.php';
  require __DIR__ . '/admin_auth.php';


  // 前端可能送資料庫的 user_id（5），也可能送畫面上的會員編號（MKD00000005）——
  // 會員管理的網址用 member_code，檢舉那邊用 user_id，兩種都要認得。
  // 認不出來就回 0，呼叫端拿它去查一定撈不到，那正是對的答案（沒有這個人）
  function resolveUserId(PDO $pdo, $raw): int {
    $raw = trim((string)$raw);

    if ($raw === '') return 0;
    if (ctype_digit($raw)) return (int)$raw;

    $stmt = $pdo->prepare("SELECT user_id FROM member WHERE member_code = ?");
    $stmt->execute([$raw]);

    return (int)($stmt->fetchColumn() ?: 0);
  }
?>
