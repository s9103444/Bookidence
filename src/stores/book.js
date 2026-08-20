import { defineStore } from "pinia";
import twilightCover from "../assets/images/twilight-cover.png";
import littlePrinceCover from "../assets/images/little-prince-cover.png";
import nordicTimeCover from "../assets/images/nordic-time-cover.png";
import artOfSpending from "../assets/images/art-of-spending.jpg";
import Namiya from "../assets/images/解憂雜貨店.jpg";
import { API_BASE } from "../common/api";

export const useBookStore = defineStore("book", {
  state: () => ({
    selectedBook: null,
    draftBooks: [], // 每筆 { id, content, status, lastUpdated }
    books: [
      {
        id: 1,
        title: "暮光之城",
        author: "史蒂芬妮．梅爾",
        category: "奇幻小說",
        status: "閱讀中",
        cover: twilightCover,
        translator: "瞿秀蕙",
        publishDate: "2008-12-02",
        publisher: "尖端出版",
        isbn: "9789571039640",
        reviewCount: 8,
        collectCount: 21,
      },
      {
        id: 2,
        title: "小王子",
        author: "聖修伯里",
        category: "文學",
        status: "已完成",
        cover: littlePrinceCover,
        translator: "張穎綺",
        publishDate: "2015-04-01",
        publisher: "商周出版",
        isbn: "9789862726912",
        reviewCount: 25,
        collectCount: 60,
      },
      {
        id: 3,
        title: "解憂雜貨店",
        author: "東野圭吾",
        category: "小說",
        status: "閱讀中",
        cover: Namiya,
        translator: "王蘊潔",
        publishDate: "2013-02-25",
        publisher: "皇冠出版",
        isbn: "9789573328968",
        reviewCount: 15,
        collectCount: 40,
      },
      {
        id: 4,
        title: "花錢的藝術：打造富足人生的金錢心理學",
        author: "摩根．豪瑟",
        category: "商業理財",
        status: "已完成",
        cover: artOfSpending,
        translator: "洪世民",
        publishDate: "2026-07-27",
        publisher: "平安文化",
        isbn: "9786267859506",
        reviewCount: 30,
        collectCount: 55,
      },
    ],

    searchResults: [],
  }),
  actions: {
    upsertDraft(draft) {
      const idx = this.draftBooks.findIndex((d) => d.id === draft.id);
      if (idx !== -1) {
        this.draftBooks[idx] = { ...this.draftBooks[idx], ...draft };
      } else {
        this.draftBooks.push(draft);
      }
    },
    removeDraft(id) {
      this.draftBooks = this.draftBooks.filter((d) => d.id !== id);
    },
    //把顯示查詢書籍結果的API寫成函式做呼叫
    async searchBooks(keyword) {
      const res = await fetch(`${API_BASE}/book_search.php?keyword=${keyword}`);
      const result = await res.json();
      this.searchResults = result.data;
    },
  },
});
