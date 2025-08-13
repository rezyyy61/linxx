<template>
  <div>
    <button
        type="button"
        :disabled="disabled || isCreating"
        @click="open = true"
        class="rounded-lg border px-3 py-1.5 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5 disabled:opacity-60"
    >
      <span v-if="isCreating">{{ t('dashboard.common.sending') || 'Sending…' }}</span>
      <span v-else>{{ t('dashboard.event.actions.invite') || 'Invite' }}</span>
    </button>

    <div v-if="open" class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" @click="close"></div>
      <div class="absolute inset-0 grid place-items-center p-4">
        <div class="w-full max-w-md rounded-2xl border border-zinc-200 bg-white p-5 shadow-xl dark:border-white/10 dark:bg-[#0b0f1a]">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                {{ t('dashboard.event.invite.title') || 'Invite user' }}
              </h3>
              <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                {{ event?.title }} · {{ prettyDate }}
              </p>
            </div>
            <button type="button" class="rounded-lg p-1 hover:bg-zinc-100 dark:hover:bg-white/10" @click="close" aria-label="Close">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-zinc-500" viewBox="0 0 24 24" fill="currentColor"><path d="M6.4 4.98 12 10.6l5.6-5.61 1.42 1.41L13.42 12l5.6 5.6-1.41 1.42L12 13.42l-5.6 5.6-1.42-1.41 5.61-5.6-5.61-5.61z"/></svg>
            </button>
          </div>

          <div class="mt-5 space-y-3">
            <label class="block text-sm font-medium">
              {{ t('dashboard.event.invite.user_id_label') || 'User ID' }}
            </label>
            <input
                v-model.trim="userIdInput"
                type="number"
                min="1"
                class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/40 dark:border-white/10 dark:bg-[#0e1526] dark:text-white"
                :placeholder="t('dashboard.event.invite.user_id_placeholder') || 'Enter user ID'"
            />
            <p v-if="errorText" class="text-sm text-rose-600 dark:text-rose-400">{{ errorText }}</p>
            <p v-if="successText" class="text-sm text-emerald-600 dark:text-emerald-400">{{ successText }}</p>
          </div>

          <div class="mt-6 flex items-center justify-end gap-2">
            <button
                type="button"
                class="rounded-lg border px-3 py-2 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
                :disabled="isCreating"
                @click="close"
            >
              {{ t('dashboard.common.cancel') || 'Cancel' }}
            </button>
            <button
                type="button"
                class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700 disabled:opacity-60 dark:bg-fuchsia-600 dark:hover:bg-fuchsia-700"
                :disabled="disabled || isCreating"
                @click="submit"
            >
              <span v-if="isCreating">{{ t('dashboard.common.sending') || 'Sending…' }}</span>
              <span v-else>{{ t('dashboard.event.invite.send') || 'Send invite' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useEventInviteStore } from '@/stores/events/invites.store'

const props = defineProps({
  event: { type: Object, required: true }
})
const emit = defineEmits(['sent', 'close'])

const { t, locale } = useI18n({ useScope: 'global' })
const invites = useEventInviteStore()

const open = ref(false)
const userIdInput = ref('')
const errorText = ref('')
const successText = ref('')

const isCreating = computed(() => !!invites.creatingByEvent[props.event?.id])
const disabled = computed(() => !Number(userIdInput.value) || isCreating.value)

const prettyDate = computed(() => {
  const d = props.event?.starts_at ? new Date(props.event.starts_at) : null
  if (!d) return ''
  try {
    return new Intl.DateTimeFormat(locale.value || 'en', {
      year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit'
    }).format(d)
  } catch { return '' }
})

function close() {
  open.value = false
  errorText.value = ''
  successText.value = ''
  userIdInput.value = ''
  emit('close')
}

async function submit() {
  if (!props.event?.id) return
  errorText.value = ''
  successText.value = ''
  try {
    const invite = await invites.invite(props.event.id, Number(userIdInput.value))
    successText.value = t('dashboard.event.invite.success') || 'Invitation sent'
    emit('sent', invite)
    setTimeout(close, 900)
  } catch (e) {
    errorText.value =
        invites.createErrorByEvent[props.event.id] ||
        t('dashboard.event.invite.error') ||
        'Failed to send invite'
  }
}
</script>
