<!--
AdminPanel 後台白卡

後台每一塊內容都裝在一張白色圓角卡片裡。這支就是那張卡片。

=== 怎麼用 ===

最單純的用法，夾什麼就顯示什麼：

    <AdminPanel title="現有分類（12）">
      這裡夾的東西會出現在卡片裡
    </AdminPanel>

裝表格的時候要多傳一個 flush，表格才會貼齊卡片邊緣：

    <AdminPanel flush>
      <table class="data-table"> ... </table>
    </AdminPanel>

右上角要放按鈕的話，用 actions 這個位置：

    <AdminPanel title="申請內容">
      <template #actions>
        <AppButton size="xs">切換編輯模式</AppButton>
      </template>
      ...內容...
    </AdminPanel>

=== 可以傳什麼 ===

title    卡片標題。不傳就不顯示，整張卡片只有內容
sub      標題底下那行小灰字，用來補充說明
flush    裝表格時傳它。卡片的內距會收掉，讓表格自己貼齊邊緣

=== 陷阱 ===

傳了 flush 又同時傳 title 的話，標題會貼在卡片左上角沒有留白，
看起來很擠。要標題就不要 flush，兩個一起用通常是拿錯了。
-->

<script setup>
defineProps({
  title: {
    type: String,
    default: '',
  },
  sub: {
    type: String,
    default: '',
  },
  flush: {
    type: Boolean,
    default: false,
  },
})
</script>

<template>
  <section class="admin-panel" :class="{ 'admin-panel--flush': flush }">
    <header v-if="title || $slots.actions" class="admin-panel__head">
      <div>
        <h2 v-if="title" class="admin-panel__title">{{ title }}</h2>
        <p v-if="sub" class="admin-panel__sub">{{ sub }}</p>
      </div>
      <slot name="actions"></slot>
    </header>

    <slot></slot>
  </section>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;

.admin-panel {
  padding: $spacing-md + $spacing-xxs $spacing-lg $spacing-lg;
  background: $neutral-100;
  border: 1px solid $neutral-300;
  border-radius: 10px;
  // 讓圓角把表格的直角切掉，不然表頭的灰底會戳出卡片外面
  overflow: hidden;
  min-width: 0;

  &--flush {
    padding: 0;
  }

  &__head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: $spacing-md;
    margin-bottom: $spacing-md;
  }

  &__title {
    margin: 0;
    font-size: $p-sm-size;
    font-weight: $heading-weight;
    color: $neutral-800;
    letter-spacing: 0.05em;
  }

  &__sub {
    margin: $spacing-xs 0 0;
    font-size: $p-xs-size;
    color: $neutral-400;
  }
}
</style>
