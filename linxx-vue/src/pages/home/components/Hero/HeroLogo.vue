<template>
  <div class="flex flex-col items-center">
    <div class="flex items-center gap-3">
      <span class="inline-flex h-14 w-14 rounded-full bg-red-600 relative overflow-hidden shadow-sm">
        <span class="absolute inset-0 bg-gradient-to-br from-red-500/40 to-black/10"></span>
        <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 h-8 w-8 rounded-full
                      bg-white dark:bg-[#0f1115]"></span>
      </span>
      <span class="font-extrabold tracking-wide text-4xl sm:text-5xl
                   text-gray-900 dark:text-white">
        Linxx
      </span>
    </div>
  </div>
</template>

<script>
export default { name: 'HeroLogo' }
</script>
