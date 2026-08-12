<script>
  const mockAppearOptions = [
    // 性別
    { appearId: 'g01', type: 'gender', optionName: '女生', value: 'female' },
    { appearId: 'g02', type: 'gender', optionName: '男生', value: 'male' },

    // 髮色
    { appearId: 'fh1', type: 'hair', optionName: '黑髮色', gender: 'female',colorValue: '#41464E', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/female_hair_black.png` },
    { appearId: 'fh2', type: 'hair', optionName: '藍髮色', gender: 'female', colorValue: '#2D4363', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/female_hair_blue.png` },
    { appearId: 'fh3', type: 'hair', optionName: '咖啡色', gender: 'female', colorValue: '#B4641E', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/female_hair_brown.png` },
    { appearId: 'mh1', type: 'hair', optionName: '黑髮色', gender: 'male', colorValue: '#41464E', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/male_hair_black.png` },
    { appearId: 'mh2', type: 'hair', optionName: '藍髮色', gender: 'male', colorValue: '#2D4363', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/male_hair_blue.png` },
    { appearId: 'mh3', type: 'hair', optionName: '咖啡色', gender: 'male', colorValue: '#B4641E', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/male_hair_brown.png` },

    // 瞳色
    { appearId: 'fe1', type: 'eyes', optionName: '黑眼睛', gender: 'female', colorValue: '#333333', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/female_eyes_black.png` },
    { appearId: 'fe2', type: 'eyes', optionName: '藍眼睛', gender: 'female', colorValue: '#244C6B', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/female_eyes_blue.png` },
    { appearId: 'fe3', type: 'eyes', optionName: '綠眼睛', gender: 'female', colorValue: '#809320', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/female_eyes_green.png` },
    { appearId: 'me1', type: 'eyes', optionName: '黑眼睛', gender: 'male', colorValue: '#333333', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/male_eyes_black.png` },
    { appearId: 'me2', type: 'eyes', optionName: '藍眼睛', gender: 'male', colorValue: '#244C6B', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/male_eyes_blue.png` },
    { appearId: 'me3', type: 'eyes', optionName: '綠眼睛', gender: 'male', colorValue: '#809320', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/male_eyes_green.png` },

    // 膚色
    { appearId: 'fs1', type: 'skin', optionName: '白皮膚', gender: 'female', colorValue: '#F8DCBB', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/female_skin_light.png` },
    { appearId: 'fs2', type: 'skin', optionName: '黃皮膚', gender: 'female', colorValue: '#F1C88A', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/female_skin_medium.png` },
    { appearId: 'fs3', type: 'skin', optionName: '深皮膚', gender: 'female', colorValue: '#C38F61', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/female_skin_dark.png` },
    { appearId: 'ms1', type: 'skin', optionName: '白皮膚', gender: 'male', colorValue: '#F8DCBB', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/male_skin_light.png` },
    { appearId: 'ms2', type: 'skin', optionName: '黃皮膚', gender: 'male', colorValue: '#F1C88A', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/male_skin_medium.png` },
    { appearId: 'ms3', type: 'skin', optionName: '深皮膚', gender: 'male', colorValue: '#C38F61', iconPath: `${import.meta.env.BASE_URL}images/appear/character-for-register/male_skin_dark.png` },
  ];
  export default {
    props:{
      selectedGender: {
        type: String,
      },
      selectedHairColorId:{
        type: String,
      },
      selectedSkinColorId:{
        type: String,
      },
      selectedEyeColorId:{
        type: String,
      },
      nickname:{
        type: String,
        required: true
      }
    },
    emits: ['update:nickname', 'update:selectedGender', 'update:selectedHairColorId', 'update:selectedSkinColorId', 'update:selectedEyeColorId'],
    data() {
      return {
        allAppearOptions: []   // 存放「API 回來」的完整清單
      };
    },
    created() {
      this.allAppearOptions = mockAppearOptions
    },
    computed:{
      hairOptions() {
        return this.allAppearOptions.filter(option => option.type === "hair" && option.gender === this.selectedGender);
      },
      skinOptions(){
        return this.allAppearOptions.filter(option => option.type === "skin" && option.gender === this.selectedGender);
      },
      eyeOptions(){
        return this.allAppearOptions.filter(option => option.type === "eyes" && option.gender === this.selectedGender);
      },
    },
    methods: {
      selectGender(newGender) {
        const findEquivalent = (currentId, type) => {
          const currentOption = this.allAppearOptions.find(option => option.appearId === currentId);
          const matched = this.allAppearOptions.find(option => option.type === type && option.gender === newGender && option.optionName === currentOption.optionName);
          return matched ? matched.appearId : null;
        };

        this.$emit('update:selectedGender', newGender);
        this.$emit('update:selectedHairColorId', findEquivalent(this.selectedHairColorId, 'hair'));
        this.$emit('update:selectedEyeColorId', findEquivalent(this.selectedEyeColorId, 'eyes'));
        this.$emit('update:selectedSkinColorId', findEquivalent(this.selectedSkinColorId, 'skin'));
      }
    }
  };
</script>

<template>
  <div class="register-step4__body">
    <div
      class="register-step4__preview"
      :class="`register-step4__preview--${selectedGender}`"
    >
      <img
        v-for="option in skinOptions"
        :key="option.appearId"
        v-show="selectedSkinColorId === option.appearId"
        :src="option.iconPath"
        class="register-step4__preview-layer register-step4__preview-layer--skin"
      />
      <img
        v-for="option in hairOptions"
        :key="option.appearId"
        v-show="selectedHairColorId === option.appearId"
        :src="option.iconPath"
        class="register-step4__preview-layer register-step4__preview-layer--hair"
      />
      <img
        v-for="option in eyeOptions"
        :key="option.appearId"
        v-show="selectedEyeColorId === option.appearId"
        :src="option.iconPath"
        class="register-step4__preview-layer register-step4__preview-layer--eyes"
      />
    </div>
    <div class="register-step4__form">
      <p class="register-step4__title">建立你的閱讀角色</p>
      <p class="register-step4__subtitle">打造屬於你的閱讀夥伴吧！</p>
      <label>
        暱稱
        <input type="text"
        :value="nickname"
        @input="$emit('update:nickname', $event.target.value)"
        maxlength="10"
        placeholder="請輸入暱稱(限10個字)"
        />
      </label>

      <div class="register-step4__gender">
        <p class="register-step4__field-label">性別</p>
        <div class="register-step4__field-options">
          <div
            class="register-step4__gender-option"
            :class="{ 'register-step4__gender-option--selected': selectedGender === 'female' }"
            @click="selectGender('female')"
          >
            女生
          </div>
          <div
            class="register-step4__gender-option"
            :class="{ 'register-step4__gender-option--selected': selectedGender === 'male' }"
            @click="selectGender('male')"
          >
            男生
          </div>
        </div>
      </div>

      <div class="register-step4__field">
        <p class="register-step4__field-label">髮色</p>
        <div class="register-step4__field-options">
          <div 
          v-for="option in hairOptions" 
          :key="option.appearId" 
          class="register-step4__option-wrapper"
          :class="{ 'register-step4__option-wrapper--selected': selectedHairColorId === option.appearId }"
          @click="$emit('update:selectedHairColorId', option.appearId)"
          >
            <div
              class="register-step4__option"
              :style="{ backgroundColor: option.colorValue }"
            ></div>
            <span class="register-step4__option-label">{{ option.optionName }}</span>
          </div>
        </div>
      </div>
  
      <div class="register-step4__field">
        <p class="register-step4__field-label">眼睛</p>
        <div class="register-step4__field-options">
          <div 
          v-for="option in eyeOptions" 
          :key="option.appearId" 
          class="register-step4__option-wrapper"
          :class="{ 'register-step4__option-wrapper--selected': selectedEyeColorId === option.appearId }"
          @click="$emit('update:selectedEyeColorId', option.appearId)"
          >
            <div
              class="register-step4__option"
              :style="{ backgroundColor: option.colorValue }"
            ></div>
            <span class="register-step4__option-label">{{ option.optionName }}</span>
          </div>
        </div>
      </div>
  
      <div class="register-step4__field">
        <p class="register-step4__field-label">皮膚</p>
        <div class="register-step4__field-options">
          <div 
          v-for="option in skinOptions" 
          :key="option.appearId" 
          class="register-step4__option-wrapper"
          :class="{ 'register-step4__option-wrapper--selected': selectedSkinColorId === option.appearId }"
          @click="$emit('update:selectedSkinColorId', option.appearId)"
          >
            <div
              class="register-step4__option"
              :style="{ backgroundColor: option.colorValue }"
            ></div>
            <span class="register-step4__option-label">{{ option.optionName }}</span>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</template>

<style lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;
  .register{
    &-step4{
      &__title{
        font-weight: $heading-weight;
      }

      &__subtitle{
        font-size: $p-sm-size;
      }
      &__body{
        display: flex;
        gap: $spacing-xl;
        justify-content: center;
      }
      
      &__preview {
        width: 150px;
        flex-shrink: 0;
        // border: 1px solid $neutral-400;
        position: relative;
        aspect-ratio: 146 / 222;
        background-repeat: no-repeat;
        background-size: contain;
        background-position: center;

        &--female {
          background-image: url(@/assets/images/appear/female.png);

          .register-step4__preview-layer--skin { top: 43%; left: 24.5%; width: 59%; }
          .register-step4__preview-layer--eyes { top: 46.5%; left: 44%; width: 31.1%; }
          .register-step4__preview-layer--hair { top: 37.5%; left: 0%; width: 99.3%; }
        }

        &--male {
          background-image: url(@/assets/images/appear/male.png);

          .register-step4__preview-layer--skin { top: 42.6%; left: 26%; width: 51.5%; }
          .register-step4__preview-layer--eyes { top: 45.2%; left: 43%; width: 28.3%; }
          .register-step4__preview-layer--hair { top: 24.5%; left: 9%; width: 89%; }
        }

        &-layer {
          position: absolute;
          // border: 1px solid red;
        }
      }
      &__form{
        max-width: 400px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;

        input{
          @include form-field-base;
        }
      }

      &__gender{
        gap: $spacing-xs;
        &-option{
          text-align: center;
          padding: $spacing-xs;
          border: 1px solid $neutral-400;
          border-radius: $btn-radius-std;

          &--selected{
            background-color: $primary-300;
            color: $neutral-100;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
            border: none;
          }
        }
      }

      &__field{
        &-options{
          display: flex;
          gap: $spacing-md;
        }
      }
  
      &__option{
        width: 30px;
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        margin-inline: auto;
  
        
        &-wrapper {
          text-align: center;
          padding: $spacing-xs;
          border: 1px solid $neutral-400;
          border-radius: $btn-radius-std;
          
          &--selected{
            background-color: $primary-300;
            color: $neutral-100;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
            border: none;
          }
        }
      }
    }
  }
</style>