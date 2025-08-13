<template>
  <div class="absolute inset-0 pointer-events-none select-none overflow-hidden">
    <!-- Wave layer 1 - base -->
    <svg
        class="w-[200%] h-[120%] animate-wave-slow"
        viewBox="0 0 1440 320"
        preserveAspectRatio="none"
    >
      <defs>
        <linearGradient id="waveLight1" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#e2e8f0"/>
          <stop offset="100%" stop-color="#cbd5e1"/>
        </linearGradient>
        <linearGradient id="waveDark1" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#2a2f3d"/>
          <stop offset="100%" stop-color="#0f1115"/>
        </linearGradient>
      </defs>
      <path
          class="block dark:hidden"
          :fill="'url(#waveLight1)'"
          fill-opacity="1"
          d="M0,192L60,197.3C120,203,240,213,360,202.7C480,192,600,160,720,154.7C840,149,960,171,1080,170.7C1200,171,1320,149,1380,138.7L1440,128L1440,320L0,320Z"
      />
      <path
          class="hidden dark:block"
          :fill="'url(#waveDark1)'"
          fill-opacity="1"
          d="M0,192L60,197.3C120,203,240,213,360,202.7C480,192,600,160,720,154.7C840,149,960,171,1080,170.7C1200,171,1320,149,1380,138.7L1440,128L1440,320L0,320Z"
      />
    </svg>

    <!-- Wave layer 2 - highlight -->
    <svg
        class="absolute top-0 w-[200%] h-[130%] opacity-85 animate-wave-fast"
        viewBox="0 0 1440 320"
        preserveAspectRatio="none"
    >
      <defs>
        <linearGradient id="waveLight2" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#f8fafc"/>
          <stop offset="100%" stop-color="#e2e8f0"/>
        </linearGradient>
        <linearGradient id="waveDark2" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#3b4252"/>
          <stop offset="100%" stop-color="#1f2330"/>
        </linearGradient>
      </defs>
      <path
          class="block dark:hidden"
          :fill="'url(#waveLight2)'"
          d="M0,192L80,170.7C160,149,320,107,480,106.7C640,107,800,149,960,149.3C1120,149,1280,107,1360,85.3L1440,64L1440,320L0,320Z"
      />
      <path
          class="hidden dark:block"
          :fill="'url(#waveDark2)'"
          d="M0,192L80,170.7C160,149,320,107,480,106.7C640,107,800,149,960,149.3C1120,149,1280,107,1360,85.3L1440,64L1440,320L0,320Z"
      />
    </svg>
  </div>
</template>

<script>
export default { name: 'HeroBackground' }
</script>

<style scoped>
@keyframes waveSlow {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}
@keyframes waveFast {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.animate-wave-slow {
  animation: waveSlow 25s linear infinite;
}
.animate-wave-fast {
  animation: waveFast 18s linear infinite;
}
</style>
