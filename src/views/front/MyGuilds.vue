<script>
import { API_BASE, API_STATIC } from '@/common/api';
import { mapState } from 'pinia';
import { useUserStore } from '@/stores/user';
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
      guildToLeave: null,
      guild: [

        {
          guild_id: '',
          guild_code: '',
          guild_name: '',
          guild_avatar: '',
          title: '',
        }
      ]

    }
  },
  methods: {

    viewGuild(guildsItem) {  // 導向公會詳細頁
      this.$router.push({ name: 'guild-detail', params: { id: guildsItem.guild_id } })
    },
    askLeave(guildsItem) { // 觸發彈窗，記住要退出的公會
      this.guildToLeave = guildsItem
    },
    cancelAction() { // 取消，清空暫存
      this.guildToLeave = null
    },
    async confirmLeave() { // 確認，真正移除 + 清空暫存

      const res = await fetch(`${API_BASE}/leave_guild.php`,
        {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${this.token}`,
            'Content-Type': 'application/json'
          }, body: JSON.stringify({ deleteGuild: this.guildToLeave.guild_id })
        });

      const result = await res.json();

      if (result.success) {
        await this.loadMyGuild();
      }

      // this.guild = this.guild.filter(item => item.id != this.guildToLeave.id)

      this.guildToLeave = null

    }, async loadMyGuild() {

      const res = await fetch(`${API_BASE}/get_my_guild.php`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${this.token}`,
        },
      });

      const result = await res.json();

      if (result.success) {
        this.guild = result.myguilds.map(g => ({
          ...g,
          guild_avatar: g.guild_avatar ? `${API_STATIC}/uploads/${g.guild_avatar}` : ''
        }))


      }

    }


  }, computed: {

    ...mapState(useUserStore, ["token"])
  },

  mounted() {
    this.loadMyGuild();

  }

}
</script>

<template>
  <div class="my-guilds container-content">
    <div class="col-10">
      <GuildBreadcrumb class="col-10" :items="[
        { label: '❮ 首頁', to: `/` },
        { label: '我的讀書公會' }
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
          <tr v-for="item in guild" :key="item.guild_id">
            <td class="guild-name-cell">
              <div class="guild-name-content">
                <img :src="item.guild_avatar" :alt="item.guild_name" class="guild-avatar">
                <div class="guild-name-info">
                  <span class="guild-name">{{ item.guild_name }}</span>
                  <span class="guild-id">{{ item.guild_code }}</span>
                </div>
              </div>

            </td>
            <td class="guild-book">
              <div class="guild-book-content">{{ item.title }}</div>
            </td>
            <td class="guild-action">
              <div class="guild-action-content">
                <button class="guild-leave-btn" @click="askLeave(item)">退出公會</button>
                <button class="guild-view-btn" @click="viewGuild(item)">查看公會</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>


      <div v-if="guildToLeave" class="confirm-modal-overlay">
        <div class="confirm-modal">
          <p class="confirm-modal__text">請問確定要退出{{ guildToLeave.guild_name }}公會嗎？</p>



          <div class="confirm-modal__actions">
            <div class="confirm-modal__cancel" @click="cancelAction()">取消</div>
            <div class="confirm-modal__confirm" @click="confirmLeave()">確認</div>
          </div>


        </div>

      </div>

    </div>
  </div>
</template>

<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;



.guild-section-title {
  font-size: $p-md-size;
  padding-bottom: $spacing-sm;
  border-bottom: 4px solid $primary-300;
  display: inline-block;
  padding: $spacing-sm 0;
  color: $primary;
}

.guild-table {
  width: 100%;
  border-collapse: collapse; //表格儲存格合併
}

.guild-table thead {
  background: $neutral-200;
  color: $neutral-600;
  border-bottom: 1px solid $neutral-300;
  border-top: 1px solid $neutral-300;

}

.guild-table tbody tr {
  border-bottom: 1px solid $neutral-300;
}


.guild-table td {
  text-align: left;
  padding-inline: $spacing-md;
  font-size: $p-sm-size ;

  @media (max-width:1024px) {
    padding-inline: 0;

  }
}

.guild-table th {
  color: $neutral-500;
  text-align: left;
  padding: $spacing-md;
  font-size: $p-sm-size ;
}

.guild-name-cell {
  // display: flex;
  // align-items: center;
  // gap: $spacing-sm;
  padding-block: $spacing-md;
  // height: 100%;
  vertical-align: middle;


  @media (max-width: 480px) {
    flex-direction: column;


  }
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
  font-size: $p-sm-size;


}

.guild-book {
  padding-block: $spacing-md;
  vertical-align: middle;
  @include text-ellipsis(1);
}

.guild-id {
  font-size: $p-xs-size;
  color: $neutral-500;
}

.guild-action {
  //display: flex;
  //gap: $spacing-sm;
  vertical-align: middle;
   padding-block: $spacing-md;


}

.guild-action-content {
  // display: flex;
  // gap: $spacing-sm;
 @include text-ellipsis(1);
  @media (max-width: 1024px) {
    flex-direction: column;

  }

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
    font-size: $p-sm-size;
    color: $neutral-800;
  }

  &__actions {
    display: flex;
    gap: $spacing-md;
  }

  &__cancel,
  &__confirm {
    flex: 1;
    padding: $spacing-md $spacing-lg;
    text-align: center;
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
</style>