import { defineStore } from "pinia";
import guildAvatar from "../assets/images/guild/guildAvatar-square.png";
import guildBackground from "../assets/images/guild/guildBackground.png";
import guildAvatar2 from "../assets/images/guild/guildAvatar2.png";

export const useGuildStore = defineStore("guild", {
  state: () => ({
    guilds: [
      {
        id: 1,
        avatar: guildAvatar,
        name: "文青小時光",
        code: "GD00000001",
        currentBook: "小王子",
        memberCount: 80,
      },
    ],
    currentGuild: {
      name: "壁爐與貓",
      backgroundUrl: guildBackground,
      thumbnailImage: guildAvatar2,
      introContent: '深夜的鐘聲響起，這裡是愛書人的避風港。有劈啪作響的溫暖壁爐，有腳邊打盹的貓，還有手中那本尚未讀完的書。\n\n' +
        '我們偏好的書籍類型不設限，但更傾向於具有療癒、探索感或引人深思的作品：\n' +
        '奇幻與架空冒險：喜歡跟著主角踏入宏大的世界觀與神祕古老的歷史。\n' +
        '雋永散文與心靈療癒：在文字中尋找共鳴，撫平日常的焦慮與疲憊。\n' +
        '經典文學與各類小說：品味文字的細膩編織，探討故事背後的人性與智慧。',
      announcementContent:
        '保持溫柔與包容：每個人對書籍的理解與喜好不同，這裡嚴禁流於高深的學術爭辯或批判他人的閱讀品味。\n\n' +
        '安靜的陪伴：在共讀時間請保持安靜，尊重彼此翻頁的空間，讓想獨處的人也能安心待著。\n\n' +
        '嚴禁過度商業或社交目的：這裡不歡迎推銷、直銷或過度的利益搭訕，請讓公會回歸最純粹的書香與溫度。',
      events: [],
    },
  }),
});
