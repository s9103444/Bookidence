<template>
  <div class="guild-event-card">
    <div class="guild-event-card__cover">
      <img class="guild-event-card__cover-img" :src="coverImage" :alt="bookName" />
    </div>
    <div class="guild-event-card__info">
      <div class="guild-event-card__header">
        <div class="guild-event-card__title-group">
          <p class="guild-event-card__book-name">{{ bookName }}</p>
          <p class="guild-event-card__author">{{ author }}</p>
        </div>
        <span class="guild-event-card__tag">{{ eventType }}</span>
      </div>

      <hr class="guild-event-card__divider" />

      <ul class="guild-event-card__meta">
        <li class="guild-event-card__meta-item">
          <IconClock :size="16" stroke-width="2" />
          {{ eventTime }}
        </li>
        <li class="guild-event-card__meta-item">
          <IconMapPin :size="16" stroke-width="2" />
          <span>
            {{ location }}
            <span v-if="locationNote" class="guild-event-card__location-note">
              ({{ locationNote }})
            </span>
          </span>
        </li>
        <li class="guild-event-card__meta-item">
          <IconUsers :size="16" stroke-width="2" />
          目前{{ participantCount }}位參加者
        </li>
      </ul>
    </div>
  </div>
</template>
<script setup>
import { IconClock, IconMapPin, IconUsers } from '@tabler/icons-vue'
import defaultCover from '../../assets/images/little-prince-cover.png'

defineProps({
  bookName: {
    type: String,
    default: '小王子',
  },
  author: {
    type: String,
    default: '安托萬·德·聖修伯里',
  },
  eventType: {
    type: String,
    default: '線下活動',
  },
  eventTime: {
    type: String,
    default: '2026.10.10 (五) 19:00 - 21:30 (GMT+8)',
  },
  location: {
    type: String,
    default: '台灣台北市松山區復興北路1號6樓之3-603教室',
  },
  locationNote: {
    type: String,
    default: '亞細亞大樓六樓-小樹屋共享空間',
  },
  participantCount: {
    type: Number,
    default: 5,
  },
  coverImage: {
    type: String,
    default: defaultCover,
  },
})
</script>

<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

.guild-event-card {
  display: flex;
  align-items: center;
  gap: $spacing-lg; // Figma 原稿是 32px，變數只有到 24/40，取較近的 24px
  padding: $spacing-lg $spacing-md;
  background: $neutral-100;
}

.guild-event-card__cover {
  flex-shrink: 0;
  width: 75px;
  height: 106px;
  box-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
}

.guild-event-card__cover-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.guild-event-card__info {
  display: flex;
  flex-direction: column;
  gap: $spacing-sm;
  flex: 1;
  min-width: 0;
}

.guild-event-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: $spacing-sm;
}

.guild-event-card__title-group {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
  min-width: 0;
}

.guild-event-card__book-name {
  font-size: $label-md-size;
  font-weight: $text-weight;
  color: $primary;
  letter-spacing: $letter-spacing-base;
  @include text-ellipsis;
}

.guild-event-card__author {
  font-size: $label-xs-size;
  font-weight: $text-weight;
  color: $primary;
  letter-spacing: $letter-spacing-base;
  @include text-ellipsis;
}

.guild-event-card__tag {
  flex-shrink: 0;
  padding: $spacing-xs $spacing-sm;
  background: $primary-300;
  color: $neutral-100;
  font-size: $p-xs-size;
  font-weight: $text-weight;
  border-radius: 5px;
  white-space: nowrap;
}

.guild-event-card__divider {
  height: 1px;
  margin: 0;
  border: none;
  background: $neutral-300;
}

.guild-event-card__meta {
  display: flex;
  flex-direction: column;
  gap: $spacing-xs;
}

.guild-event-card__meta-item {
  display: flex;
  align-items: flex-start;
  gap: $spacing-sm;
  font-size: $p-xs-size;
  font-weight: $text-weight;
  color: $primary;
  line-height: $text-line-height;
}

.guild-event-card__location-note {
  display: block;
}
</style>