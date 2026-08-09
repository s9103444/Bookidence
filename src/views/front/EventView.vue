<script setup>
import AppButton from "@/components/common/AppButton.vue";
import AppIcon from "@/components/common/AppIcon.vue";
</script>

<template>


    <!-- ====================================讀書會活動詳情===================================== -->

    <div class="event-detail">
        <div class="event-detail__card event-detail__card--main">
            <div class="event-detail__guild">
                <img src="../../assets/images/guild/guildAvatar.png" alt="壁爐與貓" class="event-detail__guild-avatar">
                <div class="event-detail__guild-info">
                    <span class="event-detail__guild-label">讀書公會</span>
                    <span class="event-detail__guild-name">壁爐與貓</span>
                </div>
            </div>

            <div class="event-detail__book">
                <img src="../../assets/images/little-prince-cover.png" alt="小王子" class="event-detail__book-cover">
                <div class="event-detail__book-meta">
                    <h2 class="event-detail__book-title">小王子</h2>
                    <div class="event-detail__book-list">
                        <p>作者：史蒂芬妮．梅爾</p>
                        <p>類別：奇幻小說</p>
                        <p>譯者：瞿秀蕙/ 安麗姬/ Liao, Sabrina</p>
                        <p>出版日期：2011/06/10</p>
                        <p>出版社：尖端出版</p>
                        <p>ISBN：000-0000000000</p>
                    </div>
                </div>
            </div>

            <div class="event-detail__summary">
                <div class="event-detail__summary-item">
                    <span class="event-detail__status-label">活動類型</span>
                    <span class="event-detail__status-value">線下活動</span>
                </div>
                <div class="event-detail__summary-item">
                    <span class="event-detail__status-label">截止時間</span>
                    <span class="event-detail__status-value">2026.08.24</span>
                </div>
                <div class="event-detail__signup">
                    <span class="event-detail__signup-icon">👤</span>
                    <span class="event-detail__signup-text">已報名</span>
                    <span class="event-detail__signup-count">4 / 10</span>
                </div>
            </div>
        </div>

        <div class="event-detail__card event-detail__card--info">
            <div class="event-detail__organizer">
                <div class="event-detail__person">
                    <img src="../../assets/images/guild/girl.png" alt="小森讀取中" class="event-detail__person-avatar">
                    <div class="event-detail__person-info">
                        <span class="event-detail__person-label">活動發起人</span>
                        <div class="event-detail__person-name-row">
                            <span class="event-detail__member-name">小森</span>
                            <span class="event-detail__member-id">BKD00003</span>
                        </div>
                    </div>
                </div>

                <div class="event-detail__guide">
                    <img src="../../assets/images/guild/boy.png" alt="泡泡小鹿" class="event-detail__person-avatar">
                    <div class="event-detail__guide-info">
                        <span class="event-detail__person-label">本期領讀人</span>
                        <div class="event-detail__person-name-row">
                            <span class="event-detail__member-name">泡泡小鹿</span>
                            <span class="event-detail__member-id">BKD00072</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="event-detail__meta">
                <div class="event-detail__meta-row">
                    <span class="event-detail__tag">
                        <span class="event-detail__tag-icon">📅</span>
                        活動時間
                    </span>
                    <span class="event-detail__meta-value">2026.09.15 | 14:00 ~ 16:00</span>
                </div>

                <div class="event-detail__meta-row">
                    <span class="event-detail__tag">
                        <span class="event-detail__tag-icon">📍</span>
                        活動地點
                    </span>
                    <span class="event-detail__meta-address">320桃園市中壢區舊明里長安街1之13號</span>
                </div>
            </div>

            <div class="event-detail__introduce">
                <span class="event-detail__introduce-title">活動說明</span>
                <p class="event-detail__introduce-text">
                    你也曾經是那個會畫出「吞了大象的蟒蛇」，卻被大人說是帽子的孩子嗎？<br>
                    這次我們想找幾位一樣還記得那份天真的人，一起在咖啡香裡重新翻開《小王子》。不需要準備什麼深奧的見解，帶著你對那朵玫瑰、那隻狐狸、或是那片星空的想法來就好——聊聊我們是不是也曾經，在長大的路上不小心弄丟了自己的星球。
                </p>
            </div>

            <div class="event-detail__notice">
                <span class="event-detail__notice-icon">⚠</span>
                <span class="event-detail__notice-text">參加實體活動請注意自身安全，建議選擇公共場所。未成年人請由監護人陪同</span>
            </div>
        </div>
    </div>

    <div class="event-detail__agree">
        <input type="checkbox" id="agree-checkbox" class="event-detail__agree-checkbox">
        <label for="agree-checkbox" class="event-detail__agree-label">我已閱讀活動說明並同意報名此活動</label>
    </div>
    <div class="bnt-wrap">
<AppButton class="btn">確認報名活動
</AppButton>
</div>

    

</template>
<style lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;


//====================================讀書會活動詳情=====================================

.event-detail {
    width: 90%;
    display: flex;
    margin: 0 auto;
    align-items: flex-start;
    gap: $spacing-lg;

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