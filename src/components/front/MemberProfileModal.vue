<script setup>
import AppModal from "../common/AppModal.vue";
import PhotoSticker from "./PhotoSticker.vue";
import { API_BASE } from '@/common/api';
import { useUserStore } from '@/stores/user';

const userStore=useUserStore();

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  member: {
    type: Object,
    default: null,
  },
});

defineEmits(["update:modelValue"]);

async function addFriend(){
  const res=await fetch(`${API_BASE}/send_friend_request.php`,
    {method:'POST',
      headers:{
        Authorization: `Bearer ${userStore.token}`,
      'Content-Type': 'application/json'
      },
      body:JSON.stringify({toUserId: props.member.userId})
    });

  const result= await res.json();

  if(result.success){
    alert('已送出好友邀請');
  }else{
    alert(result.message);

  }

}

</script>

<template>
  <AppModal
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <div class="member-profile" v-if="member">
      <div class="member-profile__header">
        <div class="member-profile__img-cover">
          <PhotoSticker
            class="member-profile__photo-sticker"
            :userId="member.userId"
            :width="80"
          />
        </div>
        <div class="member-profile__info">
          <div class="member-profile__title">
            <span class="member-profile__label">會員ID</span>
            <span class="member-profile__id">{{ member.id }}</span>
          </div>
          <span class="member-profile__name">{{ member.name }}</span>
        </div>
      </div>

      <div class="member-profile__intro">
        <!-- <span class="member-profile__sub-title">個人介紹</span> -->
        <p class="member-profile__intro-text">
          {{ member.bio }}
        </p>
      </div>

      <button type="button" class="member-profile__add-friend" @click="addFriend">加入好友</button>
    </div>
  </AppModal>
</template>

<style scoped lang="scss">
@use "@/assets/scss/abstracts/variables" as *;

.member-profile {
  display: flex;
  flex-direction: column;
}

.member-profile__header {
  display: flex;
  align-items: center;
  gap: 20px;
}

.member-profile__img-cover {
  width: 130px;
  height: 130px;
  background-color: $secondary-100;
  border-radius: 5px;
  overflow: hidden;

  & .member-profile__photo-sticker {
    margin-top: 30px;
    margin-left: 25px;
    transform: scale(1.4);
  }
}

.member-profile__info {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.member-profile__label {
  display: inline-block;
  margin-right: 4px;
  font-size: $label-xxs-size;
  font-weight: $text-weight;
  padding: 2px 8px;
  color: $neutral-100;
  background-color: $primary;
  border-radius: $btn-radius-rnd;
}

.member-profile__id {
  color: $neutral-800;
  font-size: $label-sm-size;
  font-weight: $heading-weight;
}

.member-profile__name {
  font-size: $label-lg-size;
  color: $neutral-800;
  font-weight: $heading-weight;
}

.member-profile__intro {
  margin-top: 12px;
}

.member-profile__sub-title {
  color: $neutral-800;
  font-weight: $heading-weight;
  display: inline-block;
}

.member-profile__intro-text {
  color: $neutral-800;
  font-size: $p-xs-size;
  line-height: $text-line-height;
  margin-top: 10px;
  margin-inline: 4px;
}

.member-profile__add-friend {
  align-self: center;
  margin-top: $spacing-lg;
  padding: $spacing-sm $spacing-xl;
  border-radius: $btn-radius-rnd;
  border: none;
  background-color: $primary;
  color: $neutral-100;
  font-size: $p-sm-size;
  font-weight: $heading-weight;
  cursor: pointer;
}
</style>
