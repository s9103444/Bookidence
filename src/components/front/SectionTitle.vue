<script setup>
    import sectionBook from '../../assets/icons/section-book.png'
</script>
<template>
    <h2 class="section-title">
        <img class="section-title__icon" :src="sectionBook" alt="">
      <slot></slot>
    </h2>
</template>
<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;
.section-title {
    display: flex;
    align-items: center;
    gap: $spacing-sm;
    font-size: $h5-size;
    font-weight: $heading-weight;
    line-height: $heading-line-height;
    color: $primary;
  }
  
  .section-title__icon {
    flex-shrink: 0;
    width: 29px;
    height: 28px;
  }
  </style>