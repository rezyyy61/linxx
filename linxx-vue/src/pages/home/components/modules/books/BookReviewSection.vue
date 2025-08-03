<template>
  <div class="mt-10">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">
      {{ t('home.books.reviews_section') }}
    </h2>

    <!-- Loading -->
    <div v-if="reviewsLoading" class="text-sm text-gray-400 mb-4 animate-pulse">
      {{ t('home.books.loading_reviews') }}
    </div>

    <!-- Review List -->
    <div v-else-if="reviews.length" class="space-y-4 mb-6">
      <div
          v-for="(review, index) in reviews"
          :key="review.id || index"
          class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800"
      >
        <div class="flex items-center gap-2 mb-1 text-amber-500">
          <template v-for="n in 5" :key="n">
            <Icon
                :icon="n <= review.rating ? 'mdi:star' : 'mdi:star-outline'"
                class="w-4 h-4"
            />
          </template>
          <span class="text-xs text-gray-500 dark:text-gray-400 ml-auto">
            {{ review.user?.name || t('home.books.anonymous') }}
          </span>
        </div>
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
          {{ review.text }}
        </p>
      </div>
    </div>

    <div v-else class="text-sm text-gray-400 italic mb-6">
      {{ t('home.books.no_reviews_yet') }}
    </div>

    <!-- Submit error -->
    <div v-if="reviewError" class="text-red-500 text-sm mb-3">
      {{ reviewError }}
    </div>

    <div v-if="!isLoggedIn" class="mt-6 bg-rose-50 dark:bg-rose-900 border border-rose-200 dark:border-rose-700 p-5 rounded-xl text-center">
      <p class="text-sm text-gray-800 dark:text-gray-200 mb-3">
        {{ t('home.books.must_login_to_review') }}
      </p>
      <button
          @click="goToLogin"
          class="inline-flex items-center gap-1 px-4 py-2 text-sm rounded-full bg-rose-500 hover:bg-rose-600 text-white transition"
      >
        <Icon icon="mdi:login" class="w-4 h-4" />
        {{ t('home.books.login_now') }}
      </button>
    </div>
    <!-- Add New Review -->
    <form
        v-else
        @submit.prevent="submitReview"
        class="mt-6 space-y-3"
    >
      <div>
        <label class="text-sm font-medium text-gray-600 dark:text-gray-300 block mb-1">
          {{ t('home.books.your_rating') }}
        </label>
        <div class="flex gap-1 text-amber-500">
          <template v-for="n in 5" :key="n">
            <Icon
                :icon="n <= newRating ? 'mdi:star' : 'mdi:star-outline'"
                class="w-6 h-6 cursor-pointer hover:scale-110 transition"
                @click="newRating = n"
            />
          </template>
        </div>
      </div>

      <div>
        <label class="text-sm font-medium text-gray-600 dark:text-gray-300 block mb-1">
          {{ t('home.books.your_review') }}
        </label>
        <textarea
            v-model="newReview"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm p-2 resize-none focus:(outline-none ring-2 ring-rose-500)"
            rows="4"
            :placeholder="t('home.books.review_placeholder')"
        ></textarea>
      </div>

      <button
          type="submit"
          class="px-4 py-2 rounded-full bg-rose-500 text-white hover:bg-rose-600 transition text-sm font-medium"
          :disabled="reviewSubmitting"
      >
        {{ reviewSubmitting ? '...' : t('home.books.submit_review') }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'
import { usePublicBookStore } from '@/stores/usePublicBookStore'
import {useAuthStore} from "@/stores/auth";

const { t } = useI18n()
const router = useRouter()

const props = defineProps({
  bookId: {
    type: [Number, String],
    required: true
  }
})

const store = usePublicBookStore()
const auth = useAuthStore()

const newRating = ref(0)
const newReview = ref('')

const reviews = computed(() => store.reviews)
const reviewsLoading = computed(() => store.reviewsLoading)
const reviewSubmitting = computed(() => store.reviewSubmitting)
const reviewError = computed(() => store.reviewError)
const isLoggedIn = computed(() => !!auth.user)

const submitReview = async () => {
  if (!newRating.value || !newReview.value.trim()) return

  await store.submitReview(props.bookId, {
    rating: newRating.value,
    review: newReview.value.trim()
  })

  if (!store.reviewError) {
    newRating.value = 0
    newReview.value = ''
  }
}

const goToLogin = () => {
  router.push({ name: 'login' })
}

onMounted(() => {
  store.fetchReviews(props.bookId)
})
</script>

