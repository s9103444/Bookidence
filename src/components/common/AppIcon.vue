<!--
AppIcon 圖示元件

用法：
<AppIcon name="arrow-right" />

要加新圖示：在下面的 icons 物件裡多加一筆就好，不用建新檔案。
1. 從 Figma 匯出 SVG，或到 node_modules/pixelarticons/svg/ 找現成的
2. 複製它的 viewBox 和 path 的 d 值
3. 照現有格式加一筆，鍵名用小寫加連字號（例如 'arrow-left'）

注意事項：
- viewBox 一定要跟著來源走（Figma 匯出通常是 14x14，pixelarticons 是 24x24），
    寫錯會讓圖示變形或被裁掉
- fill 一律維持 currentColor，圖示才會自動跟著文字顏色變
- 尺寸由使用它的地方決定（例如按鈕裡是 14px），這裡不設定
-->

<script setup>
  // 全站共用的圖示資料。要加新圖示就在這裡多一筆。
    const icons={
        'arrow-right':{
            viewBox:'0 0 14 14',
            paths: ['M7 8H0V6H7V0H8.75V2H10.5V4H12.25V6H14V8H12.25V10H10.5V12H8.75V14H7V8Z'],
        },
    }

    defineProps({
        name:{
            type:String,
            required:true
        }
    })
</script>

<template>
    <svg
    v-if="icons[name]"
    xmlns="http://www.w3.org/2000/svg"
    :viewBox="icons[name].viewBox"
    fill="currentColor">

    <path
    v-for="(path,index) in icons[name].paths" :key="index" :d="path"></path>
    </svg>
</template>
