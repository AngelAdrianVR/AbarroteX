<template>
    <AppLayout title="Facturación EzyVentas">
        <main class="lg:w-2/3 p-3 mx-auto mt-20">
            <p>Pagos</p>

            <div class="mt-7 border border-grayD9 rounded-md p-3">
                <PaymentsTable :payments="payments" />
            </div>
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import PaymentsTable from '@/Components/MyComponents/Payment/PaymentsTable.vue';

export default {
    data() {
        return {

        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        PaymentsTable
    },
    props: {
        payments: Array
    },
    methods: {

    }
}
</script>
