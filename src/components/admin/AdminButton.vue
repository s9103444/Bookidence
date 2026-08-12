<!--
AdminButton 後台按鈕

後台專用的按鈕。跟前台的 AppButton 是兩支，前台那顆有像素風的切角，
後台是密集的工具介面，切角在表格旁邊會很吵，所以這裡是單純的方角按鈕。

=== 怎麼用 ===

主要動作（深青實心）：

    <AdminButton>新增分類</AdminButton>

次要動作，例如彈窗裡的取消（白底描邊）：

    <AdminButton variant="outline">取消</AdminButton>

會刪掉東西的動作（紅色）：

    <AdminButton tone="danger">確定刪除</AdminButton>

表格裡那種小按鈕，用 size="xs"：

    <AdminButton size="xs">編輯</AdminButton>

要送出表單就照一般的寫法，不用另外接 @click：

    <form @submit.prevent="handleAdd">
      <AdminButton type="submit">新增分類</AdminButton>
    </form>

圖示直接寫在文字旁邊，位置由寫的順序決定：

    <AdminButton><AppIcon name="plus" :size="14" /> 新增上架書籍</AdminButton>

按鈕如果是用來跳到另一頁，填 to，不要自己接 @click。
這樣使用者可以右鍵「在新分頁開啟」：

    <AdminButton to="/admin/books/list">查看書籍列表</AdminButton>

=== 可以傳什麼 ===

variant  'solid'（預設，實心）| 'outline'（白底描邊）
tone     'primary'（預設，深青）| 'danger'（紅，會刪掉東西的動作用）
size     'sm'（預設，高 36px）| 'xs'（高 30px，放表格裡）
to       要跳到哪一頁。填了之後會變成連結，外觀不變
disabled 用 HTML 原生的就好，不用另外傳

=== 陷阱 ===

⚠️ 填了 to 就不要再用 disabled，不會有效果，點了照樣會跳。
   需要「條件符合才能跳」的話，讓 to 在不能跳的時候是 null，
   它會自動變回一般按鈕，disabled 就有效了：

   <AdminButton :to="canGo ? '/admin/books/list' : null" :disabled="!canGo">下一步</AdminButton>
-->
<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'

const props = defineProps({
  variant: {
    type: String,
    default: 'solid',
  },
  tone: {
    type: String,
    default: 'primary',
  },
  size: {
    type: String,
    default: 'sm',
  },
  to: {
    type: [String, Object],
    default: null,
  },
})

const classes = computed(() => [
  `admin-button--${props.variant}`,
  `admin-button--${props.tone}`,
  `admin-button--${props.size}`,
])
</script>

<template>
  <RouterLink v-if="to" :to="to" class="admin-button" :class="classes">
    <slot></slot>
  </RouterLink>
  <button v-else class="admin-button" :class="classes" type="button">
    <slot></slot>
  </button>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;

.admin-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: $spacing-sm;
  border: 1px solid transparent;
  border-radius: $btn-radius-std;
  font-family: inherit;
  font-weight: $heading-weight;
  letter-spacing: 0.05em;
  white-space: nowrap;
  text-decoration: none;
  cursor: pointer;
  transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;

  &--sm {
    height: 36px;
    padding: 0 $spacing-lg;
    font-size: $label-xs-size;
  }

  &--xs {
    height: 30px;
    padding: 0 $spacing-md;
    font-size: $label-xxs-size;
  }

  &--solid {
    color: $neutral-100;

    &.admin-button--primary {
      background: $primary;

      &:hover:not(:disabled) {
        background: color-mix(in srgb, black 15%, #{$primary});
      }
    }

    &.admin-button--danger {
      background: $color-danger;

      &:hover:not(:disabled) {
        background: color-mix(in srgb, black 15%, #{$color-danger});
      }
    }
  }

  &--outline {
    background: $neutral-100;

    &.admin-button--primary {
      border-color: $neutral-300;
      color: $neutral-600;

      &:hover:not(:disabled) {
        border-color: $primary;
        color: $primary;
      }
    }

    &.admin-button--danger {
      border-color: $neutral-300;
      color: $color-danger;

      &:hover:not(:disabled) {
        border-color: $color-danger;
        background: color-mix(in srgb, #{$color-danger} 6%, #{$neutral-100});
      }
    }
  }

  &:focus-visible {
    outline: 2px solid $primary;
    outline-offset: 2px;
  }

  &:disabled {
    border-color: $neutral-300;
    background: $neutral-200;
    color: $neutral-400;
    cursor: not-allowed;
  }

  :slotted(svg) {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
  }
}

@media (prefers-reduced-motion: reduce) {
  .admin-button {
    transition: none;
  }
}
</style>
