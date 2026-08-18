-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- 主機： localhost:8889
-- 產生時間： 2026-08-18 06:37:12
-- 伺服器版本： 5.7.24
-- PHP 版本： 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫: `ckd101_g1`
--

-- --------------------------------------------------------

--
-- 資料表結構 `book`
--

CREATE TABLE `book` (
  `book_id` int(11) NOT NULL COMMENT '書籍ID',
  `book_display_id` char(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '書籍顯示編號',
  `isbn` char(13) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ISBN',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '書名',
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '作者',
  `publisher` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '出版社',
  `bc_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '書籍封面',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT '書籍介紹',
  `p_date` date DEFAULT NULL COMMENT '出版日期',
  `b_status` enum('已上架','已下架') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '已下架' COMMENT '狀態'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='書籍';

-- --------------------------------------------------------

--
-- 資料表結構 `book_application_form`
--

CREATE TABLE `book_application_form` (
  `book_ap_id` int(11) NOT NULL COMMENT '申請編號',
  `isbn` char(13) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '申請書籍ISBN',
  `user_id` int(11) NOT NULL COMMENT '申請人',
  `ap_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '申請書籍名稱',
  `ap_author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '申請書籍作者',
  `book_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '書籍參照連結',
  `application_reason` text COLLATE utf8mb4_unicode_ci COMMENT '申請理由',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '申請時間',
  `ap_status` enum('待處理','已駁回','已核准') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '待處理' COMMENT '處理狀態'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='書籍申請表格';

-- --------------------------------------------------------

--
-- 資料表結構 `book_category`
--

CREATE TABLE `book_category` (
  `bcg_id` int(11) NOT NULL COMMENT '書籍類別ID',
  `bcg_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '書籍類別名稱'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='書籍類別';

-- --------------------------------------------------------

--
-- 資料表結構 `book_collection`
--

CREATE TABLE `book_collection` (
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `book_id` int(11) NOT NULL COMMENT '書籍ID',
  `r_status` enum('未閱讀','閱讀中','閱讀完畢') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '未閱讀' COMMENT '閱讀狀態',
  `added_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='我的藏書';

-- --------------------------------------------------------

--
-- 資料表結構 `book_thoughts`
--

CREATE TABLE `book_thoughts` (
  `b_thought_id` int(11) NOT NULL COMMENT '書籍心得ID',
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `book_id` int(11) NOT NULL COMMENT '書籍ID',
  `bth_content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '心得內容',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '更新時間',
  `bth_status` enum('公開','非公開','儲存草稿') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '儲存草稿' COMMENT '心得狀態'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='書籍心得';

-- --------------------------------------------------------

--
-- 資料表結構 `bulletin`
--

CREATE TABLE `bulletin` (
  `bulletin_id` int(11) NOT NULL COMMENT '公告編號',
  `guild_id` int(11) NOT NULL COMMENT '公會ID',
  `user_id` int(11) NOT NULL COMMENT '發起人(會長/副會長)',
  `posted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '發布時間',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公告內容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='公告欄';

-- --------------------------------------------------------

--
-- 資料表結構 `event`
--

CREATE TABLE `event` (
  `event_id` int(11) NOT NULL COMMENT '活動ID',
  `guild_id` int(11) NOT NULL COMMENT '公會ID',
  `book_id` int(11) NOT NULL COMMENT '書籍ID',
  `organizer_user_id` int(11) NOT NULL COMMENT '發起人會員ID',
  `leader_user_id` int(11) NOT NULL COMMENT '領讀人',
  `event_type` enum('線上(Online)','線下(Offline)') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '活動形式',
  `event_date` date NOT NULL COMMENT '活動日期',
  `event_time` time NOT NULL COMMENT '活動開始時間',
  `event_end_time` time NOT NULL COMMENT '活動結束時間',
  `meeting_url` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '活動連結',
  `event_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '活動地點',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '活動說明',
  `max_participants` int(11) NOT NULL COMMENT '人數限制',
  `deadline` date NOT NULL COMMENT '報名截止時間',
  `event_status` enum('正常','已取消') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '活動狀態'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='活動';

-- --------------------------------------------------------

--
-- 資料表結構 `event_registration`
--

CREATE TABLE `event_registration` (
  `event_id` int(11) NOT NULL COMMENT '活動ID',
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `submitted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '報名送出時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='報名紀錄';

-- --------------------------------------------------------

--
-- 資料表結構 `guild`
--

CREATE TABLE `guild` (
  `guild_id` int(11) NOT NULL COMMENT '公會id',
  `guild_code` char(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公會代碼(顯示用)',
  `book_id` int(11) NOT NULL COMMENT '公會目前正在讀的書',
  `guild_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公會名稱',
  `founded_at` date NOT NULL COMMENT '創辦日期',
  `guild_avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公會頭像',
  `intro` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '簡介',
  `approval_required` tinyint(4) NOT NULL COMMENT '審核設定',
  `member_count` int(11) NOT NULL COMMENT '人數',
  `guild_status` enum('正常','已解散','停權') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公會狀態',
  `guild_skin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公會外觀'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='讀書公會';

-- --------------------------------------------------------

--
-- 資料表結構 `guilddiscussion`
--

CREATE TABLE `guilddiscussion` (
  `message_id` bigint(20) NOT NULL COMMENT '訊息編號',
  `segment_id` int(11) NOT NULL COMMENT '段落ID',
  `user_id` int(11) NOT NULL COMMENT '發言人',
  `posted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '發言時間',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '發言內容',
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '照片\r\n單張存URL，多張改JSON存路徑陣列'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='讀書公會討論區';

-- --------------------------------------------------------

--
-- 資料表結構 `guildmember`
--

CREATE TABLE `guildmember` (
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `guild_id` int(11) NOT NULL COMMENT '公會id',
  `permission_level` enum('一般','副會長','會長') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '一般' COMMENT '權限等級',
  `member_status` enum('申請中','在會中','已踢出','自行退出') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '申請中' COMMENT '成員狀態'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='公會成員';

-- --------------------------------------------------------

--
-- 資料表結構 `member`
--

CREATE TABLE `member` (
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `member_code` char(11) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '會員編號',
  `nickname` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '暱稱',
  `report_count` int(11) DEFAULT '0' COMMENT '檢舉次數',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '信箱',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密碼',
  `session_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '登入 session token',
  `bio` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '自我介紹',
  `account_status` enum('正常','停權') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '正常' COMMENT '帳號狀態',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
  `achieve_id` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '成就類別ID',
  `total_exp` int(11) DEFAULT '0' COMMENT '累積經驗值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='會員';

-- --------------------------------------------------------

--
-- 資料表結構 `member_book_categorys`
--

CREATE TABLE `member_book_categorys` (
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `bcg_id` int(11) NOT NULL COMMENT '書籍類別ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='會員喜好書籍類別';

-- --------------------------------------------------------

--
-- 資料表結構 `segment`
--

CREATE TABLE `segment` (
  `segment_id` int(11) NOT NULL COMMENT '段落ID',
  `record_id` int(11) NOT NULL COMMENT '讀書紀錄ID',
  `start_chapter` smallint(6) NOT NULL COMMENT '起始章節',
  `end_chapter` smallint(6) NOT NULL COMMENT '結束章節',
  `expected_end_date` date NOT NULL COMMENT '預計讀完日期',
  `sort_order` tinyint(4) NOT NULL COMMENT '順序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='讀書排程段落';

-- --------------------------------------------------------

--
-- 資料表結構 `test`
--

CREATE TABLE `test` (
  `test` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `uk_book_display_id` (`book_display_id`),
  ADD UNIQUE KEY `uk_isbn` (`isbn`);

--
-- 資料表索引 `book_application_form`
--
ALTER TABLE `book_application_form`
  ADD PRIMARY KEY (`book_ap_id`),
  ADD UNIQUE KEY `uk_isbn` (`isbn`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `book_category`
--
ALTER TABLE `book_category`
  ADD PRIMARY KEY (`bcg_id`);

--
-- 資料表索引 `book_collection`
--
ALTER TABLE `book_collection`
  ADD PRIMARY KEY (`user_id`,`book_id`),
  ADD KEY `book_id` (`book_id`);

--
-- 資料表索引 `book_thoughts`
--
ALTER TABLE `book_thoughts`
  ADD PRIMARY KEY (`b_thought_id`),
  ADD UNIQUE KEY `uk_user_book` (`user_id`,`book_id`),
  ADD KEY `book_id` (`book_id`);

--
-- 資料表索引 `bulletin`
--
ALTER TABLE `bulletin`
  ADD PRIMARY KEY (`bulletin_id`);

--
-- 資料表索引 `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`);

--
-- 資料表索引 `event_registration`
--
ALTER TABLE `event_registration`
  ADD PRIMARY KEY (`event_id`);

--
-- 資料表索引 `guild`
--
ALTER TABLE `guild`
  ADD PRIMARY KEY (`guild_id`),
  ADD UNIQUE KEY `guild_code` (`guild_code`);

--
-- 資料表索引 `guilddiscussion`
--
ALTER TABLE `guilddiscussion`
  ADD PRIMARY KEY (`message_id`);

--
-- 資料表索引 `guildmember`
--
ALTER TABLE `guildmember`
  ADD PRIMARY KEY (`user_id`,`guild_id`),
  ADD KEY `guild_id` (`guild_id`);

--
-- 資料表索引 `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uk_member_code` (`member_code`),
  ADD UNIQUE KEY `uk_session_token` (`session_token`);

--
-- 資料表索引 `member_book_categorys`
--
ALTER TABLE `member_book_categorys`
  ADD PRIMARY KEY (`user_id`,`bcg_id`),
  ADD KEY `bcg_id` (`bcg_id`);

--
-- 資料表索引 `segment`
--
ALTER TABLE `segment`
  ADD PRIMARY KEY (`segment_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `book`
--
ALTER TABLE `book`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '書籍ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `book_application_form`
--
ALTER TABLE `book_application_form`
  MODIFY `book_ap_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '申請編號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `book_category`
--
ALTER TABLE `book_category`
  MODIFY `bcg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '書籍類別ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `book_thoughts`
--
ALTER TABLE `book_thoughts`
  MODIFY `b_thought_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '書籍心得ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `bulletin`
--
ALTER TABLE `bulletin`
  MODIFY `bulletin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公告編號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '活動ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `event_registration`
--
ALTER TABLE `event_registration`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '活動ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `guild`
--
ALTER TABLE `guild`
  MODIFY `guild_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公會id';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `guilddiscussion`
--
ALTER TABLE `guilddiscussion`
  MODIFY `message_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '訊息編號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `member`
--
ALTER TABLE `member`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '使用者ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `segment`
--
ALTER TABLE `segment`
  MODIFY `segment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '段落ID';

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `book_collection`
--
ALTER TABLE `book_collection`
  ADD CONSTRAINT `book_collection_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`),
  ADD CONSTRAINT `book_collection_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
