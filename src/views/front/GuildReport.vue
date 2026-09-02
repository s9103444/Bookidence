<script setup>
import { onMounted, ref } from "vue";
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import { useRoute, useRouter } from "vue-router";
import { API_BASE } from "@/common/api";
import { useUserStore } from "@/stores/user";

const route = useRoute();
const router = useRouter();
const userStore = useUserStore();
const reports = ref([]);

function loadReports(){
    fetch(`${API_BASE}/guild_get_reports.php?guild_id=${route.params.id}`, {
        headers: { Authorization: `Bearer ${userStore.token}` },
    })
    .then(res => res.json()).then(data => {
        if(data.success){
            reports.value = data.reports.map(r => ({
                id: r.report_id,
                reporterName: r.reporter_name,
                reportedName: r.reported_name,
                reportedAt: r.created_at,
            }));
        }
    });
}

onMounted(() => {
    loadReports();
});

function goToReportDetail(reportId){
    router.push({name: "report-detail",params: {id: route.params.id,reportId}});
}
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
                <tr class="report-row" v-for="report in reports" :key="report.id">
                    <td class="report-cell">{{ report.reporterName }}</td>
                    <td class="report-cell">{{ report.reportedName }}</td>
                    <td class="report-cell">{{ report.reportedAt }}</td>
                    <td class="report-action-cell">
                        <button class="report-detail" @click="goToReportDetail(report.id)">查看詳情</button>
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
    overflow-x: auto;

}

.report-table {
    width: 80%;
    border-collapse: collapse;
    table-layout: fixed;
    margin: 0 auto;

    @include tablet {
        width: 95%;
    }

    @include mobile {
        width: 600px;
    }
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