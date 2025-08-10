// src/features/events/rsvp.store.js
import {defineStore} from 'pinia'
import {cancelRsvpApi, listAttendeesApi, rsvpApi} from './rsvp.api'

const msg = (e, fb) => e?.response?.data?.message || e?.message || fb

const userKey = (it) => {
    const u = it?.user || {}
    return u.id != null ? `id:${u.id}` : (u.slug ? `slug:${u.slug}` : `nouser`)
}

// keep-last: اگر یک کاربر چند بار تکرار شد، آخرین رکوردش بمونه
function uniqueByUserKeepLast(list) {
    const m = new Map()
    for (const it of (list || [])) m.set(userKey(it), it)
    return Array.from(m.values())
}

function isMe(it, currentUserId) {
    return it?.user?.is_current === true || (currentUserId != null && it?.user?.id === currentUserId)
}

export const useRsvpStore = defineStore('rsvp', {
    state: () => ({
        attendeesByEvent: {},
        loadingByEvent: {},
        savingByEvent: {},
        errorByEvent: {},
        myStatusByEvent: {},
        currentUserId: null,
    }),

    getters: {
        attendees: (s) => (eventId) => s.attendeesByEvent[eventId] ?? [],
        isLoading: (s) => (eventId) => !!s.loadingByEvent[eventId],
        isSaving: (s) => (eventId) => !!s.savingByEvent[eventId],
        error: (s) => (eventId) => s.errorByEvent[eventId] ?? null,

        myStatus: (s) => (eventId) => {
            if (eventId == null) return null
            const cached = s.myStatusByEvent[eventId]
            if (cached !== undefined) return cached
            const list = s.attendeesByEvent[eventId] || []
            const mine = list.find(x => x?.user?.is_current === true) ||
                (s.currentUserId != null ? list.find(x => x?.user?.id === s.currentUserId) : null)
            return mine?.status ?? null
        },

        counts: (s) => (eventId) => {
            const list = s.attendeesByEvent[eventId] || []
            let going = 0; let interested = 0
            for (const it of list) {
                if (it?.status === 'going') going++
                else if (it?.status === 'interested') interested++
            }
            return { going, interested }
        },

        goingAvatars: (s) => (eventId, limit = 8) =>
            (s.attendeesByEvent[eventId] || []).filter(x => x?.status === 'going').slice(0, limit),

        goingMoreCount: (s) => (eventId, shown = 8) => {
            const total = (s.attendeesByEvent[eventId] || []).filter(x => x?.status === 'going').length
            return Math.max(0, total - shown)
        },
    },

    actions: {
        setCurrentUser(id) { this.currentUserId = id ?? null },

        async fetchAttendees(eventId) {
            if (eventId == null) return
            this.loadingByEvent[eventId] = true
            this.errorByEvent[eventId] = null
            try {
                const list = await listAttendeesApi(eventId)
                console.log(list)
                const clean = uniqueByUserKeepLast(Array.isArray(list) ? list : [])
                this.attendeesByEvent[eventId] = clean

                const mine = clean.find(x => x?.user?.is_current === true) ||
                    (this.currentUserId != null ? clean.find(x => x?.user?.id === this.currentUserId) : null)
                if (mine) this.myStatusByEvent[eventId] = mine.status
                else if (this.myStatusByEvent[eventId] === undefined) this.myStatusByEvent[eventId] = null
            } catch (e) {
                this.errorByEvent[eventId] = msg(e, 'Failed to load attendees')
                throw e
            } finally {
                this.loadingByEvent[eventId] = false
            }
        },

        async setStatus(eventId, status) {
            if (eventId == null) return
            this.savingByEvent[eventId] = true
            this.errorByEvent[eventId] = null

            const prevList = [...(this.attendeesByEvent[eventId] || [])]
            const prevStatus = this.myStatusByEvent[eventId] ?? null

            try {
                // optimistic: هر چی رکورد «من» هست حذف، یکی جدید می‌گذاریم
                let list = prevList.filter(it => !isMe(it, this.currentUserId))
                list.unshift({ user: { id: this.currentUserId, is_current: true }, status })
                this.attendeesByEvent[eventId] = uniqueByUserKeepLast(list)
                this.myStatusByEvent[eventId] = status

                // سرور
                const res = await rsvpApi(eventId, status)
                const key = userKey(res)
                list = (this.attendeesByEvent[eventId] || []).filter(it => userKey(it) !== key && !isMe(it, this.currentUserId))
                list.unshift(res)
                this.attendeesByEvent[eventId] = uniqueByUserKeepLast(list)
                this.myStatusByEvent[eventId] = res?.status ?? status
            } catch (e) {
                this.attendeesByEvent[eventId] = prevList
                this.myStatusByEvent[eventId] = prevStatus
                this.errorByEvent[eventId] = msg(e, 'Failed to set RSVP')
                throw e
            } finally {
                this.savingByEvent[eventId] = false
            }
        },

        async cancel(eventId) {
            if (eventId == null) return
            this.savingByEvent[eventId] = true
            this.errorByEvent[eventId] = null

            const prevList = [...(this.attendeesByEvent[eventId] || [])]
            const prevStatus = this.myStatusByEvent[eventId] ?? null

            try {
                this.attendeesByEvent[eventId] = prevList.filter(it => !isMe(it, this.currentUserId))
                this.myStatusByEvent[eventId] = null
                await cancelRsvpApi(eventId)
            } catch (e) {
                this.attendeesByEvent[eventId] = prevList
                this.myStatusByEvent[eventId] = prevStatus
                this.errorByEvent[eventId] = msg(e, 'Failed to cancel RSVP')
                throw e
            } finally {
                this.savingByEvent[eventId] = false
            }
        },
    },
})
