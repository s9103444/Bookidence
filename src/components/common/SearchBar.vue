<template>
  <div class="search-bar" :class="[`bar-size--${size}`, `bar-color--${color}`]">
    <AppIcon class="app-icon" name="search" />
    <input type="search" placeholder="請輸入關鍵字" />
  </div>
</template>

<script>
import AppIcon from "../../components/common/AppIcon.vue";
export default {
  components: {
    AppIcon,
  },
  props: {
    size: {
      type: String,
      default: "sm",
      validator: (value) => ["sm", "md"].includes(value),
    },
    color: {
      type: String,
      default: "neutral",
      validator: (value) => ["brown", "primary", "neutral"].includes(value),
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/scss/abstracts/variables";

.search-bar {
  display: flex;
  align-items: center;
  gap: 5px;
  flex: 1;
  height: fit-content;
  border-radius: $btn-radius-std;
  padding-inline: $spacing-sm;
  padding-block: $spacing-xs;
  background-color: $neutral-200;
  & input {
    appearance: none;
    background-color: transparent;
    border: none;
    outline: none;
    font-size: inherit;
    &::placeholder {
      color: $neutral-500;
    }
    &::-webkit-search-cancel-button {
      display: none;
    }
  }
}
.app-icon {
  width: 20px;
  height: 20px;
}
.bar-size--sm {
  font-size: $label-sm-size;
}
.bar-size--md {
  font-size: $label-md-size;
}

.bar-color--brown {
  border: 1px solid $brown-light;
  color: $brown-light;
}
.bar-color--primary {
  border: 1px solid $primary;
  color: $primary;
}
.bar-color--neutral {
  border: 1px solid $neutral-400;
  color: $neutral-400;
}
</style>
