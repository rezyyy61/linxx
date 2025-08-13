<template>
  <article
      class="group relative flex items-stretch gap-3 sm:gap-4 rounded-2xl border border-zinc-200 bg-white p-3 sm:p-4 pl-6 shadow-sm transition hover:shadow-md hover:-translate-y-0.5 dark:border-white/10 dark:bg-[#0b0f1a]"
      role="button"
      tabindex="0"
      @click="open"
      @keyup.enter="open"
  >
    <div class="absolute left-0 top-0 bottom-0 w-2 rounded-l-2xl bg-gradient-to-b from-rose-500 to-rose-600"></div>
    <span class="absolute right-4 -top-2 h-0 w-0 border-l-4 border-r-4 border-b-6 border-l-transparent border-r-transparent border-b-rose-500"></span>

    <div class="relative shrink-0 w-16 h-24 sm:w-20 sm:h-28 rounded-xl overflow-hidden bg-zinc-50 ring-1 ring-inset ring-black/5 dark:bg-white/5 dark:ring-white/10">
      <div v-if="!imgLoaded && !imgError" class="absolute inset-0 animate-pulse bg-zinc-200/60 dark:bg-white/10"></div>
      <img
          :src="cover"
          :alt="book.title"
          class="h-full w-full object-contain p-1.5 transition duration-500"
          :class="imgLoaded ? 'opacity-100' : 'opacity-80 blur-[6px]'"
          loading="lazy"
          decoding="async"
          @load="imgLoaded = true"
          @error="imgError = true"
      />
    </div>

    <div class="min-w-0 flex-1">
      <div class="flex items-start justify-between gap-2">
        <div class="flex items-center gap-2 min-w-0">
          <Icon icon="mdi:book-open-page-variant" class="h-4 w-4 text-rose-600 dark:text-rose-400" />
          <h4 class="truncate text-[15px] sm:text-base font-semibold text-zinc-900 dark:text-white" :dir="dir(book.title)">
            {{ book.title }}
          </h4>
        </div>
        <div class="flex items-center gap-1.5">
          <span v-if="book.is_free" class="rounded-full bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset ring-emerald-500/20">FREE</span>
          <span class="rounded-full bg-rose-50 text-rose-700 px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset ring-rose-200 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20">BOOK</span>
        </div>
      </div>

      <p class="mt-0.5 truncate text-xs sm:text-[13px] text-zinc-600 dark:text-zinc-400" :dir="dir(book.author)">
        {{ book.author || '—' }}
      </p>

      <p v-if="book.description" class="mt-1.5 line-clamp-2 text-xs sm:text-[13px] leading-relaxed text-zinc-700 dark:text-zinc-300" :dir="dir(book.description)">
        {{ book.description }}
      </p>

      <div class="mt-2 flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400">
        <div class="flex items-center gap-3">
          <span>👁️ {{ book.view_count }}</span>
          <span>⬇️ {{ book.download_count }}</span>
        </div>
        <div class="flex items-center gap-1">
          <Icon :icon="(book.rating || 0) >= 1 ? 'mdi:star' : 'mdi:star-outline'" class="h-4 w-4 text-amber-500" />
          <span class="font-semibold text-rose-500">{{ (book.rating || 0).toFixed(1) }}</span>
          <span class="opacity-70">({{ book.reviews_count || 0 }})</span>
        </div>
      </div>
    </div>

    <span class="pointer-events-none absolute right-3 bottom-3 text-[11px] text-zinc-400 dark:text-zinc-500 transition group-hover:translate-x-0.5">Open →</span>
  </article>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { ref, computed } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({ book: { type: Object, required: true } })
const router = useRouter()

const fallback = 'https://via.placeholder.com/300x400?text=No+Cover'
const cover = computed(() => props.book.cover_image_url || fallback)

const imgLoaded = ref(false)
const imgError = ref(false)

const dir = (t) => /[\u0591-\u07FF\uFB1D-\uFDFD\uFE70-\uFEFC]/.test((t || '').trim()) ? 'rtl' : 'ltr'

function open() {
  const key = props.book.slug || props.book.id
  if (router.hasRoute('book.show')) router.push({ name: 'book.show', params: { slug: key } })
  else router.push(`/books/${key}`)
}
</script>
