<!--
AdminNotice 後台說明框

虛線框起來的一段灰字，用來說明這一頁的規則。
例如「刪除分類前請確認無書籍使用該分類」這種提醒。

=== 怎麼用 ===

    <AdminNotice>
      會員申請新增書籍時僅填寫基本資訊，核准時需由管理員手動補齊資料。
    </AdminNotice>

沒有任何要傳的東西，夾一段字進去就好。
-->

<template>
  <p class="admin-notice">
    <slot></slot>
  </p>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;

.admin-notice {
  margin: 0;
  padding: $spacing-sm + $spacing-xxs $spacing-md;
  border: 1px dashed $neutral-400;
  border-radius: $btn-radius-std + 1px;
  background: $neutral-100;
  font-size: $p-xs-size;
  line-height: 1.8;
  color: $neutral-600;
}
</style>
