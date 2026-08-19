<script>
import AppIcon from '../../components/common/AppIcon.vue';
import AppButton from '../../components/common/AppButton.vue';
import { mapActions } from 'pinia';
import { useUserStore } from '@/stores/user';
import { API_BASE } from '@/common/api';

export default {
  components: { AppIcon, AppButton },
  data() {
    return {
      showPassword: false,
      email: '',
      password: ''
    };
  },
  methods: {
    ...mapActions(useUserStore, ['login']),
    togglePassword() {
      this.showPassword = !this.showPassword;   // 這裡要填「現在的 showPassword 反過來」
    },
    async handleLogin() {
      if (!this.email || !this.password) return;

      const res = await fetch(`${API_BASE}/login.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: this.email, password: this.password })
      });
      const result = await res.json();

      if (!result.success) {
        console.error(result.message);
        return;
      }

      this.login({
        token: result.token,
        userId: result.user.user_id,
        userName: result.user.nickname,
        avatarUrl: '',
        xp: result.user.total_exp
      });
      this.$router.push({ name: 'home' });
    }
  }
};
</script>

<template>
  <div class="login-page">
    <!-- nav -->
    <header class="site-header">
      <router-link :to="{ name: 'home' }" class="site-header__logo-link">
        <img src="@/assets/logo/Bookidence_logo_primary.png" alt="LOGO" class="site-header__logo">
      </router-link>
      <div class="site-header__cta-group">
        <p class="site-header__hint">尚未成為會員?</p>
        <router-link :to="{ name: 'register' }" class="site-header__link">註冊</router-link>
      </div>
    </header>
    <!-- 大框框 -->
    <section class="auth-card">
      <div class="auth-card__illustration">
        <img src="@/assets/images/book_and_quill.png" alt="" class="auth-card__illustration-img">
      </div>
      <form class="auth-card__form" @submit.prevent="handleLogin">
        <h1 class="auth-card__title">登入</h1>
    
        <label for="email" class="auth-card__label">E-mail</label>
        <div class="auth-card__input-wrapper">
          <AppIcon name="mail" :size="20" class="auth-card__input-icon" />
          <input type="email" id="email" v-model="email" placeholder="請輸入E-mail" class="auth-card__input">
        </div>

        <label for="pwd" class="auth-card__label">密碼</label>
        <div class="auth-card__input-wrapper">
          <AppIcon name="lock" :size="20" class="auth-card__input-icon" />
          <input :type="showPassword ? 'text' : 'password'" id="pwd" v-model="password" placeholder="請輸入密碼" class="auth-card__input">
          <AppIcon v-if="showPassword" name="eye" :size="20" class="auth-card__input-toggle" @click="togglePassword" />
          <AppIcon v-else name="eye-off" :size="20" class="auth-card__input-toggle" @click="togglePassword" />
        </div>
    
        <a href="#" class="auth-card__forgot">忘記密碼?</a>
        <AppButton type="submit" class="auth-card__submit">登入</AppButton>
      </form>
    </section>
  </div>
</template>



<style lang="scss">
  @use '@/assets/scss/abstracts/variables' as *;
  @use '@/assets/scss/abstracts/mixins' as *;
  .login-page{
    min-height: 100vh;
    background-color: $neutral-200;
  }
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
    max-width: 800px;
    margin:auto;
    min-height: calc(100vh - $header-height);

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
      @include form-field-base;
      padding: $spacing-sm $spacing-xl;   // 覆蓋 mixin 預設的 padding,因為要留 icon 空間
    }

    &__forgot{
      align-self: flex-end;

      &:hover{
        color: $primary-500;
      }
    }

    &__submit{
      width: 100%;
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
      max-width: 500px;

      &__illustration{
        display: none;
      }
    }
  }
</style>