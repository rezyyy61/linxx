import { defineStore } from 'pinia'
import api from '@/lib/axios'

export const useEventInviteStore = defineStore('eventInvites', {
    state: () => ({
        byId: {},
        idsByEvent: {},
        listLoadingByEvent: {},
        listErrorByEvent: {},
        paginationByEvent: {},
        creatingByEvent: {},
        createErrorByEvent: {},
        respondingByInvite: {},
        respondErrorByInvite: {},
        deletingByInvite: {},
        deleteErrorByInvite: {},
    }),

    getters: {
        list: (state) => (eventId) => {
            const ids = state.idsByEvent[eventId] || []
            return ids.map((id) => state.byId[id]).filter(Boolean)
        },
        isListLoading: (state) => (eventId) => !!state.listLoadingByEvent[eventId],
        listError: (state) => (eventId) => state.listErrorByEvent[eventId] || null,
        pagination:
            (state) =>
                (eventId) =>
                    state.paginationByEvent[eventId] || {
                        page: 1,
                        per_page: 20,
                        total: 0,
                        last_page: 1,
                    },
    },

    actions: {
        async fetchList(eventId, overrides = {}) {
            this.listLoadingByEvent = {
                ...this.listLoadingByEvent,
                [eventId]: true,
            }
            this.listErrorByEvent = {
                ...this.listErrorByEvent,
                [eventId]: null,
            }

            try {
                const current =
                    this.paginationByEvent[eventId] || { page: 1, perPage: 20 }
                const page =
                    overrides.page != null ? overrides.page : current.page
                const perPage =
                    overrides.per_page != null ? overrides.perPage : current.perPage

                const res = await api.get(`/api/events/${eventId}/invites`, {
                    params: { page, perPage },
                })
                const payload = res?.data || {}
                const data = Array.isArray(payload.data) ? payload.data : []
                const meta = payload.meta || {}

                const map = {}
                const ids = []
                for (const inv of data) {
                    map[inv.id] = inv
                    ids.push(inv.id)
                }

                this.byId = { ...this.byId, ...map }
                this.idsByEvent = { ...this.idsByEvent, [eventId]: ids }
                this.paginationByEvent = {
                    ...this.paginationByEvent,
                    [eventId]: {
                        page: meta.current_page ?? page,
                        perPage: meta.perPage ?? perPage,
                        total: meta.total ?? ids.length,
                        last_page: meta.last_page ?? 1,
                    },
                }
            } catch (e) {
                const msg =
                    e?.response?.data?.message || e?.message || 'Failed to load invites'
                this.listErrorByEvent = {
                    ...this.listErrorByEvent,
                    [eventId]: msg,
                }
                throw e
            } finally {
                this.listLoadingByEvent = {
                    ...this.listLoadingByEvent,
                    [eventId]: false,
                }
            }
        },

        async invite(eventId, userId) {
            this.creatingByEvent = {
                ...this.creatingByEvent,
                [eventId]: true,
            }
            this.createErrorByEvent = {
                ...this.createErrorByEvent,
                [eventId]: null,
            }

            try {
                const res = await api.post(`/api/events/${eventId}/invites`, {
                    user_id: userId,
                })
                const invite = res?.data?.data || res?.data || null
                if (invite?.id) {
                    this.byId = { ...this.byId, [invite.id]: invite }
                    const current = this.idsByEvent[eventId] || []
                    this.idsByEvent = {
                        ...this.idsByEvent,
                        [eventId]: [invite.id, ...current.filter((i) => i !== invite.id)],
                    }
                }
                return invite
            } catch (e) {
                const msg =
                    e?.response?.data?.message || e?.message || 'Failed to invite user'
                this.createErrorByEvent = {
                    ...this.createErrorByEvent,
                    [eventId]: msg,
                }
                throw e
            } finally {
                this.creatingByEvent = {
                    ...this.creatingByEvent,
                    [eventId]: false,
                }
            }
        },

        async respond(inviteId, status) {
            this.respondingByInvite = {
                ...this.respondingByInvite,
                [inviteId]: true,
            }
            this.respondErrorByInvite = {
                ...this.respondErrorByInvite,
                [inviteId]: null,
            }

            try {
                const res = await api.patch(`/api/event-invites/${inviteId}`, {
                    status,
                })
                const updated = res?.data?.data || res?.data || null
                if (updated?.id) {
                    this.byId = { ...this.byId, [updated.id]: updated }
                }
                return updated
            } catch (e) {
                const msg =
                    e?.response?.data?.message || e?.message || 'Failed to respond'
                this.respondErrorByInvite = {
                    ...this.respondErrorByInvite,
                    [inviteId]: msg,
                }
                throw e
            } finally {
                this.respondingByInvite = {
                    ...this.respondingByInvite,
                    [inviteId]: false,
                }
            }
        },

        async remove(inviteId) {
            this.deletingByInvite = {
                ...this.deletingByInvite,
                [inviteId]: true,
            }
            this.deleteErrorByInvite = {
                ...this.deleteErrorByInvite,
                [inviteId]: null,
            }

            try {
                await api.delete(`/api/event-invites/${inviteId}`)
                const invite = this.byId[inviteId]
                if (invite?.event_id) {
                    const eventId = invite.event_id
                    const ids = (this.idsByEvent[eventId] || []).filter(
                        (id) => id !== inviteId
                    )
                    this.idsByEvent = { ...this.idsByEvent, [eventId]: ids }
                } else {
                    const clone = { ...this.idsByEvent }
                    for (const key of Object.keys(clone)) {
                        clone[key] = clone[key].filter((id) => id !== inviteId)
                    }
                    this.idsByEvent = clone
                }
                const next = { ...this.byId }
                delete next[inviteId]
                this.byId = next
            } catch (e) {
                const msg =
                    e?.response?.data?.message || e?.message || 'Failed to delete invite'
                this.deleteErrorByInvite = {
                    ...this.deleteErrorByInvite,
                    [inviteId]: msg,
                }
                throw e
            } finally {
                this.deletingByInvite = {
                    ...this.deletingByInvite,
                    [inviteId]: false,
                }
            }
        },

        resetEvent(eventId) {
            const clone = { ...this.idsByEvent }
            delete clone[eventId]
            this.idsByEvent = clone

            const l = { ...this.listLoadingByEvent }
            delete l[eventId]
            this.listLoadingByEvent = l

            const le = { ...this.listErrorByEvent }
            delete le[eventId]
            this.listErrorByEvent = le

            const p = { ...this.paginationByEvent }
            delete p[eventId]
            this.paginationByEvent = p
        },
    },
})
