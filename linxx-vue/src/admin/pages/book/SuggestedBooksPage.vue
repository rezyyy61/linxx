<template>
  <div class="p-6 max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-800">📬 Suggested Books</h1>
      <button @click="emit('back')" class="text-sm text-blue-600 hover:underline">
        ← Back to Books
      </button>
    </div>

    <div v-if="suggestedStore.suggestedCount" class="grid gap-6">
      <div
          v-for="book in suggestedStore.suggestedBooks"
          :key="book.id"
          class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 flex flex-col sm:flex-row gap-6"
      >
        <div class="w-full sm:w-40 shrink-0">
          <img
              v-if="book.cover_url"
              :src="book.cover_url"
              alt="Cover"
              class="rounded-md w-full object-cover aspect-[3/4]"
          />
          <div
              v-else
              class="w-full aspect-[3/4] bg-gray-100 text-gray-400 flex items-center justify-center text-sm rounded-md"
          >
            No Cover
          </div>
        </div>

        <div class="flex-1">
          <div class="flex justify-between items-start mb-2">
            <div>
              <h2 class="text-lg font-semibold text-gray-800">{{ book.title }}</h2>
              <p v-if="book.author" class="text-sm text-gray-600">Author: {{ book.author }}</p>
              <p v-if="book.translator" class="text-sm text-gray-600">Translator: {{ book.translator }}</p>
            </div>
            <div class="text-sm text-gray-500 text-right">
              <span v-if="book.submitter?.name">
                Suggested by: <strong>{{ book.submitter.name }}</strong>
                <span class="text-xs p-2 text-gray-400">({{ book.submitter.email }})</span>
              </span>
              <span v-else class="italic text-gray-400">Unknown user</span>
            </div>
          </div>

          <p v-if="book.description" class="text-sm text-gray-700 whitespace-pre-wrap mb-3">
            {{ book.description }}
          </p>

          <a
              v-if="book.file_url"
              :href="book.file_url"
              target="_blank"
              class="text-sm text-blue-600 underline"
          >
            📄 View PDF
          </a>

          <div class="mt-4 flex gap-2">
            <button
                @click="handleEdit(book)"
                class="bg-yellow-500 text-white text-xs px-3 py-1 rounded hover:bg-yellow-600"
            >
              ✏️ Edit
            </button>
            <button
                @click="handleDelete(book.id)"
                class="bg-red-500 text-white text-xs px-3 py-1 rounded hover:bg-red-600"
            >
              🗑️ Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-gray-500">No suggested books found.</div>
  </div>
</template>

<script setup>
import { useSuggestedBookStore } from '@/stores/admin/useSuggestedBookStore'

const suggestedStore = useSuggestedBookStore()
const emit = defineEmits(['back', 'edit'])

function handleEdit(book) {
  emit('edit', book)
}

async function handleDelete(id) {
  if (!confirm('Delete this suggestion?')) return
  try {
    await suggestedStore.deleteSuggestedBook(id)
  } catch (e) {
    alert(suggestedStore.error || 'Delete failed')
  }
}
</script>
