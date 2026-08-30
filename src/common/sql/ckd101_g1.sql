-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- 主機： localhost:8889
-- 產生時間： 2026-08-28 08:32:50
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

--
-- 傾印資料表的資料 `achieve`
--

INSERT INTO `achieve` (`achieve_id`, `achieve_name`, `achieve_icon`, `unlock_condition`) VALUES
('a01', '旅程初啟', 'achievement-badges/new-member.png', '踏入書香世界的第一步！'),
('a02', '初次典藏', 'achievement-badges/first-participate.png', '將第一本好書放入書架。'),
('a03', '文字共鳴', 'achievement-badges/first-update.png', '首次參與聚會，與夥伴交流。');

-- --------------------------------------------------------

--
-- 資料表結構 `appear`
--

CREATE TABLE `appear` (
  `appear_id` char(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '外觀類別ID',
  `type` enum('性別','髮色','瞳色','膚色') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '類別',
  `option_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '選項名稱,顏色',
  `gender` enum('通用','女','男') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '通用' COMMENT '適用性別',
  `color_value` char(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '色塊色碼(hex)',
  `icon_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '圖示路徑,素材圖片路徑'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='外觀類別';

--
-- 傾印資料表的資料 `appear`
--

INSERT INTO `appear` (`appear_id`, `type`, `option_name`, `gender`, `color_value`, `icon_path`) VALUES
('fe1', '瞳色', '黑眼睛', '女', '#333333', 'images/appear/character-for-register/female_eyes_black.png'),
('fe2', '瞳色', '藍眼睛', '女', '#244C6B', 'images/appear/character-for-register/female_eyes_blue.png'),
('fe3', '瞳色', '綠眼睛', '女', '#809320', 'images/appear/character-for-register/female_eyes_green.png'),
('fh1', '髮色', '黑髮色', '女', '#41464E', 'images/appear/character-for-register/female_hair_black.png'),
('fh2', '髮色', '藍髮色', '女', '#2D4363', 'images/appear/character-for-register/female_hair_blue.png'),
('fh3', '髮色', '咖啡色', '女', '#B4641E', 'images/appear/character-for-register/female_hair_brown.png'),
('fs1', '膚色', '白皮膚', '女', '#F8DCBB', 'images/appear/character-for-register/female_skin_light.png'),
('fs2', '膚色', '黃皮膚', '女', '#F1C88A', 'images/appear/character-for-register/female_skin_medium.png'),
('fs3', '膚色', '深皮膚', '女', '#C38F61', 'images/appear/character-for-register/female_skin_dark.png'),
('g01', '性別', '女生', '通用', NULL, NULL),
('g02', '性別', '男生', '通用', NULL, NULL),
('me1', '瞳色', '黑眼睛', '男', '#333333', 'images/appear/character-for-register/male_eyes_black.png'),
('me2', '瞳色', '藍眼睛', '男', '#244C6B', 'images/appear/character-for-register/male_eyes_blue.png'),
('me3', '瞳色', '綠眼睛', '男', '#809320', 'images/appear/character-for-register/male_eyes_green.png'),
('mh1', '髮色', '黑髮色', '男', '#41464E', 'images/appear/character-for-register/male_hair_black.png'),
('mh2', '髮色', '藍髮色', '男', '#2D4363', 'images/appear/character-for-register/male_hair_blue.png'),
('mh3', '髮色', '咖啡色', '男', '#B4641E', 'images/appear/character-for-register/male_hair_brown.png'),
('ms1', '膚色', '白皮膚', '男', '#F8DCBB', 'images/appear/character-for-register/male_skin_light.png'),
('ms2', '膚色', '黃皮膚', '男', '#F1C88A', 'images/appear/character-for-register/male_skin_medium.png'),
('ms3', '膚色', '深皮膚', '男', '#C38F61', 'images/appear/character-for-register/male_skin_dark.png');

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
(2, 'BK00000002', '9789861753805', '被討厭的勇氣', '岸見一郎、古賀史健', '究竟出版社', 'book-covers/9789861753805.jpg', '本書以年輕人與哲學家的對話形式，深入淺出地介紹阿德勒心理學。核心觀點強調「人的煩惱皆來自於人際關係」，並提出「課題分離」概念，鼓勵讀者擺脫他人期待的枷鎖，勇敢面對當下，獲得真正的自由與幸福。', '2014-10-30', '已上架'),
(3, 'BK00000003', '9789578423220', '致富心態', '摩根‧豪瑟（Morgan Housel）', '商周出版社', 'book-covers/9789578423220.jpg', '作者透過19個簡短故事，剖析人們處理金錢時的心理與行為模式。本書強調理財成功與否往往不取決於個人智商或專業知識，而在於如何控制情緒與面對風險，協助讀者建立健康、可持續的財務觀念。', '2021-01-27', '已上架'),
(4, 'BK00000004', '9789863842347', '富爸爸，窮爸爸', '羅伯特·徹·清崎（Robert T. Kiyosaki）', '高寶出版社', 'book-covers/9789863842347.jpg', '作者透過對比親生父親與朋友父親不同的金錢觀，闡述資產與負債的本質區別。書中提出「讓錢為你工作」而非「為錢工作」的核心概念，奠定了現代個人理財與財務自由思維的基础。', '2017-08-16', '已上架'),
(5, 'BK00000005', '9789573274711', '人類大歷史', '尤瓦爾‧諾瓦‧赫拉利（Yuval Noah Harari）', '遠流出版社', 'book-covers/9789573274711.jpg', '本書跨越十萬年歷史，將人類演化梳理為認知革命、農業革命與科學革命三大里程碑。作者提出人類憑藉「想像的秩序」與虛構故事實現大規模合作，從而登上地球生物鏈頂端，提供全新的歷史視角。', '2014-09-01', '已上架'),
(6, 'BK00000006', '9789865511876', '失落的城池：文明的盛衰與人類的未來', '安娜里‧紐維茲（Annalalee Newitz）', '廣場出版', 'book-covers/9789865511876.jpg', '本書深入探訪龐貝、恰塔霍裕克、吳哥與卡霍基亞四大古城遺跡。結合最新考古發現與歷史文獻，作者擺脫傳統「文明毀滅」的陳腔濫調，從基層人民與都市規劃的角度，重新思考古代城市興衰對現代都市發展的啟示。', '2021-11-03', '已上架'),
(7, 'BK00000007', '9789862164013', '自私的基因', '理查‧道金斯（Richard Dawkins）', '天下文化', 'book-covers/9789862164013.jpg', '演化生物學經典巨著，提出以「基因」為核心的演化觀點。作者主張生物個體僅是基因延續下去的載體，並透過演化賽局理論解釋自然界中的利他行為與競爭現象，深刻影響了當代生物學與社會學。', '2009-09-25', '已上架'),
(8, 'BK00000008', '9789862415795', '思考，快與慢', '丹尼爾‧康納曼（Daniel Kahneman）', '天下文化', 'book-covers/9789862415795.jpg', '諾貝爾經濟學獎得主康納曼剖析大腦決策機制的集大成之作。書中將思維劃分為直覺敏捷的「系統一」與理性客觀的「系統二」，揭示人類在面對不確定性時常見的認知偏誤與心理盲點。', '2012-10-31', '已上架'),
(9, 'BK00000009', '9789863982180', '當呼吸化為空氣', '保羅‧柯拉尼蒂（Paul Kalanithi）', '時報出版', 'book-covers/9789863982180.jpg', '一位年輕的神經外科醫師在即將完成訓練之際罹患肺癌末期，轉身成為面對死亡的病人。作者以優美深刻的文字紀錄生命的最後歷程，探討醫病關係、生命的價值以及面對死亡時的尊嚴與勇氣。', '2016-08-02', '已上架'),
(10, 'BK00000010', '9789571376912', '睡出好腦力', '馬修‧沃克（Matthew Walker）', '時報出版', 'book-covers/9789571376912.jpg', '頂尖神經科學家匯集多年研究，解開睡眠對大腦與身體機能的運作機制。書中詳細說明睡眠如何影響記憶巩固、情緒調節、免疫系統及代謝健康，並提供改善睡眠品質的實用建議', '2019-02-12', '已上架'),
(13, 'BK00000013', '9789571359816', '設計心理學', '唐納‧諾曼（Don Norman）', '時報出版', 'book-covers/9789571359816.jpg', '人因工程經典著作，主張設計應以使用者體驗為核心。作者分析日常用品常見的設計缺限，提出預期提示、限制條件與回饋機制等設計原則，協助創作者打造直覺且易用的產品。', '2014-06-16', '已上架'),
(14, 'BK00000014', '9789861342009', '點子都是偷來的', '奧斯汀‧克萊恩（Austin Kleon）', '遠流', 'book-covers/9789861342009.jpg', '本書揭示數化時代下的創意生成指南。作者指出世上沒有完全原創的點子，創作是建立在吸收、消化並重組既有元素的基礎上。書中提供10個實用觀點，鼓勵讀者跨越靈感瓶頸、勇敢分享作品。', '2013-03-01', '已上架'),
(15, 'BK00000015', '9789862132104', '我在底層的生活', '芭芭拉‧艾倫瑞克（Barbara Ehrenreich）', '遠足文化', 'book-covers/9789862132104.jpg', '知名臥底報導文學作品。作者親身進入低薪勞工市場，體驗餐飲服務員、清潔工等低薪工作，揭露即使拼命工作也難以維持基本生活的社會結構困境，引發大眾對最低工資與貧富差距的深刻反思。', '2010-12-01', '已上架'),
(16, 'BK00000016', '9789862412589', '正義：一場思辨之旅', '邁可‧桑德爾（Michael J. Sandel）', '雅言文化', 'book-covers/9789862412589.jpg', '哈佛大學熱門哲學公開課改編書籍。作者透過多元道德困境與當代社會爭議案件，引導讀者思索功利主義、個人自由與公共善等哲學流派，培養批判思考與多元對話的能力。', '2011-03-01', '已上架'),
(17, 'BK00000017', '9789573322696', '嫌疑犯X的獻身', '東野圭吾', '皇冠', 'book-covers/9789573322696.jpg', '東野圭吾獲得直木賞的代表作。天才數學家石神為了保護心愛的鄰居母女，精心設計了天衣無縫的不在場證明，與前來調查的天才物理學家湯川學展開一場邏輯與情感交織的高智商對決。', '2006-09-25', '已上架'),
(18, 'BK00000018', '9789576214240', '一個都不留', '阿嘉莎‧克莉絲蒂（Agatha Christie）', '遠流', 'book-covers/9789576214240.jpg', '孤島模式與童謠殺人開山之作。十個身懷秘密的陌生人被邀請至孤立無援的士兵島，隨後按照古老童謠歌詞接連遇害。全書氛圍壓迫，謎局設計嚴謹，為古典本格推理的巔峰巨著。', '2003-01-15', '已上架'),
(19, 'BK00000019', '9789576587207', '極限返航', '安迪‧威爾（Andy Weir）', '三采', 'book-covers/9789576587207.jpg', '《火星任務》作者的又一科幻力作。主角從昏迷中醒來，發現自己在一艘孤獨的太空船上，成為拯救太陽不再衰竭的唯一希望。全書將幽默、極致硬核的科學推理與跨物種的動人友情完美結合，節奏緊湊且高潮迭起。', '2022-02-25', '已上架'),
(20, 'BK00000020', '9789573336099', '哈利波特（3）：阿茲卡班的逃犯', 'J.K. 羅琳（J.K. Rowling）', '皇冠', 'book-covers/9789573336099.jpg', '本書為《哈利波特》系列第三集。哈利在霍格華茲迎來第三個學年，但整個魔法界正因危險囚犯天狼星‧布萊克的逃獄而陷入恐慌。據傳天狼星目標正是哈利，校園更因此引來吸取希望與快樂的「攝魂怪」駐守。隨著真相層層揭開，哈利不僅揭穿了當年的背叛秘密，更重獲溫暖的親情羈絆。', '2020-11-13', '已上架'),
(21, 'BK00000021', '9789573335276', '百年孤寂', '加布列‧賈西亞‧馬奎斯（Gabriel García Márquez）', '皇冠', 'book-covers/9789573335276.jpg', '魔幻寫實主義巔峰之作。全書敘述邦迪亞家族七代人在「馬康多」小鎮的興衰起落，將拉丁美洲的血淚歷史與神奇傳說熔為一爐，展現人類孤獨命運與歷史循環的深刻寓意。', '2020-02-10', '已上架'),
(22, 'BK00000022', '9789571324708', '挪威的森林', '村上春樹', '時報出版', 'book-covers/9789571324708.jpg', '村上春樹暢銷世界的金字塔作品。以1960年代日本青年運動為背景，透過主角渡邊的視角，細膩描摹青春歲月裡的愛戀、喪失、迷茫與成長，文字充滿詩意與淡淡憂傷。', '1997-08-15', '已上架'),
(23, 'BK00000023', '9789572666197', '葬送的芙莉蓮(1)', '山田鐘人、阿部司', '東立', 'book-covers/9789572666197.jpg', '榮獲多項大獎的後日譚奇幻漫畫。講述打倒魔王後的長壽精利魔法使芙莉蓮，在伴侶與夥伴相繼逝去後，重新踏上旅程以理解人類情感與生命意義的溫馨故事。', '2021-04-16', '已上架'),
(24, 'BK00000024', '9789861070506', '進擊的巨人(1)', '諫山創', '東立', 'book-covers/9789861070506.jpg', '絕望感與世界觀極具衝擊力的現象級漫畫。殘存的人類退居高牆之內，面對以人類為食的神秘巨人。主角艾連在經歷家園毀滅後發誓驅逐所有巨人，揭開層層殘酷真相。', '2011-01-20', '已上架'),
(25, 'BK00000025', '9789861371405', '斷捨離', '山下英子', '啟示', 'book-covers/9789861371405.jpg', '開創整理熱潮的經典生活哲學。強調「斷絕不需要的東西、捨棄多餘的廢物、脫離對物質的執著」。透過整理身邊環境，重新審視自己與物品的關係，進而達到心靈療癒與生活新生的目標。', '2011-09-16', '已上架'),
(26, 'BK00000026', '9789863595564', '北歐時間：世界第一幸福國度教會我的事', '柯琳‧狄克森（Colleen Patrick / Colleen Dickson）', '木馬文化', 'book-covers/9789863595564.jpg', '本書深入探討北歐國家高幸福感的祕密。作者透過親身觀察與生活體驗，分享丹麥與瑞典等國如何落實工作與生活的平衡、珍惜家庭時光，以及擁抱「Hygge」這種舒適簡約的生活哲學，引導讀者重新檢視自己的時間分配，打造更有品質且充滿幸福感的日常。', '2018-06-20', '已上架');

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
  `staff_account` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '處理人',
  `resolved_at` datetime DEFAULT NULL COMMENT '處理時間',
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
(10, 5),
(13, 6),
(14, 6),
(15, 7),
(16, 7),
(17, 8),
(18, 8),
(19, 9),
(20, 9),
(21, 10),
(22, 10),
(23, 11),
(24, 11),
(25, 12),
(26, 12);

-- --------------------------------------------------------

--
-- 資料表結構 `book_collection`
--

CREATE TABLE `book_collection` (
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `book_id` int(11) NOT NULL COMMENT '書籍ID',
  `r_status` enum('未閱讀','閱讀中','已完讀') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '未閱讀' COMMENT '閱讀狀態',
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

--
-- 傾印資料表的資料 `friendship`
--

INSERT INTO `friendship` (`user_id_a`, `user_id_b`, `rel_status`) VALUES
(2, 3, '已成為好友');

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

--
-- 傾印資料表的資料 `guild`
--

INSERT INTO `guild` (`guild_id`, `guild_code`, `book_id`, `guild_name`, `founded_at`, `guild_avatar`, `intro`, `approval_required`, `member_count`, `guild_status`, `guild_skin`, `announcement`) VALUES
(1, 'GD00000001', 9, '壁爐與貓2222', '2026-08-19', 'https://drive.google.com/uc?export=view&id=1cv1YISIpwmjBy23eJIyNwVj8ap_6F3xX', '深夜的鐘聲響起，這裡是愛書人的避風港。有劈啪作響的溫暖壁爐，有腳邊打盹的貓，還有手中那本尚未讀完的書。\r\n\r\n我們偏好的書籍類型不設限，但更傾向於具有療癒、探索感或引人深思的作品：\r\n奇幻與架空冒險：喜歡跟著主角踏入宏大的世界觀與神祕古老的歷史。\r\n雋永散文與心靈療癒：在文字中尋找共鳴，撫平日常的焦慮與疲憊。\r\n經典文學與各類小說：品味文字的細膩編織，探討故事背後的人性與智慧。', 0, 2, '正常', 'https://drive.google.com/uc?export=view&id=1pqrxt858OyLKkfKHXJA90a18oCQC4NiB', '公告欄內容公告欄內容公告欄內容公告欄內容公告欄內容公告欄內容公告欄內容公告欄內容公告欄內容公告欄內容公告欄內容公告欄內容公告欄內容公告欄內容公告欄內容公告欄內容'),
(2, 'GLD0000002', 1, '午夜書友會', '2026-08-26', 'guild-avatars/guild_f3b6330102dbbf.99706817.jpg', '喜歡懸疑推理與心理成長類作品的讀書小隊。', 0, 3, '正常', '', '本週六晚上八點線上聚會'),
(3, 'GLD0000003', 9, '致富心態研究社', '2026-08-26', 'guild-avatars/guild_1189a06bd121d8.12987842.jpg', '一起用理財書培養健康的金錢觀。', 0, 2, '正常', '', NULL),
(4, 'GLD0000004', 10, '歷史人文小酒館', '2026-08-26', 'guild-avatars/guild_b11ec07ed7f0ee.75382014.jpg', '每月挑一本歷史或人文書，配茶聊聊。', 0, 4, '正常', '', '歡迎新朋友加入！'),
(5, 'GLD0000005', 8, '好奇心實驗室', '2026-08-28', 'guild-avatars/guild_ec740344edff6b.80106587.jpg', '用科學的眼光看世界，一起讀懂基因、大腦與宇宙的奧秘。', 0, 1, '正常', '', NULL),
(6, 'GLD0000006', 9, '深呼吸讀書室', '2026-08-28', 'guild-avatars/guild_c13032a226f0e8.01748198.jpg', '陪你透過閱讀理解身體與心理的照顧方式，慢慢呼吸，慢慢讀。', 0, 3, '正常', '', NULL),
(7, 'GLD0000007', 13, '靈感倉庫', '2026-08-28', 'guild-avatars/guild_c029c25a29316e.67934687.jpg', '蒐集生活中的美感靈感，聊設計、聊創作、聊那些被忽略的細節。', 0, 2, '正常', '', NULL),
(8, 'GLD0000008', 16, '思辨咖啡館', '2026-08-28', 'guild-avatars/guild_a63c63d165cbad.89776396.jpg', '從書裡看見被忽略的角落，一起討論、思辨，不逃避難題。', 0, 5, '正常', '', NULL),
(9, 'GLD0000009', 23, '格子裡的異世界', '2026-08-28', 'guild-avatars/guild_734b9d4018388b.11697647.jpg', '熱血、奇幻、日常都可以，一起追連載、聊分鏡、認親愛的角色。', 0, 1, '正常', '', NULL),
(10, 'GLD0000010', 25, '慢活雜貨店', '2026-08-28', 'guild-avatars/guild_d0ac07cea03158.47174570.jpg', '斷捨離、極簡、慢生活，一起把日子過得更有品質一點。', 0, 2, '正常', '', NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `guilddiscussion`
--

CREATE TABLE `guilddiscussion` (
  `message_id` bigint(20) NOT NULL COMMENT '訊息編號',
  `segment_id` int(11) NOT NULL COMMENT '段落ID',
  `parent_message_id` bigint(20) DEFAULT NULL COMMENT '回覆的上層留言ID，NULL 代表這是主留言',
  `user_id` int(11) NOT NULL COMMENT '發言人',
  `posted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '發言時間',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '發言內容',
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '照片\r\n單張存URL，多張改JSON存路徑陣列'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='讀書公會討論區';

-- --------------------------------------------------------

--
-- 資料表結構 `guilddiscussion_like`
--

CREATE TABLE `guilddiscussion_like` (
  `message_id` bigint(20) NOT NULL COMMENT '被讚的留言ID',
  `user_id` int(11) NOT NULL COMMENT '按讚的人',
  `liked_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '按讚時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='公會討論區留言按讚';

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
(1, 1, '一般', '在會中'),
(1, 4, '一般', '在會中'),
(2, 2, '一般', '在會中'),
(2, 6, '一般', '在會中'),
(3, 1, '一般', '在會中'),
(6, 8, '一般', '在會中'),
(7, 8, '一般', '在會中'),
(7, 10, '會長', '在會中'),
(11, 6, '一般', '在會中'),
(11, 8, '會長', '在會中'),
(12, 2, '會長', '在會中'),
(13, 3, '一般', '在會中'),
(15, 3, '會長', '在會中'),
(18, 9, '會長', '在會中'),
(20, 5, '會長', '在會中'),
(23, 4, '一般', '在會中'),
(30, 2, '一般', '在會中'),
(30, 4, '會長', '在會中'),
(31, 6, '會長', '在會中'),
(31, 8, '一般', '在會中'),
(32, 7, '一般', '在會中'),
(32, 10, '一般', '在會中'),
(35, 4, '一般', '在會中'),
(35, 7, '會長', '在會中'),
(36, 8, '一般', '在會中');

-- --------------------------------------------------------

--
-- 資料表結構 `guildrecord`
--

CREATE TABLE `guildrecord` (
  `record_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL COMMENT '書籍ID',
  `guild_id` int(11) NOT NULL COMMENT '公會id',
  `record_date` date NOT NULL COMMENT '開始日期',
  `end_date` date NOT NULL COMMENT '結束日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='公會讀書紀錄';

--
-- 傾印資料表的資料 `guildrecord`
--

INSERT INTO `guildrecord` (`record_id`, `book_id`, `guild_id`, `record_date`, `end_date`) VALUES
(1, 1, 1, '2026-08-19', '2026-08-24'),
(2, 4, 1, '2026-08-24', '2026-08-24'),
(3, 1, 1, '2026-08-24', '2026-08-25'),
(4, 9, 1, '2026-08-25', '2026-08-25'),
(5, 1, 2, '2026-08-26', '2026-09-09'),
(6, 9, 3, '2026-08-26', '2026-09-09'),
(7, 10, 4, '2026-08-26', '2026-09-09'),
(8, 8, 5, '2026-08-28', '2026-09-11'),
(9, 9, 6, '2026-08-28', '2026-09-11'),
(10, 13, 7, '2026-08-28', '2026-09-11'),
(11, 16, 8, '2026-08-28', '2026-09-11'),
(12, 23, 9, '2026-08-28', '2026-09-11'),
(13, 25, 10, '2026-08-28', '2026-09-11');

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
  `avatar_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '大頭貼路徑',
  `account_status` enum('正常','停權') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '正常' COMMENT '帳號狀態',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
  `total_exp` int(11) DEFAULT '0' COMMENT '累積經驗值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='會員';

--
-- 傾印資料表的資料 `member`
--

INSERT INTO `member` (`user_id`, `member_code`, `nickname`, `report_count`, `email`, `password`, `session_token`, `bio`, `account_status`, `created_at`, `total_exp`) VALUES
(1, 'MKD00000001', '尤', 0, 'you@gmail.com', '$2y$10$lms4tCHs3SC2lIif72ZAp.PhTzywPh7NMw/uaaV2wy6JaCi00g7mm', 'c6f85c00b65bd97ea9fbbc1901391680221dfcdfb9ac0335db0e2f9e26e50237', '夜深的時候，我習慣點一盞燈，讓自己沉浸在現代散文或當代小說裡。對我來說，文字裡的細膩與留白，總能精準撫平日常的毛躁。我不求讀得多快，只求在某個句子裡看見自己的影子。如果你也喜歡在書裡尋找療癒與共鳴，歡迎隨時來聊聊。', '正常', '2026-08-18 19:18:55', 0),
(2, 'MKD00000002', '哈娜', 0, 'hannahandnicle@gmail.com', '$2y$10$hrYpsJKmSy9eXJXRr4fP1uk.tq7aa.QQu5X8rXaxvTwZ1MnQOgaIK', NULL, '世界很大，歷史很長。我習慣從歷史傳記與社會學專著裡尋找現代問題的解答。看著文明的興衰與時代的轉折，能讓人學會用更宏觀、冷靜的視角看待身邊的人事物。如果你也喜歡探討現象背後的脈絡，非常期待能與你進行有深度、有觀點的交流。', '正常', '2026-08-21 14:58:14', 0),
(3, 'MKD00000003', '尤尤', 0, '111@gmail.com', '$2y$10$P.C7YKYBKxSRD85TFw4.uuo7Hs.GRRBsWvtbpnBvIML25uVKj/sQe', NULL, '我是個熱愛閱讀的探索者。對我來說，閱讀就像是一場不必遠行的冒險，讓我在文字中遇見不同的觀點與人生。\n\n我特別喜歡閱讀現代文學與人文歷史類的書籍。小說中細緻的人性描寫與情感流動，總能帶來深刻的共鳴與療癒；而歷史與社會學相關的作品，則能拓展我的視野，帶我從宏觀的角度理解世界的變遷與多元面貌。此外，偶爾我也喜歡翻閱生活哲學與心理學，在繁忙的生活步調中尋找心靈的平靜與啟發。\n\n閱讀不僅是我汲取知識的方式，更是陪伴我思考、成長的養分。期待能與更多愛書之人交流，分享彼此在書頁間發現的美好', '正常', '2026-08-20 21:32:15', 0),
(4, 'MKD00000004', '小森', 0, 'test@test.com', '$2y$10$oqJCJoges7vZiVQiTWHL0uH9JmLFmWvKKYLvYjQ7MI/eJZQJY9ase', 'd26f3c2ab4d115f18f427bc04086198bf16376450abd1649d4709861e99007d3', '哈囉！你今天抽空離開地球了嗎？我是個重度科幻與奇幻文學迷！龐大的宇宙觀、穿越時空的設定，還有那些天馬行空的幻想，總能讓我目不轉睛。現實生活太規律，還好有書本這扇秘密門。如果你也是虛構世界的漫遊者，快來跟我認親吧！', '正常', '2026-08-26 11:14:51', 0),
(5, 'MKD00000005', '阿林', 0, 'test2@test.com', '$2y$10$lg/rDa8o7/nbxJb.q/0lDO5dLLfGeDAPepTGJe3CRotUtTKlbzsfG', '574a79836086ec34ae0da6fe84afb6bd747bb0108762132e32f45ffb9d749ada', '你好，我是個講求實踐的讀者。我的書單集中在商業思維、高效習慣與職場心理。對我來說，閱讀是投報率最高的自我升級方式。我不只看書，更重實施，喜歡把書中的理論直接拿來優化生活與工作。歡迎想一起提升思維、高效成長的朋友交流！', '正常', '2026-08-26 11:14:57', 0),
(6, 'MKD00000006', '小蘑菇', 0, 'test3@test.com', '$2y$10$oJp9e59tDZrn/3t7hhK1Regd3qutYGnWrsHqz.lFCiCx8LIdUHdo2', '7ea93db28df31a6569b04c0e9c322fd1515f577f63f7a88309e0b014b2a2e021', '我喜歡把閱讀當成一種生活提案。平時最常翻閱藝術設計、飲食文化與旅行隨筆。文字與圖片交織出的美感，總能提醒我放慢腳步，去細細品味四季的變化與一頓飯的溫度。閱讀不用很嚴肅，能讓日子變得更有質感，就是最棒的事。', '正常', '2026-08-26 11:15:03', 0),
(7, 'MKD00000007', '貓爪印', 0, 'test7@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '嗨！我大概是個閱讀界的「雜食性動物」吧！從經典文學、硬核科普到哲學思辨，只要有趣我通通照單全收。隨手翻開一本新書就像在拆盲盒，永遠不知道下一個彎角會撞見什麼新奇的觀點。如果你也不喜歡被框架限制，來跟我互相推坑吧！', '正常', '2026-08-26 11:15:48', 0),
(8, 'MKD00000008', '夜讀人', 0, 'test8@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '我是一個無法停止問「為什麼」的人。哲學思辨與社會評論是我最常涉獵的領域。我享受在冷靜的邏輯與嚴密的論述中，不斷打碎並重構自己的思維體系。閱讀是一場永無止境的思考訓練，很期待能遇到願意一起進行腦力激盪、火花四射的朋友。', '正常', '2026-08-26 11:16:33', 0),
(9, 'MKD00000009', '微光書屋', 0, 'test9@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '你好！我對這個世界的運作方式充滿敬畏。平時最愛看科普讀物與自然寫作，從微觀的細胞運作到宏觀的宇宙星辰都讓我深深著迷。書籍帶我跨越地理限制，看見大自然的奧秘。希望透過我的分享，能帶你用全新且有趣的視角重新看看這個世界。', '正常', '2026-08-26 11:17:18', 0),
(10, 'MKD00000010', '阿柚', 0, 'test10@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '在這個快節奏的時代，我依然偏愛那些經受住時間淘洗的古典文學與歷史名著。字裡行間濃縮的是跨越世代的人性智慧與沉澱。靜心翻閱經典，就像在與千百年前的智者進行一場跨越時空的深度對話。如果你也鍾情於沉穩深邃的文字，很高興認識你。', '正常', '2026-08-26 11:18:03', 0),
(11, 'MKD00000011', '晴天雨傘', 0, 'test11@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '嘿，準備好一起解謎了嗎？我是個推理小說重度愛好者，最迷戀完美的伏筆與意想不到的翻轉。閱讀對我來說，是一場跟作者比拼腦力的智力博弈。每次看到令人拍案叫絕的局，都爽快到不行！如果你也是燒腦控，快跟我分享你的私房書單！', '正常', '2026-08-26 11:18:48', 0),
(12, 'MKD00000012', '一頁詩', 0, 'test12@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '夜晚是我留給詩集與微型散文的時間。我喜歡詩句中那種恰到好處的留白與隱喻，短短幾行字，就能勾勒出無盡的想像與情緒。閱讀是我的靈感來源，也是我捕捉生活微光的方式。希望這些輕盈的文字，也能輕輕觸動你的心。', '正常', '2026-08-26 11:19:33', 0),
(13, 'MKD00000013', '落雨', 0, 'test13@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '你好！比起純文字，我更常流連於展覽畫冊、設計思維與建築視覺類的書籍。閱讀對我來說，不只是汲取資訊，更是視覺洗禮與創意的再激發。我喜歡探究創作者背後的概念與審美哲學，希望能與同樣熱愛美學與設計的朋友碰撞出火花。', '正常', '2026-08-26 11:20:18', 0),
(14, 'MKD00000014', '老派書蟲', 0, 'test14@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '嗨，我一直相信「你讀什麼，就會成為什麼樣的人」。我的閱讀焦點永遠放在習慣養成、心智模型與時間管理上。對我而言，把書讀完只是第一步，建立可執行的方法論才是重點。如果你也致力於自我迭代、追求更好的自己，歡迎一起交流學習！', '正常', '2026-08-26 11:21:03', 0),
(15, 'MKD00000015', '山羊先生', 0, 'test15@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '我是一個習慣關注社會邊緣與時代議題的讀者。紀實文學、報導攝影與人權著作是我的閱讀核心。文字提醒我保持警醒與同理，去看見那些容易被忽略的角落與多元聲音。期待透過閱讀的分享，能為這個社會多凝聚一點理解與溫暖的力量。', '正常', '2026-08-26 11:21:48', 0),
(16, 'MKD00000016', '咖啡因過量', 0, 'test16@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '哈囉！先說好，我讀書可不想太累！圖文創作、搞笑隨筆跟輕鬆漫畫是我的最愛。生活已經夠辛苦了，看書就是要讓人開心、舒壓呀！如果你也想在繁忙的日常裡找個能讓你笑出聲的閱讀選擇，跟我聊天就對了，絕對沒有壓力！', '正常', '2026-08-26 11:22:33', 0),
(17, 'MKD00000017', '迷路的貓', 0, 'test17@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '我對「人」的故事毫無抵抗力。最喜歡讀名人傳記與深度訪談，看著別人如何在關鍵時刻做選擇、如何走出低谷，總能給我極大的震撼與勇氣。讀一本好的傳記，就像用幾個小時體驗了別人精彩的一生。迫不及待想跟你分享這些熱血故事！', '正常', '2026-08-26 11:23:18', 0),
(18, 'MKD00000018', '藍莓奶昔', 0, 'test18@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '你好，我是個關注經濟運作與市場心理的讀者。比起飆股明牌，我更喜歡探討金錢心理學與決策框架。閱讀幫助我在資訊爆炸且情緒化的市場中，建立獨立思考的底氣並保持清醒。歡迎對商業邏輯與決策思維有興趣的朋友一起探討。', '正常', '2026-08-26 11:24:03', 0),
(19, 'MKD00000019', '風鈴草', 0, 'test19@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '即使雙腳停在原地，我的靈魂也一直在路上！我超喜歡翻譯文學、異國文化觀察與人文地理類的書籍。透過文字穿梭在不同的城市與文化之間，體驗完全不同的生活方式，真的太迷人了。如果你也有一顆渴望探索世界的心，歡迎跟我交流！', '正常', '2026-08-26 11:24:48', 0),
(20, 'MKD00000020', '深夜食客', 0, 'test20@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '你好！我對科技如何重塑人類社會充滿好奇。我的書單多集中在人工智慧、未來趨勢與數位文明思辨。閱讀對我來說，是一張通往未來的預告券，讓我能在技術迭代的浪潮中保持敏銳與思考。如果你也喜歡討論未來的各種可能性，非常期待跟你聊聊！', '正常', '2026-08-26 11:25:33', 0),
(21, 'MKD00000021', '星塵旅人', 0, 'test21@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '在這浮躁的世界裡，閱讀是我讓心靈歸零的儀式。我偏好研讀東方哲學、正念冥想與身心靈療癒的書籍。文字能帶領我收攝心神，找回專注與當下的平靜。希望能將這份靜謐與定力分享給你，陪你在忙碌的生活中找到一處心靈的棲息地。', '正常', '2026-08-26 11:26:18', 0),
(22, 'MKD00000022', '橘子汽水', 0, 'test22@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '我不喜歡太繁複的包裝，看書也是。最常讀極簡生活、斷捨離與思維整理類的書籍。閱讀是我幫生活與大腦「清空快取」的方式，用簡練的文字剔除不必要的雜訊。如果你也偏好乾淨、實用且直擊重點的內容，這裡應該很適合你。', '正常', '2026-08-26 11:27:03', 0),
(23, 'MKD00000023', '阿光', 0, 'test23@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '你好。我習慣在字裏行間捕捉那些被忽視的蛛絲馬跡。比起熱血冒險，我更偏好古典推理、法醫學與犯罪心理分析。閱讀就像是一場嚴謹的偵查過程，享受邏輯嚴密推導、最終真相大白的瞬間。如果你也注重細節與理性邏輯，歡迎交流你的推理解析。', '正常', '2026-08-26 11:27:48', 0),
(24, 'MKD00000024', '麥子熟了', 0, 'test24@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '哈囉！我是個喜歡用味蕾與文字認識世界的讀者。飲食文學、食材風土與文化采風是我的最愛。食物不只是填飽肚子，背後更有深厚的情感與在地故事。閱讀這些文字總是讓我食慾大開，也更懂得珍惜一碗飯的溫度。歡迎同為吃貨的你來交流！', '正常', '2026-08-26 11:28:33', 0),
(25, 'MKD00000025', '影子讀者', 0, 'test25@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, '嗨！我的靈魂有一半在山海裡。平時最喜歡看山岳文學、極地探險與戶外紀實。文字能帶我爬上終年積雪的高山，或是潛入未知的大洋，感受人類在自然面前的渺小與堅韌。如果你也熱愛大自然、喜歡聽那些挑戰極限的故事，快來跟我分享！', '正常', '2026-08-26 11:29:18', 0),
(26, 'MKD00000026', '一顆蘋果', 0, 'test26@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, NULL, '正常', '2026-08-26 11:30:03', 0),
(27, 'MKD00000027', '芋圓控', 0, 'test27@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, NULL, '正常', '2026-08-26 11:30:48', 0),
(28, 'MKD00000028', '貓野', 0, 'test28@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, NULL, '正常', '2026-08-26 11:31:33', 0),
(29, 'MKD00000029', '小雨傘', 0, 'test29@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, NULL, '正常', '2026-08-26 11:32:18', 0),
(30, 'MKD00000030', '木棉花開', 0, 'test30@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, NULL, '正常', '2026-08-26 11:33:03', 0),
(31, 'MKD00000031', '阿鹿', 0, 'test31@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, NULL, '正常', '2026-08-26 11:33:48', 0),
(32, 'MKD00000032', '灰灰', 0, 'test32@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, NULL, '正常', '2026-08-26 11:34:33', 0),
(33, 'MKD00000033', '綿羊先生', 0, 'test33@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, NULL, '正常', '2026-08-26 11:35:18', 0),
(34, 'MKD00000034', '半夏', 0, 'test34@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, NULL, '正常', '2026-08-26 11:36:03', 0),
(35, 'MKD00000035', '阿嵐', 0, 'test35@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, NULL, '正常', '2026-08-26 11:36:48', 0),
(36, 'MKD00000036', '慢半拍', 0, 'test36@test.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, NULL, '正常', '2026-08-26 11:37:33', 0),
(37, 'MKD00000037', '哈哈我測試員啦', 0, '123456@gmail.com', '$2y$10$ZOIbOH40SeBxW3bP3a6pF.Lp8JNGKf4SYlwF.qxqX14qP6eyioVhe', NULL, NULL, '正常', '2026-08-30 00:00:00', 0);

-- --------------------------------------------------------

--
-- 資料表結構 `member_book_categorys`
--

CREATE TABLE `member_book_categorys` (
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `bcg_id` int(11) NOT NULL COMMENT '書籍類別ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='會員喜好書籍類別';

--
-- 傾印資料表的資料 `member_book_categorys`
--

INSERT INTO `member_book_categorys` (`user_id`, `bcg_id`) VALUES
(3, 1),
(4, 1),
(8, 1),
(12, 1),
(13, 1),
(21, 1),
(31, 1),
(7, 2),
(9, 2),
(10, 2),
(11, 2),
(13, 2),
(14, 2),
(15, 2),
(18, 2),
(20, 2),
(34, 2),
(3, 3),
(9, 3),
(15, 3),
(22, 3),
(24, 3),
(28, 3),
(29, 3),
(32, 3),
(33, 3),
(36, 3),
(3, 4),
(5, 4),
(13, 4),
(14, 4),
(16, 4),
(18, 4),
(19, 4),
(25, 4),
(31, 4),
(7, 5),
(15, 5),
(17, 5),
(20, 5),
(22, 5),
(26, 5),
(28, 5),
(29, 5),
(33, 5),
(3, 6),
(10, 6),
(16, 6),
(17, 6),
(19, 6),
(23, 6),
(24, 6),
(25, 6),
(26, 6),
(33, 6),
(35, 6),
(3, 7),
(9, 7),
(13, 7),
(23, 7),
(24, 7),
(25, 7),
(33, 7),
(34, 7),
(35, 7),
(36, 7),
(3, 8),
(11, 8),
(21, 8),
(28, 8),
(3, 9),
(5, 9),
(11, 9),
(12, 9),
(14, 9),
(17, 9),
(18, 9),
(27, 9),
(29, 9),
(31, 9),
(3, 10),
(4, 10),
(7, 10),
(8, 10),
(12, 10),
(19, 10),
(20, 10),
(24, 10),
(26, 10),
(27, 10),
(30, 10),
(31, 10),
(32, 10),
(36, 10),
(3, 11),
(6, 11),
(10, 11),
(15, 11),
(27, 11),
(30, 11),
(32, 11),
(34, 11),
(35, 11),
(11, 12),
(16, 12),
(26, 12),
(29, 12),
(37, 1),
(37, 5),
(37, 9);

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
  `segment_id` int(11) NOT NULL,
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
(3, 1, 5, 6, '2026-08-30', 3),
(5, 3, 1, 2, '2026-08-25', 1),
(6, 5, 1, 10, '2026-09-09', 1),
(7, 6, 1, 5, '2026-09-02', 1),
(8, 7, 1, 10, '2026-09-09', 1),
(9, 8, 1, 10, '2026-09-11', 1),
(10, 9, 1, 4, '2026-09-01', 1),
(11, 10, 1, 10, '2026-09-11', 1),
(12, 11, 1, 10, '2026-09-11', 1),
(13, 12, 1, 6, '2026-09-04', 1),
(14, 13, 1, 10, '2026-09-11', 1),
(15, 6, 6, 10, '2026-09-09', 2),
(16, 9, 5, 7, '2026-09-06', 2),
(17, 9, 8, 10, '2026-09-11', 3),
(18, 12, 7, 10, '2026-09-11', 2);

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
('shuyun', '書芸', '$2y$10$0Aw5t3lq51D4l8Tg5XRFaOGk.aMsGoBzAMIcZGSOVgGCzvFLhQdEK', '5d4a3bbae1230d9afd5b28637c87be1fd8092ee513304a6378130bb1aaccdffa');

-- --------------------------------------------------------

--
-- 資料表結構 `user_achieve`
--

CREATE TABLE `user_achieve` (
  `user_id` int(11) NOT NULL COMMENT '使用者ID',
  `achieve_id` char(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '成就類別ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='使用者成就';

--
-- 傾印資料表的資料 `user_achieve`
--

INSERT INTO `user_achieve` (`user_id`, `achieve_id`) VALUES
(37, 'a01'),
(37, 'a02'),
(37, 'a03');

-- --------------------------------------------------------

--
-- 資料表結構 `user_appear`
--

CREATE TABLE `user_appear` (
  `user_id` int(11) NOT NULL COMMENT '使用者ID(擁有者)',
  `appear_id` char(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '外觀類別ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='使用者外觀';

--
-- 傾印資料表的資料 `user_appear`
--

INSERT INTO `user_appear` (`user_id`, `appear_id`) VALUES
(10, 'fe1'),
(18, 'fe1'),
(28, 'fe1'),
(35, 'fe1'),
(36, 'fe1'),
(9, 'fe2'),
(12, 'fe2'),
(14, 'fe2'),
(27, 'fe2'),
(29, 'fe2'),
(30, 'fe2'),
(31, 'fe2'),
(32, 'fe2'),
(4, 'fe3'),
(6, 'fe3'),
(7, 'fe3'),
(8, 'fe3'),
(11, 'fe3'),
(17, 'fe3'),
(21, 'fe3'),
(22, 'fe3'),
(7, 'fh1'),
(8, 'fh1'),
(10, 'fh1'),
(28, 'fh1'),
(30, 'fh1'),
(36, 'fh1'),
(11, 'fh2'),
(12, 'fh2'),
(17, 'fh2'),
(27, 'fh2'),
(31, 'fh2'),
(32, 'fh2'),
(35, 'fh2'),
(4, 'fh3'),
(6, 'fh3'),
(9, 'fh3'),
(14, 'fh3'),
(18, 'fh3'),
(21, 'fh3'),
(22, 'fh3'),
(29, 'fh3'),
(7, 'fs1'),
(9, 'fs1'),
(11, 'fs1'),
(12, 'fs1'),
(21, 'fs1'),
(22, 'fs1'),
(27, 'fs1'),
(30, 'fs1'),
(4, 'fs2'),
(6, 'fs2'),
(10, 'fs2'),
(17, 'fs2'),
(29, 'fs2'),
(32, 'fs2'),
(36, 'fs2'),
(8, 'fs3'),
(14, 'fs3'),
(18, 'fs3'),
(28, 'fs3'),
(31, 'fs3'),
(35, 'fs3'),
(4, 'g01'),
(6, 'g01'),
(7, 'g01'),
(8, 'g01'),
(9, 'g01'),
(10, 'g01'),
(11, 'g01'),
(12, 'g01'),
(14, 'g01'),
(17, 'g01'),
(18, 'g01'),
(21, 'g01'),
(22, 'g01'),
(27, 'g01'),
(28, 'g01'),
(29, 'g01'),
(30, 'g01'),
(31, 'g01'),
(32, 'g01'),
(35, 'g01'),
(36, 'g01'),
(3, 'g02'),
(5, 'g02'),
(13, 'g02'),
(15, 'g02'),
(16, 'g02'),
(19, 'g02'),
(20, 'g02'),
(23, 'g02'),
(24, 'g02'),
(25, 'g02'),
(26, 'g02'),
(33, 'g02'),
(34, 'g02'),
(13, 'me1'),
(15, 'me1'),
(19, 'me1'),
(20, 'me1'),
(24, 'me1'),
(25, 'me1'),
(3, 'me2'),
(23, 'me2'),
(26, 'me2'),
(5, 'me3'),
(16, 'me3'),
(33, 'me3'),
(34, 'me3'),
(3, 'mh1'),
(13, 'mh1'),
(19, 'mh1'),
(33, 'mh1'),
(34, 'mh1'),
(20, 'mh2'),
(23, 'mh2'),
(25, 'mh2'),
(5, 'mh3'),
(15, 'mh3'),
(16, 'mh3'),
(24, 'mh3'),
(26, 'mh3'),
(3, 'ms1'),
(19, 'ms1'),
(20, 'ms1'),
(24, 'ms1'),
(26, 'ms1'),
(34, 'ms1'),
(5, 'ms2'),
(13, 'ms2'),
(15, 'ms2'),
(23, 'ms2'),
(25, 'ms2'),
(33, 'ms2'),
(16, 'ms3'),
(37, 'g01'),
(37, 'fh3'),
(37, 'fe1'),
(37, 'fs2');

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
  ADD KEY `user_id` (`user_id`),
  ADD KEY `staff_account` (`staff_account`);

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
  ADD KEY `fk_guilddiscussion_user` (`user_id`),
  ADD KEY `fk_guilddiscussion_parent` (`parent_message_id`);

--
-- 資料表索引 `guilddiscussion_like`
--
ALTER TABLE `guilddiscussion_like`
  ADD PRIMARY KEY (`message_id`,`user_id`),
  ADD KEY `fk_guilddiscussion_like_user` (`user_id`);

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
  ADD UNIQUE KEY `uk_email` (`email`),
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
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '書籍ID', AUTO_INCREMENT=27;

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
  MODIFY `guild_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公會id', AUTO_INCREMENT=11;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `guilddiscussion`
--
ALTER TABLE `guilddiscussion`
  MODIFY `message_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '訊息編號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `guildrecord`
--
ALTER TABLE `guildrecord`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `login_log`
--
ALTER TABLE `login_log`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'log編號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `member`
--
ALTER TABLE `member`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '使用者ID', AUTO_INCREMENT=38;

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
  MODIFY `segment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `book_application_form`
--
ALTER TABLE `book_application_form`
  ADD CONSTRAINT `book_application_form_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`),
  ADD CONSTRAINT `book_application_form_ibfk_2` FOREIGN KEY (`staff_account`) REFERENCES `staff` (`staff_account`);

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
  ADD CONSTRAINT `fk_guilddiscussion_user` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`),
  ADD CONSTRAINT `fk_guilddiscussion_parent` FOREIGN KEY (`parent_message_id`) REFERENCES `guilddiscussion` (`message_id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `guilddiscussion_like`
--
ALTER TABLE `guilddiscussion_like`
  ADD CONSTRAINT `fk_guilddiscussion_like_message` FOREIGN KEY (`message_id`) REFERENCES `guilddiscussion` (`message_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_guilddiscussion_like_user` FOREIGN KEY (`user_id`) REFERENCES `member` (`user_id`) ON DELETE CASCADE;

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
