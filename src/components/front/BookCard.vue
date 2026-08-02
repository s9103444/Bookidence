<script setup>
    import AppButton from '@/components/common/AppButton.vue';
    import AppIcon from '@/components/common/AppIcon.vue';

</script>

<template>
    <article class="book-card">
        <div class="book-card__cover">
            <img src="@/assets/images/little-prince-cover.png" alt="">
        </div>

        <div class="book-card__body">
            <h3 class="book-card__title">
                北歐時間：世界第一幸福國度教會我的事
            </h3>
            <p class="book-card__author">詹姆斯・克利爾</p>
            <ul class="book-card__tags">
                <li class="book-card__tag">心理成長</li>
                <li class="book-card__tag">習慣養成</li>
            </ul>
            <p class="book-card__desc">細微改變帶來巨大成就的實踐法則，幫助你建立好習慣，打破壞習慣。</p>
        </div>
        <AppButton color="primary" size="sm">
            查看詳情
            <AppIcon name="arrow-right"></AppIcon>
        </AppButton>
    </article>
</template>

<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;

.book-card {
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 319px;
    padding: 16px;
    border-radius: 5px;
    //  待確認 —— 這個顏色在 Figma 是寫死的，不是變數
    background-color: #fff8ea;
}

.book-card__cover {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 237px;
}

.book-card__cover img {
    width: 159px;
    box-shadow: 0 4px 4px rgb(0 0 0 / 0.25);
}

.book-card__body {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.book-card__title {
    font-size: $label-lg-size;
    font-weight: $text-weight;
    color: $neutral-800;
    letter-spacing: $letter-spacing-base;
}

.book-card__author {
    font-size: $label-md-size;
    font-weight: $text-weight;
    color: $neutral-600;
    letter-spacing: $letter-spacing-base;
}

.book-card__tags {
    display: flex;
    gap: 8px;
    margin-top: 12px;
}

.book-card__tag {
    padding: 8px 12px;
    border: 1px solid $primary;
    border-radius: 5px;
    background-color: $neutral-100;
    font-size: $label-sm-size;
    font-weight: $text-weight;
    color: $primary;
    letter-spacing: $letter-spacing-base;
}

.book-card__desc {
    margin-top: 13px;
    font-size: $label-xs-size;
    font-weight: $text-weight;
    color: $neutral-700;
    letter-spacing: $letter-spacing-base;
}
</style>