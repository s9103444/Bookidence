<template>
  <div class="layout">
    <div class="header">
      <div class="img-cover">
        <!-- <img src="../../assets/images/member-selfie.png" alt="" /> -->
        <PhotoSticker class="photo-sticker" :userId="userId" :width="80" />
      </div>
      <div class="member-info">
        <div>
          <div class="member-title">
            <span class="label">會員ID</span
            ><span class="member-id">{{ memberCode }}</span>
          </div>
          <div>
            <span class="member-name">{{ nickname }}</span>
            <!-- <br />
            <span class="lv">Lv.</span>
            <span class="lv-num">4</span> -->
          </div>
        </div>
        <div class="member-achievement">
          <div class="member-title">
            <span class="label">獲得成就</span>
          </div>
          <div class="achive-img-cover">
            <div class="img-wrapper">
              <img
                src="../../assets/images/achievement-badges/new-member.png"
                alt=""
              />
            </div>
            <div class="img-wrapper">
              <img
                src="../../assets/images/achievement-badges/first-update.png"
                alt=""
              />
            </div>
            <div class="img-wrapper">
              <img
                src="../../assets/images/achievement-badges/first-participate.png"
                alt=""
              />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="intro-content">
      <span class="sub-title">個人介紹</span>
      <p class="intro-context">
        {{ bio }}
      </p>
    </div>
    <div class="achievement-content">
      <span class="sub-title">個人成就</span>
      <div class="achievement-info-wrapper">
        <div class="achievement-info">
          <div class="achieve-img-cover">
            <img
              src="../../assets/images/achievement-badges/new-member.png"
              alt=""
            />
          </div>
          <span class="achievement-title">旅程初啟</span>
          <span class="achievement-subtitle">踏入書香世界的第一步！</span>
        </div>
        <div class="achievement-info">
          <div class="achieve-img-cover">
            <img
              src="../../assets/images/achievement-badges/first-participate.png"
              alt=""
            />
          </div>
          <span class="achievement-title">初次典藏</span>
          <span class="achievement-subtitle">將第一本好書放入書架。</span>
        </div>
        <div class="achievement-info">
          <div class="achieve-img-cover">
            <img
              src="../../assets/images/achievement-badges/first-update.png"
              alt=""
            />
          </div>
          <span class="achievement-title">文字共鳴</span>
          <span class="achievement-subtitle">首次參與聚會，與夥伴交流。</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import PhotoSticker from "../../components/front/PhotoSticker.vue";
import { useUserStore } from "../../stores/user.js";
import { API_BASE } from "../../common/api.js";
export default {
  components: {
    PhotoSticker,
  },
  data() {
    return {
      userId: "",
      memberCode: "",
      nickname: "",
      bio: "",
    };
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
};
</script>

<style lang="scss" scoped>
@use "@/assets/scss/abstracts/variables" as *;

.layout {
  height: 100%;
  overflow-y: auto;
  scrollbar-width: none; // Firefox
  -ms-overflow-style: none; // 舊版 IE/Edge

  &::-webkit-scrollbar {
    // Chrome / Safari /新版 Edge
    display: none;
  }
}
.header {
  display: flex;
  gap: 20px;
}
.intro-content,
.achievement-content {
  margin-top: 36px;
}
.img-cover {
  width: 130px;
  height: 130px;
  background-color: $secondary-100;
  border-radius: 5px;
  overflow: hidden;
  & .photo-sticker {
    margin-top: 30px;
    margin-left: 25px;
    transform: scale(1.4);
  }
}
.member-name {
  font-size: $label-lg-size;
  color: $brown;
  font-weight: $heading-weight;
}
.member-info {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 10px;
}
.member-title,
.member-achievement {
  margin-bottom: 4px;
}
.label {
  display: inline-block;
  margin-right: 4px;
  font-size: $label-xxs-size;
  font-weight: $text-weight;
  padding: 2px 8px;
  color: $brown;
  background-color: #eedbae;
  border: 1px solid rgb(196, 182, 138);
  border-radius: $btn-radius-rnd;
}
.lv {
  display: inline-block;
  margin-left: 6px;
  color: $brown;
  font-weight: $heading-weight;
}
.lv-num {
  display: inline-block;
  color: $brown;
  font-weight: $heading-weight;
}
.member-id {
  color: $brown-light;
  font-size: $label-sm-size;
  font-weight: $heading-weight;
}
.achive-img-cover {
  display: flex;
  margin-top: 6px;
  align-items: center;
  gap: 6px;
  & .img-wrapper {
    width: 24px;
    height: 24px;
    & img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }
  }
}
.achievement-info-wrapper {
  display: flex;
  gap: 30px;
  margin-top: 6px;
}
.achievement-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0;
}
.sub-title {
  color: $brown;
  font-weight: $heading-weight;
  display: inline-block;
  margin-bottom: 0;
}
.achievement-title {
  display: inline-block;
  margin-top: 10px;
  color: $brown;
  font-size: $p-xs-size;
  font-weight: $heading-weight;
}
.intro-context {
  color: $brown;
  font-size: $p-xs-size;
  line-height: $text-line-height;
  margin-top: 10px;
  margin-inline: 4px;
}
.achievement-subtitle {
  color: $brown;
  font-size: $label-xxs-size;
  line-height: $text-line-height;
  width: 90px;
  text-align: center;
  margin-top: 2px;
}
.achieve-img-cover {
  width: 80px;
  height: 80px;
  border: 1px solid rgb(196, 182, 138);
  background-color: #eedbae;
  border-radius: 10px;
  padding: 14px;
  display: flex;
  align-items: center;
  justify-content: center;

  & img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }
}

//RWD
@media (max-width: 960px) {
  .layout {
    width: 96%;
    margin-inline: auto;
    padding-inline: $spacing-sm;
  }

  .header {
    gap: 12px;
    align-items: center;
  }
  .img-cover {
    width: 120px;
    height: 120px;
    & .photo-sticker {
      transform: scale(1.3);
      margin-top: 20px;
      margin-left: 20px;
    }
  }
  .member-name {
    font-size: $label-lg-size;
  }
  .achievement-info-wrapper {
    gap: $spacing-lg;
  }

  .achievement-subtitle {
    width: 76px;
  }
  .achievement-info-wrapper {
    gap: 10px;
  }
  .achieve-img-cover {
    width: 60px;
    height: 60px;
    padding: 10px;
  }
}
</style>
