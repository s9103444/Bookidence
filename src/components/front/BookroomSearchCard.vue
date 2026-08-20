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
  flex-wrap: wrap;
  font-weight: $text-weight;
  font-size: $label-xs-size;

  & span {
    white-space: nowrap;
  }
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
  --btn-surface: rgb(243, 235, 213);
}
.mb {
  display: none;
}
.add-book {
  --btn-surface: rgb(243, 235, 213);
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
      <img
        :src="`${apiStatic}/src/common/uploads/${book.bc_image}`"
        alt="twilight-cover"
      />
    </div>
    <div class="content">
      <div>
        <span class="title">{{ book.title }}</span>
      </div>
      <hr />
      <div class="info">
        <span class="separator">{{ book.author }}</span>
        <span class="separator">尚未串定類別API</span>
        <span class="separator">{{ book.publisher }}</span>
        <span>{{ book.p_date }}</span>
      </div>
      <p class="context">
        {{ book.description }}
      </p>
      <div class="btns">
        <AppButton
          @click="toggleAdd"
          v-show="add == false"
          class="add-book wb"
          size="xs"
          color="brown"
        >
          <AppIcon class="icon-heart" name="heart" size="20" />加入我的藏書
        </AppButton>
        <AppButton
          @click="toggleAdd"
          v-show="add == true"
          class="add-book wb"
          size="xs"
          color="brown"
          variant="outlined"
        >
          <AppIcon class="icon-heart" name="heart-filled" size="20" />已加入藏書
        </AppButton>
        <AppButton class="add-book mb" size="xs" color="brown">
          <AppIcon class="icon-heart" name="heart" size="16" />
        </AppButton>

        <AppButton
          class="trans wb"
          size="xs"
          color="brown"
          variant="outlined"
          @click="
            $router.push({ name: 'book-detail', params: { id: book.id } })
          "
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
import { API_STATIC } from "../../common/api.js";

export default {
  props: { book: Object },
  data() {
    return {
      add: false,
    };
  },

  computed: {
    apiStatic() {
      return API_STATIC;
    },
  },

  components: {
    AppButton,
    AppIcon,
  },

  methods: {
    toggleAdd() {
      this.add = !this.add;
    },
  },
};
</script>
