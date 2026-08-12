<script setup>
import AppButton from "@/components/common/AppButton.vue";
import AppIcon from "@/components/common/AppIcon.vue";
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import { useRoute, useRouter } from "vue-router";
import { ref, computed } from "vue";
import { useGuildStore } from "@/stores/guild";
import currentBookCover from "@/assets/images/little-prince-cover.png";


const route = useRoute();
const hasLeader = ref(false);
const leaderId = ref("");
const eventDate = ref("2026-09-15");
const deadlineDate = ref("2026-08-24");
const eventFormat = ref("offline");
const location = ref("320桃園市中壢區舊明里長安街1之13號");
const startHour = ref("19");
const startMinute = ref("00");
const endHour = ref("21");
const endMinute = ref("00");
const hourOptions = Array.from({ length: 24 }, (_, i) => String(i).padStart(2, "0"));
const minuteOptions = ["00", "15", "30", "45"];
const peopleLimit = ref(2);

const router = useRouter();
const guildStore = useGuildStore();
const attemptedSubmit = ref(false);
const isSubmitted = ref(false);

const errors = computed(() => {
    const e = {};
    if (!eventDate.value) e.eventDate = "請選擇活動日期";
    if (!deadlineDate.value) e.deadlineDate = "請選擇報名截止時間";
    else if (deadlineDate.value > eventDate.value) e.deadlineDate = "報名截止時間不能晚於活動日期";
    if (!eventFormat.value) e.eventFormat = "請選擇活動形式";
    if (!location.value.trim()) e.location = "請輸入活動地點";
    if (hasLeader.value && !leaderId.value.trim()) e.leaderId = "請輸入領讀人 ID";

    const startMinutes = Number(startHour.value) * 60 + Number(startMinute.value);
    const endMinutes = Number(endHour.value) * 60 + Number(endMinute.value);
    if (startMinutes >= endMinutes) e.time = "開始時間不能晚於結束時間";

    if (!peopleLimit.value || peopleLimit.value < 1) e.peopleLimit = "請輸入人數限制";
    return e;
});

const canSubmit = computed(() => Object.keys(errors.value).length === 0);

function submit() {
    attemptedSubmit.value = true;
    if (!canSubmit.value) return;

    const weekdays = ['日', '一', '二', '三', '四', '五', '六'];
    const [y, m, d] = eventDate.value.split('-');
    const weekday = weekdays[new Date(eventDate.value).getDay()];

    guildStore.currentGuild.events.push({
        eventId: Date.now(),
        bookName: '小王子',
        author: '史蒂芬妮．梅爾',
        coverImage: currentBookCover,
        eventType: eventFormat.value === 'offline' ? '線下活動' : '線上活動',
        eventTime: `${y}.${m}.${d} (${weekday}) ${startHour.value}:${startMinute.value} - ${endHour.value}:${endMinute.value} (GMT+8)`,
        location: location.value,
        participantCount: 0,
    });

    isSubmitted.value = true;
}

function goToGuild() {
    router.push(`/guilds/${route.params.id}`);
}
</script>

<template>

    <GuildBreadcrumb :items="[
    { label: '❮  公會主頁', to: `/guilds/${route.params.id}` },// guilds/:id 填入目前公會的 id
    { label: '建立讀書會活動' }
]" />


    <div class="event-form">
    <div class="event-form__book">
        <img src="@/assets/images/little-prince-cover.png" alt="小王子" class="event-form__book-cover">
        <div class="event-form__book-meta">
            <h2 class="event-form__book-title">小王子</h2>
            <div class="event-form__book-list">
                <p>作者：史蒂芬妮．梅爾</p>
                <p>類別：奇幻小說</p>
                <p>譯者：瞿秀蕙/ 安麗姬/ Liao, Sabrina</p>
                <p>出版日期：2011/06/10</p>
                <p>出版社：尖端出版</p>
                <p>ISBN：000-0000000000</p>
            </div>
        </div>
    </div>

    <div class="event-form__fields">
        <div class="event-form__row">
            <div class="event-form__host">
                <img src="@/assets/images/guild/girl.png" alt="小森讀取中" class="event-form__host-avatar">
                <div class="event-form__host-info">
                    <span class="event-form__host-label">活動發起人</span>
                    <div class="event-form__host-name-row">
                        <span class="event-form__host-name">小森</span>
                        <span class="event-form__host-id">BKD00003</span>
                    </div>
                </div>
            </div>

            <div class="event-form__leader">
                <div class="event-form__leader-top">
                    <label class="event-form__checkbox-label">
                        <input type="checkbox" class="event-form__checkbox" v-model="hasLeader">
                        領讀人
                    </label>
                    <span class="event-form__leader-hint">若有安排可勾選並填寫ID</span>
                </div>
                <input type="text" class="event-form__leader-input" placeholder="請輸入ID" v-model="leaderId">
                <p v-if="attemptedSubmit && errors.leaderId" class="event-form__error">{{ errors.leaderId }}</p>
            </div>
        </div>

        <div class="event-form__row">
            <div class="event-form__field">
                <label class="event-form__label">活動日期<span class="event-form__required">*</span></label>
                <input type="date" class="event-form__input event-form__input--date" v-model="eventDate">
                <p v-if="attemptedSubmit && errors.eventDate" class="event-form__error">{{ errors.eventDate }}</p>
            </div>

            <div class="event-form__field">
                <label class="event-form__label">報名截止時間<span class="event-form__required">*</span></label>
                <input type="date" class="event-form__input event-form__input--date" v-model="deadlineDate">
                <p v-if="attemptedSubmit && errors.deadlineDate" class="event-form__error">{{ errors.deadlineDate }}</p>
            </div>
        </div>

        <div class="event-form__field event-form__field--full">
            <label class="event-form__label">活動形式<span class="event-form__required">*</span></label>
            <select class="event-form__input event-form__input--select" v-model="eventFormat">
                <option value="offline">線下活動</option>
                <option value="online">線上活動</option>
            </select>
            <p v-if="attemptedSubmit && errors.eventFormat" class="event-form__error">{{ errors.eventFormat }}</p>
        </div>

        <div class="event-form__field event-form__field--full">
            <label class="event-form__label">活動地點<span class="event-form__required">*</span></label>
            <input type="text" class="event-form__input event-form__input--location" v-model="location" placeholder="請輸入活動地點">
            <p v-if="attemptedSubmit && errors.location" class="event-form__error">{{ errors.location }}</p>
        </div>
        

        <div class="event-form__row">
            <div class="event-form__field">
                <label class="event-form__label">當日預計活動時程<span class="event-form__required">*</span></label>
                <div class="event-form__time-range">
                    <select class="event-form__input event-form__input--time" v-model="startHour">
                        <option v-for="h in hourOptions" :key="h" :value="h">{{ h }}</option>
                    </select>
                    <span class="event-form__tilde">:</span>
                    <select class="event-form__input event-form__input--time" v-model="startMinute">
                        <option v-for="m in minuteOptions" :key="m" :value="m">{{ m }}</option>
                    </select>
                    <span class="event-form__tilde">～</span>
                    <select class="event-form__input event-form__input--time" v-model="endHour">
                        <option v-for="h in hourOptions" :key="h" :value="h">{{ h }}</option>
                    </select>
                    <span class="event-form__tilde">:</span>
                    <select class="event-form__input event-form__input--time" v-model="endMinute">
                        <option v-for="m in minuteOptions" :key="m" :value="m">{{ m }}</option>
                    </select>
                </div>
                <p v-if="attemptedSubmit && errors.time" class="event-form__error">{{ errors.time }}</p>
            </div>

            <div class="event-form__field">
                <label class="event-form__label">人數限制</label>
                <input type="number" min="1" class="event-form__input" v-model.number="peopleLimit" placeholder="請輸入人數限制">
                <p v-if="attemptedSubmit && errors.peopleLimit" class="event-form__error">{{ errors.peopleLimit }}</p>
            </div>
            </div>
        </div>
    </div>

<div class="bnt-wrap" v-if="!isSubmitted">
<AppButton class="btn" @click="submit">確認創立活動</AppButton>
</div>
<div class="event-form__done" v-else>
    <p class="event-form__done-text">活動已成功建立！</p>
    <AppButton class="btn" @click="goToGuild">返回公會主頁</AppButton>
</div>
</template>

<style lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.event-form {
    width: 80%;
    display: flex;
    margin: 0 auto;
    align-items: flex-start;
    gap: $spacing-xl;

    &__book {
        display: flex;
        flex-direction: column;
        gap: $spacing-lg;
        flex: 0 0 30%;
    }

    &__book-cover {
        width: 100%;
        aspect-ratio: 238 / 336;
        object-fit: cover;
    }

    &__book-title {
        font-size: $h5-size;
        font-weight: $heading-weight;
        line-height: $heading-line-height;
        color: $primary;
        margin: 0 0 $spacing-sm;
    }

    &__book-meta {
        display: flex;
        flex-direction: column;
        gap: $spacing-sm;
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

    &__fields {
        display: flex;
        flex-direction: column;
        gap: $spacing-xl;
        flex: 1;
    }

    &__row {
        display: flex;
        align-items: flex-start;
        gap: $spacing-xl;
    }

    &__host {
        display: flex;
        align-items: center;
        gap: $spacing-sm;
    }

    &__host-avatar {
        width: 49px;
        height: 49px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    &__leader {
        flex: 1;
        min-height: 76px;
        padding: $spacing-sm;
        background: $secondary-100;
        border: 1px solid $secondary;
        border-radius: 5px;
        display: flex;
        flex-direction: column;
        gap: $spacing-xs;
    }

    &__leader-input {
        width: 100%;
        box-sizing: border-box;
        min-height: 27px;
        padding: $spacing-xs $spacing-sm;
        background: #fff;
        border: 1px solid $neutral-400;
        border-radius: 5px;
        font-size: $p-xs-size;
        color: $neutral-500;
    }

    &__field {
        display: flex;
        flex-direction: column;
        gap: $spacing-sm;
        flex: 1;

        &--full {
            width: 100%;
        }
    }

    &__input {
        width: 100%;
        box-sizing: border-box;
        min-height: 48px;
        padding: $spacing-sm $spacing-lg;
        background: #fff;
        border: 1px solid $neutral-400;
        border-radius: 5px;
        font-size: $p-md-size;
        color: $neutral-500;
        font-family: inherit;

        &--select {
            color: $neutral-500;
        }

        &--location {
            color: $neutral-800;
        }

        &--time {
            width: auto;
            min-width: 72px;
            padding: $spacing-sm;
        }
    }

    &__time-range {
        display: flex;
        align-items: center;
        gap: $spacing-sm;
    }
    &__error {
        margin: 0;
        font-size: $p-sm-size;
        color: #C73333;
    }

    &__done {
        max-width: 480px;
        margin: $spacing-xl auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: $spacing-md;
        text-align: center;
    }

    &__done-text {
        font-size: $p-lg-size;
        color: $primary;
    }
}

.bnt-wrap{
    
    margin: $spacing-xl 0px;
    display: flex;
    justify-content: center;
}


</style>