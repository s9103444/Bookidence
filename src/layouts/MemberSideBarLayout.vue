<script>
// 會員專區側欄。
// 使用到的頁面：個人檔案/我的好友/我的公會/我的讀書會活動/通知中心/使用者設定

    export default {
        data(){
            return{
                userId:this.$route.params.id,
                nav_array:[
                    {
                        path:'user-settings',
                        name:'使用者設定'
                    },
                           {
                        path:'friends',
                        name:'我的好友'
                    },
                           {
                        path:'my-guilds',
                        name:'我的讀書公會'
                    },
                         {
                        path:'my-books-events',
                        name:'我的讀書會活動'
                    },
                      {
                        path:'notice',
                        name:'通知中心'
                    },
                    
                ]
            }
        },
          
    };
</script>

<template>
    <div class="member-sidebar-layout">
        <div class="member-sidebar">
            <!-- 使用者資訊 -->
            <div class="member-sidebar__title">
                <span class="member-sidebar__label">會員專區</span>
            </div>
        
            <!-- 側邊導覽 -->
            <div class="member-sidebar__nav">
                
                <!-- <router-link class="nav-item" :to="{name:'member',params:{id: 'profile'}}">個人檔案</router-link> -->

                <!-- <router-link v-for="(item,key) in nav_array" :key="key" class="nav-item" :to="{name:'member',params:{id: item.path }}">{{ item.name }}</router-link> -->

                <router-link v-for="(item,key) in nav_array" :key="key" class="nav-item" :to="'/front/member/'+ item.path">{{ item.name }}</router-link>

            <!--  <router-link class="nav-item" to="/front/member/profile">個人檔案</router-link>
                <router-link class="nav-item" to="/front/member/friends">我的好友</router-link>
               -->
      </div>


    </div>
    <div class="member-content">
        <router-view></router-view>
    </div>

 </div>  
</template>



<style lang="scss" scoped>

@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;
 
.member-sidebar-layout {
  display: flex;
  min-height: 100vh;
}

.member-content {
  flex: 1;
  min-width: 0;
}

//

.member-sidebar {
  display: flex;
  flex-direction: column;
  width: $sidebar-width;
  padding: $spacing-md;
  background: $neutral-300;
  position: sticky;
  top: 20px;
}
 
.member-sidebar__img {
  width: 120px;
  height: 120px;
  margin: $spacing-md auto;
  object-fit: cover;
  border-radius: 50%;
}
 
.member-sidebar__title {
  display: flex;
  flex-direction: column;
  margin-bottom: $spacing-md;
  padding-left: $spacing-md;
}
 
.member-sidebar__label {
  font-size: $h5-size;
  color: $primary;
}
 
.member-sidebar__name {
  font-size: $h6-size;
  font-weight: $heading-weight;
  color: $primary;
}
 
.member-sidebar__nav {
  display: flex;
  flex-direction: column;
  gap: $spacing-xs;
}
 
.nav-item {
  padding: $spacing-sm $spacing-md $spacing-sm calc(#{$spacing-md} + #{$spacing-md});
  margin: 0 (-$spacing-md);
  background: transparent;
  color: $primary;
  text-decoration: none;
  text-align: left;
  font-size: $p-sm-size;
  display: block;
  transition: transform 0.2s ease, background 0.2s ease;
  cursor: pointer;
 
  &:hover {
    background: $neutral-100;
    transform: translateY(-2px);
  }
}

</style>