<template>
  <div class="overlay" v-show="searchActive" @click="$emit('close')"></div>
  <div class="layout" :class="{ 'search-active': searchActive }">
    <SearchBar v-model="keyword"></SearchBar>
    <div class="tabs">
      <button
        v-for="tab in tabs"
        :class="{ 'is-active': activeTab == tab.id }"
        :key="tab.id"
        @click="activeTab = tab.id"
      >
        {{ tab.name }}
      </button>
    </div>
    <div class="search-result-wrapper">
      <div
        class="search-result"
        v-for="item in results"
        :key="item.id ?? item.user_id"
      >
        <MainSearchGuild
          :guild="item"
          v-if="activeCategory == 'guild'"
        ></MainSearchGuild>
        <MainSearchBook
          :book="item"
          v-if="activeCategory == 'book'"
        ></MainSearchBook>
        <MainSearchUser
          :user="item"
          v-if="activeCategory == 'user'"
        ></MainSearchUser>
      </div>
    </div>
  </div>
</template>

<script>
import SearchBar from "../../components/common/SearchBar.vue";
import MainSearchGuild from "../front/MainSearchGuild.vue";
import MainSearchBook from "../front/MainSearchBook.vue";
import MainSearchUser from "../front/MainSearchUser.vue";
import { API_BASE, API_STATIC } from "../../common/api.js";

export default {
  props: {
    searchActive: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    SearchBar,
    MainSearchGuild,
    MainSearchBook,
    MainSearchUser,
  },
  data() {
    return {
      tabs: [
        { id: 1, name: "讀書公會", category: "guild" },
        { id: 2, name: "書籍名稱", category: "book" },
        { id: 3, name: "書籍作者", category: "book" },
        { id: 4, name: "用戶名", category: "user" },
      ],
      activeTab: 1,
      keyword: "",
      results: [],
      searchTimer: null,
    };
  },
  computed: {
    activeCategory() {
      const tab = this.tabs.find((t) => t.id === this.activeTab);
      return tab ? tab.category : "";
    },
  },
  watch: {
    activeTab() {
      this.triggerSearch();
    },
    keyword() {
      this.triggerSearch();
    },
  },
  methods: {
    triggerSearch() {
      clearTimeout(this.searchTimer);
      if (!this.keyword) {
        this.results = [];
        return;
      }
      // debounce：等使用者停下 300ms 再打 API，避免每打一個字就送一次請求
      this.searchTimer = setTimeout(() => this.fetchResults(), 300);
    },
    async fetchResults() {
      const category = this.activeCategory;
      const url = `${API_BASE}/main_search.php?category=${category}&keyword=${encodeURIComponent(this.keyword)}`;
      const res = await fetch(url);
      const result = await res.json();
      this.results = (result.data ?? []).map((row) =>
        this.mapRow(category, row),
      );
    },
    resolveImage(path) {
      return path ? `${API_STATIC}/src/common/uploads/${path}` : "";
    },
    mapRow(category, row) {
      if (category === "guild") {
        return {
          id: row.guild_id,
          avatar: this.resolveImage(row.guild_avatar),
          name: row.guild_name,
          code: row.guild_code,
          currentBook: row.title,
          memberCount: row.member_count,
        };
      }
      if (category === "book") {
        return {
          id: row.book_id,
          cover: this.resolveImage(row.bc_image),
          title: row.title,
          author: row.author,
          category: row.categories,
          publisher: row.publisher,
          publishDate: row.p_date,
        };
      }
      if (category === "user") {
        return {
          user_id: row.user_id,
          member_code: row.member_code,
          nickname: row.nickname,
          favoriteCategories: row.categories ? row.categories.split(",") : [],
        };
      }
      return row;
    },
  },
};
</script>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.overlay {
  position: fixed;
  inset: 0;
  z-index: 50;
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
}

.layout {
  position: absolute;
  width: 100%;
  left: 0;
  top: $header-height;
  z-index: 50;
  transform: translateY(-100%);
  transition:
    transform 0.2s ease,
    visibility 0.2s ease;
  background-color: $neutral-100;
  padding: 24px;
  height: 400px;
  visibility: hidden;

  &.search-active {
    transform: translateY(0);
    visibility: visible;
  }
}

.tabs {
  font-size: $p-sm-size;
  display: flex;
  gap: 40px;
  margin-top: 24px;
  margin-inline: 24px;
}

.tabs button {
  position: relative;
  &::before {
    content: "";
    display: block;
    position: absolute;
    left: 0;
    bottom: -2px;
    height: 1px;
    width: 0;
    transition: all 0.2s ease;
    background-color: $primary;
  }
}

.tabs button.is-active {
  font-weight: $heading-weight;
  color: $primary;
  &::before {
    content: "";
    display: block;
    position: absolute;
    width: 100%;
  }
}

.search-result-wrapper {
  height: 100%;
  overflow-y: auto;
}

.search-result {
  margin-top: 12px;
  margin-inline: 24px;
  transition: all 0.2s ease;
}

.search-result:hover {
  background-color: $neutral-200;
}
</style>
