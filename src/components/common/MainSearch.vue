<template>
  <div class="overlay" v-show="searchActive" @click="$emit('close')"></div>
  <div class="layout" :class="{ 'search-active': searchActive }">
      <SearchBar></SearchBar>
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
        <div class="search-result">
          <MainSearchGuild
            :guild="guildStore.guilds[0]"
            v-if="activeTab == 1"
          ></MainSearchGuild>
          <MainSearchBook
            :book="bookStore.books[0]"
            v-if="activeTab == 2"
          ></MainSearchBook>
          <MainSearchBook
            :book="bookStore.books[0]"
            v-if="activeTab == 3"
          ></MainSearchBook>
          <MainSearchUser v-if="activeTab == 4"></MainSearchUser>
        </div>
      </div>
  </div>
 
</template>

<script>
import SearchBar from "../../components/common/SearchBar.vue";
import MainSearchGuild from "../front/MainSearchGuild.vue";
import MainSearchBook from "../front/MainSearchBook.vue";
import MainSearchUser from "../front/MainSearchUser.vue";
import { useGuildStore } from "../../stores/guild.js";
import { useBookStore } from "../../stores/book.js";
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
  computed: {
    guildStore() {
      return useGuildStore();
    },
    bookStore() {
      return useBookStore();
    },
  },
  data() {
    return {
      tabs: [
        {
          id: 1,
          name: "讀書公會",
        },
        {
          id: 2,
          name: "書籍名稱",
        },
        {
          id: 3,
          name: "書籍作者",
        },
        {
          id: 4,
          name: "用戶名",
        },
      ],
      activeTab: 1,
    };
  },
};
</script>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.overlay{
  position: fixed;
  inset: 0;
   z-index: 50;
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
}

.layout{
  position: absolute;
  width: 100%;
  left: 0;
  top: $header-height;
  z-index: 50;
  transform: translateY(-100%);
  transition: transform 0.2s ease, visibility 0.2s ease;
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

.search-result-wrapper{
  height: 100%;
  overflow-y: auto;
}

.search-result{
 margin-top: 12px;
 margin-inline: 24px;
 transition: all 0.2s ease;
}

.search-result:hover{
  background-color: $neutral-200;
}

</style>
