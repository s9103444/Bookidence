<!--
AdminResultBar 處理結果列

一件事處理完之後，在頁面最上方橫著的那條說明：
左邊寫發生了什麼、什麼時候、誰做的，右邊可以放一顆反悔用的按鈕。

書籍審核的「已核准」、會員的「已停權」、檢舉的「檢舉成立」都是這一條。

=== 怎麼用 ===

1. 最單純的用法，只有左邊那段字：

    <AdminResultBar
      label="已停權"
      meta="2026/07/20 14:30 · 處理人 書芸"
      detail="原因：累計違規 3 次"
      tone="danger"
    />

2. 右邊要放反悔按鈕的話，直接夾在中間：

    <AdminResultBar label="已駁回" meta="..." tone="danger">
      <AdminButton variant="outline" size="xs" @click="重新審核">重新審核</AdminButton>
    </AdminResultBar>

3. 剛處理完的那一次要傳 announce，螢幕閱讀器才會把結果念出來 ——
   畫面沒有跳頁，不念的話使用者不知道剛剛發生了什麼：

    <AdminResultBar :announce="justHandled" ... />

=== 可以傳什麼 ===

label     發生了什麼。「已核准」「已停權」「檢舉成立 · 警告用戶」
meta      時間和處理人那行小字
detail    最底下那行，通常是原因。不傳就不顯示
tone      顏色，規則是「紅色只留給現在還擋著人的狀態」：
          primary 這件事辦成了（預設）—— 已核准、檢舉成立
          muted   這件事處理完了但沒有成果 —— 已駁回、檢舉不成立
          danger  現在正在生效、而且擋著人 —— 已停權
announce  只有剛處理完的那一次傳 true，重新整理後就不要傳

=== 陷阱 ===

label 要用「狀態在前」的寫法，「已停權」不要寫成「書芸 於⋯停權」——
中文把人名放句首會被讀成那個人被停權。處理人放 meta 那行就好。
-->

<script setup>
defineProps({
  label: {
    type: String,
    required: true,
  },
  meta: {
    type: String,
    default: '',
  },
  detail: {
    type: String,
    default: '',
  },
  tone: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'danger', 'muted'].includes(value),
  },
  announce: {
    type: Boolean,
    default: false,
  },
})
</script>

<template>
  <div class="result-bar" :class="`result-bar--${tone}`" :role="announce ? 'status' : null">
    <div class="result-bar__body">
      <span class="result-bar__label">{{ label }}</span>
      <span v-if="meta" class="result-bar__meta">{{ meta }}</span>
      <p v-if="detail" class="result-bar__detail">{{ detail }}</p>
    </div>

    <slot />
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;

.result-bar {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: $spacing-md;
  padding: $spacing-sm + $spacing-xs $spacing-md;
  // 四邊描邊、左邊加粗成一根槓。少了框線，白底那種會看起來像沒上色
  border: 1px solid;
  border-left-width: 4px;
  border-radius: $btn-radius-std;
  font-size: $p-sm-size;
  line-height: 1.6;
  color: $neutral-700;

  &--primary {
    border-color: $primary;
    background: $primary-100;

    .result-bar__label {
      color: $primary;
    }
  }

  &--danger {
    border-color: $color-danger;
    background: rgba($color-danger, 0.07);

    .result-bar__label {
      color: $color-danger;
    }
  }

  // 白底而不是灰底 —— 後台頁面底色就是灰的，用灰會整條融進背景
  &--muted {
    border-color: $neutral-500;
    background: $neutral-100;

    .result-bar__label {
      color: $neutral-800;
    }
  }

  &__body {
    min-width: 0;
  }

  // 三段都是 span 或 p，要各佔一行
  &__label {
    display: block;
    font-weight: $heading-weight;
  }

  &__meta {
    display: block;
    margin-top: $spacing-xs;
    font-size: $p-xs-size;
    color: $neutral-600;
  }

  &__detail {
    margin: $spacing-xs 0 0;
    color: $neutral-800;
  }

  // 按鈕壓在有底色的區塊上，預設的淺灰邊框會看不見
  :slotted(.admin-button) {
    flex-shrink: 0;
    border-color: $neutral-400;
  }
}
</style>
