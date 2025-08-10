// src/stores/events/public/public.store.js
import { defineStore } from 'pinia'
import { listPublicEventsApi, showPublicEventApi } from './public.api'

const errMsg = (e) => e?.response?.data?.message || e?.message || 'خطا'

export const usePublicEventsStore = defineStore('publicEvents', {
    state: () => ({
        byId: {},
        bySlug: {},
        ids: [],
        listLoading: false,
        listError: null,
        pagination: { page: 1, perPage: 16, total: 0, lastPage: 1 },
        filters: { q: '', city: '', type: '', mode: '', creatorId: null, from: '', to: '', upcoming: true, sort: 'soon' },
        fetchingOne: false,
        fetchOneError: null
    }),

    getters: {
        list(state) { return state.ids.map(id => state.byId[id]).filter(Boolean) },
        getById: (s) => (id) => s.byId[id] ?? null,
        getByIdOrSlug: (s) => (idOrSlug) => {
            const id = s.bySlug[idOrSlug] ?? idOrSlug
            return s.byId[id] ?? null
        },
    },

    actions: {
        async fetchList(overrides = {}) {
            this.listLoading = true
            this.listError = null

            const merged = {
                ...this.filters,
                page: this.pagination.page,
                perPage: this.pagination.perPage,
                ...overrides
            }
            const clean = v => (v === '' || v == null ? undefined : v)
            const query = {
                q: clean(merged.q),
                city: clean(merged.city),
                type: clean(merged.type),
                mode: clean(merged.mode),
                from: clean(merged.from),
                to: clean(merged.to),
                upcoming: typeof merged.upcoming === 'boolean' ? merged.upcoming : undefined,
                sort: clean(merged.sort),
                page: merged.page ?? 1,
                per_page: merged.perPage ?? 16,
            }

            try {
                const res = await listPublicEventsApi(query)
                const items = res.data || []

                const map = {}
                const slugMap = {}
                const ids = []
                for (const ev of items) {
                    map[ev.id] = ev
                    if (ev.slug) slugMap[ev.slug] = ev.id
                    ids.push(ev.id)
                }
                this.byId = { ...this.byId, ...map }
                this.bySlug = { ...this.bySlug, ...slugMap }
                this.ids = ids

                const meta = res.meta || {}
                this.pagination.page = meta.current_page ?? (merged.page ?? 1)
                this.pagination.perPage = meta.per_page ?? (merged.perPage ?? 16)
                this.pagination.total = meta.total ?? ids.length
                this.pagination.lastPage = meta.last_page ?? 1

                this.filters = {
                    ...this.filters,
                    q: merged.q || '',
                    city: merged.city || '',
                    type: merged.type || '',
                    mode: merged.mode || '',
                    from: merged.from || '',
                    to: merged.to || '',
                    upcoming: typeof merged.upcoming === 'boolean' ? merged.upcoming : this.filters.upcoming,
                    sort: merged.sort || this.filters.sort,
                }
            } catch (e) {
                this.listError = errMsg(e)
                throw e
            } finally {
                this.listLoading = false
            }
        },

        async fetchOne(idOrSlug, { force = false } = {}) {
            if (!idOrSlug) return null

            // کش با اسلاگ یا آیدی
            const cachedId = this.bySlug[idOrSlug] ?? (this.byId[idOrSlug] ? idOrSlug : null)
            const cached = cachedId ? this.byId[cachedId] : null
            if (cached && !force) return cached

            this.fetchingOne = true
            this.fetchOneError = null
            try {
                const ev = await showPublicEventApi(idOrSlug)
                this.byId[ev.id] = ev
                if (ev.slug) this.bySlug[ev.slug] = ev.id
                // مهم: ids رو دست نمی‌زنیم
                return ev
            } catch (e) {
                this.fetchOneError = errMsg(e)
                throw e
            } finally {
                this.fetchingOne = false
            }
        },

        reset() {
            this.byId = {}
            this.bySlug = {}
            this.ids = []
            this.listLoading = false
            this.listError = null
            this.pagination = { page: 1, perPage: 16, total: 0, lastPage: 1 }
            this.filters = { q: '', city: '', type: '', mode: '', creatorId: null, from: '', to: '', upcoming: true, sort: 'soon' }
            this.fetchingOne = false
            this.fetchOneError = null
        }
    }
})
