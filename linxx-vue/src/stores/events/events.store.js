import { defineStore } from 'pinia'
import {
    listEventsApi,
    showEventApi,
    createEventApi,
    updateEventApi,
    deleteEventApi, updateEventStatusApi,
} from './events.api'

const getErr = (err) => err?.response?.data?.message || err?.message || 'خطای نامشخص'

export const useEventStore = defineStore('events', {
    state: () => ({
        byId: {},
        ids: [],
        listLoading: false,
        listError: null,
        pagination: { page: 1, per_page: 15, total: 0, last_page: 1 },
        filters: { creator_id: null, public_only: true, upcoming: true },
        creating: false,
        createError: null,
        lastCreatedId: null,
        fetchingOne: false,
        fetchOneError: null,
        saving: false,
        saveError: null,
        deleting: false,
        deleteError: null,
    }),

    getters: {
        list(state) {
            return state.ids.map((id) => state.byId[id]).filter(Boolean)
        },
        getById: (state) => (id) => state.byId[id] ?? null,
        lastCreated(state) {
            return state.lastCreatedId ? state.byId[state.lastCreatedId] ?? null : null
        },
    },

    actions: {
        async fetchList(overrides = {}) {
            this.listLoading = true
            this.listError = null
            try {
                const query = {
                    page: overrides.page ?? this.pagination.page,
                    per_page: overrides.per_page ?? this.pagination.per_page,
                    creator_id: overrides.creator_id ?? this.filters.creator_id ?? undefined,
                    public_only: overrides.public_only ?? this.filters.public_only,
                    upcoming: overrides.upcoming ?? this.filters.upcoming,
                }

                const res = await listEventsApi(query)

                const map = {}
                const ids = []
                for (const e of res.data) {
                    map[e.id] = e
                    ids.push(e.id)
                }

                this.byId = { ...this.byId, ...map }
                this.ids = ids

                const meta = res.meta || {}
                this.pagination.page = meta.current_page ?? query.page
                this.pagination.per_page = meta.per_page ?? query.per_page
                this.pagination.total = meta.total ?? ids.length
                this.pagination.last_page = meta.last_page ?? 1

                this.filters.creator_id = query.creator_id ?? null
                this.filters.public_only = !!query.public_only
                this.filters.upcoming = !!query.upcoming
            } catch (err) {
                this.listError = getErr(err)
                throw err
            } finally {
                this.listLoading = false
            }
        },

        async fetchOne(id, { force = false } = {}) {
            if (!force && this.byId[id]) return this.byId[id]
            this.fetchingOne = true
            this.fetchOneError = null
            try {
                const e = await showEventApi(id)
                this.byId[e.id] = e
                if (!this.ids.includes(e.id)) this.ids.unshift(e.id)
                return e
            } catch (err) {
                this.fetchOneError = getErr(err)
                throw err
            } finally {
                this.fetchingOne = false
            }
        },

        async create(payload, files = {}) {
            this.creating = true
            this.createError = null
            try {
                const event = await createEventApi(payload, files)
                this.byId[event.id] = event
                this.ids.unshift(event.id)
                this.lastCreatedId = event.id
                return event
            } catch (err) {
                this.createError = getErr(err)
                throw err
            } finally {
                this.creating = false
            }
        },

        async update(id, payload, files = {}) {
            this.saving = true
            this.saveError = null
            try {
                const event = await updateEventApi(id, payload, files)
                this.byId[event.id] = event
                return event
            } catch (err) {
                this.saveError = getErr(err)
                throw err
            } finally {
                this.saving = false
            }
        },

        async updateStatus(id, status) {
            this.saving = true
            this.saveError = null
            try {
                const event = await updateEventStatusApi(id, { status })
                this.byId[event.id] = event
                return event
            } catch (err) {
                this.saveError = getErr(err)
                throw err
            } finally {
                this.saving = false
            }
        },

        async remove(id) {
            this.deleting = true
            this.deleteError = null
            try {
                await deleteEventApi(id)
                delete this.byId[id]
                this.ids = this.ids.filter((x) => x !== id)
            } catch (err) {
                this.deleteError = getErr(err)
                throw err
            } finally {
                this.deleting = false
            }
        },

        resetCreateState() {
            this.creating = false
            this.createError = null
            this.lastCreatedId = null
        },
    },
})
