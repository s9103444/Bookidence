<script setup>
import { ref, watch } from "vue";
import { useRouter } from "vue-router";
import AppModal from "../common/AppModal.vue";
import AppButton from "../common/AppButton.vue";
import { API_BASE, API_STATIC } from "@/common/api";

const props = defineProps({
modelValue: {
    type: Boolean,
    default: false,
},
    bookId: {
    type: [Number, String],
    default: null,
},
});

defineEmits(["update:modelValue"]);
const router = useRouter();

const book = ref(null);
const categories = ref([]);

watch(() => props.modelValue, (isOpen) => {
if (isOpen && props.bookId) {
    fetch(`${API_BASE}/get_book_detail.php?book_id=${props.bookId}`)
    .then(res => res.json())
    .then(data => {
        if (data.success) {
        book.value = data.book;
        categories.value = data.categories;
        }
    });
}
});

function goToBookPage() {
router.push({ name: "book-detail", params: { id: props.bookId } });
}
</script>

<template>
<AppModal
    :model-value="modelValue"
    title="書籍詳情"
    @update:model-value="$emit('update:modelValue', $event)"
>
    <div class="book-detail-modal" v-if="book">
    <img
        :src="book.bc_image.startsWith('http') ? book.bc_image : `${API_STATIC}/uploads/${book.bc_image}`"
        :alt="book.title"
        class="book-detail-modal__cover"
    />
    <div class="book-detail-modal__info">
        <h3 class="book-detail-modal__title">{{ book.title }}</h3>
        <p>作者：{{ book.author }}</p>
        <p>出版日期：{{ book.p_date }}</p>
        <p>出版社：{{ book.publisher }}</p>
        <p>ISBN：{{ book.isbn }}</p>
        <p v-if="categories.length">分類：{{ categories.join('、') }}</p>
        <p class="book-detail-modal__desc">{{ book.description }}</p>
    </div>
    <AppButton class="book-detail-modal__btn" @click="goToBookPage">前往書籍頁</AppButton>
    </div>
</AppModal>
</template>

<style scoped lang="scss">
@use "@/assets/scss/abstracts/variables" as *;

.book-detail-modal {
    display: flex;
    flex-direction: column;
    gap: $spacing-md;
}

.book-detail-modal__cover {
    align-self: center;
    width: 160px;
    aspect-ratio: 174 / 246;
    object-fit: cover;
}

.book-detail-modal__info p {
    margin: 4px 0;
    font-size: $p-sm-size;
    color: $neutral-800;
}

.book-detail-modal__title {
    color: $primary;
    margin: 0 0 8px;
}

.book-detail-modal__desc {
    margin-top: 8px;
    line-height: $text-line-height;
}

.book-detail-modal__btn {
    align-self: center;
    margin-top: $spacing-sm;
}
</style>