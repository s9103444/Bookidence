<script>
import { Carousel, Slide } from "vue3-carousel";
import "vue3-carousel/carousel.css";
import AppButton from "@/components/common/AppButton.vue";
import AppIcon from "@/components/common/AppIcon.vue";
import BookCategoryTag from "@/components/common/BookCategoryTag.vue";

export default {
  components: {
    AppButton,
    AppIcon,
    BookCategoryTag,
    Carousel,
    Slide,
  },
  data() {
    return {
      books: [
        {
          id: 1,
          cover: new URL("@/assets/images/peter-cover.png", import.meta.url)
            .href,
          title: "彼得原理",
          author: "勞倫斯·彼得",
          categories: ["商業財經", "領導管理"],
        },
        {
          id: 2,
          cover: new URL("@/assets/images/peter-cover.png", import.meta.url)
            .href,
          title: "彼得原理",
          author: "勞倫斯·彼得",
          categories: ["商業財經", "領導管理"],
        },
        {
          id: 3,
          cover: new URL("@/assets/images/peter-cover.png", import.meta.url)
            .href,
          title: "彼得原理",
          author: "勞倫斯·彼得",
          categories: ["商業財經", "領導管理"],
        },
        {
          id: 4,
          cover: new URL("@/assets/images/peter-cover.png", import.meta.url)
            .href,
          title: "彼得原理",
          author: "勞倫斯·彼得",
          categories: ["商業財經", "領導管理"],
        },
        {
          id: 5,
          cover: new URL("@/assets/images/peter-cover.png", import.meta.url)
            .href,
          title: "彼得原理",
          author: "勞倫斯·彼得",
          categories: ["商業財經", "領導管理"],
        },
        {
          id: 6,
          cover: new URL("@/assets/images/peter-cover.png", import.meta.url)
            .href,
          title: "彼得原理",
          author: "勞倫斯·彼得",
          categories: ["商業財經", "領導管理"],
        },
        {
          id: 7,
          cover: new URL("@/assets/images/peter-cover.png", import.meta.url)
            .href,
          title: "彼得原理",
          author: "勞倫斯·彼得",
          categories: ["商業財經", "領導管理"],
        },
        {
          id: 8,
          cover: new URL("@/assets/images/peter-cover.png", import.meta.url)
            .href,
          title: "彼得原理",
          author: "勞倫斯·彼得",
          categories: ["商業財經", "領導管理"],
        },
      ],
      breakpoints: {
        768: { itemsToShow: 2, itemsToScroll: 2 },
        1024: { itemsToShow: 3, itemsToScroll: 3 },
        1440: { itemsToShow: 4, itemsToScroll: 4 },
      },
    };
  },
  methods: {
    goPrev() {
      this.$refs.carouselRef.prev();
    },
    goNext() {
      this.$refs.carouselRef.next();
    },
  },
};
</script>

<template>
  <header class="kv-section-img">
    <div class="kv-section">
      <h3 class="kv-welcome">歡迎來到 Bookidence</h3>
      <h1 class="kv-title">讓你的閱讀，<br />住進有人陪的地方</h1>
      <div class="kv-button">
        <AppButton color="secondary" to="/front/guilds"
          >開始探索
          <AppIcon name="arrow-right" />
        </AppButton>
      </div>
    </div>
    <img
      src="/src/assets/images/home-element/flower.png"
      alt=""
      class="flower"
    />
    <img src="/src/assets/images/home-element/seed.png" alt="" class="seed" />
    <img
      src="/src/assets/images/home-element/book-boy.png"
      alt=""
      class="book-boy"
    />
    <img
      src="/src/assets/images/home-element/book-girl.png"
      alt=""
      class="book-girl"
    />
  </header>

  <!-- 1. 加上 container 啟用 12 欄 Grid -->
  <section class="intro-homeroom container">
    <!-- 左側內容區：佔 6 欄 -->
    <div class="col-6 content-homeroom-intro">
      <p class="tagline-homeroom-intro">搭建一座屬於你的閱讀小屋</p>
      <h3 class="title-homeroom-intro">
        每一次翻頁，<br />都為小屋砌上一片磚瓦
      </h3>
      <p class="desc-homeroom-intro">
        這不只是個冷冰冰的數位書棚，<br />而是你在 Bookidence
        小鎮裡親手搭建的精神樹屋。<br />讓閱讀累積的足跡，化作肉眼可見的溫暖裝飾。
      </p>
      <AppButton :to="{ name: 'study' }"
        >進入我的書房
        <AppIcon name="arrow-right" />
      </AppButton>
    </div>
    <div class="col-1"></div>
    <!-- 右側圖片區：佔 5 欄 -->

    <div class="col-5 img-homeroom-intro">
      <img
        src="@/assets/images/home-element/house.png"
        alt=""
        style="width: 100%"
      />
    </div>
  </section>

  <section class="container features-homeroom">
    <div class="col-4 card-features-homeroom">
      <div class="img-features-homeroom">
        <img
          src="@/assets/images/home-element/features-images-01.png"
          alt=""
          class="pixel-box1"
        />
      </div>
      <div class="intro-features-homeroom">
        <p class="title-features-homeroom">捏出你的小讀者</p>
        <p class="desc-features-homeroom">
          在鏡子前換上喜歡的髮型、膚色與瞳色， 打造專屬於你的像素小精靈。
          隨著閱讀經驗值慢慢累積， 還能解鎖獨特的成就徽章，
          讓你在小鎮裡閃閃發光！
        </p>
      </div>
    </div>

    <div class="col-4 card-features-homeroom">
      <div class="intro-features-homeroom">
        <p class="title-features-homeroom">會長大的藏書閣</p>
        <p class="desc-features-homeroom">
          提供「未閱讀、閱讀中、閱讀完畢」的貼心狀態篩選。最神奇的是，
          你的專屬書架外觀會隨著藏書量增加而變得越來越豐富，看著書房一點一滴變精緻，成就感滿滿！
        </p>
      </div>

      <div class="img-features-homeroom">
        <img
          src="@/assets/images/home-element/features-images-02.png"
          alt=""
          class="pixel-box"
        />
      </div>
    </div>

    <div class="col-4 card-features-homeroom">
      <div class="img-features-homeroom">
        <img
          src="@/assets/images/home-element/features-images-03.png"
          alt=""
          class="pixel-box"
        />
      </div>
      <div class="intro-features-homeroom">
        <p class="title-features-homeroom">溫柔的思緒避風港</p>
        <p class="desc-features-homeroom">
          靈感來敲門，卻還沒準備好公開？「心得草稿區」是你的私人日記本。先寫給自己看，等準備好了，再一鍵切換「公開/非公開」分享給小鎮上的居民，或是留作私密的思想寶藏。
        </p>
      </div>
    </div>
  </section>

  <div class="recommand-book-section">
    <div class="recommand-book-header">
      <h1 class="title-recommand-book">好書推薦</h1>
      <div class="carousel-nav">
        <button
          type="button"
          class="carousel-nav__btn"
          aria-label="上一頁"
          @click="goPrev"
        >
          <AppIcon name="chevron-left" :size="16" />
        </button>
        <button
          type="button"
          class="carousel-nav__btn"
          aria-label="下一頁"
          @click="goNext"
        >
          <AppIcon name="chevron-right" :size="16" />
        </button>
      </div>
    </div>

    <Carousel
      ref="carouselRef"
      :items-to-show="1"
      :items-to-scroll="1"
      :breakpoints="breakpoints"
      :gap="24"
      :wrap-around="true"
      snap-align="start"
      class="recommand-book"
    >
      <Slide v-for="book in books" :key="book.id">
        <div class="card-recommand-book">
          <img :src="book.cover" :alt="book.title" />
          <h3 class="bookname-recommand-book">{{ book.title }}</h3>
          <p class="author-recommand-book">{{ book.author }}</p>
          <div class="tag-space">
            <div class="BookCategoryTag-space">
              <BookCategoryTag
                v-for="cat in book.categories"
                :key="cat"
                class="book-category-tag"
                size="sm"
                color="primary"
                variant="outlined"
                radius="rounded"
                >{{ cat }}</BookCategoryTag
              >
            </div>
            <AppIcon name="arrow-right" class="arrow-right-color" :size="24" />
          </div>
        </div>
      </Slide>
    </Carousel>
  </div>

  <section class="hero-banner">
    <div class="container container-2">
      <!-- 左側：插圖區塊 -->
      <div class="col-5 hero-image-wrapper">
        <img
          src="@/assets/images/home-element/worry.png"
          alt="困惑的小巫師與書本"
          class="hero-img"
        />
      </div>

      <!-- 右側：文案內容區塊 -->
      <div class="col-7 hero-content">
        <h1 class="hero-title">買書很快，讀完好難？</h1>
        <p class="hero-text">
          找到讀同一本書的夥伴、把讀完的想法說給有共鳴的人聽，用一個屬於自己的書房，看見閱讀累積下來的樣子。
        </p>
      </div>
    </div>
  </section>

  <section class="container statistics">
    <div class="col-4 area-statistics">
      <h2 class="title-statistics">128</h2>
      <p class="desc-statistics">個讀書公會正在交流</p>
    </div>
    <div class="col-4 area-statistics">
      <h2 class="title-statistics">2,341</h2>
      <p class="desc-statistics">則心得已被分享</p>
    </div>
    <div class="col-4 area-statistics">
      <h2 class="title-statistics">856</h2>
      <p class="desc-statistics">本書被共讀完成</p>
    </div>
  </section>

  <section class="container reading-guild">
    <div class="col-5 img-reading-guild">
      <img
        src="@/assets/images/home-element/read-together.png"
        alt=""
        style="width: 100%"
      />
    </div>
    <div class="col-1"></div>
    <div class="col-6 content-homeroom-intro">
      <p class="tagline-homeroom-intro">尋找小鎮各處的讀書公會</p>
      <h3 class="title-homeroom-intro">探索散落在小鎮<br />各處的共讀能量！</h3>
      <p class="desc-homeroom-intro">
        不用露臉、不需即時發言。在討論區裡，用你最舒服的節奏與同好交流。」
        我們打破了實體讀書會「時間難喬、社交壓力大」的魔咒。在 Bookidence
        的公會小鎮裡，大家不用隨時在線，每個人都能用自己最溫柔、最無負擔的步調共讀。
      </p>
      <AppButton to="/front/guilds"
        >探索讀書公會
        <AppIcon name="arrow-right" />
      </AppButton>
    </div>
  </section>

  <section class="container features-guild">
    <div class="col-4 card-features-homeroom">
      <div class="intro-features-homeroom">
        <p class="title-features-homeroom">無壓力的討論留言區</p>
        <p class="desc-features-homeroom">
          純文字的非同步留言互動，讓你在閱讀完後慢慢整理思緒、寫下看法。就算出差、加班，也能在深夜隨時推開門留下你的足跡，不急不徐地參與交流。
        </p>
      </div>
      <div class="img-features-homeroom">
        <img
          src="@/assets/images/home-element/intro-image-03.png"
          alt=""
          class="pixel-box1"
        />
      </div>
    </div>

    <div class="col-4 card-features-homeroom">
      <div class="img-features-homeroom">
        <img
          src="@/assets/images/home-element/intro-image-01.png"
          alt=""
          class="pixel-box"
        />
      </div>
      <div class="intro-features-homeroom">
        <p class="title-features-homeroom">挑選理想的公會</p>
        <p class="desc-features-homeroom">
          加入前，先看看每個公會的「公會卡片」！不管是線上聊還是實體聚、精讀還是輕鬆分享、進度快還是慢，點開簡介與進度排程一目了然，輕鬆避開頻率不合的圈子。
        </p>
      </div>
    </div>

    <div class="col-4 card-features-homeroom">
      <div class="intro-features-homeroom">
        <p class="title-features-homeroom">有秩序的共讀與實體聚會</p>
        <p class="desc-features-homeroom">
          公會內除了擁有「排程留言討論區」來專注探討當前進度外，也支援會長創立線上會議連結或實體讀書會活動。不管你是想要安靜地看著留言陪伴，還是挑選適合的日子與居民見面，這裡都有屬於你的位置。
        </p>
      </div>
      <div class="img-features-homeroom">
        <img
          src="@/assets/images/home-element/intro-image-04.png"
          alt=""
          class="pixel-box"
        />
      </div>
    </div>
  </section>

  <section class="container book-wish-pool">
    <div class="col-1"></div>
    <div class="col-4">
      <p class="tagline-book-wish-pool">公會導航與圖書許願池</p>
      <h3 class="title-book-wish-pool">
        尋找一本書<br />找一盞在夜裡<br />
        <span>為你亮起的燈。</span>
      </h3>
      <p class="desc-book-wish-pool">
        連結書籍、公會與居民的溫馨導航，讓知識與陪伴不再迷路。
      </p>
    </div>
  </section>

  <section class="container feature-book-wish-pool">
    <div class="col-1"></div>
    <div class="col-5 content-feature-book-wish-pool">
      <div class="img-feature-book-wish-pool">
        <img src="@/assets/images/home-element/intro-image-05.png" alt="" />
      </div>
      <div class="text-feature-book-wish-pool">
        <p class="title-feature-book-wish-pool">五合一公會的搜查鏡</p>
        <p class="desc-feature-book-wish-pool">
          整合「讀書公會、書籍名稱、用戶名稱、書籍作者、讀書會地點」五種搜尋維度。無論你想找一個特定的溫馨公會，還是想探索身邊的實體聚會，一搜即達。
        </p>
      </div>
    </div>

    <div class="col-5 content-feature-book-wish-pool">
      <div class="img-feature-book-wish-pool">
        <img src="@/assets/images/home-element/intro-image-02.png" alt="" />
      </div>
      <div class="text-feature-book-wish-pool">
        <p class="title-feature-book-wish-pool">讀書人的許願信封</p>
        <p class="desc-feature-book-wish-pool">
          只要寄出「推薦書籍申請」，管理員審核上架後，系統將第一時間用小鈴鐺和信箱通知你，讓你的小屋隨時保持書香四溢！
        </p>
      </div>
    </div>
    <div class="col-1"></div>
  </section>

  <section class="container intro-read-together">
    <div class="col-1"></div>
    <div class="col-3 content-read-together">
      <p class="title-read-together">一個人讀，<br />也可以不孤單。</p>
      <p class="desc-read-together">
        Bookidence
        陪你找到願意一起翻開同一本書的人，把讀完的感動說說訴說給懂的人聽。
      </p>
      <AppButton to="/front/guilds"
        >開啟我的共讀旅程
        <AppIcon name="arrow-right" />
      </AppButton>
    </div>
    <div class="col-7 img-read-together">
      <img src="/src/assets/images/home-element/home-banner-02.png" alt="" />
    </div>
    <div class="col-1"></div>
  </section>
</template>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.kv-section-img {
  background-image: url("@/assets/images/home-element/morning-1.png");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center center;
  min-height: 700px;
  position: relative;
}

.flower,
.book-boy,
.book-girl,
.seed {
  position: absolute;
  z-index: 40;
  --panel-scale: 1;
  transform: scale(var(--panel-scale));
}

.flower {
  bottom: -30px;
  left: 4%;
}

.book-boy {
  bottom: -80px;
  left: 8%;
}

.book-girl {
  bottom: -70px;
  right: 8%;
}

.seed {
  bottom: -33px;
  right: 1%;
  // transform: translateY(20px);
}

@keyframes slide-in-left {
  from {
    opacity: 0;
    transform: translateX(-60px) scale(var(--panel-scale));
  }
  to {
    opacity: 1;
    transform: translateX(0) scale(var(--panel-scale));
  }
}

@keyframes slide-in-right {
  from {
    opacity: 0;
    transform: translateX(60px) scale(var(--panel-scale));
  }
  to {
    opacity: 1;
    transform: translateX(0) scale(var(--panel-scale));
  }
}

.flower,
.book-boy {
  animation: slide-in-left 0.8s ease-out;
}

.book-girl,
.seed {
  animation: slide-in-right 0.8s ease-out;
}

@media (max-width: $breakpoint-desktop) {
  .flower,
  .book-boy,
  .book-girl,
  .seed {
    --panel-scale: 0.8;
    transform: scale(var(--panel-scale));
  }
}

@media (max-width: $breakpoint-mobile) {
  .flower,
  .book-boy,
  .book-girl,
  .seed {
    --panel-scale: 0.7;
    transform: scale(var(--panel-scale));
  }
  .book-boy {
    left: -1%;
  }
  .flower {
    left: -1%;
  }
  .book-girl {
    right: 2%;
  }
  .seed {
    right: -3%;
  }
}

.kv-section {
  margin: 0 auto;
}

.kv-welcome {
  text-align: center;
  justify-content: center;
  color: $primary;
  font-size: $p-md-size;
  margin-bottom: $spacing-md;
  padding-top: $spacing-xl;
}

.kv-title {
  text-align: center;
  justify-content: center;
  font-size: $h1-size;
  color: $primary;
  text-align: center;
  line-height: $heading-line-height;
  margin-bottom: $spacing-xl;
  font-weight: $heading-weight;
}

.kv-button {
  display: inline-block;
  display: flex;
  justify-content: center;
}

.intro-homeroom {
  align-items: stretch; //  讓所有 col 等高
  background-image: url("@/assets/images/home-element/light-green-pixel.png");
  background-repeat: no-repeat;
  background-size: cover;
  min-height: 400px;
  margin-top: 120px;
  margin-bottom: 120px;
}

.content-homeroom-intro {
  @media (max-width: $breakpoint-desktop) {
    grid-column: span 12;
  }
}

.tagline-homeroom-intro {
  font-size: $h6-size;
  color: $primary;
  font-weight: $heading-weight;
  margin-bottom: $spacing-lg;
}

.title-homeroom-intro {
  font-size: $h1-size;
  color: $neutral-800;
  font-weight: $heading-weight;
  margin-bottom: 64px;

  @media (max-width: $breakpoint-desktop) {
    margin-bottom: 40px;
  }
}

.desc-homeroom-intro {
  font-size: $p-md-size;
  margin-bottom: 64px;
}

.img-homeroom-intro {
  padding-top: 60px;

  @media (max-width: $breakpoint-desktop) {
    grid-column: span 12;
    font-size: $h3-size;
  }
}

.features-homeroom {
  background-color: $neutral-100;
  align-items: stretch;
  min-height: 500px;
  gap: 80px;

  @media (max-width: $breakpoint-desktop) {
    display: flex;
    overflow-x: auto;
    gap: $spacing-md;
    scroll-snap-type: x mandatory; // 允許橫向捲動吸附
    -webkit-overflow-scrolling: touch; // iOS 慣性滑動更順
    scroll-padding-left: $grid-margin;

    &::-webkit-scrollbar {
      display: none; // 隱藏預設醜醜的捲軸（Chrome/Safari）
    }

    scrollbar-width: none; // 隱藏預設捲軸（Firefox）
  }
}

.card-features-homeroom {
  display: flex;
  flex-direction: column;
  text-align: center;
  gap: 16px;
  min-height: 100%; // 卡片撐滿父容器

  @media (max-width: $breakpoint-desktop) {
    flex: 0 0 280px; // 固定寬度，不會被壓縮，卡片才能一張一張橫向排開
    scroll-snap-align: start; // 每張卡片都是一個吸附點
  }
}

.img-features-homeroom {
  --step: 4px;
  flex: 1; //  圖片區域自動伸縮，撐滿可用空間
  background: $secondary-300;

  display: flex;
  align-items: center;
  justify-content: center;

  clip-path: polygon(
    var(--step) 0,
    calc(100% - var(--step)) 0,
    calc(100% - var(--step)) var(--step),
    100% var(--step),
    100% calc(100% - var(--step)),
    calc(100% - var(--step)) calc(100% - var(--step)),
    calc(100% - var(--step)) 100%,
    var(--step) 100%,
    var(--step) calc(100% - var(--step)),
    0 calc(100% - var(--step)),
    0 var(--step),
    var(--step) var(--step)
  );
  position: relative;
  width: 100%;
  height: 100%;
  aspect-ratio: 1/1;
  padding: auto;
}

.pixel-box1 {
  position: absolute;
  object-fit: contain;
  width: 100%;
}

.pixel-box {
  position: absolute;
  width: 100%;
}

.title-features-homeroom {
  color: $primary;
  font-size: $h3-size;
  margin-bottom: $spacing-lg;
  font-weight: $heading-weight;
}

.desc-features-homeroom {
  font-size: $p-sm-size;
}

.intro-features-homeroom {
  padding: $spacing-md;
  display: flex;
  flex-direction: column;
  justify-content: flex-end; // 文字靠下，空間留給圖片
  margin-bottom: $spacing-lg;
}

.recommand-book-section {
  padding-left: $grid-margin;
  padding-right: $grid-margin;
  margin-top: 60px;

  @media (max-width: $breakpoint-tablet) {
    padding-left: 32px;
    padding-right: 32px;
  }

  @media (max-width: $breakpoint-mobile) {
    padding-left: 16px;
    padding-right: 16px;
  }
}

.recommand-book-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.carousel-nav {
  display: flex;
  gap: 14px;
}

.carousel-nav__btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 50px;
  height: 50px;
  border: 1px solid $primary;
  border-radius: $btn-radius-rnd;
  background-color: transparent;
  color: $primary;
  cursor: pointer;
}

.title-recommand-book {
  margin-bottom: 40px;
}

.recommand-book {
  background-color: $neutral-100;
  display: flex;
  padding-bottom: 60px;
}

.arrow-right-color {
  color: $primary;
}

.BookCategoryTag-space {
  display: flex;

  gap: 8px;
}

.tag-space {
  display: flex;
  justify-content: space-between;
}

.card-recommand-book {
  background: $secondary-300;
  //  width: 400px;
  border-radius: 20px;
  padding-inline: $spacing-xl;
  position: relative;
  padding-bottom: $spacing-xl;
}

.card-recommand-book img {
  position: relative;
  margin-top: -20px;
}

.bookname-recommand-book {
  font-size: $h5-size;
}

.author-recommand-book {
  color: $primary;
  font-size: $label-md-size;
}

/* 外層容器（確保整體上下留白） */
.hero-banner {
  margin-bottom: 60px;
}

.container-2 {
  // max-width: 1200px;
  margin: 0 auto;
  background-image: url(@/assets/images/home-element/gr-bg.png);
  background-position: top right;
  background-repeat: no-repeat;
  background-size: cover;
  position: relative;
  align-items: center;
  padding: 40px 20px;
}

/* 左側圖片區塊：利用負邊界讓圖片上下溢出 */
.hero-image-wrapper {
  grid-column: span 5;
  margin-top: -80px;
  /* 向上凸出深綠色框 */
  margin-bottom: -82px;
  /* 向下凸出深綠色框 */
  position: relative;
  z-index: 2;
  /* 確保圖片壓在背景上方 */

  @media (max-width: $breakpoint-tablet) {
    grid-column: span 12;
  }
}

.hero-img {
  width: 100%;
  height: auto;
  display: block;

  @media (max-width: $breakpoint-tablet) {
    display: none;
  }
}

/* 右側文案區塊 */
.hero-content {
  color: #ffffff;

  @media (max-width: $breakpoint-tablet) {
    grid-column: span 12;
  }
}

.hero-title {
  font-size: 2.25rem;
  font-weight: bold;
  margin-bottom: 1rem;
  letter-spacing: 2px;
}

.hero-text {
  font-size: 0.95rem;
  line-height: 1.8;
  opacity: 0.9;
}

.title-statistics {
  text-align: center;
  font-size: 96px;
  color: $primary-500;
}

.desc-statistics {
  text-align: center;
  font-size: $h6-size;
  color: $primary;
}

.reading-guild {
  margin-block: 60px;
  align-items: stretch;
}

.img-reading-guild {
  margin: auto auto;

  @media (max-width: $breakpoint-desktop) {
    grid-column: span 12;
    margin: 0 auto $spacing-md;
  }
}

.features-guild {
  background-color: $neutral-100;
  align-items: stretch;
  min-height: 500px;
  gap: 80px;
  margin-bottom: 60px;

  @media (max-width: $breakpoint-desktop) {
    display: flex;
    overflow-x: auto;
    gap: $spacing-md;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scroll-padding-left: $grid-margin;

    &::-webkit-scrollbar {
      display: none;
    }

    scrollbar-width: none;
  }
}

.book-wish-pool {
  padding-block: 96px;
  background-image: url(@/assets/images/home-element/home-banner-01.png);
  background-position: center center;
  background-repeat: no-repeat;
  background-size: cover;
  min-height: 500px;
}

.tagline-book-wish-pool {
  color: $neutral-100;
  font-size: $h6-size;
  padding-bottom: $spacing-xl;
  font-weight: $heading-weight;
}

.title-book-wish-pool {
  color: $neutral-100;
  font-size: $h2-size;
}

.title-book-wish-pool span {
  color: $secondary;
  font-size: $h2-size;
}

.desc-book-wish-pool {
  color: $neutral-100;
  font-size: $p-md-size;
}

.content-feature-book-wish-pool {
  display: flex;

  @media (max-width: $breakpoint-desktop) {
    grid-column: span 12;
  }
}

.title-feature-book-wish-pool {
  font-size: $h4-size;
  color: $primary;
  font-weight: $heading-weight;
}

.feature-book-wish-pool {
  align-items: stretch;
}

.img-feature-book-wish-pool {
  margin: auto auto;
}

.img-feature-book-wish-pool img {
  width: 240px;
  height: 240px;
  object-fit: contain;
}

// intro-image-05（放大鏡）圖檔本身留白比較多，視覺上比信封小一圈，直接放大實際尺寸湊視覺一致
.img-feature-book-wish-pool img[src*="intro-image-05"] {
  width: 312px;
  height: 312px;
}

.text-feature-book-wish-pool {
  margin: auto auto;
}

.title-read-together {
  font-size: $h2-size;
  font-weight: $heading-weight;
  padding-bottom: $spacing-lg;
}

.desc-read-together {
  font-size: $p-md-size;
  padding-bottom: 60px;
}

.intro-read-together {
  margin-block: 60px;
  align-items: stretch;
}

.img-read-together {
  margin: auto auto;

  @media (max-width: $breakpoint-desktop) {
    grid-column: span 12;
  }
}

.area-statistics {
  @media (max-width: $breakpoint-desktop) {
    grid-column: span 12;
  }
}

.content-read-together {
  @media (max-width: $breakpoint-desktop) {
    grid-column: span 12;
  }
}

.book-wish-pool .col-4 {
  @media (max-width: $breakpoint-desktop) {
    grid-column: span 12;
  }
}
</style>
