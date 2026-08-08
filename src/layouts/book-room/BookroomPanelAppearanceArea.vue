<template>
  <div>
    <span class="preferance-title">個人外觀設定</span>
  </div>
  <div class="gender-switch">
    <div class="btn-sm" @click="gender = 'female'">
      <img
        class="fem-pic"
        src="../../assets/icons/female.png"
        alt="female-icon"
      />
    </div>
    <div class="btn-sm" @click="gender = 'male'">
      <img src="../../assets/icons/male.png" alt="male-icon" />
    </div>
  </div>
  <div class="female-character" v-show="gender == 'female'">
    <img src="../../assets/images/chracter/female.png" alt="" />
  </div>
  <div class="male-character" v-show="gender == 'male'">
    <img src="../../assets/images/chracter/male.png" alt="" />
  </div>
  <div class="preferance-tabs">
    <button
      class="preferance-tab"
      v-for="tab in tabs"
      :key="tab.id"
      :class="{ tabActive: activatTab === tab.id }"
      @click="activatTab = tab.id"
    >
      {{ tab.name }}
    </button>
  </div>
  <div class="preferance-content">
    <div
      class="wrapper-skin-select-male slc-wrapper"
      v-if="activatTab == 1 && gender === 'male'"
    >
      <button
        class="default-skin-male slc-btn"
        :class="{ selected: selectedSkin === 'default' }"
        @click="selectedSkin = 'default'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/male_df_skin.png"
          alt=""
        />
      </button>
      <button
        class="light-skin-male slc-btn"
        :class="{ selected: selectedSkin === 'light' }"
        @click="selectedSkin = 'light'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/male_light_skin.png"
          alt=""
        />
      </button>
      <button
        class="dark-skin-male slc-btn"
        :class="{ selected: selectedSkin === 'dark' }"
        @click="selectedSkin = 'dark'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/male_dark_skin.png"
          alt=""
        />
      </button>
    </div>

    <div
      class="wrapper-skin-select-female slc-wrapper"
      v-if="activatTab == 1 && gender === 'female'"
    >
      <button
        class="default-skin-female slc-btn"
        :class="{ selected: selectedSkin === 'default' }"
        @click="selectedSkin = 'default'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/female_df_skin.png"
          alt=""
        />
      </button>
      <button
        class="light-skin-female slc-btn"
        :class="{ selected: selectedSkin === 'light' }"
        @click="selectedSkin = 'light'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/female_light_skin.png"
          alt=""
        />
      </button>
      <button
        class="dark-skin-female slc-btn"
        :class="{ selected: selectedSkin === 'dark' }"
        @click="selectedSkin = 'dark'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/female_dark_skin.png"
          alt=""
        />
      </button>
    </div>

    <div
      class="wrapper-eyes-select-male slc-wrapper"
      v-if="activatTab == 2 && gender === 'male'"
    >
      <button
        class="green-eyes-male slc-btn"
        :class="{ selected: selectedEyes === 'green' }"
        @click="selectedEyes = 'green'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/male_green_eyes.png"
          alt=""
        />
      </button>
      <button
        class="blue-eyes-male slc-btn"
        :class="{ selected: selectedEyes === 'blue' }"
        @click="selectedEyes = 'blue'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/male_blue_eyes.png"
          alt=""
        />
      </button>
      <button
        class="black-eyes-male slc-btn"
        :class="{ selected: selectedEyes === 'black' }"
        @click="selectedEyes = 'black'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/male_black_eyes.png"
          alt=""
        />
      </button>
    </div>

    <div
      class="wrapper-eyes-select-female slc-wrapper"
      v-if="activatTab == 2 && gender === 'female'"
    >
      <button
        class="green-eyes-female slc-btn"
        :class="{ selected: selectedEyes === 'green' }"
        @click="selectedEyes = 'green'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/female_green_eyes.png"
          alt=""
        />
      </button>
      <button
        class="blue-eyes-female slc-btn"
        :class="{ selected: selectedEyes === 'blue' }"
        @click="selectedEyes = 'blue'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/female_blue_eyes.png"
          alt=""
        />
      </button>
      <button
        class="black-eyes-female slc-btn"
        :class="{ selected: selectedEyes === 'black' }"
        @click="selectedEyes = 'black'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/female_black_eyes.png"
          alt=""
        />
      </button>
    </div>

    <div
      class="wrapper-hair-select-male slc-wrapper"
      v-if="activatTab == 3 && gender === 'male'"
    >
      <button
        class="default-hair-male slc-btn"
        :class="{ selected: selectedHair === 'default' }"
        @click="selectedHair = 'default'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/male_df_hair.png"
          alt=""
        />
      </button>
      <button
        class="blue-hair-male slc-btn"
        :class="{ selected: selectedHair === 'blue' }"
        @click="selectedHair = 'blue'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/male_blue_hair.png"
          alt=""
        />
      </button>
      <button
        class="black-hair-male slc-btn"
        :class="{ selected: selectedHair === 'black' }"
        @click="selectedHair = 'black'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/male_black_hair.png"
          alt=""
        />
      </button>
    </div>

    <div
      class="wrapper-hair-select-female slc-wrapper"
      v-if="activatTab == 3 && gender === 'female'"
    >
      <button
        class="default-hair-female slc-btn"
        :class="{ selected: selectedHair === 'default' }"
        @click="selectedHair = 'default'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/female_df_hair.png"
          alt=""
        />
      </button>
      <button
        class="blue-hair-female slc-btn"
        :class="{ selected: selectedHair === 'blue' }"
        @click="selectedHair = 'blue'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/female_blue_hair.png"
          alt=""
        />
      </button>
      <button
        class="black-hair-female slc-btn"
        :class="{ selected: selectedHair === 'black' }"
        @click="selectedHair = 'black'"
      >
        <img
          src="../../assets/images/appear/character-slecte-for-bookroom/female_black_hair.png"
          alt=""
        />
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      tabs: [
        { id: 1, name: "選擇膚色" },
        { id: 2, name: "選擇瞳色" },
        { id: 3, name: "選擇髮色" },
      ],
      activatTab: 1,
      gender: "male",
      selectedSkin: "default",
      selectedEyes: "green",
      selectedHair: "default",
    };
  },
};
</script>

<style lang="scss" scoped>
@use "@/assets/scss/abstracts/variables" as *;

.preferance-title {
  color: $brown;
  font-weight: $heading-weight;
}

.preferance-tabs {
  display: flex;
  gap: 10px;
  margin-left: 2px;
  font-size: $label-sm-size;
  font-weight: $heading-weight;
}

.preferance-tab {
  color: #f0d9a8;
  position: relative;
  background-color: #6b5b5b;
  border: none;
  border-right: 2px solid #6b5b5b;
  padding-inline: 12px;
  padding-block: 2px;
  z-index: 5;
}

.preferance-tab::before {
  content: "";
  position: absolute;
  top: -2px;
  left: 0px;
  /* 故意內縮，跟左邊的線錯開 → 缺角在這裡 */
  right: -8px;
  /* 對齊到 border-right 的外緣，右上角密合 */
  width: 100%;
  height: 2px;
  background-color: #6b5b5b;
}

.preferance-tab::after {
  content: "";
  position: absolute;
  top: 0;
  /* 從按鈕頂端算起，比 ::before 的 -8px 低一截 → 缺角 */
  left: -2px;
  bottom: 0;
  width: 2px;
  background-color: #6b5b5b;
}

.tabActive {
  background-color: #f0d9a8;
  color: $brown;
}

.preferance-content {
  position: relative;
  width: 100%;
  height: 200px;
  border-left: 2px solid #6b5b5b;
  border-bottom: 2px solid #6b5b5b;
  background-color: #f0d9a8;
  box-shadow: inset 0 -4px 0 0 rgba(149, 109, 90, 0.3);
}

.preferance-content::before {
  content: "";
  position: absolute;
  top: -2px;
  left: 0%;
  right: 0px;
  height: 2px;
  background-color: #6b5b5b;
}

.preferance-content::after {
  content: "";
  position: absolute;
  top: 0;
  right: -2px;
  bottom: 0;
  width: 2px;
  background-color: #6b5b5b;
}

.btn-sm {
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 30px;
  height: 30px;
  background-image: url("../../assets/button/panel_burron_sm.png");
  background-repeat: no-repeat; /* 圖片不要重複貼滿 */
  background-size: contain; /* 或 contain，圖片縮放方式 */
  background-position: center;
  & img {
    width: 60%;
    height: auto;
  }
  & .fem-pic {
    width: 45%;
    height: auto;
  }
}
.btn-sm:hover {
  filter: brightness(0.9);
}
.btn-sm:active {
  transform: scale(0.95);
  filter: brightness(0.8);
}

.slc-btn {
  position: relative;
  width: 100px;
  margin-top: 24px;
}
.slc-btn:hover {
  filter: brightness(0.98);
  transform: translateY(1px);
}
.slc-btn:active {
  filter: brightness(0.95);
}
.slc-btn.selected {
  transform: translateY(2px);
  &::after {
    content: "";
    position: absolute;
    inset: 0;
    background-image: url(../../assets/images/appear/character-slecte-for-bookroom/selected-frame.png);
    background-size: contain;
    background-repeat: no-repeat;
    z-index: 5;
  }
}

.slc-wrapper {
  display: flex;
  gap: 20px;
  margin-left: 28px;
}
</style>
