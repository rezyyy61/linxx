<template>
  <div class="max-w-4xl mx-auto p-8 bg-white rounded-3xl shadow-lg space-y-8">
    <!-- 🔙 Header -->
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-800">
        {{ book ? '✏️ Edit Book' : '📚 Add New Book' }}
      </h1>
      <button @click="emit('cancel')" class="flex items-center text-sm text-gray-600 hover:text-blue-600 transition">
        <Icon icon="mdi:arrow-left" class="w-5 h-5 mr-1" />
        Back to List
      </button>
    </div>

    <!-- 📝 Form -->
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Title & Author -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="label">Title <span class="text-red-500">*</span></label>
          <input v-model="form.title" type="text" class="input" required />
        </div>
        <div>
          <label class="label">Author</label>
          <input v-model="form.author" type="text" class="input" />
        </div>
      </div>

      <!-- Description -->
      <div>
        <label class="label">Description</label>
        <textarea v-model="form.description" rows="4" class="input resize-none"></textarea>
      </div>

      <!-- Cover & File Upload -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Cover Image -->
        <div>
          <label class="label">Cover Image</label>
          <input type="file" @change="handleCoverChange" accept="image/*" class="file-input" />

          <div v-if="coverPreviewUrl" class="mt-2 relative w-32">
            <img :src="coverPreviewUrl" class="rounded shadow" />
            <button
                type="button"
                @click="removeCover"
                class="absolute top-0 right-0 text-white bg-red-500 rounded-full p-1 -mt-2 -mr-2 hover:bg-red-600"
            >
              ✕
            </button>
          </div>
        </div>

        <!-- Book File -->
        <div>
          <label class="label">Book File (PDF/EPUB) <span class="text-red-500">*</span></label>
          <input type="file" @change="handleFileChange" accept=".pdf,.epub" class="file-input" :required="!book && !existingFile" />

          <div v-if="bookFile || existingFile" class="mt-2 text-sm text-gray-700 flex items-center space-x-2">
            <span>{{ bookFile ? bookFile.name : 'Current file uploaded' }}</span>
            <a v-if="existingFile && !bookFile" :href="existingFile" target="_blank" class="text-blue-600 underline">Download</a>
            <button type="button" @click="removeFile" class="text-red-500 hover:underline">Remove</button>
          </div>
        </div>
      </div>

      <!-- Is Free & Category -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="label">Is Free?</label>
          <select v-model="form.is_free" class="input">
            <option :value="true">Yes</option>
            <option :value="false">No</option>
          </select>
        </div>
        <div>
          <label class="label">Category</label>
          <select v-model="form.category_id" class="input">
            <option disabled value="">Select Category</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="pt-4 flex justify-end">
        <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-xl transition disabled:opacity-50"
            :disabled="loading"
        >
          <span v-if="loading">Saving...</span>
          <span v-else>
               {{ isSuggestion ? 'Approve & Publish'
              : props.book ? 'Update Book' : 'Save Book' }}
          </span>
        </button>
      </div>

      <!-- Error -->
      <div v-if="error" class="text-red-600 text-sm mt-2 whitespace-pre-line">{{ error }}</div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Icon } from '@iconify/vue'
import { useBookStore } from '@/stores/admin/useBookStore'
import { useSuggestedBookStore } from '@/stores/admin/useSuggestedBookStore'

const props = defineProps({ book: Object })
const emit = defineEmits(['cancel'])

const bookStore = useBookStore()
const suggestedStore = useSuggestedBookStore()

const isSuggestion = computed(() => props.book?.status === 'pending')

const categories = ref([])
const coverFile = ref(null)
const bookFile = ref(null)
const existingCover = ref(null)
const existingFile = ref(null)
const loading = ref(false)
const error = ref(null)

const form = ref({
  title: '',
  author: '',
  description: '',
  is_free: true,
  category_id: ''
})

onMounted(async () => {
  try {
    categories.value = await bookStore.fetchCategories()

    if (props.book) {
      form.value = {
        title: props.book.title || '',
        author: props.book.author || '',
        description: props.book.description || '',
        is_free: props.book.is_free ?? true,
        category_id: props.book.category_id || ''
      }

      existingCover.value = props.book.cover_url || null
      existingFile.value = props.book.file_url || null
    }
  } catch {
    error.value = bookStore.error || 'Failed to load categories'
  }
})

const coverPreviewUrl = computed(() =>
    coverFile.value ? URL.createObjectURL(coverFile.value) : existingCover.value
)

function handleCoverChange(e) { coverFile.value = e.target.files[0] }
function handleFileChange(e) { bookFile.value = e.target.files[0] }
function removeCover() { coverFile.value = null; existingCover.value = null }
function removeFile() { bookFile.value = null; existingFile.value = null }

async function handleSubmit() {
  loading.value = true
  error.value = null

  const data = new FormData()
  for (const k in form.value) {
    const v = form.value[k]
    if (k === 'is_free') data.append(k, v ? 1 : 0)
    else if (v !== '' && v !== null) data.append(k, v)
  }
  if (coverFile.value) data.append('cover_image', coverFile.value)
  if (bookFile.value) data.append('file_path', bookFile.value)

  try {
    if (isSuggestion.value) {
      await suggestedStore.approveSuggestedBook(props.book.id, data)
    } else if (props.book) {
      await bookStore.updateBook(props.book.id, data)
    } else {
      await bookStore.addBook(data)
    }
    emit('cancel')
  } catch (err) {
    const r = err.response
    error.value =
        r?.status === 422 && r.data.errors
            ? Object.values(r.data.errors).flat().join('\n')
            : bookStore.error || suggestedStore.error || 'Something went wrong.'
  } finally {
    loading.value = false
  }
}
</script>


<style scoped>
.input {
  @apply w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400;
}
.label {
  @apply block text-sm font-medium mb-1 text-gray-700;
}
.file-input {
  @apply block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
  file:rounded-lg file:border-0 file:text-sm file:font-semibold
  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100;
}
</style>
