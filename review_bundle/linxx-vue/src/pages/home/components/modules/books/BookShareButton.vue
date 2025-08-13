<!-- src/pages/home/components/modules/books/BookShareButton.vue -->
<template>
  <div ref="rootRef" class="inline-flex">
    <button
        type="button"
        class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
        @click.stop.prevent="openShare"
    >
      <Icon icon="mdi:share-variant" class="w-4 h-4" />
      <span>Share</span>
    </button>

    <ShareModal
        v-model:modelValue="showShare"
        :position="sharePos"
        :teleport-to-body="true"
    @select="onShareSelect"
    />

  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Icon } from '@iconify/vue'
import ShareModal from '@/pages/home/components/modules/posts/Footer/share/ShareModal.vue'
import { useShareStore } from '@/stores/share'

const props = defineProps({ book: { type: Object, required: true } })
const emit = defineEmits(['in-app'])
const shareStore = useShareStore()

const rootRef = ref(null)
const showShare = ref(false)
const sharePos = ref({ top: 0, left: 0 })
const creating = ref(false)
const cache = ref({ id: null, slug: '', link: '' })

function calcPos() {
  const el = rootRef.value
  if (!el) return
  const r = el.getBoundingClientRect()
  sharePos.value = { top: r.bottom + 8, left: r.left + r.width / 2 }
}

function openShare() {
  calcPos()
  showShare.value = true
}

function onScrollOrResize() {
  if (showShare.value) calcPos()
}
onMounted(() => {
  window.addEventListener('scroll', onScrollOrResize, { passive: true })
  window.addEventListener('resize', onScrollOrResize, { passive: true })
})
onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScrollOrResize)
  window.removeEventListener('resize', onScrollOrResize)
})

async function ensureLink() {
  const id = props.book?.id
  if (!id) throw new Error('No book id')
  if (cache.value.id === id && cache.value.link) return cache.value
  if (creating.value) return cache.value
  creating.value = true
  try {
    const share = await shareStore.create({
      shareable_type: 'book',
      shareable_id: id,
      scope: 'public'
    })
    const link = share.link || `${location.origin}/r/${share.slug}`
    cache.value = { id, slug: share.slug, link }
    return cache.value
  } finally {
    creating.value = false
  }
}

async function onShareSelect({ key }) {
  showShare.value = false
  const { link, slug } = await ensureLink()
  if (key === 'in-app') {
    emit('in-app', { slug, link, book: props.book })
    return
  }
  if (key === 'copy') {
    if (navigator.clipboard?.writeText) await navigator.clipboard.writeText(link)
    else window.prompt('Copy link', link)
    return
  }
  if (key === 'whatsapp') window.open(`https://wa.me/?text=${encodeURIComponent(link)}`, '_blank', 'noopener')
  if (key === 'telegram') window.open(`https://t.me/share/url?url=${encodeURIComponent(link)}`, '_blank', 'noopener')
  if (key === 'x') window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(link)}`, '_blank', 'noopener')
  if (key === 'facebook') window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(link)}`, '_blank', 'noopener')
}
</script>
