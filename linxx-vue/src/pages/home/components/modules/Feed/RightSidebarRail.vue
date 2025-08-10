<template>
  <aside class="hidden lg:block">
    <div class="sticky top-24 space-y-4">

      <!-- Trending Campaigns -->
      <section class="card">
        <h3 class="card-title">Trending campaigns</h3>
        <ul class="mt-3 space-y-2">
          <li v-for="c in trendingCampaigns" :key="c.slug" class="row hover justify-between">
            <router-link :to="{ name: 'campaigns' }" class="flex items-center gap-3 truncate">
              <span class="badge">#</span>
              <span class="truncate">{{ c.title }}</span>
            </router-link>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ c.supports }} supporters</span>
          </li>
        </ul>
      </section>

      <!-- Upcoming Events -->
      <section class="card">
        <h3 class="card-title">Upcoming events</h3>
        <ul class="mt-3 space-y-2">
          <li v-for="e in events" :key="e.id" class="row hover justify-between">
            <router-link :to="{ name: 'events' }" class="truncate">{{ e.title }}</router-link>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ e.date }}</span>
          </li>
        </ul>
      </section>

      <!-- Top Profiles -->
      <section class="card">
        <h3 class="card-title">Top profiles</h3>
        <ul class="mt-3 space-y-2">
          <li v-for="u in topProfiles" :key="u.slug">
            <router-link :to="{ name: 'users.show', params: { slug: u.slug } }" class="row hover">
              <img :src="u.avatar" alt="" class="h-6 w-6 rounded-full object-cover" />
              <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">{{ u.name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ u.title }}</p>
              </div>
            </router-link>
          </li>
        </ul>
      </section>

      <!-- Announcements -->
      <section class="card">
        <h3 class="card-title">Announcements</h3>
        <ul class="mt-3 space-y-2">
          <li v-for="a in announcements" :key="a.id" class="row hover">
            <span class="badge">!</span>
            <span class="truncate">{{ a.text }}</span>
          </li>
        </ul>
      </section>

      <!-- Who to follow -->
      <section class="card">
        <h3 class="card-title">Who to follow</h3>
        <ul class="mt-3 space-y-2">
          <li v-for="s in suggestions" :key="s.slug" class="flex items-center justify-between gap-3">
            <router-link :to="{ name: 'users.show', params: { slug: s.slug } }" class="row hover flex-1">
              <img :src="s.avatar" alt="" class="h-6 w-6 rounded-full object-cover" />
              <span class="truncate">{{ s.name }}</span>
            </router-link>
            <button class="px-2 py-1 rounded-lg text-xs font-semibold
                           bg-gray-200 text-gray-800 hover:bg-gray-300
                           dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
              Follow
            </button>
          </li>
        </ul>
      </section>

    </div>
  </aside>
</template>

<script>
export default {
  name: 'RightSidebarRail',
  data () {
    return {
      trendingCampaigns: [
        { title: 'Clean River Act', slug: 'clean-river', supports: '12.4k' },
        { title: 'Transparency Now', slug: 'transparency-now', supports: '9.8k' },
        { title: 'Education for All', slug: 'edu4all', supports: '7.1k' },
      ],
      events: [
        { id: 1, title: 'National Debate — Round 2', date: 'Fri 19:00' },
        { id: 2, title: 'Fundraising Live Stream', date: 'Sat 17:30' },
        { id: 3, title: 'Local Town Hall', date: 'Mon 10:00' },
      ],
      topProfiles: [
        { slug: 'a.mir', name: 'A. Mir', title: 'Party Leader', avatar: 'https://i.pravatar.cc/40?img=12' },
        { slug: 's.karimi', name: 'S. Karimi', title: 'City Councillor', avatar: 'https://i.pravatar.cc/40?img=5' },
        { slug: 'l.azimi', name: 'L. Azimi', title: 'Journalist', avatar: 'https://i.pravatar.cc/40?img=22' },
      ],
      announcements: [
        { id: 1, text: 'New privacy policy draft open for comments.' },
        { id: 2, text: 'Platform maintenance this weekend.' },
      ],
      suggestions: [
        { slug: 'm.rahimi', name: 'M. Rahimi', avatar: 'https://i.pravatar.cc/40?img=15' },
        { slug: 'n.hosseini', name: 'N. Hosseini', avatar: 'https://i.pravatar.cc/40?img=32' },
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
.badge { @apply inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-600 text-white text-[10px] font-bold; }
</style>
