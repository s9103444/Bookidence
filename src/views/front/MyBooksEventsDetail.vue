<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import AppButton from "@/components/common/AppButton.vue";
import PhotoSticker from "@/components/front/PhotoSticker.vue";
import { API_BASE, API_STATIC } from "@/common/api";
import { useUserStore } from "@/stores/user";

const route = useRoute();
const router = useRouter();
const userStore = useUserStore();

const event = ref(null);
const categories = ref([]);
function loadEvent(){
  fetch(`${API_BASE}/guild_get_events.php?event_id=${route.params.id}`)
  .then(res => res.json()).then(data => {
  if(data.success){
    event.value = data.event;
    categories.value = data.categories;
    participants.value = data.participants;
  }
});
}
onMounted(() => {
  loadEvent();
});


const participants = ref([]);
const roleMap = {
  '會長': '會長',
  '副會長': '副會長',
  '一般': '一般會員',
};
const canCancelEvent = computed(() => {
  if (!event.value) return false;
const daysUntilEvent = (new Date(event.value.event_date) - new Date()) / (1000 * 60 * 60 * 24);
  return daysUntilEvent > 7;
});

function cancelEvent() {
  if (!confirm(`請問確定要取消「${event.value.book_title}」讀書會活動嗎？`)) return;

  fetch(`${API_BASE}/cancel_my_book_event.php`, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${userStore.token}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ cancelEvent: route.params.id }),
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert("活動已取消");
        router.push("/member/my-books-events");
      } else {
        alert(data.message);
      }
    });
}
</script>

<template>
  
  <GuildBreadcrumb :items="[
    { label: '❮  我的讀書會活動', to: `/member/my-books-events` },
    { label: '讀書會活動詳情' }
  ]" />

  <div class="detail container-content" v-if="event">
    <div class="detail-main col-6">
      <div class="detail-guild">
        <img :src="event.guild_avatar.startsWith('http') ? event.guild_avatar : `${API_STATIC}/uploads/${event.guild_avatar}`" :alt="event.guild_name" class="detail-avatar detail-avatar--guild">
        <div class="detail-guild-info">
          <span class="detail-label">讀書公會</span>
          <span class="detail-guild-name">{{ event.guild_name }}</span>
        </div>
      </div>

      <div class="detail-book">
        <img :src="event.bc_image.startsWith('http') ? event.bc_image : `${API_STATIC}/uploads/${event.bc_image}`" :alt="event.book_title" class="detail-book-cover">
        <div class="detail-book-meta">
          <h2 class="detail-book-title">{{ event.book_title }}</h2>
          <div class="detail-book-list">
            <p>作者：{{ event.book_author }}</p>
            <p>類別：{{ categories.join('、') }}</p>
            <p>出版日期：{{ event.book_p_date }}</p>
            <p>出版社：{{ event.book_publisher }}</p>
            <p>ISBN：{{ event.book_isbn }}</p>
          </div>
        </div>
      </div>

      <div class="detail-status">
        <div class="detail-status-info">
          <div class="detail-status-item">
            <span class="detail-label">活動類型</span>
            <span class="detail-status-value">{{ event.event_type.includes('線上') ? '線上活動' : '線下活動' }}</span>
          </div>
          <div class="detail-status-item">
            <span class="detail-label">截止時間</span>
            <span class="detail-status-value">{{ event.deadline }}</span>
          </div>
        </div>
        <div class="detail-signup">
          <span class="detail-signup-text">已報名 {{ event.participant_count }} / {{ event.max_participants }}</span>
        </div>
      </div>

      <div class="detail-people">
        <div class="detail-person">
          <div class="detail-avatar">
          <PhotoSticker class="detail-avatar-canvas" :userId="event.organizer_user_id" :width="90" />
        </div>
          <div class="detail-person-info">
            <span class="detail-label">活動發起人</span>
            <span class="detail-person-name">{{ event.organizer_name }} {{ event.organizer_member_code }}</span>
          </div>
        </div>
        <div class="detail-person">
          <div class="detail-avatar">
          <PhotoSticker class="detail-avatar-canvas" :userId="event.leader_user_id" :width="90" />
        </div>
          <div class="detail-person-info">
            <span class="detail-label">本期領讀人</span>
            <span class="detail-person-name">{{ event.leader_name }} {{ event.leader_member_code }}</span>
          </div>
        </div>
      </div>

      <div class="detail-meta">
        <div class="detail-meta-row">
          <span class="detail-tag">活動時間</span>
          <span class="detail-meta-value">{{ event.event_date.replaceAll('-', '.') }} | {{ event.event_time.slice(0, 5) }} ~ {{ event.event_end_time.slice(0, 5) }}</span>
        </div>
        <div class="detail-meta-row">
        <span class="detail-tag">{{ event.event_type.includes('線上') ? '會議連結' : '活動地點' }}</span>
        <span class="detail-meta-value">{{ event.event_location || event.meeting_url }}</span>
        </div>
      </div>

      <div class="detail-intro-title">
        <span class="detail-label">活動說明</span>
      </div>
      <div class="detail-intro-text">
        <p>{{ event.description }}</p>
      </div>
    </div>

    <div class="detail-side col-4">
      <span class="detail-side-title">參加成員</span>
      <div class="detail-member-list">
        <input type="text" class="detail-member-search" placeholder="成員名稱">
        <div class="detail-member" v-for="member in participants" :key="member.user_id">
          <div class="detail-avatar">
            <PhotoSticker class="detail-avatar-canvas" :userId="member.user_id" :width="90" />
          </div>
          <div class="detail-member-info">
            <span class="detail-member-name">{{ member.nickname }}</span>
            <span class="detail-member-id">{{ member.member_code }}</span>
          </div>
          <span class="detail-member-role">{{ roleMap[member.permission_level] }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="detail-actions container-content">
    <AppButton v-if="canCancelEvent" :style="{ '--btn-color': '#d9534f' }" @click="cancelEvent">取消活動</AppButton>
    <AppButton color="primary">產生活動QR碼</AppButton>
  </div>
</template>

<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.detail-main,
.detail-side {
  @include tablet {
    grid-column: 1 / -1;
  }
}

.detail-avatar {
  display: inline-block;
  width: 49px;
  height: 49px;
  border-radius: 50%;
  background-color: $secondary-100;
  object-fit: cover;
  overflow: hidden;
  flex-shrink: 0;

  &--guild {
    width: 51px;
    height: 50px;
  }

  .detail-avatar-canvas {
    margin-top: 5px;
    margin-left: 4px;
    transform: scale(0.46);
    transform-origin: top left;
  }
}

.detail-label {
  font-size: $label-sm-size;
  color: $primary;
}

// 讀書公會
.detail-guild {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
}

.detail-guild-info {
  display: flex;
  flex-direction: column;
}

.detail-guild-name {
  font-size: $label-md-size;
  font-weight: $heading-weight;
  color: $neutral-800;
}

// 書籍資訊
.detail-book {
  display: flex;
  align-items: flex-end;
  gap: $spacing-lg;
  margin-top: $spacing-lg;
}

.detail-book-cover {
  width: 174px;
  aspect-ratio: 174 / 246;
  object-fit: cover;
  flex-shrink: 0;
}

.detail-book-meta {
  display: flex;
  flex-direction: column;
  gap: $spacing-sm;
}

.detail-book-title {
  font-size: $label-lg-size;
  font-weight: $heading-weight;
  color: $primary;
  margin: 0 0 $spacing-sm;
  padding-bottom: $spacing-sm;
  border-bottom: 1px solid $neutral-300;
}

.detail-book-list {
  display: flex;
  flex-direction: column;
  gap: $spacing-xs;

  p {
    margin: 0;
    font-size: $label-xs-size;
    color: $primary;
  }
}

// 活動狀態
.detail-status {
  position: relative;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: $spacing-lg;
  margin-top: $spacing-lg;
  padding: $spacing-md 0;

  &::before,
  &::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    height: 1px;
    background-color: $neutral-300;
  }

  &::before {
    top: 0;
  }

  &::after {
    bottom: 0;
  }
}

.detail-status-info {
  display: flex;
  gap: $spacing-xl;
}

.detail-status-item {
  display: flex;
  flex-direction: column;
}

.detail-status .detail-label {
  font-weight: $heading-weight;
}

.detail-status-value {
  font-size: $label-sm-size;
  color: $neutral-800;
}

.detail-signup {
  display: flex;
  align-items: center;
  padding: $spacing-xs $spacing-md;
  background: $primary-500;
  border-radius: 999px;
}

.detail-signup-text {
  font-size: 10px;
  color: $neutral-100;
}

// 發起人 / 領讀人
.detail-people {
  display: flex;
  gap: $spacing-xl;
  margin-top: $spacing-lg;

  @include mobile {
    flex-direction: column;
    gap: $spacing-md;
  }
}

.detail-person {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
}

.detail-person-info {
  display: flex;
  flex-direction: column;
}

.detail-person-info .detail-label {
  font-weight: $heading-weight;
}

.detail-person-name {
  font-size: $label-sm-size;
  color: $neutral-800;
}

// 時間 / 地點
.detail-meta {
  display: flex;
  flex-direction: column;
  gap: $spacing-md;
  margin-top: $spacing-lg;
}

.detail-meta-row {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
}

.detail-tag {
  padding: $spacing-xs $spacing-md;
  border: 1px solid $primary-500;
  border-radius: 5px;
  font-size: $label-xs-size;
  font-weight: $heading-weight;
  color: $primary;
  white-space: nowrap;
}

.detail-meta-value {
  font-size: $label-sm-size;
  color: $neutral-800;
}

// 活動說明
.detail-intro-title {
  margin-top: $spacing-lg;
}

.detail-intro-title .detail-label {
  font-weight: $heading-weight;
}

.detail-intro-text {
  margin-top: $spacing-sm;

  p {
    margin: 0;
    font-size: $label-xs-size;
    color: $neutral-800;
    line-height: $text-line-height;
  }
}

// 右側：參加成員
.detail-side-title {
  display: block;
  font-size: $label-md-size;
  font-weight: $heading-weight;
  color: $neutral-800;
  margin-bottom: $spacing-md;
}

.detail-member-list {
  display: flex;
  flex-direction: column;
  gap: $spacing-sm;
}

.detail-member-search {
  padding: $spacing-sm $spacing-md;
  border: none;
  border-radius: 5px;
  background: $neutral-200;
  font-size: $label-sm-size;
  margin-bottom: $spacing-xs;
}

.detail-member {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
}

.detail-member-info {
  display: flex;
  flex-direction: column;
  flex: 1;
}

.detail-member-name {
  font-size: $label-sm-size;
  color: $neutral-800;
}

.detail-member-id {
  font-size: $label-sm-size;
  color: $neutral-500;
}

.detail-member-role {
  font-size: $label-sm-size;
  color: $neutral-600;
}

// 下方操作按鈕
.detail-actions {
  display: flex;
  justify-content: center;
  gap: 36px;
  margin-top: $spacing-xl;
  margin-bottom: 48px;
}
</style>

