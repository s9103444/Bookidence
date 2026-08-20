-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- 主機： localhost:8889
-- 產生時間： 2026-08-20 07:21:21
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

--
-- 傾印資料表的資料 `bulletin`
--

INSERT INTO `bulletin` (`bulletin_id`, `guild_id`, `user_id`, `posted_at`, `content`) VALUES
(1, 1, 2, '2026-08-20 02:29:20', '為了維持舒適、純粹的閱讀氛圍，有以下幾點溫柔的堅持：\r\n1.保持溫柔與包容：每個人對書籍的理解與喜好不同，這裡嚴禁流於高深的學術爭辯或批判他人的閱讀品味。 \r\n2.安靜的陪伴：在共讀時間請保持安靜，尊重彼此翻頁的空間，讓想獨處的人也能安心待著。 \r\n3.嚴禁過度商業或社交目的：這裡不歡迎推銷、直銷或過度的利益搭訕，請讓公會回歸最純粹的書香與溫度。');

-- --------------------------------------------------------

--
-- 資料表結構 `event`
--

CREATE TABLE `event` (
  `event_id` int(11) NOT NULL COMMENT '活動ID',
  `guild_id` int(11) NOT NULL COMMENT '公會ID',
  `book_id` int(11) NOT NULL COMMENT '書籍ID',
  `organizer_user_id` int(11) NOT NULL COMMENT '發起人會員ID',
  `leader_user_id` int(11) DEFAULT NULL COMMENT '領讀人',
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

--
-- 傾印資料表的資料 `event`
--

INSERT INTO `event` (`event_id`, `guild_id`, `book_id`, `organizer_user_id`, `leader_user_id`, `event_type`, `event_date`, `event_time`, `event_end_time`, `meeting_url`, `event_location`, `description`, `max_participants`, `deadline`, `event_status`) VALUES
(1, 1, 1, 1, 2, '線上(Online)', '2026-08-25', '09:00:00', '11:00:00', 'https://meet.google.com/abc-defg-hij', NULL, '本次讀書會將一起討論本月選書的前五章，歡迎帶著自己的心得與疑問加入！活動採線上進行，全程開放發言與交流，不需要提前準備，輕鬆聊書就好。', 10, '2026-08-22', '正常'),
(2, 1, 1, 3, 3, '線下(Offline)', '2026-08-29', '13:00:00', '15:00:00', NULL, '320桃園市中壢區舊明里長安街1之13號', '你也曾經是那個會畫出「吞了大象的蟒蛇」，卻被大人說是帽子的孩子嗎？\r\n這次我們想找幾位一樣還記得那份天真的人，一起在咖啡香裡重新翻開《小王子》。不需要準備什麼深奧的見解，帶著你對那朵玫瑰、那隻狐狸、或是那片星空的想法來就好——聊聊我們是不是也曾經，在長大的路上不小心弄丟了自己的星球。', 5, '2026-08-21', '正常'),
(5, 1, 1, 2, NULL, '線下(Offline)', '2026-08-31', '09:00:00', '12:00:00', NULL, '台北市信義區松高路11號', '久違的實體聚會來囉！這次選在一間安靜舒適的咖啡廳，除了聊書之外也歡迎帶上自己最近在讀的其他作品互相推薦。現場備有簡單茶點，記得提前完成報名以確保位置。', 10, '2026-08-28', '正常'),
(6, 1, 1, 1, 1, '線上(Online)', '2026-09-05', '14:00:00', '17:00:00', 'https://meet.google.com/qwe-rtyu-iop', NULL, '這次聚會聚焦在角色成長與世界觀設定的探討，會由發起人先簡單分享導讀重點，接著開放大家分享自己最有感觸的段落。建議提前讀完指定章節，討論會更有共鳴喔！', 7, '2026-08-25', '正常');

-- --------------------------------------------------------

--
-- 資料表結構 `event_registration`
--

CREATE TABLE `event_registration` (
  `event_id` int(11) NOT NULL COMMENT '活動ID',
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `submitted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '報名送出時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='報名紀錄';

--
-- 傾印資料表的資料 `event_registration`
--

INSERT INTO `event_registration` (`event_id`, `user_id`, `submitted_at`) VALUES
(1, 2, '2026-08-20 11:45:52'),
(1, 3, '2026-08-20 11:45:52'),
(2, 1, '2026-08-20 11:47:07'),
(2, 2, '2026-08-20 11:47:07');

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
  `guild_skin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公會外觀'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='讀書公會';

--
-- 傾印資料表的資料 `guild`
--

INSERT INTO `guild` (`guild_id`, `guild_code`, `book_id`, `guild_name`, `founded_at`, `guild_avatar`, `intro`, `approval_required`, `member_count`, `guild_status`, `guild_skin`) VALUES
(1, 'GD00000001', 1, '壁爐與貓', '2026-08-19', 'https://drive.google.com/uc?export=view&id=1cv1YISIpwmjBy23eJIyNwVj8ap_6F3xX', '深夜的鐘聲響起，這裡是愛書人的避風港。有劈啪作響的溫暖壁爐，有腳邊打盹的貓，還有手中那本尚未讀完的書。\r\n\r\n我們偏好的書籍類型不設限，但更傾向於具有療癒、探索感或引人深思的作品：\r\n奇幻與架空冒險：喜歡跟著主角踏入宏大的世界觀與神祕古老的歷史。\r\n雋永散文與心靈療癒：在文字中尋找共鳴，撫平日常的焦慮與疲憊。\r\n經典文學與各類小說：品味文字的細膩編織，探討故事背後的人性與智慧。', 0, 10, '正常', 'https://drive.google.com/uc?export=view&id=1pqrxt858OyLKkfKHXJA90a18oCQC4NiB');

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

--
-- 傾印資料表的資料 `guilddiscussion`
--

INSERT INTO `guilddiscussion` (`message_id`, `segment_id`, `user_id`, `posted_at`, `content`, `photo`) VALUES
(1, 1, 2, '2026-08-19 22:32:39', '笑死，我把這張「大蟒蛇消化大象」的圖給別人看，她第一句話直接說「這是帽子吧」，跟書裡的大人一模一樣，我笑到不行。後來想想我自己出社會後好像也越來越常這樣，看到什麼都先用最簡單、最方便的方式解讀，懶得多想一層。可能我們每個人心裡都住著一個把大象看成帽子的大人，只是平常沒被拆穿而已。', ''),
(2, 1, 3, '2026-08-19 22:32:39', '我倒是對「你要永遠對你馴養的東西負責」這句印象最深。第一次讀的時候只覺得是狐狸在講道理，但這次搭配小王子跟玫瑰的關係一起看，才意識到這句話其實有點沉重──馴養不是一時興起，而是一種承諾，一旦開始了就不能說放就放。放到現代人際關係裡也很適用，我們常常很輕易地建立連結，卻很少想到「負責」這個部分，責任感常常被忽略，這也是我覺得這本書寫給大人看的原因之一。', ''),
(3, 1, 4, '2026-08-19 22:32:39', '小王子問狐狸「什麼是馴養」的時候，我覺得他其實是在問「什麼是愛」，只是他還不知道那個詞，只能用最直接的方式去問。這種天真反而讓答案更有力量──狐狸沒有給一個很抽象的定義，而是講「建立連結」，還有「你的玫瑰對你來說會變得比全世界任何一朵玫瑰都重要」，用很具體的畫面去解釋一個很抽象的概念。我覺得這也是這本書厲害的地方，它不是用大人的邏輯講道理，而是讓一個孩子問出最單純也最核心的問題，逼我們重新想一遍自己已經習慣不去想的事情。', '');

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

--
-- 傾印資料表的資料 `guildmember`
--

INSERT INTO `guildmember` (`user_id`, `guild_id`, `permission_level`, `member_status`) VALUES
(2, 1, '會長', '在會中'),
(3, 1, '副會長', '在會中'),
(4, 1, '一般', '在會中'),
(5, 1, '一般', '申請中'),
(6, 1, '一般', '申請中'),
(7, 1, '一般', '申請中');

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

--
-- 傾印資料表的資料 `guildrecord`
--

INSERT INTO `guildrecord` (`record_id`, `book_id`, `guild_id`, `record_date`, `end_date`) VALUES
(1, 1, 1, '2026-08-19', '2026-08-30');

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
(1, 'MKD00000001', '尤', 0, 'you@gmail.com', '$2y$10$lms4tCHs3SC2lIif72ZAp.PhTzywPh7NMw/uaaV2wy6JaCi00g7mm', '5d819afaf6955f9dd0143fabbee383e272f972265595f357261cebafb4a2d594', NULL, '正常', '2026-08-18 19:18:55', 0),
(2, 'MKD00000002', '小森', 0, 'test2@test.com', '$2y$10$examplehash0000000002', NULL, '推理小說愛好者', '正常', '2026-08-19 21:57:14', 0),
(3, 'MKD00000003', '阿林', 0, 'test3@test.com', '$2y$10$examplehash0000000003', NULL, '喜歡散文與心靈療癒書籍', '正常', '2026-08-19 21:57:14', 0),
(4, 'MKD00000004', '小蘑菇', 0, 'test4@test.com', '$2y$10$examplehash0000000004', NULL, '文學小說控', '正常', '2026-08-19 21:57:14', 0),
(5, 'MKD00000005', '小貓', 0, 'test5@test.com', '$2y$10$examplehash0000000005', NULL, '喜歡架空冒險故事', '正常', '2026-08-19 21:57:14', 0),
(6, 'MKD00000006', '小熊', 0, 'test6@test.com', '$2y$10$examplehash0000000006', NULL, '喜歡輕鬆的日常系作品', '正常', '2026-08-19 21:57:14', 0),
(7, 'MKD00000007', '小松鼠', 0, 'test7@test.com', '$2y$10$examplehash0000000007', NULL, '喜歡懸疑推理', '正常', '2026-08-19 21:57:14', 0);

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

--
-- 傾印資料表的資料 `segment`
--

INSERT INTO `segment` (`segment_id`, `record_id`, `start_chapter`, `end_chapter`, `expected_end_date`, `sort_order`) VALUES
(1, 1, 1, 2, '2026-08-20', 1),
(2, 1, 3, 4, '2026-08-25', 2),
(3, 1, 5, 6, '2026-08-30', 3);

-- --------------------------------------------------------

--
-- 資料表結構 `staff`
--

CREATE TABLE `staff` (
  `staff_account` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '帳號',
  `staff_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名（畫面顯示用）',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密碼，非明碼'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='員工';

--
-- 傾印資料表的資料 `staff`
--

INSERT INTO `staff` (`staff_account`, `staff_name`, `password`) VALUES
('shuyun', '書芸', '$2y$10$0Aw5t3lq51D4l8Tg5XRFaOGk.aMsGoBzAMIcZGSOVgGCzvFLhQdEK');

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
-- 資料表索引 `bulletin`
--
ALTER TABLE `bulletin`
  ADD PRIMARY KEY (`bulletin_id`),
  ADD KEY `fk_bulletin_guild` (`guild_id`),
  ADD KEY `fk_bulletin_user` (`user_id`);

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
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '書籍ID', AUTO_INCREMENT=11;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `book_application_form`
--
ALTER TABLE `book_application_form`
  MODIFY `book_ap_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '申請編號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `book_category`
--
ALTER TABLE `book_category`
  MODIFY `bcg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '書籍類別ID', AUTO_INCREMENT=13;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `book_thoughts`
--
ALTER TABLE `book_thoughts`
  MODIFY `b_thought_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '書籍心得ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `bulletin`
--
ALTER TABLE `bulletin`
  MODIFY `bulletin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公告編號', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '活動ID', AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `exp_log`
--
ALTER TABLE `exp_log`
  MODIFY `exp_log_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '編號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `guild`
--
ALTER TABLE `guild`
  MODIFY `guild_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公會id', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `guilddiscussion`
--
ALTER TABLE `guilddiscussion`
  MODIFY `message_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '訊息編號', AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `guildrecord`
--
ALTER TABLE `guildrecord`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '讀書紀錄ID', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `login_log`
--
ALTER TABLE `login_log`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'log編號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `member`
--
ALTER TABLE `member`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '使用者ID', AUTO_INCREMENT=8;

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
  MODIFY `segment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '段落ID', AUTO_INCREMENT=4;

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
-- 資料表的限制式 `bulletin`
--
ALTER TABLE `bulletin`
  ADD CONSTRAINT `fk_bulletin_guild` FOREIGN KEY (`guild_id`) REFERENCES `guild` (`guild_id`),
  ADD CONSTRAINT `fk_bulletin_user` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`);

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
