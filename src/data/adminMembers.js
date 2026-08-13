// 後台會員管理的假資料。之後接後端 API，就把這個檔案換成打 API 的函式，頁面不用改。

export const MEMBER_STATUS = {
  active: '正常',
  suspended: '停權',
};

// 檢舉查證後的處理結果。pending 和 dismissed 都不算違規 ——
// 任何人都能檢舉，沒查證過的指控不能當成這個人的紀錄。
export const REPORT_ACTION = {
  pending: '未處理',
  dismissed: '不成立',
  warned: '已警告',
  suspended: '停權',
};

// 管理員對會員做過的處分，對應 moderation_action 表。
// 只新增不覆寫 —— 解除停權是再記一筆，不是把停權那筆抹掉，
// 這樣「被停權過幾次」才查得到。
export const ACTION_TYPE = {
  warn: '警告',
  suspend: '停權',
  restore: '解除停權',
};

export function actionsOf(member) {
  return member.actions ?? [];
}

export function warningCountOf(member) {
  return actionsOf(member).filter((action) => action.type === ACTION_TYPE.warn).length;
}

export function suspensionCountOf(member) {
  return actionsOf(member).filter((action) => action.type === ACTION_TYPE.suspend).length;
}

// 目前這次停權是哪一筆。actions 維持最新在前，所以找到的第一筆
// 只要是「停權」就代表現在還停著，是「解除停權」就代表已經解開了。
export function currentSuspension(member) {
  const latest = actionsOf(member).find(
    (action) => action.type === ACTION_TYPE.suspend || action.type === ACTION_TYPE.restore,
  );
  return latest && latest.type === ACTION_TYPE.suspend ? latest : null;
}

// 這個人被處分過幾次。算處分不算檢舉 —— 任何人都能檢舉，
// 而且管理員主動處分（例如申請單灌爆審核佇列）根本不會有檢舉單。
export function punishmentsOf(member) {
  return actionsOf(member).filter((action) => action.type !== ACTION_TYPE.restore);
}

export function openReportsOf(member) {
  return member.reports.filter((report) => report.action === REPORT_ACTION.pending);
}

export const adminMembers = [
  {
    id: 'MKD00000102',
    nickname: '晨讀時光',
    email: 'morning102@mail.com',
    joinedAt: '2026/03/09',
    status: MEMBER_STATUS.active,
    actions: [
          {
            id: 'MA-0108',
            type: '警告',
            reason: '書評內容偏離主題並夾帶推銷連結。',
            by: '書芸',
            at: '2026/06/20 10:00',
            reportId: 'R-0142',
          },
        ],
    guilds: [{ name: '週三夜讀圈', role: '會長' }],
    reports: [
      {
        id: 'R-0142',
        targetType: '心得',
        reason: '不當內容',
        createdAt: '2026/06/20',
        excerpt: '書評內容偏離主題並夾帶推銷連結',
        action: '已警告',
      },
    ],
  },
  {
    id: 'MKD00000244',
    nickname: '會員_0244',
    email: 'm0244@mail.com',
    joinedAt: '2026/03/12',
    status: MEMBER_STATUS.active,
    actions: [],
    guilds: [],
    reports: [],
  },
  {
    id: 'MKD00000387',
    nickname: '會員_0387',
    email: 'm0387@mail.com',
    joinedAt: '2026/03/20',
    status: MEMBER_STATUS.active,
    actions: [
          {
            id: 'MA-0106',
            type: '警告',
            reason: '內容疑似抄襲他人書評。',
            by: '書芸',
            at: '2026/06/02 10:00',
            reportId: 'R-0155',
          },
          {
            id: 'MA-0104',
            type: '警告',
            reason: '發言涉及人身攻擊。',
            by: '書芸',
            at: '2026/05/18 10:00',
            reportId: 'R-0098',
          },
        ],
    guilds: [
      { name: '週三夜讀圈', role: '一般成員' },
      { name: '推理小說同好會', role: '一般成員' },
    ],
    reports: [
      {
        id: 'R-0155',
        targetType: '心得',
        reason: '抄襲 / 侵權',
        createdAt: '2026/06/02',
        excerpt: '內容疑似抄襲他人書評',
        action: '已警告',
      },
      {
        id: 'R-0098',
        targetType: '留言',
        reason: '人身攻擊',
        createdAt: '2026/05/18',
        excerpt: '發言涉及人身攻擊',
        action: '已警告',
      },
    ],
  },
  {
    id: 'MKD00000421',
    nickname: '會員_0421',
    email: 'm0421@mail.com',
    joinedAt: '2026/04/02',
    status: MEMBER_STATUS.active,
    actions: [
          {
            id: 'MA-0111',
            type: '警告',
            reason: '討論區發言帶有攻擊性字眼。',
            by: '書芸',
            at: '2026/07/02 10:00',
            reportId: 'R-0177',
          },
          {
            id: 'MA-0107',
            type: '警告',
            reason: '重複張貼外部購物連結。',
            by: '書芸',
            at: '2026/06/05 10:00',
            reportId: 'R-0151',
          },
          {
            id: 'MA-0105',
            type: '警告',
            reason: '整篇轉貼自其他網站書評。',
            by: '書芸',
            at: '2026/05/22 10:00',
            reportId: 'R-0133',
          },
          {
            id: 'MA-0103',
            type: '警告',
            reason: '對其他成員的閱讀品味進行嘲諷。',
            by: '書芸',
            at: '2026/05/10 10:00',
            reportId: 'R-0121',
          },
          {
            id: 'MA-0102',
            type: '警告',
            reason: '在多個討論串張貼相同宣傳文字。',
            by: '書芸',
            at: '2026/04/15 10:00',
            reportId: 'R-0102',
          },
          {
            id: 'MA-0101',
            type: '警告',
            reason: '公開點名並嘲諷特定會員。',
            by: '書芸',
            at: '2026/04/06 10:00',
            reportId: 'R-0091',
          },
        ],
    guilds: [{ name: '壁爐與貓', role: '會長' }],
    reports: [
      {
        id: 'R-0192',
        targetType: '心得',
        reason: '人身攻擊',
        createdAt: '2026/07/13',
        excerpt: '在心得下方多次辱罵其他讀者',
        action: '未處理',
      },
      {
        id: 'R-0177',
        targetType: '留言',
        reason: '人身攻擊',
        createdAt: '2026/07/02',
        excerpt: '討論區發言帶有攻擊性字眼',
        action: '已警告',
      },
      {
        id: 'R-0164',
        targetType: '心得',
        reason: '不當內容',
        createdAt: '2026/06/18',
        excerpt: '心得內容與書籍無關且情緒性字眼過多',
        action: '不成立',
      },
      {
        id: 'R-0151',
        targetType: '留言',
        reason: '廣告垃圾資訊',
        createdAt: '2026/06/05',
        excerpt: '重複張貼外部購物連結',
        action: '已警告',
      },
      {
        id: 'R-0133',
        targetType: '心得',
        reason: '抄襲 / 侵權',
        createdAt: '2026/05/22',
        excerpt: '整篇轉貼自其他網站書評',
        action: '已警告',
      },
      {
        id: 'R-0121',
        targetType: '留言',
        reason: '人身攻擊',
        createdAt: '2026/05/10',
        excerpt: '對其他成員的閱讀品味進行嘲諷',
        action: '已警告',
      },
      {
        id: 'R-0110',
        targetType: '心得',
        reason: '不當內容',
        createdAt: '2026/04/28',
        excerpt: '心得夾帶與書籍無關的政治訴求',
        action: '不成立',
      },
      {
        id: 'R-0102',
        targetType: '留言',
        reason: '廣告垃圾資訊',
        createdAt: '2026/04/15',
        excerpt: '在多個討論串張貼相同宣傳文字',
        action: '已警告',
      },
      {
        id: 'R-0091',
        targetType: '心得',
        reason: '人身攻擊',
        createdAt: '2026/04/06',
        excerpt: '公開點名並嘲諷特定會員',
        action: '已警告',
      },
    ],
  },
  {
    id: 'MKD00000518',
    nickname: '會員_0518',
    email: 'm0518@mail.com',
    joinedAt: '2026/05/11',
    status: MEMBER_STATUS.active,
    actions: [],
    guilds: [{ name: '推理小說同好會', role: '一般成員' }],
    reports: [],
  },
  {
    id: 'MKD00000912',
    nickname: '會員_0912',
    email: 'm0912@mail.com',
    joinedAt: '2026/05/01',
    status: MEMBER_STATUS.suspended,
    actions: [
          {
            id: 'MA-0115',
            type: '停權',
            reason: '累計違規 3 次，警告後仍持續於心得留言區辱罵其他讀者。',
            by: '書芸',
            at: '2026/07/20 14:30',
            reportId: 'R-0188',
          },
          {
            id: 'MA-0110',
            type: '警告',
            reason: '多次於心得留言區與其他讀者發生爭執，請注意用字。',
            by: '書芸',
            at: '2026/06/30 10:12',
            reportId: 'R-0158',
          },
          {
            id: 'MA-0109',
            type: '警告',
            reason: '連續三篇心得抄自同一個書評網站。',
            by: '書芸',
            at: '2026/06/28 10:00',
            reportId: 'R-0170',
          },
        ],
    guilds: [],
    reports: [
      {
        id: 'R-0188',
        targetType: '心得',
        reason: '人身攻擊',
        createdAt: '2026/07/12',
        excerpt: '心得留言區與多位讀者發生爭執並辱罵',
        action: '已停權',
      },
      {
        id: 'R-0170',
        targetType: '心得',
        reason: '抄襲 / 侵權',
        createdAt: '2026/06/28',
        excerpt: '連續三篇心得抄自同一個書評網站',
        action: '已警告',
      },
      {
        id: 'R-0158',
        targetType: '留言',
        reason: '人身攻擊',
        createdAt: '2026/06/14',
        excerpt: '對公會會長進行人身攻擊',
        action: '已警告',
      },
    ],
  },
  {
    id: 'MKD00000295',
    nickname: '會員_0295',
    email: 'm0295@mail.com',
    joinedAt: '2026/05/23',
    status: MEMBER_STATUS.active,
    actions: [
          {
            id: 'MA-0114',
            type: '解除停權',
            reason: '申覆成立，經查該篇心得為本人原創。',
            by: '書芸',
            at: '2026/07/14 11:20',
            reportId: null,
          },
          {
            id: 'MA-0113',
            type: '停權',
            reason: '書籍簡介疑似整段抄自出版社網頁。',
            by: '書芸',
            at: '2026/07/13 18:05',
            reportId: 'R-0189',
          },
          {
            id: 'MA-0112',
            type: '警告',
            reason: '申請好書推薦時連續送出 8 筆重複書單，請勿灌爆審核佇列。',
            by: '書芸',
            at: '2026/07/05 16:40',
            reportId: null,
          },
        ],
    guilds: [{ name: '文青小時光', role: '一般成員' }],
    reports: [
      {
        id: 'R-0189',
        targetType: '心得',
        reason: '抄襲 / 侵權',
        createdAt: '2026/07/13',
        excerpt: '書籍簡介整段複製自出版社網頁',
        action: '不成立',
      },
    ],
  },
  {
    id: 'MKD00000633',
    nickname: '夜貓讀者',
    email: 'nightcat@mail.com',
    joinedAt: '2026/06/01',
    status: MEMBER_STATUS.active,
    actions: [],
    guilds: [{ name: '壁爐與貓', role: '一般成員' }],
    reports: [],
  },
  {
    id: 'MKD00000701',
    nickname: '小鹿',
    email: 'deer701@mail.com',
    joinedAt: '2026/06/08',
    status: MEMBER_STATUS.active,
    actions: [],
    guilds: [],
    reports: [],
  },
  {
    id: 'MKD00000777',
    nickname: '會員_0777',
    email: 'm0777@mail.com',
    joinedAt: '2026/06/15',
    status: MEMBER_STATUS.active,
    actions: [],
    guilds: [{ name: '週三夜讀圈', role: '一般成員' }],
    reports: [
      {
        id: 'R-0181',
        targetType: '留言',
        reason: '廣告垃圾資訊',
        createdAt: '2026/07/08',
        excerpt: '在討論區張貼補習班宣傳',
        action: '未處理',
      },
    ],
  },
  {
    id: 'MKD00000845',
    nickname: '一頁一世界',
    email: 'onepage@mail.com',
    joinedAt: '2026/06/22',
    status: MEMBER_STATUS.active,
    actions: [],
    guilds: [{ name: '文青小時光', role: '會長' }],
    reports: [],
  },
  {
    id: 'MKD00000968',
    nickname: '咖啡與書',
    email: 'coffee968@mail.com',
    joinedAt: '2026/07/02',
    status: MEMBER_STATUS.suspended,
    actions: [
          {
            id: 'MA-0117',
            type: '停權',
            reason: '註冊後大量張貼商業廣告，確認為行銷帳號。',
            by: '書芸',
            at: '2026/07/25 09:15',
            reportId: 'R-0195',
          },
          {
            id: 'MA-0116',
            type: '停權',
            reason: '心得內容為純廣告文案。',
            by: '書芸',
            at: '2026/07/22 10:00',
            reportId: 'R-0193',
          },
        ],
    guilds: [],
    reports: [
      {
        id: 'R-0195',
        targetType: '留言',
        reason: '廣告垃圾資訊',
        createdAt: '2026/07/24',
        excerpt: '一天內在 14 個討論串張貼相同廣告',
        action: '已停權',
      },
      {
        id: 'R-0193',
        targetType: '心得',
        reason: '廣告垃圾資訊',
        createdAt: '2026/07/22',
        excerpt: '心得內容為純廣告文案',
        action: '已停權',
      },
    ],
  },
  {
    id: 'MKD00001024',
    nickname: '慢慢看',
    email: 'slow1024@mail.com',
    joinedAt: '2026/07/09',
    status: MEMBER_STATUS.active,
    actions: [],
    guilds: [],
    reports: [],
  },
  {
    id: 'MKD00001156',
    nickname: '晴天閱讀',
    email: 'sunny@mail.com',
    joinedAt: '2026/07/16',
    status: MEMBER_STATUS.active,
    actions: [],
    guilds: [{ name: '推理小說同好會', role: '一般成員' }],
    reports: [],
  },
  {
    id: 'MKD00001283',
    nickname: '紙本派',
    email: 'paper@mail.com',
    joinedAt: '2026/07/28',
    status: MEMBER_STATUS.active,
    actions: [],
    guilds: [],
    reports: [],
  },
];
