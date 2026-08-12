import { defineStore } from "pinia";
import twilightCover from "../assets/images/twilight-cover.png";
import littlePrinceCover from "../assets/images/little-prince-cover.png";
import peterCover from "../assets/images/peter-cover.png";
import nordicTimeCover from "../assets/images/nordic-time-cover.png";

export const useBookStore = defineStore("book", {
  state: () => ({
    selectedBook: null,
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
        cover: peterCover,
        translator: "王蘊潔",
        publishDate: "2013-02-25",
        publisher: "皇冠出版",
        isbn: "9789573328968",
        reviewCount: 15,
        collectCount: 40,
      },
      {
        id: 4,
        title: "被討厭的勇氣",
        author: "岸見一郎",
        category: "心理成長",
        status: "已完成",
        cover: nordicTimeCover,
        translator: "葉小燕",
        publishDate: "2014-10-30",
        publisher: "究竟出版",
        isbn: "9789861373870",
        reviewCount: 30,
        collectCount: 55,
      },
    ],
  }),
});
