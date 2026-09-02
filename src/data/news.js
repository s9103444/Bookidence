// 最新消息頁（NewsView）用的假資料。
//
// 官方消息：資料庫目前完全沒有對應的資料表，內容是純手寫的，之後有後端 API
// 再把 officialNews 換成打 API 的函式。
//
// 讀書公會推薦／好書推薦：內容不是虛構的，是直接照抄本機資料庫（ckd101_g1）
// 裡真實存在的公會與書籍（book_id / guild_id 都對得上），只是這裡先手動列出來，
// 沒有真的打 API。之後要換成真資料只要把這兩個陣列換成 fetch guild_list.php /
// 書籍列表 API 的結果即可，元件不用改。封面／頭像圖檔是資料庫裡存的真實檔名，
// 所以直接組 API_STATIC 路徑就能顯示，跟其他頁面讀真實資料時的做法一致。
// member_count 是資料庫當下的真實人數（測試站資料還不多，人數看起來會比較少，
// 不是刻意調低）。

import { API_STATIC } from '@/common/api';

const uploadUrl = (path) => `${API_STATIC}/uploads/${path}`;

export const officialNews = [
  {
    id: 1,
    tag: '系統更新',
    icon: 'bell',
    title: 'Bookidence 全新改版上線！',
    summary: '我們優化了介面設計與閱讀體驗，讓你更方便找到喜歡的書與公會，快來探索新功能吧！',
    date: '2026/07/01',
    views: 1245,
  },
  {
    id: 2,
    tag: '活動公告',
    icon: 'sparkles',
    title: '夏日閱讀挑戰開跑！',
    summary: '完成閱讀任務就有機會獲得限定徽章與抽獎資格，快來參加挑戰吧！',
    date: '2026/06/18',
    views: 1560,
  },
  {
    id: 3,
    tag: '功能上線',
    icon: 'article',
    title: '全新書單分享功能上線',
    summary: '現在可以建立專屬書單並分享給朋友，一起發現更多好書！',
    date: '2026/06/10',
    views: 892,
  },
  {
    id: 4,
    tag: '系統更新',
    icon: 'bell',
    title: '新增深色模式',
    summary: '夜間閱讀更護眼，到「使用者設定」裡就能切換，喜歡哪一種都不用勉強自己。',
    date: '2026/05/22',
    views: 634,
  },
];

// region 資料庫沒有這個欄位，跟 GuildListView.vue 用同一招：
// 用 guildId 固定對應到一個地區當裝飾用，不是真資料。
const REGIONS = ['北部', '中部', '南部', '東部', '線上'];
function getGuildRegion(guildId) {
  return REGIONS[guildId % REGIONS.length];
}

export const recommendedGuilds = [
  {
    guildId: 2,
    avatar: uploadUrl('guild-avatars/guild_f3b6330102dbbf.99706817.jpg'),
    name: '午夜書友會',
    description: '喜歡懸疑推理與心理成長類作品的讀書小隊。',
    currentBook: '原子習慣',
    memberCount: 6,
    region: getGuildRegion(2),
    tags: ['心理成長'],
  },
  {
    guildId: 4,
    avatar: uploadUrl('guild-avatars/guild_b11ec07ed7f0ee.75382014.jpg'),
    name: '歷史人文小酒館',
    description: '每月挑一本歷史或人文書，配茶聊聊。',
    currentBook: '睡出好腦力',
    memberCount: 7,
    region: getGuildRegion(4),
    tags: ['醫療生活'],
  },
  {
    guildId: 6,
    avatar: uploadUrl('guild-avatars/guild_c13032a226f0e8.01748198.jpg'),
    name: '深呼吸讀書室',
    description: '陪你透過閱讀理解身體與心理的照顧方式，慢慢呼吸，慢慢讀。',
    currentBook: '當呼吸化為空氣',
    memberCount: 6,
    region: getGuildRegion(6),
    tags: ['醫療生活'],
  },
  {
    guildId: 8,
    avatar: uploadUrl('guild-avatars/guild_a63c63d165cbad.89776396.jpg'),
    name: '思辨咖啡館',
    description: '從書裡看見被忽略的角落，一起討論、思辨，不逃避難題。',
    currentBook: '正義：一場思辨之旅',
    memberCount: 7,
    region: getGuildRegion(8),
    tags: ['社會議題'],
  },
];

export const recommendedBooks = [
  {
    id: 1,
    cover: uploadUrl('book-covers/9789861755267.jpg'),
    title: '原子習慣',
    author: '詹姆斯‧克利爾（James Clear）',
    summary: '本書說明微小改變如何帶來巨大躍進。作者提出建立好習慣與戒除壞習慣的四階法則——讓提示顯而易見、讓渴望有吸引力、讓行動輕而易舉、讓獎賞令人滿足，幫助讀者透過系統化方式調整行為，達成持續性的自我成長。',
    categories: ['心理成長'],
  },
  {
    id: 2,
    cover: uploadUrl('book-covers/9789861753805.jpg'),
    title: '被討厭的勇氣',
    author: '岸見一郎、古賀史健',
    summary: '本書以年輕人與哲學家的對話形式，深入淺出地介紹阿德勒心理學。核心觀點強調「人的煩惱皆來自於人際關係」，並提出「課題分離」概念，鼓勵讀者擺脫他人期待的枷鎖，勇敢面對當下，獲得真正的自由與幸福。',
    categories: ['心理成長'],
  },
  {
    id: 3,
    cover: uploadUrl('book-covers/9789578423220.jpg'),
    title: '致富心態',
    author: '摩根‧豪瑟（Morgan Housel）',
    summary: '作者透過19個簡短故事，剖析人們處理金錢時的心理與行為模式。本書強調理財成功與否往往不取決於個人智商或專業知識，而在於如何控制情緒與面對風險，協助讀者建立健康、可持續的財務觀念。',
    categories: ['商業理財'],
  },
  {
    id: 26,
    cover: uploadUrl('book-covers/9789863595564.jpg'),
    title: '北歐時間：世界第一幸福國度教會我的事',
    author: '柯琳‧狄克森（Colleen Patrick / Colleen Dickson）',
    summary: '本書深入探討北歐國家高幸福感的祕密。作者透過親身觀察與生活體驗，分享丹麥與瑞典等國如何落實工作與生活的平衡、珍惜家庭時光，以及擁抱「Hygge」這種舒適簡約的生活哲學，引導讀者重新檢視自己的時間分配，打造更有品質且充滿幸福感的日常。',
    categories: ['生活風格'],
  },
];
