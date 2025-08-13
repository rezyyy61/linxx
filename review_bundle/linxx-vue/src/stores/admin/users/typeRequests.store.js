import { defineStore } from 'pinia'
import { listTypeRequestsApi, approveTypeRequestApi, rejectTypeRequestApi } from './typeRequests.api'

const err = e => e?.response?.data?.message || e?.message || 'Error'

export const useTypeRequestsStore = defineStore('adminTypeRequests', {
    state: () => ({
        byId: {},
        ids: [],
        loading: false,
        error: null,
        pagination: { page: 1, perPage: 20, total: 0, lastPage: 1 },
        filters: { q: '', requested: '' },
        acting: false
    }),
    getters: {
        list(s) { return s.ids.map(id => s.byId[id]).filter(Boolean) }
    },
    actions: {
        async fetchList(overrides = {}) {
            this.loading = true
            this.error = null
            const merged = { ...this.filters, page: this.pagination.page, perPage: this.pagination.perPage, ...overrides }
            const clean = v => (v === '' || v == null ? undefined : v)
            const params = {
                q: clean(merged.q),
                requested: clean(merged.requested),
                page: merged.page ?? 1,
                per_page: merged.perPage ?? 20
            }
            try {
                const res = await listTypeRequestsApi(params)
                const items = res.data || []
                const map = {}
                const ids = []
                for (const r of items) {
                    map[r.id] = r
                    ids.push(r.id)
                }
                this.byId = map
                this.ids = ids
                const meta = res.meta || {}
                this.pagination.page = meta.current_page ?? 1
                this.pagination.perPage = meta.per_page ?? 20
                this.pagination.total = meta.total ?? ids.length
                this.pagination.lastPage = meta.last_page ?? 1
                this.filters = { q: merged.q || '', requested: merged.requested || '' }
            } catch (e) {
                this.error = err(e)
                throw e
            } finally {
                this.loading = false
            }
        },
        async approve(userId) {
            this.acting = true
            try {
                await approveTypeRequestApi(userId)
                this.ids = this.ids.filter(id => {
                    const item = this.byId[id]
                    return item?.user?.id !== userId
                })
                for (const id of Object.keys(this.byId)) {
                    if (this.byId[id]?.user?.id === userId) delete this.byId[id]
                }
            } finally {
                this.acting = false
            }
        },
        async reject(userId) {
            this.acting = true
            try {
                await rejectTypeRequestApi(userId)
                this.ids = this.ids.filter(id => {
                    const item = this.byId[id]
                    return item?.user?.id !== userId
                })
                for (const id of Object.keys(this.byId)) {
                    if (this.byId[id]?.user?.id === userId) delete this.byId[id]
                }
            } finally {
                this.acting = false
            }
        },
        setFilters(p = {}) {
            this.filters = { ...this.filters, ...p }
            this.pagination.page = 1
        },
        setPage(p) {
            this.pagination.page = Math.max(1, Number(p) || 1)
        },
        setPerPage(n) {
            this.pagination.perPage = Math.max(1, Number(n) || 20)
            this.pagination.page = 1
        }
    }
})
