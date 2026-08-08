<template>
  <div class="search-bar" :class="[`bar-size--${size}`, `bar-color--${color}`]">
    <AppIcon class="app-icon" name="search" :size="iconSize" />
    <input type="search" :placeholder="placeholder" />
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
    placeholder: {
      type: String,
      default: "請輸入關鍵字",
    },
  },
  computed: {
    iconSize() {
      return this.size === "md" ? 24 : 14;
    },
  },
};
</script>

<style lang="scss" scoped>
@use "@/assets/scss/abstracts/variables" as *;

.search-bar {
  display: flex;
  align-items: center;
  flex: 1;

  height: fit-content;
  border-radius: $btn-radius-std;
  & input {
    appearance: none;
    background-color: transparent;
    border: none;
    outline: none;
    font-size: inherit;
    &::placeholder {
      color: $neutral-500;
      font-size: $p-xs-size;
    }
    &::-webkit-search-cancel-button {
      display: none;
    }
  }
}

// 內距與間距跟著 size 走
.bar-size--sm {
  font-size: $label-sm-size;
  gap: 5px;
  padding: $spacing-sm;
}
.bar-size--md {
  font-size: $label-md-size;
  gap: $spacing-md;
  padding: $spacing-md;
  & input {
    flex: 1;
    &::placeholder {
      color: $neutral-400;
      font-size: $label-md-size;
    }
  }
}

// 底色跟著 color 走
.bar-color--brown {
  border: 1px solid $brown-light;
  color: $brown-light;
  background-color: $neutral-200;
}
.bar-color--primary {
  border: 1px solid $primary;
  color: $primary;
  background-color: $neutral-200;
}
.bar-color--neutral {
  border: 1px solid $neutral-400;
  color: $neutral-400;
  background-color: $neutral-100;
}
</style>
