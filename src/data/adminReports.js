// 檢舉的狀態常數。值要跟資料庫的 enum 完全一致 ——
// 前端拿它篩選、後端拿它比對，差一個字就對不上。

export const REPORT_TARGET = {
  thought: '心得',
  message: '留言',
};

export const REPORT_REASON = {
  attack: '人身攻擊',
  spam: '廣告垃圾資訊',
  inappropriate: '不當內容',
  plagiarism: '抄襲 / 侵權',
};

// 第一步：這筆檢舉成不成立
export const REPORT_STATUS = {
  pending: '尚未處理',
  upheld: '檢舉成立',
  dismissed: '檢舉不成立',
};

// 第二步：成立之後做了什麼處置。單選，對應 report.action_taken。
// 不成立一律是「駁回」。
export const REPORT_ACTION = {
  removeContent: '刪除內容',
  warnUser: '警告用戶',
  suspendUser: '停權用戶',
  dismiss: '駁回',
};
