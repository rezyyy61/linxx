import { defineStore } from 'pinia'
import axios from '@/lib/publicAxios'
import axiosAuth from '@/lib/axios'

export const usePublicBookStore = defineStore('publicBookStore', {
    state: () => ({
        books: [],
        currentPage: 1,
        lastPage: null,
        loading: false,
        error: null,

        // search & filters
        searchTerm: '',
        filters: {
            category: 'all',
            pricing: 'all',
            sortBy: 'newest'
        },

        // single book
        singleBook: null,
        bookLoading: false,
        bookError: null,

        // reviews
        reviews: [],
        reviewsLoading: false,
        reviewsError: null,
        reviewSubmitting: false,
        reviewError: null
    }),

    actions: {
        // ---- FETCH BOOKS ----
        async fetchBooks(page = 1) {
            if (this.loading || (this.lastPage && page > this.lastPage)) return

            this.loading = true
            this.error = null

            try {
                const params = {
                    page
                }

                if (this.searchTerm) params.q = this.searchTerm
                if (this.filters.category !== 'all') params.category = this.filters.category
                if (this.filters.pricing !== 'all') params.pricing = this.filters.pricing
                if (this.filters.sortBy && this.filters.sortBy !== 'newest') params.sort_by = this.filters.sortBy

                const res = await axios.get(`/api/books/search`, { params })
                const { data, meta } = res.data

                this.books = page === 1 ? data : [...this.books, ...data]
                this.currentPage = meta.current_page
                this.lastPage = meta.last_page
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to load books'
            } finally {
                this.loading = false
            }
        },

        async loadMore() {
            if (this.currentPage < this.lastPage) {
                await this.fetchBooks(this.currentPage + 1)
            }
        },

        setSearch(term) {
            this.searchTerm = term
        },

        setFilters({ category, pricing, sortBy }) {
            this.filters.category = category || 'all'
            this.filters.pricing = pricing || 'all'
            this.filters.sortBy = sortBy || 'newest'
        },

        resetAndFetch() {
            this.books = []
            this.currentPage = 1
            this.lastPage = null
            this.fetchBooks()
        },

        // ---- SINGLE BOOK ----
        async fetchBook(slug) {
            this.singleBook = null
            this.bookError = null
            this.bookLoading = true

            try {
                const res = await axios.get(`/api/books/${slug}`)
                this.singleBook = res.data
            } catch (err) {
                this.bookError = err.response?.data?.message || 'The book not found'
            } finally {
                this.bookLoading = false
            }
        },

        // ---- REVIEWS ----
        async fetchReviews(bookId) {
            this.reviews = []
            this.reviewsError = null
            this.reviewsLoading = true

            try {
                const res = await axios.get(`/api/books/${bookId}/reviews`)
                this.reviews = res.data?.data || []
            } catch (err) {
                this.reviewsError = err.response?.data?.message || 'Failed to load reviews'
            } finally {
                this.reviewsLoading = false
            }
        },

        async submitReview(bookId, payload) {
            this.reviewError = null
            this.reviewSubmitting = true

            try {
                await axiosAuth.post(`/api/books/${bookId}/reviews`, payload)
                await this.fetchReviews(bookId)
            } catch (err) {
                this.reviewError = err.response?.data?.message || 'Failed to submit review'
            } finally {
                this.reviewSubmitting = false
            }
        },

        // ---- CATEGORIES ----
        async fetchCategories() {
            try {
                const res = await axios.get('/api/categories')
                return res.data?.data || []
            } catch (err) {
                return []
            }
        }
    }
})
