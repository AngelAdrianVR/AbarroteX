<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

defineProps({
    terms: String,
});
</script>

<template>
    <Head title="Términos y condiciones" />

    <div class="font-sans text-gray-900 antialiased">
        <div class="pt-4 bg-gray-100">
            <div class="min-h-screen flex flex-col items-center pt-0 sm:pt-0">
                <div>
                    <AuthenticationCardLogo />
                </div>

                <div class="w-full sm:max-w-3xl mt-6 p-6 text-justify bg-white shadow-md overflow-hidden sm:rounded-lg prose" v-html="terms" />
            </div>
        </div>
    </div>
</template>
