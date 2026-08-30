<template>
  <div class="layout">
    <div class="preferance-title">
      <span>個人外觀設定</span>
    </div>
    <!-- 切換性別按鈕 -->
    <div class="gender-switch">
      <div
        class="btn-sm"
        :class="{ pressed: gender === 'female' }"
        @click="selectGender('female')"
      >
        <img
          class="fem-pic"
          :src="gender === 'female' ? femaleIconPressed : femaleIcon"
          alt="female-icon"
        />
      </div>
      <div
        class="btn-sm"
        :class="{ pressed: gender === 'male' }"
        @click="selectGender('male')"
      >
        <img
          :src="gender === 'male' ? maleIconPressed : maleIcon"
          alt="male-icon"
        />
      </div>
    </div>
    <!-- 角色外觀預覽 -->
    <div class="ch-preview female-character" v-show="gender == 'female'">
      <img
        class="ch-element"
        v-show="selectedSkin == 'fs2'"
        src="../../assets/images/appear/character-for-register/female_skin_medium.png"
        alt="df-skin"
      />
      <img
        class="ch-element"
        v-show="selectedSkin == 'fs1'"
        src="../../assets/images/appear/character-for-register/female_skin_light.png"
        alt="light-skin"
      />
      <img
        class="ch-element"
        v-show="selectedSkin == 'fs3'"
        src="../../assets/images/appear/character-for-register/female_skin_dark.png"
        alt="dark-skin"
      />
      <img
        class="ch-element"
        v-show="selectedEyes == 'fe3'"
        src="../../assets/images/appear/character-for-register/female_eyes_green.png"
        alt="green-eyes"
      />
      <img
        class="ch-element"
        v-show="selectedEyes == 'fe2'"
        src="../../assets/images/appear/character-for-register/female_eyes_blue.png"
        alt="blue-eyes"
      />
      <img
        class="ch-element"
        v-show="selectedEyes == 'fe1'"
        src="../../assets/images/appear/character-for-register/female_eyes_black.png"
        alt="black-eyes"
      />
      <img
        class="ch-element"
        v-show="selectedHair == 'fh3'"
        src="../../assets/images/appear/character-for-register/female_hair_brown.png"
        alt="df-hair"
      />
      <img
        class="ch-element"
        v-show="selectedHair == 'fh2'"
        src="../../assets/images/appear/character-for-register/female_hair_blue.png"
        alt="blue-hair"
      />
      <img
        class="ch-element"
        v-show="selectedHair == 'fh1'"
        src="../../assets/images/appear/character-for-register/female_hair_black.png"
        alt="black-hair"
      />
    </div>
    <div class="ch-preview male-character" v-show="gender == 'male'">
      <img
        class="ch-element"
        v-show="selectedSkin == 'ms2'"
        src="../../assets/images/appear/character-for-register/male_skin_medium.png"
        alt="df-skin"
      />
      <img
        class="ch-element"
        v-show="selectedSkin == 'ms1'"
        src="../../assets/images/appear/character-for-register/male_skin_light.png"
        alt="light-skin"
      />
      <img
        class="ch-element"
        v-show="selectedSkin == 'ms3'"
        src="../../assets/images/appear/character-for-register/male_skin_dark.png"
        alt="dark-skin"
      />
      <img
        class="ch-element"
        v-show="selectedEyes == 'me3'"
        src="../../assets/images/appear/character-for-register/male_eyes_green.png"
        alt="green-eyes"
      />
      <img
        class="ch-element"
        v-show="selectedEyes == 'me2'"
        src="../../assets/images/appear/character-for-register/male_eyes_blue.png"
        alt="blue-eyes"
      />
      <img
        class="ch-element"
        v-show="selectedEyes == 'me1'"
        src="../../assets/images/appear/character-for-register/male_eyes_black.png"
        alt="black-eyes"
      />
      <img
        class="ch-element"
        v-show="selectedHair == 'mh3'"
        src="../../assets/images/appear/character-for-register/male_hair_brown.png"
        alt="df-hair"
      />
      <img
        class="ch-element"
        v-show="selectedHair == 'mh2'"
        src="../../assets/images/appear/character-for-register/male_hair_blue.png"
        alt="blue-hair"
      />
      <img
        class="ch-element"
        v-show="selectedHair == 'mh1'"
        src="../../assets/images/appear/character-for-register/male_hair_black.png"
        alt="black-hair"
      />
    </div>
    <div class="preferance-wrapper">
      <div class="preferance-select-bar">
        <!-- 外觀切換按鈕列 -->
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
        <!-- 外觀切換選單面板 -->
        <div class="preferance-content">
          <!-- 男生部分：皮膚 -->
          <div
            class="wrapper-skin-select-male slc-wrapper"
            v-if="activatTab == 1 && gender === 'male'"
          >
            <button
              class="default-skin-male slc-btn"
              :class="{ selected: selectedSkin === 'ms2' }"
              @click="selectedSkin = 'ms2'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/male_df_skin.png"
                alt=""
              />
            </button>
            <button
              class="light-skin-male slc-btn"
              :class="{ selected: selectedSkin === 'ms1' }"
              @click="selectedSkin = 'ms1'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/male_light_skin.png"
                alt=""
              />
            </button>
            <button
              class="dark-skin-male slc-btn"
              :class="{ selected: selectedSkin === 'ms3' }"
              @click="selectedSkin = 'ms3'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/male_dark_skin.png"
                alt=""
              />
            </button>
          </div>
          <!-- 女生部分：皮膚 -->
          <div
            class="wrapper-skin-select-female slc-wrapper"
            v-if="activatTab == 1 && gender === 'female'"
          >
            <button
              class="default-skin-female slc-btn"
              :class="{ selected: selectedSkin === 'fs2' }"
              @click="selectedSkin = 'fs2'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/female_df_skin.png"
                alt=""
              />
            </button>
            <button
              class="light-skin-female slc-btn"
              :class="{ selected: selectedSkin === 'fs1' }"
              @click="selectedSkin = 'fs1'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/female_light_skin.png"
                alt=""
              />
            </button>
            <button
              class="dark-skin-female slc-btn"
              :class="{ selected: selectedSkin === 'fs3' }"
              @click="selectedSkin = 'fs3'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/female_dark_skin.png"
                alt=""
              />
            </button>
          </div>
          <!-- 男生部分：眼睛 -->
          <div
            class="wrapper-eyes-select-male slc-wrapper"
            v-if="activatTab == 2 && gender === 'male'"
          >
            <button
              class="green-eyes-male slc-btn"
              :class="{ selected: selectedEyes === 'me3' }"
              @click="selectedEyes = 'me3'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/male_green_eyes.png"
                alt=""
              />
            </button>
            <button
              class="blue-eyes-male slc-btn"
              :class="{ selected: selectedEyes === 'me2' }"
              @click="selectedEyes = 'me2'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/male_blue_eyes.png"
                alt=""
              />
            </button>
            <button
              class="black-eyes-male slc-btn"
              :class="{ selected: selectedEyes === 'me1' }"
              @click="selectedEyes = 'me1'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/male_black_eyes.png"
                alt=""
              />
            </button>
          </div>
          <!-- 女生部分：眼睛 -->
          <div
            class="wrapper-eyes-select-female slc-wrapper"
            v-if="activatTab == 2 && gender === 'female'"
          >
            <button
              class="green-eyes-female slc-btn"
              :class="{ selected: selectedEyes === 'fe3' }"
              @click="selectedEyes = 'fe3'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/female_green_eyes.png"
                alt=""
              />
            </button>
            <button
              class="blue-eyes-female slc-btn"
              :class="{ selected: selectedEyes === 'fe2' }"
              @click="selectedEyes = 'fe2'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/female_blue_eyes.png"
                alt=""
              />
            </button>
            <button
              class="black-eyes-female slc-btn"
              :class="{ selected: selectedEyes === 'fe1' }"
              @click="selectedEyes = 'fe1'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/female_black_eyes.png"
                alt=""
              />
            </button>
          </div>
          <!-- 男生部分：頭髮 -->
          <div
            class="wrapper-hair-select-male slc-wrapper"
            v-if="activatTab == 3 && gender === 'male'"
          >
            <button
              class="default-hair-male slc-btn"
              :class="{ selected: selectedHair === 'mh3' }"
              @click="selectedHair = 'mh3'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/male_df_hair.png"
                alt=""
              />
            </button>
            <button
              class="blue-hair-male slc-btn"
              :class="{ selected: selectedHair === 'mh2' }"
              @click="selectedHair = 'mh2'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/male_blue_hair.png"
                alt=""
              />
            </button>
            <button
              class="black-hair-male slc-btn"
              :class="{ selected: selectedHair === 'mh1' }"
              @click="selectedHair = 'mh1'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/male_black_hair.png"
                alt=""
              />
            </button>
          </div>
          <!-- 女生部分：頭髮 -->
          <div
            class="wrapper-hair-select-female slc-wrapper"
            v-if="activatTab == 3 && gender === 'female'"
          >
            <button
              class="default-hair-female slc-btn"
              :class="{ selected: selectedHair === 'fh3' }"
              @click="selectedHair = 'fh3'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/female_df_hair.png"
                alt=""
              />
            </button>
            <button
              class="blue-hair-female slc-btn"
              :class="{ selected: selectedHair === 'fh2' }"
              @click="selectedHair = 'fh2'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/female_blue_hair.png"
                alt=""
              />
            </button>
            <button
              class="black-hair-female slc-btn"
              :class="{ selected: selectedHair === 'fh1' }"
              @click="selectedHair = 'fh1'"
            >
              <img
                src="../../assets/images/appear/character-slecte-for-bookroom/female_black_hair.png"
                alt=""
              />
            </button>
          </div>
        </div>
      </div>
      <AppButton
        class="comfirm-btn"
        color="brown"
        size="xs"
        @click="confirmChangeAppearance"
        >確認變更</AppButton
      >
    </div>
  </div>
</template>

<script>
import AppButton from "../../components/common/AppButton.vue";
import femaleIcon from "../../assets/icons/female.png";
import femaleIconPressed from "../../assets/icons/female2.png";
import maleIcon from "../../assets/icons/male.png";
import maleIconPressed from "../../assets/icons/male2.png";
import { API_BASE } from "../../common/api.js";
import { useUserStore } from "../../stores/user.js";

export default {
  components: {
    AppButton,
  },
  data() {
    return {
      femaleIcon,
      femaleIconPressed,
      maleIcon,
      maleIconPressed,
      tabs: [
        { id: 1, name: "選擇膚色" },
        { id: 2, name: "選擇瞳色" },
        { id: 3, name: "選擇髮色" },
      ],
      activatTab: 1,
      gender: "female",
      selectedSkin: "fs2",
      selectedEyes: "fe3",
      selectedHair: "fh3",
    };
  },
  async created() {
    const userStore = useUserStore();
    const res = await fetch(`${API_BASE}/user_appear.php`, {
      method: "GET",
      headers: {
        Authorization: `Bearer ${userStore.token}`,
      },
    });
    const result = await res.json();
    if (result.success) {
      this.gender = result.data.gender;
      this.selectedHair = result.data.hair;
      this.selectedEyes = result.data.eyes;
      this.selectedSkin = result.data.skin;
    }
  },
  methods: {
    async confirmChangeAppearance() {
      let r = window.confirm("是否確認變更外觀？");
      if (r) {
        const userStore = useUserStore();
        const res = await fetch(`${API_BASE}/user_appear.php`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json; charset=utf8",
            Authorization: `Bearer ${userStore.token}`,
          },
          body: JSON.stringify({
            gender: this.gender,
            selectedHair: this.selectedHair,
            selectedEyes: this.selectedEyes,
            selectedSkin: this.selectedSkin,
          }),
        });
        const result = await res.json();
        console.log(result); //主控報錯
      }
    },
    selectGender(g) {
      this.gender = g;
      if (g === "female") {
        this.selectedSkin = "fs2";
        this.selectedEyes = "fe3";
        this.selectedHair = "fh3";
      } else {
        this.selectedSkin = "ms2";
        this.selectedEyes = "me3";
        this.selectedHair = "mh3";
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@use "@/assets/scss/abstracts/variables" as *;

.layout {
  position: relative;
  width: 100%;
  height: 100%;
  display: flex;
  overflow-y: auto;
  flex-direction: column;
  justify-content: center;
  scrollbar-width: none; // Firefox
  -ms-overflow-style: none; // 舊版 IE/Edge

  &::-webkit-scrollbar {
    // Chrome / Safari /新版 Edge
    display: none;
  }
}

.gender-switch {
  position: absolute;
  display: flex;
  flex-direction: column;
  gap: 4px;
  right: 10px;
  top: 10px;
}

.preferance-title {
  display: block;
  margin-left: 6px;
  color: $brown;
  font-weight: $heading-weight;
}

.ch-preview.female-character {
  position: relative;
  margin-inline: auto;
  width: 150px;
  flex-shrink: 0;
  aspect-ratio: 146 / 222;
  background-image: url(../../assets/images/appear/female.png);
  background-repeat: no-repeat; /* 圖片不要重複貼滿 */
  background-size: contain; /* 或 contain，圖片縮放方式 */
  background-position: center;
}

.ch-preview.male-character {
  position: relative;
  margin-inline: auto;
  width: 150px;
  flex-shrink: 0;
  aspect-ratio: 146 / 222;
  background-image: url(../../assets/images/appear/male.png);
  background-repeat: no-repeat; /* 圖片不要重複貼滿 */
  background-size: contain; /* 或 contain，圖片縮放方式 */
  background-position: center;
}

.ch-element {
  position: absolute;
}

/* 女生角色：9 張圖依序為 膚色(df/light/dark) → 瞳色(green/blue/black) → 髮色(df/blue/black) */
/* 座標是依裁切圖比例估的起始值，非精確量測，需再視覺比對微調 */
.ch-preview.female-character {
  & .ch-element:nth-child(1) {
    top: 37%;
    left: 24.5%;
    width: 60%;
    height: auto;
  } /* df-skin */
  & .ch-element:nth-child(2) {
    top: 37%;
    left: 24.5%;
    width: 60%;
    height: auto;
  } /* light-skin */
  & .ch-element:nth-child(3) {
    top: 37%;
    left: 24.7%;
    width: 58.5%;
    height: auto;
  } /* dark-skin */
  & .ch-element:nth-child(4) {
    top: 44.5%;
    left: 44%;
    width: 30.8%;
    height: auto;
  } /* green-eyes */
  & .ch-element:nth-child(5) {
    top: 44.5%;
    left: 44%;
    width: 30.8%;
    height: auto;
  } /* blue-eyes */
  & .ch-element:nth-child(6) {
    top: 44.5%;
    left: 44%;
    width: 30.8%;
    height: auto;
  } /* black-eyes */
  & .ch-element:nth-child(7) {
    top: 26.5%;
    left: 0%;
    width: 100%;
    height: auto;
  } /* df-hair */
  & .ch-element:nth-child(8) {
    top: 26.5%;
    left: 0%;
    width: 100%;
    height: auto;
  } /* blue-hair */
  & .ch-element:nth-child(9) {
    top: 26.5%;
    left: 0%;
    width: 100%;
    height: auto;
  } /* black-hair */
}

/* 男生角色：9 張圖順序同上 */
/* 座標是依裁切圖比例估的起始值，非精確量測，需再視覺比對微調 */
.ch-preview.male-character {
  & .ch-element:nth-child(1) {
    top: 36.5%;
    left: 26%;
    width: 51.5%;
    height: auto;
  } /* df-skin */
  & .ch-element:nth-child(2) {
    top: 36.5%;
    left: 26%;
    width: 51.5%;
    height: auto;
  } /* light-skin */
  & .ch-element:nth-child(3) {
    top: 36.2%;
    left: 26%;
    width: 51.5%;
    height: auto;
  } /* dark-skin */
  & .ch-element:nth-child(4) {
    top: 41.5%;
    left: 42.5%;
    width: 28.3%;
    height: auto;
  } /* green-eyes */
  & .ch-element:nth-child(5) {
    top: 41.5%;
    left: 42.5%;
    width: 28.3%;
    height: auto;
  } /* blue-eyes */
  & .ch-element:nth-child(6) {
    top: 41.5%;
    left: 42.5%;
    width: 28.3%;
    height: auto;
  } /* black-eyes */
  & .ch-element:nth-child(7) {
    top: 0.5%;
    left: 8.5%;
    width: 89%;
    height: auto;
  } /* df-hair */
  & .ch-element:nth-child(8) {
    top: 0.5%;
    left: 8.5%;
    width: 89%;
    height: auto;
  } /* blue-hair */
  & .ch-element:nth-child(9) {
    top: 0.5%;
    left: 8.5%;
    width: 89%;
    height: auto;
  } /* black-hair */
}
.preferance-wrapper {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}
.preferance-select-bar {
  flex: 1;
}
.preferance-tabs {
  display: flex;
  gap: 10px;
  margin-top: 10px;
  margin-left: 2px;
  font-size: $label-sm-size;
  font-weight: $heading-weight;
}

.preferance-tab {
  color: #f0d9a8;
  position: relative;
  background-color: $brown;
  border-right: 2px solid $brown;
  padding-inline: 12px;
  padding-block: 4px;
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
  width: 98%;
  padding: 16px;
  border-left: 2px solid #6b5b5b;
  border-bottom: 2px solid #6b5b5b;
  background-color: #f0d9a8;
  box-shadow: inset 0 -4px 0 0 rgba(149, 109, 90, 0.3);
  min-height: 94px;
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
.btn-sm.pressed {
  background-image: url("../../assets/button/panel_burron_sm_pressed.png");
}

.slc-btn {
  position: relative;
  width: 60px;
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
}
.comfirm-btn {
  margin-top: 30px;
}

@media (max-width: 960px) {
  .ch-preview.female-character,
  .ch-preview.male-character {
    margin-top: 12%;
    width: 150px;
  }

  /* 手機版角色定位（複製自網頁版，這份可以獨立調整，不會動到網頁版） */
  .ch-preview.female-character {
    & .ch-element:nth-child(1) {
      top: 37%;
      left: 24.5%;
      width: 60%;
      height: auto;
    } /* df-skin */
    & .ch-element:nth-child(2) {
      top: 37%;
      left: 24.5%;
      width: 60%;
      height: auto;
    } /* light-skin */
    & .ch-element:nth-child(3) {
      top: 37%;
      left: 24.7%;
      width: 58.5%;
      height: auto;
    } /* dark-skin */
    & .ch-element:nth-child(4) {
      top: 44.5%;
      left: 44%;
      width: 30.8%;
      height: auto;
    } /* green-eyes */
    & .ch-element:nth-child(5) {
      top: 44.5%;
      left: 44%;
      width: 30.8%;
      height: auto;
    } /* blue-eyes */
    & .ch-element:nth-child(6) {
      top: 44.5%;
      left: 44%;
      width: 30.8%;
      height: auto;
    } /* black-eyes */
    & .ch-element:nth-child(7) {
      top: 26.5%;
      left: 0%;
      width: 99.3%;
      height: auto;
    } /* df-hair */
    & .ch-element:nth-child(8) {
      top: 26.5%;
      left: 0%;
      width: 99.3%;
      height: auto;
    } /* blue-hair */
    & .ch-element:nth-child(9) {
      top: 26.5%;
      left: 0%;
      width: 99.3%;
      height: auto;
    } /* black-hair */
  }

  .ch-preview.male-character {
    & .ch-element:nth-child(1) {
      top: 37%;
      left: 26%;
      width: 51.5%;
      height: auto;
    } /* df-skin */
    & .ch-element:nth-child(2) {
      top: 37%;
      left: 26%;
      width: 51.5%;
      height: auto;
    } /* light-skin */
    & .ch-element:nth-child(3) {
      top: 36%;
      left: 26%;
      width: 51.5%;
      height: auto;
    } /* dark-skin */
    & .ch-element:nth-child(4) {
      top: 42%;
      left: 42.5%;
      width: 28.3%;
      height: auto;
    } /* green-eyes */
    & .ch-element:nth-child(5) {
      top: 42%;
      left: 42.5%;
      width: 28.3%;
      height: auto;
    } /* blue-eyes */
    & .ch-element:nth-child(6) {
      top: 42%;
      left: 42.5%;
      width: 28.3%;
      height: auto;
    } /* black-eyes */
    & .ch-element:nth-child(7) {
      top: 1%;
      left: 8.5%;
      width: 89%;
      height: auto;
    } /* df-hair */
    & .ch-element:nth-child(8) {
      top: 1%;
      left: 8.5%;
      width: 89%;
      height: auto;
    } /* blue-hair */
    & .ch-element:nth-child(9) {
      top: 1%;
      left: 8.5%;
      width: 89%;
      height: auto;
    } /* black-hair */
  }

  .preferance-wrapper {
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
  }
  .preferance-select-bar {
    width: 100%;
  }
  .preferance-tabs {
    margin-top: 4%;
    margin-inline: auto;
    width: fit-content;
  }
  .preferance-content {
    margin-left: 1.3%;
  }
  .comfirm-btn {
    margin-top: 3%;
  }
}
</style>
