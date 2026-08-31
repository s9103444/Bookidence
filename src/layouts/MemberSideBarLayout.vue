<script>
import AppIcon from '@/components/common/AppIcon.vue'
import AppButton from "@/components/common/AppButton.vue";
// 會員專區側欄。
// 使用到的頁面：個人檔案/我的好友/我的公會/我的讀書會活動/通知中心/使用者設定

export default {
  components: { AppIcon },
  data() {


    return {
      isSidebarOpen: false,
      userId: this.$route.params.id,
      nav_array: [
        {
          path: 'user-settings',
          name: '使用者設定'
        },
        {
          path: 'friends',
          name: '我的好友'
        },
        {
          path: 'my-guilds',
          name: '我的讀書公會'
        },
        {
          path: 'my-books-events',
          name: '我的讀書會活動'
        },
        {
          path: 'notice',
          name: '通知中心'
        },

      ]
    }
  },
  methods: {
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen
    }

  }

};
</script>

<template>
  <div class="member-sidebar-layout">

    <button class="member-sidebar__tab" :class="{ 'member-sidebar__tab--open': isSidebarOpen }" aria-label="開啟選單"
      @click="toggleSidebar">
      <AppIcon :name="isSidebarOpen ? 'close' : 'chevron-left'" :size="18" />
    </button>

    <div class="member-sidebar-overlay" v-if="isSidebarOpen" @click="toggleSidebar"></div>

    <div class="member-sidebar" :class="{ 'member-sidebar--open': isSidebarOpen }">
      <!-- 使用者資訊 -->
      <div class="member-sidebar__title">
        <span class="member-sidebar__label">會員專區</span>
      </div>

      <!-- 側邊導覽 -->
      <div class="member-sidebar__nav">

        <!-- <router-link class="nav-item" :to="{name:'member',params:{id: 'profile'}}">個人檔案</router-link> -->

        <!-- <router-link v-for="(item,key) in nav_array" :key="key" class="nav-item" :to="{name:'member',params:{id: item.path }}">{{ item.name }}</router-link> -->

        <router-link v-for="(item, key) in nav_array" :key="key" class="nav-item" :to="'/member/' + item.path">{{
          item.name }}</router-link>

        <!--  <router-link class="nav-item" to="/member/profile">個人檔案</router-link>
                <router-link class="nav-item" to="/member/friends">我的好友</router-link>
               -->
      </div>


    </div>
    <div class="member-content">
      <router-view></router-view>
    </div>

  </div>
</template>



<style lang="scss" scoped>
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.member-sidebar-layout {
  display: flex;
  min-height: 100vh;
}

.member-content {
  flex: 1;
  min-width: 0;
  background-image: url(../assets/images/home-element/light-green-pixel.png);
  background-position: bottom center;
  background-repeat: no-repeat;
  background-size: 100% auto;
}

//

.member-sidebar {
  display: flex;
  flex-direction: column;
  width: $sidebar-width;
  padding: $spacing-md;
  background: $neutral-300;
  position: sticky;
  top: 20px;

  @media (max-width: 1024px) {
    position: fixed;
    top: 0;
    right: 0;
    height: 100vh;
    z-index: 100;
    transform: translateX(100%);
    transition: transform .3s ease;
    overflow-y: auto;
    transform: translateX(100%);

    &.member-sidebar--open {
      transform: translateX(0);
    }
  }




}

.member-sidebar__img {
  width: 120px;
  height: 120px;
  margin: $spacing-md auto;
  object-fit: cover;
  border-radius: 50%;
}

.member-sidebar__title {
  display: flex;
  flex-direction: column;
  margin-bottom: $spacing-md;
  padding-left: $spacing-md;
}

.member-sidebar__label {
  font-size: $h5-size;
  color: $primary;
}

.member-sidebar__name {
  font-size: $h6-size;
  font-weight: $heading-weight;
  color: $primary;
}

.member-sidebar__nav {
  display: flex;
  flex-direction: column;
  gap: $spacing-xs;
}

.nav-item {
  padding: $spacing-sm $spacing-md $spacing-sm calc(#{$spacing-md} + #{$spacing-md});
  margin: 0 (-$spacing-md);
  background: transparent;
  color: $primary;
  text-decoration: none;
  text-align: left;
  font-size: $p-sm-size;
  display: block;
  transition: transform 0.2s ease, background 0.2s ease;
  cursor: pointer;

  &:hover {
    background: $neutral-100;
    transform: translateY(-2px);
  }
}


.member-sidebar__tab {
  display: none;

  @media (max-width: 1024px) {
    display: flex;
    align-items: center;
    justify-content: center;
    position: fixed;
    top: calc(#{$header-height} + $spacing-md);
    right: 0;
    width: 32px;
    height: 64px;
    background: $primary;
    color: $neutral-100;
    border-radius: 8px 0 0 8px;
    z-index: 102;

    &.member-sidebar__tab--open {
      top: $spacing-sm;
      right: $spacing-sm;
      width: 32px;
      height: 32px;
      border-radius: 8px;
    }
  }
}

.member-sidebar-overlay {
  display: none;

  @media (max-width: 1024px) {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 90;
  }
}
</style>