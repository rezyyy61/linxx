<template>
  <div class="flex items-center justify-center min-h-screen">
    <p>در حال ورود…</p>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'SocialLoginCallback',
  async mounted () {
    const auth = useAuthStore()
    const { token, user } = this.$route.query

    if (token && user) {
      try {
        const parsedUser = JSON.parse(decodeURIComponent(user))
        auth.setAuthData(token, parsedUser)

        const dash = `${window.location.origin}/dashboard`
        const pop = window.open(dash, '_blank', 'noopener,noreferrer')

        if (pop) {
          window.location.replace('/')
        } else {
          window.location.href = dash
        }
      } catch (e) {
        window.location.href = '/login'
      }
    } else {
      window.location.href = '/login'
    }
  }
}
</script>
