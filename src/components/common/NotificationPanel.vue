<script>
import AppButton from "./AppButton.vue";

export default {
  name: "NotificationPanel",
  components: { AppButton },
  props: {
    notifications: {
      type: Array,
      default: () => [
        "恭喜加入Bookidence小鎮，一起來探索美好的共讀世界",
        "愛玉讀本期讀物更新：ＸＸＸ",
        "您近期申請的書籍已由管理員確認並幫您做上架囉，快到書房登錄書籍吧！",
      ],
    },
  },
};
</script>

<template>
  <div class="notification-panel">
    <div class="notification-panel__header">通知</div>
    <ul class="notification-panel__list">
      <li
        v-for="(message, index) in notifications"
        :key="index"
        class="notification-panel__item"
      >
        <span class="notification-panel__thumb"></span>
        <p class="notification-panel__text">{{ message }}</p>
      </li>
    </ul>
    <AppButton
      size="sm"
      color="primary"
      to="/member/notice"
      class="notification-panel__btn"
      >查看通知中心</AppButton
    >
  </div>
</template>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.notification-panel {
  display: flex;
  flex-direction: column;
  width: 320px;
  background: $neutral-100;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

.notification-panel__header {
  padding: $spacing-md;
  font-size: $p-md-size;
  font-weight: $heading-weight;
  color: $neutral-800;
}

.notification-panel__list {
  list-style: none;
  margin: 0;
  padding: 0;
}

.notification-panel__item {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
  padding: $spacing-sm $spacing-md;

  &:hover {
    background: $neutral-200;
  }
}

.notification-panel__thumb {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: $neutral-300;
}

.notification-panel__text {
  margin: 0;
  font-size: $p-sm-size;
  line-height: $text-line-height;
  color: $neutral-800;
}

.notification-panel__btn {
  width: calc(100% - #{$spacing-md} * 2);
  margin: $spacing-sm $spacing-md $spacing-md;
}
</style>
