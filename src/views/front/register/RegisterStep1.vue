<script>
export default {
  props: {
    isAdult: {
      type: Boolean,
      required: true
    },
    agreeTerms: {
      type: Boolean,
      required: true
    },
    agreePrivacy: {
      type: Boolean,
      required: true
    }
  },
  emits: ['update:isAdult', 'update:agreeTerms', 'update:agreePrivacy']
}
</script>

<template>
  <div class="register-step1">
    <label class="register-step1__option">
      <input
        type="checkbox"
        :checked="isAdult"
        @change="$emit('update:isAdult', $event.target.checked)"
      />
      我已年滿 13 歲
    </label>

    <label class="register-step1__option">
            <input
        type="checkbox"
        :checked="agreeTerms"
        @change="$emit('update:agreeTerms', $event.target.checked)"
      />
      我同意服務條款
    </label>

        <label class="register-step1__option">
            <input
        type="checkbox"
        :checked="agreePrivacy"
        @change="$emit('update:agreePrivacy', $event.target.checked)"
      />
      我同意隱私權政策
    </label>
  </div>
</template>