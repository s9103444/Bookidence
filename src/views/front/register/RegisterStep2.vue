<script>
  import { IconMail, IconLock, IconEye, IconEyeClosed } from '@tabler/icons-vue';

  export default {
    components: { IconMail, IconLock, IconEye, IconEyeClosed },
    props: {
      email: {
        type: String,
        required: true
      },
      password: {
        type: String,
        required: true
      },
      confirmPassword: {
        type: String,
        required: true
      }
    },
  emits: ['update:email', 'update:password', 'update:confirmPassword'],
  data() {
    return {
      pwdVisibility: {
        password: false,
        confirmPassword: false
      }
    };
  },
  methods: {
    togglePwdVisibility(field) {
      this.pwdVisibility[field] = !this.pwdVisibility[field];
    }
  }
}
</script>

<template>
  <div class="register-step2__body">
    <div class="register-step2__illustration">
      <img src="@/assets/images/book_and_quill.png" alt="" class="register-step2__illustration-img" />
    </div>
  
    <div class="register-step2">
      <p class="register-step2__title">建立你的帳號</p>
      <label>
        <p class="register-step2__option-title">E-mail</p>
        <div class="register-step2__input-wrapper">
          <IconMail class="register-step2__input-icon" />
          <input type="email"
          :value="email"
          @input="$emit('update:email', $event.target.value)"
          placeholder="請輸入E-mail"
          />
        </div>
      </label>

      <label>
        <p class="register-step2__option-title">密碼</p>
        <div class="register-step2__input-wrapper">
          <IconLock class="register-step2__input-icon" />
          <input
            :type="pwdVisibility.password ? 'text' : 'password'"
            :value="password"
            @input="$emit('update:password', $event.target.value)"
            placeholder="請輸入密碼"
          />
          <IconEye v-if="pwdVisibility.password" class="register-step2__input-toggle" @click="togglePwdVisibility('password')" />
          <IconEyeClosed v-else class="register-step2__input-toggle" @click="togglePwdVisibility('password')" />
        </div>
      </label>

      <label>
        <p class="register-step2__option-title">確認密碼</p>
        <div class="register-step2__input-wrapper">
          <IconLock class="register-step2__input-icon" />
          <input
            :type="pwdVisibility.confirmPassword ? 'text' : 'password'"
            :value="confirmPassword"
            @input="$emit('update:confirmPassword', $event.target.value)"
            placeholder="確認密碼"
          />
          <IconEye v-if="pwdVisibility.confirmPassword" class="register-step2__input-toggle" @click="togglePwdVisibility('confirmPassword')" />
          <IconEyeClosed v-else class="register-step2__input-toggle" @click="togglePwdVisibility('confirmPassword')" />
        </div>
      </label>
    </div>
  </div>
</template>

<style lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
  .register {

    &-step2__body{
      display: flex;
      gap: $spacing-xl;
    }

    &-step2__illustration{
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

    &-step2{
      flex: 5;
      display: flex;
      flex-direction: column;
      gap: $spacing-xs;
      justify-content: center;
      padding: 0 $spacing-xl;   
      
      &__title{
        font-weight: $heading-weight;
      }

      input{
      font-size: $p-md-size;
      padding: $spacing-sm $spacing-xl;
      border: 1px solid $neutral-400;
      border-radius: $btn-radius-std;
      width: 100%;

        &:focus{
          border-color: $primary;
          outline: none;
          box-shadow: 0 0 0 3px rgba($primary, 0.2);
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
    }
  }
</style>