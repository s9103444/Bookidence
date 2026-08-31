<script>
// 入口選擇頁:npm run dev 打開後看到的第一頁。
// 左邊「客戶端模式」導去前台首頁,右邊「管理員模式」導去後台登入頁。
// 這頁刻意不套 FrontLayout / AdminLayout,畫面外框全部自己寫。
import AppIcon from "@/components/common/AppIcon.vue";

export default {
  name: "EntrySelectView",
  components: {
    AppIcon
  }
};
</script>

<template>
  <div class="select-mode">
    <header class="select-mode__header">
      <img
        src="@/assets/logo/Bookidence_logo_primary.png"
        alt="Bookidence"
        class="select-mode__logo"
      />
    </header>

    <main class="select-mode__main">
      <router-link :to="{ name: 'home' }" class="mode-card">
        <img
          src="@/assets/images/book_and_quill.png"
          alt=""
          class="mode-card__art"
        />
        <span class="mode-card__label">
          客戶端模式
          <AppIcon name="chevron-right" :size="16" />
        </span>
      </router-link>

      <router-link :to="{ name: 'admin-login' }" class="mode-card">
        <img
          src="@/assets/images/recommend-book.png"
          alt=""
          class="mode-card__art"
        />
        <span class="mode-card__label">
          管理員模式
          <AppIcon name="chevron-right" :size="16" />
        </span>
      </router-link>
    </main>

    <footer class="select-mode__footer">
      <p>© 2026 Bookidence. All rights reserved.</p>
      <p class="select-mode__legal">
        本網站為緯育TibaMe_前端工程師班第100期學員專題成果作品,本平台僅供學習、展示之用。若有抵觸有關著作權,或有第三人主張侵害智慧財產權等情事,均由學員負法律上責任。若有侵權疑慮,您可以私訊<a href="https://www.facebook.com/TibaMe" target="_blank" rel="noopener noreferrer">緯育TibaMe</a>,後續會由專人協助處理。
      </p>
    </footer>
  </div>
</template>

<style scoped lang="scss">
@use "../assets/scss/abstracts/variables" as *;

.select-mode {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background: $neutral-100;
}

.select-mode__header {
  padding: $spacing-lg;
}

.select-mode__logo {
  width: 120px;
  display: block;
}

.select-mode__main {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: $spacing-xl;
  padding: $spacing-xl $spacing-lg;
}

.mode-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: $spacing-lg;
  text-decoration: none;
  color: $primary;
  cursor: pointer;
}

.mode-card__art {
  width: 220px;
  height: 220px;
  object-fit: contain;
  transition: transform 0.15s ease-out;
}

.mode-card__label {
  display: flex;
  align-items: center;
  gap: $spacing-xs;
  font-weight: $heading-weight;
  font-size: $label-lg-size;
  letter-spacing: $letter-spacing-base;
}

@media (hover: hover) {
  .mode-card:hover .mode-card__art {
    transform: translateY(-4px);
  }
}

.select-mode__footer {
  background: $primary;
  color: $neutral-100;
  padding: $spacing-md $spacing-lg;
  text-align: left;
  font-size: $p-xs-size;
  opacity: 0.85;
}

.select-mode__legal {
  margin-top: $spacing-sm;
  line-height: 1.8;

  a {
    color: $neutral-100;
    text-decoration: underline;

    &:hover {
      opacity: 1;
    }
  }
}
</style>
