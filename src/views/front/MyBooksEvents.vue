<script>
import { API_BASE, API_STATIC } from '@/common/api';
import { mapState } from 'pinia';
import { useUserStore } from '@/stores/user';
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import guildAvatar from "@/assets/images/guild/guildAvatar.png";

// 之後接後端資料，這裡先用假資料佔位

export default {

  components: {
    GuildBreadcrumb,

  },
  data() {
    return {
      pendingAction: null,
      activeTab: "created",
      createdEvents: [
        {
          event_id: "",
          title: "",
          guild: { guild_name: "", guild_code: "" },
          deadline: "",
          guild_avatar: ''

        },


      ],
      joinedEvents: [
        {
          event_id: "EV00006",
          title: "致富心態",
          guild: { guild_name: "深夜書房", guild_code: "GD000027" },
          deadline: "2026.07.30",
          guild_avatar: ''

        },

      ]

    }

  },
  methods: {
    viewEvent(myEventItem) {

      this.$router.push({ name: 'my-books-events-detail', params: { id: myEventItem.event_id } })
    },

    askCancel(myEventItem) { //觸發彈窗，暫存（記住）取消該活動

      this.pendingAction = { event: myEventItem, type: 'cancel' }

    },
    cancelAction() { // 取消，清空暫存
      this.pendingAction = null

    },
    async loadCreatedEvents() {

      const res = await fetch(`${API_BASE}/get_my_book_event.php`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${this.token}`,
        }

      });

      const result = await res.json();

      if (result.success) {
        this.createdEvents = result.mybookevent.map(g => ({ ...g, guild_avatar: g.guild_avatar ? `${API_STATIC}/uploads/${g.guild_avatar}` : '' }))

      }
    },
    async loadJoinedEvents() {

      const res = await fetch(`${API_BASE}/get_joined_events.php`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${this.token}`,
        }
      });

      const result = await res.json();

      if (result.success) {
        this.joinedEvents = result.myJoinedEvents.map(g => ({ ...g, guild_avatar: g.guild_avatar ? `${API_STATIC}/src/common/uploads/${g.guild_avatar}` : '' }))

      }

    }, askLeaveEvent(myEventItem) {
      this.pendingAction = { event: myEventItem, type: 'leave' }

    },
    async confirmAction() {
      if (this.pendingAction.type === 'cancel') {
        const res = await fetch(`${API_BASE}/cancel_my_book_event.php`,
          {
            method: 'POST',

            headers: {
              Authorization: `Bearer ${this.token}`,
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 'cancelEvent': this.pendingAction.event.event_id })

          });

        const result = await res.json();

        if (result.success) {
          await this.loadCreatedEvents();
        }

        this.pendingAction = null

      } else {
        const res = await fetch(`${API_BASE}/leave_event.php`,
          {
            method: 'POST',
            headers: {
              Authorization: `Bearer ${this.token}`,
              'Content-Type': 'application/json'
            }, body: JSON.stringify({ 'leaveMyEvent':this.pendingAction.event.event_id })

          });
        const result = await res.json();
        if (result.success) {
          this.loadJoinedEvents();
        }
        this.pendingAction = null

      }
    },
  },

  computed: {
    ...mapState(useUserStore, ["token"]),

    
  },
  mounted() {
    this.loadCreatedEvents();
    this.loadJoinedEvents();

  }


}


</script>

<template>

  <div class="event-list container-content">
    <div class="col-10">
      <GuildBreadcrumb :items="[{ label: '❮  首頁', to: `/home` }, { label: '我的讀書會活動' }]" />


      <div class="event-tabs">
        <a class="event-tab" :class="{ 'is-active': activeTab === 'created' }" @click="activeTab = 'created'">發起的活動</a>
        <a class="event-tab" :class="{ 'is-active': activeTab === 'joined' }" @click="activeTab = 'joined'">參與的活動</a>
      </div>

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
        <tr class="event-row" v-for="event in activeTab === 'created'
          ? createdEvents : joinedEvents" :key="event.event_id">
          <td class="event-book">{{ event.title }}</td>

          <td class="event-guild">
            <img :src="event.guild_avatar" class="event-guild-avatar ">
            <div class="event-guild-info">
              <span class="event-guild-name">{{ event.guild_name }}</span>
              <span class="event-guild-code">{{ event.guild_code }}</span>
            </div>
          </td>

          <td class="event-deadline">{{ event.deadline }}</td>

          <td class="event-action">
            <button class="event-view" @click="viewEvent(event)">查看活動</button>
            <button v-if="activeTab === 'created'" class="event-cancel" @click="askCancel(event)">取消活動</button>
            <button v-else class="event-cancel" @click="askLeaveEvent(event)">退出活動</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="pendingAction" class="confirm-modal-overlay">

      <div class="confirm-modal">
        <p class="confirm-modal__text">請問確定要{{pendingAction.type=='cancel'? '取消':'退出' }}「{{ pendingAction.event.title }}」讀書會活動嗎？</p>


        <div class="confirm-modal__actions">
          <div class="confirm-modal__cancel" @click="cancelAction()">取消</div>
          <div class="confirm-modal__confirm" @click="confirmAction()">確認</div>
        </div>

      </div>

    </div>

  </div>
</template>

<style scoped lang="scss">
@use "@/assets/scss/abstracts/variables" as *;
@use "@/assets/scss/abstracts/mixins" as *;

.event-list {
  row-gap: 0;
}

.event-tabs {
  display: flex;
  gap: $spacing-md;
}

.event-tab {
  background: none;
  border: none;
  padding: $spacing-xs 0;
  font-size: $p-md-size;
  font-weight: $heading-weight;
  color: $neutral-500;
  cursor: pointer;

  &.is-active {
    color: $primary;
    border-bottom: 4px solid $primary-300;
  }
}

.event-table {
  border-collapse: collapse;
  table-layout: fixed;
}

.event-header {
  background-color: $neutral-200;
  color: $neutral-500;
  border-top: 1px solid $neutral-300;
  border-bottom: 1px solid $neutral-300;
}

.event-col {
  text-align: left;
  padding: $spacing-md;
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
  object-fit: cover
  
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
