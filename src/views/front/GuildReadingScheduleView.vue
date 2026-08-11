<script setup>
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import { useRoute } from "vue-router";

const route = useRoute();

</script>

<template>
    <GuildBreadcrumb :items="[
    { label: '❮  公會主頁', to: `/guilds/${route.params.id}` },// guilds/:id 填入目前公會的 id
    { label: '設定讀書排程' }
]" />
<div scoped class="schedule-card">

<div class="reading-book">
            <div class="reading-book__details">
                <img src="@/assets/images/little-prince-cover.png" alt="小王子" class="reading-book__details-img">
                <div class="reading-book__details-meta">
                    <h2 class="reading-book__details-title">小王子</h2>
                    <div class="reading-book__details-list">
                        <p>作者：史蒂芬妮．梅爾</p>
                        <p>類別：奇幻小說</p>
                        <p>譯者：瞿秀蕙/ 安麗姬/ Liao, Sabrina</p>
                        <p>出版日期：2011/06/10</p>
                        <p>出版社：尖端出版</p>
                        <p>ISBN：000-0000000000</p>
                    </div>
                </div>
            </div>
</div>



        <div class="schedule-card__header">
            <h3 class="schedule-card__title">章節分段討論板：01</h3>
            <button class="schedule-card__close" aria-label="關閉">✕</button>
        </div>

        <div class="schedule-card__body">
            <div class="schedule-card__field">
                <label class="schedule-card__label">請輸入章節範圍</label>
                <div class="schedule-card__range">
                    <input type="number" class="schedule-card__input" placeholder="請輸入數字">
                    <span class="schedule-card__tilde">～</span>
                    <input type="number" class="schedule-card__input" placeholder="請輸入數字">
                    <span class="schedule-card__unit">章節</span>
                </div>
            </div>

            <div class="schedule-card__field">
                <label class="schedule-card__label">預計完讀日期</label>
                <input type="date" class="schedule-card__input schedule-card__input--date" value="1999-01-01">
            </div>
        </div>
    </div>
</template>

<style lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.schedule-card {
    width: 545px;
    min-height: 225px;
    border: 1px solid $neutral-300;
    border-radius: 12px;
    overflow: hidden;
    flex-direction: column;
    display: flex;

    &__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: $spacing-sm $spacing-md;
        background: $secondary-500;
    }

    &__title {
        margin: 0;
        font-size: $h6-size;
        font-weight: $heading-weight;
        line-height: $heading-line-height;
        color: $primary;
    }

    &__close {
        color: $neutral-500;
        font-size: $label-lg-size;
        background: none;
        border: none;
        cursor: pointer;
        line-height: 1;
    }

    &__body {
        padding: $spacing-md;
        display: flex;
        flex-direction: column;
        gap: $spacing-md;
        background: $secondary-100;
        flex: 1;
    }

    &__field {
        display: flex;
        flex-direction: column;
        gap: $spacing-xs;
    }

    &__label {
        font-size: $p-sm-size;
        color: $neutral-600;
    }

    &__range {
        display: flex;
        align-items: center;
        gap: $spacing-xs;
    }

    &__input {
        width: 100px;
        padding: $spacing-sm;
        border: 1px solid $neutral-300;
        border-radius: 6px;

        &--date {
            width: 50%;
        }
    }
}
</style>