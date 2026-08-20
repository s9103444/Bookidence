-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- 主機： localhost:8889
-- 產生時間： 2026-08-20 12:01:09
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
-- 資料表結構 `achieve`
--

CREATE TABLE `achieve` (
  `achieve_id` char(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '成就類別ID',
  `achieve_name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '成就名稱',
  `achieve_icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '成就圖示,圖片路徑',
  `unlock_condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '達成條件說明'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='成就類別';

-- --------------------------------------------------------

--
-- 資料表結構 `appear`
--

CREATE TABLE `appear` (
  `appear_id` char(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '外觀類別ID',
  `type` enum('性別','髮色','瞳色','膚色') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '類別',
  `option_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '選項名稱,顏色',
  `icon_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '圖示路徑,素材圖片路徑'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='外觀類別';

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
(1, 'BK00000001', '9789861755267', '原子習慣', '詹姆斯‧克利爾（James Clear）', '方智出版社', 'book-covers/9789861755267.jpg', '本書說明微小改變如何帶來巨大躍進。作者提出建立好習慣與戒除壞習慣的四階法則——讓提示顯而易見、讓渴望有吸引力、讓行動輕而易舉、讓獎賞令人滿足，幫助讀者透過系統化方式調整行為，達成持續性的自我成長。', '2019-05-01', '已上架'),
(2, 'BK00000002', '9789861753805', '被討厭的勇氣', '岸見一郎、古賀史健', '究竟出版社', 'book-covers/9789861753805.jpg', '本書以年輕人與哲學家的對話形式，深入淺出地介紹阿德勒心理學。核心觀點強調「人的煩惱皆來自於人際關係」，並提出「課題分離」概念，鼓勵讀者擺脫他人期待的枷鎖，勇敢面對當下，獲得真正的自由與幸福。', '2014-10-30', '已下架'),
(3, 'BK00000003', '9789578423220', '致富心態', '摩根‧豪瑟（Morgan Housel）', '商周出版社', 'book-covers/9789578423220.jpg', '作者透過19個簡短故事，剖析人們處理金錢時的心理與行為模式。本書強調理財成功與否往往不取決於個人智商或專業知識，而在於如何控制情緒與面對風險，協助讀者建立健康、可持續的財務觀念。', '2021-01-27', '已下架'),
(4, 'BK00000004', '9789863842347', '富爸爸，窮爸爸', '羅伯特·徹·清崎（Robert T. Kiyosaki）', '高寶出版社', 'book-covers/9789863842347.jpg', '作者透過對比親生父親與朋友父親不同的金錢觀，闡述資產與負債的本質區別。書中提出「讓錢為你工作」而非「為錢工作」的核心概念，奠定了現代個人理財與財務自由思維的基础。', '2017-08-16', '已上架'),
(5, 'BK00000005', '9789573274711', '人類大歷史', '尤瓦爾‧諾瓦‧赫拉利（Yuval Noah Harari）', '遠流出版社', 'book-covers/9789573274711.jpg', '本書跨越十萬年歷史，將人類演化梳理為認知革命、農業革命與科學革命三大里程碑。作者提出人類憑藉「想像的秩序」與虛構故事實現大規模合作，從而登上地球生物鏈頂端，提供全新的歷史視角。', '2014-09-01', '已下架'),
(6, 'BK00000006', '9789865511876', '失落的城池：文明的盛衰與人類的未來', '安娜里‧紐維茲（Annalalee Newitz）', '廣場出版', 'book-covers/9789865511876.jpg', '本書深入探訪龐貝、恰塔霍裕克、吳哥與卡霍基亞四大古城遺跡。結合最新考古發現與歷史文獻，作者擺脫傳統「文明毀滅」的陳腔濫調，從基層人民與都市規劃的角度，重新思考古代城市興衰對現代都市發展的啟示。', '2021-11-03', '已下架'),
(7, 'BK00000007', '9789862164013', '自私的基因', '理查‧道金斯（Richard Dawkins）', '天下文化', 'book-covers/9789862164013.jpg', '演化生物學經典巨著，提出以「基因」為核心的演化觀點。作者主張生物個體僅是基因延續下去的載體，並透過演化賽局理論解釋自然界中的利他行為與競爭現象，深刻影響了當代生物學與社會學。', '2009-09-25', '已下架'),
(8, 'BK00000008', '9789862415795', '思考，快與慢', '丹尼爾‧康納曼（Daniel Kahneman）', '天下文化', 'book-covers/9789862415795.jpg', '諾貝爾經濟學獎得主康納曼剖析大腦決策機制的集大成之作。書中將思維劃分為直覺敏捷的「系統一」與理性客觀的「系統二」，揭示人類在面對不確定性時常見的認知偏誤與心理盲點。', '2012-10-31', '已下架'),
(9, 'BK00000009', '9789863982180', '當呼吸化為空氣', '保羅‧柯拉尼蒂（Paul Kalanithi）', '時報出版', 'book-covers/9789863982180.jpg', '一位年輕的神經外科醫師在即將完成訓練之際罹患肺癌末期，轉身成為面對死亡的病人。作者以優美深刻的文字紀錄生命的最後歷程，探討醫病關係、生命的價值以及面對死亡時的尊嚴與勇氣。', '2016-08-02', '已上架'),
(10, 'BK00000010', '9789571376912', '睡出好腦力', '馬修‧沃克（Matthew Walker）', '時報出版', 'book-covers/9789571376912.jpg', '頂尖神經科學家匯集多年研究，解開睡眠對大腦與身體機能的運作機制。書中詳細說明睡眠如何影響記憶巩固、情緒調節、免疫系統及代謝健康，並提供改善睡眠品質的實用建議', '2019-02-12', '已上架');

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
  `ap_status` enum('待處理','已駁回','已核准') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '待處理' COMMENT '處理狀態',
  `reject_reason` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '駁回原因'
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
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  `bth_status` enum('公開','非公開','儲存草稿') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '儲存草稿' COMMENT '心得狀態'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='書籍心得';

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
-- 資料表結構 `exp_log`
--

CREATE TABLE `exp_log` (
  `exp_log_id` bigint(20) NOT NULL COMMENT '編號',
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `event_description` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '事件說明',
  `occurred_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '時間',
  `event_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '事件類型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='經驗值紀錄';

-- --------------------------------------------------------

--
-- 資料表結構 `exp_rule`
--

CREATE TABLE `exp_rule` (
  `event_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '事件類型',
  `exp_value` int(10) UNSIGNED NOT NULL COMMENT '經驗值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='經驗值規則';

-- --------------------------------------------------------

--
-- 資料表結構 `friendship`
--

CREATE TABLE `friendship` (
  `user_id_a` int(11) NOT NULL COMMENT '使用者A(邀請人)',
  `user_id_b` int(11) NOT NULL COMMENT '使用者B(被邀請人)',
  `rel_status` enum('申請中','已成為好友','不同意') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '申請中' COMMENT '關係狀態'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='好友關係';

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
  `guild_skin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公會外觀',
  `announcement` text COLLATE utf8mb4_unicode_ci COMMENT '公會公告'
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
-- 資料表結構 `guildrecord`
--

CREATE TABLE `guildrecord` (
  `record_id` int(11) NOT NULL COMMENT '讀書紀錄ID',
  `book_id` int(11) NOT NULL COMMENT '書籍ID',
  `guild_id` int(11) NOT NULL COMMENT '公會id',
  `record_date` date NOT NULL COMMENT '開始日期',
  `end_date` date NOT NULL COMMENT '結束日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='公會讀書紀錄';

-- --------------------------------------------------------

--
-- 資料表結構 `login_log`
--

CREATE TABLE `login_log` (
  `log_id` bigint(20) NOT NULL COMMENT 'log編號',
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `login_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '登入時間',
  `login_result` enum('成功','失敗') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登入結果',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP位址'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='登入紀錄';

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
  `total_exp` int(11) DEFAULT '0' COMMENT '累積經驗值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='會員';

--
-- 傾印資料表的資料 `member`
--

INSERT INTO `member` (`user_id`, `member_code`, `nickname`, `report_count`, `email`, `password`, `session_token`, `bio`, `account_status`, `created_at`, `total_exp`) VALUES
(1, 'MKD00000001', '尤', 0, 'you@gmail.com', '$2y$10$lms4tCHs3SC2lIif72ZAp.PhTzywPh7NMw/uaaV2wy6JaCi00g7mm', '5d819afaf6955f9dd0143fabbee383e272f972265595f357261cebafb4a2d594', NULL, '正常', '2026-08-18 19:18:55', 0);

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
-- 資料表結構 `moderation_action`
--

CREATE TABLE `moderation_action` (
  `action_id` int(11) NOT NULL COMMENT '處分編號',
  `target_user_id` int(11) NOT NULL COMMENT '被處分會員ID',
  `staff_account` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '執行人員帳號',
  `report_id` int(11) DEFAULT NULL COMMENT '來源檢舉單號，NULL=後台主動處分',
  `action_type` enum('警告','停權','解除停權','刪除內容') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '處分類型',
  `reason` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '處分原因，會顯示給被處分者看',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '處分時間',
  `revoked_at` datetime DEFAULT NULL COMMENT '撤銷時間，判錯而收回',
  `revoked_by` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '撤銷人員帳號'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理處分紀錄';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='通知中心';

-- --------------------------------------------------------

--
-- 資料表結構 `report`
--

CREATE TABLE `report` (
  `report_id` int(11) NOT NULL COMMENT '檢舉編號',
  `reporter_id` int(11) NOT NULL COMMENT '檢舉人ID',
  `reported_user_id` int(11) NOT NULL COMMENT '被檢舉人ID',
  `b_thought_id` int(11) DEFAULT NULL COMMENT '被檢舉的心得ID，心得檢舉時填',
  `message_id` bigint(20) DEFAULT NULL COMMENT '被檢舉的留言ID，留言檢舉時填',
  `staff_account` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '後台處理人員，待處理時為空',
  `target_type` enum('心得','留言') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '檢舉類型',
  `reason` enum('人身攻擊','廣告垃圾資訊','不當內容','抄襲 / 侵權') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '檢舉原因',
  `reason_detail` text COLLATE utf8mb4_unicode_ci COMMENT '檢舉原因敘述，字數上限500',
  `status` enum('尚未處理','檢舉成立','檢舉不成立') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '尚未處理' COMMENT '處理狀態',
  `action_taken` enum('刪除內容','警告用戶','停權用戶','駁回') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '處理動作',
  `resolution_notes` text COLLATE utf8mb4_unicode_ci COMMENT '處理紀錄與結果通知，字數上限500',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '檢舉時間',
  `resolved_at` datetime DEFAULT NULL COMMENT '執行時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='檢舉表單';

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
-- 資料表結構 `staff`
--

CREATE TABLE `staff` (
  `staff_account` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '帳號',
  `staff_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名（畫面顯示用）',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密碼，非明碼',
  `session_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '登入 session token'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='員工';

--
-- 傾印資料表的資料 `staff`
--

INSERT INTO `staff` (`staff_account`, `staff_name`, `password`, `session_token`) VALUES
('shuyun', '書芸', '$2y$10$0Aw5t3lq51D4l8Tg5XRFaOGk.aMsGoBzAMIcZGSOVgGCzvFLhQdEK', NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `user_achieve`
--

CREATE TABLE `user_achieve` (
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `achieve_id` char(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '成就類別ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='使用者成就';

-- --------------------------------------------------------

--
-- 資料表結構 `user_appear`
--

CREATE TABLE `user_appear` (
  `user_id` int(11) NOT NULL COMMENT '使用者ID(擁有者)',
  `appear_id` char(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '外觀類別ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='使用者外觀';

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `achieve`
--
ALTER TABLE `achieve`
  ADD PRIMARY KEY (`achieve_id`);

--
-- 資料表索引 `appear`
--
ALTER TABLE `appear`
  ADD PRIMARY KEY (`appear_id`);

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
-- 資料表索引 `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `fk_event_guild` (`guild_id`),
  ADD KEY `fk_event_book` (`book_id`),
  ADD KEY `fk_event_organizer` (`organizer_user_id`),
  ADD KEY `fk_event_leader` (`leader_user_id`);

--
-- 資料表索引 `event_registration`
--
ALTER TABLE `event_registration`
  ADD PRIMARY KEY (`event_id`,`user_id`),
  ADD KEY `fk_eventreg_user` (`user_id`);

--
-- 資料表索引 `exp_log`
--
ALTER TABLE `exp_log`
  ADD PRIMARY KEY (`exp_log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_event_type` (`event_type`);

--
-- 資料表索引 `exp_rule`
--
ALTER TABLE `exp_rule`
  ADD PRIMARY KEY (`event_type`);

--
-- 資料表索引 `friendship`
--
ALTER TABLE `friendship`
  ADD PRIMARY KEY (`user_id_a`,`user_id_b`),
  ADD KEY `fk_friendship_b` (`user_id_b`);

--
-- 資料表索引 `guild`
--
ALTER TABLE `guild`
  ADD PRIMARY KEY (`guild_id`),
  ADD UNIQUE KEY `guild_code` (`guild_code`),
  ADD KEY `fk_guild_book` (`book_id`);

--
-- 資料表索引 `guilddiscussion`
--
ALTER TABLE `guilddiscussion`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `fk_guilddiscussion_segment` (`segment_id`),
  ADD KEY `fk_guilddiscussion_user` (`user_id`);

--
-- 資料表索引 `guildmember`
--
ALTER TABLE `guildmember`
  ADD PRIMARY KEY (`user_id`,`guild_id`),
  ADD KEY `guild_id` (`guild_id`);

--
-- 資料表索引 `guildrecord`
--
ALTER TABLE `guildrecord`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `idx_book_id` (`book_id`),
  ADD KEY `idx_guild_id` (`guild_id`);

--
-- 資料表索引 `login_log`
--
ALTER TABLE `login_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`);

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
-- 資料表索引 `moderation_action`
--
ALTER TABLE `moderation_action`
  ADD PRIMARY KEY (`action_id`),
  ADD KEY `idx_target_created` (`target_user_id`,`created_at`),
  ADD KEY `idx_staff` (`staff_account`),
  ADD KEY `idx_report` (`report_id`),
  ADD KEY `idx_revoker` (`revoked_by`);

--
-- 資料表索引 `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notifi_id`),
  ADD KEY `fk_notification_user` (`user_id`);

--
-- 資料表索引 `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`),
  ADD UNIQUE KEY `uq_reporter_thought` (`reporter_id`,`b_thought_id`),
  ADD KEY `idx_status_created` (`status`,`created_at`),
  ADD KEY `idx_reported_user` (`reported_user_id`),
  ADD KEY `idx_thought` (`b_thought_id`),
  ADD KEY `idx_message` (`message_id`),
  ADD KEY `idx_staff` (`staff_account`);

--
-- 資料表索引 `segment`
--
ALTER TABLE `segment`
  ADD PRIMARY KEY (`segment_id`),
  ADD KEY `fk_segment_record` (`record_id`);

--
-- 資料表索引 `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_account`);

--
-- 資料表索引 `user_achieve`
--
ALTER TABLE `user_achieve`
  ADD PRIMARY KEY (`user_id`,`achieve_id`),
  ADD KEY `fk_userachieve_achieve` (`achieve_id`);

--
-- 資料表索引 `user_appear`
--
ALTER TABLE `user_appear`
  ADD PRIMARY KEY (`user_id`,`appear_id`),
  ADD KEY `fk_userappear_appear` (`appear_id`);

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
-- 使用資料表自動遞增(AUTO_INCREMENT) `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '活動ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `exp_log`
--
ALTER TABLE `exp_log`
  MODIFY `exp_log_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '編號';

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
-- 使用資料表自動遞增(AUTO_INCREMENT) `guildrecord`
--
ALTER TABLE `guildrecord`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '讀書紀錄ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `login_log`
--
ALTER TABLE `login_log`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'log編號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `member`
--
ALTER TABLE `member`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '使用者ID', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `moderation_action`
--
ALTER TABLE `moderation_action`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '處分編號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `notification`
--
ALTER TABLE `notification`
  MODIFY `notifi_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '通知編號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `report`
--
ALTER TABLE `report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '檢舉編號';

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
  ADD CONSTRAINT `book_application_form_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`);

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
-- 資料表的限制式 `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_book` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`),
  ADD CONSTRAINT `fk_event_guild` FOREIGN KEY (`guild_id`) REFERENCES `guild` (`guild_id`),
  ADD CONSTRAINT `fk_event_leader` FOREIGN KEY (`leader_user_id`) REFERENCES `member` (`user_id`),
  ADD CONSTRAINT `fk_event_organizer` FOREIGN KEY (`organizer_user_id`) REFERENCES `member` (`user_id`);

--
-- 資料表的限制式 `event_registration`
--
ALTER TABLE `event_registration`
  ADD CONSTRAINT `fk_eventreg_event` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`),
  ADD CONSTRAINT `fk_eventreg_user` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`);

--
-- 資料表的限制式 `exp_log`
--
ALTER TABLE `exp_log`
  ADD CONSTRAINT `fk_explog_type` FOREIGN KEY (`event_type`) REFERENCES `exp_rule` (`event_type`),
  ADD CONSTRAINT `fk_explog_user` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`);

--
-- 資料表的限制式 `friendship`
--
ALTER TABLE `friendship`
  ADD CONSTRAINT `fk_friendship_a` FOREIGN KEY (`user_id_a`) REFERENCES `member` (`user_id`),
  ADD CONSTRAINT `fk_friendship_b` FOREIGN KEY (`user_id_b`) REFERENCES `member` (`user_id`);

--
-- 資料表的限制式 `guild`
--
ALTER TABLE `guild`
  ADD CONSTRAINT `fk_guild_book` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`);

--
-- 資料表的限制式 `guilddiscussion`
--
ALTER TABLE `guilddiscussion`
  ADD CONSTRAINT `fk_guilddiscussion_segment` FOREIGN KEY (`segment_id`) REFERENCES `segment` (`segment_id`),
  ADD CONSTRAINT `fk_guilddiscussion_user` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`);

--
-- 資料表的限制式 `guildmember`
--
ALTER TABLE `guildmember`
  ADD CONSTRAINT `guildmember_ibfk_1` FOREIGN KEY (`guild_id`) REFERENCES `guild` (`guild_id`),
  ADD CONSTRAINT `guildmember_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`);

--
-- 資料表的限制式 `guildrecord`
--
ALTER TABLE `guildrecord`
  ADD CONSTRAINT `fk_guildrecord_book` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`),
  ADD CONSTRAINT `fk_guildrecord_guild` FOREIGN KEY (`guild_id`) REFERENCES `guild` (`guild_id`);

--
-- 資料表的限制式 `login_log`
--
ALTER TABLE `login_log`
  ADD CONSTRAINT `fk_loginlog_user` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`);

--
-- 資料表的限制式 `member_book_categorys`
--
ALTER TABLE `member_book_categorys`
  ADD CONSTRAINT `member_book_categorys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`),
  ADD CONSTRAINT `member_book_categorys_ibfk_2` FOREIGN KEY (`bcg_id`) REFERENCES `book_category` (`bcg_id`);

--
-- 資料表的限制式 `moderation_action`
--
ALTER TABLE `moderation_action`
  ADD CONSTRAINT `fk_ma_report` FOREIGN KEY (`report_id`) REFERENCES `report` (`report_id`),
  ADD CONSTRAINT `fk_ma_revoker` FOREIGN KEY (`revoked_by`) REFERENCES `staff` (`staff_account`),
  ADD CONSTRAINT `fk_ma_staff` FOREIGN KEY (`staff_account`) REFERENCES `staff` (`staff_account`),
  ADD CONSTRAINT `fk_ma_target` FOREIGN KEY (`target_user_id`) REFERENCES `member` (`user_id`);

--
-- 資料表的限制式 `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_notification_user` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`);

--
-- 資料表的限制式 `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `fk_report_message` FOREIGN KEY (`message_id`) REFERENCES `guilddiscussion` (`message_id`),
  ADD CONSTRAINT `fk_report_reported` FOREIGN KEY (`reported_user_id`) REFERENCES `member` (`user_id`),
  ADD CONSTRAINT `fk_report_reporter` FOREIGN KEY (`reporter_id`) REFERENCES `member` (`user_id`),
  ADD CONSTRAINT `fk_report_staff` FOREIGN KEY (`staff_account`) REFERENCES `staff` (`staff_account`),
  ADD CONSTRAINT `fk_report_thought` FOREIGN KEY (`b_thought_id`) REFERENCES `book_thoughts` (`b_thought_id`);

--
-- 資料表的限制式 `segment`
--
ALTER TABLE `segment`
  ADD CONSTRAINT `fk_segment_record` FOREIGN KEY (`record_id`) REFERENCES `guildrecord` (`record_id`);

--
-- 資料表的限制式 `user_achieve`
--
ALTER TABLE `user_achieve`
  ADD CONSTRAINT `fk_userachieve_achieve` FOREIGN KEY (`achieve_id`) REFERENCES `achieve` (`achieve_id`),
  ADD CONSTRAINT `fk_userachieve_member` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`);

--
-- 資料表的限制式 `user_appear`
--
ALTER TABLE `user_appear`
  ADD CONSTRAINT `fk_userappear_appear` FOREIGN KEY (`appear_id`) REFERENCES `appear` (`appear_id`),
  ADD CONSTRAINT `fk_userappear_user` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
