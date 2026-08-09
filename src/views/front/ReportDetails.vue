<script setup>

</script>

<template>
 <div class="report-detail">
        <div class="report-detail__id">檢舉編號#0033</div>

        <div class="report-detail__grid">
            <div class="report-detail__field">
                <span class="report-detail__label">檢舉人暱稱&ID</span>
                <div class="report-detail__person">
                    <span class="report-detail__name">我是檢舉人</span>
                    <span class="report-detail__pid">BKD00025</span>
                </div>
            </div>

            <div class="report-detail__field">
                <span class="report-detail__label">被檢舉人暱稱&ID</span>
                <div class="report-detail__person">
                    <span class="report-detail__name">我是被檢舉人</span>
                    <span class="report-detail__pid">BKD00036</span>
                </div>
            </div>

            <div class="report-detail__field">
                <span class="report-detail__label">檢舉類型</span>
                <span class="report-detail__value">留言</span>
            </div>

            <div class="report-detail__field">
                <span class="report-detail__label">檢舉時間</span>
                <span class="report-detail__value">2026-07-16 14:20</span>
            </div>
        </div>

        <hr class="report-detail__divider">

        <div class="report-detail__section">
            <span class="report-detail__section-title">檢舉內容</span>

            <div class="report-detail__quote-card">
                <div class="report-detail__quote-header">
                    <div class="report-detail__quote-avatar"></div>
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
    margin: 0 auto;

    &__id {
        font-size: $p-md-size;
        color: $neutral-500;
        margin-bottom: $spacing-md;
    }

    &__grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: $spacing-md $spacing-lg;
        margin-bottom: $spacing-md;
    }

    &__field {
        display: flex;
        flex-direction: column;
        gap: $spacing-xs;
    }

    &__label {
        font-size: $p-md-size;
        color: $primary;
    }

    &__person {
        display: flex;
        flex-direction: column;
    }

    &__name {
        font-size: $p-lg-size;
        font-weight: $text-weight;
        line-height: $text-line-height;
        color: $neutral-800;
    }

    &__pid {
        font-size: $p-sm-size;
        color: $neutral-400;
    }

    &__value {
        font-size: $p-lg-size;
        font-weight: $text-weight;
        line-height: $text-line-height;
        color: $neutral-800;
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
        background: $neutral-200;
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
        width: 49px;
        height: 49px;
        border-radius: 50%;
        background: $neutral-300;
    }

    &__quote-name {
        font-size: $p-lg-size;
        font-weight: $text-weight;
        line-height: $text-line-height;
        color: $neutral-800;
    }

    &__quote-text {
        font-size: $p-lg-size;
        font-weight: $text-weight;
        line-height: $text-line-height;
        color: $neutral-800;
        margin: 0;
        padding-left: $spacing-lg;
    }

    &__description {
        font-size: $p-lg-size;
        font-weight: $text-weight;
        line-height: $text-line-height;
        color: $neutral-800;
        margin: 0;
    }
}
</style>