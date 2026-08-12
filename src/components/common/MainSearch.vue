<template>
  <div class="layout">
    <SearchBar></SearchBar>
  </div>
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
  <div class="search-result">
    <MainSearchGuild
      :guild="guildStore.guilds[0]"
      v-if="activeTab == 1"
    ></MainSearchGuild>
  </div>
</template>

<script>
import SearchBar from "../../components/common/SearchBar.vue";
import MainSearchGuild from "../front/MainSearchGuild.vue";
import { useGuildStore } from "../../stores/guild.js";
export default {
  components: {
    SearchBar,
    MainSearchGuild,
  },
  computed: {
    guildStore() {
      return useGuildStore();
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

.tabs {
  display: flex;
  gap: 40px;
  margin-top: 24px;
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
</style>
