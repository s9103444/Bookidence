// 後台會員管理的狀態。處分紀錄寫進 localStorage，重新整理不會回到原點。
//
// ⚠️ 要改資料一律經過這裡，不要去改 data/adminMembers.js 匯入的那個陣列 ——
// 那是普通陣列，改了畫面不會更新。
//
// 之後接後端 API，就把 warn / suspend / restore 換成打 API，頁面不用改。

import { ref, computed, watch } from 'vue'
import { defineStore } from 'pinia'
import {
  adminMembers,
  MEMBER_STATUS,
  ACTION_TYPE,
  currentSuspension,
  isRevoked,
} from '@/data/adminMembers.js'

const STORAGE_KEY = 'adminMemberList'

function load() {
  try {
    const saved = localStorage.getItem(STORAGE_KEY)
    if (!saved) return structuredClone(adminMembers)
    const parsed = JSON.parse(saved)
    return Array.isArray(parsed) ? parsed : structuredClone(adminMembers)
  } catch {
    return structuredClone(adminMembers)
  }
}

function save(value) {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(value))
  } catch {
    // 無痕模式或容量滿了會寫不進去，不影響當下操作
  }
}

export const useAdminMembersStore = defineStore('adminMembers', () => {
  const members = ref(load())

  watch(members, (value) => save(value), { deep: true })

  const suspendedCount = computed(
    () => members.value.filter((member) => member.status === MEMBER_STATUS.suspended).length,
  )

  function getMember(id) {
    return members.value.find((member) => member.id === id)
  }

  function now() {
    const date = new Date()
    const pad = (value) => String(value).padStart(2, '0')
    return `${date.getFullYear()}/${pad(date.getMonth() + 1)}/${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`
  }

  function nextActionId() {
    const used = members.value
      .flatMap((member) => member.actions ?? [])
      .map((action) => Number(String(action.id).replace('MA-', '')))
      .filter((n) => !Number.isNaN(n))

    return `MA-${String(Math.max(0, ...used) + 1).padStart(4, '0')}`
  }

  // 處分紀錄只新增、不修改也不刪除，所以每個動作都是往前面插一筆
  function addAction(member, type, reason, reportId = null) {
    if (!member.actions) member.actions = []

    member.actions.unshift({
      id: nextActionId(),
      type,
      reason,
      by: '書芸',
      at: now(),
      reportId,
    })
  }

  function warn(id, reason, reportId = null) {
    const member = getMember(id)
    if (!member || member.status === MEMBER_STATUS.suspended) return

    addAction(member, ACTION_TYPE.warn, reason, reportId)
  }

  function suspend(id, reason, reportId = null) {
    const member = getMember(id)
    if (!member || member.status === MEMBER_STATUS.suspended) return

    member.status = MEMBER_STATUS.suspended
    addAction(member, ACTION_TYPE.suspend, reason, reportId)
  }

  // 刪除違規內容。罰的是那則心得或留言，不是這個帳號，
  // 所以已停權的人也照樣可以刪他的內容，而且不算進「處分次數」。
  function removeContent(id, reason, reportId = null) {
    const member = getMember(id)
    if (!member) return

    addAction(member, ACTION_TYPE.remove, reason, reportId)
  }

  // 某筆檢舉被退回重新處理時，它當初開出來的處分要一起作廢 ——
  // 不然「查證後改判不成立」的人身上還留著一筆違規。
  //
  // 紀錄不刪，只是標記成已撤銷：稽核要查得到管理員做過什麼、又收回了什麼。
  function revokeFromReport(reportId) {
    members.value.forEach((member) => {
      const revoked = (member.actions ?? []).filter(
        (action) => action.reportId === reportId && !isRevoked(action),
      )

      if (!revoked.length) return

      revoked.forEach((action) => {
        action.revokedAt = now()
        action.revokedBy = '書芸'
      })

      // 撤銷的如果是停權，帳號要跟著回到正常
      member.status = currentSuspension(member) ? MEMBER_STATUS.suspended : MEMBER_STATUS.active
    })
  }

  function restore(id, reason) {
    const member = getMember(id)
    if (!member || member.status !== MEMBER_STATUS.suspended) return

    member.status = MEMBER_STATUS.active
    addAction(member, ACTION_TYPE.restore, reason)
  }

  return {
    members,
    suspendedCount,
    getMember,
    warn,
    suspend,
    removeContent,
    revokeFromReport,
    restore,
  }
})
