// src/stores/auth.js
import { defineStore } from 'pinia'
import axios from '@/lib/axios'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: sessionStorage.getItem('token') || null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        getUser: (state) => state.user,
    },

    actions: {
        async register(payload) {
            try {
                const response = await axios.post('/api/register', payload)
                this.token = response.data.token
                this.user = response.data.user
                sessionStorage.setItem('token', this.token)
                this.setAxiosHeader()
                const newTab = window.open('/dashboard', '_blank')
                setTimeout(() => {
                    newTab.postMessage({
                        token: this.token
                    }, window.location.origin)
                }, 500)

                window.location.href = '/'
            } catch (error) {
                throw error.response?.data || error
            }
        },
        async login(payload) {
            try {
                const response = await axios.post('/api/login', payload)
                this.token = response.data.token
                this.user = response.data.user
                sessionStorage.setItem('token', this.token)
                this.setAxiosHeader()
                const newTab = window.open('/dashboard', '_blank')
                setTimeout(() => {
                    newTab.postMessage({
                        token: this.token
                    }, window.location.origin)
                }, 500)

                window.location.href = '/'
            } catch (error) {
                throw error.response?.data || error
            }
        },

        async fetchUser() {
            if (!this.token) return

            try {
                this.setAxiosHeader()
                const response = await axios.get('/api/me')
                this.user = response.data
            } catch (error) {
                this.logout()
                throw error
            }
        },

        async logout() {
            try {
                await axios.post('/api/logout')
            } catch (e) {
                // Even if error, force cleanup
            } finally {
                this.clearAuth()
            }
        },

        async logoutAll() {
            try {
                await axios.post('/api/logout-all')
            } catch (e) {
                //
            } finally {
                this.clearAuth()
            }
        },

        setAxiosHeader() {
            if (this.token) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
            }
        },

        clearAuth() {
            this.token = null
            this.user = null
            sessionStorage.removeItem('token')
            delete axios.defaults.headers.common['Authorization']
        },
    },
})
