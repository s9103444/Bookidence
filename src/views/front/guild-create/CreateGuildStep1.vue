<script>
import AppIcon from '@/components/common/AppIcon.vue';

export default {
  name: 'CreateGuildStep1',
  components: { AppIcon },
  props: {
    guildName: { type: String, default: '' },
    guildAnnouncement: { type: String, default: '' },
    guildIntro: { type: String, default: '' },
    guildAvatarPreview: { type: String, default: '' }
  },
  emits: [
    'update:guild-name',
    'update:guild-announcement',
    'update:guild-intro',
    'avatar-selected'
  ],
  methods: {
    handleAvatarChange(event) {
      const file = event.target.files[0];
      if (!file) return;
      const previewUrl = URL.createObjectURL(file);
      // 一次把 File 物件跟預覽網址交給父層,父層負責存進 data()
      this.$emit('avatar-selected', { file, previewUrl });
    }
  }
};
</script>

<template>
  <div class="guild-create-step1">
    <div class="guild-create-step1__field">
      <label class="guild-create-step1__label">
        輸入公會名稱<span class="guild-create-step1__required">*</span>
      </label>
      <input
        type="text"
        class="guild-create-step1__input"
        placeholder="請在此輸入"
        :value="guildName"
        @input="$emit('update:guild-name', $event.target.value)"
      />
    </div>

    <div class="guild-create-step1__row">
      <div class="guild-create-step1__field guild-create-step1__field--grow">
        <label class="guild-create-step1__label">設定公會公告</label>
        <textarea
          class="guild-create-step1__textarea"
          placeholder="請輸入公會公告內容"
          :value="guildAnnouncement"
          @input="$emit('update:guild-announcement', $event.target.value)"
        ></textarea>
      </div>

      <div class="guild-create-step1__field">
        <label class="guild-create-step1__label">
          設定公會頭像<span class="guild-create-step1__required">*</span>
        </label>
        <label class="guild-create-step1__dropzone">
          <input
            type="file"
            accept=".jpg,.jpeg,.png"
            class="guild-create-step1__file-input"
            @change="handleAvatarChange"
          />
          <img
            v-if="guildAvatarPreview"
            :src="guildAvatarPreview"
            alt="公會頭像預覽"
            class="guild-create-step1__preview"
          />
          <div v-else class="guild-create-step1__dropzone-hint">
            <AppIcon name="plus" :size="20" />
            <span>點擊或拖拽參考圖</span>
            <span class="guild-create-step1__dropzone-note">(限.jpg .png檔案，5MB以內)</span>
          </div>
        </label>
      </div>
    </div>

    <div class="guild-create-step1__field">
      <label class="guild-create-step1__label">設定簡介</label>
      <textarea
        class="guild-create-step1__textarea"
        placeholder="請輸入公會簡介"
        :value="guildIntro"
        @input="$emit('update:guild-intro', $event.target.value)"
      ></textarea>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.guild-create-step1 {
  display: flex;
  flex-direction: column;
  gap: $spacing-lg;

  &__row {
    display: flex;
    gap: $spacing-lg;
  }

  &__field {
    display: flex;
    flex-direction: column;
    gap: $spacing-xs;

    &--grow {
      flex: 1;
    }
  }

  &__label {
    font-weight: $heading-weight;
    color: $neutral-800;
  }

  &__required {
    color: $color-danger;
    margin-left: 2px;
  }

  &__input {
    @include form-field-base;
  }

  &__textarea {
    @include form-field-base;
    height: 220px;
    resize: none;
    background-color: $neutral-200;   // 保留原本文字框的底色差異,mixin 套完之後再覆蓋這一行即可
  }

  &__dropzone {
    position: relative;
    width: 220px;
    height: 220px;
    aspect-ratio: 1 / 1;
    border: 1px dashed $neutral-400;
    border-radius: $btn-radius-std;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    overflow: hidden;
    background-color: $neutral-200;
  }

  &__file-input {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
  }

  &__dropzone-hint {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: $spacing-xxs;
    color: $neutral-500;
    font-size: $p-sm-size;
    text-align: center;
  }

  &__dropzone-note {
    color: $neutral-400;
    font-size: 12px;
  }

  &__preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}
</style>