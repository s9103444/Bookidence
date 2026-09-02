<script setup>
// 全站共用的「已被踢出公會」提醒彈框。
// 真正的攔截邏輯在 src/common/apiGuard.js（蓋掉 window.fetch，偵測到後端回傳
// reason: 'not_member' 時呼叫 kickedNoticeStore.show()），這裡只負責把彈框畫出來、
// 處理使用者關閉後導回公會列表頁。
import { useKickedNoticeStore } from '@/stores/kickedNotice';
import { useRouter } from 'vue-router';
import AppModal from '@/components/common/AppModal.vue';

const kickedNoticeStore = useKickedNoticeStore();
const router = useRouter();

function backToGuildList() {
    kickedNoticeStore.hide();
    router.push({ name: 'guilds' });
}
</script>
<template>
    <AppModal :model-value="kickedNoticeStore.isOpen" title="提醒" :z-index="2000" @update:model-value="backToGuildList">
        <p>{{ kickedNoticeStore.message }}</p>
        <div class="bnt-wrap">
            <button @click="backToGuildList">確認</button>
        </div>
    </AppModal>
</template>

<style scoped lang="scss">
.bnt-wrap {
    display: flex;
    justify-content: center;
    margin-top: 16px;
}
</style>