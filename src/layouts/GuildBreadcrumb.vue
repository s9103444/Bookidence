<script setup>
// 公會相關分頁的麵包屑。
// 使用到的頁面 : 讀書公會-設定讀書排程/成員列表-申請中/成員總覽/檢舉事件/檢舉事件詳情/建立讀書會活動/讀書會活動詳情/公會設定。

defineProps({
    items: {
        type: Array,
        required: true,
    },
});
</script>

<template>

    <nav class="breadcrumb">
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
    padding: $spacing-md 0;
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