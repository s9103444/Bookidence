// 後台公會管理的假資料。之後接後端 API，就把這個檔案換成打 API 的函式，頁面不用改。
//
// 會員的暱稱／編號盡量沿用 data/adminMembers.js 既有的那批，檢舉也直接連到
// data/adminReports.js 裡真的存在的那幾筆——這樣「前往檢舉案」點過去才看得到東西。

export const GUILD_STATUS = {
  active: '進行中',
  suspended: '已停權',
  deleted: '已刪除',
};

// 活動場次自己有線上／線下之分，公會本身不分
export const GUILD_MODE = {
  online: '線上',
  offline: '線下',
};

export const GUILD_MEMBER_ROLE = {
  leader: '會長',
  deputy: '副會長',
  member: '成員',
};

export function leaderOf(guild) {
  return guild.members.find((member) => member.role === GUILD_MEMBER_ROLE.leader) ?? null;
}

export function deputyOf(guild) {
  return guild.members.find((member) => member.role === GUILD_MEMBER_ROLE.deputy) ?? null;
}

export function isOfficerRole(role) {
  return role === GUILD_MEMBER_ROLE.leader || role === GUILD_MEMBER_ROLE.deputy;
}

// 檢舉紀錄不重複存一份，直接算留言裡被標記的那幾則
export function reportCountOf(guild) {
  return guild.messages.filter((message) => message.flagged).length;
}

// 近月平均出席率：每場的出席人數 / 容納人數，取平均
export function avgAttendanceOf(guild) {
  if (!guild.events.length) return 0;
  const total = guild.events.reduce((sum, event) => sum + event.attendeeCount / event.capacity, 0);
  return Math.round((total / guild.events.length) * 100);
}

export const adminGuilds = [
  {
    id: 'G-0027',
    name: '週三夜讀圈',
    status: GUILD_STATUS.active,
    description: '每週三晚間視訊共讀',
    currentBook: '北歐時間：世界第一幸福國度教會我的事',
    currentChapter: '第 8 章',
    createdAt: '2026/03/12',
    completedBooksCount: 3,
    suspendedAt: null,
    suspendedBy: null,
    suspendReason: null,
    deletedAt: null,
    deletedBy: null,
    deleteReason: null,
    members: [
      { id: 'MKD00000102', nickname: '晨讀時光', role: GUILD_MEMBER_ROLE.leader, joinedAt: '2026/03/12', attendanceRate: 96, messageCount: 142, flagged: false },
      { id: 'MKD00000244', nickname: '會員_0244', role: GUILD_MEMBER_ROLE.deputy, joinedAt: '2026/03/12', attendanceRate: 92, messageCount: 118, flagged: false },
      { id: 'MKD00000387', nickname: '會員_0387', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/03/20', attendanceRate: 88, messageCount: 75, flagged: false },
      { id: 'MKD00000421', nickname: '會員_0421', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/04/02', attendanceRate: 61, messageCount: 63, flagged: true },
      { id: 'MKD00000518', nickname: '會員_0518', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/05/11', attendanceRate: 74, messageCount: 31, flagged: false },
      { id: 'MKD00000777', nickname: '會員_0777', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/05/20', attendanceRate: 85, messageCount: 40, flagged: false },
      { id: 'MKD00000295', nickname: '會員_0295', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/05/25', attendanceRate: 70, messageCount: 22, flagged: false },
      { id: 'MKD00000633', nickname: '夜貓讀者', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/06/03', attendanceRate: 90, messageCount: 55, flagged: false },
      { id: 'MKD00001024', nickname: '慢慢看', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/06/18', attendanceRate: 55, messageCount: 9, flagged: false },
      { id: 'MKD00001156', nickname: '晴天閱讀', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/07/02', attendanceRate: 68, messageCount: 14, flagged: false },
    ],
    events: [
      { id: 'GE-0024', no: 24, mode: GUILD_MODE.online, time: '07/13 20:00', title: '第 8 章討論', attendeeCount: 8, capacity: 10 },
      { id: 'GE-0023', no: 23, mode: GUILD_MODE.online, time: '07/06 20:00', title: '第 7 章討論', attendeeCount: 9, capacity: 10 },
      { id: 'GE-0022', no: 22, mode: GUILD_MODE.offline, time: '06/29 14:00', title: '期中實體聚會（半本回顧）', attendeeCount: 7, capacity: 10 },
      { id: 'GE-0021', no: 21, mode: GUILD_MODE.online, time: '06/22 20:00', title: '第 6 章討論', attendeeCount: 8, capacity: 10 },
      { id: 'GE-0020', no: 20, mode: GUILD_MODE.online, time: '06/15 20:00', title: '第 5 章討論', attendeeCount: 9, capacity: 10 },
      { id: 'GE-0019', no: 19, mode: GUILD_MODE.online, time: '06/08 20:00', title: '第 4 章討論', attendeeCount: 7, capacity: 10 },
    ],
    messages: [
      {
        id: 'GM-0301',
        authorId: 'MKD00000387',
        authorNickname: '會員_0387',
        authorRole: null,
        thread: '第 8 章討論串',
        time: '07/13 21:12',
        content: '這章的轉折完全出乎意料，作者前面埋的伏筆到這裡才收回來……',
        flagged: false,
        reportId: null,
      },
      {
        id: 'GM-0302',
        authorId: 'MKD00000421',
        authorNickname: '會員_0421',
        authorRole: null,
        thread: '第 8 章討論串',
        time: '07/13 21:40',
        content:
          '這本書根本是垃圾，作者是〇〇……會推薦這本書的人腦袋都有問題，包括樓上那個自以為很懂文學的，建議先去把國中國文讀完再來留言。',
        flagged: true,
        reportId: 'R-0192',
      },
      {
        id: 'GM-0303',
        authorId: 'MKD00000102',
        authorNickname: '晨讀時光',
        authorRole: GUILD_MEMBER_ROLE.leader,
        thread: '進度打卡牆',
        time: '07/13 20:05',
        content: '今晚出席 8 位，下週進度第 9 章，補課的夥伴記得看討論串整理～',
        flagged: false,
        reportId: null,
      },
    ],
  },
  {
    id: 'G-0014',
    name: '推理小說同好會',
    status: GUILD_STATUS.active,
    description: '雙週六下午實體聚會，主攻本格推理',
    currentBook: '毒巧克力命案',
    currentChapter: '第 5 章',
    createdAt: '2026/02/20',
    completedBooksCount: 5,
    suspendedAt: null,
    suspendedBy: null,
    suspendReason: null,
    deletedAt: null,
    deletedBy: null,
    deleteReason: null,
    members: [
      { id: 'MKD00000518', nickname: '會員_0518', role: GUILD_MEMBER_ROLE.leader, joinedAt: '2026/02/20', attendanceRate: 90, messageCount: 60, flagged: false },
      { id: 'MKD00001156', nickname: '晴天閱讀', role: GUILD_MEMBER_ROLE.deputy, joinedAt: '2026/02/22', attendanceRate: 85, messageCount: 47, flagged: false },
      { id: 'MKD00000845', nickname: '一頁一世界', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/03/01', attendanceRate: 78, messageCount: 20, flagged: false },
      { id: 'MKD00000421', nickname: '會員_0421', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/03/15', attendanceRate: 66, messageCount: 12, flagged: false },
      { id: 'MKD00001283', nickname: '紙本派', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/04/02', attendanceRate: 80, messageCount: 18, flagged: false },
    ],
    events: [
      { id: 'GE-0012', no: 12, mode: GUILD_MODE.offline, time: '07/12 14:00', title: '第 5 章討論', attendeeCount: 10, capacity: 15 },
      { id: 'GE-0011', no: 11, mode: GUILD_MODE.offline, time: '06/28 14:00', title: '第 4 章討論', attendeeCount: 11, capacity: 15 },
      { id: 'GE-0010', no: 10, mode: GUILD_MODE.offline, time: '06/14 14:00', title: '第 3 章討論', attendeeCount: 9, capacity: 15 },
    ],
    messages: [
      {
        id: 'GM-0201',
        authorId: 'MKD00000518',
        authorNickname: '會員_0518',
        authorRole: GUILD_MEMBER_ROLE.leader,
        thread: '第 5 章討論串',
        time: '07/12 15:30',
        content: '這次凶手的動機安排得比前作合理多了，大家覺得呢？',
        flagged: false,
        reportId: null,
      },
      {
        id: 'GM-0202',
        authorId: 'MKD00000845',
        authorNickname: '一頁一世界',
        authorRole: null,
        thread: '選書投票',
        time: '07/10 09:15',
        content: '下一本想提名《東方快車謀殺案》，附議的話幫我在下面 +1～',
        flagged: false,
        reportId: null,
      },
    ],
  },
  {
    id: 'G-0009',
    name: '壁爐與貓',
    status: GUILD_STATUS.active,
    description: '深夜語音讀書會，貓派限定',
    currentBook: '在咖啡冷掉之前',
    currentChapter: '第 3 章',
    createdAt: '2026/01/15',
    completedBooksCount: 2,
    suspendedAt: null,
    suspendedBy: null,
    suspendReason: null,
    deletedAt: null,
    deletedBy: null,
    deleteReason: null,
    members: [
      { id: 'MKD00000421', nickname: '會員_0421', role: GUILD_MEMBER_ROLE.leader, joinedAt: '2026/01/15', attendanceRate: 58, messageCount: 90, flagged: true },
      { id: 'MKD00000633', nickname: '夜貓讀者', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/01/20', attendanceRate: 82, messageCount: 33, flagged: false },
      { id: 'MKD00000518', nickname: '會員_0518', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/02/10', attendanceRate: 70, messageCount: 21, flagged: false },
    ],
    events: [
      { id: 'GE-0006', no: 6, mode: GUILD_MODE.online, time: '07/06 22:00', title: '第 3 章討論', attendeeCount: 5, capacity: 8 },
      { id: 'GE-0005', no: 5, mode: GUILD_MODE.online, time: '06/22 22:00', title: '第 2 章討論', attendeeCount: 6, capacity: 8 },
    ],
    messages: [
      {
        id: 'GM-0101',
        authorId: 'MKD00000421',
        authorNickname: '會員_0421',
        authorRole: GUILD_MEMBER_ROLE.leader,
        thread: '日常閒聊',
        time: '07/02 08:44',
        content: '這種讀後感也敢貼出來，是不是沒讀過書啊。',
        flagged: true,
        reportId: 'R-0177',
      },
    ],
  },
  {
    id: 'G-0041',
    name: '口袋書局',
    status: GUILD_STATUS.suspended,
    description: '不定期線上快閃讀書會，主題常常換',
    currentBook: null,
    currentChapter: null,
    createdAt: '2026/05/02',
    completedBooksCount: 1,
    suspendedAt: '2026/07/18 11:20',
    suspendedBy: '書芸',
    suspendReason: '會長長期未管理，多名成員檢舉群組內大量垃圾廣告訊息，經查證屬實，予以公會停權。',
    deletedAt: null,
    deletedBy: null,
    deleteReason: null,
    members: [
      { id: 'MKD00000912', nickname: '會員_0912', role: GUILD_MEMBER_ROLE.leader, joinedAt: '2026/05/02', attendanceRate: 40, messageCount: 88, flagged: true },
      { id: 'MKD00001283', nickname: '紙本派', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/05/10', attendanceRate: 60, messageCount: 20, flagged: false },
      { id: 'MKD00000701', nickname: '小鹿', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/05/15', attendanceRate: 45, messageCount: 12, flagged: false },
    ],
    events: [
      { id: 'GE-0003', no: 3, mode: GUILD_MODE.online, time: '06/20 20:00', title: '選書夜', attendeeCount: 3, capacity: 10 },
      { id: 'GE-0002', no: 2, mode: GUILD_MODE.online, time: '06/06 20:00', title: '閒聊夜', attendeeCount: 4, capacity: 10 },
    ],
    messages: [
      {
        id: 'GM-0501',
        authorId: 'MKD00001283',
        authorNickname: '紙本派',
        authorRole: null,
        thread: '選書夜討論串',
        time: '06/20 21:00',
        content: '這次選的書題材有點重複，可以換個類型嗎？',
        flagged: false,
        reportId: null,
      },
    ],
  },
  {
    id: 'G-0033',
    name: '文青小時光',
    status: GUILD_STATUS.active,
    description: '文學小說與散文交流，偶爾辦實體野餐讀書會',
    currentBook: '82 年生的金智英',
    currentChapter: '第 3 章',
    createdAt: '2026/04/01',
    completedBooksCount: 4,
    suspendedAt: null,
    suspendedBy: null,
    suspendReason: null,
    deletedAt: null,
    deletedBy: null,
    deleteReason: null,
    members: [
      { id: 'MKD00000845', nickname: '一頁一世界', role: GUILD_MEMBER_ROLE.leader, joinedAt: '2026/04/01', attendanceRate: 88, messageCount: 70, flagged: false },
      { id: 'MKD00000295', nickname: '會員_0295', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/04/05', attendanceRate: 75, messageCount: 26, flagged: false },
      { id: 'MKD00000701', nickname: '小鹿', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/04/10', attendanceRate: 64, messageCount: 15, flagged: true },
      { id: 'MKD00001283', nickname: '紙本派', role: GUILD_MEMBER_ROLE.member, joinedAt: '2026/05/01', attendanceRate: 80, messageCount: 19, flagged: false },
    ],
    events: [
      { id: 'GE-0009', no: 9, mode: GUILD_MODE.offline, time: '07/11 15:00', title: '野餐讀書會', attendeeCount: 12, capacity: 20 },
      { id: 'GE-0008', no: 8, mode: GUILD_MODE.online, time: '06/27 20:00', title: '第 2 章討論', attendeeCount: 14, capacity: 20 },
      { id: 'GE-0007', no: 7, mode: GUILD_MODE.online, time: '06/13 20:00', title: '第 1 章討論', attendeeCount: 15, capacity: 20 },
    ],
    messages: [
      {
        id: 'GM-0401',
        authorId: 'MKD00000845',
        authorNickname: '一頁一世界',
        authorRole: GUILD_MEMBER_ROLE.leader,
        thread: '野餐讀書會揪團',
        time: '07/05 12:00',
        content: '這週六野餐讀書會記得帶野餐墊，書社準備飲料跟點心～',
        flagged: false,
        reportId: null,
      },
      {
        id: 'GM-0402',
        authorId: 'MKD00000701',
        authorNickname: '小鹿',
        authorRole: null,
        thread: '雜談區',
        time: '07/01 13:12',
        content: '（內容包含大量與讀書會無關的私人爭執與情緒發言）',
        flagged: true,
        reportId: 'R-0175',
      },
    ],
  },
];
