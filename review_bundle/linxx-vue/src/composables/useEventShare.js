import { ref } from 'vue'
import { useShareStore } from '@/stores/share'

export function useEventShare() {
    const shareStore = useShareStore()

    const showShare = ref(false)
    const position = ref({ top: 0, left: 0 })

    const currentEvent = ref(null)
    const cache = ref({}) // { [eventId]: { link, slug } }
    const creating = ref({}) // { [eventId]: boolean }

    // for in-app
    const showEmbedModal = ref(false)
    const embedTarget = ref(null) // full event object
    const currentSlug = ref('')

    function openShare(ev, mouseEvent) {
        if (!ev?.id) return
        currentEvent.value = ev
        const t = mouseEvent?.currentTarget
        if (t?.getBoundingClientRect) {
            const r = t.getBoundingClientRect()
            position.value = { top: r.top + window.scrollY, left: r.left + r.width / 2 + window.scrollX }
        } else {
            position.value = { top: window.innerHeight / 2, left: window.innerWidth / 2 }
        }
        showShare.value = true
    }

    async function ensureLink(id) {
        const key = String(id)
        if (cache.value[key]?.link || creating.value[key]) return cache.value[key]
        creating.value[key] = true
        try {
            const share = await shareStore.create({ shareable_type: 'event', shareable_id: id, scope: 'public' })
            const link = share.link || `${location.origin}/r/${share.slug}`
            cache.value[key] = { link, slug: share.slug }
            return cache.value[key]
        } finally {
            creating.value[key] = false
        }
    }

    async function onSelect({ key }) {
        showShare.value = false
        if (!currentEvent.value?.id) return
        const { link, slug } = await ensureLink(currentEvent.value.id)

        if (key === 'in-app') {
            currentSlug.value = slug
            embedTarget.value = currentEvent.value
            showEmbedModal.value = true
            return
        }

        const u = encodeURIComponent(link)
        if (key === 'copy') {
            if (navigator.clipboard?.writeText) await navigator.clipboard.writeText(link)
            else prompt('Copy this link:', link)
            return
        }
        if (key === 'whatsapp') window.open(`https://wa.me/?text=${u}`, '_blank', 'noopener')
        else if (key === 'telegram') window.open(`https://t.me/share/url?url=${u}`, '_blank', 'noopener')
        else if (key === 'x') window.open(`https://twitter.com/intent/tweet?url=${u}`, '_blank', 'noopener')
        else if (key === 'facebook') window.open(`https://www.facebook.com/sharer/sharer.php?u=${u}`, '_blank', 'noopener')
    }

    return {
        // popover
        showShare,
position,
openShare,
onSelect,
        // in-app
        showEmbedModal,
embedTarget,
currentSlug,
    }
}
