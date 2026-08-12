<!--
AdminFilterTabs 後台篩選按鈕

一排橢圓按鈕，點哪顆就只看那一批資料。
例如「全部｜已上架｜已下架」、「待處理｜已核准｜已駁回」。

=== 怎麼用 ===

1. 準備一個變數記住現在選了哪一顆，還有一份選項清單

    import { ref } from 'vue';
    import AdminFilterTabs from '@/components/admin/AdminFilterTabs.vue';

    const current = ref('全部');

    const options = [
      { label: '全部', value: '全部' },
      { label: '已上架', value: '已上架' },
      { label: '已下架', value: '已下架' },
    ];

2. 放進畫面，用 v-model 接那個變數

    <AdminFilterTabs v-model="current" :options="options" />

3. 資料自己按 current 篩。這支只負責「告訴你使用者點了哪顆」，
   畫面上的資料要不要跟著變，是你自己寫的：

    const shown = computed(() =>
      current.value === '全部'
        ? books.value
        : books.value.filter((book) => book.status === current.value)
    );

=== 可以傳什麼 ===

v-model   現在選中的那個 value（必填）
options   選項清單（必填）。每一筆是 { label, value }，
          想在按鈕上顯示數字就多給一個 count

=== 陷阱 ===

- 換了篩選之後，分頁的頁碼要自己推回第 1 頁。
  在第 3 頁按下「已下架」而只剩一頁的話，畫面會變空白。

- count 給 0 的話按鈕上不會顯示數字（0 筆不用特別標）。
  真的想顯示 0，那筆改成字串 '0'。
-->

<script setup>
defineProps({
  modelValue: {
    type: String,
    required: true,
  },
  options: {
    type: Array,
    required: true,
  },
})

defineEmits(['update:modelValue'])
</script>

<template>
  <div class="filter-tabs">
    <!-- 是 <button> 不是連結：在同一頁換資料，不是跳頁。
         aria-pressed 會讓螢幕閱讀器念出「已選取」 -->
    <button
      v-for="option in options"
      :key="option.value"
      type="button"
      class="filter-tabs__btn"
      :class="{ 'filter-tabs__btn--active': option.value === modelValue }"
      :aria-pressed="option.value === modelValue"
      @click="$emit('update:modelValue', option.value)"
    >
      {{ option.label }}
      <span v-if="option.count" class="filter-tabs__count">{{ option.count }}</span>
    </button>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;

.filter-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: $spacing-sm;

  &__btn {
    display: flex;
    align-items: center;
    gap: $spacing-sm;
    height: 32px;
    padding: 0 $spacing-md;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-rnd;
    background: $neutral-100;
    color: $neutral-600;
    font-family: inherit;
    font-size: $p-xs-size;
    cursor: pointer;

    &:hover {
      border-color: $primary;
      color: $primary;
    }

    &--active {
      background: $primary;
      border-color: $primary;
      color: $neutral-100;
      font-weight: $heading-weight;

      &:hover {
        color: $neutral-100;
      }
    }
  }

  // 選中時數字要反過來配色，不然深青底上放深青字會看不見
  &__count {
    min-width: 20px;
    padding: 0 $spacing-xs;
    border-radius: $btn-radius-rnd;
    background: $neutral-300;
    color: $neutral-700;
    font-size: $p-xs-size;
    font-weight: $heading-weight;
    line-height: 20px;
    text-align: center;
  }

  &__btn--active &__count {
    background: $neutral-100;
    color: $primary;
  }
}
</style>
