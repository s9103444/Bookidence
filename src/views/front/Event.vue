
<script></script>

<template>
    <div class="event-form">
    <div class="event-form__book">
        <img src="./img37.png" alt="小王子" class="event-form__book-cover">
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
                <img src="./img36.png" alt="小森讀取中" class="event-form__host-avatar">
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
                        <input type="checkbox" class="event-form__checkbox">
                        領讀人
                    </label>
                    <span class="event-form__leader-hint">若有安排可勾選並填寫ID</span>
                </div>
                <input type="text" class="event-form__leader-input" placeholder="請輸入ID">
            </div>
        </div>

        <div class="event-form__row">
            <div class="event-form__field">
                <label class="event-form__label">活動日期<span class="event-form__required">*</span></label>
                <div class="event-form__input event-form__input--date">
                    <span>2026/09/15</span>
                    <span class="event-form__icon-calendar">📅</span>
                </div>
            </div>

            <div class="event-form__field">
                <label class="event-form__label">報名截止時間<span class="event-form__required">*</span></label>
                <div class="event-form__input event-form__input--date">
                    <span>2026/08/24</span>
                    <span class="event-form__icon-calendar">📅</span>
                </div>
            </div>
        </div>

        <div class="event-form__field event-form__field--full">
            <label class="event-form__label">活動形式<span class="event-form__required">*</span></label>
            <div class="event-form__input event-form__input--select">
                <span>線下活動</span>
                <span class="event-form__icon-arrow">▾</span>
            </div>
        </div>

        <div class="event-form__field event-form__field--full">
            <label class="event-form__label">活動地點<span class="event-form__required">*</span></label>
            <div class="event-form__input event-form__input--location">
                <span>320桃園市中壢區舊明里長安街1之13號</span>
            </div>
        </div>

        <div class="event-form__row">
            <div class="event-form__field">
                <label class="event-form__label">當日預計活動時程<span class="event-form__required">*</span></label>
                <div class="event-form__time-range">
                    <div class="event-form__input event-form__input--time">請輸入時間</div>
                    <span class="event-form__tilde">～</span>
                    <div class="event-form__input event-form__input--time">請輸入時間</div>
                </div>
            </div>

            <div class="event-form__field">
                <label class="event-form__label">人數限制</label>
                <div class="event-form__input event-form__input--select">
                    <span>2</span>
                    <span class="event-form__icon-arrow">▾</span>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<style lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.event-form {
    width: 60%;
    display: flex;
    justify-content: space-between;
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
        min-height: 48px;
        padding: $spacing-sm $spacing-lg;
        background: #fff;
        border: 1px solid $neutral-400;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: $p-md-size;
        color: $neutral-500;
        white-space: nowrap;

        &--select {
            color: $neutral-500;
        }

        &--location {
            color: $neutral-800;
        }

        &--time {
            flex: 1;
            min-width: 100px;
            justify-content: flex-start;
        }
    }

    &__time-range {
        display: flex;
        align-items: center;
        gap: $spacing-sm;
    }
}

</style>