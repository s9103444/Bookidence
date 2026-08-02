<!-- 書提分類用標籤，使用範本： -->

<!-- <BookCategoryTag class="book-category-tag" size="sm" color="primary" variant="outlined"
>內容打在這，之後用後端抓</BookCategoryTag>

可用樣式：
tag-size >>>  "sm", "xs", "md"
tag-color >>> "brown", "primary"
tag-variant >>> "outlined", "filled"
-->

<template>
  <span
    class="book-category-tag"
    :class="[
      `tag-size--${size}`,
      `tag-color--${color}`,
      `tag-variant--${variant}`,
    ]"
  >
    <slot />
  </span>
</template>

<script>
export default {
  props: {
    size: {
      type: String,
      default: "sm",
      validator: (value) => ["sm", "xs", "md"].includes(value),
    },
    color: {
      type: String,
      default: "primary",
      validator: (value) => ["brown", "primary"].includes(value),
    },
    variant: {
      type: String,
      default: "outlined",
      validator: (value) => ["outlined", "filled"].includes(value),
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/scss/abstracts/variables";

// 標籤本身共用樣式
.book-category-tag {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 1px solid transparent;
  border-radius: $btn-radius-std;
  padding-inline: $spacing-sm;
  padding-block: $spacing-xxs;
  width: fit-content;
}

// 標籤字體大小
.tag-size--sm {
  font-size: $label-sm-size;
}
.tag-size--xs {
  font-size: $label-xs-size;
}
.tag-size--md {
  font-size: $label-md-size;
}

// 標籤顏色（outlined 樣式下生效：邊框 + 文字色，無底色）
.tag-color--primary {
  color: $primary-500;
  border-color: $primary-500;
}
.tag-color--brown {
  color: $brown-light;
  border-color: $brown-light;
}

// 標籤樣式：outlined（預設，維持透明底、依 color 顯示邊框與文字色）
.tag-variant--outlined {
  background-color: transparent;
}

// 標籤樣式：filled（文字固定用淺色，背景色依 color prop 變化）
.tag-variant--filled {
  color: $neutral-100;
}
.tag-variant--filled.tag-color--primary {
  background-color: $primary-500;
  border-color: $primary-500;
}
.tag-variant--filled.tag-color--brown {
  background-color: $brown-light;
  border-color: $brown-light;
}
</style>
