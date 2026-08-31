<!--
AppModal 彈窗（全站共用）

這支只做「外面那一層殼」：
背景變暗的半透明遮罩、中間的白色卡片、右上角的 X、標題。
中間要放什麼，由拿去用的人自己決定。

=== 怎麼用 ===

要做兩件事：準備一個開關，然後把它打開。

1. 準備開關（開關就是一個 true / false，預設 false 代表關著）

    import { ref } from 'vue';
    import AppModal from '@/components/common/AppModal.vue';

    const isReportOpen = ref(false);

2. 想打開的時候，把開關改成 true

    <button @click="isReportOpen = true">檢舉</button>

3. 把彈窗放進畫面，中間夾你要顯示的東西

    <AppModal v-model="isReportOpen" title="檢舉申請">
    這裡夾的東西會出現在卡片中間，放什麼都可以
    </AppModal>

=== 可以傳什麼 ===

v-model  開關，接你自己那個 true / false 的變數（必填）
title    標題文字。不傳就不會顯示標題

=== 關閉不用處理 ===

以下三種都會自動把開關變回 false，你不用寫任何關閉的程式：
按右上角的 X、按鍵盤 Esc、點卡片外面暗掉的地方。

（點卡片「裡面」不會關掉，放心在裡面放輸入框跟按鈕。）

=== 幾個你可能會擔心的點 ===

Q：彈窗要寫在畫面的哪個位置？要不要放到最外層？
A：放哪都可以，跟著相關的按鈕一起放最好讀。
   遮罩一定會蓋滿整個螢幕，不會被外層容器的留白或寬度限制切到。

Q：彈窗打開時，後面的頁面還能滑嗎？
A：不能，已經鎖住了。關掉會自動解鎖。

Q：會不會被 header 或其他東西蓋住？
A：目前設 z-index: 1000，比站上其他東西都高。
   萬一哪天真的被蓋住，就是去 SCSS 裡把這個數字調大。

=== 還沒做的部分（不影響使用，之後有空再補）===

- 彈窗打開時，用鍵盤一直按 Tab 有可能跑到後面的頁面上。
    要擋掉需要另外寫一段程式，一般用滑鼠的使用者不會遇到。
- 一次只預期開一個彈窗。同時開兩個會直接疊在一起，沒有處理。
-->

<script setup>
    import { watch,onMounted,onUnmounted } from 'vue';
    import AppIcon from '@/components/common/AppIcon.vue';
    
    const props=defineProps({
        modelValue:{
            type:Boolean,
            default:false
        },
        title:{
            type:String,
            default:''
        },
    });

    const emit=defineEmits(['update:modelValue']);
    function close(){
        emit('update:modelValue',false);
    }

    watch(()=>
        props.modelValue,(isOpen)=>{
            document.body.style.overflow=isOpen?'hidden':'';
        });
    function onKeydown(e){
        if(e.key==='Escape'){
            close();
        }
    };

    onMounted(()=>window.addEventListener("keydown",onKeydown));
    onUnmounted(()=>{
        window.removeEventListener("keydown",onKeydown);
        document.body.style.overflow='';
    });

</script>

<template>
    <Teleport to="body">
        <div v-if="modelValue" class="app-modal" @click.self="close">
            <div class="app-modal__card" role="dialog" aria-modal="true">
                <button
                type="button"
                class="app-modal__close"
                aria-label="關閉"
                @click="close">
                <AppIcon name="close" :size="24"></AppIcon>
                </button>
                <h2 v-if="title" class="app-modal__title">{{ title }}</h2>
                <slot></slot>
            </div>
        </div>
    </Teleport>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

// 遮罩：蓋滿整個螢幕。靠 Teleport 掛在 body 底下，才不會被外層容器切到
.app-modal {
  position: fixed;
  inset: 0;
  z-index: 1000; // 全站目前沒有 z-index 變數，先用這個值蓋過 header
  display: flex;
  align-items: center;
  justify-content: center;
  padding: $spacing-md; // 手機上讓卡片離螢幕邊緣有點距離
  background-color: rgba(0, 0, 0, 0.5);
  overflow-y: auto; // 內容比螢幕高時整個遮罩可以捲
}

.app-modal__card {
  position: relative; // 關閉鈕定位的基準
  display: flex;
  flex-direction: column;
  gap: 10px; // 設計稿標題與內容的間距
  width: 100%;
  max-width: 582px; // 設計稿卡片寬度
  max-height: 90vh;
  padding: $spacing-xl;
  background-color: $neutral-100;
  overflow-y: auto;

  @include tablet {
    padding: $spacing-lg;
  }

  @include mobile {
    padding: $spacing-md;
  }
}

.app-modal__close {
  position: absolute;
  top: $spacing-sm;
  right: $spacing-sm;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background-color: transparent;
  color: $neutral-800;
  cursor: pointer;
}

.app-modal__title {
  font-size: $h6-size;
  font-weight: $heading-weight;
  line-height: $heading-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-800;
  padding-right: $spacing-xl; // 標題長的時候不要壓到右上角的關閉鈕
}
</style>