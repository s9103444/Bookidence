<template>
  <div class="layout">
    <div class="header">
      <div class="img-cover">
        <img src="../../assets/images/member-selfie.png" alt="" />
      </div>
      <div class="member-info">
        <div>
          <div class="member-title">
            <span class="label">會員ID</span
            ><span class="member-id">BKD00001</span>
          </div>
          <div>
            <span class="member-name">小森讀取中</span>
            <span class="lv">Lv.</span>
            <span class="lv-num">4</span>
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
        嗨大家！我是小森，一個極度重度依賴文字療癒的讀書人。平時最喜歡在深夜或閒暇時，窩進奇幻冒險的世界裡探險，也很愛讀能撫平日常焦慮的溫馨散文與經典文學。
        最近希望能多跨出舒適圈，參加各種線下或線上的讀書會活動，跟大家一起交流書中的精采片段與生活感悟！期待能在這裡結識更多愛書的夥伴，一起在頁面翻動間尋找溫度與靈感，多多指教囉！
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
export default {};
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
.member-name {
  font-size: 20px;
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
  color: $neutral-100;
  background-color: $brown;
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
}
.achive-img-cover {
  display: flex;
  align-items: center;
  gap: 8px;
  & .img-wrapper {
    width: 30px;
    height: 30px;
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
}
.achievement-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}
.sub-title {
  color: $brown;
  font-weight: $heading-weight;
  display: inline-block;
  margin-bottom: 6px;
}
.achievement-title {
  display: inline-block;
  margin-top: 4px;
  color: $brown;
  font-size: $p-xs-size;
  font-weight: $heading-weight;
}
.intro-context {
  color: $brown;
  font-size: $p-xs-size;
  line-height: 1.6;
}
.achievement-subtitle {
  color: $brown;
  font-size: 10px;
  line-height: 1.2;
  width: 90px;
  text-align: center;
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
