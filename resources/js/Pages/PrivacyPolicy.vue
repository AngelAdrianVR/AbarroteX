<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

defineProps({
    policy: String,
});
</script>

<template>
    <Head title="Politicas de privacidad" />

    <div class="font-sans text-gray-900 antialiased">
        <div class="pt-1 bg-gray-100">
            <div class="min-h-screen flex flex-col items-center">
                <div>
                    <AuthenticationCardLogo />
                </div>

                <div class="w-full sm:w-full mt-6 p-6 text-justify bg-white shadow-md overflow-hidden sm:rounded-lg prose" v-html="policy" />
            </div>
        </div>
    </div>
</template>
