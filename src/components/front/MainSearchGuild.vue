<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.card {
  padding: 24px;
  display: flex;
  gap: 24px;
  align-items: center;
  position: relative;

  &::before {
    content: "";
    display: block;
    position: absolute;
    left: 0;
    bottom: -8px;
    height: 1px;
    width: 100%;
    background-color: $neutral-300;
  }
}

.guild-avatar {
  padding: 4px;
  width: 100px;
  min-width: 100px;
  aspect-ratio: 1 / 1;
  overflow: hidden;

  & img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.content {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: $spacing-xs;
}

.title {
  display: block;
  color: $primary;
  font-weight: $heading-weight;
  font-size: $p-md-size;
}

hr {
  border: 0.5px solid $primary;
}

.info {
  color: $neutral-500;
  display: flex;
  gap: 10px;

  font-weight: $text-weight;
  font-size: $label-xs-size;
}

.separator::after {
  content: "";
  display: inline-block;
  height: 14px;
  width: 1px;
  background-color: $primary;
  margin-left: 10px;
  vertical-align: middle;
}

.btns {
  display: flex;
  margin-top: 8px;
  gap: $spacing-md;
}

.trans {
  mix-blend-mode: multiply;
}
.mb {
  display: none;
}

@media (max-width: 960px) {
  .card {
    width: 100%;
  }
  .wb {
    display: none;
  }
  .mb {
    display: flex;
  }
  .separator::after {
    display: none;
  }
  .info {
    flex-direction: column;
    gap: 0;
  }
}
</style>

<template>
  <div class="card">
    <div class="guild-avatar">
      <img :src="guild.avatar" :alt="guild.name" />
    </div>
    <div class="content">
      <div>
        <span class="title">{{ guild.name }}</span>
      </div>
      <hr />
      <div class="info">
        <span class="separator">{{ guild.code }}</span>
        <span class="separator">現正閱讀：{{ guild.currentBook }}</span>
        <span>{{ guild.memberCount }}人</span>
      </div>
      <div class="btns">
        <AppButton
          class="trans wb"
          size="xs"
          color="primary"
          variant="outlined"
          @click="
            $router.push({ name: 'guild-detail', params: { id: guild.id } })
          "
          >查看公會</AppButton
        >
        <AppButton class="trans mb" size="xs" color="brown" variant="outlined"
          ><AppIcon name="arrow-right" size="12"
        /></AppButton>
      </div>
    </div>
  </div>
</template>

<script>
import AppButton from "../common/AppButton.vue";
import AppIcon from "../common/AppIcon.vue";
export default {
  data() {
    return {
      joined: false,
    };
  },
  props: { guild: Object },
  components: {
    AppButton,
    AppIcon,
  },
  methods: {
    toggleJoin() {
      this.joined = !this.joined;
    },
  },
};
</script>
