import femaleBase from "../assets/images/appear/female.png";
import maleBase from "../assets/images/appear/male.png";
import femaleSkinLight from "../assets/images/appear/character-for-register/female_skin_light.png";
import femaleSkinMedium from "../assets/images/appear/character-for-register/female_skin_medium.png";
import femaleSkinDark from "../assets/images/appear/character-for-register/female_skin_dark.png";
import femaleEyesBlack from "../assets/images/appear/character-for-register/female_eyes_black.png";
import femaleEyesBlue from "../assets/images/appear/character-for-register/female_eyes_blue.png";
import femaleEyesGreen from "../assets/images/appear/character-for-register/female_eyes_green.png";
import femaleHairBlack from "../assets/images/appear/character-for-register/female_hair_black.png";
import femaleHairBlue from "../assets/images/appear/character-for-register/female_hair_blue.png";
import femaleHairBrown from "../assets/images/appear/character-for-register/female_hair_brown.png";
import maleSkinLight from "../assets/images/appear/character-for-register/male_skin_light.png";
import maleSkinMedium from "../assets/images/appear/character-for-register/male_skin_medium.png";
import maleSkinDark from "../assets/images/appear/character-for-register/male_skin_dark.png";
import maleEyesBlack from "../assets/images/appear/character-for-register/male_eyes_black.png";
import maleEyesBlue from "../assets/images/appear/character-for-register/male_eyes_blue.png";
import maleEyesGreen from "../assets/images/appear/character-for-register/male_eyes_green.png";
import maleHairBlack from "../assets/images/appear/character-for-register/male_hair_black.png";
import maleHairBlue from "../assets/images/appear/character-for-register/male_hair_blue.png";
import maleHairBrown from "../assets/images/appear/character-for-register/male_hair_brown.png";

import { watch } from "vue";
import { API_BASE } from "../common/api";

//單純一個對照表，appear_id（如 "fs2"）→ 對應的圖片檔案（import 進來的圖片路徑）。不會變動、不是响應式，純粹是一個查表工具，被 drawAvatar 用。
const appearPositionMap = {
  fs1: { top: 0.37, left: 0.245, width: 0.6 },
  fs2: { top: 0.37, left: 0.245, width: 0.6 },
  fs3: { top: 0.37, left: 0.247, width: 0.585 },
  fe1: { top: 0.445, left: 0.44, width: 0.308 },
  fe2: { top: 0.445, left: 0.44, width: 0.308 },
  fe3: { top: 0.445, left: 0.44, width: 0.308 },
  fh1: { top: 0.265, left: 0, width: 1 },
  fh2: { top: 0.265, left: 0, width: 1 },
  fh3: { top: 0.265, left: 0, width: 1 },
  ms1: { top: 0.365, left: 0.26, width: 0.515 },
  ms2: { top: 0.365, left: 0.26, width: 0.515 },
  ms3: { top: 0.365, left: 0.26, width: 0.515 },
  me1: { top: 0.415, left: 0.425, width: 0.283 },
  me2: { top: 0.415, left: 0.425, width: 0.283 },
  me3: { top: 0.415, left: 0.425, width: 0.283 },
  mh1: { top: 0.005, left: 0.085, width: 0.89 },
  mh2: { top: 0.005, left: 0.085, width: 0.89 },
  mh3: { top: 0.005, left: 0.085, width: 0.89 },
};

const appearImageMap = {
  fs1: femaleSkinLight,
  fs2: femaleSkinMedium,
  fs3: femaleSkinDark,
  fe1: femaleEyesBlack,
  fe2: femaleEyesBlue,
  fe3: femaleEyesGreen,
  fh1: femaleHairBlack,
  fh2: femaleHairBlue,
  fh3: femaleHairBrown,
  ms1: maleSkinLight,
  ms2: maleSkinMedium,
  ms3: maleSkinDark,
  me1: maleEyesBlack,
  me2: maleEyesBlue,
  me3: maleEyesGreen,
  mh1: maleHairBlack,
  mh2: maleHairBlue,
  mh3: maleHairBrown,
};

//輸入一個圖片路徑字串，回傳一個 Promise，這個 Promise 會在圖片真正下載完成時才完成（resolve）。它不知道 canvas、不知道使用者是誰，是純粹被 drawAvatar 呼叫的小工具。
function loadImage(src) {
  return new Promise((resolve) => {
    const img = new Image();
    img.onload = () => resolve(img);
    img.src = src;
  });
}

function drawLayer(ctx, img, appearId, width, height) {
  const pos = appearPositionMap[appearId];
  const x = pos.left * width;
  const y = pos.top * height;
  const drawWidth = pos.width * width;
  const drawHeight = drawWidth * (img.naturalHeight / img.naturalWidth);
  ctx.drawImage(img, x, y, drawWidth, drawHeight);
}

async function drawAvatar(canvasEl, appearance, width, height) {
  const ctx = canvasEl.getContext("2d");
  const baseSrc = appearance.gender === "female" ? femaleBase : maleBase;
  const baseImg = await loadImage(baseSrc);
  const skinImg = await loadImage(appearImageMap[appearance.skin]);
  const eyesImg = await loadImage(appearImageMap[appearance.eyes]);
  const hairImg = await loadImage(appearImageMap[appearance.hair]);

  ctx.drawImage(baseImg, 0, 0, width, height);
  drawLayer(ctx, skinImg, appearance.skin, width, height);
  drawLayer(ctx, eyesImg, appearance.eyes, width, height);
  drawLayer(ctx, hairImg, appearance.hair, width, height);
}

const CHARACTER_RATIO = 222 / 146; // 角色底圖固定比例（高/寬），跟原本 CSS aspect-ratio: 146/222 一致

export function useDrawAvatar(canvasRef, userIdRef, width) {
  const height = width * CHARACTER_RATIO;

  async function fetchAndDraw() {
    const canvasEl = canvasRef.value;
    canvasEl.width = width;
    canvasEl.height = height;

    const res = await fetch(
      `${API_BASE}/user_appear_public.php?user_id=${userIdRef.value}`,
    );
    const result = await res.json();
    if (result.success) {
      drawAvatar(canvasEl, result.data, width, height);
    }
  }
  watch([canvasRef, userIdRef], ([newCanvas, newUserId]) => {
    //資料庫有變動就更新
    if (newCanvas && newUserId) {
      fetchAndDraw();
    }
  });
}
