
<script setup>
import AppButton from "@/components/common/AppButton.vue";
import AppIcon from "@/components/common/AppIcon.vue";


</script>


<template>
  <header class="kv-section-img">
    <div class="kv-section">
    <h3 class="kv-welcome">歡迎來到 Bookidence</h3>
    <h1 class="kv-title">讓你的閱讀，<br>住進有人陪的地方</h1>
    <div class="kv-button">
    <AppButton color="secondary">開始探索 <AppIcon name="arrow-right" /></AppButton>
    </div>
    
    </div>
  </header>

  <section class="intro-homeroom">
    <div class="content-homeroom-intro">
    <p class="tagline-homeroom-intro" >搭建一座屬於你的閱讀小屋</p>
    <h3 class="title-homeroom-intro" >每一次翻頁，<br>都為小屋砌上一片磚瓦</h3>
    <p class="desc-homeroom-intro">這不只是個冷冰冰的數位書棚，<br>而是你在 Bookidence 小鎮裡親手搭建的精神樹屋。<br>讓閱讀累積的足跡，化作肉眼可見的溫暖裝飾。</p>
    <AppButton >進入我的書房 <AppIcon name="arrow-right" /></AppButton>
    </div>
    <div class="img-homeroom-intro">
      <img src="/src/assets/images/home-element/house.png" alt="" style="width: 100%";>
    </div>
  </section>

  <section class="features-homeroom">
    <div class="intro-features-homeroom"> 

      <p class="title-features-homeroom">捏出你的小讀者</p>
      <p class="desc-features-homeroom">
        在鏡子前換上喜歡的髮型、膚色與瞳色，
        打造專屬於你的像素小精靈。
        隨著閱讀經驗值慢慢累積，
        還能解鎖獨特的成就徽章，
        讓你在小鎮裡閃閃發光！</p>
        
    </div>

  </section>



</template>



<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;
@use "../../assets/scss/abstracts/mixins" as *;


.kv-section-img{
  background-image: url("@/assets/images/home-element/morning-1.png");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center center;
  min-height:700px ;
 
}
.kv-section{
  margin: 0 auto;
  
}
.kv-welcome{

  display: flex;
  justify-content: center;
  color: $primary;
  font-size:$p-md-size;
  margin-bottom: $spacing-md;
  padding-top: $spacing-xl;
  
}

.kv-title{
   display: flex;
  justify-content: center;
  font-size:$h1-size;
  color: $primary;
  text-align: center;
  line-height: $heading-line-height;
  margin-bottom: $spacing-xl;
  font-weight: $heading-weight;
}

.kv-button{
  display:inline-block;
  display: flex;
  justify-content: center;

}

.intro-homeroom{
  @include grid-container; // 載入 Count: 12, Gutter: 24px, Margin: 48px
  align-items: center;    // 控制 Grid 內的子元素垂直居中（取代 display: flex）
  background-image: url("@/assets/images/home-element/light-green-pixel.png");
  background-repeat: no-repeat;
  background-size: cover;
  align-items: center;
 
  

}


.content-homeroom-intro{
  @include col-span(6); // 佔據 6 欄
  padding-top:120px ;
  
}

.tagline-homeroom-intro{
  font-size: $h6-size;
  color:$primary;
  font-weight:$heading-weight;
  margin-bottom: $spacing-lg;

}

.title-homeroom-intro{
  font-size: $h1-size;
  color:$neutral-800;
  font-weight:$heading-weight ;
  margin-bottom: 64px;

}


.desc-homeroom-intro{
  font-size:$p-md-size;
  margin-bottom: 64px;
}

.img-homeroom-intro{
  @include col-span(6); // 佔據 6 欄
  padding-top:120px ;

}
</style>