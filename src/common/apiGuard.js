// 全域攔截器：蓋掉原生 window.fetch，讓每一次 API 回應都先經過這裡檢查一次。
// 如果後端判斷「呼叫者已經不是這個公會的成員」（JSON 裡帶 reason: 'not_member'），
// 就跳出全站共用的提醒彈框（畫面掛載在 App.vue 的 KickedNoticeModal），並且讓
// fetch 回傳一個永遠不會 resolve 的 Promise，讓原本呼叫端的 .then() 不會被執行到，
// 避免同時跳出原本按鈕自己的 alert(data.message)，造成兩個提示疊在一起。

import { useKickedNoticeStore } from "@/stores/kickedNotice";

const originalFetch = window.fetch;

window.fetch = async function (...args) {
    const response = await originalFetch(...args);
    // response.clone() 複製一份可以獨立讀取的 body，讓我們可以先偷看內容判斷，
    // 不影響原本呼叫端還能正常用 res.json() 讀到資料
    const clone = response.clone();

    try {
        const data = await clone.json();
        if (data && data.success === false && data.reason === "not_member") {
            useKickedNoticeStore().show(data.message || "您已不再是這個公會的成員。");
            return new Promise(() => { }); // 永遠不 resolve，原呼叫端的 .then() 不會被執行到
        }
    } catch (e) {
        // 不是 JSON 回應就忽略，直接放行
    }

    return response;
};