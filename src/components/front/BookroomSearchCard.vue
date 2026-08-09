<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.card {
  display: flex;
  // width: 100%;
  justify-content: space-between;
  gap: 20px;
  align-items: center;
}

.book-cover {
  width: 100px;
  min-width: 100px;
  aspect-ratio: unquote($book-cover-ratio);
  overflow: hidden;

  & img {
    width: 100%;
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
  color: $brown;
  font-weight: $heading-weight;
  font-size: $p-lg-size;
}

hr {
  border: 0.5px solid $brown;
}

.info {
  color: $brown;
  display: flex;
  gap: 10px;

  font-weight: $text-weight;
  font-size: $label-xs-size;
}

.separator::after {
  content: "";
  display: inline-block; // 加這行，width/height 才會生效
  height: 14px;
  width: 1px;
  background-color: $brown;
  margin-left: 10px; // 建議加一點間距，讓線不要貼著文字
  vertical-align: middle; // 讓線跟文字垂直置中對齊
}

.context {
  margin-block: $spacing-xs;
  color: $brown-light;
  font-size: $label-xxs-size;
}

.btns {
  display: flex;
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
  .context {
    display: none;
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
    <div class="book-cover">
      <img src="../../assets/images/twilight-cover.png" alt="twilight-cover" />
    </div>
    <div class="content">
      <div>
        <span class="title">暮光之城</span>
      </div>
      <hr />
      <div class="info">
        <span class="separator">史蒂芬妮．梅爾</span>
        <span class="separator">文學小說</span>
        <span class="separator">尖端出版</span>
        <span>2011/06/10</span>
      </div>
      <p class="context">
        占據國內外各大排行榜長達三年的時間，《暮光之城》、《新月》、《蝕》、《破曉》與《布莉的重生》融合了浪漫、驚險與神祕，史蒂芬妮．梅爾描繪了一系列人類、吸血鬼與狼人之間令人動容的感情糾葛，及各種族間令人難以忘懷的對決……
      </p>
      <div class="btns">
        <AppButton class="add-book wb" size="xs" color="brown">
          <AppIcon class="icon-heart" name="heart" size="20" />加入我的藏書
        </AppButton>
        <AppButton class="add-book mb" size="xs" color="brown">
          <AppIcon class="icon-heart" name="heart" size="16" />
        </AppButton>
        <AppButton class="trans wb" size="xs" color="brown" variant="outlined"
          >查看書籍</AppButton
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
  components: {
    AppButton,
    AppIcon,
  },
};
</script>
