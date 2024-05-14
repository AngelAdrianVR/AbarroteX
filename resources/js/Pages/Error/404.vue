<template>
    <AppLayout title="Pagina no encontrada">
        <section class="flex flex-col items-center justify-center">
            <p>404</p>
            <p>Lo sentimos, página no encontrada</p>
            <PrimaryButton @click="$inertia.visit(route('sales.point'))">Volver al punto de venta</PrimaryButton>
            <ApplicationMark />
        </section>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ApplicationMark from '@/Components/ApplicationMark.vue';

export default {
    components: {
        AppLayout,
        PrimaryButton,
        ApplicationMark,
    }
}
</script>