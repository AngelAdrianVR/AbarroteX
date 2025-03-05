<template>
    <!-- <AppLayout title="Pagina no encontrada"> -->

    <Head title="Error 404" />

    <section class="flex flex-col items-center justify-center text-xl mt-20">
        <p class="font-bold text-3xl">404</p>
        <p class="mb-5">Lo sentimos, página no encontrada</p>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-0.5 -0.5 16 16" fill="none" stroke="currentColor"
            stroke-linecap="round" stroke-linejoin="round" id="Compass-Off--Streamline-Tabler" height="150" width="150">
            <desc>Compass Off Streamline Icon: https://streamlinehq.com</desc>
            <path d="m8.125 5.625 1.875 -0.625 -0.625 1.875m-0.625 1.875 -3.75 1.25 1.25 -3.75" stroke-width="1"></path>
            <path
                d="M12.526250000000001 10.028125000000001A5.625 5.625 0 0 0 4.971875 2.47375M3.523125 3.5218749999999996a5.625 5.625 0 1 0 7.953125 7.956250000000001"
                stroke-width="1"></path>
            <path d="M7.5 1.875v1.25" stroke-width="1"></path>
            <path d="M7.5 11.875v1.25" stroke-width="1"></path>
            <path d="M1.875 7.5h1.25" stroke-width="1"></path>
            <path d="M11.875 7.5h1.25" stroke-width="1"></path>
            <path d="m1.875 1.875 11.25 11.25" stroke-width="1"></path>
        </svg>
        <PrimaryButton @click="$inertia.visit(route('dashboard'))" class="my-6">Volver a la Punto de venta
        </PrimaryButton>
    </section>
    <!-- </AppLayout> -->
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import { Head } from '@inertiajs/vue3';

export default {
    components: {
        AppLayout,
        PrimaryButton,
        ApplicationMark,
        Head
    },
    props: {
        slug: String,
    }
}
</script>