<template>
    <AppLayout title="Facturación EzyVentas">
        <main class="lg:w-2/3 p-3 mx-auto mt-20">
            <div class="flex items-center justify-between">
                <p>Pagos</p>
                <PrimaryButton>Actualizar datos fiscales</PrimaryButton>
            </div>

            <div class="mt-7 border border-grayD9 rounded-md p-3">
                <InternalInvoicesTable :internal_invoices="internal_invoices" />
            </div>
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InternalInvoicesTable from '@/Components/MyComponents/InternalInvoice/InternalInvoicesTable.vue';

export default {
data() {
    return {

    }
},
components:{
    AppLayout,
    PrimaryButton,
    InternalInvoicesTable
},
props:{
    internal_invoices: Array
},
methods:{

}
}
</script>
