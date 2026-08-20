<script>
// 公會相關分頁的側邊欄。
// 使用到的頁面 : 讀書公會-設定讀書排程/成員列表-申請中/成員總覽/檢舉事件/檢舉事件詳情/建立讀書會活動/讀書會活動詳情/公會設定。
    import AppIcon from '@/components/common/AppIcon.vue'
    import { useGuildStore } from '@/stores/guild'

    export default {
    components: {
        AppIcon,
    },
    data() {
        return {
            isSidebarOpen: false,
            guildStore: useGuildStore(),
        };
    },
    methods: {
        goToGuildFeature(routeName) {
            this.$router.push({
                name: routeName,
                params: { id: this.$route.params.id } });
            },
        goToGuild() {
            this.$router.push({ name: 'guild-detail', params: { id: this.$route.params.id } });
        },
        toggleSidebar() {
            this.isSidebarOpen = !this.isSidebarOpen;
        },
        },
    };
</script>

<template>
    <div class="guild-sidebar-layout">
        <button
            class="guild-sidebar__tab"
            :class="{ 'guild-sidebar__tab--open': isSidebarOpen }"
            aria-label="開啟選單"
            @click="toggleSidebar"
        >
            <AppIcon :name="isSidebarOpen ? 'close' : 'chevron-left'" :size="18" />
        </button>

        <div class="guild-sidebar-overlay" v-if="isSidebarOpen" @click="toggleSidebar"></div>

        <div class="guild-sidebar" :class="{ 'guild-sidebar--open': isSidebarOpen }">
            <img :src="guildStore.currentGuild.thumbnailImage" alt="公會頭貼" class="guild-sidebar__img" @click="goToGuild">

            <div class="guild-sidebar__title" @click="goToGuild">
                <span class="guild-sidebar__label">讀書公會</span>
                <span class="guild-sidebar__name">{{ guildStore.currentGuild.name }}</span>
            </div>

            <div class="guild-sidebar__nav">
                <a class="nav-item" @click="goToGuildFeature('event-apply')">建立讀書會活動</a>
                <a class="nav-item" @click="goToGuildFeature('guild-reading-schedule')">設定讀書排程</a>
                <a class="nav-item" @click="goToGuildFeature('guild-members')">成員列表</a>
                <a class="nav-item" @click="goToGuildFeature('report')">檢舉事件</a>
                <a class="nav-item" @click="goToGuildFeature('guild-settings')">公會設定</a>
            </div>
        </div>

        <div class="guild-sidebar-layout__content">
            <!-- 這裡會依照網址，換成對應的公會子頁面 -->
            <router-view />
        </div>
    </div>
</template>

<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.guild-sidebar-layout {
    display: flex;
    min-height: 100vh;
    
}

.guild-sidebar-layout__content {
    flex: 1;
    padding: $spacing-lg;
    
}

.guild-sidebar {
    display: flex;
    flex-direction: column;
    width: $sidebar-width;
    padding: $spacing-md;
    background: $neutral-300;
    position: sticky;
    top:20px;

    @include tablet {
        position: fixed;
        top: 0;
        right: 0;
        height: 100vh;
        z-index: 100;
        transform: translateX(100%);
        transition: transform .3s ease;
        overflow-y: auto;

        &.guild-sidebar--open {
            transform: translateX(0);
        }
    }
}

.guild-sidebar__img {
    width: 120px;
    height: 120px;
    margin: $spacing-md auto;
    object-fit: cover;
    border-radius: 50%;
    cursor: pointer;
}

.guild-sidebar__title {
    display: flex;
    flex-direction: column;
    margin-bottom: $spacing-md;
    padding-left:$spacing-md;
    cursor: pointer;
}

.guild-sidebar__label {
    font-size: $p-xs-size;
    color: $primary;
}

.guild-sidebar__name {
    font-size: $h6-size;
    font-weight: $heading-weight;
    color: $primary;
}

.guild-sidebar__nav {
    display: flex;
    flex-direction: column;
    gap: $spacing-xs;
}

.nav-item {
    padding:$spacing-sm $spacing-md $spacing-sm calc(#{$spacing-md} + #{$spacing-md});
    margin: 0 (-$spacing-md);
    background: transparent;
    color: $primary;
    text-decoration: none;
    text-align: left;
    font-size: $p-sm-size;
    display: block;
    transition: transform .2s ease, background .2s ease;

    &:hover {
        background: $neutral-100;
        transform: translateY(-2px);
        cursor: pointer;
    }
}

.guild-sidebar-overlay {
    display: none;

    @include tablet {
        display: block;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 90;
    }
}

.guild-sidebar__tab {
    display: none;

    @include tablet {
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

        &.guild-sidebar__tab--open {
            top: $spacing-sm;
            right: $spacing-sm;
            width: 32px;
            height: 32px;
            border-radius: 8px;
        }
    }
}
</style>