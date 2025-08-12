<template>
  <Teleport to="body">
    <transition name="scale-fade">
      <div v-if="visible" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-md p-4" @click.self="close">
        <div class="w-full max-w-[92vw] sm:max-w-lg md:max-w-xl lg:max-w-2xl rounded-3xl shadow-xl ring-1 ring-white/10 dark:ring-white/20 overflow-hidden flex flex-col max-h-[92vh] bg-white/90 dark:bg-neutral-900/95 backdrop-blur-2xl">
          <header class="flex items-center justify-between px-6 py-4 bg-gradient-to-br from-white/70 to-white/30 dark:from-neutral-800/60 dark:to-neutral-800/30 backdrop-blur-sm border-b border-white/20 dark:border-white/10">
            <div class="flex items-center gap-3 min-w-0">
              <UserAvatar :src="avatarSrc" :fallback="userName" :color="avatarColor" size="md" />
              <div class="flex flex-col min-w-0">
                <span class="font-semibold text-gray-800 dark:text-gray-100 truncate">{{ userName }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Share to your feed</span>
              </div>
            </div>
            <button class="text-gray-400 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-xl leading-none transition-colors" @click="close" aria-label="Close">✕</button>
          </header>

          <section class="p-6 space-y-6 overflow-y-auto">
            <div class="relative rounded-2xl bg-gradient-to-br from-blue-100/50 to-indigo-100/30 dark:from-blue-500/20 dark:to-indigo-500/10 p-0.5">
              <textarea
                  ref="taRef"
                  v-model="caption"
                  :dir="isRTL ? 'rtl' : 'ltr'"
                  :class="[
                  'w-full min-h-[5.5rem] rounded-[15px] resize-none p-4 focus:outline-none',
                  isRTL ? 'text-right' : 'text-left',
                  'bg-white/80 dark:bg-neutral-800/80 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 shadow-inner focus:ring-4 focus:ring-blue-300/40 dark:focus:ring-blue-800/40'
                ]"
                  :placeholder="placeholder"
                  @input="limitInput"
              />
              <div class="absolute bottom-2 right-3 left-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 select-none">
                <span class="truncate">{{ caption ? ' ' : hint }}</span>
                <span v-if="captionLimit">{{ caption.length }}/{{ captionLimit }}</span>
              </div>
            </div>

            <div class="border border-white/30 dark:border-white/15 rounded-2xl overflow-hidden max-w-md mx-auto shadow-sm">
              <EventEmbedCard :ev="event" />
            </div>
          </section>

          <footer class="px-6 py-4 border-t border-white/20 dark:border-white/10 bg-gradient-to-t from-white/60 to-white/20 dark:from-neutral-800/60 dark:to-neutral-800/30 backdrop-blur-sm flex gap-3 justify-end">
            <button class="px-5 py-2 rounded-full border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-neutral-800 transition" :disabled="submitting" @click="close">
              Cancel
            </button>
            <button
                class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:from-blue-400 disabled:to-indigo-400 text-white font-semibold rounded-full px-8 py-2 shadow-lg shadow-blue-600/30 transition-all active:scale-95 flex items-center justify-center min-w-[110px]"
                :disabled="submitting || !event?.id"
                @click="submit"
            >
              <span v-if="!submitting">Share</span>
              <svg v-else class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
              </svg>
            </button>
          </footer>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth'
import UserAvatar from '@/pages/auth/UserAvatar.vue'
import EventEmbedCard from '@/pages/home/components/modules/events/EventEmbedCard.vue'

const props = defineProps({
  visible: Boolean,
  event: Object,
  captionLimit: { type: Number, default: 280 }
})
const emit = defineEmits(['close', 'submitted'])

const auth = useAuthStore()
if (!auth.user) auth.fetchUser()

const user = computed(() => auth.user ?? {})
const userName = computed(() => user.value.name ?? '')
const avatarColor = computed(() => user.value.avatar_color ?? null)
const avatarSrc = computed(() => user.value.logo_url ?? null)

const taRef = ref(null)
const caption = ref('')
const submitting = ref(false)
const isRTL = computed(() => /[\u0591-\u07FF\uFB1D-\uFDFD\uFE70-\uFEFC]/.test(caption.value))
const placeholder = 'Add a comment (optional)…'
const hint = 'Your note will appear above the shared event.'

watch(() => props.visible, async v => {
  if (v) { await nextTick(); taRef.value?.focus() }
})

function limitInput() {
  if (props.captionLimit && caption.value.length > props.captionLimit) {
    caption.value = caption.value.slice(0, props.captionLimit)
  }
}
function close() {
  caption.value = ''
  emit('close')
}
function submit() {
  if (submitting.value || !props.event?.id) return
  submitting.value = true
  emit('submitted', { text: caption.value.trim() })
  caption.value = ''
  submitting.value = false
  close()
}
</script>

<style scoped>
.scale-fade-enter-from { opacity: 0; transform: scale(0.92); }
.scale-fade-enter-active { transition: all 0.25s ease; }
.scale-fade-leave-active { transition: all 0.2s ease; opacity: 0; transform: scale(0.92); }
</style>
