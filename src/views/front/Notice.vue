<script>
import { API_BASE } from '@/common/api';
import { mapState } from 'pinia';
import { useUserStore } from '@/stores/user';
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import AppIcon from "@/components/common/AppIcon.vue";
import iconActivity from "@/assets/images/notice-icons/notice-guild-activity.png";
import iconGuild from "@/assets/images/notice-icons/notice-guild.png";
import iconSystem from "@/assets/images/notice-icons/notice-system.png";



export default {
  components: {
    GuildBreadcrumb,
    AppIcon,
  },
  data() {
    return {
    
      activeTab: 'all',
      notifications: [
        { notifi_id: 1, notifi_title: '你報名的讀書會即將開始', content: '《原子習慣》讀書會 將於 6/15（六）19:30 開始', sent_at: '10 分鐘前', type: 'guild', is_read: false },
        { notifi_id: 2, notifi_title: '溫暖羽筆公會 有新的活動', content: '新活動「奇幻小說交流會」已發布，快來報名參加吧！', sent_at: '1 小時前', type: 'ACTIVITY', is_read: false },
        { notifi_id: 3, notifi_title: '茉莉 回覆了你的留言', content: '在《被討厭的勇氣》讀書會中回覆了你的留言', sent_at: '3 小時前', type: 'guild', is_read: false },
        { notifi_id: 4, notifi_title: '你收到新的好友邀請', content: '小宇 想加你為好友', sent_at: '昨天 21:45', type: 'guild', is_read: false },
        { notifi_id: 5, notifi_title: '你加入的讀書會有新公告', content: '《深度工作力》讀書會 發布了新公告', sent_at: '昨天 18:30', type: 'ACTIVITY', is_read: true },
        { notifi_id: 6, notifi_title: '系統公告', content: 'Bookidence 6 月份活動行事曆已更新', sent_at: '6/8（日）', type: 'SYSTEM_MESSAGE', is_read: true },
      ],
      typeIconMap:{
        ACTIVITY: iconActivity,
        NEW_REPLY: iconActivity,
        GUILD_NOTICE: iconGuild,
        SYSTEM_MESSAGE: iconSystem,

      }

    }
  },


  computed: {
    filteredNotifications() {
      if (this.activeTab === 'unread') return this.notifications.filter(n => !n.is_read)
      if (this.activeTab === 'ACTIVITY') return this.notifications.filter(n => n.type === 'ACTIVITY')
      if (this.activeTab === 'GUILD_NOTICE') return this.notifications.filter(n => n.type === 'GUILD_NOTICE')
      if (this.activeTab === 'SYSTEM_MESSAGE') return this.notifications.filter(n => n.type === 'SYSTEM_MESSAGE')
      return this.notifications
    },
    ...mapState(useUserStore,["token"])

  },
  methods:{
  async  loadNotification(){

    const res= await fetch(`${API_BASE}/get_notifications.php`,
      {method:'POST',
      headers:{ Authorization:`Bearer ${this.token}`,
      }
    })
    const result= await res.json();

    if(result.success){
      this.notifications=result.getNotice
    }
    },
    async markAllAsRead(){
      const res=await fetch(`${API_BASE}/mark_notification_read.php`,{
      method:'POST',
      headers:{ Authorization:`Bearer ${this.token}`,
        'Content-Type': 'application/json'
      },
      body:JSON.stringify({})
      })
      const result= await res.json();

      if(result.success){
       await this.loadNotification();
      }
    },
    async markOneAsRead(notice){
      const res=await fetch(`${API_BASE}/mark_notification_read.php`,{
      method:'POST',
      headers:{ Authorization:`Bearer ${this.token}`,
        'Content-Type': 'application/json'
      },
      body:JSON.stringify({notifiId:notice.notifi_id})
      })
       const result= await res.json();

      if(result.success){
       await this.loadNotification();

      }

    }

    
  },mounted(){
    this.loadNotification();
  }

}
</script>

<template>
  <div class="notice-page container-content member-list">
    <div class="col-10">
      <div class="notice-header">
        <GuildBreadcrumb class="col-10" :items="[
          { label: '❮ 首頁', to: `/home` },
          { label: '通知中心' }
        ]" />
        <a class="mark-all-read" @click="markAllAsRead()">✓ 全部標記為已讀</a>
      </div>

      <div class="notice-tabs">
        <a class="notice-tab" :class="{ 'is-active': activeTab === 'all' }" @click="activeTab = 'all'">全部
          <span class="notice-count">{{ notifications.length }}</span>
        </a>
        <a class="notice-tab" :class="{ 'is-active': activeTab === 'unread' }" @click="activeTab = 'unread'">
          未讀 <span class="notice-badge">{{notifications.filter(n => !n.is_read).length}}</span>
        </a>
        <a class="notice-tab" :class="{ 'is-active': activeTab === 'ACTIVITY' }" @click="activeTab = 'ACTIVITY'">
          讀書會 <span class="notice-badge">{{notifications.filter(n => n.type === 'ACTIVITY').length}}</span>
        </a>
        <a class="notice-tab" :class="{ 'is-active': activeTab === 'GUILD_NOTICE' }" @click="activeTab = 'GUILD_NOTICE'">
          公會 <span class="notice-badge">{{notifications.filter(n => n.type === 'GUILD_NOTICE').length}}</span>
        </a>
        <a class="notice-tab" :class="{ 'is-active': activeTab === 'SYSTEM_MESSAGE' }" @click="activeTab = 'SYSTEM_MESSAGE'">
          系統 <span class="notice-badge">{{notifications.filter(n => n.type === 'SYSTEM_MESSAGE').length}}</span>
        </a>
      </div>

      <ul class="notice-list">
        <li class="notice-item" v-for="notice in filteredNotifications" :key="notice.notifi_id" @click="markOneAsRead(notice)">
          <img :src="typeIconMap[notice.type]" :alt="notice.notifi_title" class="notice-avatar">
          <div class="notice-info">
              <span class=" notice-notifi_title">{{ notice.notifi_title }}</span>
            <p class="notice-content">{{ notice.content }}</p>
          </div>
          <div class="notice-meta">
            <span class="notice-sent_at">{{ notice.sent_at }}</span>
            <span class="notice-dot" :class="{ 'is-unread': !notice.is_read }"></span>

          </div>
        </li>
      </ul>




    </div>
  </div>
</template>


<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;
.notice-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
 
}

.mark-all-read {
  font-size: $p-sm-size;
  color: $primary;
  cursor: pointer;
}

.notice-tabs {
  display: flex;
  gap: $spacing-lg;
  border-bottom: 1px solid $neutral-300;
  margin-bottom: $spacing-md;
}

.notice-tab {
  padding: $spacing-sm 0;
  font-size: $p-md-size;
  font-weight: $heading-weight;
  color: $neutral-500;
  cursor: pointer;

  &.is-active {
    color: $primary;
    border-bottom: 4px solid $primary-300;
  }

   @media (max-width:768px) {
   font-size: $p-sm-size;
   }


}

.notice-count {
  color: inherit;
}

.notice-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 18px;
  height: 18px;
  padding: 0 $spacing-xs;
  border-radius: 999px;
  background: #E15647;
  color: #fff;
  font-size: $p-xs-size;

}

.notice-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.notice-item {
  display: flex;
  align-items: center;
  gap: $spacing-md;
  padding: $spacing-md 0;
  border-bottom: 1px solid $neutral-300;
}

.notice-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.notice-info {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.notice-notifi_title {
  font-size: $p-md-size;
  font-weight: $heading-weight;
  color: $neutral-800;
}

.notice-content {
  font-size: $p-sm-size;
  color: $neutral-800;
  margin: 0;
   @media (max-width: 768px) {
    font-size: $p-xs-size;
  }
}

.notice-meta {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
  flex-shrink: 0;
}

.notice-sent_at {
  font-size: $p-sm-size;
  color: $neutral-600;
   @media (max-width: 768px) {
    font-size: $p-xs-size;
  }
}

.notice-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: $neutral-300;

  &.is-unread {
    background: $secondary;
  }
}


</style>
