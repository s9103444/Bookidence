<script>
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import AppIcon from "@/components/common/AppIcon.vue";

export default {
  components: {
    GuildBreadcrumb,
    AppIcon,
  },
  data() {
    return {
      formData: {
        nickname: '',
        introduce: '',
        joinDate: '2026/5/1',
        accountType: '一般會員',
        memberId: 'BKI-2024-0412-0827',
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

  }
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
          <div class="avatar-upload">
            <label class="avatar-preview" :style="{ backgroundImage: avatarPreview ? `url(${avatarPreview})` : '' }"><AppIcon v-if="!avatarPreview" name="image" :size="24" class="avatar-placeholder-icon" />
              <input type="file" ref="avatarInput" @change="handleAvatarChange" accept="image/*" class="avatar-input">
            </label>
          </div>
          <div class="nickname-field">
            <label for="nickname">暱稱</label>
            <input type="text" name="nickname" id="nickname" v-model="formData.nickname" placeholder="請輸入你的暱稱">
          
          <div class="about-me">
            <label for="introduce">自我介紹</label>
            <textarea name="introduce" id="introduce" v-model="formData.introduce" rows=" 7"></textarea>
            <label for="joinDate">加入時間</label>
            <input type="text" name="joinDate" id="joinDate" v-model="formData.joinDate" disabled>
          </div>
          </div>
        </div>
      </div>

  
    <div class="porfile col-5">
      <h3>帳號與安全</h3>
      <label for="accountType">帳號類型</label>
      <input type="text" name="accountType" id="accountType" v-model="formData.accountType" disabled>
      <label for="memberId">會員編號</label>
      <input type="text" name="memberId" id="memberId" v-model="formData.memberId" disabled>
      <label for="email">E-mail</label>
      <input type="text" name="email" id="email" v-model="formData.email" disabled>
      <label for="password">密碼</label>
      <input type="password" name="password" id="password" v-model="formData.password" placeholder="••••••••">
     
    
    </div>

     <div class="save col-10">
      <button type="button" class="save-btn">儲存更變</button>
    </div>

    </div>
  </section>
</template>


<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

h3{
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
.profile-main{
  display: flex;
  align-items: flex-start;
  gap: $spacing-md;
  margin-bottom: $spacing-md;

}
.nickname-field {
  display: flex;
  flex-direction: column;
  gap: $spacing-xs;
  flex: 1;   // 讓暱稱輸入框吃滿頭貼旁邊剩下的寬度
}
.about-me {
  display: flex;
  flex-direction: column;

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

.avatar-placeholder-icon{
  position: absolute;
  top: 50%;
  left:50%;
  transform: translate(-50%, -50%);
  color: $neutral-400;
  pointer-events: none;
  
}

.save-btn{
  background-color:$primary;
  border-radius: 5px;
  color:$neutral-100;
  padding-inline:16px ;

}

.save{
  display:flex ;
  flex-direction: row;
  justify-content: center;
}
input{
  height: 40px;
  margin-bottom:$spacing-xs ;
 
}
textarea {
  
  resize: none;
}
</style>