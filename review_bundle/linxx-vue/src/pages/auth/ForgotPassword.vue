<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-[#0d0d0d] dark:to-[#1a1a1a]">
        <MainNavbar />

        <div class="flex justify-center items-center py-16 px-4">
            <div class="w-full max-w-md bg-white/80 dark:bg-gray-900/90 backdrop-blur-md rounded-3xl shadow-2xl p-8 animate-fade-up">

                <!-- Header -->
                <div class="text-center mb-8 p-4">
                    <h1 class="text-3xl font-extrabold text-red-700 dark:text-red-400 p-2">
                        {{ $t('auth.forgot.title') }}
                    </h1>
                    <p class="mt-2 text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed tracking-normal">
                        {{ $t('auth.forgot.subtitle') }}
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <input
                        v-model="email"
                        type="email"
                        :placeholder="$t('auth.forgot.email')"
                        :class="[
              'form-input',
              $i18n.locale === 'fa' ? 'text-right' : 'text-left'
            ]"
                    />

                    <button
                        type="submit"
                        class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow transition"
                    >
                        {{ $t('auth.forgot.submit') }}
                    </button>
                </form>

                <!-- Back to login -->
                <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6">
                    <router-link to="/login" class="text-red-600 dark:text-red-400 font-medium hover:underline">
                        {{ $t('auth.forgot.back_to_login') }}
                    </router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import MainNavbar from "@/pages/home/components/MainNavbar.vue";

export default {
    name: 'ForgotPassword',
    components: { MainNavbar },
    data() {
        return {
            email: ''
        }
    },
    methods: {
        async submit() {
            try {
                // Backend call here
                console.log('Sending reset link to', this.email)
                // await axios.post('/api/forgot-password', { email: this.email })
            } catch (e) {
                console.error(e)
            }
        }
    }
}
</script>

<style scoped>
@keyframes fade-up {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-up {
    animation: fade-up 0.6s ease-out both;
}
.form-input {
    @apply w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500;
}
</style>
