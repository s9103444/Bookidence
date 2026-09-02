<?php
    // 公會成員/角色檢查共用函式，各支 guild_*.php require 這個檔案後直接呼叫。
    // 用完直接 exit()，呼叫端不用自己判斷回傳值、也不用自己寫 json_encode。

    // 檢查 $token 的人是不是 $guildId 的「在會中」成員，回傳他的 permission_level。
    // 不是成員就直接印出標準化的拒絕 JSON（帶 reason: 'not_member'，讓前端 apiGuard.js 全域攔截器認得出來）並 exit()。
    function requireGuildMember($pdo, $guildId, $token) {
        $stmt = $pdo->prepare(
            "SELECT gm.permission_level
            FROM guildmember gm
            JOIN member m ON gm.user_id = m.user_id
            WHERE gm.guild_id = :guild_id AND gm.member_status = '在會中' AND m.session_token = :token"
        );
        $stmt->execute(['guild_id' => $guildId, 'token' => $token]);
        $permission = $stmt->fetchColumn();

        if (!$permission) {
            http_response_code(403);
            echo json_encode(['success' => false, 'reason' => 'not_member', 'message' => '你不是這個公會的會員，無法操作這個功能。']);
            exit();
        }

        return $permission;
    }

    // 接在 requireGuildMember() 後面呼叫，檢查角色是不是在允許清單裡（例如 ['會長', '副會長']）。
    // 角色不夠就印出拒絕 JSON（不帶 reason，這種情況不算「被踢出」，跟上面那個分開處理）並 exit()。
    function requireGuildRole($permission, $allowedRoles, $message) {
        if (!in_array($permission, $allowedRoles, true)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => $message]);
            exit();
        }
    }
?>