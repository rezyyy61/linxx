// src/stores/notifications.js
import { defineStore } from 'pinia'
import axios from '@/lib/axios'

function getEcho() {
    if (typeof window !== 'undefined' && window.Echo) return window.Echo
    return null
}

export const useNotificationsStore = defineStore('notifications', {
    state: () => ({
        items: [],
        page: 1,
        lastPage: 1,
        counter: { unseen: 0, unread: 0 },
        loading: false,
        live: false,
        channelName: null,
    }),
    getters: {
        badge(state) { return Number(state.counter.unseen || 0) },
        hasMore(state) { return state.page < state.lastPage },
    },
    actions: {
        async fetchCounter() {
            const { data } = await axios.get('/api/notifications/counter')
            this.counter = { unseen: data.unseen || 0, unread: data.unread || 0 }
        },
        async fetchLatest(page = 1, perPage = 10) {
            this.loading = true
            try {
                const { data } = await axios.get('/api/notifications', { params: { page, per_page: perPage } })
                const items = data.data || []
                const meta = data.meta || {}
                if (page === 1) this.items = items
                else this.items.push(...items)
                this.page = meta.current_page || page
                this.lastPage = meta.last_page || page
                if (meta.unseen !== undefined || meta.unread !== undefined) {
                    this.counter = {
                        unseen: meta.unseen ?? this.counter.unseen,
                        unread: meta.unread ?? this.counter.unread
                    }
                }
            } finally {
                this.loading = false
            }
        },
        async markAllSeen() {
            await axios.post('/api/notifications/seen')
            this.counter.unseen = 0
            const now = new Date().toISOString()
            this.items = this.items.map(i => ({ ...i, seen_at: i.seen_at || now }))
        },
        async markRead(id) {
            await axios.post(`/api/notifications/${id}/read`)
            const n = this.items.find(i => i.id === id)
            if (n) {
                const now = new Date().toISOString()
                n.read_at = n.read_at || now
                n.seen_at = n.seen_at || now
            }
            this.counter.unread = Math.max(0, Number(this.counter.unread) - 1)
            this.counter.unseen = Math.max(0, Number(this.counter.unseen) - 1)
        },
        connect(userId) {
            const echo = getEcho()
            if (!echo || this.live || !userId) return

            const name = `App.Models.User.${userId}`

            if (this.channelName && this.channelName !== name) {
                try { echo.leave(this.channelName) } catch {}
                this.channelName = null
                this.live = false
            }

            echo.private(name).listen('.notifications.created', (payload) => {
                this.items.unshift(payload)
                this.counter.unseen = Number(this.counter.unseen || 0) + 1
                this.counter.unread = Number(this.counter.unread || 0) + 1
            })

            this.channelName = name
            this.live = true
        },
        disconnect() {
            const echo = getEcho()
            if (echo && this.channelName) {
                try { echo.leave(this.channelName) } catch {}
            }
            this.channelName = null
            this.live = false
        }
    }
})
