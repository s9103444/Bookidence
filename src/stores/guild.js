import { defineStore } from "pinia";
import guildAvatar from "../assets/images/guild/guildAvatar-square.png";

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
  }),
});
