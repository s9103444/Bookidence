<script setup>
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import AppButton from "@/components/common/AppButton.vue";
import { useRoute } from "vue-router";
import { onMounted, ref } from "vue";
import { API_BASE } from "@/common/api";
import PhotoSticker from "@/components/front/PhotoSticker.vue";

const route = useRoute();
const displayReport = ref(null);

function loadReport(){
    fetch(`${API_BASE}/guild_get_reports.php?report_id=${route.params.reportId}`)
    .then(res => res.json()).then(data =>{
        if(data.success && data.report){
            displayReport.value = {
                id: data.report.report_id,
                no: data.report.report_no,
                reporterName: data.report.reporter_name,
                reporterId: data.report.reporter_code,
                reportedName: data.report.reported_name,
                reportedId: data.report.reported_code,
                reportedUserId: data.report.reported_user_id,
                reportType: data.report.reason,
                reportedAt:data.report.created_at,
                quoteText: data.report.quote_content,
                description: data.report.reason_detail,
            };
        }
    });
}

onMounted(() => {
    loadReport();
});

const isKicked = ref(false);
function kickReportedUser() {
    isKicked.value = true;

}
</script>

<template>

<GuildBreadcrumb :items="[
    { label: '❮  公會主頁', to: `/guilds/${route.params.id}` },// guilds/:id 填入目前公會的 id
    { label: '檢舉事件', to: `/guilds/${route.params.id}/report` },
    { label: '查看詳情' }
]" />


<div class="report-detail" v-if="displayReport">
        <div class="report-detail__id">檢舉編號#{{ displayReport.no }}</div>

        <div class="report-detail__grid">
            <div class="report-detail__field col-6">
                <span class="report-detail__label">檢舉人暱稱&ID</span>
                <div class="report-detail__person">
                    <span class="report-detail__name">{{ displayReport.reporterName }}</span>
                    <span v-if="displayReport.reporterId" class="report-detail__pid">{{ displayReport.reporterId }}</span>
                </div>
            </div>

            <div class="report-detail__field col-6">
                <span class="report-detail__label">被檢舉人暱稱&ID</span>
                <div class="report-detail__person">
                    <span class="report-detail__name">{{ displayReport.reportedName }}</span>
                    <span v-if="displayReport.reportedId" class="report-detail__pid">{{ displayReport.reportedId }}</span>
                </div>
            </div>

            <div class="report-detail__field col-6">
                <span class="report-detail__label">檢舉類型</span>
                <span class="report-detail__value">{{ displayReport.reportType }}</span>
            </div>

            <div class="report-detail__field col-6">
                <span class="report-detail__label">檢舉時間</span>
                <span class="report-detail__value">{{ displayReport.reportedAt }}</span>
            </div>
        </div>

        <hr class="report-detail__divider">

        <div class="report-detail__section">
            <span class="report-detail__section-title">檢舉內容</span>

            <div class="report-detail__quote-card">
                <div class="report-detail__quote-header">
                    <div class="report-detail__quote-avatar">
                        <PhotoSticker class="report-detail__quote-avatar-canvas" :userId="displayReport.reportedUserId" :width="90" />
                    </div>
                    <span class="report-detail__quote-name">{{ displayReport.reportedName }}</span>
                </div>
                <p class="report-detail__quote-text">「{{ displayReport.quoteText }}」——討論區留言</p>
            </div>
        </div>

        <hr class="report-detail__divider">

        <div class="report-detail__section">
            <span class="report-detail__section-title">檢舉詳細說明</span>
            <p class="report-detail__description">{{ displayReport.description }}</p>
        </div>

        <div class="report-detail__actions">
            <AppButton :disabled="isKicked" @click="kickReportedUser">
                {{ isKicked ? '已將被檢舉人踢出公會' : '將被檢舉人踢出公會' }}
            </AppButton>
        </div>
    </div>
</template>

<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.report-detail {
    width: 80%;
    margin: $spacing-md auto;

    @include tablet {
        width: 90%;
    }

    &__id {
        font-size: $p-md-size;
        color: $neutral-500;
        margin-bottom: $spacing-md;
    }

    &__field {
        display: flex;
        flex-direction: column;
        gap: $spacing-xs;

        @include mobile {
            grid-column: 1 / -1;
        }
    }

    &__grid {
        @include grid-container;
        margin-bottom: $spacing-md;
}

    &__label {
        font-size: $p-md-size;
        color: $primary;
    }

    &__person {
        display: flex;
        flex-direction: column;
    }

    &__name,
    &__value {
        font-size: $p-md-size;
        font-weight: $text-weight;
        line-height: $text-line-height;
        color: $neutral-800;
    }

    &__pid {
        font-size: $p-sm-size;
        color: $neutral-400;
    }

    &__divider {
        border: none;
        border-top: 1px solid $neutral-200;
        margin: $spacing-lg 0;
    }

    &__section {
        display: flex;
        flex-direction: column;
        gap: $spacing-sm;
    }

    &__section-title {
        font-size: $p-md-size;
        color: $primary;
    }

    &__quote-card {
        background: $neutral-300;
        border-radius: 5px;
        padding: $spacing-lg;
    }

    &__quote-header {
        display: flex;
        align-items: center;
        gap: $spacing-md;
        margin-bottom: $spacing-md;
    }

    &__quote-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: $neutral-300;
        overflow: hidden;

        & .report-detail__quote-avatar-canvas {
            margin-top: 5px;
            margin-left: 4px;
            transform: scale(0.46);
            transform-origin: top left;
        }
        
        img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        }
    }

    &__quote-name {
        font-size: $p-md-size;
        font-weight: $text-weight;
        line-height: $text-line-height;
        color: $neutral-800;
    }

    &__quote-text {
        font-size: $p-md-size;
        font-weight: $text-weight;
        line-height: $text-line-height;
        color: $neutral-800;
        margin: 0;
        padding-left: $spacing-lg;
    }

    &__description {
        font-size: $p-md-size;
        font-weight: $text-weight;
        line-height: $text-line-height;
        color: $neutral-800;
        margin: 0;
    }

    &__actions {
        display: flex;
        justify-content: flex-end;
        margin-top: $spacing-lg;
    }
}
</style>