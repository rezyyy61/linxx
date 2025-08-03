<template>
  <div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
          v-for="book in books"
          :key="book.id"
          class="bg-white border rounded-xl shadow p-4 flex flex-col justify-between"
      >
        <div>
          <img
              :src="book.cover_image_url || 'https://via.placeholder.com/300x400?text=No+Cover'"
              alt="Cover"
              class="w-full h-48 object-cover rounded-lg mb-3"
          />
          <h2 class="text-lg font-semibold mb-1">{{ book.title }}</h2>
          <p class="text-sm text-gray-500 mb-2">{{ book.author }}</p>
        </div>

        <div class="flex justify-end space-x-2 mt-4">
          <button
              @click="$emit('edit', book)"
              class="text-sm bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded"
          >
            Edit
          </button>
          <button
              @click="$emit('delete', book.id)"
              class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <div v-if="!loading && books.length === 0" class="text-center text-gray-500 mt-12">
      No books found.
    </div>
    <div v-if="loading" class="text-center text-blue-600 mt-6">Loading...</div>
    <div v-if="error" class="text-center text-red-600 mt-6 whitespace-pre-line">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
defineProps({
  books: {
    type: Array,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: null,
  },
})
</script>
