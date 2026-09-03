<script>
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import { useGuildStore } from "@/stores/guild";
import { useUserStore } from "@/stores/user";
import { API_BASE, API_STATIC } from "@/common/api";
import { resolveImageUrl } from '@/common/image'
import defaultGuildBackground from '@/assets/images/guild/book-room2.png'

export default {
    components: {
    GuildBreadcrumb,
    },
    data() {
    return {
        guildStore: useGuildStore(),
        userStore: useUserStore(),
    };
    },
    created() {
    console.log('公會 ID：', this.$route.params.id)
    this.loadGuildDetail(this.$route.params.id)
    },
    computed: {
        isGuildOwner() {
            return this.guildStore.currentGuild.myRole === '會長'
        },
    },
    methods: {
        saveName() {
            if (this.guildStore.currentGuild.name.trim() === '') {
                alert("公會名稱不能空白");
                return;
            }
            const formData = new FormData();
            formData.append("guild_id", this.$route.params.id);
            formData.append("name", this.guildStore.currentGuild.name);

            fetch(`${API_BASE}/guild_update_settings.php`, {
                method: "POST",
                headers: { Authorization: `Bearer ${this.userStore.token}` },
                body: formData,
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("公會名稱已儲存");
                    } else {
                        alert(data.message);
                    }
                });
        },
        saveIntro() {
            if (this.guildStore.currentGuild.introContent.length > 500) {
                alert("公會介紹不能超過 500 字");
                return;
            }
            const formData = new FormData();
            formData.append("guild_id", this.$route.params.id);
            formData.append("intro", this.guildStore.currentGuild.introContent);

            fetch(`${API_BASE}/guild_update_settings.php`, {
                method: "POST",
                headers: { Authorization: `Bearer ${this.userStore.token}` },
                body: formData,
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("公會介紹已儲存");
                    } else {
                        alert(data.message);
                    }
                });
        },
        saveAnnouncement() {
            const formData = new FormData();
            formData.append("guild_id", this.$route.params.id);
            formData.append("announcement", this.guildStore.currentGuild.announcementContent);

            fetch(`${API_BASE}/guild_update_settings.php`, {
                method: "POST",
                headers: { Authorization: `Bearer ${this.userStore.token}` },
                body: formData,
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("公布欄內容已儲存");
                    } else {
                        alert(data.message);
                    }
                });
        },
        loadGuildDetail(guildId) {
            const headers = {}
            if (this.userStore.token) {
                headers.Authorization = `Bearer ${this.userStore.token}`
            }
            fetch(`${API_BASE}/guild_get_detail.php?guild_id=${guildId}`, { headers })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.guild) {
                        this.guildStore.currentGuild.name = data.guild.guild_name
                        this.guildStore.currentGuild.introContent = data.guild.intro
                        this.guildStore.currentGuild.announcementContent = data.guild.announcement
                        this.guildStore.currentGuild.thumbnailImage = data.guild.guild_avatar.startsWith('http')? data.guild.guild_avatar
                        : `${API_STATIC}/uploads/${data.guild.guild_avatar}`
                        this.guildStore.currentGuild.backgroundUrl =resolveImageUrl(data.guild.guild_skin, defaultGuildBackground)
                        this.guildStore.currentGuild.myRole = data.guild.viewer_permission_level
                    }
                })
        },
        deleteGuild(){
            const ok = confirm("確定要解散這個公會嗎？此動作無法復原");
            if(!ok) return;

            const formData = new FormData();
            formData.append("guild_id",this.$route.params.id);

            fetch(`${API_BASE}/guild_delete_guild.php`, {
                method: "POST",
                headers: { Authorization: `Bearer ${this.userStore.token}` },
                body: formData,
            }).then(res => res.json()).then(data => {
                if(data.success){alert("公會已解散");
                    this.$router.push({name: 'guilds'});
                }else{alert(data.message);}
            });
        },
        uploadAvatar(event){
            const file = event.target.files[0];
            if(!file)return;
            
            const formData = new FormData();
            formData.append("guild_id", this.$route.params.id);
            formData.append("avatar", file);

            fetch(`${API_BASE}/guild_update_settings.php`,{
                method: "POST",
                headers: { Authorization: `Bearer ${this.userStore.token}` },
                body: formData,
            }).then(res => res.json()).then(data => {
                if(data.success){
                    this.loadGuildDetail(this.$route.params.id);
                }else{
                    alert(data.message);
                }
            });
        },
        uploadSkin(event){
            const file = event.target.files[0];
            if(!file) return;

            const formData = new FormData();
            formData.append("guild_id", this.$route.params.id);
            formData.append("skin", file);

            fetch(`${API_BASE}/guild_update_settings.php`, {
                method: "POST",
                headers: { Authorization: `Bearer ${this.userStore.token}` },
                body: formData,
            }).then(res => res.json()).then(data => {
                if(data.success){
                    this.loadGuildDetail(this.$route.params.id);
                }else{
                    alert(data.message);
                }
            });
        },
    },
}


</script>

<template>
    <GuildBreadcrumb variant="guild" :items="[
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
        <label class="guild-settings__btn guild-settings__btn--outline">
            更換背景圖片
            <input type="file" accept="image/jpeg,image/png,image/webp" style="display:none" @change="uploadSkin">
        </label>
    </div>

    <hr class="guild-settings__divider">

    <div class="guild-settings__section">
        <div class="guild-settings__info">
            <h2 class="guild-settings__title">讀書公會頭貼</h2>
            <img :src="guildStore.currentGuild.thumbnailImage" alt="公會頭貼預覽" class="guild-settings__avatar-preview">
        </div>
        <label class="guild-settings__btn guild-settings__btn--outline">
        更換頭貼
            <input type="file" accept="image/jpeg,image/png,image/webp" style="display:none" @change="uploadAvatar">
        </label>
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
            <h2 class="guild-settings__title">公告欄內容</h2>
            <textarea class="guild-settings__textarea" v-model="guildStore.currentGuild.announcementContent" placeholder="請輸入公布欄內容"></textarea>

        </div>
        <button class="guild-settings__btn guild-settings__btn--outline" @click="saveAnnouncement">儲存</button>
    </div>

    <template v-if="isGuildOwner">
        <hr class="guild-settings__divider">

        <div class="guild-settings__section">
            <div class="guild-settings__info">
                <h2 class="guild-settings__title guild-settings__title--danger">刪除讀書公會</h2>
                <p class="guild-settings__desc">僅公會長可執行，刪除後無法復原</p>
            </div>
            <button class="guild-settings__btn guild-settings__btn--danger" @click="deleteGuild">刪除公會</button>
        </div>
    </template>
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

    @include tablet {
        width: 90%;
    }

    &__section {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: $spacing-lg 0;
        gap: $spacing-lg;

        @include tablet {
            flex-direction: column;
            align-items: stretch;
        }
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

        @include mobile {
            width: 100%;
        }
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

        @include tablet {
            align-self: end;
        }
        

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
