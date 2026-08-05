<script>
export default {
  props: {
    isAdult: {
      type: Boolean,
      required: true
    },
    agreeTerms: {
      type: Boolean,
      required: true
    },
    agreePrivacy: {
      type: Boolean,
      required: true
    }
  },
  emits: ['update:isAdult', 'update:agreeTerms', 'update:agreePrivacy']
}
</script>

<template>
  <div class="register-step1__body">
    <div class="register-step1__illustration">
      <img src="@/assets/images/book_and_quill.png" alt="" class="register-step1__illustration-img" />
    </div>
  
    <div class="register-step1">
      <p class="register-step1__title">請確認以下條款，即可繼續下一步建立帳號</p>
      <label class="register-step1__option">
        <input
          type="checkbox"
          :checked="isAdult"
          @change="$emit('update:isAdult', $event.target.checked)"
        />
        <div>
          <p class="register-step1__option-title">我已年滿 13 歲</p>
          <p class="register-step1__option-desc">我確認自己已年滿 13 歲以上，並具備完全行為能力。</p>
        </div>
      </label>
  
      <label class="register-step1__option">
        <input
          type="checkbox"
          :checked="agreeTerms"
          @change="$emit('update:agreeTerms', $event.target.checked)"
        />
        <div>
          <p class="register-step1__option-title">我同意服務條款</p>
          <p class="register-step1__option-desc">我已詳細閱讀並同意 Bookidence 服務條款。</p>
        </div>
      </label>
  
      <label class="register-step1__option">
        <input
          type="checkbox"
          :checked="agreePrivacy"
          @change="$emit('update:agreePrivacy', $event.target.checked)"
        />
        <div>
          <p class="register-step1__option-title">我同意隱私權政策</p>
          <p class="register-step1__option-desc">我已詳細閱讀並同意 Bookidence 隱私權政策。</p>
        </div>
      </label>
    </div>
  </div>
</template>

<style lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
  .register {

    &-step1__body{
      display: flex;
      gap: $spacing-xl;
    }

    &-step1__illustration{
      flex: 4;
      display: flex;
      align-items: center;
      justify-content: center;
      max-width: 100%;
      overflow: hidden;

      &-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
      }
    }

    &-step1{
      flex: 5;
      display: flex;
      flex-direction: column;
      gap: $spacing-xs;
      justify-content: center;
      padding: 0 $spacing-xl;   

      &__title{
        font-weight: $heading-weight;
      }      

      label{
        padding-block: $spacing-sm;
        display: flex;
        border: 1px solid $primary-300;
        border-radius: $btn-radius-std;
      }

      input{
        margin-inline: $spacing-sm;
      }

      &__option-title{
        font-weight: $heading-weight;
      }

      &__option-desc{
        font-size: $p-xs-size;
        color: $neutral-600;
      }
    }
    
  }
</style>