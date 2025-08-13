// src/stores/shares/index.js
import { defineStore } from 'pinia'
import { createShare, getShare, revokeShare } from './api'

export const useShareStore = defineStore('share', {
    state: () => ({
        shares: [],
        loading: false,
        error: null,
    }),

    actions: {
        async create(payload) {
            this.loading = true
            this.error = null
            try {
                const share = await createShare(payload)
                this.shares.push(share)
                return share
            } catch (err) {
                this.error = err?.response?.data?.message || 'مشکل در ساخت اشتراک‌گذاری'
                throw err
            } finally {
                this.loading = false
            }
        },

        async fetchBySlug(slug) {
            this.loading = true
            this.error = null
            try {
                const share = await getShare(slug)
                return share
            } catch (err) {
                this.error = err?.response?.data?.message || 'مشکل در دریافت اطلاعات'
                throw err
            } finally {
                this.loading = false
            }
        },

        async revoke(id) {
            this.loading = true
            this.error = null
            try {
                await revokeShare(id)
                this.shares = this.shares.map(s =>
                    s.id === id ? { ...s, revoked_at: new Date().toISOString() } : s
                )
            } catch (err) {
                this.error = err?.response?.data?.message || 'مشکل در لغو اشتراک‌گذاری'
                throw err
            } finally {
                this.loading = false
            }
        },
    },
})
