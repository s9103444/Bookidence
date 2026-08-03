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
      <label class="register-step1__option">
        <input
          type="checkbox"
          :checked="isAdult"
          @change="$emit('update:isAdult', $event.target.checked)"
        />
        我已年滿 13 歲
      </label>
  
      <label class="register-step1__option">
              <input
          type="checkbox"
          :checked="agreeTerms"
          @change="$emit('update:agreeTerms', $event.target.checked)"
        />
        我同意服務條款
      </label>
  
          <label class="register-step1__option">
              <input
          type="checkbox"
          :checked="agreePrivacy"
          @change="$emit('update:agreePrivacy', $event.target.checked)"
        />
        我同意隱私權政策
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
  }
</style>