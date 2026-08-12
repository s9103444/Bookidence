<script>
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import AppIcon from "@/components/common/AppIcon.vue";
import guildAvatar from "@/assets/images/guild/guildAvatar.png";


export default {

  components: {
    GuildBreadcrumb,
    AppIcon,
  },
  data() {

    return {
      guild:[
        {
        id: 'GD000018',
        name: '字裡・行間',
        avatar: guildAvatar,
        currentBook: '冰與血之歌：北歐千年史',
        },
        {
        id: 'GD000003',
        name: '壁爐與貓',
        avatar: guildAvatar,
        currentBook: '小王子',
        }
      ]

    }
  },
  methods: {

  }
}
</script>

<template>
  <div class="my-guilds container-content">
    <div class="col-10">
      <GuildBreadcrumb class="col-10" :items="[
        { label: '❮ 首頁', to: `/` },
        { label: '我的公會' }
      ]" />
      <h2 class="guild-section-title">正參加的讀書公會</h2>
<table class="guild-table">
<thead>
   <tr>
    <th>公會名稱</th>
    <th>正在讀</th>
    <th></th>
   </tr>
   </thead>
    <tbody>
      <tr v-for="item in guild" :key="item.id">
        <td class="guild-name-cell">
          <img :src="item.avatar" :alt="item.name" class="guild-avatar">
          <div class="guild-name-info">
            <span class="guild-name">{{ item.name }}</span>
            <span class="guild-id">{{ item.id }}</span>
          </div>
        </td>
        <td class="guild-book">{{ item.currentBook }}</td>
        <td class="guild-action">
        <button class="guild-leave-btn">退出公會</button>
        <button class="guild-view-btn">查看公會</button>
        </td>
      </tr>
    </tbody>
</table>
    
       </div>
  </div>
</template>

<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.guild-section-title {
  font-size: $p-md-size;
  padding-bottom: $spacing-sm;
  border-bottom: 2px solid $primary;
  display: inline-block;
  margin-bottom: $spacing-md;
  color:$primary;
}

.guild-table {
  width: 100%;
  border-collapse: collapse;//表格儲存格合併
}

.guild-table thead {
  background: $neutral-300;
  color: $neutral-500;
}

.guild-table tbody tr {
  border-bottom: 1px solid $neutral-300;
}

.guild-table th,
.guild-table td {
  text-align: left;
  padding: $spacing-md;
}

.guild-name-cell {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
  padding-block: $spacing-md;
}

.guild-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.guild-name-info {
  display: flex;
  flex-direction: column;
}

.guild-name,
.guild-book {
  font-size: $p-md-size;
}

.guild-book {
  padding-block: $spacing-md;
}

.guild-id {
  font-size: $p-xs-size;
  color: $neutral-500;
}

.guild-action {
  display: flex;
  gap: $spacing-sm;
}

.guild-leave-btn,
.guild-view-btn {
  padding: $spacing-xs $spacing-md;
  border-radius: $btn-radius-std;
  cursor: pointer;
  background: $neutral-100;
  font-size: $p-sm-size;
}

.guild-leave-btn {
  border: 1px solid $color-danger;
  color: $color-danger;
}

.guild-view-btn {
  border: 1px solid $neutral-400;
  color: $neutral-600;
}






</style>