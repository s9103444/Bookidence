<?php
    require __DIR__ . '/admin_bootstrap.php';


    
    //撈全部會員的基本資料
    $stmt=$pdo->prepare("
    SELECT user_id, member_code,created_at,account_status,nickname,email
    FROM member
    ");
    $stmt->execute();
    $members=$stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt=$pdo->prepare("
    SELECT action_id,target_user_id,staff_account,report_id,
    action_type,reason,created_at,revoked_at,revoked_by
    FROM moderation_action
   

    ");
    $stmt->execute( );
    $actions=$stmt->fetchAll(PDO::FETCH_ASSOC);


    $stmt=$pdo->prepare("
    SELECT guildmember.user_id,guildmember.guild_id,guildmember.permission_level,guildmember.member_status,guild.guild_name
    FROM guildmember
    JOIN guild ON guild.guild_id=guildmember.guild_id
    WHERE member_status='在會中'
    ");
    $stmt->execute( );
    $inGuilds=$stmt->fetchAll(PDO::FETCH_ASSOC);




    echo json_encode(['success'=>true,'member'=>$members,'inGuild'=>$inGuilds,'action'=>$actions],JSON_UNESCAPED_UNICODE);

    







?>