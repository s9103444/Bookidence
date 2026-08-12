<script>
// 公會相關分頁的側邊欄。
// 使用到的頁面 : 讀書公會-設定讀書排程/成員列表-申請中/成員總覽/檢舉事件/檢舉事件詳情/建立讀書會活動/讀書會活動詳情/公會設定。
    export default {
    methods: {
        goToGuildFeature(routeName) {
            this.$router.push({ 
                name: routeName, 
                params: { id: this.$route.params.id } });
            },
        },
    };
</script>

<template>
    <div class="guild-sidebar-layout">
        <div class="guild-sidebar">
            <img src="@/assets/images/guild/guildAvatar.png" alt="公會頭貼" class="guild-sidebar__img">

            <div class="guild-sidebar__title">
                <span class="guild-sidebar__label">讀書公會</span>
                <span class="guild-sidebar__name">壁爐與貓</span>
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
}

.guild-sidebar__img {
    width: 120px;
    height: 120px;
    margin: $spacing-md auto;
    object-fit: cover;
    border-radius: 50%;
}

.guild-sidebar__title {
    display: flex;
    flex-direction: column;
    margin-bottom: $spacing-md;
    padding-left:$spacing-md;
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
</style>