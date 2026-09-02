<script>
import { API_BASE } from '@/common/api';
import { mapState } from 'pinia';
import { useUserStore } from '@/stores/user';
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import AppIcon from "@/components/common/AppIcon.vue";
import guildAvatar from "@/assets/images/guild/guildAvatar2.png";
import PhotoSticker from "@/components/front/PhotoSticker.vue";


export default {
  components: {
    GuildBreadcrumb,
    AppIcon,
    PhotoSticker
  
  },
  data() {
    return {
      searchFriend:"",
      guildAvatar,
      pendingAction: null,
      activeTab: 'all',
      members: [
        {
          id: "M001",
          nickname: "小宇",
          avatar: guildAvatar,
          bio: "偏好閱讀奇幻故事與推理小說，熱衷解謎樂趣...",
        },
        {
          id: "M002",
          nickname: "茉莉",
          avatar: guildAvatar,
          bio: "最近正在研讀《原子習慣》，積極培養良好習慣...",
        },
      ], incomingRequests: [
        {
          id: "M020",
          nickname: "小琣",
          avatar: guildAvatar,
          bio: "喜好科學書籍，熱衷做科學研究...",
        },
        {
          id: "M021",
          nickname: "森林系女孩",
          avatar: guildAvatar,
          bio: "經典愛好者，喜歡看國外經典文學...",
        }
      ],
      sentRequests: [
        {
          id: "M031",
          nickname: "阿瓦",
          avatar: guildAvatar,
          bio: "財經相關的書籍皆有涉略，歡迎一起交流...",
        },
        {
          id: "M021",
          nickname: "庭庭",
          avatar: guildAvatar,
          bio: "心理書籍、療癒系小品散文...",
        }
      ], openMenuId: null,
    }
  },
  methods: {
    toggleMenu(id) {
      if (this.openMenuId === id) {
        this.openMenuId = null
      } else {
        this.openMenuId = id
      }
    },
    askAction(member, type) { // 觸發：記錄要做什麼
      this.pendingAction = { member, type }
      this.openMenuId = null

    },
    cancelAction() { // 取消：清空暫存
      this.pendingAction = null
    },
    async confirmAction() { // 確認：真正執行 + 清空暫存
      if (this.pendingAction.type === 'accept') {
        // this.incomingRequests = this.incomingRequests.filter(item => item.user_id !== this.pendingAction.member.user_id)
        // this.members.push(this.pendingAction.member)

        const res = await fetch(`${API_BASE}/accept_friend_request.php`,
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${this.token}`
            },
            body: JSON.stringify({ fromUserId: this.pendingAction.member.user_id })
          });

        const result = await res.json();
        if (result.success) {
          await this.loadFriends();
        }

      } else if (this.pendingAction.type === 'delete') {
        const res = await fetch(`${API_BASE}/delete_friend.php`,
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${this.token}`
            },
            body: JSON.stringify({ deleteUserId: this.pendingAction.member.user_id })
          });
        const result = await res.json();
        if (result.success) {
          await this.loadFriends();
        }


        // this.members = this.members.filter(item => item.user_id !== this.pendingAction.member.user_id)
      } else if (this.pendingAction.type === 'cancel') {

        const res = await fetch(`${API_BASE}/cancel_friend_request.php`,
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${this.token}`
            },
            body: JSON.stringify({ toUserId: this.pendingAction.member.user_id })

          });
        const result = await res.json();

        if (result.success) {
          await this.loadFriends();
        }



        // this.sentRequests = this.sentRequests.filter(item => item.user_id !== this.pendingAction.member.user_id)
      }
      this.pendingAction = null

    }, async rejectInvite(member) { // 拒絕好友邀請，不用確認直接執行

      const res = await fetch(`${API_BASE}/reject_friend_request.php`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${this.token}`
          },
          body: JSON.stringify({ fromUserId: member.user_id })
        });

      const result = await res.json();
      if (result.success) {
        await this.loadFriends();
      }

      // this.incomingRequests = this.incomingRequests.filter(item => item.user_id !== member.user_id)

    }, async loadFriends() {
      const res = await fetch(`${API_BASE}/get_friends.php`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${this.token}` }
      });
      const result = await res.json();

      if (result.success) {
        this.members = result.friends
        this.incomingRequests = result.incomingRequests
        this.sentRequests = result.sentRequests

      }

    },

  },
  computed: {
    activeList() {
      let list
      if (this.activeTab === 'incoming') list= this.incomingRequests
      else if (this.activeTab === 'sent') list= this.sentRequests
      else list=this.members
      
      return list.filter(member=>member.nickname.includes(this.searchFriend))


    },
    ...mapState(useUserStore, ["token"]),
    



  },
  mounted() {
    this.loadFriends();
  },
  
}
</script>
<template>



  <!-- member-list -->

  <div class=" member-list container-content bgc-content">
    <div class="col-10">
      <GuildBreadcrumb class="col-10" :items="[
        { label: '❮  首頁', to: `/home` },// guilds/:id 填入目前公會的 id
        { label: '我的好友' }
      ]" />



      <div class="member-tabs">
        <a class="member-tab" :class="{ 'is-active': activeTab === 'all' }" @click="activeTab = 'all'">好友列表<span>{{
          members.length }}</span></a>
        <a class="member-tab" :class="{ 'is-active': activeTab === 'incoming' }" @click="activeTab = 'incoming'">
          好友邀請 <span class="member-badge">{{ incomingRequests.length }}</span>
        </a>
        <a class="member-tab" :class="{ 'is-active': activeTab === 'sent' }" @click="activeTab = 'sent'">
          已送出邀請<span class="member-badge">{{ sentRequests.length }}</span>
        </a>
      </div>
      <div class="search-friends-wrapper col-10 ">
        <AppIcon name="search" :size="16" class="search-friends-icon" />
        <input type="text" id="search-friends" placeholder="搜尋好友名稱" v-model="searchFriend">
      </div>

      <ul class="member-list-items">
        <li class="member-row" v-for="member in activeList" :key="member.user_id">
          <PhotoSticker :userId="member.user_id" :width="40" class="member-avatar"/>
          <div class="member-info">
            <span class="member-name">{{ member.nickname }}</span>
            <p class="member-bio">{{ member.bio }}</p>
          </div>
          <div class="member-action">
            <button class="member-more" aria-label="更多操作" @click="toggleMenu(member.user_id)">...</button>

            <div class="member-dropdown" :class="{ 'is-open': openMenuId === member.user_id }">
              <template v-if="activeTab === 'incoming'">
                <button class="member-dropdown-item" @click="askAction(member, 'accept')">同意邀請</button>
                <button class="member-dropdown-item" @click="rejectInvite(member)">拒絕邀請</button>
              </template>
              <template v-else-if="activeTab === 'sent'">
                <button class="member-dropdown-item" @click="askAction(member, 'cancel')">取消邀請</button>
              </template>
              <template v-else>
                <button class="member-dropdown-item" @click="askAction(member, 'delete')">刪除好友</button>
              </template>

            </div>
          </div>
        </li>
      </ul>



      <div v-if="pendingAction" class="confirm-modal-overlay">
        <div class="confirm-modal">

          <div class="confirm-modal-spacing">

            <p v-if="pendingAction.type === 'accept'" class="confirm-modal__text">請問確定要接受好友邀請嗎？</p>
            <p v-else-if="pendingAction.type === 'delete'" class="confirm-modal__text">請問確定要刪除好友嗎？</p>
            <p v-else class="confirm-modal__text">確定要取消這則已送出的邀請嗎？</p>

          </div>

          <div class="confirm-modal__actions">
            <div class="confirm-modal__cancel" @click="cancelAction()">取消</div>
            <div class="confirm-modal__confirm" @click="confirmAction()">確認</div>
          </div>

        </div>

      </div>






    </div>
  </div>




</template>


<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;



.member-list-items {
  list-style: none;
  padding: 0;
  margin: 0;
}

.member-row {
  display: flex;
  align-items: center;
  gap: $spacing-md;
  padding-top: $spacing-md ;
  padding-bottom: $spacing-sm ;
  border-bottom: 1px solid $neutral-300;

}

.member-info {
  flex: 1; // 吃滿頭貼跟按鈕中間剩下的寬度
  display: flex;
  flex-direction: column;
}

.member-bio {
  font-size: $p-sm-size;
  color: $neutral-500;
  margin: 0;
  @include text-ellipsis(1); // 你的 mixins.scss 裡已經有這個，超出一行會變 ...
}



.member-tabs {
  display: flex;
  gap: $spacing-md;
  margin-bottom: $spacing-md;
  border-bottom: 1px solid $neutral-300;
  

}

.member-tab {
  background: none;
  border: none;
  padding: $spacing-sm 0;
  font-size: $label-md-size;
  font-weight: $heading-weight;
  color: $neutral-500;
  cursor: pointer;

  &.is-active {
    color: $primary;
    border-bottom: 4px solid $primary-300;
  }

  @media (max-width:768px) {
   font-size: $p-sm-size;
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
  &--online {
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
   object-position: center 10%;
   background-color: $secondary-100;
}

.member-member-info,
.apply-member-info {
  display: flex;
  flex-direction: column;
}

.member-name,
.apply-name {
  font-size: $p-sm-size;
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
  &--vice {
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
  border-radius: 5px;

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
  border-radius: 5px;

  &:hover {
    background: $neutral-200;
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

.apply-col {

  &--member,
  &--time,
  &--handle {
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

.search-friends-wrapper {
  position: relative;
  width: 100%;
  margin-bottom: $spacing-md;
}

.search-friends-icon {
  position: absolute;
  left: $spacing-sm ;
  top: 50%;
  transform: translateY(-50%);
  color: $neutral-400;
}

#search-friends {
  padding-left: calc(#{$spacing-sm} * 2 + 16px); // 左邊留空間給圖示，不要文字被蓋住
  height: 40px;
  width: 100%;
  border: 1px solid $neutral-400;
  border-radius: 4px;
}


.confirm-modal-spacing {
  margin-bottom: $spacing-md;
}
</style>