<!--
AdminStatusTag 後台狀態標籤

小小的圓角標籤，用來顯示「已上架」「已駁回」「心理成長」這類短字。

=== 怎麼用 ===

    <AdminStatusTag label="已上架" tone="solid" />
    <AdminStatusTag label="心理成長" />

=== 可以傳什麼 ===

label   標籤上的字（必填）
tone    三種都是透明底，只有顏色深淺不同：
        'outline'（預設）灰色描邊，用在分類、類型這種純資訊
        'solid'         深青描邊深青字，用在「現在生效中」的狀態，例如已上架
        'muted'         淺灰描邊淺灰字，用在「已停用、已結束」的狀態，例如已下架

=== 陷阱 ===

⚠️ 標籤一律不要填滿底色。後台的規則是「有底色的可以點、描邊的是資訊」。

⚠️ 這支跟前台的 BookCategoryTag 不要混用，也不要合併。
   前台那支標的是「這本書屬於哪一類」，是書本身的屬性；
   這支標的是「這筆資料現在什麼狀態」，會隨著管理員操作改變。
   兩件事以後外觀一定會各自變，合在一起改一個就會弄壞另一個。
-->

<script setup>
defineProps({
  label: {
    type: String,
    required: true,
  },
  tone: {
    type: String,
    default: 'outline',
  },
})
</script>

<template>
  <span class="admin-tag" :class="`admin-tag--${tone}`">{{ label }}</span>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;

.admin-tag {
  display: inline-block;
  padding: $spacing-xs $spacing-sm;
  border: 1px solid transparent;
  border-radius: $btn-radius-rnd;
  font-size: $label-xxs-size;
  font-weight: $heading-weight;
  line-height: 1.4;
  white-space: nowrap;

  &--outline {
    border-color: $neutral-400;
    color: $neutral-600;
  }

  &--solid {
    border-color: $primary;
    color: $primary;
  }

  &--muted {
    border-color: $neutral-300;
    color: $neutral-400;
  }
}
</style>
