<script>
// 第一步:import 進來,取一個變數名字
import RegisterStep1 from './register/RegisterStep1.vue';
import RegisterStep2 from './register/RegisterStep2.vue';
import RegisterStep3 from './register/RegisterStep3.vue';
import RegisterStep4 from './register/RegisterStep4.vue';

export default {
  // 第二步:在 components 裡「登記」,才能在 <template> 裡當標籤用
  components: {
    RegisterStep1,
    RegisterStep2,
    RegisterStep3,
    RegisterStep4
  },
  data() {
    return {
      // 精靈控制
      currentStep: 1,

      // Step1 同意條款
      isAdult: false,              // 我已年滿 13 歲
      agreeTerms: false,           // 我同意服務條款
      agreePrivacy: false,         // 我同意隱私權政策

      // Step2 設定帳號
      email: '',
      password: '',
      confirmPassword: '',

      // Step3 閱讀偏好(多選)
      selectedCategoryIds: [],     // 使用者選中的 bcg_id 陣列,例如 [3, 5, 8]

      // Step4 創建角色
      nickname: '',
      selectedHairstyle: '',       // 髮型(寫死選項,存選項的代號)
      selectedHairColorId: null,   // 髮色(存 API 回來的 appear_id)
      selectedSkinColorId: null,   // 膚色(存 API 回來的 appear_id)
      selectedEyeColorId: null,    // 瞳色(存 API 回來的 appear_id)
      appearOptions: {             // API 抓回來的外觀選項清單,依 type 分組
        hairColor: [],
        skinColor: [],
        eyeColor: []
      }
    };
  },
  computed: {
    stepShortLabels() {
      const labels = {
        1: '同意條款',
        2: '設定帳號',
        3: '選擇閱讀偏好',
        4: '創建角色'
      };
      return labels;
    },
    isCurrentStepValid() {
      switch (this.currentStep) {
        case 1:
          return this.isStep1Valid;
        case 2:
          return this.isStep2Valid;
        case 3:
          return true;  // 閱讀偏好可以不選,不強制驗證
        case 4:
          return true;  // Step4 的驗證規則,等寫到 Step4 表單再回來補
        default:
          return false;
      }
    },
    isStep1Valid(){
      return this.isAdult && this.agreeTerms && this.agreePrivacy;
    },
    isStep2Valid(){
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailPattern.test(this.email) && this.password.length >= 6 && this.password === this.confirmPassword;
    },
  },
  methods: {
    goToPrevStep() {
      this.currentStep -= 1;
    },
    goToNextStep() {
      this.currentStep += 1;
    },
    toggleCategory(categoryId) {
      if (this.selectedCategoryIds.includes(categoryId)) {
        // 已經選了 → 要移除
        this.selectedCategoryIds = this.selectedCategoryIds.filter(id => id !== categoryId);
      } else {
        // 還沒選 → 要加進去
        this.selectedCategoryIds = [...this.selectedCategoryIds, categoryId];
      }
    }
  }
}
</script>

<template>
  <div class="register-page">
    <!-- nav -->
    <header class="site-header">
      <a href="/" class="site-header__logo-link">
        <img src="@/assets/logo/Bookidence_logo_primary.png" alt="LOGO" class="site-header__logo">
      </a>
      <div class="site-header__cta-group">
        <p class="site-header__hint">已有帳號?</p>
        <a href="" class="site-header__link">登入</a>
      </div>
    </header>

    <div class="register-progress">
      <div class="register-progress__step" v-for="step in 4" :key="step">
        <div
          class="register-progress__circle"
          :class="{
            'register-progress__circle--active': step === currentStep,
            'register-progress__circle--completed': step < currentStep
          }">
          {{ step }}
        </div>
        <span class="register-progress__label">{{ stepShortLabels[step] }}</span>
      </div>
      <!-- 連接線之後再處理 -->
    </div>

    <div class="register">
      <RegisterStep1
      v-if="currentStep === 1"
      :is-adult="isAdult"
      :agree-terms="agreeTerms"
      :agree-privacy="agreePrivacy"
      @update:is-adult="isAdult = $event"
      @update:agree-terms="agreeTerms = $event"
      @update:agree-privacy="agreePrivacy = $event"
      />
      <RegisterStep2 
      v-else-if="currentStep === 2"
      :email="email"
      :password="password"
      :confirm-password="confirmPassword"
      @update:email="email = $event"
      @update:password="password = $event"
      @update:confirm-password="confirmPassword = $event"
      />
      <RegisterStep3 
      v-else-if="currentStep === 3" 
      :selected-category-ids="selectedCategoryIds"
      @toggle-category="toggleCategory"
      />
      <RegisterStep4 v-else-if="currentStep === 4" />

      <div class="register__step-button">
        <button class="register__prev" v-show="currentStep !== 1" @click="goToPrevStep">上一步</button>
        <button class="register__next" :disabled="!isCurrentStepValid" @click="goToNextStep">下一步</button>
      </div>
    </div>
  </div>
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

  .register-page {
    background-color: $neutral-200;
    min-height: 100vh;
  }

  .register-progress {
  max-width: 800px;
  margin-inline: auto;
  padding: $spacing-xl $spacing-xl 0;
  display: flex;
}

  .register {
    max-width: 800px;
    margin-inline: auto;
    margin-bottom: $spacing-xl;
    padding: $spacing-xl;

    &__step-button{
      display: flex;
      justify-content: center;
      margin-top: $spacing-md;
      gap: $spacing-lg;
    }

    &__prev{
      background-color: transparent;
      color: $primary;
      width: 200px;
      padding: $spacing-xxs;
      border-radius: $spacing-sm;
      border: 1px solid $primary;
      cursor: pointer;

      &:hover{
        background-color: $primary-300;
        color: $neutral-100;
      }
    }

    &__next{
      background-color: $primary;
      color: $neutral-100;
      width: 200px;
      padding: $spacing-xxs;
      border-radius: $spacing-sm;
      border: none;
      cursor: pointer;

      &:hover{
        background-color: $primary-500;
      }

      &:disabled{
        background-color: $neutral-400;
        cursor: not-allowed;
      }
    }
  }
</style>