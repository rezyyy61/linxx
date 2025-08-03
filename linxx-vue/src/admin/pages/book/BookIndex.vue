<template>
  <div class="p-6 max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
      <h1 class="text-3xl font-bold text-gray-800">📚 Book Management</h1>

      <div class="flex flex-wrap items-center gap-3">
        <Transition name="fade" mode="out-in">
          <button
              v-if="suggestedStore.suggestedCount > 0"
              @click="viewMode = 'suggestions'"
              class="flex items-center gap-2 bg-yellow-100 text-yellow-800 border border-yellow-400 px-4 py-2 rounded-lg hover:bg-yellow-200 transition"
          >
            <span class="text-xl">📬</span>
            <span class="font-medium">
              {{ suggestedStore.suggestedCount }}
              Suggested Book{{ suggestedStore.suggestedCount > 1 ? 's' : '' }}
            </span>
          </button>
        </Transition>

        <button
            @click="startCreate"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
        >
          + Add New Book
        </button>
      </div>
    </div>

    <BookCreate
        v-if="viewMode === 'create'"
        :book="editingBook"
        @cancel="resetForm"
    />

    <SuggestedBooksPage
        v-else-if="viewMode === 'suggestions'"
        @back="viewMode = 'list'"
        @edit="handleEditSuggested"
    />

    <BookList
        v-else
        :books="books"
        :loading="loading"
        :error="error"
        @edit="startEdit"
        @delete="handleDelete"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import BookCreate from '@/admin/pages/book/BookCreate.vue'
import BookList from '@/admin/pages/book/BookList.vue'
import SuggestedBooksPage from '@/admin/pages/book/SuggestedBooksPage.vue'
import { useBookStore } from '@/stores/admin/useBookStore'
import { useSuggestedBookStore } from '@/stores/admin/useSuggestedBookStore'

const bookStore = useBookStore()
const suggestedStore = useSuggestedBookStore()

const viewMode = ref('list')
const editingBook = ref(null)
const books = ref([])
const loading = ref(false)
const error = ref(null)

onMounted(async () => {
  await suggestedStore.fetchSuggestedBooks()
  await fetchBooks()
})

async function fetchBooks() {
  loading.value = true
  error.value = null
  try {
    books.value = await bookStore.fetchBooks()
  } catch (e) {
    error.value = bookStore.error || 'Failed to fetch books'
  } finally {
    loading.value = false
  }
}

function startCreate() {
  viewMode.value = 'create'
  editingBook.value = null
}

function startEdit(book) {
  viewMode.value = 'create'
  editingBook.value = book
}

function handleEditSuggested(suggestion) {
  editingBook.value = suggestion
  viewMode.value = 'create'
}

function resetForm() {
  viewMode.value = 'list'
  editingBook.value = null
  fetchBooks()
  suggestedStore.fetchSuggestedBooks()
}

async function handleDelete(id) {
  if (!confirm('Are you sure you want to delete this book?')) return
  try {
    await bookStore.deleteBook(id)
    books.value = books.value.filter(b => b.id !== id)
    suggestedStore.fetchSuggestedBooks()
  } catch {
    console.error('Failed to delete book')
  }
}
</script>
