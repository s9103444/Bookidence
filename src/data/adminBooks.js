// 後台書籍管理的兩組狀態字串。畫面文字和資料庫 ENUM 值是同一組，所以直接共用。

export const APPLICATION_STATUS = {
  pending: '待處理',
  approved: '已核准',
  rejected: '已駁回',
};

export const BOOK_STATUS = {
  listed: '已上架',
  unlisted: '已下架',
};
