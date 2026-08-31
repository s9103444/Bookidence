<!--
AppButton 按鈕元件

props:
size    : 'sm'（預設，高 40px）| 'lg'（高 60px）| 'xs'（高 30px）
color   : 'primary'（預設，深青）| 'secondary'（黃綠）| 'brown'（棕）
variant : 'filled'（預設，實心）| 'outlined'（透明底 + 描邊）
to      : 要跳到哪一頁。填了之後會變成連結，外觀不變

基本用法：
<AppButton>進入我的書房</AppButton>
<AppButton size="lg" color="secondary">建立公會</AppButton>
<AppButton size="xs" color="brown">新增藏書</AppButton>

按鈕如果是用來跳到另一頁，填 to，不用自己接 @click。
這樣使用者可以右鍵「在新分頁開啟」，也可以 Ctrl+點擊一次開好幾個。

to 填的是網址列上的那一段，開頭的斜線不能少：

<AppButton to="/search">搜索圖書</AppButton>
<AppButton to="/books/apply">申請推薦書籍</AppButton>

路徑裡有會變的部分（例如書籍 id），要多加一個冒號並改用反引號：

<AppButton :to="`/books/${bookId}`">查看詳情</AppButton>

  to 前面加冒號   代表裡面是要算出來的，不是固定文字
  引號改成反引號  反引號裡面才能用 ${ } 插入變數

沒加冒號的話，網址會變成 /books/${bookId} 這幾個字，找不到頁面。

router/front.js 裡的 path 是相對於外層的一小段（外層固定是 /），
to 要填的則是組合起來的完整路徑：

  路由表寫 path: "search"       →  to="/search"
  路由表寫 path: "books/apply"  →  to="/books/apply"
  路由表寫 path: "books/:id"    →  :to="`/books/${bookId}`"

不確定的時候，實際點過去一次，照抄網址列上的內容最準。

⚠️ 填了 to 就不要再用 disabled，不會有效果，點了照樣會跳。
   需要「條件符合才能跳」的按鈕，讓 to 在不能跳的時候是空的：

   <AppButton :to="canGo ? '/next' : null" :disabled="!canGo">下一步</AppButton>

   to 是空的時候會變回一般按鈕，disabled 就有效了。

停用：用 HTML 原生的 disabled，不用另外傳 prop
<AppButton disabled>已額滿</AppButton>
瀏覽器會自動擋掉點擊、Tab 跳過、螢幕閱讀器念出「已停用」。
不分原本什麼顏色，停用後一律變灰、變扁平。

圖示：直接寫在文字旁邊，位置由「寫的順序」決定
<AppButton>進入我的書房 <AppIcon name="arrow-right" /></AppButton>
<AppButton><AppIcon name="plus" /> 建立公會</AppButton>
圖示會自動變成 14px、顏色跟著文字走，不用額外設定。

⚠️ variant="outlined" 一定要看：
它的內部是「實心色塊」而不是透明的（CSS 的切角沒辦法做出真透明的描邊）。
放進頁面時要把 --btn-surface 設成那個位置的背景色，否則會是白底：

    .你的容器 { --btn-surface: #f5efe6; }

---------------------------------
丞相自有一招解決：
在該btn商家一個自定義calss，並附上
mix-blend-mode: multiply;
即可解決白底問題（僅限outlined按鈕使用）

---------------------------------

CSS 變數會往下繼承，設在容器上，裡面所有 outlined 按鈕都會吃到，
不用一顆一顆設。

如果按鈕正好壓在圖片或漸層上，填任何單一顏色都會露餡。
遇到那種情況先不要自己硬處理，跟我說一聲，要換一種畫法。

實作備註（不要當成 bug 修掉）：
- outlined 的「下緣」比其他三邊粗，是刻意的，用來做出按下去被壓扁的手感，
    跟實心按鈕的厚度是同一套視覺語言。設計師已確認可以。
- 手機等觸控裝置不套用 hover（避免點完顏色卡住），這是刻意的。
-->
<script setup>
import { computed } from "vue";
import { RouterLink } from "vue-router";

const props = defineProps({
  size: {
    type: String,
    default: "sm",
  },
  color: {
    type: String,
    default: "primary",
  },
  variant: {
    type: String,
    default: "filled",
  },
  to: {
    type: [String, Object],
    default: null,
  },
});

const classes = computed(() => [
  `app-button--${props.size}`,
  `app-button--${props.color}`,
  `app-button--${props.variant}`,
]);
</script>
<template>
  <RouterLink v-if="to" :to="to" class="app-button" :class="classes">
    <slot></slot>
  </RouterLink>
  <button v-else class="app-button" :class="classes" type="button">
    <slot></slot>
  </button>
</template>
<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.app-button {
  position: relative;
  isolation: isolate;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  max-width: 100%;
  letter-spacing: $letter-spacing-base;
  -webkit-user-select: none;
  user-select: none;
  -webkit-tap-highlight-color: transparent;
  transition: transform 0.12s ease-out;
  gap: 10px;
  color: var(--btn-text);
  text-decoration: none; // 變成連結時不要有底線
}

// 兩層共用的形狀
.app-button::before,
.app-button::after {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  z-index: -1;
  clip-path: polygon(var(--step-2) 0,
      calc(100% - var(--step-2)) 0,
      calc(100% - var(--step-2)) 12.25%,
      calc(100% - var(--step-1)) 12.25%,
      calc(100% - var(--step-1)) 33.333%,
      100% 33.333%,
      100% 66.667%,
      calc(100% - var(--step-1)) 66.667%,
      calc(100% - var(--step-1)) 87.75%,
      calc(100% - var(--step-2)) 87.75%,
      calc(100% - var(--step-2)) 100%,
      var(--step-2) 100%,
      var(--step-2) 87.75%,
      var(--step-1) 87.75%,
      var(--step-1) 66.667%,
      0 66.667%,
      0 33.333%,
      var(--step-1) 33.333%,
      var(--step-1) 12.25%,
      var(--step-2) 12.25%);
}

// 暗面：貼齊底部
.app-button::before {
  bottom: 0;
  transition: transform 0.12s ease-out;
  background-color: color-mix(in srgb, black 25%, var(--btn-color));
}

// 亮面：貼齊頂部
.app-button::after {
  top: 0;
  background-color: var(--btn-color);
}

//尺寸:xs
.app-button--xs {
  --step-1: 3px;
  --step-2: 9px;
  --sink: 1px;
  height: 30px;
  padding: 0 30px;
  font-size: $label-xs-size;
  --border-w: 1px;
}

.app-button--xs::before,
.app-button--xs::after {
  height: 28px;
}

// 尺寸：sm
.app-button--sm {
  --step-1: 4px; //step變數是維持像素風格邊緣的比例
  --step-2: 12px;
  --sink: 1px;
  height: 40px;
  padding: 0 36px;
  font-size: $label-sm-size;
  --border-w: 1.5px;
}

.app-button--sm::before,
.app-button--sm::after {
  height: 38px;
}

// 尺寸：lg
.app-button--lg {
  --step-1: 7px;
  --step-2: 18px;
  --sink: 3px;
  height: 60px;
  padding: 0 48px;
  font-size: $label-lg-size;
  --border-w: 2px;
}

.app-button--lg::before,
.app-button--lg::after {
  height: 56px;
}

//顏色:primary
.app-button--primary {
  --btn-color: #{$primary};
  --btn-text: #{$neutral-100};
}

//顏色:secondary
.app-button--secondary {
  --btn-color: #{$secondary};
  --btn-text: #{$primary};
}

//顏色:brown
.app-button--brown {
  --btn-color: #{$brown};
  --btn-text: #{$neutral-100};
}

//icon尺寸設定
.app-button :slotted(svg) {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
}

//outlined：透明底 + 描邊
.app-button--outlined {
  --btn-surface: #{$neutral-100};
  color: var(--btn-color);
}

// 外層：填描邊色
.app-button.app-button--outlined::before {
  top: 0;
  bottom: 0;
  height: auto;
  background-color: var(--btn-color);
}

// 內層：四邊各縮 --border-w，填底色
.app-button.app-button--outlined::after {
  top: var(--border-w);
  left: var(--border-w);
  right: var(--border-w);
  bottom: calc(var(--border-w) + var(--sink));
  height: auto;
  background-color: var(--btn-surface);
  transition: bottom 0.12s ease-out;
}

//hover顏色變更 (僅限有滑鼠裝置)
@media (hover: hover) {

  // 實心按鈕：整個變深
  .app-button:hover:not(:disabled):not(.app-button--outlined)::after {
    background-color: color-mix(in srgb, black 30%, var(--btn-color));
  }

  // outlined：內部染上較淡的主色
  .app-button.app-button--outlined:hover:not(:disabled)::after {
    background-color: color-mix(in srgb,
        var(--btn-color) 12%,
        var(--btn-surface));
  }
}

//action:按下去的瞬間，亮面往下，厚度變薄
.app-button:active:not(:disabled) {
  transform: translateY(var(--sink));
  transition-duration: 0s;
}

.app-button:active:not(:disabled)::before {
  transform: translateY(calc(var(--sink) * -1));
  transition-duration: 0s;
}

@media (prefers-reduced-motion: reduce) {

  .app-button,
  .app-button::before {
    transition: none;
  }
}

// action:outlined
.app-button.app-button--outlined:active::before {
  transform: none;
}

.app-button.app-button--outlined:active::after {
  bottom: var(--border-w);
  transition-duration: 0s;
}

//disabled:一律變灰、扁平
.app-button:disabled {
  color: $neutral-500;
  cursor: not-allowed;
}

.app-button:disabled::after,
.app-button:disabled::before {
  background-color: $neutral-400;
}
</style>
