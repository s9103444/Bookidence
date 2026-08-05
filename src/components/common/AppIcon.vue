<!--
AppIcon 圖示元件

用法：
<AppIcon name="arrow-right" />
<AppIcon name="plus" :size="20" />      ← 要指定尺寸就加 :size（記得冒號）

目前有的圖示：
'arrow-right'  右箭頭（按鈕裡用的那個）
'arrow-left'   左箭頭
'plus'         加號

要加新圖示：在下面的 icons 物件裡多加一筆就好，不用建新檔案。
1. 從 Figma 匯出 SVG，或到 node_modules/pixelarticons/svg/ 找現成的
2. 複製它的 viewBox 和 path 的 d 值
3. 照現有格式加一筆，鍵名用小寫加連字號（例如 'arrow-left'）

注意事項：
- viewBox 一定要跟著來源走（Figma 匯出通常是 14x14，pixelarticons 是 24x24），
    寫錯會讓圖示變形或被裁掉
- fill 一律維持 currentColor，圖示才會自動跟著文字顏色變
- 尺寸用 size prop 指定，不寫的話是 14px。
  viewBox 不是尺寸，改它只會裁掉圖案，要調大小請用 size
-->

<script setup>
// 全站共用的圖示資料。要加新圖示就在這裡多一筆。
const icons = {
  "arrow-right": {
    viewBox: "0 0 14 14",
    paths: [
      "M7 8H0V6H7V0H8.75V2H10.5V4H12.25V6H14V8H12.25V10H10.5V12H8.75V14H7V8Z",
    ],
  },
  // arrow-left 是 arrow-right 的水平鏡射（x → 14 - x）。
  "arrow-left": {
    viewBox: "0 0 14 14",
    paths: [
      "M7 8H14V6H7V0H5.25V2H3.5V4H1.75V6H0V8H1.75V10H3.5V12H5.25V14H7V8Z",
    ],
  },
  plus: {
    viewBox: "0 0 22 22",
    paths: [
      "M22 10V12H21V13H13V21H12V22H10V21H9V13H1V12H0V10H1V9H9V1H10V0H12V1H13V9H21V10H22Z",
    ],
  },
  search: {
    viewBox: "0 0 24 24",
    paths: [
      "M22 22h-2v-2h2v2Zm-2-2h-2v-2h2v2Zm-6-2H6v-2h8v2Zm4 0h-2v-2h2v2ZM6 16H4v-2h2v2Zm10 0h-2v-2h2v2ZM4 14H2V6h2v8Zm14 0h-2V6h2v8ZM6 6H4V4h2v2Zm10 0h-2V4h2v2Zm-2-2H6V2h8v2Z",
    ],
  },
  "chevron-left": {
  viewBox: "0 0 9 16",
  paths: [
    "M3 9H4V10H5V11H6V12H7V13H8V14H9V15H8V16H7V15H6V14H5V13H4V12H3V11H2V10H1V9H0V7H1V6H2V5H3V4H4V3H5V2H6V1H7V0H8V1H9V2H8V3H7V4H6V5H5V6H4V7H3V9Z",
  ],
  },
  "chevron-right": {
    viewBox: "0 0 9 16",
    paths: [
      "M9 7V9H8V10H7V11H6V12H5V13H4V14H3V15H2V16H1V15H0V14H1V13H2V12H3V11H4V10H5V9H6V7H5V6H4V5H3V4H2V3H1V2H0V1H1V0H2V1H3V2H4V3H5V4H6V5H7V6H8V7H9Z",
    ],
  },
};

defineProps({
  name: {
    type: String,
    required: true,
  },
  size: {
    type: [String, Number],
    default: 14,
  },
});
</script>

<template>
  <svg
    v-if="icons[name]"
    xmlns="http://www.w3.org/2000/svg"
    :viewBox="icons[name].viewBox"
    :style="{ width: size + 'px', height: size + 'px' }"
    fill="currentColor"
  >
    <path
      v-for="(path, index) in icons[name].paths"
      :key="index"
      :d="path"
    ></path>
  </svg>
</template>
