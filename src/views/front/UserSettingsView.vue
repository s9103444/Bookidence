<script>
import { API_BASE } from '@/common/api';
import { mapState } from 'pinia';
import { useUserStore } from '@/stores/user';
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import AppButton from "@/components/common/AppButton.vue";
import AppIcon from "@/components/common/AppIcon.vue";
import PhotoSticker from "../../components/front/PhotoSticker.vue";

export default {
  components: {
    GuildBreadcrumb,
    AppIcon,
    PhotoSticker,
    AppButton
  },
  data() {
    return {
      showOldPassword: false,
      showNewPassword: false,
      showConfirmPassword: false,
      // showPassword: false,
      showConfirm: false,
      formData: {
        nickname: '',
        bio: '',
        createdAt: '',
        account_status: '',
        memberCode: '',
        email: '',
        password: ''
      },
      showResetPassword: false,
      reSetForm: {
        oldPassword: '',
        newPassword: '',
        confirmPassword: ''
      },
      avatarPreview: '',
      userId: null
    }
  },
  methods: {
    togglePassword() {
      this.showPassword = !this.showPassword;   // 這裡要填「現在的 showPassword 反過來」
    },
    handleAvatarChange(event) {
      const file = event.target.files[0]
      if (!file) return
      this.avatarPreview = URL.createObjectURL(file)
    },
    async loadProfile() {
      const res = await fetch(`${API_BASE}/get_profile.php`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${this.token}`,
          'Content-Type': 'application/json'
        },
      })

      const result = await res.json();
      if (result.success) {
        this.formData.nickname = result.profile.nickname
        this.formData.bio = result.profile.bio
        this.formData.email = result.profile.email
        this.formData.account_status = result.profile.account_status
        this.formData.createdAt = result.profile.created_at
        this.formData.memberCode = result.profile.member_code
      }

    },
    askSave() {
      this.showConfirm = true
    },

    async confirmSave() {

      const res = await fetch(`${API_BASE}/update_profile.php`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${this.token}`
          },
          body: JSON.stringify({ bio: this.formData.bio, nickname: this.formData.nickname })
        });
      const result = await res.json();

      if (result.success) {
        await this.loadProfile();
        alert('更變成功');
      }
      this.showConfirm = false

    }, cancelSave() {
      this.showConfirm = false
    },

    askEditPassword() {
      this.showResetPassword = true
    },

    async confirmSavePassword() {

      if (!this.reSetForm.oldPassword || !this.reSetForm.newPassword || !this.reSetForm.confirmPassword) {
        alert('請完整填寫所有欄位')
        return
      }

      if (this.reSetForm.newPassword !== this.reSetForm.confirmPassword) {
        alert('兩次輸入的新密碼不一致')
        return
      }

      const res = await fetch(`${API_BASE}/update_password.php`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${this.token}`
          },
          body: JSON.stringify({ newPassword: this.reSetForm.newPassword, oldPassword: this.reSetForm.oldPassword })
        });
      const result = await res.json();

      if (result.success) {
        await this.loadProfile();
        alert('更變成功');
        this.showResetPassword = false;
      } else {
        alert(result.message);
      }



    },
    cancelSavePassword() {
      this.showResetPassword = false
    },

  },

  computed: {

    ...mapState(useUserStore, ["token"])

  },
  mounted() {
    this.loadProfile();
  },
  async created() {
    const userStore = useUserStore();
    const res = await fetch(`${API_BASE}/me.php`, {
      method: "GET",
      headers: {
        Authorization: `Bearer ${userStore.token}`,
      },
    });
    const result = await res.json();
    if (result.success) {

      this.userId = result.user.user_id;
    }
  },
}
</script>
<template>
  <section class="space">

    <div class="my-setting container-content  ">
      <GuildBreadcrumb class="col-10" :items="[
        { label: '❮  首頁', to: `/home` },// guilds/:id 填入目前公會的 id
        { label: '使用者設定' }
      ]" />
      <div class="porfile col-5 ">
        <h3>個人資料</h3>
        <div class="profile-main">
          <div class="img-cover">
            <!-- <label class="avatar-preview" :style="{ backgroundImage: avatarPreview ? `url(${avatarPreview})` : '' }"> -->
            <PhotoSticker class="photo-sticker" :userId="userId" :width="80" />
            <!-- 
              <input type="file" ref="avatarInput" @change="handleAvatarChange" accept="image/*" class="avatar-input"> -->
            <!-- </label> -->
          </div>
          <div class="nickname-field">
            <label for="nickname">暱稱</label>
            <input type="text" name="nickname" id="nickname" v-model="formData.nickname" placeholder="請輸入你的暱稱">

            <div class="about-me">
              <label for="bio">自我介紹</label>
              <textarea name="bio" id="bio" v-model="formData.bio" rows=" 7"></textarea>
              <label for="createdAt">加入時間</label>
              <input type="text" name="createdAt" id="createdAt" v-model="formData.createdAt" disabled>
            </div>
          </div>
        </div>
      </div>


      <div class="porfile col-5">
        <h3>帳號與安全</h3>
        <label for="account_status">帳號類型</label>
        <input type="text" name="account_status" id="account_status" v-model="formData.account_status" disabled>
        <label for=" memberCode">會員編號</label>
        <input type="text" name="memberCode" id=" memberCode" v-model="formData.memberCode" disabled>
        <label for="email">E-mail</label>
        <input type="text" name="email" id="email" v-model="formData.email" disabled>
        <label for="password">密碼</label>
        <input type="password" name="password" id="password" v-model="formData.password" placeholder="••••••••"
          disabled>
        <div class="change-password">
          <a href="#" @click.prevent="askEditPassword">修改密碼
            <AppIcon name="pencil" :size="16" />
          </a>
        </div>


      </div>



      <div class="save col-10">
        <AppButton size="sm" color="primary" @click="askSave()">儲存更變</AppButton>
      </div>

    </div>
  </section>


  <div v-if="showConfirm" class="confirm-modal-overlay">
    <div class="confirm-modal">

      <div class="confirm-modal-spacing">
        <div class="confirm-modal__actions">
          <p>請問是否要更變儲存內容？</p>
          <div class="confirm-content">
            <div class="confirm-modal__cancel" @click="cancelSave()">取消</div>
            <div class="confirm-modal__confirm" @click="confirmSave()">確認</div>
          </div>
        </div>
      </div>

    </div>

  </div>




  <div v-if="showResetPassword" class="confirm-modal-overlay" @click.self="cancelSavePassword">
    <div class="confirm-modal">
      <div class="close-window" @click="cancelSavePassword">
        <AppIcon name="close" :size="20" />
      </div>
      <h4 class="reset-password-title">重設密碼</h4>

      <div class="edit-password">
        <label for="old-pw">舊密碼</label>
        <div class="input-wrapper">
          <input v-model="reSetForm.oldPassword" :type="showOldPassword ? 'text' : 'password'" id="old-password"
            placeholder="請輸入舊密碼" />
          <AppIcon class="auth-card__input-toggle" :name="showOldPassword ? 'eye' : 'eye-off'"
            @click="showOldPassword = !showOldPassword" />
        </div>
      </div>

      <div class="edit-password">
        <label for="new-pw">新密碼</label>
        <div class="input-wrapper">
          <input v-model="reSetForm.newPassword" :type="showNewPassword ? 'text' : 'password'" id="new-pw"
            placeholder="請輸入新密碼" />
          <AppIcon class="auth-card__input-toggle" :name="showNewPassword ? 'eye' : 'eye-off'"
            @click="showNewPassword = !showNewPassword" />
        </div>
      </div>

      <div class="edit-password">
        <label for="confrim-new-pw">確認新密碼</label>
        <div class="input-wrapper">
          <input v-model="reSetForm.confirmPassword" :type="showConfirmPassword ? 'text' : 'password'"
            id="confrim-new-pw" placeholder="請再次輸入新密碼" />
          <AppIcon class="auth-card__input-toggle" :name="showConfirmPassword ? 'eye' : 'eye-off'"
            @click="showConfirmPassword = !showConfirmPassword" />
        </div>
      </div>


      <div class="confirm-modal__actions">
        <div class="confirm-modal__cancel" @click="cancelSavePassword()">取消</div>
        <div class="confirm-modal__confirm" @click="confirmSavePassword()">更新儲存 </div>
      </div>

    </div>

  </div>



</template>


<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

h3 {
  margin-bottom: 8px;
}

.porfile {
  display: flex;
  flex-direction: column;
  padding: 16px;
  border: 1px solid #f5f5f5;
  border-radius: 20px;
  // background-color: aqua;

  @media (max-width: 860px) {
    grid-column: 1 / -1;
  }

}

.profile-main {
  display: flex;
  align-items: flex-start;
  gap: $spacing-md;
  margin-bottom: $spacing-md;

}

.nickname-field {
  display: flex;
  flex-direction: column;
  gap: $spacing-xs;
  flex: 1; // 讓暱稱輸入框吃滿頭貼旁邊剩下的寬度
}

.about-me {
  display: flex;
  flex-direction: column;

}

textarea {
  padding: 5px;
}

.avatar-preview {
  display: block;
  position: relative;
  background-color: #f5f5f5;
  background-size: cover;
  overflow: hidden;
  width: 96px;
  height: 96px;
  border-radius: 100%;
  background-position: center;
  cursor: pointer;
}

.avatar-input {
  position: absolute;
  inset: 0; // 上下左右都貼齊，蓋滿整個 .avatar-preview
  opacity: 0; //隱藏原生檔案的外觀
  cursor: pointer;

}

.avatar-placeholder-icon {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: $neutral-400;
  pointer-events: none;

}

.save-btn {
  background-color: $primary;
  border-radius: 5px;
  color: $neutral-100;
  padding-inline: 16px;

}

.save {
  display: flex;
  flex-direction: row;
  justify-content: center;
  margin-block: $spacing-lg;
}

input {
  height: 40px;
  margin-bottom: $spacing-xs ;

}

textarea {

  resize: none;
}

.confirm-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
}

.confirm-modal {
  background: $neutral-100;
  border-radius: 5px;
  padding: $spacing-xl;

  /* 1. 增加 Modal 寬度，輸入框就會自動變長 */
  width: 420px;
  max-width: 90vw;
  /* 避免在手機版超出螢幕 */

  &__actions {
    display: flex;
    flex-direction: column; 
    gap: $spacing-md;
    margin-top: $spacing-lg;
    /* 增加上方間距 */
  }

  &__cancel,
  &__confirm {
    flex: 1;

    padding: $spacing-md $spacing-lg;

    text-align: center;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-size: $p-sm-size;
    transition: transform .2s ease, box-shadow .2s ease;

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
  }

  &__cancel {
    background: $neutral-200;
    color: $neutral-700;
  }

  &__confirm {
    background: $color-danger;
    color: $neutral-100;
  }
}

.img-cover {
  width: 130px;
  height: 130px;
  background-color: $secondary-100;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;

  & .photo-sticker {
    margin-top: 30px;
    margin-left: 25px;
    transform: scale(1.4);
  }
}

.change-password {
  display: flex;
  justify-content: end;
  color: $neutral-500;

}

.edit-password {
  display: flex;
  flex-direction: column;
}

.reset-password-title {
  font-size: $h6-size ;
}


.reset-password-title {
  margin-block: $spacing-sm;
}

.edit-password {
  margin-block: $spacing-sm;

}

/* 包裹容器設定相對定位 */
.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%;
  /* 依據您的版型調整 */

}

/* 輸入框右側留出寬度，防止文字擠壓到眼睛 */
.input-wrapper input {
  width: 100%;
  padding-right: 40px;
  /* 留空間給眼睛圖示 */
  box-sizing: border-box;
  padding-inline: 4px;
}

/* 眼睛圖示絕對定位到右側中間 */
.auth-card__input-toggle {
  position: absolute;
  right: 12px;
  /* 距離右側邊緣的距離 */
  cursor: pointer;
  color: #666;
  /* 調整視覺顏色 */
  user-select: none;
}

.close-window {
  display: flex;
  justify-content: end;
}

.confirm-content {
  display: flex;
  gap: $spacing-sm;
}


</style>