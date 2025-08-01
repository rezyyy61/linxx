<template>
  <div class="frame-container">
    <div
        class="frame-content"
        :style="frameStyle"
        :class="{ 'max-wide': isExpanded, 'max-narrow': !isExpanded }"
    >
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, toRefs } from 'vue'

const props = defineProps({
  ratio: {
    type: [String, Number],
    default: '16/9'
  }
})

const { ratio } = toRefs(props)
const isExpanded = ref(true)
const toggleWidth = () => (isExpanded.value = !isExpanded.value)

const frameStyle = computed(() => ({
  aspectRatio: ratio.value,
  width: '100%',
  backgroundColor: 'black',
  display: 'flex',
  justifyContent: 'center',
  alignItems: 'center',
  overflow: 'hidden',
  borderRadius: '12px',
  maxHeight: 'calc(75vh - 140px)',
}))
</script>

<style scoped>
.frame-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1rem;
}
.frame-content {
  width: 100%;
  transition: max-width 0.3s ease;
}
.max-wide {
  max-width: 100%;
}
.max-narrow {
  max-width: 80%;
}
</style>
