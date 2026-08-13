// 後台檢舉管理的狀態。處理結果寫進 localStorage，重新整理不會回到原點。
//
// ⚠️ 要改資料一律經過這裡，不要去改 data/adminReports.js 匯入的那個陣列 ——
// 那是普通陣列，改了畫面不會更新。
//
// 處理一筆檢舉會同時做兩件事：把結果寫回檢舉單，以及在被檢舉人身上記一筆處分。
// 兩件事一定要一起做，只寫檢舉單的話，管理員會發現「判了停權，人卻還登得進來」。

import { ref, computed, watch } from 'vue'
import { defineStore } from 'pinia'
import { adminReports, REPORT_STATUS, REPORT_ACTION } from '@/data/adminReports.js'
import { useAdminMembersStore } from '@/stores/adminMembers.js'

const STORAGE_KEY = 'adminReportList'

function load() {
  try {
    const saved = localStorage.getItem(STORAGE_KEY)
    if (!saved) return structuredClone(adminReports)
    const parsed = JSON.parse(saved)
    return Array.isArray(parsed) ? parsed : structuredClone(adminReports)
  } catch {
    return structuredClone(adminReports)
  }
}

function save(value) {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(value))
  } catch {
    // 無痕模式或容量滿了會寫不進去，不影響當下操作
  }
}

export const useAdminReportsStore = defineStore('adminReports', () => {
  const reports = ref(load())

  watch(reports, (value) => save(value), { deep: true })

  const pendingCount = computed(
    () => reports.value.filter((report) => report.status === REPORT_STATUS.pending).length,
  )

  // 待處理的，最新的排前面
  const pendingReports = computed(() =>
    reports.value
      .filter((report) => report.status === REPORT_STATUS.pending)
      .sort((a, b) => b.createdAt.localeCompare(a.createdAt)),
  )

  function getReport(id) {
    return reports.value.find((report) => report.id === id)
  }

  // 這位會員被檢舉過哪幾筆
  function reportsAgainst(userId) {
    return reports.value
      .filter((report) => report.reportedUserId === userId)
      .sort((a, b) => b.createdAt.localeCompare(a.createdAt))
  }

  function now() {
    const date = new Date()
    const pad = (value) => String(value).padStart(2, '0')
    return `${date.getFullYear()}/${pad(date.getMonth() + 1)}/${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`
  }

  // 處分寫進被檢舉人的處分紀錄。「駁回」不留紀錄 ——
  // 沒查證成立的指控不能算在一個人頭上。
  //
  // 成立就一定會下架那則內容，所以每一筆都先記一次，再看帳號要不要另外罰。
  // 順序不能反 —— warn 遇到已停權的人會直接跳過，內容那筆就漏了。
  function applyToMember(report, notes) {
    if (report.status !== REPORT_STATUS.upheld) return

    const members = useAdminMembersStore()

    members.removeContent(report.reportedUserId, notes, report.id)

    if (report.actionTaken === REPORT_ACTION.warnUser) {
      members.warn(report.reportedUserId, notes, report.id)
    } else if (report.actionTaken === REPORT_ACTION.suspendUser) {
      members.suspend(report.reportedUserId, notes, report.id)
    }
  }

  function resolve(id, { status, actionTaken, notes }) {
    const report = getReport(id)
    if (!report || report.status !== REPORT_STATUS.pending) return

    report.status = status
    report.actionTaken = actionTaken
    report.resolvedAt = now()
    report.staffAccount = '書芸'
    report.resolutionNotes = notes

    applyToMember(report, notes)
  }

  // 判錯了可以退回待處理。這次判決當初開出來的處分要一起撤銷 ——
  // 留著的話，改判不成立之後那個人身上還掛著一筆違規。
  function reopen(id) {
    const report = getReport(id)
    if (!report || report.status === REPORT_STATUS.pending) return

    useAdminMembersStore().revokeFromReport(id)

    report.status = REPORT_STATUS.pending
    report.actionTaken = null
    report.resolvedAt = null
    report.staffAccount = null
    report.resolutionNotes = null
  }

  return {
    reports,
    pendingCount,
    pendingReports,
    getReport,
    reportsAgainst,
    resolve,
    reopen,
  }
})
