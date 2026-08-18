-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- 主機： localhost:8889
-- 產生時間： 2026-08-18 11:54:39
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

--
-- 傾印資料表的資料 `book`
--

INSERT INTO `book` (`book_id`, `book_display_id`, `isbn`, `title`, `author`, `publisher`, `bc_image`, `description`, `p_date`, `b_status`) VALUES
(1, 'BK00000001', '9789861755267', '原子習慣', '詹姆斯‧克利爾（James Clear）', '方智出版社', NULL, '本書說明微小改變如何帶來巨大躍進。作者提出建立好習慣與戒除壞習慣的四階法則——讓提示顯而易見、讓渴望有吸引力、讓行動輕而易舉、讓獎賞令人滿足，幫助讀者透過系統化方式調整行為，達成持續性的自我成長。', '2019-05-01', '已上架'),
(2, 'BK00000002', '9789861753805', '被討厭的勇氣', '岸見一郎、古賀史健', '究竟出版社', NULL, '本書以年輕人與哲學家的對話形式，深入淺出地介紹阿德勒心理學。核心觀點強調「人的煩惱皆來自於人際關係」，並提出「課題分離」概念，鼓勵讀者擺脫他人期待的枷鎖，勇敢面對當下，獲得真正的自由與幸福。', '2014-10-30', '已下架'),
(3, 'BK00000003', '9789578423220', '致富心態', '摩根‧豪瑟（Morgan Housel）', '商周出版社', NULL, '作者透過19個簡短故事，剖析人們處理金錢時的心理與行為模式。本書強調理財成功與否往往不取決於個人智商或專業知識，而在於如何控制情緒與面對風險，協助讀者建立健康、可持續的財務觀念。', '2021-01-27', '已下架'),
(4, 'BK00000004', '9789863842347', '富爸爸，窮爸爸', '羅伯特·徹·清崎（Robert T. Kiyosaki）', '高寶出版社', NULL, '作者透過對比親生父親與朋友父親不同的金錢觀，闡述資產與負債的本質區別。書中提出「讓錢為你工作」而非「為錢工作」的核心概念，奠定了現代個人理財與財務自由思維的基础。', '2017-08-16', '已上架'),
(5, 'BK00000005', '9789573274711', '人類大歷史', '尤瓦爾‧諾瓦‧赫拉利（Yuval Noah Harari）', '遠流出版社', NULL, '本書跨越十萬年歷史，將人類演化梳理為認知革命、農業革命與科學革命三大里程碑。作者提出人類憑藉「想像的秩序」與虛構故事實現大規模合作，從而登上地球生物鏈頂端，提供全新的歷史視角。', '2014-09-01', '已下架'),
(6, 'BK00000006', '9789865511876', '失落的城池：文明的盛衰與人類的未來', '安娜里‧紐維茲（Annalalee Newitz）', '廣場出版', NULL, '本書深入探訪龐貝、恰塔霍裕克、吳哥與卡霍基亞四大古城遺跡。結合最新考古發現與歷史文獻，作者擺脫傳統「文明毀滅」的陳腔濫調，從基層人民與都市規劃的角度，重新思考古代城市興衰對現代都市發展的啟示。', '2021-11-03', '已下架'),
(7, 'BK00000007', '9789862164013', '自私的基因', '理查‧道金斯（Richard Dawkins）', '天下文化', NULL, '演化生物學經典巨著，提出以「基因」為核心的演化觀點。作者主張生物個體僅是基因延續下去的載體，並透過演化賽局理論解釋自然界中的利他行為與競爭現象，深刻影響了當代生物學與社會學。', '2009-09-25', '已下架'),
(8, 'BK00000008', '9789862415795', '思考，快與慢', '丹尼爾‧康納曼（Daniel Kahneman）', '天下文化', NULL, '諾貝爾經濟學獎得主康納曼剖析大腦決策機制的集大成之作。書中將思維劃分為直覺敏捷的「系統一」與理性客觀的「系統二」，揭示人類在面對不確定性時常見的認知偏誤與心理盲點。', '2012-10-31', '已下架'),
(9, 'BK00000009', '9789863982180', '當呼吸化為空氣', '保羅‧柯拉尼蒂（Paul Kalanithi）', '時報出版', NULL, '一位年輕的神經外科醫師在即將完成訓練之際罹患肺癌末期，轉身成為面對死亡的病人。作者以優美深刻的文字紀錄生命的最後歷程，探討醫病關係、生命的價值以及面對死亡時的尊嚴與勇氣。', '2016-08-02', '已上架'),
(10, 'BK00000010', '9789571376912', '睡出好腦力', '馬修‧沃克（Matthew Walker）', '時報出版', NULL, '頂尖神經科學家匯集多年研究，解開睡眠對大腦與身體機能的運作機制。書中詳細說明睡眠如何影響記憶巩固、情緒調節、免疫系統及代謝健康，並提供改善睡眠品質的實用建議', '2019-02-12', '已上架');

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

--
-- 傾印資料表的資料 `book_category`
--

INSERT INTO `book_category` (`bcg_id`, `bcg_name`) VALUES
(1, '心理成長'),
(2, '商業理財'),
(3, '歷史人文'),
(4, '科普知識'),
(5, '醫療生活'),
(6, '藝術設計'),
(7, '社會議題'),
(8, '推理懸疑'),
(9, '奇幻科幻'),
(10, '文學小說'),
(11, '漫畫'),
(12, '生活風格');

-- --------------------------------------------------------

--
-- 資料表結構 `book_categorys`
--

CREATE TABLE `book_categorys` (
  `book_id` int(11) NOT NULL COMMENT '書籍ID',
  `bcg_id` int(11) NOT NULL COMMENT '書籍類別ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='書籍所屬類別';

--
-- 傾印資料表的資料 `book_categorys`
--

INSERT INTO `book_categorys` (`book_id`, `bcg_id`) VALUES
(1, 1),
(2, 1),
(3, 2),
(4, 2),
(5, 3),
(6, 3),
(7, 4),
(8, 4),
(9, 5),
(10, 5);

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

--
-- 傾印資料表的資料 `member`
--

INSERT INTO `member` (`user_id`, `member_code`, `nickname`, `report_count`, `email`, `password`, `session_token`, `bio`, `account_status`, `created_at`, `achieve_id`, `total_exp`) VALUES
(1, 'MKD00000001', '尤', 0, 'you@gmail.com', '$2y$10$lms4tCHs3SC2lIif72ZAp.PhTzywPh7NMw/uaaV2wy6JaCi00g7mm', 'c8a4fae0a048cb247194354e9f483188562ee27d5c329e595d3cc79801a27024', NULL, '正常', '2026-08-18 19:18:55', NULL, 0);

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
-- 資料表結構 `notification`
--

CREATE TABLE `notification` (
  `notifi_id` int(11) NOT NULL COMMENT '通知編號',
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `type` enum('SYSTEM_MESSAGE',' NEW_REPLY','ACTIVITY','') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '通知類型:',
  `content` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '消息內容',
  `sent_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '消息內容',
  `is_starred` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否加星號',
  `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已讀'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- 資料表索引 `book_categorys`
--
ALTER TABLE `book_categorys`
  ADD PRIMARY KEY (`book_id`,`bcg_id`),
  ADD KEY `bcg_id` (`bcg_id`);

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
-- 資料表索引 `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notifi_id`);

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
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '書籍ID', AUTO_INCREMENT=13;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `book_application_form`
--
ALTER TABLE `book_application_form`
  MODIFY `book_ap_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '申請編號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `book_category`
--
ALTER TABLE `book_category`
  MODIFY `bcg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '書籍類別ID', AUTO_INCREMENT=25;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '使用者ID', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `notification`
--
ALTER TABLE `notification`
  MODIFY `notifi_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '通知編號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `segment`
--
ALTER TABLE `segment`
  MODIFY `segment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '段落ID';

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `book_application_form`
--
ALTER TABLE `book_application_form`
  ADD CONSTRAINT `book_application_form_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`),
  ADD CONSTRAINT `fk_bookform_isbn` FOREIGN KEY (`isbn`) REFERENCES `book` (`isbn`),
  ADD CONSTRAINT `fk_bookform_user` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`);

--
-- 資料表的限制式 `book_categorys`
--
ALTER TABLE `book_categorys`
  ADD CONSTRAINT `book_categorys_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`),
  ADD CONSTRAINT `book_categorys_ibfk_2` FOREIGN KEY (`bcg_id`) REFERENCES `book_category` (`bcg_id`);

--
-- 資料表的限制式 `book_collection`
--
ALTER TABLE `book_collection`
  ADD CONSTRAINT `book_collection_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`),
  ADD CONSTRAINT `book_collection_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`);

--
-- 資料表的限制式 `book_thoughts`
--
ALTER TABLE `book_thoughts`
  ADD CONSTRAINT `book_thoughts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`),
  ADD CONSTRAINT `book_thoughts_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`);

--
-- 資料表的限制式 `guildmember`
--
ALTER TABLE `guildmember`
  ADD CONSTRAINT `guildmember_ibfk_1` FOREIGN KEY (`guild_id`) REFERENCES `guild` (`guild_id`),
  ADD CONSTRAINT `guildmember_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`);

--
-- 資料表的限制式 `member_book_categorys`
--
ALTER TABLE `member_book_categorys`
  ADD CONSTRAINT `member_book_categorys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`),
  ADD CONSTRAINT `member_book_categorys_ibfk_2` FOREIGN KEY (`bcg_id`) REFERENCES `book_category` (`bcg_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
