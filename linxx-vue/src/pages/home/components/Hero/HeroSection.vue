<template>
  <section
      class="relative overflow-hidden text-center pt-16 pb-20
           bg-gradient-to-b from-white to-gray-100
           dark:from-[#0f1115] dark:to-[#12151d]"
  >
    <HeroBackground />

    <div class="relative z-10 flex flex-col items-center gap-6">
      <HeroLogo />
      <HeroSubtitle />
      <HeroCTA />
    </div>
  </section>
</template>

<script>
import HeroBackground from './HeroBackground.vue'
import HeroLogo from './HeroLogo.vue'
import HeroSubtitle from './HeroSubtitle.vue'
import HeroCTA from './HeroCTA.vue'

export default {
  name: 'HeroSection',
  components: { HeroBackground, HeroLogo, HeroSubtitle, HeroCTA },
}
</script>
