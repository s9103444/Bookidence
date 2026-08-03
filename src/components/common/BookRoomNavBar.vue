<template>
  <button class="btn" :class="[`nav-color--${color}`, `nav-size--${size}`]">
    <AppIcon class="icon" name="arrow-left" size="16" /><span
      ><slot></slot
    ></span>
  </button>
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
      validator: (value) => ["sm", "xs", "md"].includes(value),
    },
    color: {
      type: String,
      default: "primary",
      validator: (value) => ["brown", "primary"].includes(value),
    },
  },
};
</script>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

button {
  display: inline-flex;
  align-items: center;
  gap: $spacing-xs;
  background: transparent;
  border: none;
  padding: 0;
  cursor: pointer;
  font-weight: $heading-weight;
  letter-spacing: $letter-spacing-base;
  -webkit-tap-highlight-color: transparent;
}

// 尺寸：xs
.nav-size--xs {
  font-size: $label-xs-size;
  :deep(svg) {
    width: 16px;
    height: 16px;
  }
}

// 尺寸：sm
.nav-size--sm {
  font-size: $label-sm-size;
  :deep(svg) {
    width: 20px;
    height: 20px;
  }
}

// 尺寸：md
.nav-size--md {
  font-size: $label-md-size;
  :deep(svg) {
    width: 24px;
    height: 24px;
  }
}

// 顏色：brown
.nav-color--brown {
  color: $brown-light;
}

// 顏色：primary
.nav-color--primary {
  color: $primary;
}
.icon {
  transition: transform 0.2s ease;
}

// hover 顏色變更（僅限有滑鼠裝置）
@media (hover: hover) {
  .nav-color--brown:hover {
    color: $brown;
  }
  .nav-color--primary:hover {
    color: $primary-500;
  }
  .btn:hover .icon {
    transform: translateX(-2px);
  }
}
</style>
