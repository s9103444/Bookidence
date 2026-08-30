<script>
import { API_BASE } from '@/common/api';
import { mapState } from 'pinia';
import { useUserStore } from '@/stores/user';
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import AppIcon from "@/components/common/AppIcon.vue";
import PhotoSticker from "../../components/front/PhotoSticker.vue";

export default {
  components: {
    GuildBreadcrumb,
    AppIcon,
    PhotoSticker,
  },
  data() {
    return {
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
      avatarPreview: '',
    }
  },
  methods: {
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
    }


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
      this.memberCode = result.user.member_code;
      this.nickname = result.user.nickname;
      this.bio = result.user.bio;
      this.userId = userStore.userId;
    }
  },
}
</script>
<template>
  <section class="space">

    <div class="my-setting container-content  ">
      <GuildBreadcrumb class="col-10" :items="[
        { label: '❮  首頁', to: `/` },// guilds/:id 填入目前公會的 id
        { label: '使用者設定' }
      ]" />
      <div class="porfile col-5 ">
        <h3>個人資料</h3>
        <div class="profile-main">
          <div class="img-cover">
            <!-- <label class="avatar-preview" :style="{ backgroundImage: avatarPreview ? `url(${avatarPreview})` : '' }"> -->
              <PhotoSticker class="photo-sticker" :userId="userId" :width="80" />

              <input type="file" ref="avatarInput" @change="handleAvatarChange" accept="image/*" class="avatar-input">
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
        <input type="password" name="password" id="password" v-model="formData.password" placeholder="••••••••">


      </div>

      <div class="save col-10">
        <button type="button" class="save-btn" @click="askSave()">儲存更變</button>
      </div>

    </div>
  </section>


  <div v-if="showConfirm" class="confirm-modal-overlay">
    <div class="confirm-modal">

      <div class="confirm-modal-spacing">

        <div class="confirm-modal__actions">
          <p>請問是否要更變儲存內容？</p>
          <div class="confirm-modal__cancel" @click="cancelSave()">取消</div>
          <div class="confirm-modal__confirm" @click="confirmSave()">確認</div>
        </div>

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
textarea{
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
  min-width: 280px;

  &__text {
    margin: 0 0 $spacing-lg;
    font-size: $p-md-size;
    color: $neutral-800;
  }

  &__actions {
    display: flex;
    gap: $spacing-md;
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
  & .photo-sticker {
    margin-top: 30px;
    margin-left: 25px;
    transform: scale(1.4);
  }
}
</style>