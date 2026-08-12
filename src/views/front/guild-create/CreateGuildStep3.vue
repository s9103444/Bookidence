<script>
export default {
  name: 'CreateGuildStep3',
  props: {
    reviewQuestions: { type: Array, default: () => ['', '', ''] },
    inviteFriendList: { type: Array, default: () => [] }
  },
  emits: ['update:review-questions', 'toggle-invite'],
  methods: {
    updateQuestion(index, value) {
      const updated = [...this.reviewQuestions];
      updated[index] = value;
      this.$emit('update:review-questions', updated);
    }
  }
};
</script>

<template>
  <div class="guild-create-step3">
    <div class="guild-create-step3__field">
      <label class="guild-create-step3__label">設定審核題目</label>
      <div
        v-for="(question, index) in reviewQuestions"
        :key="index"
        class="guild-create-step3__question"
      >
        <span class="guild-create-step3__question-tag">Q{{ index + 1 }}.</span>
        <input
          type="text"
          placeholder="請輸入問題"
          :value="question"
          @input="updateQuestion(index, $event.target.value)"
        />
      </div>
    </div>

    <div class="guild-create-step3__field">
      <label class="guild-create-step3__label">邀請好友</label>
      <table class="guild-create-step3__table">
        <thead>
          <tr>
            <th>好友名稱</th>
            <th>上次上線時間</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="friend in inviteFriendList" :key="friend.id">
            <td class="guild-create-step3__friend-cell">
              <img :src="friend.avatarUrl" :alt="friend.name" />
              <div>
                <p>{{ friend.name }}</p>
                <p class="guild-create-step3__friend-code">{{ friend.memberCode }}</p>
              </div>
            </td>
            <td>{{ friend.lastOnlineText }}</td>
            <td>
              <button
                class="guild-create-step3__invite-btn"
                :disabled="friend.invited"
                @click="$emit('toggle-invite', friend.id)"
              >
                {{ friend.invited ? '已邀請' : '邀請加入' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@use '@/assets/scss/abstracts/variables' as *;

.guild-create-step3 {
  display: flex;
  flex-direction: column;
  gap: $spacing-xl;

  &__field {
    display: flex;
    flex-direction: column;
    gap: $spacing-xs;
  }

  &__label {
    font-weight: $heading-weight;
    color: $neutral-800;
  }

  &__question {
    display: flex;
    align-items: stretch;
    border: 1px solid $neutral-400;      // 改動:原本 $neutral-300
    border-radius: $btn-radius-std;
    overflow: hidden;

    &:focus-within {                     // 新增
      border-color: $primary;
      box-shadow: 0 0 0 3px rgba($primary, 0.2);
    }

    input {
      flex: 1;
      border: none;
      outline: none;                     // 新增:拿掉內層 input 自己的黑框,反正外層框已經處理 focus 效果
      padding: $spacing-xs;
      background-color: $neutral-200;

      &::placeholder {
        color: $neutral-400;
      }
    }
  }

  &__question-tag {
    display: flex;
    align-items: center;
    padding-inline: $spacing-md;
    background-color: $primary;
    color: $neutral-100;
    font-weight: $heading-weight;
  }

  &__table {
    width: 100%;
    border-collapse: collapse;

    th {
      text-align: left;
      padding: $spacing-xs;
      background-color: $neutral-200;
      color: $neutral-600;
      font-weight: normal;
      font-size: $p-sm-size;
    }

    td {
      padding: $spacing-xs;
      border-bottom: 1px solid $neutral-200;
    }
  }

  &__friend-cell {
    display: flex;
    align-items: center;
    gap: $spacing-xs;

    img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }
  }

  &__friend-code {
    font-size: $p-sm-size;
    color: $neutral-500;
  }

  &__invite-btn {
    background-color: $secondary-500;
    border: none;
    padding: $spacing-xxs $spacing-md;
    border-radius: $btn-radius-std;
    cursor: pointer;

    &:disabled {
      background-color: $neutral-300;
      color: $neutral-500;
      cursor: not-allowed;
    }
  }
}
</style>