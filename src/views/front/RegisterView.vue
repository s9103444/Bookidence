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
    stepTitle() {
      const titles = {
        1: 'step.01：同意條款',
        2: 'step.02：設定帳號',
        3: 'step.03：選擇閱讀偏好',
        4: 'step.04：創建角色＆完成！'
      };
      return titles[this.currentStep];
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
  <div class="register">
    <div class="register__header">
      <span class="register__badge">註冊</span>
      <h1 class="register__step-title">{{ stepTitle }}</h1>
    </div>

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
      <button v-show="currentStep !== 1" @click="goToPrevStep">上一步</button>
      <button :disabled="!isCurrentStepValid" @click="goToNextStep">下一步</button>
    </div>
  </div>
</template>