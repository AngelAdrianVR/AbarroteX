<template>
    <Loading v-if="loading" />
    <section v-else class="text-sm mt-5">
        <div v-if="sales.length" class="space-y-4">
            <OnlineSaleDetails v-for="(item, index) in sales" :key="index" :onlineSale="item" @show-modal="showModal"
                :ref="'osd' + item.id" />

            <ConfirmationModal :show="showRefundConfirm" @close="showRefundConfirm = false">
                <template #title>
                    <h1>Reembolsar venta</h1>
                </template>
                <template #content>
                    <p>
                        Se devolverán los productos de la venta al inventario y se retirará el monto de dinero
                        correspondiente de la caja.
                        Si en caja no hay suficiente dinero, quedará en $0.00
                        ¿Deseas continuar?
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
        <el-empty v-else description="No hay ventas para mostrar" />
    </section>
</template>

<script>
import OnlineSaleDetails from "@/Components/MyComponents/OnlineSale/OnlineSaleDetails.vue";
import Loading from "@/Components/MyComponents/Loading.vue";
import axios from "axios";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { addOrUpdateBatchOfItems, getItemByAttributes } from '@/dbService.js';
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
                    // if (this.isInventoryOn) {
                    this.updateIndexedDBproductsStock(response.data.updated_items);
                    // }

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

                    // if (this.isInventoryOn) {
                    this.updateIndexedDBproductsStock(response.data.updated_items);
                    // }

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
        async updateIndexedDBproductsStock(updatedItems) {
            // actualizar stock de productos de indexedDB
            const products = await Promise.all(updatedItems.map(async (item) => {
                // Obtener productos por código
                let foundProducts = await getItemByAttributes('products', { name: item.name });

                // Verificar si se encontró el producto
                if (foundProducts.length > 0) {
                    // Actualizar el stock
                    foundProducts[0].current_stock = item.current_stock || 0;
                    return foundProducts[0];
                }

                // Manejar el caso donde no se encuentre el producto
                return null;
            }));

            // Filtrar productos que no fueron encontrados
            const validProducts = products.filter(product => product !== null);

            // Actualizar los productos en IndexedDB
            await addOrUpdateBatchOfItems('products', validProducts);
        }
    },
    async mounted() {
        await this.fetchOnlineSalesByDate();
    }
};
</script>