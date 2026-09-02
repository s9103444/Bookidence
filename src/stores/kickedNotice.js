import { defineStore } from "pinia";

export const useKickedNoticeStore = defineStore("kickedNotice", {
    state: () => ({
        isOpen: false,
        message: "",
    }),
    actions: {
        show(message) {
            this.message = message;
            this.isOpen = true;
        },
        hide() {
            this.isOpen = false;
        },
    },
});