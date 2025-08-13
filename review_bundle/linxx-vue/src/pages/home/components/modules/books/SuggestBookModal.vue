<template>
  <div
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
      @click.self="$emit('close')"
  >
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-3xl w-full p-6">
      <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
        {{ t('home.books.suggest_title') }}
      </h2>

      <!-- اگر لاگین نبود -->
      <div v-if="!isLoggedIn" class="text-center text-gray-800 dark:text-white py-8">
        <p class="mb-4">{{ t('home.books.must_login') || 'برای پیشنهاد کتاب، ابتدا وارد حساب خود شوید.' }}</p>
        <router-link to="/login" class="btn-primary">
          {{ t('Login')  }}
        </router-link>
      </div>

      <!-- فرم فقط اگر لاگین بود -->
      <form v-else @submit.prevent="handleSubmit" enctype="multipart/form-data">
        <div class="space-y-4">
          <div>
            <label class="form-label">{{ t('home.books.title_label') }}</label>
            <input v-model="form.title" required type="text" class="form-input" />
          </div>

          <div>
            <label class="form-label">{{ t('home.books.author_label') }}</label>
            <input v-model="form.author" type="text" class="form-input" />
          </div>

          <div>
            <label class="form-label">{{ t('home.books.translator_label') }}</label>
            <input v-model="form.translator" type="text" class="form-input" />
          </div>

          <div>
            <label class="form-label">{{ t('home.books.description_label') }}</label>
            <textarea v-model="form.description" rows="4" class="form-input resize-none"></textarea>
          </div>

          <div>
            <label class="form-label">
              {{ t('home.books.link_label') }}
              <span class="text-xs text-gray-400">({{ t('home.books.optional') }})</span>
            </label>
            <input v-model="form.link" type="url" class="form-input" placeholder="https://..." />
          </div>

          <div>
            <label class="form-label">{{ t('home.books.cover_upload') }}</label>
            <input type="file" accept="image/*" @change="handleCoverUpload" class="form-input file:mr-2" />
            <div v-if="previewCover" class="mt-2 relative inline-block">
              <img :src="previewCover" alt="preview" class="h-32 rounded-lg object-cover border" />
              <button type="button" @click="removeCover" class="absolute top-0 right-0 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-bl">✕</button>
            </div>
          </div>

          <div>
            <label class="form-label">{{ t('home.books.file_upload') }}</label>
            <input type="file" accept=".pdf" @change="handleFileUpload" class="form-input file:mr-2" />
            <div v-if="form.file" class="text-sm mt-2 flex items-center gap-2">
              <span class="truncate max-w-xs">{{ form.file.name }}</span>
              <button type="button" @click="removeFile" class="text-red-600 hover:underline text-xs">✕ {{ t('home.books.remove_file') }}</button>
            </div>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <button type="button" @click="$emit('close')" class="btn-secondary">
            {{ t('home.books.cancel') }}
          </button>
          <button type="submit" class="btn-primary">
            {{ t('home.books.submit') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'

const { t } = useI18n()
const authStore = useAuthStore()
const isLoggedIn = computed(() => !!authStore.user)

const emit = defineEmits(['close', 'submitted'])

const form = ref({
  title: '',
  author: '',
  translator: '',
  description: '',
  link: '',
  cover: null,
  file: null,
})

const previewCover = ref(null)

function handleCoverUpload(e) {
  const file = e.target.files[0]
  form.value.cover = file
  if (file) previewCover.value = URL.createObjectURL(file)
}

function removeCover() {
  form.value.cover = null
  previewCover.value = null
}

function handleFileUpload(e) {
  form.value.file = e.target.files[0]
}

function removeFile() {
  form.value.file = null
}

function handleSubmit() {
  const formData = new FormData()
  formData.append('title', form.value.title)
  formData.append('author', form.value.author || '')
  formData.append('translator', form.value.translator || '')
  formData.append('description', form.value.description || '')
  formData.append('link', form.value.link || '')

  if (form.value.cover) formData.append('cover_path', form.value.cover)
  if (form.value.file) formData.append('file_path', form.value.file)

  emit('submitted', formData)
  emit('close')
}

</script>

<style scoped>
.form-label {
  @apply block text-sm font-medium text-gray-700 dark:text-gray-300;
}
.form-input {
  @apply w-full mt-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500;
}
.btn-primary {
  @apply px-4 py-2 rounded-lg bg-rose-500 hover:bg-rose-600 text-white font-medium transition;
}
.btn-secondary {
  @apply px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500 transition;
}
</style>
