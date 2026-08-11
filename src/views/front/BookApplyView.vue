<script setup>
import AppIcon from '@/components/common/AppIcon.vue';
import AppButton from '@/components/common/AppButton.vue';
import {ref,computed} from 'vue';
import { useRouter } from 'vue-router';

const form=ref({
    title:'',
    author:'',
    isbn:'',
    link:'',
    reason:'',
});

// 每一格去掉頭尾空白後的樣子。判斷和送出都用這份，不用 form 原始的值
const trimmed=computed(()=>({
    title:form.value.title.trim(),
    author:form.value.author.trim(),
    isbn:form.value.isbn.trim(),
    link:form.value.link.trim(),
    reason:form.value.reason.trim(),
}));

// ISBN 是 10 碼或 13 碼，中間允許連字號。10 碼的最後一位可能是 X
function isValidIsbn(value){
    const digits=value.replace(/[-\s]/g,'');
    if(digits.length===13) return /^\d{13}$/.test(digits);
    if(digits.length===10) return /^\d{9}[\dX]$/i.test(digits);
    return false;
}

function isValidUrl(value){
    try{
        const url=new URL(value);
        return url.protocol==='http:'||url.protocol==='https:';
    }catch{
        return false;
    }
}

// 哪幾格使用者已經填過又離開了。沒碰過的格子不報錯，才不會一進頁面就滿江紅
const touched=ref({title:false,author:false,isbn:false,link:false});

function markTouched(field){
    touched.value[field]=true;
}

const errors=computed(()=>{
    const e={title:'',author:'',isbn:'',link:''};

    if(touched.value.title && !trimmed.value.title) e.title='請輸入書名';
    if(touched.value.author && !trimmed.value.author) e.author='請輸入作者';

    if(touched.value.isbn){
        if(!trimmed.value.isbn) e.isbn='請輸入 ISBN';
        else if(!isValidIsbn(trimmed.value.isbn)) e.isbn='ISBN 應為 10 碼或 13 碼，可含連字號';
    }

    if(touched.value.link && trimmed.value.link && !isValidUrl(trimmed.value.link)){
        e.link='請輸入完整網址，開頭要有 https://';
    }

    return e;
});

const canSubmit=computed(()=>{
    return trimmed.value.title
        && trimmed.value.author
        && isValidIsbn(trimmed.value.isbn)
        && (!trimmed.value.link || isValidUrl(trimmed.value.link));
});

const router = useRouter();

function goBack() {
    if (window.history.state.back) {
    router.back();
    } else {
    router.push('/search');
    }
};

const isSubmitted = ref(false);
function submit() {
    if (!canSubmit.value){
        return;
    }
    isSubmitted.value = true;
}

function applyAgain(){
    form.value={title:'',author:'',isbn:'',link:'',reason:''};
    touched.value={title:false,author:false,isbn:false,link:false};
    isSubmitted.value=false;
}
</script>

<template>
    <div class="book-apply">
    <header class="book-apply__header">
        <button type="button" class="book-apply__back" aria-label="返回上一頁" @click="goBack">
            <AppIcon name="chevron-left" :size="28"></AppIcon>
        </button>
        <h1 class="book-apply__title">申請好書推薦</h1>
    </header>

    <div v-if="isSubmitted" class="book-apply__done" role="status">
        <AppIcon name="check-circle" :size="64"></AppIcon>
        <h2 class="book-apply__done-title">已收到您的申請</h2>
        <p class="book-apply__done-text">審核通過後會通知您</p>
        <div class="book-apply__done-actions">
            <AppButton size="lg" @click="goBack">返回上一頁</AppButton>
            <AppButton size="lg" variant="outlined" @click="applyAgain">再申請一本</AppButton>
        </div>
    </div>


    <form v-else class="book-apply__form">
        <div class="book-apply__row">
        <div class="form-field">
            <label class="form-field__label" for="book-title">
            書名<span class="form-field__required" aria-hidden="true">*</span>
            </label>
            <input
            v-model="form.title"
            id="book-title"
            class="form-field__input"
            :class="{'form-field__input--error':errors.title}"
            type="text"
            name="title"
            placeholder="請在此輸入"
            required
            :aria-invalid="!!errors.title"
            aria-describedby="book-title-error"
            @blur="markTouched('title')">
            <p v-if="errors.title" id="book-title-error" class="form-field__error">{{ errors.title }}</p>
        </div>

        <div class="form-field">
            <label class="form-field__label" for="book-author">
            作者<span class="form-field__required" aria-hidden="true">*</span>
            </label>
            <input
            v-model="form.author"
            id="book-author"
            class="form-field__input"
            :class="{'form-field__input--error':errors.author}"
            type="text"
            name="author"
            placeholder="請在此輸入"
            required
            :aria-invalid="!!errors.author"
            aria-describedby="book-author-error"
            @blur="markTouched('author')">
            <p v-if="errors.author" id="book-author-error" class="form-field__error">{{ errors.author }}</p>
        </div>
        </div>

        <div class="form-field">
        <label class="form-field__label" for="book-isbn">
            ISBN<span class="form-field__required" aria-hidden="true">*</span>
        </label>
        <input
            v-model="form.isbn"
            id="book-isbn"
            class="form-field__input"
            :class="{'form-field__input--error':errors.isbn}"
            type="text"
            name="isbn"
            placeholder="例如 9789573317249"
            required
            :aria-invalid="!!errors.isbn"
            aria-describedby="book-isbn-error"
            @blur="markTouched('isbn')">
        <p v-if="errors.isbn" id="book-isbn-error" class="form-field__error">{{ errors.isbn }}</p>
        </div>

        <div class="form-field">
        <label class="form-field__label" for="book-link">書籍相關連結</label>
        <input
            v-model="form.link"
            id="book-link"
            class="form-field__input"
            :class="{'form-field__input--error':errors.link}"
            type="url"
            name="link"
            placeholder="例如博客來或出版社的書籍頁面網址"
            :aria-invalid="!!errors.link"
            aria-describedby="book-link-error"
            @blur="markTouched('link')">
        <p v-if="errors.link" id="book-link-error" class="form-field__error">{{ errors.link }}</p>
        </div>

        <div class="form-field">
        <label class="form-field__label" for="book-reason">申請理由</label>
        <textarea
            v-model="form.reason"
            id="book-reason"
            class="form-field__textarea"
            name="reason"
            placeholder="請說明您推薦這本書的理由"></textarea>
        </div>

        <div class="book-apply__actions">
        <AppButton size="lg" :disabled="!canSubmit" @click="submit">提交好書推薦申請</AppButton>
        </div>
    </form>
    </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

.book-apply {
  max-width: 1440px; // 設計稿基準寬度，跟搜索圖書頁、書籍詳情頁一致
  margin-inline: auto;
  padding: $spacing-xl;

  @include tablet {
    padding: $spacing-lg;
  }

  @include mobile {
    padding: $spacing-md;
  }
}

// ---------- 標題列 ----------
.book-apply__header {
  display: flex;
  align-items: center;
  gap: $spacing-xs;
}

.book-apply__back {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 46px; // 圖示只有 28px，容器撐大是為了讓手指點得到
  height: 46px;
  padding: 0;
  border: 0;
  background: none;
  cursor: pointer;
  color: $primary;
  border-radius: $btn-radius-std;
}

.book-apply__back:focus-visible {
  outline: 2px solid $primary-300;
  outline-offset: 2px;
}

.book-apply__title {
  font-size: $h4-size;
  font-weight: $heading-weight;
  line-height: $heading-line-height;
  letter-spacing: $letter-spacing-base;
  color: $primary;
}

// ---------- 送出成功 ----------
.book-apply__done {
  max-width: 888px;
  margin-inline: auto;
  margin-top: $spacing-xl;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: $spacing-md;
  text-align: center;
  color: $primary; // 勾勾圖示的顏色跟著這行走
}

.book-apply__done-title {
  font-size: $h5-size;
  font-weight: $heading-weight;
  line-height: $heading-line-height;
  letter-spacing: $letter-spacing-base;
  color: $primary;
}

.book-apply__done-text {
  font-size: $p-md-size;
  font-weight: $text-weight;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-500;
}

.book-apply__done-actions {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: $spacing-md;
  margin-top: $spacing-lg;
}

// ---------- 表單 ----------
.book-apply__form {
  max-width: 888px; // 設計稿表單寬度，比整頁窄，置中
  margin-inline: auto;
  margin-top: $spacing-xl;
  display: flex;
  flex-direction: column;
  gap: 31px; // 設計稿欄位之間的間距
}

// 書名 + 作者。桌機並排，平板以下折成上下
.book-apply__row {
  display: flex;
  gap: 22px; // 設計稿兩欄之間的間距

  @include tablet {
    flex-direction: column;
    gap: 31px; // 折行後要跟其他欄位的間距一致，否則這兩欄會擠在一起
  }
}

.book-apply__row .form-field {
  flex: 1;
  min-width: 0; // 不加的話 input 會撐開 flex 項目，兩欄變不等寬
}

// ---------- 單一欄位（label + 輸入框）----------
.form-field {
  display: flex;
  flex-direction: column;
  gap: $spacing-sm;
}

.form-field__label {
  font-size: $label-lg-size;
  font-weight: $text-weight;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-800;
}

// 必填星號。純裝飾，真正告訴螢幕閱讀器「這格必填」的是 input 的 required
.form-field__required {
  margin-left: $spacing-xs;
  color: $color-danger;
  font-weight: $heading-weight;
}

.form-field__input,
.form-field__textarea {
  width: 100%;
  padding: $spacing-md;
  border: 1px solid $neutral-400;
  border-radius: $btn-radius-std;
  font-family: inherit; // 表單元素預設不吃站上的字體，要自己接回來
  font-size: $p-md-size;
  font-weight: $text-weight;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-800;
}

.form-field__input {
  background-color: $neutral-100;
}

.form-field__input--error {
  border-color: $color-danger;
}

.form-field__textarea {
  height: 283px; // 設計稿高度
  background-color: $neutral-200; // 設計稿的大框是灰底，跟單行輸入框區分
  resize: none;

  @include tablet {
    height: 200px;
  }
}

.form-field__input::placeholder,
.form-field__textarea::placeholder {
  color: $neutral-400;
}

.form-field__input:focus-visible,
.form-field__textarea:focus-visible {
  outline: 2px solid $primary-300;
  outline-offset: 2px;
}

.form-field__error {
  font-size: $p-sm-size;
  font-weight: $text-weight;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $color-danger;
}

// ---------- 送出 ----------
.book-apply__actions {
  display: flex;
  justify-content: center;
  margin-top: 34px; // 加上表單本身的 31px gap，湊成設計稿的 65px
}
</style>
