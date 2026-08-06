<template>
    <div>
        <span class="preferance-title">個人外觀設定</span>
    </div>
    <div class="female-character">
        <img src="../../assets/images/chracter/female.png" alt="">
    </div>
    <div class="male-character">
        <img src="../../assets/images/chracter/male.png" alt="">
    </div>
    <div class="preferance-tabs">
        <button class="preferance-tab" v-for="tab in tabs" :key="tab.id" :class="{ tabActive: activatTab === tab.id }"
            @click="activatTab = tab.id">{{ tab.name }}</button>
    </div>
    <div class="preferance-content">
        <div class=""></div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            tabs: [
                { id: 1, name: '選擇膚色' },
                { id: 2, name: '選擇瞳色' },
                { id: 3, name: '選擇髮色' },
            ],
            activatTab: 1,
        }
    }
}
</script>

<style lang="scss" scoped>
@use "@/assets/scss/abstracts/variables" as *;

.preferance-title {
    color: $brown;
    font-weight: $heading-weight;
}


.preferance-tabs {
    display: flex;
    gap: 20px;
    margin-left: 2px;
}

.preferance-tab {
    color: #F0D9A8;
    position: relative;
    background-color: #6B5B5B;
    border: none;
    border-right: 2px solid #6B5B5B;
    padding-inline: 12px;
    padding-block: 2px;
    z-index: 5;
}

.preferance-tab::before {
    content: '';
    position: absolute;
    top: -2px;
    left: 0px;
    /* 故意內縮，跟左邊的線錯開 → 缺角在這裡 */
    right: -8px;
    /* 對齊到 border-right 的外緣，右上角密合 */
    width: 100%;
    height: 2px;
    background-color: #6B5B5B;
}

.preferance-tab::after {
    content: '';
    position: absolute;
    top: 0;
    /* 從按鈕頂端算起，比 ::before 的 -8px 低一截 → 缺角 */
    left: -2px;
    bottom: 0;
    width: 2px;
    background-color: #6B5B5B;
}

.tabActive {
    background-color: #F0D9A8;
    color: $brown;
}

.preferance-content {
    position: relative;
    width: 100%;
    height: 200px;
    border-left: 2px solid #6B5B5B;
    border-bottom: 2px solid #6B5B5B;
    background-color: #F0D9A8;
    box-shadow: inset 0 -4px 0 0 rgba(149, 109, 90, 0.3);
}

.preferance-content::before {
    content: '';
    position: absolute;
    top: -2px;
    left: 0%;
    right: 0px;
    height: 2px;
    background-color: #6B5B5B;
}

.preferance-content::after {
    content: '';
    position: absolute;
    top: 0;
    right: -2px;
    bottom: 0;
    width: 2px;
    background-color: #6B5B5B;
}
</style>