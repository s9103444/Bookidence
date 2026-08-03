<script>
export default {
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
  methods: {
    // togglePassword() {
    //   this.showPassword = !this.showPassword;   // 這裡要填「現在的 showPassword 反過來」
    // }
  }
}
</script>

<template>
  <div class="register-step2__body">
    <div class="register-step2__illustration">
      <img src="@/assets/images/book_and_quill.png" alt="" class="register-step2__illustration-img" />
    </div>
  
    <div class="register-step2">
      <input type="email"
      :value="email"
      @input="$emit('update:email', $event.target.value)"
      />
      <input type="password"
      :value="password"
      @input="$emit('update:password', $event.target.value)"    
      />
      <input type="password" 
      :value="confirmPassword"
      @input="$emit('update:confirmPassword', $event.target.value)"     
      />
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
  }
</style>