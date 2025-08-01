<template>
    <div>
        <div
            v-if="!isExpanded && isTruncatable"
            class="px-4 text-sm leading-relaxed break-words whitespace-pre-line mt-2 space-y-3"
            :dir="dir"
            :class="dir === 'rtl' ? 'text-right' : 'text-left'"
            :style="{ fontFamily: dir === 'rtl' ? 'IRANSans' : 'inherit' }"
        >
            <span>{{ truncatedInline }}</span>
            <button
                v-if="!props.hideControls"
                @click="toggle"
                class="inline text-blue-600 dark:text-blue-400 hover:underline select-none ml-1">
                {{ $t('post.showMore') }}
            </button>
        </div>

        <div
            v-else
            class="px-4 text-sm leading-relaxed break-words whitespace-pre-line mt-2 space-y-3"
            :dir="dir"
            :class="dir === 'rtl' ? 'text-right' : 'text-left'"
            :style="{ fontFamily: dir === 'rtl' ? 'IRANSans' : 'inherit' }"
        >
            <template v-for="(block, idx) in props.richText" :key="idx">
                <p
                    v-if="block.type === 'paragraph'"
                    class="my-2"
                    :style="dir === 'rtl' ? rtlStyle : null"
                >
                    <template v-for="(child, i) in block.children" :key="i">
                        <template v-if="child.type === 'text'">{{ child.text }}</template>
                        <template v-else-if="child.type === 'link'">
                            <a
                                :href="child.href"
                                dir="ltr"
                                class="inline-block text-blue-600 hover:underline break-words"
                                target="_blank"
                                rel="noopener"
                            >
                                {{ child.text }}
                            </a>
                        </template>
                        <template v-else-if="child.type === 'emoji'">
                            <img :src="child.src" :alt="child.alt" class="inline w-4 h-4 align-middle mx-1" />
                        </template>
                    </template>
                </p>

                <p
                    v-else-if="block.type==='heading'"
                    class="my-2 font-bold [direction:rtl] [unicode-bidi:plaintext]"
                    :class="{
    'text-xl': block.level===1,
    'text-lg': block.level===2,
    'text-base': block.level>=3
  }"
                >

                <template v-for="(child, i) in block.children" :key="i">
                        <template v-if="child.type === 'text'">{{ child.text }}</template>
                        <template v-else-if="child.type === 'link'">
                            <a
                                :href="child.href"
                                dir="ltr"
                                class="inline-block text-blue-600 hover:underline break-words"
                                target="_blank"
                                rel="noopener"
                            >
                                {{ child.text }}
                            </a>
                        </template>
                        <template v-else-if="child.type === 'emoji'">
                            <img :src="child.src" :alt="child.alt" class="inline w-4 h-4 align-middle mx-1" />
                        </template>
                    </template>
                </p>

                <p v-else-if="block.type === 'line-break'" class="h-4"></p>
            </template>
        </div>

        <div class="flex justify-between items-center px-4 mt-3" :class="dir === 'rtl' ? 'flex-row-reverse' : ''">
            <button
                v-if="isExpanded && isTruncatable"
                @click="toggle"
                class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline transition"
            >
                {{ $t('post.showLess') }}
            </button>

            <button
                v-if="!props.hideControls && showReadFull"
                @click="openModal"
                class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1"
            >
                <Icon icon="mdi:book-open-variant" class="w-4 h-4" />
                <span>{{ $t('post.readFull') }}</span>
            </button>
        </div>

        <ReadFullTextModal :visible="showModal" :text="sanitizedFullHtml" @close="showModal = false" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import DOMPurify from 'dompurify'
import { Icon } from '@iconify/vue'
import ReadFullTextModal from '@/pages/home/components/modules/posts/ReadFullTextModal.vue'

const props = defineProps({
    richText: { type: Array, default: () => [] },
    fullHtml: { type: String, default: '' },
    truncate: { type: Boolean, default: true },
    limit: { type: Number, default: null },
    limits: { type: Object, default: () => ({ sm: 200, md: 300, lg: 400 }) },
    hideControls: { type: Boolean, default: false }
})

const rtlStyle = { direction: 'rtl', unicodeBidi: 'plaintext' }

const isExpanded = ref(false)
const toggle = () => (isExpanded.value = !isExpanded.value)

const responsiveLimit = ref(props.limits.lg)
const recomputeLimit = () => {
    const w = window.innerWidth
    const { sm, md, lg } = props.limits
    responsiveLimit.value = w < 640 ? sm : w < 1024 ? md : lg
}
onMounted(() => {
    recomputeLimit()
    window.addEventListener('resize', recomputeLimit, { passive: true })
})
onBeforeUnmount(() => window.removeEventListener('resize', recomputeLimit))

const currentLimit = computed(() => (props.limit != null ? props.limit : responsiveLimit.value))

const plainText = computed(() =>
    props.richText
        .filter(b => b.type === 'paragraph' || b.type === 'heading')
        .flatMap(b => b.children)
        .filter(c => c.type === 'text')
        .map(c => c.text)
        .join(' ')
)

const isTruncatable = computed(() => plainText.value.length > currentLimit.value)
const truncatedInline = computed(() => plainText.value.slice(0, currentLimit.value).trimEnd() + '…')

const showModal = ref(false)
const openModal = () => (showModal.value = true)
const showReadFull = computed(() => plainText.value.length > 800)

const sanitizedFullHtml = computed(() =>
    DOMPurify.sanitize(props.fullHtml || '', {
        ALLOWED_TAGS: [
            'p', 'br', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'strong', 'em',
            'ul', 'ol', 'li', 'img', 'span', 'div'
        ],
        ALLOWED_ATTR: ['href', 'src', 'alt', 'target', 'rel']
    })
)

const hasPersian = t => /[\\u0600-\\u06FF]/.test(t)
const dir = computed(() => (hasPersian(plainText.value) ? 'rtl' : 'ltr'))
</script>
