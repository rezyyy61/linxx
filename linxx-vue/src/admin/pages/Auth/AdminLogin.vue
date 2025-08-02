<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md p-6 bg-white rounded shadow">
      <h2 class="text-xl font-bold mb-4 text-center">Admin Login</h2>

      <!-- Login Form -->
      <form @submit.prevent="submitLogin" v-if="step === 'login'">
        <input v-model="form.email" type="email" placeholder="Email" class="input" />
        <input v-model="form.password" type="password" placeholder="Password" class="input mt-2" />
        <button type="submit" class="btn w-full mt-4">Login</button>
      </form>

      <!-- 2FA Setup + Code Form -->
      <form @submit.prevent="submit2FA" v-else-if="step === '2fa'">
        <div v-if="qrUrl && secret" class="mb-6 text-center">
          <p class="text-sm text-gray-700 mb-2">Scan this QR code with Google Authenticator:</p>
          <img :src="qrUrl" alt="QR Code" class="mx-auto mb-4 w-40 h-40 border" />

          <div class="flex items-center justify-center space-x-2">
            <p class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded break-all font-mono">
              {{ secret }}
            </p>
            <button
                type="button"
                @click="copySecret"
                class="text-xs bg-gray-300 hover:bg-gray-400 px-2 py-1 rounded"
            >
              Copy
            </button>
          </div>
        </div>

        <input v-model="code" type="text" placeholder="Enter 2FA Code" class="input mt-4" />
        <button type="submit" class="btn w-full mt-4">Verify</button>
      </form>

      <p class="text-red-500 text-sm mt-4 text-center" v-if="error">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '@/lib/axios'

const step = ref('login')
const form = ref({ email: '', password: '' })
const code = ref('')
const userId = ref(null)
const error = ref(null)
const qrUrl = ref(null)
const secret = ref(null)

const submitLogin = async () => {
  error.value = null

  try {
    const { data } = await axios.post('/admin/login', form.value)

    if (data?.data?.token) {
      localStorage.setItem('admin_token', data.data.token)
      localStorage.setItem('admin_user', JSON.stringify(data.data.user))
      window.location.href = '/admin/dashboard'
    } else if (data?.data?.['2fa_required']) {
      userId.value = data.data.user_id
      step.value = '2fa'

      try {
        const res = await axios.post('/admin/2fa/setup', {
          user_id: userId.value,
        })

        const qr = res?.data?.data?.qr_code_url
        const sec = res?.data?.data?.secret

        qrUrl.value = qr || null
        secret.value = sec || null
      } catch (setupErr) {
        console.warn('2FA setup failed or skipped', setupErr)
        qrUrl.value = null
        secret.value = null
      }
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed'
  }
}

const submit2FA = async () => {
  error.value = null

  try {
    const { data } = await axios.post('/admin/verify-2fa-login', {
      user_id: userId.value,
      code: code.value,
    })

    localStorage.setItem('admin_token', data.token)
    localStorage.setItem('admin_user', JSON.stringify(data.user))
    window.location.href = '/admin/dashboard'
  } catch (err) {
    error.value = err.response?.data?.message || 'Verification failed'
  }
}

const copySecret = async () => {
  try {
    await navigator.clipboard.writeText(secret.value)
    alert('Secret copied to clipboard!')
  } catch (e) {
    alert('Failed to copy secret.')
  }
}
</script>

<style scoped>
.input {
  @apply w-full px-3 py-2 border rounded;
}
.btn {
  @apply bg-blue-600 text-white py-2 rounded hover:bg-blue-700;
}
</style>
