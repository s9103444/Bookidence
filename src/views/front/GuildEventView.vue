<script setup>
import AppButton from "@/components/common/AppButton.vue";
import AppIcon from "@/components/common/AppIcon.vue";
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import { useRoute } from "vue-router";
import { ref, onMounted, computed } from "vue";
import { API_BASE, API_STATIC } from "@/common/api";
import { useUserStore } from "@/stores/user";

const route = useRoute();
const userStore = useUserStore();

const event = ref(null);
const agreed = ref(false);
const canRegister = computed(() => {
    if (!event.value) return false;
    const isFull = event.value.participant_count >= event.value.max_participants;
    const today = new Date().toISOString().slice(0, 10);
    const isPastDeadline = event.value.deadline < today;
    return !isFull && !isPastDeadline;
});

function loadEvent(){
    fetch(`${API_BASE}/guild_get_events.php?event_id=${route.params.eventId}`)
    .then(res => res.json()).then(data => {
        if(data.success){
            event.value = data.event;
        }
    });
}

onMounted(() => {
    loadEvent();
});

function register() {
    if (!agreed.value) {
        alert("請先閱讀並同意活動說明");
        return;
    }

    const formData = new FormData();
    formData.append("event_id", route.params.eventId);

    fetch(`${API_BASE}/guild_register_event.php`, {
        method: "POST",
        headers: { Authorization: `Bearer ${userStore.token}` },
        body: formData,
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("報名成功！");
                loadEvent();
            } else {
                alert(data.message);
            }
        });
}
</script>

<template>

<GuildBreadcrumb :items="[
    { label: '❮  公會主頁', to: `/front/guilds/${route.params.id}` },// guilds/:id 填入目前公會的 id
    { label: '報名讀書會活動' }
]" />

    <div class="event-detail" v-if="event">
        <div class="event-detail__card event-detail__card--main">
            <div class="event-detail__guild">
                <img
                :src="event.guild_avatar.startsWith('http') ? event.guild_avatar : `${API_STATIC}/uploads/${event.guild_avatar}`"
                :alt="event.guild_name"
                class="event-detail__guild-avatar">
                <div class="event-detail__guild-info">
                    <span class="event-detail__guild-label">讀書公會</span>
                    <span class="event-detail__guild-name">{{ event.guild_name }}</span>
                </div>
            </div>

            <div class="event-detail__book">
                <img
                :src="event.bc_image.startsWith('http') ? event.bc_image : `${API_STATIC}/uploads/${event.bc_image}`"
                :alt="event.book_title"
                class="event-detail__book-cover">
            <div class="event-detail__book-meta">
                <h2 class="event-detail__book-title">{{ event.book_title }}</h2>
                <div class="event-detail__book-list">
                    <p>作者：{{ event.book_author }}</p>
                    <p>出版日期：{{ event.book_p_date }}</p>
                    <p>出版社：{{ event.book_publisher }}</p>
                    <p>ISBN：{{ event.book_isbn }}</p>
                </div>
            </div>
        </div>

            <div class="event-detail__summary">
                <div class="event-detail__summary-item">
                    <span class="event-detail__status-label">活動類型</span>
                    <span class="event-detail__status-value">{{ event.event_type.includes('線上') ? '線上活動' : '線下活動' }}</span>
                </div>
                <div class="event-detail__summary-item">
                    <span class="event-detail__status-label">截止時間</span>
                    <span class="event-detail__status-value">{{ event.deadline }}</span>
                </div>
                <div class="event-detail__signup">
                    <AppIcon name="users" :size="14" class="event-detail__signup-icon" />
                    <span class="event-detail__signup-text">已報名</span>
                    <span class="event-detail__signup-count">{{ event.participant_count }} / {{ event.max_participants }}</span>
                </div>
            </div>
        </div>

        <div class="event-detail__card event-detail__card--info">
            <div class="event-detail__organizer">
                <div class="event-detail__person">
                    <img src="@/assets/images/guild/girl.png" alt="" class="event-detail__person-avatar">
                    <div class="event-detail__person-info">
                        <span class="event-detail__person-label">活動發起人</span>
                        <div class="event-detail__person-name-row">
                            <span class="event-detail__member-name">{{ event.organizer_name }}</span>
                            <span class="event-detail__member-id">{{ event.organizer_member_code }}</span>
                        </div>
                    </div>
                </div>

                <div class="event-detail__guide">
                    <img src="@/assets/images/guild/boy.png" alt="" class="event-detail__person-avatar">
                    <div class="event-detail__guide-info">
                        <span class="event-detail__person-label">本期領讀人</span>
                        <div class="event-detail__person-name-row">
                            <span class="event-detail__member-name">{{ event.leader_name }}</span>
                            <span class="event-detail__member-id">{{ event.leader_member_code }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="event-detail__meta">
                <div class="event-detail__meta-row">
                    <span class="event-detail__tag">
                        <AppIcon name="calendar" :size="14" class="event-detail__tag-icon" />
                        活動時間
                    </span>
                    <span class="event-detail__meta-value">{{ event.event_date.replaceAll('-', '.') }} | {{ event.event_time.slice(0, 5) }} ~ {{ event.event_end_time.slice(0, 5) }}</span>
                </div>

                <div class="event-detail__meta-row">
                    <span class="event-detail__tag">
                        <AppIcon name="map-pin" :size="14" class="event-detail__tag-icon" />
                        {{ event.event_type.includes('線上') ? '會議連結' : '活動地點' }}
                    </span>
                    <span class="event-detail__meta-address">{{ event.event_location || event.meeting_url }}</span>
                </div>
            </div>

            <div class="event-detail__introduce">
                <span class="event-detail__introduce-title">活動說明</span>
                <p class="event-detail__introduce-text">
                    {{ event.description }}
                </p>
            </div>

            <div class="event-detail__notice">
                <span class="event-detail__notice-icon">⚠</span>
                <span class="event-detail__notice-text">參加實體活動請注意自身安全，建議選擇公共場所。未成年人請由監護人陪同</span>
            </div>
        </div>
    </div>

    <template v-if="event">
        <div v-if="canRegister" class="event-detail__agree">
            <input type="checkbox" id="agree-checkbox" class="event-detail__agree-checkbox" v-model="agreed">
            <label for="agree-checkbox" class="event-detail__agree-label">我已閱讀活動說明並同意報名此活動</label>
        </div>
        <div v-if="canRegister" class="bnt-wrap">
            <AppButton class="btn" @click="register">確認報名活動</AppButton>
        </div>
        <p v-else class="event-detail__closed-text">
            {{ event.participant_count >= event.max_participants ? '報名人數已額滿' : '已超過報名截止時間' }}
        </p>
    </template>

</template>
<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.event-detail {
    width: 90%;
    display: flex;
    margin: 0 auto;
    align-items: flex-start;
    gap: $spacing-lg;

    @media (max-width: 1024px) {
        flex-direction: column;
        width: 95%;
    }

    &__card {
        border-radius: 5px;
        padding: $spacing-xl $spacing-lg;
        display: flex;
        flex-direction: column;
        gap: $spacing-lg;

        &--main {
            background: $secondary-100;
            flex: 1;
        }

        &--info {
            background: #fff;
            flex: 1;
        }
    }

    &__guild {
        display: flex;
        align-items: center;
        gap: $spacing-sm;
    }

    &__guild-avatar {
        width: 51px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    &__guild-info {
        display: flex;
        flex-direction: column;
    }

    &__guild-label {
        font-size: $p-sm-size;
        color: $primary;
    }

    &__guild-name {
        font-size: $p-lg-size;
        color: $neutral-800;
    }

    &__book {
        display: flex;
        align-items: flex-end;
        gap: $spacing-lg;

        @include mobile {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    &__book-cover {
        width: 174px;
        height: auto;
        aspect-ratio: 174 / 246;
        object-fit: cover;
        flex-shrink: 0;
    }

    &__book-meta {
        display: flex;
        flex-direction: column;
        gap: $spacing-sm;
    }

    &__book-title {
        font-size: $h6-size;
        font-weight: $heading-weight;
        line-height: $heading-line-height;
        color: $primary;
        margin: 0;
    }

    &__book-list {
        display: flex;
        flex-direction: column;
        gap: $spacing-xs;

        p {
            margin: 0;
            font-size: $p-sm-size;
            color: $primary;
        }
    }

    &__summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: $spacing-lg;

        @include mobile {
            flex-wrap: wrap;
        }
    }

    &__summary-item {
        display: flex;
        flex-direction: column;
    }

    &__status-label {
        font-size: $p-md-size;
        color: $primary;
    }

    &__status-value {
        font-size: $p-lg-size;
        color: $neutral-800;
    }

    &__signup {
        display: flex;
        align-items: center;
        gap: $spacing-xs;
        padding: $spacing-xs $spacing-md;
        background: $primary-500;
        border-radius: 999px;
        color: #fff;
    }

    &__signup-icon {
        font-size: $p-xs-size;
    }

    &__signup-text,
    &__signup-count {
        font-size: $p-xs-size;
        color: #fff;
    }

    &__organizer {
        display: flex;
        align-items: center;
        gap: $spacing-xl;

        @include mobile {
            flex-direction: column;
            align-items: flex-start;
            gap: $spacing-md;
        }
    }

    &__person,
    &__guide {
        display: flex;
        align-items: center;
        gap: $spacing-sm;
    }

    &__person-avatar {
        width: 49px;
        height: 49px;
        border-radius: 50%;
        object-fit: cover;
    }

    &__person-info,
    &__guide-info {
        display: flex;
        flex-direction: column;
    }

    &__person-label {
        font-size: $p-xs-size;
        color: $primary;
    }

    &__person-name-row {
        display: flex;
        align-items: center;
        gap: $spacing-xs;
    }

    &__member-name {
        font-size: $p-md-size;
        color: $neutral-800;
    }

    &__member-id {
        font-size: $p-xs-size;
        color: $neutral-500;
    }

    &__meta {
        display: flex;
        flex-direction: column;
        gap: $spacing-md;
    }

    &__meta-row {
        display: flex;
        align-items: center;
        gap: $spacing-sm;

        @include mobile {
            flex-wrap: wrap;
        }
    }

    &__tag {
        display: flex;
        align-items: center;
        gap: $spacing-xs;
        padding: $spacing-xs $spacing-md;
        border: 1px solid $primary;
        border-radius: 5px;
        font-size: $p-sm-size;
        color: $primary;
        white-space: nowrap;
    }

    &__tag-icon {
        font-size: $p-xs-size;
    }

    &__meta-value,
    &__meta-address {
        font-size: $p-md-size;
        color: $neutral-800;
    }

    &__introduce {
        display: flex;
        flex-direction: column;
        gap: $spacing-sm;
    }

    &__introduce-title {
        font-size: $p-md-size;
        color: $neutral-700;
    }

    &__introduce-text {
        margin: 0;
        font-size: $p-sm-size;
        color: $neutral-800;
        line-height: $text-line-height;
    }

    &__notice {
        display: flex;
        align-items: center;
        gap: $spacing-xs;
    }

    &__notice-icon {
        font-size: $p-sm-size;
        color: $primary-300;
    }

    &__notice-text {
        font-size: $p-xs-size;
        color: $primary-300;
    }

    &__agree {
        display: flex;
        align-items: center;
        gap: $spacing-sm;
        justify-content: center;
        margin: $spacing-xl auto 0px;
    }

    &__agree-label {
        font-size: $p-sm-size;
        color: $neutral-600;
        cursor: pointer;
    }

    &__agree-label {
        font-size: $p-sm-size;
        color: $neutral-600;
        cursor: pointer;
    }
}

.bnt-wrap{
    display: flex;
    justify-content: center;

}
.btn{
    display: block;
    margin:40px auto ;
    
}
</style>