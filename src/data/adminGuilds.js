// 後台公會管理頁的常數、DB↔UI 對照表、工具函式。
// 資料本身已經改打 admin_guilds.php / admin_guild_detail.php，這裡不再放假資料。

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

// guild.guild_status 的 DB enum 是 正常/停權/已解散，跟畫面上的中文標籤不是同一套字
const DB_TO_UI_STATUS = {
  正常: GUILD_STATUS.active,
  停權: GUILD_STATUS.suspended,
  已解散: GUILD_STATUS.deleted,
};

const UI_TO_DB_STATUS = {
  [GUILD_STATUS.active]: '正常',
  [GUILD_STATUS.suspended]: '停權',
  [GUILD_STATUS.deleted]: '已解散',
};

export function dbGuildStatusToUi(dbStatus) {
  return DB_TO_UI_STATUS[dbStatus] ?? dbStatus;
}

export function uiGuildStatusToDb(uiStatus) {
  return UI_TO_DB_STATUS[uiStatus] ?? uiStatus;
}

// guildmember.permission_level 的 DB enum 是 一般/副會長/會長
const DB_TO_UI_ROLE = {
  一般: GUILD_MEMBER_ROLE.member,
  副會長: GUILD_MEMBER_ROLE.deputy,
  會長: GUILD_MEMBER_ROLE.leader,
};

export function dbPermissionToRole(permissionLevel) {
  return DB_TO_UI_ROLE[permissionLevel] ?? permissionLevel;
}

// event.event_type 的 DB enum 值是 線上(Online)/線下(Offline)，不是單純的「線上」「線下」
export function dbEventTypeToMode(eventType) {
  return eventType?.includes('線上') ? GUILD_MODE.online : GUILD_MODE.offline;
}

export function leaderOf(members) {
  return members.find((member) => member.role === GUILD_MEMBER_ROLE.leader) ?? null;
}

export function deputyOf(members) {
  return members.find((member) => member.role === GUILD_MEMBER_ROLE.deputy) ?? null;
}

export function isOfficerRole(role) {
  return role === GUILD_MEMBER_ROLE.leader || role === GUILD_MEMBER_ROLE.deputy;
}

// event 表沒有獨立的標題欄位，只能拿 description 截一段當標題
export function titleOf(event) {
  const text = event.description ?? '';
  return text.length > 20 ? `${text.slice(0, 20)}…` : text;
}
