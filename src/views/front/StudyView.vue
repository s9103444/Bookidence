<script>
import BookroomPanelBbookArea from "../../layouts/book-room/BookroomPanelBbookArea.vue";
import BookroomPanelScriptArea from "../../layouts/book-room/BookroomPanelScriptArea.vue";
import BookroomPanelAddArea from "../../layouts/book-room/BookroomPanelAddArea.vue";
import BookroomPanelProfileArea from "../../layouts/book-room/BookroomPanelProfileArea.vue";
import BookroomPanelAppearanceArea from "../../layouts/book-room/BookroomPanelAppearanceArea.vue";
import BookroomPanelReviewWriteArea from "../../layouts/book-room/BookroomPanelReviewWriteArea.vue";
import BookroomPanelWriteTable from "../../layouts/book-room/BookroomPanelWriteTable.vue";
import BookroomPanelAddDetailArea from "../../layouts/book-room/BookroomPanelAddDetailArea.vue";
import ReviewPublishedModal from "../../components/common/ReviewPublishedModal.vue";
import AppIcon from "../../components/common/AppIcon.vue";
import { useBookStore } from "../../stores/book.js";

export default {
  components: {
    BookroomPanelBbookArea,
    BookroomPanelScriptArea,
    BookroomPanelAddArea,
    BookroomPanelProfileArea,
    BookroomPanelAppearanceArea,
    BookroomPanelReviewWriteArea,
    BookroomPanelWriteTable,
    BookroomPanelAddDetailArea,
    ReviewPublishedModal,
    AppIcon,
  },
  data() {
    return {
      isPanelOpen: false, //預設是關閉
      isWritingReview: false, //顯示書桌的開關
      showPublishSuccess: false, //心得發布成功燈箱開關
      publishedBook: null, //燈箱裡要顯示的書
      tabs: [
        {
          id: 1,
          name: "個人資訊",
        },
        {
          id: 2,
          name: "書籍專區",
        },
        {
          id: 3,
          name: "個人外觀",
        },
        {
          id: 4,
          name: "撰寫心得",
        },
      ],
      activeTab: 1,
    };
  },
  watch: {
    activeTab(newTab, oldTab) {
      if (oldTab === 4 && newTab !== 4) {
        this.bookStore.selectedBook = null;
        this.isWritingReview = false;
      }
    },
  },
  methods: {
    togglePanel() {
      this.isPanelOpen = !this.isPanelOpen;
    },
    closePanel() {
      this.isPanelOpen = false;
      this.isWritingReview = false;
      this.bookStore.selectedBook = null;
    },
    startWriteReview() {
      if (this.bookStore.selectedBook) {
        this.isWritingReview = true;
      }
    },
    resetBookSelection() {
      this.bookStore.selectedBook = null;
    },
    switchTab(id) {
      this.resetBookSelection();
      this.activeTab = id;
    },
    goToDraftDetail(book) {
      this.bookStore.selectedBook = book;
      this.isWritingReview = true;
      this.activeTab = 4;
    },
    handlePublished(book) {
      this.publishedBook = book;
      this.showPublishSuccess = true;
      this.isPanelOpen = false;
      this.isWritingReview = false;
      this.bookStore.selectedBook = null;
    },
  },
  computed: {
    bookStore() {
      return useBookStore();
    },
  },
};
</script>

<template>
  <div class="study-stage-container">
    <!-- 書房設定按鈕：web/mobile共用，位置由 960px 斷點的 CSS 調整 -->
    <div class="study-stage-setting-btn-anchor">
      <button class="study-stage-setting-btn" @click="togglePanel">
        <img
          src="../../assets/images/book-room-element/studyroom-btn-setting.png"
          alt=""
        />
      </button>
    </div>

    <!-- web版本 -->

    <div class="study-stage-web-mode">
      <!-- 背景-web -->
      <img
        class="studyroom-cover-web"
        src="../../assets/images/book-room-element/layout-web/studyroom-cover-web.jpeg"
        alt="studyroom-cover-web"
      />

      <!-- 鏡子-web -->
      <button
        class="studyroom-preference-btns-web"
        @click="
          resetBookSelection();
          isPanelOpen = true;
          activeTab = 3;
        "
      >
        <img
          class="studyroom-preference-btn-web"
          src="../../assets/images/book-room-element/layout-web/studyroom-preference-btn-web.png"
          alt="studyroom-preference-btn-web"
        />
        <img
          class="studyroom-preference-btn-web studyroom-preference-btn-web--glow"
          src="../../assets/images/book-room-element/layout-web/studyroom-preference-btn-hover-web.png"
          alt=""
        />
        <div class="studyroom-preference-btn-nametag">
          <span>外觀設定</span>
        </div>
      </button>

      <!-- 人物-web -->
      <button
        class="studyroom-profile-btns-web"
        @click="
          resetBookSelection();
          isPanelOpen = true;
          activeTab = 1;
        "
      >
        <img
          class="studyroom-profile-btn-web"
          src="../../assets/images/book-room-element/layout-web/studyroom-profile-btn-web.png"
          alt="studyroom-profile-btn-web"
        />
        <img
          class="studyroom-profile-btn-web studyroom-profile-btn-web--glow"
          src="../../assets/images/book-room-element/layout-web/studyroom-profile-btn-hover-web.png"
          alt="studyroom-profile-btn-web--glow"
        />
        <div class="studyroom-profile-btn-nametag">
          <span>個人資訊</span>
        </div>
      </button>

      <!-- 書籍專區-web -->
      <button
        class="studyroom-bookarea-btns-web"
        @click="
          resetBookSelection();
          isPanelOpen = true;
          activeTab = 2;
        "
      >
        <img
          class="studyroom-bookarea-btn-web"
          src="../../assets/images/book-room-element/layout-web/studyroom-bookarea-btn-web.png"
          alt="studyroom-bookarea-btn-web"
        />
        <img
          class="studyroom-bookarea-btn-web studyroom-bookarea-btn-web--glow"
          src="../../assets/images/book-room-element/layout-web/studyroom-bookarea-btn-hover-web.png"
          alt="studyroom-bookarea-btn-web--glow"
        />
        <div class="studyroom-bookarea-btn-nametag">
          <span>書籍專區</span>
        </div>
      </button>
    </div>

    <!-- mobile版本 -->
    <div class="study-stage-mobile-mode">
      <!-- 背景-mobile -->
      <img
        class="studyroom-cover-mobile"
        src="../../assets/images/book-room-element/layout-mobile/studyroom-cover-mobile.jpeg"
        alt="studyroom-cover-mobile"
      />
      <button
        @click="
          resetBookSelection();
          isPanelOpen = true;
          activeTab = 2;
        "
      >
        <img
          class="studyroom-bookarea-btn-hover-mobile"
          src="../../assets/images/book-room-element/layout-mobile/studyroom-bookarea-btn-hover-mobile.png"
          alt="studyroom-bookarea-btn-hover-mobile"
        />
        <div class="studyroom-bookarea-btn-nametag-mb">
          <span>書籍專區</span>
        </div>
      </button>

      <button
        @click="
          resetBookSelection();
          isPanelOpen = true;
          activeTab = 3;
        "
      >
        <img
          class="studyroom-preference-btn-hover-mobile"
          src="../../assets/images/book-room-element/layout-mobile/studyroom-preference-btn-hover-mobile.png"
          alt="studyroom-preference-btn-hover-mobile"
        />
        <div class="studyroom-preference-btn-nametag-mb">
          <span>外觀設定</span>
        </div>
      </button>

      <button
        @click="
          resetBookSelection();
          isPanelOpen = true;
          activeTab = 1;
        "
      >
        <img
          class="studyroom-profile-btn-hover-mobile"
          src="../../assets/images/book-room-element/layout-mobile/studyroom-profile-btn-hover-mobile.png"
          alt="studyroom-profile-btn-hover-mobile"
        />
        <div class="studyroom-profile-btn-nametag-mb">
          <span>個人資訊</span>
        </div>
      </button>
    </div>
    <div class="study-stage-setting-panel" v-show="isPanelOpen">
      <div class="study-stage-setting-overlay" @click="closePanel"></div>

      <BookroomPanelWriteTable
        v-if="activeTab == 4 && isWritingReview"
        :book="bookStore.selectedBook"
        @back="
          isWritingReview = false;
          bookStore.selectedBook = null;
        "
        @publish="handlePublished"
        @close="closePanel"
      ></BookroomPanelWriteTable>

      <div v-else class="study-stage-setting-panel-inner">
        <button
          class="panel-close-btn"
          aria-label="關閉面板"
          @click="closePanel"
        >
          <AppIcon name="close" :size="20" />
        </button>
        <button
          class="prepare-write-review"
          :class="{
            'is-active': activeTab == 4,
            'has-selected-book':
              bookStore.selectedBook !== null && isWritingReview == false,
          }"
          @click="startWriteReview"
        ></button>
        <button
          class="prepare-write-review-mobile"
          :class="{
            'is-active': activeTab == 4,
            'has-selected-book':
              bookStore.selectedBook !== null && isWritingReview == false,
          }"
          @click="startWriteReview"
        ></button>
        <!-- 撰寫書籍的暗面 -->
        <div class="write-review-overlay" v-show="activeTab == 4"></div>
        <!-- 設定面板內容置放區 -->

        <div class="study-stage-setting-panel-content">
          <BookroomPanelAddDetailArea
            v-if="activeTab == 2 && bookStore.selectedBook"
            :book="bookStore.selectedBook"
            @back="bookStore.selectedBook = null"
            @goto-write="goToDraftDetail(bookStore.selectedBook)"
          >
          </BookroomPanelAddDetailArea>

          <BookroomPanelBbookArea
            v-else-if="activeTab == 2"
            @switch-tab="activeTab = $event"
            @select-book="bookStore.selectedBook = $event"
          ></BookroomPanelBbookArea>

          <BookroomPanelProfileArea
            v-else-if="activeTab == 1"
          ></BookroomPanelProfileArea>

          <BookroomPanelAppearanceArea v-else-if="activeTab == 3">
          </BookroomPanelAppearanceArea>

          <BookroomPanelReviewWriteArea
            v-else-if="activeTab == 4"
            @select-book="bookStore.selectedBook = $event"
          >
          </BookroomPanelReviewWriteArea>

          <BookroomPanelScriptArea
            v-else-if="activeTab == 5"
            @switch-tab="activeTab = $event"
            @edit-book="goToDraftDetail($event)"
          >
          </BookroomPanelScriptArea>

          <BookroomPanelAddArea
            v-else-if="activeTab == 6"
            @switch-tab="activeTab = $event"
          >
          </BookroomPanelAddArea>
        </div>
        <div class="studyroom-setting-panel-title">
          <span class="">我的書房</span>
        </div>
        <div class="study-stage-setting-panel-tabs">
          <button
            class="btn"
            v-for="tab in tabs"
            :key="tab.id"
            :class="{ tabActive: activeTab === tab.id }"
            @click="switchTab(tab.id)"
          >
            {{ tab.name }}
          </button>
        </div>
      </div>
    </div>

    <ReviewPublishedModal
      v-model="showPublishSuccess"
      :book="publishedBook"
    ></ReviewPublishedModal>
  </div>
</template>
<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.study-stage-container {
  position: relative;
  background-image: url("../../assets/images/book-room-element/bookroom-bg-blur.jpg");
  background-size: cover; // 改這裡
  background-repeat: no-repeat; // 一定要加，否則圖太小會重複貼滿
  background-position: center;
}

//書房設定按鈕：包一層跟卡片同尺寸的錨點，讓按鈕在 web/mobile 都能對齊卡片右上角
.study-stage-setting-btn-anchor {
  position: absolute;
  inset: 0;
  margin-inline: auto;
  max-width: 1200px;
  aspect-ratio: 1280 / 897;
  z-index: 15;
  pointer-events: none;
}

.study-stage-setting-btn {
  position: absolute;
  right: 20px;
  top: 20px;
  width: 70px;
  pointer-events: auto;
}
.study-stage-setting-btn::before {
  position: absolute;
  right: 70px;
  top: 20px;
  content: "書房設定";
  font-weight: $heading-weight;
  color: $neutral-100;
  white-space: nowrap;
}

.study-stage-setting-btn:hover {
  top: 21px;
  filter: brightness(80%);
}

// web版本
.study-stage-web-mode {
  position: relative;
  margin-inline: auto;
  max-width: 1200px;
  aspect-ratio: 1280 / 897;
  overflow: hidden;
}

.studyroom-cover-web {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

//--鏡子：外觀設定按鈕-web
.studyroom-preference-btns-web {
  position: absolute;
  top: 48%;
  left: 10%;
  width: 21%;
  aspect-ratio: 289 / 742; // 依你實際按鈕比例調整
  transform: translate(-50%, -50%);
}

.studyroom-preference-btn-web {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.studyroom-preference-btn-web--glow {
  opacity: 0; // 預設隱藏
  aspect-ratio: 289 / 742;
  transform: scale(1.15);
  object-fit: contain;
  transition: opacity 0.2s ease;
}

.studyroom-preference-btns-web:hover .studyroom-preference-btn-web--glow {
  opacity: 1;
}

.studyroom-preference-btns-web:hover .studyroom-preference-btn-nametag {
  top: 19%;
}

//外觀設定名條按鈕上的定位-web
.studyroom-preference-btn-nametag {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  font-size: $p-md-size;
  font-weight: $heading-weight;
  color: $brown;
  top: 18%;
  left: 80%;
  width: 100px; // 把這個數字調大，容器變大，裡面的圖也會跟著變大
  aspect-ratio: 2 / 1;
  background-image: url("../../assets/images/book-room-element/studyroom-btn-nametag.png");
  background-size: contain; // 改這裡
  background-repeat: no-repeat; // 一定要加，否則圖太小會重複貼滿
  background-position: center;
  transition: all 0.2s ease;
}

//--人物：個人資訊按鈕-web
.studyroom-profile-btns-web {
  position: absolute;
  top: 67%;
  left: 48%;
  width: 20%;
  aspect-ratio: 287 / 419; // 依你實際按鈕比例調整
  transform: translate(-50%, -50%);
}

.studyroom-profile-btn-web {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.studyroom-profile-btn-web--glow {
  opacity: 0; // 預設隱藏
  aspect-ratio: 287 / 419;
  transform: scale(1.23);
  object-fit: contain;
  transition: opacity 0.2s ease;
}

.studyroom-profile-btns-web:hover .studyroom-profile-btn-web--glow {
  opacity: 1;
}

.studyroom-profile-btns-web:hover .studyroom-profile-btn-nametag {
  top: 51%;
}

//個人資訊名條按鈕上的定位-web
.studyroom-profile-btn-nametag {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  font-size: $p-md-size;
  font-weight: $heading-weight;
  color: $brown;
  top: 50%;
  right: 90%;
  width: 100px; // 把這個數字調大，容器變大，裡面的圖也會跟著變大
  aspect-ratio: 2 / 1;
  background-image: url("../../assets/images/book-room-element/studyroom-btn-nametag.png");
  background-size: contain; // 改這裡
  background-repeat: no-repeat; // 一定要加，否則圖太小會重複貼滿
  background-position: center;
  transition: all 0.2s ease;
}

//--書籍專區按鈕-web
.studyroom-bookarea-btns-web {
  position: absolute;
  top: 54.3%;
  left: 74.7%;
  width: 20%;
  aspect-ratio: 279 / 183; // 依你實際按鈕比例調整
  transform: translate(-50%, -50%);
}

.studyroom-bookarea-btn-web {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.studyroom-bookarea-btn-web--glow {
  opacity: 0; // 預設隱藏
  transform: scale(1.23);
  aspect-ratio: 279 / 183;
  object-fit: contain;
  transition: opacity 0.2s ease;
}

.studyroom-bookarea-btns-web:hover .studyroom-bookarea-btn-web--glow {
  opacity: 1;
}

.studyroom-bookarea-btns-web:hover .studyroom-bookarea-btn-nametag {
  top: 11%;
}

//書籍專區名條按鈕上的定位-web
.studyroom-bookarea-btn-nametag {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  font-size: $p-md-size;
  font-weight: $heading-weight;
  color: $brown;
  top: 10%;
  left: 103%;
  width: 100px; // 把這個數字調大，容器變大，裡面的圖也會跟著變大
  aspect-ratio: 2 / 1;
  background-image: url("../../assets/images/book-room-element/studyroom-btn-nametag.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  transition: all 0.2s ease;
}

//mobile版本
.study-stage-mobile-mode {
  position: relative;
  width: 100%;
  max-width: 420px;
  aspect-ratio: 393 / 746;
  margin-inline: auto;
  display: none;
  overflow: hidden;
}

.study-stage-mobile-mode > img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

//--書籍專區按鈕-mobile
.studyroom-bookarea-btn-hover-mobile {
  position: absolute;
  top: 43.5%;
  left: 53%;
  width: 46%;
  aspect-ratio: 184 / 126; // 依你實際按鈕比例調整
}

//書籍專區名條按鈕上的定位-mobile
.studyroom-bookarea-btn-nametag-mb {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  font-size: $p-xs-size;
  font-weight: $heading-weight;
  color: $brown;
  top: 39%;
  left: 68%;
  width: 70px; // 把這個數字調大，容器變大，裡面的圖也會跟著變大
  aspect-ratio: 2 / 1;
  background-image: url("../../assets/images/book-room-element/studyroom-btn-nametag.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  transition: all 0.2s ease;
}

//--鏡子：外觀設定按鈕-mobile
.studyroom-preference-btn-hover-mobile {
  position: absolute;
  top: 20%;
  left: 0%;
  width: 30%;
  aspect-ratio: 117 / 389; // 依你實際按鈕比例調整
}

//外觀設定名條按鈕上的定位-mobile
.studyroom-preference-btn-nametag-mb {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  font-size: $p-xs-size;
  font-weight: $heading-weight;
  color: $brown;
  top: 30%;
  left: 20%;
  width: 70px; // 把這個數字調大，容器變大，裡面的圖也會跟著變大
  aspect-ratio: 2 / 1;
  background-image: url("../../assets/images/book-room-element/studyroom-btn-nametag.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  transition: all 0.2s ease;
}

//--人物：個人資訊按鈕-mobile
.studyroom-profile-btn-hover-mobile {
  position: absolute;
  top: 45.5%;
  left: 25%;
  width: 39%;
  aspect-ratio: 150 / 230; // 依你實際按鈕比例調整
}

//個人資訊名條按鈕上的定位-mobile
.studyroom-profile-btn-nametag-mb {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  font-size: $p-xs-size;
  font-weight: $heading-weight;
  color: $brown;
  top: 67%;
  left: 62%;
  width: 70px; // 把這個數字調大，容器變大，裡面的圖也會跟著變大
  aspect-ratio: 2 / 1;
  background-image: url("../../assets/images/book-room-element/studyroom-btn-nametag.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  transition: all 0.2s ease;
}

// ------設定面板------

.study-stage-setting-panel-inner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 80%;
  max-width: 1000px;
  aspect-ratio: 984 / 609;
  z-index: 15;
}

.panel-close-btn {
  position: absolute;
  top: 6%;
  right: 5%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $brown;
  z-index: 21;

  &:hover {
    opacity: 0.8;
  }
}

// 把面板圖案獨立成偽元素，讓 .prepare-write-review 能跟它比 z-index
.study-stage-setting-panel-inner::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image: url("../../assets/images/book-room-element/studyroom-btn-setting-panel.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  z-index: 1;
  pointer-events: none;
}

.study-stage-setting-overlay {
  position: absolute;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  z-index: 10;
}

.write-review-overlay {
  position: absolute;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.2);
  z-index: 12;
  -webkit-mask-image: url("../../assets/images/book-room-element/studyroom-btn-setting-panel.png");
  mask-image: url("../../assets/images/book-room-element/studyroom-btn-setting-panel.png");
  -webkit-mask-size: contain;
  mask-size: contain;
  -webkit-mask-repeat: no-repeat;
  mask-repeat: no-repeat;
  -webkit-mask-position: center;
  mask-position: center;
}

//選單我的書房展示區
.study-stage-setting-panel-content {
  position: absolute;
  overflow: hidden;
  top: 11%;
  right: 7%;
  width: 64%;
  height: 78%;
  z-index: 20;
}

//選單我的書房標題
.studyroom-setting-panel-title {
  display: flex;
  position: inherit;
  left: 7%;
  top: 10%;
  width: 20%;
  aspect-ratio: 214 / 75;
  justify-content: center;
  align-items: center;
  background-image: url("@/assets/images/book-room-element/studyroom-setting-panel-title.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  z-index: 20;

  & span {
    font-size: $p-md-size;
    font-weight: $heading-weight;
    color: $brown;
  }
}

.study-stage-setting-panel-tabs {
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 14%;
  margin: 16% 0 0 10%;
  z-index: 20;

  & button {
    aspect-ratio: 132 / 52;
    background-image: url("../../assets/images/book-room-element/studyroom_panel_list_btn.png");
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    font-weight: $text-weight;
    color: $neutral-800;
    white-space: nowrap;
  }

  & button:hover {
    transform: translateY(1px);
  }

  & .tabActive {
    background-image: url("../../assets/images/book-room-element/studyroom_panel_list_btn_active.png");
    color: $neutral-100;
    transform: scale(1.05);
  }
}
.prepare-write-review {
  position: absolute;
  top: 50%;
  right: 0;
  width: 60px;
  aspect-ratio: 70 / 246;
  background-repeat: no-repeat;
  background-size: contain;
  background-image: url(../../assets/button/prepare-write-review.png);
  transform: translateY(-50%);
  z-index: 0; // 低於 ::before 的面板圖案（z-index:1），藏在後面
  transition: transform 0.3s ease;
}

// 點到「撰寫心得」時，滑出到面板右側邊緣
.prepare-write-review.is-active {
  transform: translate(100%, -50%);
}

.prepare-write-review.is-active.has-selected-book {
  background-image: url(../../assets/button/ready-write-review.png);
}

// 手機版：從面板底部中間往上滑出的撰寫心得按鈕（桌機不顯示）
.prepare-write-review-mobile {
  display: none;
}

//RWD
@media (max-width: 960px) {
  .study-stage-setting-btn-anchor {
    max-width: 420px;
    aspect-ratio: 393 / 746;
  }

  .study-stage-web-mode {
    display: none;
  }

  .study-stage-mobile-mode {
    display: block;
  }

  .study-stage-setting-panel-inner {
    top: 55%;
    width: 90%;
    max-width: 393px;
    aspect-ratio: 358 / 600;

    &::before {
      background-image: url("../../assets/images/book-room-element/studyroom-btn-setting-panel-mobile.png");
    }

    .write-review-overlay {
      -webkit-mask-image: url("../../assets/images/book-room-element/studyroom-btn-setting-panel-mobile.png");
      mask-image: url("../../assets/images/book-room-element/studyroom-btn-setting-panel-mobile.png");
    }

    .studyroom-setting-panel-title {
      width: 40%;
      left: 30%;
      top: -3%;
    }

    .study-stage-setting-panel-tabs {
      flex-direction: row;
      margin: 12% 8% 0;
      width: fit-content;
      height: 4.5%;
      gap: 10px;
      font-size: $p-sm-size;
    }

    .study-stage-setting-panel-content {
      top: 14%;
      right: 9%;
      width: 83%;
      height: 78%;
    }

    .panel-close-btn {
      top: 2%;
      right: 4%;
      width: 20px;
      height: 20px;
      color: $neutral-100;
    }

    // 手機版改用底部滑出的按鈕，原本右滑的桌機按鈕隱藏
    .prepare-write-review {
      display: none;
    }

    .prepare-write-review-mobile {
      display: none;
      position: absolute;
      left: 50%;
      bottom: 16px;
      width: 55%;
      max-width: 220px;
      aspect-ratio: 246 / 70;
      background-repeat: no-repeat;
      background-size: contain;
      background-image: url(../../assets/images/book-room-element/btn-mb-bookwrite-select.png);
      transform: translate(-50%, 35%);
      z-index: 21;
    }

    // 點到「撰寫心得」時才顯現
    .prepare-write-review-mobile.is-active {
      display: block;
    }

    .prepare-write-review-mobile.is-active.has-selected-book {
      background-image: url(../../assets/images/book-room-element/btn-mb-bookwrite-ready.png);
    }
  }
}
</style>
