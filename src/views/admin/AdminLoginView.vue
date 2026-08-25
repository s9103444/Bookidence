<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import {API_BASE} from '@/common/api'
import {useAdminStore} from '@/stores/adminAuth'
import AdminButton from '@/components/admin/AdminButton.vue'
import AppIcon from '@/components/common/AppIcon.vue'

const router=useRouter();
const adminStore=useAdminStore();

const form = reactive({ account: '', password: '' })
const errors = reactive({ account: '', password: '' })

const formError = ref('')
const isPasswordVisible = ref(false)
const isHintOpen = ref(false)

const accountInput = ref(null)
const passwordInput = ref(null)

function validate() {
  errors.account = form.account.trim() ? '' : '請輸入管理員帳號'
  errors.password = form.password ? '' : '請輸入密碼'
  return !errors.account && !errors.password
}

async function handleSubmit() {
  formError.value = ''

  if (!validate()) {
    if (errors.account) {
      accountInput.value.focus()
    } else {
      passwordInput.value.focus()
    }
    return
  }

  const res=await fetch(`${API_BASE}/admin_login.php`,{
    method:'POST',
    headers:{"Content-Type":"application/json"},
    body:JSON.stringify({account:form.account.trim(),password:form.password}),
  })
  const result=await res.json();
  // console.log(result);
  if(!result.success){
    formError.value=result.message
    return
  }
  adminStore.login({
    token:result.token,
    account:result.staff.staff_account,
    staffName:result.staff.staff_name,
  })
  router.push({name:"admin-dashboard"})
}
</script>

<template>
  <main class="admin-login">
    <div class="admin-login__card">
      <img class="admin-login__logo" src="@/assets/logo/Bookidence_logo_primary_flat.png" alt="Bookidence" />
      <h1 class="admin-login__title">管理員登入</h1>

      <form class="admin-login__form" novalidate @submit.prevent="handleSubmit">
        <p v-if="formError" class="admin-login__alert" role="alert">
          {{ formError }}
        </p>

        <div class="admin-login__field" :class="{ 'admin-login__field--error': errors.account }">
          <label class="admin-login__label" for="admin-account">管理員帳號</label>
          <input
            id="admin-account"
            ref="accountInput"
            v-model="form.account"
            class="admin-login__input"
            type="text"
            name="account"
            autocomplete="username"
            autocapitalize="none"
            spellcheck="false"
            :aria-invalid="Boolean(errors.account)"
            :aria-describedby="errors.account ? 'admin-account-error' : undefined"
          />
          <p v-if="errors.account" id="admin-account-error" class="admin-login__error" role="alert">
            {{ errors.account }}
          </p>
        </div>

        <div class="admin-login__field" :class="{ 'admin-login__field--error': errors.password }">
          <label class="admin-login__label" for="admin-password">密碼</label>
          <div class="admin-login__control">
            <input
              id="admin-password"
              ref="passwordInput"
              v-model="form.password"
              class="admin-login__input admin-login__input--password"
              :type="isPasswordVisible ? 'text' : 'password'"
              name="password"
              autocomplete="current-password"
              :aria-invalid="Boolean(errors.password)"
              :aria-describedby="errors.password ? 'admin-password-error' : undefined"
            />
            <button
              type="button"
              class="admin-login__reveal"
              :aria-label="isPasswordVisible ? '隱藏密碼' : '顯示密碼'"
              @click="isPasswordVisible = !isPasswordVisible"
            >
              <AppIcon :name="isPasswordVisible ? 'eye' : 'eye-off'" :size="20" />
            </button>
          </div>
          <p v-if="errors.password" id="admin-password-error" class="admin-login__error" role="alert">
            {{ errors.password }}
          </p>
        </div>

        <AdminButton type="submit" class="admin-login__submit">登入</AdminButton>
      </form>

      <div class="admin-login__help">
        <button
          type="button"
          class="admin-login__forgot"
          :aria-expanded="isHintOpen"
          aria-controls="admin-login-hint"
          @click="isHintOpen = !isHintOpen"
        >
          忘記密碼？
        </button>
        <p v-if="isHintOpen" id="admin-login-hint" class="admin-login__hint">
          管理員帳號由內部核發，無法自行重設。請聯繫核發帳號給你的人。
        </p>
      </div>

      <p class="admin-login__note">
        僅供系統管理人員使用。一般會員請由
        <RouterLink :to="{ name: 'login' }">官網登入</RouterLink>。
      </p>
    </div>
  </main>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;

.admin-login {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: $spacing-xl $spacing-lg;
  background: $neutral-200;

  &__card {
    display: flex;
    flex-direction: column;
    width: 100%;
    max-width: 416px;
    padding: $spacing-xl;
    border-radius: 10px;
    background: $neutral-100;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04), 0 10px 30px rgba(0, 0, 0, 0.08);
    text-align: center;
  }

  &__logo {
    width: auto;
    height: 30px;
    margin: 0 auto $spacing-md;
  }

  &__title {
    margin: 0 0 $spacing-lg;
    font-size: $h6-size;
    font-weight: $heading-weight;
    letter-spacing: $letter-spacing-base;
    color: $neutral-800;
  }

  &__form {
    display: flex;
    flex-direction: column;
    gap: $spacing-md;
    text-align: left;
  }

  &__alert {
    margin: 0;
    padding: $spacing-sm + $spacing-xs $spacing-md;
    border-left: 4px solid $color-danger;
    border-radius: 0 $btn-radius-std $btn-radius-std 0;
    background: $neutral-200;
    font-size: $p-sm-size;
    line-height: $text-line-height;
    color: $neutral-800;
  }

  &__field {
    display: flex;
    flex-direction: column;
  }

  &__label {
    margin-bottom: $spacing-sm;
    font-size: $p-xs-size;
    color: $neutral-600;
  }

  &__control {
    position: relative;
    display: flex;
    align-items: center;
  }

  &__input {
    width: 100%;
    box-sizing: border-box;
    padding: $spacing-sm + $spacing-xs $spacing-sm + $spacing-xs;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std;
    background: $neutral-100;
    font-family: inherit;
    font-size: $p-sm-size;
    color: $neutral-800;

    &:focus-visible {
      outline: 2px solid $primary;
      outline-offset: -1px;
    }

    &--password {
      padding-right: 44px;
    }
  }

  &__reveal {
    position: absolute;
    right: $spacing-xs;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    border: 0;
    border-radius: $btn-radius-std;
    background: transparent;
    color: $neutral-500;
    cursor: pointer;

    &:hover {
      background: $neutral-200;
      color: $primary;
    }

    &:focus-visible {
      outline: 2px solid $primary;
      outline-offset: -2px;
    }
  }

  &__error {
    margin: $spacing-xs 0 0;
    font-size: $p-xs-size;
    line-height: $text-line-height;
    color: $color-danger;
  }

  &__field--error &__input {
    border-color: $color-danger;
  }

  &__submit {
    width: 100%;
    margin-top: $spacing-sm;
  }

  &__help {
    margin-top: $spacing-lg;
  }

  &__forgot {
    padding: 0;
    border: 0;
    background: none;
    font-family: inherit;
    font-size: $p-sm-size;
    color: $primary;
    text-decoration: underline;
    text-underline-offset: 3px;
    cursor: pointer;

    &:focus-visible {
      outline: 2px solid $primary;
      outline-offset: 2px;
    }
  }

  &__hint {
    margin: $spacing-sm + $spacing-xs 0 0;
    padding: $spacing-sm + $spacing-xs $spacing-md;
    border-radius: $btn-radius-std;
    background: $primary-100;
    font-size: $p-xs-size;
    line-height: 1.8;
    color: $neutral-700;
    text-align: left;
  }

  &__note {
    margin: $spacing-lg 0 0;
    padding-top: $spacing-lg;
    border-top: 1px solid $neutral-300;
    font-size: $p-xs-size;
    line-height: 1.8;
    color: $neutral-500;

    a {
      color: $primary;
    }
  }

  @media (max-width: $breakpoint-mobile) {
    padding: $spacing-lg $spacing-md;

    &__card {
      padding: $spacing-lg;
    }
  }
}
</style>
