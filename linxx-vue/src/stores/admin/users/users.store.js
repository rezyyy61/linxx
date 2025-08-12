// src/stores/admin/users/users.store.js
import { defineStore } from 'pinia'
import { listUsersApi, showUserApi, updateUserApi, deleteUserApi } from './users.api'

const err = e => e?.response?.data?.message || e?.message || 'Error'

export const useAdminUsersStore = defineStore('adminUsers', {
    state: () => ({
        byId: {},
        ids: [],
        listLoading: false,
        listError: null,
        pagination: { page: 1, perPage: 20, total: 0, lastPage: 1 },
        filters: { q: '', entity_type: '' },
        fetchingOne: false,
        fetchOneError: null,
        saving: false,
        deleting: false
    }),

    getters: {
        list(s) { return s.ids.map(id => s.byId[id]).filter(Boolean) },
        getById: s => id => s.byId[id] ?? null
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
                entity_type: clean(merged.entity_type),
                page: merged.page ?? 1,
                per_page: merged.perPage ?? 20
            }
            try {
                const res = await listUsersApi(query)
                const items = res.data || []
                const map = {}
                const ids = []
                for (const u of items) {
                    map[u.id] = u
                    ids.push(u.id)
                }
                this.byId = { ...this.byId, ...map }
                this.ids = ids
                const meta = res.meta || {}
                this.pagination.page = meta.current_page ?? (merged.page ?? 1)
                this.pagination.perPage = meta.per_page ?? (merged.perPage ?? 20)
                this.pagination.total = meta.total ?? ids.length
                this.pagination.lastPage = meta.last_page ?? 1
                this.filters = {
                    q: merged.q || '',
                    entity_type: merged.entity_type || ''
                }
            } catch (e) {
                this.listError = err(e)
                throw e
            } finally {
                this.listLoading = false
            }
        },

        async fetchOne(id, { force = false } = {}) {
            if (!id) return null
            if (this.byId[id] && !force) return this.byId[id]
            this.fetchingOne = true
            this.fetchOneError = null
            try {
                const res = await showUserApi(id)
                const u = res.data || res
                this.byId[u.id] = u
                if (!this.ids.includes(u.id)) this.ids.push(u.id)
                return u
            } catch (e) {
                this.fetchOneError = err(e)
                throw e
            } finally {
                this.fetchingOne = false
            }
        },

        async updateOne(id, payload) {
            this.saving = true
            try {
                const res = await updateUserApi(id, payload)
                const u = res.data || res
                this.byId[u.id] = u
                if (!this.ids.includes(u.id)) this.ids.unshift(u.id)
                return u
            } finally {
                this.saving = false
            }
        },

        async deleteOne(id) {
            this.deleting = true
            try {
                await deleteUserApi(id)
                delete this.byId[id]
                this.ids = this.ids.filter(x => x !== id)
                this.pagination.total = Math.max(0, this.pagination.total - 1)
            } finally {
                this.deleting = false
            }
        },

        setPage(p) {
            this.pagination.page = Math.max(1, Number(p) || 1)
        },

        setPerPage(n) {
            this.pagination.perPage = Math.max(1, Number(n) || 20)
            this.pagination.page = 1
        },

        setFilters(partial = {}) {
            this.filters = { ...this.filters, ...partial }
            this.pagination.page = 1
        },

        reset() {
            this.byId = {}
            this.ids = []
            this.listLoading = false
            this.listError = null
            this.pagination = { page: 1, perPage: 20, total: 0, lastPage: 1 }
            this.filters = { q: '', entity_type: '' }
            this.fetchingOne = false
            this.fetchOneError = null
            this.saving = false
            this.deleting = false
        }
    }
})
