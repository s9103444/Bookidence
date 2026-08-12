<script>
import AppIcon from "@/components/common/AppIcon.vue";
import defaultCover from "../../assets/images/little-prince-cover.png";

export default {
  components: {
    AppIcon,
  },
  props: {
    eventId: {
      type: [Number, String],
      default: null,
    },
    bookName: {
      type: String,
      default: "小王子",
    },
    author: {
      type: String,
      default: "安托萬·德·聖修伯里",
    },
    eventType: {
      type: String,
      default: "線下活動",
    },
    eventTime: {
      type: String,
      default: "2026.10.10 (五) 19:00 - 21:30 (GMT+8)",
    },
    location: {
      type: String,
      default: "台灣台北市松山區復興北路1號6樓之3-603教室",
    },
    locationNote: {
      type: String,
      default: "亞細亞大樓六樓-小樹屋共享空間",
    },
    participantCount: {
      type: Number,
      default: 5,
    },
    coverImage: {
      type: String,
      default: defaultCover,
    },
  },
  emits: ["view-event"],
  methods: {
    handleClick() {
      this.$emit("view-event", this.eventId);
    },
  },
};
</script>

<template>
  <div class="guild-event-card" @click="handleClick">
    <div class="guild-event-card__cover">
      <img
        class="guild-event-card__cover-img"
        :src="coverImage"
        :alt="bookName"
      />
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
          <AppIcon name="clock" :size="16" />
          {{ eventTime }}
        </li>
        <li class="guild-event-card__meta-item">
          <AppIcon name="map-pin" :size="16" />
          <span>
            {{ location }}
            <span v-if="locationNote" class="guild-event-card__location-note">
              ({{ locationNote }})
            </span>
          </span>
        </li>
        <li class="guild-event-card__meta-item">
          <AppIcon name="users" :size="16" />
          目前{{ participantCount }}位參加者
        </li>
      </ul>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;
@use "../../assets/scss/abstracts/mixins" as *;

.guild-event-card {
  display: flex;
  align-items: flex-start;
  gap: $spacing-lg;
  padding: $spacing-lg $spacing-md;
  background: $neutral-100;
  cursor: pointer;
  border-radius: 8px;
  transition:
    box-shadow 0.15s ease,
    transform 0.15s ease;

  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
  }

  // 寬度不夠時，封面改到資訊上方，不要硬擠在旁邊
  @include tablet {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
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
  width: 100%; // 直排模式下，資訊區要吃滿卡片寬度，不然文字會被壓縮到很窄
}

.guild-event-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap; // 書名+標籤放不下同一行時，標籤自動換到下一行，不會硬擠或溢出
  gap: $spacing-sm;

  @include tablet {
    justify-content: center; // 直排模式下置中，跟封面對齊
  }
}

.guild-event-card__title-group {
  display: flex;
  flex-direction: column; // 書名、作者改成上下排列，不再並排搶位置
  gap: 2px;
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
  color: $neutral-500; // 作者比書名淺一階，兩行資訊才有主從層次，不是兩行一樣重
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
  word-break: break-word; // 地址這種長文字，窄螢幕不會撐破卡片、造成橫向捲軸

  @include tablet {
    justify-content: center; // 直排模式下置中，跟封面對齊
    text-align: left; // 但文字內容本身還是靠左讀，只有整排置中，不是每個字置中
  }
}

.guild-event-card__location-note {
  display: block;
}
</style>
