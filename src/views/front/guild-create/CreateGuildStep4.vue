
<script>
import joyBoy from '@/assets/images/guild/joy-boy.png';
import joyGirl from '@/assets/images/guild/joy-girl.png';

export default {
  name: 'CreateGuildStep4',
  emits: ['view-my-guild'],
  data() {
    return {
      joyBoy,
      joyGirl
    };
  }
};
</script>

  <template>
    <div class="guild-create-step4">
      <h2 class="guild-create-step4__title">恭喜創建成功!</h2>
      <p class="guild-create-step4__subtitle">公會有哪些有趣的功能呢?</p>
      <div class="guild-create-step4__illustration-group">
        <img :src="joyBoy" alt="慶祝插圖" class="guild-create-step4__illustration" />
        <img :src="joyGirl" alt="慶祝插圖" class="guild-create-step4__illustration" />
      </div>
      <button class="guild-create-step4__cta" @click="$emit('view-my-guild')">
        查看我的公會
      </button>
    </div>
  </template>
  
<style lang="scss" scoped>
@use '@/assets/scss/abstracts/variables' as *;

.guild-create-step4 {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: $spacing-md;
  padding-block: $spacing-xl;

  &__title {
    color: $primary;
  }

  &__subtitle {
    color: $neutral-600;
  }

  &__illustration-group {
    display: flex;
    align-items: flex-end;
    justify-content: center;
    margin-block: $spacing-md;
  }

  &__illustration {
    width: 200px;
    height: auto;

    &:first-child {
      margin-right: -20px;
      z-index: 1;
    }

    &:last-child {
      z-index: 0;
    }
  }

  &__cta {
    background-color: $primary;
    color: $neutral-100;
    padding: $spacing-xs $spacing-xl;
    border-radius: $btn-radius-std;
    border: none;
    cursor: pointer;

    &:hover {
      background-color: $primary-500;
    }
  }
}
</style>