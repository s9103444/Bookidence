<template>
    <button class="app-button" type="button"><slot></slot></button>
</template>
<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;

.app-button{
    position: relative;
    isolation: isolate;
    display: inline-flex;
    align-items: center;
    height: 40px;
    padding: 0 36px;
    color: $neutral-100;
    font-size: $label-sm-size;
    font-weight: 600;
    letter-spacing: $letter-spacing-base;
}

// 兩層共用的形狀
.app-button::before,
.app-button::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    height: 38px;
    z-index: -1;
    clip-path: polygon( 
         /* 上緣 */
        12px 0,
        calc(100% - 12px) 0,
        /* 右上角，往外走兩階 */
        calc(100% - 12px) 12.25%,
        calc(100% - 4px) 12.25%,
        calc(100% - 4px) 33.333%,
        100% 33.333%,
        /* 右下角，往內走兩階 */
        100% 66.667%,
        calc(100% - 4px) 66.667%,
        calc(100% - 4px) 87.75%,
        calc(100% - 12px) 87.75%,
        calc(100% - 12px) 100%,
        /* 下緣 */
        12px 100%,
        /* 左下角 */
        12px 87.75%,
        4px 87.75%,
        4px 66.667%,
        0 66.667%,
        /* 左上角 */
        0 33.333%,
        4px 33.333%,
        4px 12.25%,
        12px 12.25%
    );
}

// 暗面：貼齊底部
.app-button::before {
    bottom: 0;
    background-color: color-mix(in srgb, black 25%, #{$primary});
}
// 亮面：貼齊頂部
.app-button::after {
    top: 0;
    background-color: $primary;
}
</style>