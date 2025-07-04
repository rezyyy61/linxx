<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-[#0d0d0d] dark:to-[#1a1a1a]">
        <MainNavbar />

        <div class="flex justify-center items-center py-16 px-4">
            <div class="w-full max-w-4xl bg-white/80 dark:bg-gray-900/90 backdrop-blur-md rounded-3xl shadow-2xl p-10 animate-fade-up">

                <!-- Header -->
                <div class="text-center mb-10">
                    <h1 class="text-4xl font-extrabold text-red-700 dark:text-red-400 tracking-wide">Linxx</h1>
                    <p class="mt-2 text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed tracking-normal">
                        {{ $t('auth.login.subtitle') }}
                    </p>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10" dir="ltr">

                    <!-- Social Login -->
                    <div class="flex flex-col justify-center gap-5">
                        <p class="text-center text-base font-medium text-gray-600 dark:text-gray-300">
                            {{ $t('auth.login.or_continue_with') }}
                        </p>

                        <a
                            href="/auth/google"
                            class="flex items-center gap-3 px-6 py-3 border border-gray-300 rounded-xl bg-white hover:bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-white dark:border-gray-600 transition shadow-sm"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" preserveAspectRatio="xMidYMid" viewBox="0 0 256 262" id="google">
                                <path fill="#4285F4" d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027"></path>
                                <path fill="#34A853" d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1"></path>
                                <path fill="#FBBC05" d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782"></path>
                                <path fill="#EB4335" d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251"></path>
                            </svg>
                            <span class="font-semibold">Login with Google</span>
                        </a>

                        <a
                            href="/auth/twitter"
                            class="flex items-center gap-3 px-6 py-3 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition shadow-sm"
                        >
                            <Twitter class="h-6 w-6" />
                            <span class="font-semibold">Login with Twitter</span>
                        </a>

                        <a
                            href="/auth/facebook"
                            class="flex items-center gap-3 px-6 py-3 rounded-xl bg-[#3b5998] hover:bg-[#2d4373] text-white transition shadow-sm"
                        >
                            <Facebook class="h-6 w-6" />
                            <span class="font-semibold">Login with Facebook</span>
                        </a>
                    </div>

                    <!-- Login Form -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-semibold text-center text-gray-800 dark:text-white">
                            {{ $t('auth.login.title') }}
                        </h2>

                        <form @submit.prevent="login" class="space-y-4">
                            <input
                                v-model="form.email"
                                type="email"
                                :placeholder="$t('auth.login.email')"
                                :class="[
      'form-input',
      $i18n.locale === 'fa' ? 'text-right' : 'text-left'
    ]"
                            />
                            <input
                                v-model="form.password"
                                type="password"
                                :placeholder="$t('auth.login.password')"
                                :class="[
      'form-input',
      $i18n.locale === 'fa' ? 'text-right' : 'text-left'
    ]"
                            />

                            <div :class="[$i18n.locale === 'fa' ? 'text-right' : 'text-left']">
                                <router-link
                                    to="/forgot-password"
                                    class="text-sm text-red-600 dark:text-red-400 hover:underline"
                                >
                                    {{ $t('auth.login.forgot_password') }}
                                </router-link>
                            </div>

                            <button
                                type="submit"
                                class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow transition"
                            >
                                {{ $t('auth.login.submit') }}
                            </button>
                        </form>


                        <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                            {{ $t('auth.login.no_account') }}
                            <router-link to="/register" class="text-red-600 dark:text-red-400 font-medium hover:underline">
                                {{ $t('auth.login.register_link') }}
                            </router-link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import MainNavbar from "@/pages/home/components/MainNavbar.vue";
import { Twitter, Facebook } from 'lucide-vue-next'
import {useAuthStore} from "@/stores/auth";

export default {
    name: 'LoginPage',
    components: {
        MainNavbar,
        Twitter,
        Facebook
    },
    data() {
        return {
            form: {
                email: '',
                password: ''
            }
        }
    },
    methods: {
        async login() {
            const auth = useAuthStore();

            try {
                await auth.login(this.form);
            } catch (error) {
                console.error('Login failed:', error);
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
