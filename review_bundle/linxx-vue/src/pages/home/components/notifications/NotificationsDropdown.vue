<template>
  <div v-if="open" class="absolute right-0 top-12 z-50 w-96 max-w-[90vw]">
    <div class="rounded-2xl border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
      <header class="flex items-center justify-between px-3 py-2">
        <div class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Notifications</div>
        <button
            class="text-xs text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
            @click="$emit('close')"
        >×</button>
      </header>

      <div class="h-px bg-zinc-100 dark:bg-zinc-800" />

      <div v-if="loading" class="p-3 space-y-2">
        <div class="h-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 animate-pulse" />
        <div class="h-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 animate-pulse" />
        <div class="h-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 animate-pulse" />
      </div>

      <ul v-else class="max-h-96 overflow-auto divide-y divide-zinc-100 dark:divide-zinc-800">
        <li v-for="n in items" :key="n.id">
          <button class="w-full text-left px-3 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-800" @click="$emit('open-item', n)">
            <div class="flex items-start gap-2">
              <div class="h-8 w-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs">
                {{ icon(n.type) }}
              </div>
              <div class="min-w-0 flex-1">
                <div class="text-sm text-zinc-800 dark:text-zinc-100 truncate">
                  {{ n.title || label(n) }}
                </div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                  {{ n.body }}
                </div>
              </div>
              <div class="mt-1">
                <span v-if="!n.read_at" class="inline-block h-2 w-2 rounded-full bg-rose-500" />
              </div>
            </div>
          </button>
        </li>
        <li v-if="!items.length" class="px-3 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
          {{ $t?.('notifications.empty','No notifications yet') }}
        </li>
      </ul>

      <footer class="px-3 py-2 flex items-center justify-between text-xs">
        <button class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200" @click="$emit('mark-seen')">
          {{ $t?.('notifications.markSeen','Mark all as seen') }}
        </button>
        <button class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200" @click="$emit('view-all')">
          {{ $t?.('notifications.viewAll','View all') }}
        </button>
      </footer>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  open: { type: Boolean, default: false },
  items: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false }
})
defineEmits(['close', 'mark-seen', 'view-all', 'open-item'])

function icon(type) {
  if (type === 'like.post') return '❤'
  if (type === 'comment.post') return '💬'
  if (type === 'reply.comment') return '↩'
  if (type === 'mention.user') return '@'
  return '•'
}
function label(n) {
  if (n.type === 'like.post') return 'Post liked'
  if (n.type === 'comment.post') return 'New comment'
  if (n.type === 'reply.comment') return 'New reply'
  if (n.type === 'mention.user') return 'You were mentioned'
  return 'Notification'
}
</script>
