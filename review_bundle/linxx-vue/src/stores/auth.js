// src/stores/auth.js
import { defineStore } from 'pinia'
import axios from '@/lib/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    pendingVerificationEmail: localStorage.getItem('pendingVerificationEmail') || null,
    userLoading: false,
    _ensurePromise: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    getUser: (state) => state.user,
    safeUser: (s) => s.user ?? { id: null, slug: '', email: '', avatar_color: null, logo_url: null },
    currentUserId: (s) => s.user?.id ?? s.user?.data?.id ?? null
  },

  actions: {
    async ensureUser() {
      if (!this.token) return null
      if (this.user) return this.user
      if (this._ensurePromise) return this._ensurePromise

      this.userLoading = true
      this._ensurePromise = this.fetchUser()
          .catch((e) => { throw e })
          .finally(() => {
            this.userLoading = false
            this._ensurePromise = null
          })

      return this._ensurePromise
    },

    async register(payload) {
      try {
        await axios.post('/api/auth/register', payload)
        this.setPendingVerificationEmail(payload.email)
        window.location.href = '/verify-email'
      } catch (error) {
        throw error.response?.data || error
      }
    },

    async login(payload) {
      try {
        const response = await axios.post('/api/auth/login', payload)

        this.setAuthData(response.data.token, response.data.user)

        const newTab = window.open('/dashboard', '_blank')
        setTimeout(() => {
          newTab.postMessage({ token: this.token }, window.location.origin)
        }, 500)

        window.location.href = '/'
      } catch (error) {
        if (error.response?.status === 403) {
          this.setPendingVerificationEmail(payload.email)
          window.location.href = '/verify-email'
          return
        }
        throw error.response?.data || error
      }
    },

    async verifyEmail(code) {
      try {
        const response = await axios.post('/api/auth/verify-email', {
          email: this.pendingVerificationEmail,
          code
        })

        if (response.data.token) {
          this.setAuthData(response.data.token, response.data.user)
          this.clearPendingVerificationEmail()

          const newTab = window.open('/dashboard', '_blank')
          setTimeout(() => {
            newTab.postMessage({ token: this.token }, window.location.origin)
          }, 500)

          window.location.href = '/'
        } else {
          this.clearPendingVerificationEmail()
          window.location.href = '/login'
        }
      } catch (error) {
        throw error.response?.data || error
      }
    },

    async resendCode() {
      try {
        await axios.post('/api/auth/resend-code', {
          email: this.pendingVerificationEmail
        })
      } catch (error) {
        throw error.response?.data || error
      }
    },

    async fetchUser() {
      if (!this.token) return
      try {
        this.setAxiosHeader()
        const response = await axios.get('/api/me')
        this.user = this.normalizeUser(response.data)
      } catch (error) {
        this.clearAuth()
        window.location.href = '/login'
        throw error
      }
    },

    async logout() {
      try {
        await axios.post('/api/logout')
      } catch (e) {
      } finally {
        this.clearAuth()
        window.location.href = '/login'
      }
    },

    async logoutAll() {
      try {
        await axios.post('/api/logout-all')
      } catch (e) {
      } finally {
        this.clearAuth()
        window.location.href = '/login'
      }
    },

    normalizeUser(u) {
      return u?.data ?? u ?? null
    },

    setAuthData(token, user) {
      this.token = token
      this.user = this.normalizeUser(user)
      localStorage.setItem('token', token)
      this.setAxiosHeader()
    },

    setAxiosHeader() {
      if (this.token) {
        axios.defaults.headers.common.Authorization = `Bearer ${this.token}`
      }
    },

    setPendingVerificationEmail(email) {
      this.pendingVerificationEmail = email
      localStorage.setItem('pendingVerificationEmail', email)
    },

    clearPendingVerificationEmail() {
      this.pendingVerificationEmail = null
      localStorage.removeItem('pendingVerificationEmail')
    },

    clearAuth() {
      this.token = null
      this.user = null
      this.clearPendingVerificationEmail()
      localStorage.removeItem('token')
      delete axios.defaults.headers.common.Authorization
    },

    initAuthSync() {
      window.addEventListener('storage', (event) => {
        if (event.key === 'token' && !event.newValue) {
          this.clearAuth()
          window.location.href = '/login'
        }
      })
    }
  }
})
