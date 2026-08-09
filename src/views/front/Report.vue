<script setup>

</script>

<template>
<div class="report-list">
        <table class="report-table">
            <thead>
                <tr class="report-header">
                    <th class="report-col report-col--reporter">檢舉人</th>
                    <th class="report-col report-col--reported">被檢舉人</th>
                    <th class="report-col report-col--time">檢舉時間</th>
                    <th class="report-col report-col--action"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="report-row">
                    <td class="report-cell">我是檢舉人</td>
                    <td class="report-cell">我是被檢舉人</td>
                    <td class="report-cell">1天前</td>
                    <td class="report-action-cell">
                        <button class="report-detail">查看詳情</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;


.report-list {
    width: 100%;
    margin: 0 auto;
}

.report-table {
    width: 60%;
    border-collapse: collapse;
    table-layout: fixed;
}

.report-header {
    background-color: $neutral-200;
    color: $neutral-500;
}

.report-col {
    text-align: left;
    padding: $spacing-sm $spacing-md;
    font-size: $p-sm-size;
    font-weight: $text-weight;
    color: $neutral-600;

    &--reporter {
        width: 33.33%;
    }

    &--reported {
        width: 22.22%;
    }

    &--time {
        width: 22.22%;
    }

    &--action {
        width: 22.22%;
    }
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

    &:hover {
        background: $neutral-100;
    }
}
</style>