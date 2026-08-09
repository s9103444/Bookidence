<script setup>
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
</script>

<template>

<GuildBreadcrumb :items="[
    { label: '❮  公會主頁', to: '/guilds/1' },// guilds/:id 填入目前公會的 id
    { label: '檢舉事件', to: '/report' },
    { label: '查看詳情' }
]" />


<div class="report-detail">
        <div class="report-detail__id">檢舉編號#0033</div>

        <div class="report-detail__grid">
            <div class="report-detail__field reporter col-6">
                <span class="report-detail__label">檢舉人暱稱&ID</span>
                <div class="report-detail__person">
                    <span class="report-detail__name" id="reporter-name">我是檢舉人</span>
                    <span class="report-detail__pid" id="reporter-id">BKD00025</span>
                </div>
            </div>

            <div class="report-detail__field reported col-6">
                <span class="report-detail__label">被檢舉人暱稱&ID</span>
                <div class="report-detail__person">
                    <span class="report-detail__name" id="reported-name">我是被檢舉人</span>
                    <span class="report-detail__pid" id="reported-id">BKD00036</span>
                </div>
            </div>

            <div class="report-detail__field col-6">
                <span class="report-detail__label">檢舉類型</span>
                <span class="report-detail__value">留言</span>
            </div>

            <div class="report-detail__field col-6">
                <span class="report-detail__label">檢舉時間</span>
                <span class="report-detail__value">2026-07-16 14:20</span>
            </div>
        </div>

        <hr class="report-detail__divider">

        <div class="report-detail__section">
            <span class="report-detail__section-title">檢舉內容</span>

            <div class="report-detail__quote-card">
                <div class="report-detail__quote-header">
                    <div class="report-detail__quote-avatar">
                        <img src="../../assets/images/guild/boy.png" alt="被檢舉人頭貼">
                    </div>
                    <span class="report-detail__quote-name">我是被檢舉人</span>
                </div>
                <p class="report-detail__quote-text">「這本書真的很無聊，浪費時間」——討論區留言</p>
            </div>
        </div>

        <hr class="report-detail__divider">

        <div class="report-detail__section">
            <span class="report-detail__section-title">檢舉詳細說明</span>
            <p class="report-detail__description">內容涉及人身攻擊字眼，非單純書籍評論，請嚴查，感謝。</p>
        </div>
    </div>
</template>

<style lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.report-detail {
    width: 80%;
    margin: $spacing-md auto;

    &__id {
        font-size: $p-md-size;
        color: $neutral-500;
        margin-bottom: $spacing-md;
    }

    &__field {
        display: flex;
        flex-direction: column;
        gap: $spacing-xs;
    }

    &__grid {
        @include grid-container;
        margin-bottom: $spacing-md;
}

    &__label {
        font-size: $p-md-size;
        color: $primary;
    }

    &__person {
        display: flex;
        flex-direction: column;
    }

    &__name,
    &__value {
        font-size: $p-md-size;
        font-weight: $text-weight;
        line-height: $text-line-height;
        color: $neutral-800;
    }

    &__pid {
        font-size: $p-sm-size;
        color: $neutral-400;
    }

    &__divider {
        border: none;
        border-top: 1px solid $neutral-200;
        margin: $spacing-lg 0;
    }

    &__section {
        display: flex;
        flex-direction: column;
        gap: $spacing-sm;
    }

    &__section-title {
        font-size: $p-md-size;
        color: $primary;
    }

    &__quote-card {
        background: $neutral-300;
        border-radius: 5px;
        padding: $spacing-lg;
    }

    &__quote-header {
        display: flex;
        align-items: center;
        gap: $spacing-md;
        margin-bottom: $spacing-md;
    }

    &__quote-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: $neutral-300;
        overflow: hidden;
        
        img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        }
    }

    &__quote-name {
        font-size: $p-md-size;
        font-weight: $text-weight;
        line-height: $text-line-height;
        color: $neutral-800;
    }

    &__quote-text {
        font-size: $p-md-size;
        font-weight: $text-weight;
        line-height: $text-line-height;
        color: $neutral-800;
        margin: 0;
        padding-left: $spacing-lg;
    }

    &__description {
        font-size: $p-md-size;
        font-weight: $text-weight;
        line-height: $text-line-height;
        color: $neutral-800;
        margin: 0;
    }
}
</style>