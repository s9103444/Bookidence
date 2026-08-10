<script setup>
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import { useRoute } from "vue-router";

const route = useRoute();

</script>

<template>

<GuildBreadcrumb :items="[
    { label: '❮  公會主頁', to: `/guilds/${route.params.id}` },// guilds/:id 填入目前公會的 id
    { label: '檢舉事件' }
]" />

<div class="report-list">
        <table class="report-table">
            <thead>
                <tr class="report-header">
                    <th class="report-col col-3">檢舉人</th>
                    <th class="report-col col-3">被檢舉人</th>
                    <th class="report-col col-3">檢舉時間</th>
                    <th class="report-col col-3">查看詳情</th>
                </tr>
            </thead>
            <tbody>
                <tr class="report-row">
                    <td class="report-cell" id="reporter">我是檢舉人</td>
                    <td class="report-cell" id="reported">我是被檢舉人</td>
                    <td class="report-cell" id="report-time">1天前</td>
                    <td class="report-action-cell">
                        <button class="report-detail" id="report-details">查看詳情</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;


.report-list {
    width: 100%;
    margin: $spacing-md 0px;

}

.report-table {
    width: 80%;
    border-collapse: collapse;
    table-layout: fixed;
    margin: 0 auto;
}

.report-header {
    background-color: $neutral-300;
    color: $neutral-500;
}

.report-col {
    text-align: left;
    padding: $spacing-sm $spacing-md;
    font-size: $p-sm-size;
    font-weight: $text-weight;
    color: $neutral-600;

}

.report-row {
    border-bottom: 1px solid $neutral-200;

    td {
        vertical-align: middle;
        padding: $spacing-sm $spacing-md;
    }
}

.report-cell {
    text-align: left;
    font-size: $p-sm-size;
    color: $neutral-800;
}

.report-action-cell {
    text-align: left;
}

.report-detail {
    padding: $spacing-sm $spacing-md;
    border: 1px solid $neutral-300;
    border-radius: 6px;
    background: #fff;
    color: $neutral-800;
    font-size: $p-sm-size;
    cursor: pointer;
    transition: transform 0.2s ease, background 0.2s ease;

    &:hover {
        background: $primary;
        color:$neutral-100 ;
        transform: translateY(-2px);

    }
}
</style>