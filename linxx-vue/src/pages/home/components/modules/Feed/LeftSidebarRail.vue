<template>
  <aside class="hidden lg:block">
    <div class="sticky top-24 space-y-4">

      <!-- Navigation -->
      <section class="card">
        <h3 class="card-title">Browse</h3>
        <nav class="mt-2 grid gap-2">
          <router-link
v-for="link in navLinks" :key="link.to.name" :to="link.to"
                       class="row hover"
>
            <span>{{ link.label }}</span>
          </router-link>
        </nav>
      </section>

      <!-- Quick Filters -->
      <section class="card">
        <h3 class="card-title">Quick filters</h3>
        <div class="mt-2 flex flex-wrap gap-2">
          <button
v-for="f in quickFilters" :key="f"
                  class="px-3 py-1 rounded-lg text-xs font-semibold
                   bg-gray-200 text-gray-800 hover:bg-gray-300
                   dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
>
            {{ f }}
          </button>
        </div>
      </section>

      <!-- Followed Parties -->
      <section class="card">
        <h3 class="card-title">Followed parties</h3>
        <ul class="mt-3 space-y-2">
          <li v-for="p in followedParties" :key="p.slug">
          </li>
        </ul>
      </section>

    </div>
  </aside>
</template>

<script>
export default {
  name: 'LeftSidebarRail',
  data () {
    return {
      navLinks: [
        { label: 'Feed', to: { name: 'feed' } },
        { label: 'Parties', to: { name: 'parties' } },
        { label: 'Profiles', to: { name: 'profiles' } },
        { label: 'Campaigns', to: { name: 'campaigns' } },
        { label: 'Events', to: { name: 'events' } },
        { label: 'Announcements', to: { name: 'announcements' } },
        { label: 'Media', to: { name: 'media' } },
        { label: 'Books', to: { name: 'books' } },
      ],
      quickFilters: ['Newest', 'Most Liked', 'With Video', 'With Images'],
      followedParties: [
        { name: 'Liberty Coalition', slug: 'liberty-coalition', color: '#ef4444' },
        { name: 'Green Future', slug: 'green-future', color: '#22c55e' },
        { name: 'People First', slug: 'people-first', color: '#3b82f6' },
      ],
    }
  }
}
</script>

<style scoped>
.card {
  @apply rounded-2xl border border-gray-200 bg-white/90 p-4
  dark:border-gray-700 dark:bg-[#12151d]/60;
}
.card-title { @apply text-sm font-bold text-gray-800 dark:text-gray-200; }
.row { @apply flex items-center gap-3 px-3 py-2 rounded-xl; }
.row.hover { @apply hover:bg-gray-100 dark:hover:bg-white/5 transition; }
.avatar { @apply inline-block h-6 w-6 rounded-full; }
</style>
