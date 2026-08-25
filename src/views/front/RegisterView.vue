<script>
// 第一步:import 進來,取一個變數名字
import RegisterStep1 from "./register/RegisterStep1.vue";
import RegisterStep2 from "./register/RegisterStep2.vue";
import RegisterStep3 from "./register/RegisterStep3.vue";
import RegisterStep4 from "./register/RegisterStep4.vue";
import AppButton from "../../components/common/AppButton.vue";
import { mapActions } from "pinia";
import { useUserStore } from "@/stores/user";
import { API_BASE } from "@/common/api";

export default {
  // 第二步:在 components 裡「登記」,才能在 <template> 裡當標籤用
  components: {
    RegisterStep1,
    RegisterStep2,
    RegisterStep3,
    RegisterStep4,
    AppButton,
  },
  data() {
    return {
      // 精靈控制
      currentStep: 1,

      // Step1 同意條款
      isAdult: false, // 我已年滿 13 歲
      agreeTerms: false, // 我同意服務條款
      agreePrivacy: false, // 我同意隱私權政策

      // Step2 設定帳號
      email: "",
      password: "",
      confirmPassword: "",

      // Step3 閱讀偏好(多選)
      selectedCategoryIds: [], // 使用者選中的 bcg_id 陣列,例如 [3, 5, 8]

      // Step4 創建角色
      nickname: "",
      selectedGender: "female", // 性別
      selectedHairColorId: "fh3", // 髮色(存 API 回來的 appear_id)
      selectedSkinColorId: "fs2", // 膚色(存 API 回來的 appear_id)
      selectedEyeColorId: "fe3", // 瞳色(存 API 回來的 appear_id)
      appearOptions: {
        // API 抓回來的外觀選項清單,依 type 分組
        hairColor: [],
        skinColor: [],
        eyeColor: [],
      },
    };
  },
  computed: {
    stepShortLabels() {
      const labels = {
        1: "同意條款",
        2: "設定帳號",
        3: "選擇閱讀偏好",
        4: "創建角色",
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
          return true; // 閱讀偏好可以不選,不強制驗證
        case 4:
          return this.isStep4Valid; // Step4 的驗證規則,等寫到 Step4 表單再回來補
        default:
          return false;
      }
    },
    isStep1Valid() {
      return this.isAdult && this.agreeTerms && this.agreePrivacy;
    },
    isStep2Valid() {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return (
        emailPattern.test(this.email) &&
        this.password.length >= 6 &&
        this.password === this.confirmPassword
      );
    },
    isStep4Valid() {
      return this.nickname;
    },
    progressFillWidth() {
      const fraction = (this.currentStep - 1) / 3;
      return `calc((75% - 60px) * ${fraction})`;
    },
    nextButtonLabel() {
      return this.currentStep === 4 ? "完成" : "下一步";
    },
  },
  methods: {
    ...mapActions(useUserStore, ["login"]),
    goToPrevStep() {
      this.currentStep -= 1;
    },
    goToNextStep() {
      if (!this.isCurrentStepValid) {
        return;
      }
      if (this.currentStep < 4) {
        this.currentStep += 1;
      } else {
        this.completeRegistration();
      }
    },
    async completeRegistration() {
      const res = await fetch(`${API_BASE}/register.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          email: this.email,
          password: this.password,
          nickname: this.nickname,
          selectedCategoryIds: this.selectedCategoryIds,
          selectedGender: this.selectedGender,
          selectedHairColorId: this.selectedHairColorId,
          selectedSkinColorId: this.selectedSkinColorId,
          selectedEyeColorId: this.selectedEyeColorId,
        }),
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
        avatarUrl: "",
        xp: result.user.total_exp,
      });
      this.$router.push({ name: "home" });
    },
    toggleCategory(categoryId) {
      if (this.selectedCategoryIds.includes(categoryId)) {
        // 已經選了 → 要移除
        this.selectedCategoryIds = this.selectedCategoryIds.filter(
          (id) => id !== categoryId,
        );
      } else {
        // 還沒選 → 要加進去
        this.selectedCategoryIds = [...this.selectedCategoryIds, categoryId];
      }
    },
  },
};
</script>

<template>
  <div class="register-page">
    <!-- nav -->
    <header class="site-header">
      <router-link :to="{ name: 'home' }" class="site-header__logo-link">
        <img
          src="@/assets/logo/Bookidence_logo_primary.png"
          alt="LOGO"
          class="site-header__logo"
        />
      </router-link>
      <div class="site-header__cta-group">
        <p class="site-header__hint">已有帳號?</p>
        <router-link :to="{ name: 'login' }" class="site-header__link"
          >登入</router-link
        >
      </div>
    </header>

    <div class="register-progress">
      <div
        class="register-progress__line-fill"
        :style="{ width: progressFillWidth }"
      ></div>
      <div class="register-progress__step" v-for="step in 4" :key="step">
        <div
          class="register-progress__circle"
          :class="{
            'register-progress__circle--active': step === currentStep,
            'register-progress__circle--completed': step < currentStep,
          }"
        >
          {{ step }}
        </div>
        <span
          class="register-progress__label"
          :class="{ 'register-progress__label--active': step === currentStep }"
        >
          {{ stepShortLabels[step] }}
        </span>
      </div>
    </div>

    <div class="register" @keyup.enter="currentStep === 2 && goToNextStep()">
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
      <RegisterStep4
        v-else-if="currentStep === 4"
        :nickname="nickname"
        :selectedGender="selectedGender"
        :selectedHairColorId="selectedHairColorId"
        :selectedSkinColorId="selectedSkinColorId"
        :selectedEyeColorId="selectedEyeColorId"
        @update:nickname="nickname = $event"
        @update:selectedGender="selectedGender = $event"
        @update:selectedHairColorId="selectedHairColorId = $event"
        @update:selectedSkinColorId="selectedSkinColorId = $event"
        @update:selectedEyeColorId="selectedEyeColorId = $event"
      />

      <div class="register__step-button">
        <AppButton
          variant="outlined"
          class="register__prev"
          v-show="currentStep !== 1"
          @click="goToPrevStep"
          >上一步</AppButton
        >
        <AppButton
          class="register__next"
          :disabled="!isCurrentStepValid"
          @click="goToNextStep"
        >
          {{ nextButtonLabel }}
        </AppButton>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
@use "@/assets/scss/abstracts/variables" as *;
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

  &__cta-group {
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
  justify-content: space-between;
  position: relative;

  &__line-fill {
    position: absolute;
    top: calc($spacing-xl + 16px);
    left: calc(12.5% + 30px);
    height: 2px;
    background-color: $primary-300;
    z-index: 0;
    transition: width 0.3s ease;
  }

  &::before {
    content: "";
    position: absolute;
    top: calc($spacing-xl + 16px); // 對齊圓圈垂直中心
    left: calc(12.5% + 30px);
    right: calc(12.5% + 30px);
    height: 2px;
    background-color: $neutral-300;
    z-index: 0;
  }

  &__step {
    display: flex;
    flex: 1;
    flex-direction: column;
    align-items: center;
    gap: $spacing-xs;
    position: relative;
  }

  &__label {
    font-size: $p-sm-size;
    color: $neutral-500;
    transition:
      color 0.2s ease,
      font-weight 0.2s ease;

    &--active {
      font-weight: $heading-weight;
      color: $neutral-800;
    }
  }

  &__circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid $neutral-300;
    color: $neutral-400;
    background-color: $neutral-100;
    font-weight: $heading-weight;
    transition: all 0.3s ease;

    &--active {
      background-color: $primary;
      border-color: $primary;
      color: $neutral-100;
      width: 40px;
      height: 40px;
    }

    &--completed {
      background-color: $primary-300;
      border-color: $primary-300;
      color: $neutral-100;
    }
  }

  @media (max-width: $breakpoint-tablet) {
    padding: $spacing-xl $spacing-md 0;

    &__line-fill {
      left: calc(12.5% + 12px);
    }

    &::before {
      left: calc(12.5% + 12px);
      right: calc(12.5% + 12px);
    }
  }

  @media (max-width: $breakpoint-mobile) {
    &__label {
      display: none;
    }
  }
}

.register {
  max-width: 800px;
  margin-inline: auto;
  margin-bottom: $spacing-xl;
  padding: $spacing-xl;
  --btn-surface: #{$neutral-200}; // outlined 按鈕要吃這頁的底色,不然會露白底

  &__step-button {
    display: flex;
    justify-content: center;
    margin-top: $spacing-md;
    gap: $spacing-lg;
  }

  &__prev,
  &__next {
    width: 200px;
  }

  @media (max-width: $breakpoint-tablet) {
    padding: $spacing-md;

    &__prev,
    &__next {
      width: auto;
      flex: 1;
    }
  }
}
</style>
