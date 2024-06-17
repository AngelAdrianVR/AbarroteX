<template>
    <Loading v-if="loading" />
    <div v-else class="text-sm mt-5 space-y-4">
        <OnlineSaleDetails v-for="(item, index) in sales" :key="index" :onlineSale="item" @show-modal="showModal"
            :ref="'osd' + item.id" />

        <ConfirmationModal :show="showRefundConfirm" @close="showRefundConfirm = false">
            <template #title>
                <h1>Reembolsar venta</h1>
            </template>
            <template #content>
                <p v-if="isInventoryOn">
                    Se devolverán los productos de la venta al inventario y se retirará el monto de dinero
                    correspondiente de la caja.
                    Si en caja no hay suficiente dinero, quedará en $0.00
                    ¿Deseas continuar?
                </p>
                <p v-else>
                    Se retirará el monto correspondiente a esta venta de la caja. Si en caja no hay suficiente dinero,
                    quedará en $0.00 ¿Deseas continuar?
                </p>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="showRefundConfirm = false" :disabled="refunding">Cancelar</CancelButton>
                    <PrimaryButton @click="refundSale" :disabled="refunding">Continuar</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>

        <ConfirmationModal :show="showCancelConfirm" @close="showCancelConfirm = false">
            <template #title>
                <h1>Cancelar venta</h1>
            </template>
            <template #content>
                <p>
                    Se cancelará la venta y esto es irreversible. ¿Deseas continuar?
                </p>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="showCancelConfirm = false" :disabled="canceling">Cancelar</CancelButton>
                    <PrimaryButton @click="cancelSale" :disabled="canceling">Continuar</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
    </div>
</template>

<script>
import OnlineSaleDetails from "@/Components/MyComponents/OnlineSale/OnlineSaleDetails.vue";
import Loading from "@/Components/MyComponents/Loading.vue";
import axios from "axios";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { addOrUpdateBatchOfItems } from '@/dbService.js';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";

export default {
    name: 'OnlineSales',
    data() {
        return {
            sales: [],
            saleIdToRefund: null,
            saleIdToCancel: null,
            // carga
            loading: false,
            refunding: false,
            canceling: false,
            // inventario de codigos activado
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
            // modales
            showRefundConfirm: false,
            showCancelConfirm: false,
        };
    },
    components: {
        OnlineSaleDetails,
        Loading,
        ConfirmationModal,
        PrimaryButton,
        CancelButton,
    },
    props: {
        date: String,
    },
    computed: {

    },
    methods: {
        showModal(modal, saleId) {
            if (modal === 'refund') {
                this.saleIdToRefund = saleId;
                this.showRefundConfirm = true;
            } else if (modal === 'cancel') {
                this.saleIdToCancel = saleId;
                this.showCancelConfirm = true;
            }
        },
        async fetchOnlineSalesByDate() {
            this.loading = true;
            try {
                const response = await axios.get(route('online-sales.get-sales-by-date', this.date));

                if (response.status === 200) {
                    this.sales = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async refundSale() {
            this.refunding = true;
            try {
                let response = await axios.post(route('online-sales.refund', this.saleIdToRefund));
                if (response.status === 200) {
                    // Obtener productos de servidor
                    response = await axios.get(route('products.get-all-for-indexedDB'));
                    const products = response.data.products;
                    // actualizar lista de productos en indexedDB
                    addOrUpdateBatchOfItems('products', products);

                    this.showRefundConfirm = false;

                    // actualizar elementos de la vista (reactividad)
                    const reference = 'osd' + this.saleIdToRefund;
                    this.$refs[reference][0].markAsRefunded(this.saleIdToRefund);

                    this.$notify({
                        title: 'Venta reembolsada',
                        message: '',
                        type: 'success',
                    });
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'No se pudo procesar la peticion de reembolso',
                    message: '',
                    type: 'error',
                });
            } finally {
                this.refunding = false;
            }
        },
        async cancelSale() {
            this.canceling = true;
            try {
                let response = await axios.post(route('online-sales.cancel', this.saleIdToCancel));
                if (response.status === 200) {
                    // Obtener productos de servidor
                    
                    if (response.data.prevStatus === 'Procesando') {
                        response = await axios.get(route('products.get-all-for-indexedDB'));
                        const products = response.data.products;
                        // actualizar lista de productos en indexedDB
                        addOrUpdateBatchOfItems('products', products);
                    }

                    this.showCancelConfirm = false;

                    // actualizar elementos de la vista (reactividad)
                    const reference = 'osd' + this.saleIdToCancel;
                    this.$refs[reference][0].markAsCanceled();

                    this.$notify({
                        title: 'Venta cancelada',
                        message: '',
                        type: 'success',
                    });
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'No se pudo procesar la peticion de cancelación',
                    message: '',
                    type: 'error',
                });
            } finally {
                this.refunding = false;
            }
        },
    },
    async mounted() {
        await this.fetchOnlineSalesByDate();
    }
};
</script>