// 搜索圖書、搜尋結果、書籍詳情三頁共用的假書籍資料。
// 三頁都從這裡拿，卡片點進詳情頁才會是同一本書。
// 之後接後端 API，就把這個檔案換成打 API 的函式，三頁不用改。
//
// 欄位分兩種用途：
//   summary     一句話簡介，卡片上顯示
//   description 分段的完整介紹，詳情頁顯示

import twilightCover from '@/assets/images/twilight-cover.png';
import littlePrinceCover from '@/assets/images/little-prince-cover.png';
import peterCover from '@/assets/images/peter-cover.png';
import nordicTimeCover from '@/assets/images/nordic-time-cover.png';

import guildAvatar from '@/assets/images/guild/guildAvatar.png';
import girlAvatar from '@/assets/images/guild/girl.png';
import boyAvatar from '@/assets/images/guild/boy.png';

// 心得的 id 用「書號 + 流水號」編（暮光之城是 1xx、小王子是 2xx…），
// 全站不會重複。按讚和檢舉紀錄存在 localStorage 裡是認 id 的，
// 重複的話換一本書會出現「我沒按過卻已經是紅心」。
//
// createdAt 給排序用，date 是畫面上顯示的字串。
// userCode 是給人看的會員編號（檢舉彈窗要顯示），不是資料庫的 user_id，
// 接 API 時兩個都要有。
//
// 每本書的心得順序都刻意打亂，三種排序（最新／所有／最高）結果才會互不相同，
// 壞掉時看得出來。

const baseBooks = [
  {
    id: 1,
    title: '暮光之城',
    author: '史蒂芬妮．梅爾',
    translator: '瞿秀蕙',
    publisher: '尖端出版',
    publishDate: '2011/06/10',
    isbn: '978-9571043470',
    cover: twilightCover,
    categories: ['文學小說', '奇幻愛情'],
    reviewCount: 412,
    collectCount: 1265,
    summary: '一場跨越人類與吸血鬼界線的愛情，讓貝拉在小鎮福克斯的雨季裡，走進了一個她再也無法回頭的世界。',
    description: [
      '占據國內外各大排行榜長達三年的時間，《暮光之城》、《新月》、《蝕》、《破曉》與《布莉的重生》融合了浪漫、驚險與神祕，史蒂芬妮．梅爾描繪了一系列人類、吸血鬼與狼人之間令人動容的感情糾葛，及各種族間令人難以忘懷的對決。',
      '故事從貝拉搬到終年潮濕多雨的小鎮福克斯開始。她在生物課上遇見了愛德華——一個俊美、蒼白、對她充滿敵意卻又忍不住靠近的少年。當貝拉發現他的真實身分時，她必須決定：是要退回安全的日常，還是踏進一個永遠不會老去、卻也永遠有危險的世界。',
    ],
    reviews: [
      { id: 103, username: 'rainy_forks', userCode: 'BKD00512', avatar: girlAvatar, date: 'Jun 22, 2026 9:40 PM', createdAt: '2026-06-22T21:40:00', likeCount: 18, content: '福克斯那種一年到頭都在下雨的氛圍寫得很好，讀的時候真的會覺得潮濕。劇情本身很青春，但氣氛營造是這本最強的地方。' },
      { id: 101, username: 'Bella_0913', userCode: 'BKD00733', avatar: guildAvatar, date: 'Jul 04, 2026 2:15 PM', createdAt: '2026-07-04T14:15:00', likeCount: 52, content: '國中的時候讀過一次，隔了十年再讀感受完全不一樣。以前覺得愛德華很浪漫，現在只覺得他管太多。有趣的是這樣反而更好看了。' },
      { id: 102, username: 'nightshift_k', userCode: 'BKD00298', avatar: boyAvatar, date: 'Jun 30, 2026 11:02 PM', createdAt: '2026-06-30T23:02:00', likeCount: 7, content: '棒球那一場戲是全書我最喜歡的段落，把超能力寫得很日常、很好玩。可惜後面又回到感情線了。' },
      { id: 104, username: 'moon_reader', userCode: 'BKD00641', avatar: girlAvatar, date: 'Jun 08, 2026 4:28 PM', createdAt: '2026-06-08T16:28:00', likeCount: 31, content: '不需要用文學標準去要求它，它就是一本讓你熬夜翻完的書。翻完之後心裡空空的那種感覺，很久沒有了。' },
    ],
  },
  {
    id: 2,
    title: '小王子',
    author: '安東尼．聖修伯里',
    translator: '張譯',
    publisher: '二魚文化',
    publishDate: '2015/03/02',
    isbn: '978-9865813581',
    cover: littlePrinceCover,
    categories: ['文學小說', '經典文學'],
    reviewCount: 328,
    collectCount: 2104,
    summary: '一位飛行員在撒哈拉沙漠遇見了來自小行星的小王子，也重新遇見了自己心裡那個被弄丟的大人。',
    description: [
      '一位飛行員迫降在撒哈拉沙漠，在幾乎斷水的第八天，遇見了一個從小行星 B612 來的金髮男孩。男孩開口的第一句話不是求救，而是：「請幫我畫一隻綿羊。」',
      '小王子走過六顆星球，見過只在乎命令的國王、只想被讚美的虛榮者、忙著計算星星的商人。他發現大人們都活得很奇怪。直到在地球上遇見狐狸，他才明白：真正重要的東西，用眼睛是看不見的，只有用心才看得清楚。',
    ],
    reviews: [
      { id: 202, username: 'fox_and_rose', userCode: 'BKD00154', avatar: girlAvatar, date: 'Jul 06, 2026 10:20 AM', createdAt: '2026-07-06T10:20:00', likeCount: 88, content: '狐狸那一章我每次讀都要停下來。「你為你的玫瑰花費的時間，讓你的玫瑰變得如此重要」——這句話大概可以解釋人生一半的事情。' },
      { id: 204, username: 'desert_pilot', userCode: 'BKD00877', avatar: boyAvatar, date: 'May 28, 2026 8:05 PM', createdAt: '2026-05-28T20:05:00', likeCount: 12, content: '小時候讀覺得是童話，長大讀才發現作者在罵大人。那個忙著數星星的商人，根本就是在講加班的我。' },
      { id: 201, username: 'b612_note', userCode: 'BKD00420', avatar: guildAvatar, date: 'Jun 17, 2026 3:33 PM', createdAt: '2026-06-17T15:33:00', likeCount: 40, content: '很薄的一本，但不適合一次讀完。建議一天讀一顆星球，讀完想一下自己像哪一個。我像那個點燈的人。' },
      { id: 203, username: 'quiet_sheep', userCode: 'BKD00069', avatar: girlAvatar, date: 'Jun 03, 2026 7:51 AM', createdAt: '2026-06-03T07:51:00', likeCount: 25, content: '結尾其實很傷心，但作者處理得很輕。第一次讀完沒反應過來，闔上書過了一分鐘才難過起來。' },
    ],
  },
  {
    id: 3,
    title: '彼得潘',
    author: 'J.M. 巴利',
    translator: '陳澄和',
    publisher: '國語日報',
    publishDate: '2009/11/20',
    isbn: '978-9577515421',
    cover: peterCover,
    categories: ['兒童文學', '經典文學'],
    reviewCount: 156,
    collectCount: 743,
    summary: '永遠長不大的男孩帶著溫蒂三兄妹飛向夢幻島，那裡有美人魚、印地安人，和一隻吞了時鐘的鱷魚。',
    description: [
      '某個夜裡，一個不會長大的男孩從窗戶飛進了達令家的育兒室，來找他掉落的影子。他叫彼得潘，他邀請溫蒂和兩個弟弟一起飛往夢幻島。',
      '在夢幻島上，有迷失的男孩、印地安公主虎蓮、美人魚的礁湖，還有斷了一隻手、被鱷魚追著跑的虎克船長。這是一個關於冒險的故事，也是一個關於「長大」的故事——溫蒂終究要回家，而彼得潘選擇留下。',
    ],
    reviews: [
      { id: 301, username: 'tick_tock_croc', userCode: 'BKD00335', avatar: boyAvatar, date: 'Jun 26, 2026 6:12 PM', createdAt: '2026-06-26T18:12:00', likeCount: 33, content: '虎克船長怕鱷魚肚子裡的時鐘聲，其實就是怕時間。這個設定小時候完全沒看懂，現在覺得有點恐怖。' },
      { id: 303, username: 'wendy_darling', userCode: 'BKD00901', avatar: girlAvatar, date: 'Jul 02, 2026 1:47 PM', createdAt: '2026-07-02T13:47:00', likeCount: 61, content: '最後溫蒂選擇回家、選擇長大，而彼得潘連她的名字都會忘記。這不是快樂結局，是一個很溫柔的告別。' },
      { id: 302, username: 'second_star', userCode: 'BKD00218', avatar: guildAvatar, date: 'Jun 11, 2026 9:25 AM', createdAt: '2026-06-11T09:25:00', likeCount: 9, content: '唸給小孩聽很好用，飛行那一段他們會很興奮。不過原著其實比迪士尼版本暗一點，有些段落我會跳過。' },
      { id: 304, username: 'lost_boys_no7', userCode: 'BKD00580', avatar: boyAvatar, date: 'May 30, 2026 10:38 PM', createdAt: '2026-05-30T22:38:00', likeCount: 21, content: '彼得潘不是主角，他比較像一個現象。真正在成長的是溫蒂，整本書其實是她的故事。' },
    ],
  },
  {
    id: 4,
    title: '北歐時間：世界第一幸福國度教會我的事',
    author: '日暮 Inko',
    translator: '林美琪',
    publisher: '幸福文化',
    publishDate: '2025/10/29',
    isbn: '978-6267427088',
    cover: nordicTimeCover,
    categories: ['生活風格', '心靈成長'],
    reviewCount: 200,
    collectCount: 328,
    summary: '走進全球幸福指數最高的國度，學會在高效率與慢節奏之間，找回屬於自己的時間感。',
    description: [
      '在充斥著高效與忙碌的現代生活中，我們是否遺失了生活的本質？《北歐時間：世界第一幸福國度教會我的事》帶領讀者走進全球幸福指數最高的國度，探索北歐人不慌不忙的「時間美學」。',
      '本書分享了北歐人如何在高效率與慢節奏之間，取得工作與生活的完美平衡。從擁抱自然的放鬆、珍惜與家人相處的Hygge（舒適溫馨），到凡事追求剛剛好的Lagom（中庸哲學）。這不僅是一本文化觀察，更是一份生活提案，教我們在喧囂的日常中放慢腳步，重新找回屬於自己的幸福步調。',
    ],
    reviews: [
      { id: 403, username: 'reading_cat', userCode: 'BKD00312', avatar: guildAvatar, date: 'Jun 20, 2026 8:03 PM', createdAt: '2026-06-20T20:03:00', likeCount: 12, content: '文筆很舒服，配圖也很療癒。比較可惜的是後半段有些論點重複，如果能再多一點實際案例會更好。' },
      { id: 401, username: 'Lora2412545', userCode: 'BKD00246', avatar: girlAvatar, date: 'Jul 01, 2026 3:41 PM', createdAt: '2026-07-01T15:41:00', likeCount: 20, content: '這本書溫柔地敲醒了被時間追趕的我們。作者透過北歐的「放慢」哲學，讓人重新思考工作與生活的本質。它不只是文化觀察，更是一劑實用的心靈解藥，提醒我們：幸福不在於填滿日程，而是在剛剛好的日常裡，留出心靈的空白。' },
      { id: 402, username: 'Kevin_0912', userCode: 'BKD00187', avatar: boyAvatar, date: 'Jun 28, 2026 9:12 AM', createdAt: '2026-06-28T09:12:00', likeCount: 45, content: '讀完最大的感想是「原來慢下來不是懶惰」。書中提到的 Lagom 觀念徹底改變我安排一天的方式，現在會刻意留白，反而更有效率。' },
      { id: 404, username: 'Amy.chen', userCode: 'BKD00421', avatar: girlAvatar, date: 'Jun 15, 2026 11:27 AM', createdAt: '2026-06-15T11:27:00', likeCount: 63, content: '推薦給每個覺得「時間永遠不夠用」的人。它不會告訴你怎麼把行程塞得更滿，而是讓你重新問自己：這些行程真的都必要嗎？' },
      { id: 405, username: 'slowmorning', userCode: 'BKD00098', avatar: boyAvatar, date: 'Jun 02, 2026 7:15 AM', createdAt: '2026-06-02T07:15:00', likeCount: 8, content: '把 Hygge 那一章讀了三遍。作者描述北歐冬天窩在家裡點蠟燭的段落，光是看文字就覺得暖起來了。' },
    ],
  },
];

// 輪播在 1440px 以上一次顯示 4 張，只有 4 本的話按箭頭畫面不會動，
// 看起來像壞掉。這裡把同樣四本複製幾份、書名加編號，純粹是為了
// 讓輪播看得出來會轉。等後端有真的書單，把這一段和下面的展開刪掉就好。
//
// 複製的書一樣有自己的 id 和心得 id，所以點進詳情頁看到的還是同一本，
// 按讚也不會跟本尊互相影響。
//
// ⚠️ COPIES 平常維持 1（共 8 本）。要測分頁器才改大，測完一定要改回來，
//    不然 commit 出去別人會看到一整排「小王子 4」。
const COPIES = 1;

const filler = Array.from({ length: COPIES }, (_, round) =>
  baseBooks.map((book) => ({
    ...book,
    id: book.id + baseBooks.length * (round + 1),
    title: `${book.title} ${round + 2}`,
    reviews: book.reviews.map((review) => ({
      ...review,
      id: review.id + 400 * (round + 1),
    })),
  }))
).flat();

export const books = [...baseBooks, ...filler];

// 網址上的 id 是字串（/books/3 拿到的是 "3"），資料裡是數字，
// 所以這裡用 Number() 轉過再比，不然永遠找不到。
export function getBookById(id) {
  return books.find((book) => book.id === Number(id));
}
