حق با توئه؛ این فرمش زیادی بلند بود. این نسخه‌ رو بده جاش: تب‌دار، جمع‌وجور، هدر ثابت، بدنه اسکرولی، دیالوگ مستطیلی. همون props/emit قبلی رو نگه داشتم.

```vue
<template>
  <div v-if="modelValue" class="fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/30" @click="$emit('update:modelValue', false)"></div>

    <div class="absolute left-1/2 top-1/2 w-full max-w-3xl -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xl dark:border-white/10 dark:bg-[#0b0f1a]">
      <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-200 dark:border-white/10">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-full ring-1 ring-inset ring-zinc-200 dark:ring-white/10" :style="{ background: user?.profile?.avatar_color || '#e5e7eb' }"></div>
          <div>
            <h3 class="text-base font-semibold leading-none">{{ user?.name || 'Edit user' }}</h3>
            <div class="mt-1 text-xs text-zinc-500 truncate">ID: {{ user?.id }} • {{ user?.email }}</div>
          </div>
        </div>
        <button class="rounded-lg px-3 py-2 text-sm border hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5" @click="$emit('update:modelValue', false)">Close</button>
      </div>

      <div class="px-5 pt-3">
        <div class="inline-flex rounded-xl ring-1 ring-inset ring-zinc-200 p-1 bg-white dark:bg-white/5 dark:ring-white/10">
          <button :class="['px-3 py-1.5 text-sm rounded-lg', tab==='account' ? 'bg-zinc-900 text-white dark:bg-white dark:text-[#0b0f1a]' : 'text-zinc-600 dark:text-zinc-300']" @click="tab='account'">Account</button>
          <button :class="['px-3 py-1.5 text-sm rounded-lg', tab==='profile' ? 'bg-zinc-900 text-white dark:bg-white dark:text-[#0b0f1a]' : 'text-zinc-600 dark:text-zinc-300']" @click="tab='profile'">Profile</button>
        </div>
      </div>

      <div class="px-5 pb-4">
        <div class="mt-4 max-h-[60vh] overflow-y-auto pr-1">
          <div v-show="tab==='account'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-zinc-500 mb-1">Name</label>
              <input v-model="draft.name" type="text" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent">
            </div>
            <div>
              <label class="block text-xs text-zinc-500 mb-1">Email</label>
              <input v-model="draft.email" type="email" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent">
            </div>
            <div>
              <label class="block text-xs text-zinc-500 mb-1">Slug</label>
              <input v-model="draft.slug" type="text" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent">
            </div>
            <div class="flex items-center gap-3">
              <label class="text-xs text-zinc-500">Verified</label>
              <button
type="button" @click="draft.is_verified = !draft.is_verified" :aria-pressed="draft.is_verified" class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                      :class="draft.is_verified ? 'bg-emerald-600' : 'bg-zinc-300 dark:bg-white/10'"
>
                <span class="inline-block h-5 w-5 transform rounded-full bg-white transition" :class="draft.is_verified ? 'translate-x-5' : 'translate-x-1'"></span>
              </button>
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs text-zinc-500 mb-1">Verification expires at</label>
              <input v-model="draft.verification_expires_at" type="datetime-local" class="w-64 rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent">
            </div>
          </div>

          <div v-show="tab==='profile'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-zinc-500 mb-1">Group name</label>
              <input v-model="draft.profile.group_name" type="text" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent">
            </div>
            <div>
              <label class="block text-xs text-zinc-500 mb-1">Entity type</label>
              <select v-model="draft.profile.entity_type" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent">
                <option value="individual">Individual</option>
                <option value="party">Party</option>
                <option value="collective">Collective</option>
                <option value="media">Media</option>
              </select>
            </div>
            <div>
              <label class="block text-xs text-zinc-500 mb-1">Pending entity type</label>
              <select v-model="draft.profile.pending_entity_type" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent">
                <option value="">None</option>
                <option value="party">Party</option>
                <option value="collective">Collective</option>
                <option value="media">Media</option>
              </select>
            </div>
            <div class="flex items-center gap-3">
              <label class="text-xs text-zinc-500">Approved</label>
              <button
type="button" @click="draft.profile.entity_type_approved = !draft.profile.entity_type_approved" :aria-pressed="draft.profile.entity_type_approved" class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                      :class="draft.profile.entity_type_approved ? 'bg-emerald-600' : 'bg-zinc-300 dark:bg-white/10'"
>
                <span class="inline-block h-5 w-5 transform rounded-full bg-white transition" :class="draft.profile.entity_type_approved ? 'translate-x-5' : 'translate-x-1'"></span>
              </button>
            </div>
            <div>
              <label class="block text-xs text-zinc-500 mb-1">Location</label>
              <input v-model="draft.profile.location" type="text" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent">
            </div>
            <div>
              <label class="block text-xs text-zinc-500 mb-1">Founded year</label>
              <input v-model="draft.profile.founded_year" type="text" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent">
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs text-zinc-500 mb-1">Tagline</label>
              <input v-model="draft.profile.tagline" type="text" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent">
            </div>

            <div class="md:col-span-2">
              <button type="button" class="w-full text-left rounded-xl border px-3 py-2 text-sm flex items-center justify-between dark:border-white/10" @click="moreOpen = !moreOpen">
                <span>More details</span>
                <span class="text-xs text-zinc-500">{{ moreOpen ? 'Hide' : 'Show' }}</span>
              </button>
            </div>
            <template v-if="moreOpen">
              <div class="md:col-span-2">
                <label class="block text-xs text-zinc-500 mb-1">About</label>
                <textarea v-model="draft.profile.about" rows="3" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent"></textarea>
              </div>
              <div class="md:col-span-2">
                <label class="block text-xs text-zinc-500 mb-1">Goals</label>
                <textarea v-model="draft.profile.goals" rows="3" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent"></textarea>
              </div>
              <div class="md:col-span-2">
                <label class="block text-xs text-zinc-500 mb-1">Activities</label>
                <textarea v-model="draft.profile.activities" rows="3" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent"></textarea>
              </div>
              <div class="md:col-span-2">
                <label class="block text-xs text-zinc-500 mb-1">Structure</label>
                <textarea v-model="draft.profile.structure" rows="3" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent"></textarea>
              </div>
              <div>
                <label class="block text-xs text-zinc-500 mb-1">Avatar color</label>
                <input v-model="draft.profile.avatar_color" type="text" class="w-36 rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent" placeholder="#10B981">
              </div>
              <div>
                <label class="block text-xs text-zinc-500 mb-1">Logo path</label>
                <input v-model="draft.profile.logo_path" type="text" class="w-full rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent" placeholder="profiles/logos/...">
              </div>
            </template>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-zinc-200 dark:border-white/10">
        <button @click="$emit('update:modelValue', false)" class="rounded-xl border px-3 py-2 text-sm dark:border-white/10">Cancel</button>
        <button @click="emitSave" :disabled="saving" class="rounded-xl bg-zinc-900 px-3 py-2 text-sm text-white hover:bg-zinc-800 disabled:opacity-60 dark:bg-white dark:text-[#0b0f1a]">Save</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  user: { type: Object, default: null },
  saving: { type: Boolean, default: false }
})
const emit = defineEmits(['update:modelValue', 'save'])

const tab = ref('account')
const moreOpen = ref(false)

const draft = reactive({
  id: null,
  name: '',
  email: '',
  slug: '',
  is_verified: false,
  verification_expires_at: '',
  profile: {
    group_name: '',
    tagline: '',
    entity_type: 'individual',
    pending_entity_type: '',
    entity_type_approved: false,
    location: '',
    founded_year: '',
    logo_path: '',
    about: '',
    goals: '',
    activities: '',
    structure: '',
    avatar_color: ''
  }
})

watch(() => props.user, (u) => {
  if (!u) return
  draft.id = u.id
  draft.name = u.name || ''
  draft.email = u.email || ''
  draft.slug = u.slug || ''
  draft.is_verified = !!u.is_verified
  draft.verification_expires_at = u.verification_expires_at ? u.verification_expires_at.slice(0, 16) : ''
  const p = u.profile || {}
  draft.profile = {
    group_name: p.group_name || '',
    tagline: p.tagline || '',
    entity_type: p.entity_type || 'individual',
    pending_entity_type: p.pending_entity_type || '',
    entity_type_approved: !!p.entity_type_approved,
    location: p.location || '',
    founded_year: p.founded_year || '',
    logo_path: p.logo_path || '',
    about: p.about || '',
    goals: p.goals || '',
    activities: p.activities || '',
    structure: p.structure || '',
    avatar_color: p.avatar_color || ''
  }
}, { immediate: true })

function emitSave() {
  const payload = {
    name: draft.name,
    email: draft.email,
    slug: draft.slug,
    is_verified: draft.is_verified,
    verification_expires_at: draft.verification_expires_at || null,
    profile: { ...draft.profile }
  }
  emit('save', payload)
}
</script>
```
