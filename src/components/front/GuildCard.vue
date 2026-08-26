<script>
import AppIcon from '@/components/common/AppIcon.vue'
import AppButton from '@/components/common/AppButton.vue'

export default {
  components: {
    AppIcon,
    AppButton,
  },
  props: {
    guildId: {
      type: [Number, String],
      default: null,
    },
    avatar: {
      type: String,
      default: '',
    },
    name: {
      type: String,
      default: '公會名稱',
    },
    description: {
      type: String,
      default: '每週共讀一本小說，分享角色、劇情與那些令人難忘的文字。',
    },
    currentBook: {
      type: String,
      default: '秘密中的秘密',
    },
    memberCount: {
      type: Number,
      default: 200,
    },
    region: {
      type: String,
      default: '台北市',
    },
    tags: {
      type: Array,
      default: () => ['奇幻小說', '翻譯文學', '自我成長'],
    },
  },
  emits: ['view-guild'],
  methods: {
    handleViewClick() {
      this.$emit('view-guild', this.guildId)
    },
  },
}
</script>

<template>
  <div class="guild-card">
    <div class="guild-card__header">
      <div class="guild-card__avatar">
        <img v-if="avatar" :src="avatar" :alt="name" />
        <span v-else>頭像</span>
      </div>
      <div class="guild-card__intro">
        <p class="guild-card__name">{{ name }}</p>
        <p class="guild-card__desc">{{ description }}</p>
      </div>
    </div>

    <ul class="guild-card__info">
      <li>
        <AppIcon name="book" :size="16" />
        正在讀《{{ currentBook }}》
      </li>
      <li>
        <AppIcon name="users" :size="16" />
        {{ memberCount }} 人
      </li>
      <li>
        <AppIcon name="map-pin" :size="16" />
        {{ region }}
      </li>
    </ul>

    <div class="guild-card__tags">
      <span v-for="tag in tags" :key="tag" class="guild-card__tag">
        {{ tag }}
      </span>
    </div>

    <AppButton size="sm" class="guild-card__btn" @click="handleViewClick">
      查看公會 <AppIcon name="arrow-right" :size="16" />
    </AppButton>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;

.guild-card {
  display: flex;
  flex-direction: column;
  gap: $spacing-sm;
  width: 100%;
  padding: $spacing-md;
  border: 1px solid $neutral-300;
  border-radius: 12px;
  flex-shrink: 0;
}

.guild-card__header {
  display: flex;
  gap: $spacing-sm;
}

.guild-card__avatar {
  flex-shrink: 0;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: $primary-300;
  color: $neutral-100;
  font-size: $p-xs-size;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.guild-card__intro {
  min-width: 0;
}

.guild-card__name {
  font-weight: 700;
  margin-bottom: 2px;
}

.guild-card__desc {
  font-size: $p-sm-size;
  color: $neutral-500;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.guild-card__info {
  display: flex;
  flex-direction: column;
  gap: 4px;
  font-size: $p-sm-size;
  color: $neutral-600;

  li {
    display: flex;
    align-items: center;
    gap: 6px;
  }
}

.guild-card__tags {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.guild-card__tag {
  padding: 2px $spacing-sm;
  border: 1px solid $neutral-300;
  border-radius: $btn-radius-rnd;
  font-size: $p-xs-size;
  color: $neutral-600;
}

.guild-card__btn {
  margin-top: $spacing-xs;
}
</style>