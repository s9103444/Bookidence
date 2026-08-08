<!--
AppIcon 圖示元件

用法：
<AppIcon name="arrow-right" />
<AppIcon name="plus" :size="20" />      ← 要指定尺寸就加 :size（記得冒號）

目前有的圖示：
'arrow-right'   右箭頭（按鈕裡用的那個）
'arrow-left'    左箭頭
'chevron-left'  左尖角（輪播上一頁）
'chevron-right' 右尖角（輪播下一頁）
'plus'          加號
'search'        放大鏡
'heart'         愛心
'form-edit'     表單加筆（申請填寫表單）
'check-circle'  實心圓打勾（審核通過）
'book'          闔上的書
'user'          一個人
'users'         兩個人（人數）
'map-pin'       地點釘
'thumbs-up'     讚
'flag'          旗子（檢舉）

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
  heart: {
    viewBox: "0 0 24 24",
    paths: [
      "M13 22h-2v-2h2v2Zm-2-2H9v-2h2v2Zm4 0h-2v-2h2v2Zm-6-2H7v-2h2v2Zm8 0h-2v-2h2v2ZM7 16H5v-2h2v2Zm12 0h-2v-2h2v2ZM5 14H3v-2h2v2Zm16 0h-2v-2h2v2ZM3 12H1V6h2v6Zm20 0h-2V6h2v6ZM13 8h-2V6h2v2ZM5 6H3V4h2v2Zm6 0H9V4h2v2Zm4 0h-2V4h2v2Zm6 0h-2V4h2v2ZM9 4H5V2h4v2Zm10 0h-4V2h4v2Z",
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
  "form-edit": {
    viewBox: "0 0 28 28",
    paths: [
      "M27.4226 8.75188V11.0858H26.2557V12.2527H25.0888V11.0858H23.9219V9.91882H22.7549V8.75188H23.9219V7.58496H26.2557V8.75188H27.4226Z",
      "M25.0883 12.2518V13.4187H23.9214V14.5857H22.7544V15.7526H21.5875V16.9195H20.4206V18.0865H19.2537V19.2534H18.0867V20.4203H16.9198V21.5872H13.4189V18.0865H14.5858V16.9195H15.7529V15.7526H16.9198V14.5857H18.0867V13.4187H19.2537V12.2518H20.4206V11.0849H21.5875V9.91797H22.7544V11.0849H23.9214V12.2518H25.0883Z",
      "M20.4209 2.91694V1.75H2.91694V2.91694H1.75V26.2556H2.91694V27.4225H20.4209V26.2556H21.5879V19.254H20.4209V20.4209H19.254V21.5879H18.0871V22.7548H12.2523V16.9202H13.4193V15.7531H14.5862V14.5862H15.7531V13.4193H16.9202V12.2523H18.0871V11.0855H19.254V9.91852H20.4209V8.75159H21.5879V2.91694H20.4209ZM19.254 6.41773H4.08386V5.25079H19.254V6.41773ZM16.9202 9.91852H4.08386V8.75159H16.9202V9.91852ZM9.91852 20.4209H4.08386V19.254H9.91852V20.4209ZM4.08386 16.9202V15.7531H11.0855V16.9202H4.08386ZM4.08386 13.4193V12.2523H14.5862V13.4193H4.08386Z",
    ],
  },
  "check-circle": {
    viewBox: "0 0 33 33",
    paths: [
      "M30.25 12.375V9.625H28.875V6.875H27.5V5.5H26.125V4.125H23.375V2.75H20.625V1.375H12.375V2.75H9.625V4.125H6.875V5.5H5.5V6.875H4.125V9.625H2.75V12.375H1.375V20.625H2.75V23.375H4.125V26.125H5.5V27.5H6.875V28.875H9.625V30.25H12.375V31.625H20.625V30.25H23.375V28.875H26.125V27.5H27.5V26.125H28.875V23.375H30.25V20.625H31.625V12.375H30.25ZM24.75 16.5H23.375V17.875H22V19.25H20.625V20.625H19.25V22H17.875V23.375H16.5V24.75H13.75V23.375H12.375V22H11V20.625H9.625V19.25H8.25V16.5H9.625V15.125H12.375V16.5H13.75V17.875H16.5V16.5H17.875V15.125H19.25V13.75H20.625V12.375H22V11H24.75V12.375H26.125V15.125H24.75V16.5Z",
    ],
  },
  book: {
    viewBox: "0 0 28 28",
    paths: [
      "M23.333 19.8332H24.4997V18.6665H25.6663V2.33317H24.4997V1.1665H4.66634V2.33317H3.49967V3.49984H2.33301V24.4998H3.49967V25.6665H4.66634V26.8332H24.4997V25.6665H25.6663V24.4998H24.4997V23.3332H23.333V19.8332ZM20.9997 24.4998H5.83301V23.3332H4.66634V20.9998H5.83301V19.8332H20.9997V24.4998ZM4.66634 3.49984H13.9997V11.6665H15.1663V10.4998H16.333V9.33317H17.4997V10.4998H18.6663V11.6665H19.833V3.49984H23.333V17.4998H4.66634V3.49984Z",
    ],
  },
  user: {
    viewBox: "0 0 24 24",
    paths: [
      "M9 2h6v2H9zm0 8h6v2H9zm6-6h2v6h-2zM7 4h2v6H7zM4 18h2v4H4zm14 0h2v4h-2zM8 14h8v2H8zm-2 2h2v2H6zm10 0h2v2h-2z",
    ],
  },
  users: {
    viewBox: "0 0 24 24",
    paths: [
      "M5 2h6v2H5zm10 0h4v2h-4zM5 10h6v2H5zm10 0h4v2h-4zm4-6h2v6h-2zm-8 0h2v6h-2zM3 4h2v6H3zM0 18h2v4H0zm14 0h2v4h-2zm8 0h2v4h-2zM4 14h8v2H4zm12 0h4v2h-4zM2 16h2v2H2zm10 0h2v2h-2zm8 0h2v2h-2z",
    ],
  },
  "map-pin": {
    viewBox: "0 0 24 24",
    paths: [
      "M7 2h10v2H7zM5 4h2v2H5zm14 0h-2v2h2zM7 17h2v2H7zm2 2h2v2H9zm6-2h2v2h-2zm-2 2h2v2h-2zm-2 2h2v2h-2zm-6-7h2v3H5zm12 0h2v3h-2zM3 6h2v8H3zm18 0h-2v8h2zM10 6h4v2h-4zM8 8h2v4H8zm2 4h4v2h-4zm4-4h2v4h-2z",
    ],
  },
  "thumbs-up": {
    viewBox: "0 0 24 24",
    paths: [
      "M2 12h2v8H2zm2 8h14v2H4zm14-4h2v4h-2zm2-4h2v4h-2zm-6-2h6v2h-6zm0-2h2v2h-2zm2-4h2v4h-2zm-2-2h2v2h-2zm-2 2h2v2h-2zm-2 2h2v2h-2zM8 8h2v2H8zm-4 2h4v2H4zm2 2h2v8H6z",
    ],
  },
  flag: {
    viewBox: "0 0 24 24",
    paths: [
      "M4 2h2v20H4z",
      "M4 4h16v2H4zm12 2h2v2h-2zm-2 2h2v2h-2zm2 2h2v2h-2zM4 12h16v2H4z",
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
