<template>
  <div
      class="video-frame"
      :class="[orientation]"
      :style="{ aspectRatio: ratioStyle, maxHeight: maxHeight }"
  >
    <slot />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  width: { type: Number, default: 16 },
  height: { type: Number, default: 9 },
})

const ratio = computed(() => {
  const w = props.width
  const h = props.height
  return w > 0 && h > 0 ? h / w : 9 / 16
})

const ratioStyle = computed(() => (1 / ratio.value).toFixed(4))

const orientation = computed(() => {
  const r = ratio.value
  if (r > 1.1) return 'portrait'
  if (r < 0.9) return 'landscape'
  return 'square'
})

const maxHeight = computed(() => {
  if (orientation.value === 'portrait') return '60vh'
  if (orientation.value === 'square') return '70vh'
  return '60vh'
})
</script>

<style scoped>
.video-frame {
  width: 100%;
  position: relative;
  overflow: hidden;
  background-color: #000;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

/* make sure inner video fits without cropping */
.video-frame ::v-deep(video),
.video-frame ::v-deep(.vjs-poster) {
  object-fit: contain !important;
  width: 100% !important;
  height: 100% !important;
}
</style>
