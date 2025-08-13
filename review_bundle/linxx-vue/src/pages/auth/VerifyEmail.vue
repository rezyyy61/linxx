<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg space-y-6">
      <h2 class="text-2xl font-bold text-center text-gray-900 dark:text-white">
        {{ $t('auth.verifyEmailTitle') }}
      </h2>
      <p class="text-center text-gray-600 dark:text-gray-300">
        {{ $t('auth.verifyEmailDescription', { email: pendingEmail }) }}
      </p>

      <!-- Code input -->
      <input
          v-model="code"
          maxlength="6"
          type="text"
          class="form-input text-center tracking-widest text-lg"
          :placeholder="$t('auth.codePlaceholder')"
      />

      <!-- Error -->
      <p v-if="error" class="text-sm text-red-500 text-center">{{ error }}</p>

      <!-- Verify button -->
      <button
          @click="handleVerify"
          class="w-full btn-red"
          :disabled="loading || code.length !== 6"
      >
        <span v-if="loading">{{ $t('auth.verifying') }}</span>
        <span v-else>{{ $t('auth.verifyButton') }}</span>
      </button>

      <!-- Resend -->
      <div class="text-center space-y-2">
        <button
            @click="handleResend"
            class="text-sm text-blue-600 hover:underline disabled:opacity-50"
            :disabled="resendDisabled || loading"
        >
          {{ $t('auth.resendCode') }}
        </button>
        <p v-if="resendDisabled" class="text-xs text-gray-500">
          {{ $t('auth.resendAvailableIn', { seconds: countdown }) }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const store = useAuthStore()

const code = ref('')
const loading = ref(false)
const error = ref('')
const countdown = ref(0)
let timer = null

const pendingEmail = computed(() => store.pendingVerificationEmail)
const resendDisabled = computed(() => countdown.value > 0)

onMounted(() => {
  startCountdown()
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})

function startCountdown() {
  countdown.value = 60
  timer = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) clearInterval(timer)
  }, 1000)
}

async function handleVerify() {
  loading.value = true
  error.value = ''
  try {
    await store.verifyEmail(code.value)
  } catch (err) {
    error.value = err.message || t('auth.invalidCode')
  } finally {
    loading.value = false
  }
}

async function handleResend() {
  loading.value = true
  try {
    await store.resendCode()
    startCountdown()
  } catch (err) {
    error.value = err.message || t('auth.resendFailed')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.form-input {
  @apply w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg
  bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400
  focus:outline-none focus:ring-2 focus:ring-red-500 transition;
}
.btn-red {
  @apply px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition;
}
</style>
