<script setup>
import AppButton from "@/components/common/AppButton.vue";
import AppIcon from "@/components/common/AppIcon.vue";
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import { useRoute } from "vue-router";
import { ref, computed } from "vue";
import { useGuildStore } from "@/stores/guild";

const route = useRoute();
const guildStore = useGuildStore();

let nextId = guildStore.currentGuild.milestones.length
    ? Math.max(...guildStore.currentGuild.milestones.map(m => m.id)) + 1
    : 1;

function addCard() {
    guildStore.currentGuild.milestones.push({ id: nextId++, startChapter: "", endChapter: "", dueDate: "" });
}

function removeCard(id) {
    guildStore.currentGuild.milestones = guildStore.currentGuild.milestones.filter(c => c.id !== id);
}

const today = new Date().toISOString().slice(0, 10);

const errors = computed(() => {
    const e = {};
    guildStore.currentGuild.milestones.forEach((card, index) => {
        const cardErrors = {};
        if (card.startChapter === "" || card.endChapter === "") {
            cardErrors.range = "請輸入章節範圍";
        } else if (Number(card.startChapter) < 1 || Number(card.endChapter) < 1) {
            cardErrors.range = "章節不能小於 1";
        } else if (Number(card.endChapter) < Number(card.startChapter)) {
            cardErrors.range = "結束章節不能小於開始章節";
        }
        if (!card.dueDate) {
            cardErrors.dueDate = "請選擇預計完讀日期";
        } else if (false && card.dueDate < today) {
            cardErrors.dueDate = "完讀日期不能早於今天";
        } else if (index > 0 && card.dueDate < guildStore.currentGuild.milestones[index - 1].dueDate) {
            cardErrors.dueDate = "完讀日期不能早於前一個討論板";
        }
        if (Object.keys(cardErrors).length) e[card.id] = cardErrors;
    });
    return e;
});

const canSave = computed(() => Object.keys(errors.value).length === 0);
const attemptedSave = ref(false);
const saved = ref(false);

function save() {
    attemptedSave.value = true;
    if (!canSave.value) return;
    saved.value = true;
}


</script>

<template>
    <GuildBreadcrumb :items="[
    { label: '❮  公會主頁', to: `/front/guilds/${route.params.id}` },// guilds/:id 填入目前公會的 id
    { label: '設定讀書排程' }
]" />

<div class="reading-schedule">



    <div class="reading-book">
        <img src="@/assets/images/little-prince-cover.png" alt="小王子" class="reading-book__img">
                <div class="reading-book__meta">
                    <h2 class="reading-book__title">小王子</h2>
                    <div class="reading-book__list">
                        <p>作者：史蒂芬妮．梅爾</p>
                        <p>類別：奇幻小說</p>
                        <p>譯者：瞿秀蕙/ 安麗姬/ Liao, Sabrina</p>
                        <p>出版日期：2011/06/10</p>
                        <p>出版社：尖端出版</p>
                        <p>ISBN：000-0000000000</p>
                    </div>
                </div> 
                <div class="bnt-wrap">
                    <AppButton class="btn">更換當期讀物
                    </AppButton>
                </div>
            
                </div>        


<div class="schedule">
    <div class="schedule-card" v-for="(card, index) in guildStore.currentGuild.milestones" :key="card.id">
    <div class="schedule-card__header">
        <h3 class="schedule-card__title">章節分段討論板：{{ String(index + 1).padStart(2, '0') }}</h3>
        <button class="schedule-card__close" aria-label="關閉" @click="removeCard(card.id)">✕</button>
    </div>
    <div class="schedule-card__body">
        <div class="schedule-card__field">
            <label class="schedule-card__label">請輸入章節範圍</label>
            <div class="schedule-card__range">
            <input type="number"  min="1" class="schedule-card__input" placeholder="請輸入數字" v-model.number="card.startChapter">
            <span class="schedule-card__tilde">～</span>
            <input type="number"  min="1" class="schedule-card__input" placeholder="請輸入數字" v-model.number="card.endChapter">
            <span class="schedule-card__unit">章節</span>
        </div>
        <p v-if="attemptedSave && errors[card.id]?.range" class="schedule-card__error">{{ errors[card.id].range }}</p>
        </div>
        <div class="schedule-card__field">
        <label class="schedule-card__label">預計完讀日期</label>
        <input type="date" class="schedule-card__input schedule-card__input--date" v-model="card.dueDate">
        <p v-if="attemptedSave && errors[card.id]?.dueDate" class="schedule-card__error">{{ errors[card.id].dueDate }}</p>
        </div>
    </div>
    </div>
        <div class="schedule-btn">
        <p v-if="saved" class="schedule-btn__done">排程已儲存！</p>
        <button type="button" class="schedule-btn__wraps" @click="addCard">
            <AppIcon name="plus" />點選新增討論區
        </button>
        <AppButton class="btn" @click="save">儲存排程</AppButton>
        </div>
</div>

</div>
</template>

<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.reading-schedule{
    width: 85%;
    display: flex;
    margin: 0 auto;
    align-items: flex-start;
    gap: $spacing-lg;

    @include tablet {
        flex-direction: column;
        width: 90%;
    }
}

.reading-book{
    width: 35%;
    position: sticky;
    top:0px;
    display: flex;
    flex-direction: column;

    @include tablet {
        display: grid;
        grid-template-columns: auto 1fr;   // 左欄跟圖片一樣寬，右欄吃剩下空間
        grid-template-rows: auto auto;
        column-gap: $spacing-md;
        row-gap: $spacing-sm;
        width: 100%;
    }

    @include mobile {
        display: flex;
        flex-direction: column;
    }

    &__img{
    width: 300px;
    height: auto;
    aspect-ratio: 174 / 246;
    object-fit: cover;
    flex-shrink: 0;

    @include tablet {
            grid-column: 1;
            grid-row: 1 / span 2;   // 貫穿兩列，跟右邊 meta+按鈕 一樣高
        }
    }

    

    &__meta{
        display: flex;
        flex-direction: column;
        gap: $spacing-sm;

        @include tablet {
            grid-column: 2;
            grid-row: 1;
        }
    }

    &__title{
    font-size: $h6-size;
    font-weight: $heading-weight;
    line-height: $heading-line-height;
    color: $primary;
    margin: 0;
    }

    &__list{
    display: flex;
    flex-direction: column;
    gap: $spacing-xs;

    p{
        margin: 0;
        font-size: $p-sm-size;
        color: $neutral-800;
    }
    }
}

.bnt-wrap{
    margin: $spacing-lg 0px;
    transition: transform .2s ease, background .2s ease, box-shadow .2s;

    @include tablet {
        grid-column: 2;
        grid-row: 2;    // 跟 meta 同一欄，在它下面
    }

    &:hover {
        background: $neutral-100;
        transform: translateY(-2px);
        
    }
}

.schedule{
    display: flex;
    flex-direction: column;
    gap: $spacing-sm;
    width: 65%;

    @include tablet {
        width: 100%;
    }
}
.schedule-card {
    
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

    &__error {
    margin: 0;
    font-size: $p-sm-size;
    color: #C73333;
}
}

.schedule-btn{
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: $spacing-md;


    &__wraps{
        display:flex;
        margin: $spacing-xl auto;
        align-items: center;
        gap: $spacing-xs;
        color: $primary;
        transition: transform .2s ease, background .2s ease;
    }

    &:hover {
        background: $neutral-100;
        transform: translateY(-2px);
    }

    &__done {
        margin: 0;
        color: $primary;
        font-size: $p-sm-size;
    }
}

@include tablet {

}

</style>