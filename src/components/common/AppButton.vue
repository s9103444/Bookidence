<script setup>
    defineProps({
        size:{
            type:String,
            default:'sm',
        },
        color:{
            type:String,
            default:'primary',
        }
    })
</script>
<template>
    <button class="app-button" :class="[`app-button--${size}`,`app-button--${color}`]" type="button"><slot></slot></button>
</template>
<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;

.app-button {
    position: relative;
    isolation: isolate;
    display: inline-flex;
    align-items: center;
    font-weight: 700;
    max-width: 100%;
    letter-spacing: $letter-spacing-base;
    -webkit-user-select: none;
    user-select: none;
    -webkit-tap-highlight-color:transparent;
    transition: transform 0.12s ease-out;
}


// 兩層共用的形狀
.app-button::before,
.app-button::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    z-index: -1;
    clip-path: polygon( 
        var(--step-2) 0,
        calc(100% - var(--step-2)) 0,
        calc(100% - var(--step-2)) 12.25%,
        calc(100% - var(--step-1)) 12.25%,
        calc(100% - var(--step-1)) 33.333%,
        100% 33.333%,
        100% 66.667%,
        calc(100% - var(--step-1)) 66.667%,
        calc(100% - var(--step-1)) 87.75%,
        calc(100% - var(--step-2)) 87.75%,
        calc(100% - var(--step-2)) 100%,
        var(--step-2) 100%,
        var(--step-2) 87.75%,
        var(--step-1) 87.75%,
        var(--step-1) 66.667%,
        0 66.667%,
        0 33.333%,
        var(--step-1) 33.333%,
        var(--step-1) 12.25%,
        var(--step-2) 12.25%
    );
}

// 暗面：貼齊底部
.app-button::before {
    bottom: 0;
    transition: transform 0.12s ease-out;
}
// 亮面：貼齊頂部
.app-button::after {
    top: 0;
}

// 尺寸：sm
.app-button--sm {
    --step-1: 4px;//step變數是維持像素風格邊緣的比例
    --step-2: 12px;
    --sink:1px;
    height: 40px;
    padding: 0 36px;
    font-size: $label-sm-size;
}
.app-button--sm::before,
.app-button--sm::after {
    height: 38px;
}

// 尺寸：lg
.app-button--lg {
    --step-1: 7px;
    --step-2: 18px;
    --sink:3px;
    height: 60px;
    padding: 0 48px;
    font-size: $label-lg-size;
}
.app-button--lg::before,
.app-button--lg::after {
    height: 56px;
}

//顏色:primary
.app-button--primary{
    color: $neutral-100;
}
.app-button--primary::after {
    background-color: $primary;
}
.app-button--primary::before {
    background-color: color-mix(in srgb, black 25%, #{$primary});
}
//顏色:secondary
.app-button--secondary{
    color: $primary;
}
.app-button--secondary::after {
    background-color: $secondary;
}
.app-button--secondary::before {
    background-color: color-mix(in srgb, black 25%, #{$secondary});
}

//hover顏色變更 (僅限有滑鼠裝置)
@media(hover:hover){
    .app-button--primary:hover::after{
        background-color: color-mix(in srgb, black 30%,  #{$primary});
    }
    .app-button--secondary:hover::after{
        background-color: color-mix(in srgb,black 30%, #{$secondary});
    }
}

//action:按下去的瞬間，亮面往下，厚度變薄
.app-button:active{
    transform: translateY(var(--sink));
    transition-duration: 0s;
}
.app-button:active::before{
    transform: translateY(calc(var(--sink)*-1));
    transition-duration: 0s;
}

@media(prefers-reduced-motion:reduce){
    .app-button,
    .app-button::before{
        transition: none;
    }
}
</style>