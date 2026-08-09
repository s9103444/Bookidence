<!--
ReportReviewForm 檢舉心得的表單內容

注意：這支「不含彈窗」，只有表單本身。
要跳出彈窗的話，外面用 AppModal 包起來（見下面的用法）。
拆開的原因：彈窗的殼大家共用，表單欄位各自不同。

=== 怎麼用 ===

<AppModal v-model="isReportOpen" title="檢舉申請">
 <ReportReviewForm
    :reported-name="被檢舉人的名字"
    :reported-id="被檢舉人的編號"
    @submit="handleReportSubmit" />
</AppModal>

=== 可以傳什麼 ===

reported-name  被檢舉人的名字，例：我是壞蛋
reported-id    被檢舉人的編號，例：BKD00246

=== 送出的時候會給你什麼 ===

@submit 會拿到一包資料：

  { reason: '人身攻擊', detail: '使用者打的補充說明' }

reason  使用者選的檢舉原因（四選一，一定有值）
detail  補充說明，可能是空字串（資料表允許不填）

彈窗要不要關、要不要跳提示，由外面的人自己決定，這支不管。

=== 幾個已經處理好的規則 ===

- 沒選原因時，送出鈕是灰的按不下去（避免使用者什麼都沒選就送出檢舉）
- 補充說明是選填，不會擋你送出
- 補充說明最多 500 字，打到上限就打不進去了（配合資料表的長度限制）

=== 跟設計稿刻意不一樣的地方 ===

設計稿截圖上「人身攻擊」是亮著的，但這裡預設四顆都不選。
因為預設選好的話，使用者沒看清楚就按送出，等於被系統代替他
指控對方人身攻擊。各大平台的檢舉表單都不預選。待設計師確認。

=== 之後接後端要補的 ===

送出的資料還缺兩個欄位：被檢舉的心得 id（b_thought_id）
和被檢舉人的 user_id。目前畫面上的編號（BKD00246）是給人看的，
不是資料庫的 id，接 API 時要另外傳。

⚠️ 送出鈕現在是用 @click 接的，不是走表單原生的送出。
    因為 AppButton 裡面寫死了 type="button"，不會觸發表單送出。
    哪天 AppButton 支援 type="submit" 了，要記得把 @click 拿掉，
    否則會變成點一下送出兩次。
-->

<script setup>
    import {ref} from 'vue';
    import AppButton from '@/components/common/AppButton.vue';

    defineProps({
        reportedName:{
            type:String,
            default:''
        },
        reportedId:{
            type:String,
            default:''
        },
    });

    const emit=defineEmits(['submit']);

    const reasons=['人身攻擊','廣告垃圾資訊','不當內容','抄襲 / 侵權'];

    const selectedReason=ref('');
    const detail=ref('');

    function submit(){
        emit('submit',{
            reason:selectedReason.value,
            detail:detail.value
        });
    }
</script>

<template>
    <form class="report-form" @submit.prevent="submit">
        <p class="report-form__reported">
            <span class="report-form__label">被檢舉人</span>
            <span class="report-form__name">{{ reportedName }}</span>
            <span class="report-form__id">{{ reportedId }}</span>
        </p>

        <fieldset class="report-form__field">
        <legend class="report-form__label">選擇檢舉類型</legend>

        <div class="report-form__reasons">
            <template v-for="(reason, index) in reasons" :key="reason">
            <input
                :id="'reason-' + index"
                v-model="selectedReason"
                class="report-form__radio"
                type="radio"
                name="reason"
                :value="reason">
            <label :for="'reason-' + index" class="report-form__chip">
                {{ reason }}
            </label>
            </template>
        </div>
        </fieldset>

        <div class="report-form__field">
        <label class="report-form__label" for="report-detail">檢舉詳細說明</label>
        <textarea
            id="report-detail"
            v-model="detail"
            class="report-form__textarea"
            maxlength="500"
            placeholder="請輸入原因，以便後台管理員能更快理解您的需求"></textarea>
        </div>

        <div class="report-form__actions">
        <AppButton size="lg" :disabled="!selectedReason" @click="submit">
            送出檢舉申請
        </AppButton>
        </div>
    </form>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;

.report-form {
  display: flex;
  flex-direction: column;
  gap: 20px; // 設計稿三個區塊之間的間距
}

// 三個區塊的小標題（被檢舉人／選擇檢舉類型／檢舉詳細說明）
.report-form__label {
  font-size: $label-md-size;
  font-weight: $text-weight;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $primary;
}

// 被檢舉人那一行
.report-form__reported {
  display: flex;
  align-items: baseline; // 名字 16px、編號 12px，對齊底線才不會忽高忽低
  flex-wrap: wrap;
  gap: $spacing-sm $spacing-md;
}

.report-form__name {
  font-size: $p-md-size;
  font-weight: $text-weight;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-800;
  margin-right: -$spacing-sm; // 名字與編號比其他欄位靠得更近，抵銷一半的 gap
}

.report-form__id {
  font-size: $p-xs-size;
  font-weight: $text-weight;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-500;
}

// fieldset 和 div 共用。fieldset 預設有外框和內距，要清掉
.report-form__field {
  display: flex;
  flex-direction: column;
  gap: $spacing-sm;
  margin: 0;
  padding: 0;
  border: 0;
  min-inline-size: 0; // fieldset 預設不會縮小，不清掉會撐破手機版
}

.report-form__reasons {
  position: relative; // 藏起來的 radio 定位在這個範圍內
  display: flex;
  flex-wrap: wrap; // 手機放不下時自動換行
  gap: $spacing-md;
}

// 把 radio 藏起來，但保留鍵盤操作與螢幕閱讀器。
// 不能用 display: none 或 visibility: hidden，那會讓它整個消失
.report-form__radio {
  position: absolute;
  width: 1px;
  height: 1px;
  margin: -1px;
  padding: 0;
  border: 0;
  overflow: hidden;
  clip-path: inset(50%);
  white-space: nowrap;
}

// 看得到、點得到的是這個 label
.report-form__chip {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 10px; // 設計稿的 chip 內距
  border: 1px solid $neutral-500;
  border-radius: $btn-radius-std;
  background-color: transparent;
  font-size: $label-md-size;
  font-weight: $text-weight;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-500;
  cursor: pointer;
  user-select: none; // 連點兩下不要選到文字
}

// 「被選中的 radio 旁邊那個 label」。radio 與 label 必須緊鄰，+ 才找得到
.report-form__radio:checked + .report-form__chip {
  border-color: $primary;
  background-color: $primary;
  color: $neutral-100;
}

// radio 被藏起來了，鍵盤使用者要看得出焦點在哪一顆
.report-form__radio:focus-visible + .report-form__chip {
  outline: 2px solid $primary-300;
  outline-offset: 2px;
}

@media (hover: hover) {
  .report-form__radio:not(:checked) + .report-form__chip:hover {
    border-color: $primary;
    color: $primary;
  }
}

.report-form__textarea {
  width: 100%;
  height: 111px; // 設計稿高度
  padding: $spacing-md;
  border: 1px solid $neutral-400;
  border-radius: $btn-radius-std;
  background-color: $neutral-200;
  font-family: inherit; // textarea 預設不是站上的字體，要自己接回來
  font-size: $p-sm-size;
  font-weight: $text-weight;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-800;
  resize: vertical; // 只讓使用者上下拉，左右拉會破版
}

.report-form__textarea::placeholder {
  color: $neutral-400;
}

.report-form__textarea:focus-visible {
  outline: 2px solid $primary-300;
  outline-offset: 2px;
}

.report-form__actions {
  display: flex;
  justify-content: center;
  margin-top: 20px; // 加上 form 本身的 20px gap，湊成設計稿的 40px
}
</style>