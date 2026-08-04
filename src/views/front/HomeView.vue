<script setup>
import AppButton from "@/components/common/AppButton.vue";
import AppIcon from "@/components/common/AppIcon.vue";
</script>

<template>
  <header class="kv-section-img">
    <div class="kv-section">
      <h3 class="kv-welcome">歡迎來到 Bookidence</h3>
      <h1 class="kv-title">讓你的閱讀，<br />住進有人陪的地方</h1>
      <div class="kv-button">
        <AppButton color="secondary"
          >開始探索
          <AppIcon name="arrow-right" />
        </AppButton>
      </div>
    </div>
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
      <AppButton
        >進入我的書房
        <AppIcon name="arrow-right" />
      </AppButton>
    </div>
    <!-- 右側圖片區：佔 6 欄 -->
    <div class="col-6 img-homeroom-intro">
      <img
        src="/src/assets/images/home-element/house.png"
        alt=""
        style="width: 100%"
      />
    </div>
  </section>

  <section class="container features-homeroom">
    <div class="col-4 img-features-homeroom">
      <img
        src="/src/assets/images/home-element/boy-black-yellow.png"
        alt=""
        class="pixel-box"
      />
    </div>
    <div class="col-4 intro-features-homeroom">
      <p class="title-features-homeroom">捏出你的小讀者</p>
      <p class="desc-features-homeroom">
        在鏡子前換上喜歡的髮型、膚色與瞳色， 打造專屬於你的像素小精靈。
        隨著閱讀經驗值慢慢累積， 還能解鎖獨特的成就徽章， 讓你在小鎮裡閃閃發光！
      </p>
    </div>
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
  align-items: center; // 讓 6 欄與 6 欄之間的內容垂直居中
  background-image: url("@/assets/images/home-element/light-green-pixel.png");
  background-repeat: no-repeat;
  background-size: cover;
}

.content-homeroom-intro {
  padding-top: 120px;
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
}

.desc-homeroom-intro {
  font-size: $p-md-size;
  margin-bottom: 64px;
}

.img-homeroom-intro {
  padding-top: 120px;
  display: flex;
  justify-content: center;
}

.features-homeroom {
  background-color: $neutral-100;
  display: flex;
  flex-direction: column;
}

.img-features-homeroom {
  --step: 4px;
  width: 200px;
  height: 180px;
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
}

.pixel-box img {
  position: absolute;
  max-width: 40%;
  max-height: 80%;
  object-fit: contain;
  top: 10px;
}

.title-features-homeroom {
  color: $primary;
  font-size: $h3-size;
  margin-bottom: $spacing-lg;
}

.desc-features-homeroom {
  font-size: $p-sm-size;
}
</style>
