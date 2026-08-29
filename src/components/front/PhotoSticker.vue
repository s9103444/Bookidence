<template>
  <canvas ref="avatarCanvas"></canvas>
</template>

<script setup>
import { ref, toRef } from "vue";
import { useDrawAvatar } from "../../composables/useDrawAvatar";
const props = defineProps({
  userId: { type: [String, Number], required: true },
  width: { type: Number, default: 64 },
});

const avatarCanvas = ref(null);
const userIdRef = toRef(props, "userId");
useDrawAvatar(avatarCanvas, userIdRef, props.width);
</script>

<style lang="scss" scoped></style>
