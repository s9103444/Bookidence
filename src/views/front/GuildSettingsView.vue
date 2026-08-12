<script>
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import { useGuildStore } from "@/stores/guild";

export default {
    components: {
    GuildBreadcrumb,
    },
    data() {
    return {
        guildStore: useGuildStore(),
    };
    },
    created() {
    console.log('公會 ID：', this.$route.params.id)
    },
    methods: {
        saveName() {
            console.log('儲存公會名稱：', this.guildStore.currentGuild.name);
        },
        saveIntro() {
            console.log('儲存公會介紹：', this.guildStore.currentGuild.introContent);
        },
        saveAnnouncement() {
            console.log('儲存公布欄內容：', this.guildStore.currentGuild.announcementContent);
        },
    },
}


</script>

<template>
    <GuildBreadcrumb :items="[
    { label: '❮  公會主頁', to: `/guilds/${$route.params.id}` },
    { label: '公會設定' }
    ]" />

    <div class="guild-settings">
    <!-- 以下內容完全不變，只是外層容器脈絡從 <script setup> 換成 Options API -->
    <div class="guild-settings__section">
        <div class="guild-settings__info">
            <h2 class="guild-settings__title">讀書公會背景</h2>
            <img :src="guildStore.currentGuild.backgroundUrl" alt="公會背景預覽" class="guild-settings__preview">
        </div>
        <button class="guild-settings__btn guild-settings__btn--outline">更換背景圖片</button>
    </div>

    <hr class="guild-settings__divider">

    <div class="guild-settings__section">
        <div class="guild-settings__info">
            <h2 class="guild-settings__title">讀書公會頭貼</h2>
            <img :src="guildStore.currentGuild.thumbnailImage" alt="公會頭貼預覽" class="guild-settings__avatar-preview">
        </div>
        <button class="guild-settings__btn guild-settings__btn--outline">更換頭貼</button>
    </div>

    <hr class="guild-settings__divider">

    <div class="guild-settings__section">
        <div class="guild-settings__info guild-settings__info--full">
            <h2 class="guild-settings__title">讀書公會名稱</h2>
            <input type="text" class="guild-settings__input" v-model="guildStore.currentGuild.name" placeholder="請輸入公會名稱">
        </div>
        <button class="guild-settings__btn guild-settings__btn--outline" @click="saveName">儲存</button>
    </div>

    <hr class="guild-settings__divider">

    <div class="guild-settings__section">
        <div class="guild-settings__info guild-settings__info--full">
            <h2 class="guild-settings__title">公會介紹</h2>
            <textarea class="guild-settings__textarea" v-model="guildStore.currentGuild.introContent" placeholder="請輸入公會介紹"></textarea>
        </div>
        <button class="guild-settings__btn guild-settings__btn--outline" @click="saveIntro">儲存</button>
    </div>

    <hr class="guild-settings__divider">

    <div class="guild-settings__section">
        <div class="guild-settings__info guild-settings__info--full">
            <h2 class="guild-settings__title">公布欄內容</h2>
            <textarea class="guild-settings__textarea" v-model="guildStore.currentGuild.announcementContent" placeholder="請輸入公布欄內容"></textarea>

        </div>
        <button class="guild-settings__btn guild-settings__btn--outline" @click="saveAnnouncement">儲存</button>
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

<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;


.guild-settings {
    width: 80%;
    margin: $spacing-md auto;
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
        align-self: flex-end;
        

        &--outline {
            background: #fff;
            border: 1px solid $primary;
            color: $primary;
            transition: transform 0.2s ease, background 0.2s ease;

            &:hover {
                background: $primary;
                color:$neutral-100;
                transform: translateY(-2px);
            }
        }

        &--danger {
            background: #C73333;
            border: none;
            color: #fff;
            transition: transform 0.2s ease, background 0.2s ease;

            &:hover {
                opacity: 0.9;
                transform: translateY(-2px);
            }
        }
    }
}
</style>
