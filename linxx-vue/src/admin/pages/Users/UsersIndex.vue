<template>
  <div class="space-y-6 w-[90%] mx-auto my-6">
    <header class="flex items-center justify-between">
      <div>
        <h1 class="text-xl font-bold">Users</h1>
        <p class="text-sm text-zinc-500">Manage users and profiles</p>
      </div>
      <div class="flex items-center gap-2 text-sm">
        <span class="hidden sm:inline-flex items-center gap-2 rounded-xl bg-white px-3 py-1.5 ring-1 ring-inset ring-zinc-200 dark:bg-white/5 dark:ring-white/10">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span>Total: {{ pagination.total }}</span>
        </span>
        <button
            @click="openRequests"
            class="inline-flex items-center gap-2 rounded-xl px-3 py-1.5 ring-1 ring-inset ring-zinc-200 bg-white hover:bg-zinc-50 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10"
        >
          <Icon icon="mdi:bell-outline" class="w-4 h-4" />
          <span>Type requests</span>
          <span
              v-if="requestsCount>0"
              class="ml-1 inline-flex items-center justify-center rounded-full px-2 py-0.5 text-xs font-semibold bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300"
          >
            {{ requestsCount }}
          </span>
          <span v-if="requestsCount>0" class="relative">
            <span class="absolute -top-3 -right-3 h-2 w-2 rounded-full bg-rose-500 animate-ping"></span>
          </span>
        </button>
      </div>
    </header>

    <UsersToolbar
        :q="filters.q"
        :entity-type="filters.entity_type"
        :loading="loading"
        @change="onFiltersChange"
        @refresh="reload"
    />

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-[#0b0f1a]">
      <UsersTable
          :items="list"
          :loading="loading"
          @edit="openEdit"
          @delete="openDelete"
      />

      <div class="flex items-center justify-between gap-3 border-t border-zinc-100 p-3 text-sm dark:border-white/10">
        <div class="text-zinc-500">Showing {{ startIndex + 1 }}–{{ endIndex }} of {{ filteredCount }}</div>
        <PaginationLite
            :page="pagination.page"
            :per-page="pagination.perPage"
            :total="pagination.total"
            :last-page="pagination.lastPage"
            :busy="loading"
            @change:page="changePage"
            @change:perPage="changePerPage"
        />
      </div>
    </div>

    <UserEditModal
        v-model="editOpen"
        :user="selected"
        :saving="saving"
        @save="saveEdit"
    />

    <UserDeleteModal
        v-model="deleteOpen"
        :user="selected"
        :deleting="deleting"
        @confirm="confirmDelete"
    />

    <Teleport to="body">
      <Transition name="fade">
        <div v-if="requestsOpen" class="fixed inset-0 z-50">
          <div class="absolute inset-0 bg-black/30" @click="requestsOpen=false"></div>
          <div class="absolute left-1/2 top-1/2 w-[min(1100px,95vw)] max-h-[85vh] -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xl dark:border-white/10 dark:bg-[#0b0f1a]">
            <div class="flex items-center justify-between px-5 py-3 border-b border-zinc-100 dark:border-white/10">
              <div class="text-sm font-semibold">Type change requests</div>
              <div class="flex items-center gap-2">
                <button @click="refreshTypeRequestsCount" :disabled="requestsLoading" class="rounded-xl border px-3 py-1.5 text-sm dark:border-white/10">Refresh</button>
                <button @click="requestsOpen=false" class="rounded-xl border px-3 py-1.5 text-sm dark:border-white/10">Close</button>
              </div>
            </div>
            <div class="p-4 overflow-auto">
              <UserTypeRequestsIndex />
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { Icon } from '@iconify/vue'
import { useAdminUsersStore } from '@/stores/admin/users/users.store'
import { useTypeRequestsStore } from '@/stores/admin/users/typeRequests.store'
import UsersToolbar from '@/admin/components/users/UsersToolbar.vue'
import UsersTable from '@/admin/components/users/UsersTable.vue'
import PaginationLite from '@/admin/components/shared/PaginationLite.vue'
import UserEditModal from '@/admin/components/users/UserEditModal.vue'
import UserDeleteModal from '@/admin/components/users/UserDeleteModal.vue'
import UserTypeRequestsIndex from "@/admin/components/users/UserTypeRequests/UserTypeRequestsIndex.vue";


const store = useAdminUsersStore()
const trStore = useTypeRequestsStore()

const loading = computed(() => store.listLoading)
const saving = computed(() => store.saving)
const deleting = computed(() => store.deleting)
const list = computed(() => store.list)
const pagination = computed(() => store.pagination)
const filteredCount = computed(() => store.pagination.total)
const startIndex = computed(() => (store.pagination.page - 1) * store.pagination.perPage)
const endIndex = computed(() => Math.min(store.pagination.total, startIndex.value + store.pagination.perPage))

const filters = ref({ q: store.filters.q, entity_type: store.filters.entity_type })

const editOpen = ref(false)
const deleteOpen = ref(false)
const selected = ref(null)

const requestsOpen = ref(false)
const requestsLoading = computed(() => trStore.loading)
const requestsCount = computed(() => trStore.pagination.total || 0)

onMounted(() => {
  reload()
  refreshTypeRequestsCount()
})

function reload() {
  store.fetchList()
}

function onFiltersChange(payload) {
  filters.value = { ...filters.value, ...payload }
  store.setFilters(payload)
  store.fetchList()
}

function changePage(p) {
  store.setPage(p)
  store.fetchList()
}

function changePerPage(n) {
  store.setPerPage(n)
  store.fetchList()
}

function openEdit(u) {
  selected.value = u
  editOpen.value = true
}

async function saveEdit(payload) {
  if (!selected.value) return
  await store.updateOne(selected.value.id, payload)
  editOpen.value = false
}

function openDelete(u) {
  selected.value = u
  deleteOpen.value = true
}

async function confirmDelete() {
  if (!selected.value) return
  await store.deleteOne(selected.value.id)
  deleteOpen.value = false
  selected.value = null
  store.fetchList()
}

function openRequests() {
  requestsOpen.value = true
}

function refreshTypeRequestsCount() {
  trStore.fetchList({ page: 1, perPage: 1 })
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .15s ease }
.fade-enter-from, .fade-leave-to { opacity: 0 }
</style>
