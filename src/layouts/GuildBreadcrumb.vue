<script setup>
// 全站共用的麵包屑，公會相關頁面跟會員專區都在用。
// 這兩邊的內容置中方式不一樣（公會頁面是各自 width:% + margin:auto 置中，
// 會員專區統一用 .container-content 網格系統），左邊留白沒辦法共用同一個值，
// 所以用 variant prop 切換：
//   variant="guild"  公會相關頁面用，跟內容大致對齊（多數頁面是 80% 寬置中）
//   variant="member" 會員專區頁面用，精確對齊 .container-content 的網格留白
// 不傳的話維持原本沒有額外左邊留白的樣子。

defineProps({
    items: {
        type: Array,
        required: true,
    },
    variant: {
        type: String,
        default: '',
        validator: (v) => ['', 'guild', 'member'].includes(v),
    },
});
</script>

<template>

    <nav class="breadcrumb" :class="variant && `breadcrumb--${variant}`">
        <ol>
            <li v-for="(item, index) in items" :key="index">
                <router-link v-if="item.to" :to="item.to">{{ item.label }}</router-link>
                <span v-else aria-current="page">{{ item.label }}</span>
            </li>
        </ol>
    </nav>
</template>

<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;


.breadcrumb {
    padding: $spacing-xl 0 $spacing-md;

    &--guild {
        padding-left: 10%;
    }

    &--member {
        padding-left: $content-grid-margin;
    }
    
    ol {
        display: flex;
        align-items: center;
        gap: $spacing-xs;
        font-size: $p-lg-size;
        margin-block: $spacing-sm;
        padding: 0;
        list-style: none;
    }

    li {
        display: flex;
        align-items: center;
        color: $neutral-500;

        &:not(:first-child)::before {
        content: '/';
        margin: 0 $spacing-xs;
        color: $neutral-400;
        }
    }

    span[aria-current="page"] {
    color: $primary;
    font-weight: $text-weight;
}

    a {
        color: inherit;
        text-decoration: none;
        display: inline-block;
        transition: transform .2s ease, box-shadow .2s ease, color .2s ease;

        &:hover {
            color: $primary;
            transform: translateY(-2px);
        }
    }
}
</style>