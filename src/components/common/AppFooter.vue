<script setup>
// Footer 的 Mockup 你們還在細化，這裡先把「版位」搭出來：
// 左上 Logo、右側連結兩排、社群 icon 三顆、分隔線、底部兩塊備註文字
// 之後 Mockup 定案，主要改的地方是最下面那兩行 <p>，換成正式文字就好
</script>

<template>
  <footer class="app-footer">
    <div class="app-footer__top">
      <router-link to="/" class="app-footer__logo">
        <img src="@/assets/logo/Bookidence_wide_logo.png" alt="Bookidence" />
      </router-link>

      <nav class="app-footer__links">
        <router-link to="/study">我的專屬書房</router-link>
        <router-link to="/guilds">瀏覽讀書公會</router-link>
        <router-link to="/search">搜索圖書</router-link>
        <router-link to="/settings">使用者設定</router-link>
      </nav>
    </div>

    <div class="app-footer__social">
      <!-- 之後這三顆換成 IG / FB / Threads 之類的連結 icon -->
      <a href="#" class="social-dot" aria-label="社群連結 1"></a>
      <a href="#" class="social-dot" aria-label="社群連結 2"></a>
      <a href="#" class="social-dot" aria-label="社群連結 3"></a>
    </div>

    <hr class="app-footer__divider" />

    <div class="app-footer__bottom">
      <!-- TODO：Mockup 細化後替換成正式文字 -->
      <p>© 2026 Bookidence. All rights reserved.</p>
      <p>台灣・全方位共讀平台</p>
    </div>
  </footer>
</template>

<style scoped lang="scss">
@use "../../assets/scss/abstracts/variables" as *;

.app-footer {
  background: $primary;
  color: $neutral-100;
  padding: $spacing-xl $spacing-lg $spacing-lg;
}

.app-footer__top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: $spacing-md;
  margin-bottom: $spacing-lg;
}

.app-footer__logo {
  width: 140px;
}

.app-footer__links {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: $spacing-lg;
  font-size: 13px;

  a {
    color: $neutral-100;
    opacity: 0.85;

    &:hover {
      opacity: 1;
    }
  }

  // 「客服留言管理」在線框稿裡自成一排、位置偏右下，先用 margin 對齊
  &--sub {
    flex-basis: 100%;
    text-align: right;
  }
}

.app-footer__social {
  display: flex;
  gap: $spacing-sm;
  margin-bottom: $spacing-lg;
}

.social-dot {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.15);

  &:hover {
    background: rgba(255, 255, 255, 0.3);
  }
}

.app-footer__divider {
  border: none;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  margin-bottom: $spacing-md;
}

.app-footer__bottom {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: $spacing-sm;
  font-size: 12px;
  opacity: 0.7;
}
</style>
