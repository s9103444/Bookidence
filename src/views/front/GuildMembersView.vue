<script setup>
// 目前先假設「我」是會長。
// 之後接後端使用者資料時，要改成用 currentUser.role 動態判斷該不該顯示
import { ref, computed } from "vue";
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import { useRoute } from "vue-router";
import girlAvatar from "../../assets/images/guild/girl.png";
import boyAvatar from "../../assets/images/guild/boy.png";

const route = useRoute();


// 之後接後端資料，把這裡換成真的登入者資料
const currentUser = ref({ id: 'BKD00003', role: 'leader' });//這一行可以切換視角

const members = ref([
    { id: 'BKD00003', name: '小森', avatar: girlAvatar, role: 'leader', roleLabel: '會長', online: '1天前' },
    { id: 'BKD00010', name: '阿林', avatar:  boyAvatar, role: 'vice', roleLabel: '副會長', online: '1天前' },
    { id: 'BKD00025', name: '小蘑菇', avatar: girlAvatar, role: 'member', roleLabel: '一般會員', online: '1天前' },
]);

// 這一列該不該顯示 ⋯，權限判斷全部集中在這一個函式
function canManage(member) {
  if (member.id === currentUser.value.id) return false;               // 不能對自己操作
  if (currentUser.value.role === 'leader') return true;                // 會長可以管副會長、一般會員
  if (currentUser.value.role === 'vice') return member.role === 'member'; // 副會長只能管一般會員
  return false;                                                        // 一般會員完全沒有管理權限
}

// 這一列的下拉選單要放哪些選項
function getActions(member) {
    if (!canManage(member)) return [];

    const actions = ['踢出公會'];

    if (currentUser.value.role === 'leader') {
        actions.push('賦予會長權限');
        if (member.role === 'member') {
        actions.push('賦予副會長權限');
        }
    }

    return actions;
}

// 下拉選單開關：記錄「目前是哪一個成員的選單被點開」
const openDropdownId = ref(null);
function toggleDropdown(memberId) {
    openDropdownId.value = openDropdownId.value === memberId ? null : memberId;
}

// 踢出公會：先記住要踢誰、跳確認框，按確認才真的從陣列移除
const memberToKick = ref(null);
function askKick(member) {
    memberToKick.value = member;
    openDropdownId.value = null; // 順便關掉下拉選單
}
function confirmKick() {
    members.value = members.value.filter(m => m.id !== memberToKick.value.id);
    memberToKick.value = null;
}
function cancelKick() {
    memberToKick.value = null;
}


// ===================================申請中================================

const activeTab = ref('overview');
const canViewApplications = computed(() => {// 只有會長、副會長看得到「申請中」這個分頁
    return currentUser.value.role === 'leader' || currentUser.value.role === 'vice';
});

const applications = ref([
    { id: 'BKD00101', name: '我是路人1', avatar: girlAvatar, appliedAt: '1天以前' },
    { id: 'BKD00102', name: '我是路人2', avatar: boyAvatar, appliedAt: '1天以前' },
    { id: 'BKD00103', name: '我是路人3', avatar: girlAvatar, appliedAt: '1天以前' },
]);

// 同意/拒絕申請：按確認才真的從陣列移除
const applicationToHandle = ref(null); // { application, action: 'approve' | 'reject' }
function askHandleApplication(application, action) {
    applicationToHandle.value = { application, action };
}
function confirmHandleApplication() {
    const id = applicationToHandle.value.application.id;
    applications.value = applications.value.filter(a => a.id !== id);
    applicationToHandle.value = null;
}
function cancelHandleApplication() {
    applicationToHandle.value = null;
}

</script>

<template>
    <GuildBreadcrumb :items="[
    { label: '❮  公會主頁', to: `/guilds/${route.params.id}` },// guilds/:id 填入目前公會的 id
    { label: '成員列表' }
]" />

<div class="member-list">
    <div class="member-tabs">
        <a
            class="member-tab"
            :class="{ 'is-active': activeTab === 'overview' }"
            @click="activeTab = 'overview'"
        >成員總覽</a>
        <a
            v-if="canViewApplications"
            class="member-tab"
            :class="{ 'is-active': activeTab === 'pending' }"
            @click="activeTab = 'pending'"
        >
            申請中 <span class="member-badge">{{ applications.length }}</span>
        </a>
    </div>

    <table class="member-table" v-if="activeTab === 'overview'">
            <thead>
                <tr class="member-header">
                    <th class="member-col member-col--member">成員</th>
                    <th class="member-col member-col--role">成員身份</th>
                    <th class="member-col member-col--online">最近一次上線</th>
                    <th class="member-col member-col--action"></th>
                </tr>
            </thead>
            <tbody>
    <tr class="member-row" v-for="member in members" :key="member.id">
        <td class="member-member">
            <img :src="member.avatar" :alt="member.name" class="member-avatar">
            <div class="member-member-info">
                <span class="member-name">{{ member.name }}</span>
                <span class="member-id">{{ member.id }}</span>
            </div>
        </td>

        <td>
            <span class="member-role" :class="`member-role--${member.role}`">{{ member.roleLabel }}</span>
        </td>

        <td class="member-online">{{ member.online }}</td>

        <td class="member-action">
            <button
                v-if="getActions(member).length"
                class="member-more"
                aria-label="更多操作"
                @click="toggleDropdown(member.id)"
            >⋯</button>

            <div
                v-if="getActions(member).length"
                class="member-dropdown"
                :class="{ 'is-open': openDropdownId === member.id }"
            >
                <button
                    v-for="action in getActions(member)"
                    :key="action"
                    class="member-dropdown-item"
                    @click="action === '踢出公會' ? askKick(member) : null"
                >{{ action }}</button>
            </div>
        </td>
    </tr>
</tbody>
        </table>
        <table class="apply-table" v-else>
    <thead>
        <tr class="member-header">
            <th class="apply-col apply-col--member">申請人</th>
            <th class="apply-col apply-col--time">申請時間</th>
            <th class="apply-col apply-col--handle">處理方式</th>
        </tr>
    </thead>
    <tbody>
        <tr class="apply-row" v-for="application in applications" :key="application.id">
            <td class="apply-member">
                <img :src="application.avatar" :alt="application.name" class="apply-avatar">
                <div class="apply-member-info">
                    <span class="apply-name">{{ application.name }}</span>
                    <span class="apply-id">{{ application.id }}</span>
                </div>
            </td>
            <td class="apply-time">{{ application.appliedAt }}</td>
            <td class="apply-handle">
                <button class="apply-approve" @click="askHandleApplication(application, 'approve')">同意加入</button>
                <button class="apply-reject" @click="askHandleApplication(application, 'reject')">拒絕加入</button>
            </td>
        </tr>
    </tbody>
</table>
    </div>
<div v-if="memberToKick" class="confirm-modal-overlay" @click.self="cancelKick">
    <div class="confirm-modal">
        <p class="confirm-modal__text">確定要將「{{ memberToKick.name }}」踢出公會嗎？</p>
        <div class="confirm-modal__actions">
            <button class="confirm-modal__cancel" @click="cancelKick">取消</button>
            <button class="confirm-modal__confirm" @click="confirmKick">確認踢出</button>
        </div>
    </div>
</div>

<div v-if="applicationToHandle" class="confirm-modal-overlay" @click.self="cancelHandleApplication">
    <div class="confirm-modal">
        <p class="confirm-modal__text">
            確定要{{ applicationToHandle.action === 'approve' ? '同意' : '拒絕' }}「{{ applicationToHandle.application.name
}}」的加入申請嗎？
        </p>
        <div class="confirm-modal__actions">
            <button class="confirm-modal__cancel" @click="cancelHandleApplication">取消</button>
            <button class="confirm-modal__confirm" @click="confirmHandleApplication">確認</button>
        </div>
    </div>
</div>

</template>

<style lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.member-list {
    width: 80%;
    margin: $spacing-md auto;

}

.member-tabs {
    display: flex;
    gap: $spacing-md;
    margin-bottom: $spacing-md;
}

.member-tab {
    background: none;
    border: none;
    padding: $spacing-sm 0;
    font-size: $p-md-size;
    font-weight: $heading-weight;
    color: $neutral-500;
    cursor: pointer;

    &.is-active {
        color: $primary;
        border-bottom: 2px solid $primary;
    }
}

.member-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 $spacing-xs;
    border-radius: 999px;
    background: #E15647;
    color: #fff;
    font-size: $p-xs-size;
}

.member-table,
.apply-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.member-header {
    background-color: $neutral-300;
    color: $neutral-500;
}

.member-col,
.apply-col {
    text-align: left;
    padding: $spacing-sm $spacing-md;
    font-size: $p-sm-size;
    font-weight: $text-weight;
    color: $neutral-600;
}

.member-col {
    &--member,
    &--role,
    &--online
    {
        width: 30%;
    }

    &--action {
        width: 10%;
    }
}

.member-row,
.apply-row {
    border-bottom: 1px solid $neutral-300;

    td {
        vertical-align: middle;
        padding: $spacing-sm $spacing-md;
    }
}

.member-member,
.apply-member {
    display: flex;
    align-items: center;
    gap: $spacing-sm;
}

.member-member {
    padding: $spacing-md;
}

.member-avatar,
.apply-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.member-member-info,
.apply-member-info {
    display: flex;
    flex-direction: column;
}

.member-name,
.apply-name {
    font-size: $p-md-size;
    color: $neutral-800;
}

.member-id,
.apply-id {
    font-size: $p-xs-size;
    color: $neutral-500;
}

.member-role {
    font-size: $p-sm-size;
    display: inline-flex;

    &--leader,
    &--vice 
    {
        padding: $spacing-xs $spacing-sm;
        border-radius: 999px;
        background: $primary-300;
        color: $neutral-100;
    }

    &--member {
        color: $neutral-600;
    }
}

.member-online,
.apply-time {
    font-size: $p-sm-size;
    color: $neutral-600;
}

.member-action {
    position: relative;
    text-align: right;
    padding: $spacing-md;
}

.member-more {
    background: none;
    border: none;
    cursor: pointer;
    font-size: $p-lg-size;
    color: $neutral-500;
}

.member-dropdown {
    display: none;
    position: absolute;
    right: $spacing-md;
    top: 100%;
    background: $neutral-100;
    border: 1px solid $neutral-300;
    z-index: 10;

    &.is-open {
        display: flex;
        flex-direction: column;
    }
}

.member-dropdown-item {
    background: none;
    border: none;
    padding: $spacing-sm $spacing-md;
    text-align: left;
    font-size: $p-sm-size;
    color: $neutral-800;
    cursor: pointer;
    white-space: nowrap;

    &:hover {
        background: $neutral-100;
    }
}
.confirm-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
}

.confirm-modal {
    background: $neutral-100;
    border-radius: 5px;
    padding: $spacing-xl;
    min-width: 280px;

    &__text {
        margin: 0 0 $spacing-lg;
        font-size: $p-md-size;
        color: $neutral-800;
    }

    &__actions {
        display: flex;
        justify-content:space-between;
    }

    &__cancel,
    &__confirm {
        padding: $spacing-sm $spacing-md;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        font-size: $p-sm-size;
        transition: transform .2s ease, box-shadow .2s ease;

    &:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    }

    &__cancel {
        background: $neutral-200;
        color: $neutral-700;
    }

    &__confirm {
        background: $color-danger;
        color: $neutral-100;
    }
}

.apply-col {
    &--member,
    &--time,
    &--handle{
        width: 33.33333%;
    }
}

.apply-handle {
    display: flex;
    align-items: flex-start;
    gap: $spacing-sm;
    vertical-align: top;
}

.apply-row td.apply-handle {
    padding: $spacing-xs $spacing-md;
    padding-top: 0;
}

.apply-approve,
.apply-reject {
    padding: $spacing-sm $spacing-md;
    border-radius: 6px;
    font-size: $p-sm-size;
    cursor: pointer;
    transition: transform .2s ease, box-shadow .2s ease, background .2s ease, opacity .2s ease;

    &:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
}

.apply-approve {
    border: 1px solid $secondary-500;
    background: $secondary-500;
    color: $primary;

    &:hover {
        opacity: 0.85;
    }
}

.apply-reject {
    border: 1px solid $neutral-300;
    background: $neutral-100;
    color: $neutral-800;

    &:hover {
        background: $neutral-100;
    }
}
</style>