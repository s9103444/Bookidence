<script setup>
import { ref } from "vue";
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";

// 之後接後端資料，這裡先用假資料佔位
const activeTab = ref("created");

const createdEvents = ref([
  {
    id: "EV0001",
    book: "小王子",
    guild: { name: "壁爐與貓", code: "GD000003" },
    deadline: "2026.08.24",
  },
]);

const joinedEvents = ref([
  {
    id: "EV0002",
    book: "致富心態",
    guild: { name: "深夜書房", code: "GD000027" },
    deadline: "2026.07.30",
  },
]);
</script>

<template>
  <GuildBreadcrumb
    :items="[{ label: '❮  首頁', to: `/home` }, { label: '我的讀書會活動' }]"
  />

  <div class="event-list container-content">
    <div class="event-tabs col-10">
      <a
        class="event-tab"
        :class="{ 'is-active': activeTab === 'created' }"
        @click="activeTab = 'created'"
        >發起的活動</a
      >
      <a
        class="event-tab"
        :class="{ 'is-active': activeTab === 'joined' }"
        @click="activeTab = 'joined'"
        >參與的活動</a
      >
    </div>

    <table class="event-table col-10">
      <thead>
        <tr class="event-header">
          <th class="event-col event-col--book">活動讀物</th>
          <th class="event-col event-col--guild">該活動公會</th>
          <th class="event-col event-col--deadline">截止日期</th>
          <th class="event-col event-col--action"></th>
        </tr>
      </thead>
      <tbody>
        <tr
          class="event-row"
          v-for="event in activeTab === 'created'
            ? createdEvents
            : joinedEvents"
          :key="event.id"
        >
          <td class="event-book">{{ event.book }}</td>

          <td class="event-guild">
            <span class="event-guild-avatar"></span>
            <div class="event-guild-info">
              <span class="event-guild-name">{{ event.guild.name }}</span>
              <span class="event-guild-code">{{ event.guild.code }}</span>
            </div>
          </td>

          <td class="event-deadline">{{ event.deadline }}</td>

          <td class="event-action">
            <router-link
              :to="`/member/my-books-events/${event.id}`"
              class="event-view"
              >查看活動</router-link
            >
            <button class="event-cancel">取消活動</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped lang="scss">
@use "@/assets/scss/abstracts/variables" as *;
@use "@/assets/scss/abstracts/mixins" as *;

.event-tabs {
  display: flex;
  gap: $spacing-md;
}

.event-tab {
  background: none;
  border: none;
  padding: $spacing-xs 0;
  font-size: $p-sm-size;
  font-weight: $heading-weight;
  color: $neutral-500;
  cursor: pointer;

  &.is-active {
    color: $primary;
    border-bottom: 2px solid $primary;
  }
}

.event-table {
  border-collapse: collapse;
  table-layout: fixed;
}

.event-header {
  background-color: $neutral-300;
  color: $neutral-500;
}

.event-col {
  text-align: left;
  padding: $spacing-sm $spacing-md;
  font-size: $p-sm-size;
  font-weight: $text-weight;
  color: $neutral-600;

  &--book,
  &--guild,
  &--deadline {
    width: 28%;
  }

  &--action {
    width: 16%;
  }
}

.event-row {
  border-bottom: 1px solid $neutral-300;

  td {
    vertical-align: middle;
    padding: $spacing-sm $spacing-md;
  }
}

.event-book {
  font-size: $p-sm-size;
  color: $neutral-800;
}

.event-guild {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
}

.event-guild-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: $neutral-300;
  flex-shrink: 0;
}

.event-guild-info {
  display: flex;
  flex-direction: column;
}

.event-guild-name {
  font-size: $p-sm-size;
  color: $neutral-800;
}

.event-guild-code {
  font-size: $p-sm-size;
  color: $neutral-500;
}

.event-deadline {
  font-size: $p-sm-size;
  color: $neutral-600;
}

.event-action {
  text-align: right;
  white-space: nowrap;
}

.event-view,
.event-cancel {
  display: inline-block;
  padding: $spacing-xs $spacing-md;
  border-radius: 5px;
  background: $neutral-100;
  font-size: $p-sm-size;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.event-view {
  border: 1px solid $primary-500;
  color: $primary-500;
  margin-right: $spacing-sm;

  &:hover {
    background: $primary-500;
    color: $neutral-100;
  }
}

.event-cancel {
  border: 1px solid $color-danger;
  color: $color-danger;

  &:hover {
    background: $color-danger;
    color: $neutral-100;
  }
}
</style>
