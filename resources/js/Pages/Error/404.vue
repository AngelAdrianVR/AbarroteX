<template>
    <!-- <AppLayout title="Pagina no encontrada"> -->

        <Head title="Error 404" />

        <section class="flex flex-col items-center justify-center text-xl mt-20">
            <p class="font-bold text-3xl">404</p>
            <p class="mb-5">Lo sentimos, página no encontrada</p>
            <PrimaryButton @click="$inertia.visit(route('dashboard'))">Volver al punto de venta</PrimaryButton>
            <div class="w-48">
                <ApplicationMark />
            </div>
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
    }
}
</script>