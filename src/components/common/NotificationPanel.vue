<script>
import AppButton from "./AppButton.vue";
import { API_BASE } from '@/common/api';
import { mapState } from 'pinia';
import { useUserStore } from '@/stores/user';
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import AppIcon from "@/components/common/AppIcon.vue";
import iconActivity from "@/assets/images/notice-icons/notice-guild-activity.png";
import iconGuild from "@/assets/images/notice-icons/notice-guild.png";
import iconSystem from "@/assets/images/notice-icons/notice-system.png";

export default {
  name: "NotificationPanel",
  components: { AppButton },

  data(){
    return{
      notifications:[],
    };
  },computed:{
    ...mapState(useUserStore,["token"]),
  },
  methods:{
    async loadNotification(){
      const res= await fetch(`${API_BASE}/get_notifications.php`,
        {method:'POST',
        headers:{
          Authorization:`Bearer ${this.token}`,
          
        }});
        const result=await res.json();
        if(result.success){
          this.notifications=result.getNotice;


        }
    }
  },mounted(){
    this.loadNotification();

  },
  typeIconMap:{
        ACTIVITY: iconActivity,
        NEW_REPLY: iconActivity,
        GUILD_NOTICE: iconGuild,
        SYSTEM_MESSAGE: iconSystem,

      }


};
</script>

<template>
  <div class="notification-panel">
    <div class="notification-panel__header">通知</div>
    <div class="notification-panel__list-wrapper">
      <ul class="notification-panel__list">
        <li
          v-for="notice in notifications"
          :key="notice.notifi_id"
          class="notification-panel__item"
        >
          <span class="notification-panel__thumb"></span>
          <div class="noti-content">
          <p class="notification-panel__title">{{ notice.notifi_title  }}</p>
          <p class="notification-panel__text">{{ notice.content }}</p>
          </div>
        </li>
      </ul>
    </div>
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

.notification-panel__list-wrapper {
  max-height: 200px;
  overflow-y: auto;
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

.notification-panel__title {
  margin: 0;
  font-size: $p-sm-size;
  line-height: $text-line-height;
  color: $neutral-800;
  font-weight: $heading-weight;
}

.notification-panel__text {
  margin: 0;
  font-size: $p-sm-size;
  line-height: $text-line-height;
  color: $neutral-800;
}
.notification-panel__btn {
  width: calc(100% - #{$spacing-md} * 2);
  margin: $spacing-lg $spacing-md $spacing-md;
}

.noti-content{
  display: flex;
  flex-direction: column;
}
</style>
