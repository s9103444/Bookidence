<?php
  require __DIR__ . '/admin_bootstrap.php';

  $raw = file_get_contents('php://input');
  error_log('[report_resolve] 原始 body：' . $raw);
  $body = json_decode($raw, true);

  $reportId    = (int)($body['report_id'] ?? 0);
  $status      = $body['status'] ?? '';
  $actionTaken = $body['action_taken'] ?? '';
  $notes       = trim($body['resolution_notes'] ?? '');

  $UPHELD    = '檢舉成立';
  $DISMISSED = '檢舉不成立';

  if ($reportId === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '系統忙碌中，請稍後再試。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  if ($status !== $UPHELD && $status !== $DISMISSED) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '請選擇檢舉成立或不成立。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  // 成立會在對方身上留紀錄、還會通知他，所以處分和說明都要填；
  // 不成立什麼都不會發生在他身上，說明就不強制
  if ($status === $UPHELD) {
    $validActions = ['刪除內容', '警告用戶', '停權用戶'];

    if (!in_array($actionTaken, $validActions, true)) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => '請選擇要採取的處分。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    if ($notes === '') {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => '請填寫處理說明。'], JSON_UNESCAPED_UNICODE);
      exit();
    }
  } else {
    $actionTaken = '駁回';
  }

  if (mb_strlen($notes) > 500) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '處理說明不可超過 500 字。'], JSON_UNESCAPED_UNICODE);
    exit();
  }

  try {
    $check = $pdo->prepare(
      "SELECT report_id, target_type, b_thought_id, message_id, reported_user_id, status, reason,
              CONCAT(DATE_FORMAT(created_at, '%y%m%d'), '-', report_id) AS report_no
         FROM report WHERE report_id = ?"
    );
    $check->execute([$reportId]);
    $report = $check->fetch(PDO::FETCH_ASSOC);

    if (!$report) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => '找不到這筆檢舉。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    if ($report['status'] !== '尚未處理') {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => '這筆檢舉已經處理過了。'], JSON_UNESCAPED_UNICODE);
      exit();
    }

    // 被檢舉的那則內容：心得存 b_thought_id，留言存 message_id，只有一邊有值
    $targetId = $report['b_thought_id'] ?? $report['message_id'];
    $reportNo = $report['report_no'];

    // 心得的內容編號存在 b_thought_id，留言存在 message_id，先決定等一下要比對哪一欄
    $column = $report['target_type'] === '心得' ? 'b_thought_id' : 'message_id';


    $pdo->beginTransaction();


    // ---------- ⓪ 先記下有誰檢舉了這則內容 ----------
    //
    // 一定要在 UPDATE 之前撈，之後這些單子的 status 就不是「尚未處理」了。
    // 帶各自的 report_no，通知裡要給他「他自己那張單子」的編號
    $listStmt = $pdo->prepare(
      "SELECT reporter_id,
              CONCAT(DATE_FORMAT(created_at, '%y%m%d'), '-', report_id) AS report_no
         FROM report
        WHERE $column = ? AND status = '尚未處理'"
    );
    $listStmt->execute([$targetId]);
    $reporters = $listStmt->fetchAll(PDO::FETCH_ASSOC);


    // ---------- ① 標記檢舉結果 ----------
    //
    // ⬅ 要改的是 WHERE 那一行（現在只改「自己這一筆」），
    //    以及 execute() 裡最後那個 $reportId。
    //    整句 SQL 裡有幾個 ?，execute 的陣列就要有幾個值，順序從左到右。
    $mark = $pdo->prepare(
      "UPDATE report
          SET status = ?, action_taken = ?, resolution_notes = ?,
              staff_account = ?, resolved_at = NOW()
        WHERE $column = ? AND status='尚未處理' "
    );

    $mark->execute([
      $status,                          // SET 第 1 個 ?
      $actionTaken,                     // SET 第 2 個 ?
      $notes !== '' ? $notes : null,    // SET 第 3 個 ?
      $staff['staff_account'],          // SET 第 4 個 ?
      $targetId,
    ]);

    $affected = $mark->rowCount();


    // ---------- ② 處分紀錄 ----------
    //
    // 只有「成立」而且管理員選了要罰帳號時才寫。
    // 選「不處分帳號」= 只下架內容，不算在這個人的違規次數上
    $punished = false;

    if ($status === $UPHELD && ($actionTaken === '警告用戶' || $actionTaken === '停權用戶')) {

      // 兩張表用的字不一樣：report 存「警告用戶」，moderation_action 的 enum 是「警告」
      $actionType = $actionTaken === '警告用戶' ? '警告' : '停權';

      $punish = $pdo->prepare(
        "INSERT INTO moderation_action
                (target_user_id, staff_account, report_id, action_type, reason, created_at)
         VALUES (?, ?, ?, ?, ?, NOW())"
      );

      $punish->execute([
        $report['reported_user_id'],
        $staff['staff_account'],
        $reportId,
        $actionType,
        $notes,
      ]);

      $punished = true;


      // ---------- ③ 停權要改會員本人的狀態 ----------
      //
      // ⬅ 這句還少一個欄位。現在只把帳號標成停權，
      //    但其他 API 認人只看 session_token、不看 account_status，
      //    所以「已經登入的人」還能繼續用，直到他自己登出。
      //    要讓他立刻被踢出去，這句 SET 還要多改一個欄位。
      if ($actionTaken === '停權用戶') {
        $suspend = $pdo->prepare(
          "UPDATE member SET account_status = '停權' ,session_token=NULL WHERE user_id = ?"
        );
        $suspend->execute([$report['reported_user_id']]);
      }
    }


    // ---------- ④ 通知檢舉人 ----------
    //
    // 成立、不成立都要通知 —— 他發起了一個動作，沒有下文的話
    // 他不知道到底有沒有人看。一則內容有幾個檢舉人就發幾封
    $notify = $pdo->prepare(
      "INSERT INTO notification (user_id, type, notifi_title, content)
       VALUES (?, 'SYSTEM_MESSAGE', ?, ?)"
    );

    $targetWord = $report['target_type'] === '心得' ? '心得' : '留言';

    // 心得在作者的書房還看得到，說「移除」他會覺得跟畫面對不上，所以用「下架」；
    // 留言沒有上架的概念，對作者來說就是不見了
    $removeWord = $report['target_type'] === '心得' ? '已下架' : '已移除';

    foreach ($reporters as $r) {
      if ($status === $UPHELD) {
        $text = "你檢舉的{$targetWord}已審核完成，該內容{$removeWord}。案件編號 #{$r['report_no']}";
      } else {
        $text = "你檢舉的{$targetWord}已審核完成，未違反社群規範，將繼續顯示。案件編號 #{$r['report_no']}";
      }

      $title = $status === $UPHELD ? '你的檢舉已處理完成' : '你的檢舉已審核完成';

      $notify->execute([$r['reporter_id'], $title, $text]);
    }


    // ---------- ⑤ 通知被檢舉人 ----------
    //
    // 只有成立才通知。不成立的話他根本不該知道自己被檢舉過 ——
    // 告訴他只會讓他去猜是誰檢舉的
    if ($status === $UPHELD) {
      if ($report['target_type'] === '心得') {
        $place = $pdo->prepare(
          "SELECT CONCAT('《', b.title, '》的心得') AS place
             FROM book_thoughts t JOIN book b ON t.book_id = b.book_id
            WHERE t.b_thought_id = ?"
        );
      } else {
        $place = $pdo->prepare(
          "SELECT CONCAT('公會「', g.guild_name, '」的留言') AS place
             FROM guilddiscussion d
             JOIN segment sg     ON d.segment_id = sg.segment_id
             JOIN guildrecord gr ON sg.record_id = gr.record_id
             JOIN guild g        ON gr.guild_id  = g.guild_id
            WHERE d.message_id = ?"
        );
      }

      $place->execute([$targetId]);
      $where = $place->fetchColumn() ?: "一則{$targetWord}";

      // 理由寫「違反了什麼」，不寫「有人檢舉你」—— 檢舉只是發現管道，
      // 而且提了會讓他去猜是誰檢舉的，那正是不成立時不通知他的原因
      $tail = '';

      if ($actionTaken === '警告用戶') {
        $tail = '，並記錄一次警告。再次違規可能導致帳號停權';
      } elseif ($actionTaken === '停權用戶') {
        $tail = '，你的帳號已停權';
      }

      $text = "你在{$where}，因{$report['reason']}違反社群規範，{$removeWord}{$tail}。案件編號 #{$reportNo}";

      $title = $actionTaken === '停權用戶'
        ? '你的帳號已停權'
        : "你的{$targetWord}{$removeWord}";

      $notify->execute([$report['reported_user_id'], $title, $text]);
    }


    $pdo->commit();

    error_log("[report_resolve] 標記了 $affected 筆檢舉");

    echo json_encode([
      'success'    => true,
      'affected'   => $affected,
      'punished'   => $punished,
      'notified'   => count($reporters) + ($status === $UPHELD ? 1 : 0),
      'staff_name' => $staff['staff_name'],
    ], JSON_UNESCAPED_UNICODE);

  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    error_log('[admin_report_resolve] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '處理失敗，請稍後再試。'], JSON_UNESCAPED_UNICODE);
  }
?>
