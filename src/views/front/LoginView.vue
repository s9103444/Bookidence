<script>
import { IconMail, IconLock, IconEye, IconEyeClosed } from '@tabler/icons-vue';

export default {
  components: { IconMail, IconLock, IconEye, IconEyeClosed },
  data() {
    return {
      showPassword: false
    };
  },
  methods: {
    togglePassword() {
      this.showPassword = !this.showPassword;   // 這裡要填「現在的 showPassword 反過來」
    }
  }
};
</script>

<template>
  <!-- nav -->
<header class="site-header">
  <a href="/" class="site-header__logo-link">
    <img src="@/assets/logo/Bookidence_logo_primary.png" alt="LOGO" class="site-header__logo">
  </a>
  <div class="site-header__cta-group">
    <p class="site-header__hint">尚未成為會員?</p>
    <a href="" class="site-header__link">註冊</a>
  </div>
</header>
<!-- 大框框 -->
<section class="auth-card">
  <div class="auth-card__illustration">
    <img src="@/assets/images/book_and_quill.png" alt="" class="auth-card__illustration-img">
  </div>
  <form action="" class="auth-card__form">
    <h1 class="auth-card__title">登入</h1>

    <label for="email" class="auth-card__label">E-mail</label>
    <div class="auth-card__input-wrapper">
      <IconMail class="auth-card__input-icon" />
      <input type="email" id="email" placeholder="請輸入E-mail" class="auth-card__input">
    </div>

    <label for="pwd" class="auth-card__label">密碼</label>
    <div class="auth-card__input-wrapper">
      <IconLock class="auth-card__input-icon" />
      <input :type="showPassword ? 'text' : 'password'" id="pwd" placeholder="請輸入密碼" class="auth-card__input">
      <IconEye v-if="showPassword" class="auth-card__input-toggle" @click="togglePassword" />
      <IconEyeClosed v-else class="auth-card__input-toggle" @click="togglePassword" />
    </div>

    <a href="#" class="auth-card__forgot">忘記密碼?</a>
    <button type="submit" class="auth-card__submit">登入</button>
  </form>
</section>
</template>



<style lang="scss">
  @use '@/assets/scss/abstracts/variables' as *;
  .site-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: $spacing-md $spacing-xl;
    border-bottom: 1px solid $neutral-300;
    height: $header-height;

    &__logo-link {
      height: $spacing-xl;
    }

    &__logo {
      height: $spacing-xl;
      width: auto;
    }

    &__cta-group{
      display: flex;
      gap: $spacing-xs;
    }

    &__hint {
      color: $neutral-600;
      font-size: $p-sm-size;
    }

    &__link {
      color: $primary-500;
      font-size: $p-sm-size;
      text-decoration: underline;
  }

  }
  .auth-card {
    display: flex;
    gap: $spacing-xl;
    padding: $spacing-xl;
    background-color: $neutral-200;

    &__illustration {
      flex: 4;
      display: flex;
      align-items: center;
      justify-content: center;
      max-width: 100%;
      overflow: hidden;
    }

    &__illustration-img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      object-position: center;
    }

    &__form {
      flex: 5;
      display: flex;
      flex-direction: column;
      gap: $spacing-xs;
      justify-content: center;
      padding: 0 $spacing-xl;
    }

    &__title {
      font-size: $h4-size;
      font-weight: $heading-weight;
      color: $neutral-800;
      margin-bottom: $spacing-md;
      margin-inline: auto;
    }

    &__label {
      font-size: $label-md-size;
      color: $neutral-600;
    }

    &__input {
      font-size: $p-md-size;
      padding: $spacing-sm $spacing-xl;
      border: 1px solid $neutral-400;
      border-radius: $spacing-xs;
      width: 100%;

      &:focus{
        border-color: $primary;
        outline: none;
        box-shadow: 0 0 0 3px rgba($primary, 0.2);
      }
    }

    &__forgot{
      align-self: flex-end;

      &:hover{
        color: $primary-500;
      }
    }

    &__submit{
      background-color: $primary;
      color: $neutral-100;
      width: 100%;
      padding: $spacing-sm;
      border-radius: $spacing-sm;
      border: none;
      cursor: pointer;

      &:hover{
        background-color: $primary-500;
      }
    }

    &__input-wrapper {
      position: relative;
      margin-bottom: $spacing-sm;
    }

    &__input-icon {
      position: absolute;
      display: block;
      left: $spacing-sm;
      top: 50%;
      transform: translateY(-50%);
      color: $neutral-400;
      width: 20px;
      height: 20px;
    }

    &__input-toggle{
      position: absolute;
      display: block;
      right: $spacing-sm;
      top: 50%;
      transform: translateY(-50%);
      color: $neutral-400;
      width: 20px;
      height: 20px;
      cursor: pointer;
    }

    @media (max-width: $breakpoint-tablet) {
      flex-direction: column;
      padding: $spacing-md;

      &__illustration{
        display: none;
      }
    }
  }
</style>