<script>
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import AppIcon from "@/components/common/AppIcon.vue";
import noticeIcon  from "@/assets/images/member-element/board.png";



export default {
  components: {
    GuildBreadcrumb,
    AppIcon,
  },
  data() {
    return {
      activeTab: 'all',
      notifications: [
        { id: 1, title: '你報名的讀書會即將開始', desc: '《原子習慣》讀書會 將於 6/15（六）19:30 開始', time: '10 分鐘前', category: 'guild', isRead: false },
        { id: 2, title: '溫暖羽筆公會 有新的活動', desc: '新活動「奇幻小說交流會」已發布，快來報名參加吧！', time: '1 小時前', category: 'org', isRead: false },
        { id: 3, title: '茉莉 回覆了你的留言', desc: '在《被討厭的勇氣》讀書會中回覆了你的留言', time: '3 小時前', category: 'guild', isRead: false },
        { id: 4, title: '你收到新的好友邀請', desc: '小宇 想加你為好友', time: '昨天 21:45', category: 'guild', isRead: false },
        { id: 5, title: '你加入的讀書會有新公告', desc: '《深度工作力》讀書會 發布了新公告', time: '昨天 18:30', category: 'org', isRead: true },
        { id: 6, title: '系統公告', desc: 'Bookidence 6 月份活動行事曆已更新', time: '6/8（日）', category: 'system', isRead: true },
      ]

    }
  },

  computed: {
    filteredNotifications() {
      if (this.activeTab === 'unread') return this.notifications.filter(n => !n.isRead)
      if (this.activeTab === 'guild') return this.notifications.filter(n => n.category === 'guild')
      if (this.activeTab === 'org') return this.notifications.filter(n => n.category === 'org')
      if (this.activeTab === 'system') return this.notifications.filter(n => n.category === 'system')
      return this.notifications
    }
  }
}
</script>

<template>
  <div class="notice-page container-content member-list">
    <div class="col-10">
      <div class="notice-header">
        <GuildBreadcrumb class="col-10" :items="[
          { label: '❮ 首頁', to: `/` },
          { label: '通知中心' }
        ]" />
        <a class="mark-all-read">✓ 全部標記為已讀</a>
      </div>

      <div class="notice-tabs">
        <a class="notice-tab" :class="{ 'is-active': activeTab === 'all' }" @click="activeTab = 'all'">全部
          <span class="notice-count">{{ notifications.length }}</span>
        </a>
        <a class="notice-tab" :class="{ 'is-active': activeTab === 'unread' }" @click="activeTab = 'unread'">
          未讀 <span class="notice-badge">{{notifications.filter(n => !n.isRead).length}}</span>
        </a>
        <a class="notice-tab" :class="{ 'is-active': activeTab === 'guild' }" @click="activeTab = 'guild'">
          讀書會 <span class="notice-badge">{{notifications.filter(n => n.category === 'guild').length}}</span>
        </a>
        <a class="notice-tab" :class="{ 'is-active': activeTab === 'org' }" @click="activeTab = 'org'">
          公會 <span class="notice-badge">{{notifications.filter(n => n.category === 'org').length}}</span>
        </a>
        <a class="notice-tab" :class="{ 'is-active': activeTab === 'system' }" @click="activeTab = 'system'">
          系統 <span class="notice-badge">{{notifications.filter(n => n.category === 'system').length}}</span>
        </a>
      </div>

      <ul class="notice-list">
        <li class="notice-item" v-for="notice in filteredNotifications" :key="notice.id">
          <img :src="noticeIcon" :alt="notice.title" class="notice-avatar">
          <div class="notice-info">
              <span class=" notice-title">{{ notice.title }}</span>
            <p class="notice-desc">{{ notice.desc }}</p>
          </div>
          <div class="notice-meta">
            <span class="notice-time">{{ notice.time }}</span>
            <span class="notice-dot" :class="{ 'is-unread': !notice.isRead }"></span>

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
  margin-bottom: $spacing-md;
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
    border-bottom: 2px solid $primary;
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

.notice-title {
  font-size: $p-md-size;
  font-weight: $heading-weight;
  color: $neutral-800;
}

.notice-desc {
  font-size: $p-sm-size;
  color: $neutral-500;
  margin: 0;
}

.notice-meta {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
  flex-shrink: 0;
}

.notice-time {
  font-size: $p-sm-size;
  color: $neutral-600;
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
