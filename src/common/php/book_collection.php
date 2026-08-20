<?php
  // 1. header 設定：Content-Type、CORS、要記得加 Authorization 到 Allow-Headers、Allow-Methods 改成 POST（可以參考 me.php）
  // 2. 處理 OPTIONS 預檢請求（跟 me.php 一樣直接 exit）

  header('Content-Type: application/json; charset=utf8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Authorization');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
  }

  require 'connect_ckd101g1.php';
  

  // 3. 從 Authorization header 拆出 token（可以整段參考 me.php 怎麼寫）



  // 4. token 為空就回 401

  // 5. 接收前端送來的 book_id（這次資料是 POST body，要想想用 $_POST
還是要收 JSON body 用 nts('php://input'))）

  try {
    // 6. 先用 token 查出 user_id（跟 me.php 的 SQL 幾乎一樣）
    // 7. 查不到就回 4

    // 8. 用查到的 use INSERT INTObook_collection

    // 9. 回傳成功訊息 JSON
  } catch (PDOExceptio
    // 10. 錯誤處理
  }
?>