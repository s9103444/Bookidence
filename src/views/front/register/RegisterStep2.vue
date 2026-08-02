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
</template>