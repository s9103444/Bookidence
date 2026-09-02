<script>
import AppIcon from '@/components/common/AppIcon.vue'
import SectionTitle from '@/components/front/SectionTitle.vue'
import NewsCard from '@/components/front/NewsCard.vue'
import GuildCard from '@/components/front/GuildCard.vue'
import { officialNews, recommendedGuilds, recommendedBooks } from '@/data/news'

export default {
  components: {
    AppIcon,
    SectionTitle,
    NewsCard,
    GuildCard,
  },
  data() {
    return {
      officialNews,
      recommendedGuilds,
      recommendedBooks,
    }
  },
  methods: {
    goToGuildDetail(guildId) {
      this.$router.push({ name: 'guild-detail', params: { id: guildId } })
    },
  },
}
</script>

<template>
  <div class="news">
    <section class="hero">
      <div class="hero__text">
        <h1 class="hero__title">最新消息</h1>
        <p class="hero__subtitle">掌握 Bookidence 的最新動態、精選讀書公會活動與好書推薦！</p>
      </div>
    </section>

    <section class="section">
      <SectionTitle>官方消息</SectionTitle>
      <div class="card-grid">
        <NewsCard v-for="item in officialNews" :key="item.id" v-bind="item" />
      </div>
    </section>

    <section class="section">
      <div class="section__header">
        <SectionTitle>讀書公會推薦</SectionTitle>
        <router-link class="section-link" :to="{ name: 'guilds' }">
          查看全部讀書公會 <AppIcon name="arrow-right" :size="14" />
        </router-link>
      </div>
      <div class="card-grid">
        <GuildCard
          v-for="guild in recommendedGuilds"
          :key="guild.guildId"
          v-bind="guild"
          @view-guild="goToGuildDetail"
        />
      </div>
    </section>

    <section class="section">
      <div class="section__header">
        <SectionTitle>好書推薦</SectionTitle>
        <router-link class="section-link" :to="{ name: 'search' }">
          查看更多好書 <AppIcon name="arrow-right" :size="14" />
        </router-link>
      </div>
      <ul class="book-list">
        <li v-for="book in recommendedBooks" :key="book.id" class="book-list__item">
          <div class="book-list__cover">
            <img :src="book.cover" :alt="book.title" />
          </div>
          <div class="book-list__info">
            <p class="book-list__title">{{ book.title }}</p>
            <p class="book-list__author">作者：{{ book.author }}</p>
            <p class="book-list__summary">{{ book.summary }}</p>
          </div>
          <div class="book-list__side">
            <span v-for="cat in book.categories" :key="cat" class="book-list__tag">{{ cat }}</span>
            <router-link class="book-list__more" :to="{ name: 'book-detail', params: { id: book.id } }">
              查看更多
            </router-link>
          </div>
        </li>
      </ul>
    </section>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

.news {
  max-width: 1200px;
  margin: 0 auto;
  padding: $spacing-xl;

  @include tablet {
    padding: $spacing-lg;
  }

  @include mobile {
    padding: $spacing-md;
  }
}

.hero {
  position: relative;
  display: flex;
  align-items: center;
  min-height: 320px;
  padding: $spacing-xl;
  margin-bottom: $spacing-xl;
  border-radius: 16px;
  background:
    linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)),
    url('../../assets/images/hero-banner-frame/01.png'),
    url('../../assets/images/hero-banner-frame/02.png'),
    url('../../assets/images/hero-banner-frame/03.png');
  background-size: cover, cover, cover, cover;
  background-position: center, center, center, center;
  background-repeat: no-repeat, no-repeat, no-repeat, no-repeat;
}

.hero__title {
  color: $neutral-100;
  margin-bottom: $spacing-md;
}

.hero__subtitle {
  max-width: 480px;
  font-size: $p-md-size;
  color: $neutral-100;
}

.section {
  margin-bottom: $spacing-xl;
}

.section__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: $spacing-sm;
  margin-bottom: $spacing-lg;

  @include mobile {
    flex-wrap: wrap;
  }
}

.section > .section-title {
  margin-bottom: $spacing-lg;
}

.section-link {
  display: flex;
  align-items: center;
  gap: 4px;
  flex-shrink: 0;
  font-size: $p-sm-size;
  font-weight: 700;
  color: $primary;
  text-decoration: none;
}

.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: $spacing-md;
}

.book-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.book-list__item {
  display: flex;
  align-items: center;
  gap: $spacing-md;
  padding: $spacing-md 0;
  border-bottom: 1px solid $neutral-300;

  &:first-child {
    padding-top: 0;
  }
}

.book-list__cover {
  flex-shrink: 0;
  width: 56px;
  aspect-ratio: unquote($book-cover-ratio);
  border-radius: 6px;
  overflow: hidden;
  background: $primary-300;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.book-list__info {
  flex: 1;
  min-width: 0;
}

.book-list__title {
  font-weight: 700;
  margin-bottom: 2px;
}

.book-list__author {
  font-size: $p-sm-size;
  color: $neutral-600;
  margin-bottom: 4px;
}

.book-list__summary {
  font-size: $p-sm-size;
  color: $neutral-500;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-list__side {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: $spacing-sm;
  flex-shrink: 0;
  text-align: right;
}

.book-list__tag {
  padding: 2px $spacing-sm;
  border: 1px solid $neutral-300;
  border-radius: $btn-radius-rnd;
  font-size: $p-xs-size;
  color: $neutral-600;
  white-space: nowrap;
}

.book-list__more {
  font-size: $p-sm-size;
  font-weight: 700;
  color: $primary;
  text-decoration: none;
  white-space: nowrap;
}

@include mobile {
  .book-list__item {
    align-items: flex-start;
  }

  .book-list__side {
    align-items: flex-end;
  }
}
</style>
