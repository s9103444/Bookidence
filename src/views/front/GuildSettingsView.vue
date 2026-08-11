<!-- src/views/front/GuildSettingsView.vue -->
<script setup>
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import { useRoute } from "vue-router";

const route = useRoute();

</script>

<script>
export default {
  created() {
    console.log('公會 ID：', this.$route.params.id)
  },
}
</script>


<template>

  <GuildBreadcrumb :items="[
    { label: '❮  公會主頁', to: `/guilds/${route.params.id}` },// guilds/:id 填入目前公會的 id
    { label: '公會設定' }
]" />

  <div class="guild-settings">
    <div class="guild-settings__section">
        <div class="guild-settings__info">
            <h2 class="guild-settings__title">讀書公會背景</h2>
            <img src="./guild-bg-preview.jpg" alt="公會背景預覽" class="guild-settings__preview">
        </div>
        <button class="guild-settings__btn guild-settings__btn--outline">更換背景圖片</button>
    </div>

    <hr class="guild-settings__divider">

    <div class="guild-settings__section">
        <div class="guild-settings__info">
            <h2 class="guild-settings__title">讀書公會頭貼</h2>
            <img src="./guild-avatar-preview.jpg" alt="公會頭貼預覽" class="guild-settings__avatar-preview">
        </div>
        <button class="guild-settings__btn guild-settings__btn--outline">更換頭貼</button>
    </div>

    <hr class="guild-settings__divider">

    <div class="guild-settings__section">
        <div class="guild-settings__info guild-settings__info--full">
            <h2 class="guild-settings__title">讀書公會名稱</h2>
            <input type="text" class="guild-settings__input" value="壁爐與貓" placeholder="請輸入公會名稱">
        </div>
        <button class="guild-settings__btn guild-settings__btn--outline">儲存</button>
    </div>

    <hr class="guild-settings__divider">

    <div class="guild-settings__section">
        <div class="guild-settings__info guild-settings__info--full">
            <h2 class="guild-settings__title">公會介紹</h2>
            <textarea class="guild-settings__textarea" placeholder="請輸入公會介紹"></textarea>
        </div>
        <button class="guild-settings__btn guild-settings__btn--outline">儲存</button>
    </div>

    <hr class="guild-settings__divider">

    <div class="guild-settings__section">
        <div class="guild-settings__info guild-settings__info--full">
            <h2 class="guild-settings__title">公布欄內容</h2>
            <textarea class="guild-settings__textarea" placeholder="請輸入公布欄內容"></textarea>
        </div>
        <button class="guild-settings__btn guild-settings__btn--outline">儲存</button>
    </div>

    <hr class="guild-settings__divider">

    <div class="guild-settings__section">
        <div class="guild-settings__info">
            <h2 class="guild-settings__title guild-settings__title--danger">刪除讀書公會</h2>
            <p class="guild-settings__desc">僅公會長可執行，刪除後無法復原</p>
        </div>
        <button class="guild-settings__btn guild-settings__btn--danger">刪除公會</button>
    </div>
</div>
</template>



<style lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;


.guild-settings {
    width: 80%;
    display: flex;
    flex-direction: column;

    &__section {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: $spacing-lg 0;
        gap: $spacing-lg;
    }

    &__info {
        display: flex;
        flex-direction: column;
        gap: $spacing-sm;

        &--full {
            flex: 1;
        }
    }

    &__title {
        margin: 0;
        font-size: $p-lg-size;
        font-weight: $text-weight;
        color: $neutral-800;

        &--danger {
            color: #C73333;
        }
    }

    &__desc {
        margin: 0;
        font-size: $p-md-size;
        color: $neutral-400;
    }

    &__preview {
        width: 198px;
        aspect-ratio: 198 / 106;
        object-fit: cover;
        border-radius: 5px;
        margin-top: $spacing-sm;
    }

    &__avatar-preview {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        margin-top: $spacing-sm;
    }

    &__input {
        width: 100%;
        box-sizing: border-box;
        min-height: 48px;
        padding: $spacing-sm $spacing-md;
        margin-top: $spacing-sm;
        border: 1px solid $neutral-400;
        border-radius: 5px;
        font-size: $p-md-size;
        color: $neutral-800;
    }

    &__textarea {
        width: 100%;
        box-sizing: border-box;
        min-height: 100px;
        padding: $spacing-sm $spacing-md;
        margin-top: $spacing-sm;
        border: 1px solid $neutral-400;
        border-radius: 5px;
        font-size: $p-md-size;
        color: $neutral-800;
        font-family: inherit;
        resize: vertical;
    }

    &__divider {
        border: none;
        border-top: 1px solid $neutral-200;
        margin: 0;
    }

    &__btn {
        flex-shrink: 0;
        padding: $spacing-sm $spacing-md;
        border-radius: 5px;
        font-size: $p-md-size;
        cursor: pointer;
        white-space: nowrap;

        &--outline {
            background: #fff;
            border: 1px solid $primary;
            color: $primary;

            &:hover {
                background: $neutral-100;
            }
        }

        &--danger {
            background: #C73333;
            border: none;
            color: #fff;

            &:hover {
                opacity: 0.9;
            }
        }
    }
}
</style>
