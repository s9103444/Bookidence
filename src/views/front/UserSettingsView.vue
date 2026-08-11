<script>
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";

export default {
  components: {
    GuildBreadcrumb
  },
  data() {
    return {
      formData: {
        nickname: '',
        introduce: '',
        joinDate: '',
        accountType: '',
        memberId: '',
        password: ''
      },
    }
  },
}
</script>
<template>
  <section class="space">
    <GuildBreadcrumb :items="[
      { label: '❮  公會主頁222', to: `/home` },// guilds/:id 填入目前公會的 id
      { label: '建立讀書會活動222' }
    ]" />
    <div class="my-setting">
      <div class="porfile">
        <h3>個人資料</h3>
        <div class="upload-photo">
          <div class="my-photo">
            <input type="file">
          </div>
        </div>
        <label for="nickname">暱稱</label>
        <input type="text" name="nickname" id="nickname" v-model="formData.nickname" placeholder="請輸入你的暱稱">
        <label for="introduce">自我介紹</label>
        <textarea name="introduce" id="introduce" v-model="formData.introduce" rows="4"></textarea>
        <label for="joinDate">加入時間</label>
        <input type="text" name="joinDate" id="joinDate" v-model="formData.joinDate" disabled>
      </div>
      <div>
        <h3>帳號與安全</h3>
        <div class="porfile">
          <label for="accountType">帳號類型</label>
          <input type="text" name="accountType" id="accountType" v-model="formData.accountType" disabled>
          <label for="memberId">會員編號</label>
          <input type="text" name="memberId" id="memberId" v-model="formData.memberId" disabled>
          <label for="password">密碼</label>
          <input type="text" name="password" id="password" v-model="formData.password" placeholder="••••••••">
          <button type="button">儲存更變</button>
        </div>
      </div>
    </div>
  </section>
</template>


<style scoped lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.space {
  padding-inline: 24px;
  padding-block: 40px;
}

.my-setting {
  display: flex;

}

.porfile {
  display: flex;
  flex-direction: column;
  padding: 16px;

}

.my-photo {
  width: 36px;
  border-radius: 100%;
  background-color: #f5f5f5;
}
</style>