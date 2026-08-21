<script setup>
import { ref } from "vue";
import { useRoute } from "vue-router";
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import AppButton from "@/components/common/AppButton.vue";
import bookCover from "@/assets/images/little-prince-cover.png";
import memberAvatar from "@/assets/images/guild/girl.png";
import guildAvatar from "@/assets/images/guild/guildAvatar.png";

const route = useRoute();

// 之後接後端資料，這裡先用假資料佔位
const event = ref({
  guild: { name: '壁爐與貓' },
  book: {
    title: '小王子',
    author: '史蒂芬妮．梅爾',
    category: '奇幻小說',
    translator: '瞿秀蕙/ 安麗姬',
    publishDate: '2011/06/10',
    publisher: '尖端出版',
    isbn: '000-0000000000',
  },
  type: '線下活動',
  deadline: '2026.08.24',
  signedUp: 4,
  capacity: 10,
  organizer: { name: '小森讀取中', id: 'BKD00003' },
  guide: { name: '泡泡小鹿', id: 'BKD00072' },
  time: '2026.09.15 | 14:00 ~ 16:00',
  address: '320桃園市中壢區舊明里長安街1之13號',
  description: '你也曾經是那個會畫出「吞了大象的蟒蛇」，卻被大人說是帽子的孩子嗎？這次我們想找幾位一樣還記得那份天真的人，一起在咖啡香裡重新翻開《小王子》。不需要準備什麼深奧的見解，帶著你對那朵玫瑰、那隻狐狸、或是那片星空的想法來就好——聊聊我們是不是也曾經，在長大的路上不小心弄丟了自己的星球。',
});

const members = ref([
  { id: 'BKD00003', name: '小森已讀取', roleLabel: '會長' },
  { id: 'BKD00003', name: '小森已讀取', roleLabel: '副會長' },
  { id: 'BKD00003', name: '小森已讀取', roleLabel: '一般會員' },
  { id: 'BKD00003', name: '小森已讀取', roleLabel: '一般會員' },
]);
</script>

<template>
  
  <GuildBreadcrumb :items="[
    { label: '❮  我的讀書會活動', to: `/front/member/my-books-events` },
    { label: '讀書會活動詳情' }
  ]" />

  <div class="detail container-content">
    <div class="detail-main col-6">
      <div class="detail-guild">
        <img :src="guildAvatar" alt="" class="detail-avatar detail-avatar--guild">
        <div class="detail-guild-info">
          <span class="detail-label">讀書公會</span>
          <span class="detail-guild-name">{{ event.guild.name }}</span>
        </div>
      </div>

      <div class="detail-book">
        <img :src="bookCover" alt="小王子" class="detail-book-cover">
        <div class="detail-book-meta">
          <h2 class="detail-book-title">{{ event.book.title }}</h2>
          <div class="detail-book-list">
            <p>作者：{{ event.book.author }}</p>
            <p>類別：{{ event.book.category }}</p>
            <p>譯者：{{ event.book.translator }}</p>
            <p>出版日期：{{ event.book.publishDate }}</p>
            <p>出版社：{{ event.book.publisher }}</p>
            <p>ISBN：{{ event.book.isbn }}</p>
          </div>
        </div>
      </div>

      <div class="detail-status">
        <div class="detail-status-info">
          <div class="detail-status-item">
            <span class="detail-label">活動類型</span>
            <span class="detail-status-value">{{ event.type }}</span>
          </div>
          <div class="detail-status-item">
            <span class="detail-label">截止時間</span>
            <span class="detail-status-value">{{ event.deadline }}</span>
          </div>
        </div>
        <div class="detail-signup">
          <span class="detail-signup-text">已報名 {{ event.signedUp }} / {{ event.capacity }}</span>
        </div>
      </div>

      <div class="detail-people">
        <div class="detail-person">
          <img :src="memberAvatar" alt="" class="detail-avatar">
          <div class="detail-person-info">
            <span class="detail-label">活動發起人</span>
            <span class="detail-person-name">{{ event.organizer.name }} {{ event.organizer.id }}</span>
          </div>
        </div>
        <div class="detail-person">
          <img :src="memberAvatar" alt="" class="detail-avatar">
          <div class="detail-person-info">
            <span class="detail-label">本期領讀人</span>
            <span class="detail-person-name">{{ event.guide.name }} {{ event.guide.id }}</span>
          </div>
        </div>
      </div>

      <div class="detail-meta">
        <div class="detail-meta-row">
          <span class="detail-tag">活動時間</span>
          <span class="detail-meta-value">{{ event.time }}</span>
        </div>
        <div class="detail-meta-row">
          <span class="detail-tag">活動地點</span>
          <span class="detail-meta-value">{{ event.address }}</span>
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
        <div class="detail-member" v-for="(member, index) in members" :key="index">
          <img :src="memberAvatar" alt="" class="detail-avatar">
          <div class="detail-member-info">
            <span class="detail-member-name">{{ member.name }}</span>
            <span class="detail-member-id">{{ member.id }}</span>
          </div>
          <span class="detail-member-role">{{ member.roleLabel }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="detail-actions container-content">
    <AppButton :style="{ '--btn-color': '#d9534f' }">取消活動</AppButton>
    <AppButton color="primary">產生活動QR碼</AppButton>
  </div>
</template>

<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.detail-avatar {
  display: inline-block;
  width: 49px;
  height: 49px;
  border-radius: 50%;
  background-color: $neutral-300;
  object-fit: cover;
  flex-shrink: 0;

  &--guild {
    width: 51px;
    height: 50px;
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

