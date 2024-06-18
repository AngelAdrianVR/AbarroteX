<template>
    <Loading v-if="loading" class="mt-10" />
    <section v-else-if="getCashSales.length">
        <header class="flex items-center justify-between space-x-3">
            <!-- Imprimir historial -->
            <button @click.stop="print" class="border border-[#D9D9D9] rounded-full py-1 px-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                </svg>
                <p class="text-sm ml-2">Imprimir historial</p>
            </button>

            <!-- filtro -->
            <!-- <div class="relative">
                <button @click.stop="showFilter = !showFilter"
                    class="border border-[#D9D9D9] rounded-full py-1 px-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="16" width="16"
                        id="Filter-Sort-Lines-Descending--Streamline-Ultimate">
                        <desc>Filter Sort Lines Descending Streamline Icon: https://streamlinehq.com</desc>
                        <defs></defs>
                        <title>filter</title>
                        <path d="M0.73 4.2791H23.27" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="1"></path>
                        <path d="M3.131 9.426H20.869" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="1"></path>
                        <path d="M8.7141 19.7209H15.2859" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="1"></path>
                        <path d="M5.531 14.573H18.469" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="1"></path>
                    </svg>
                    <p class="text-sm ml-2">Filtrar</p>
                </button>
                <div v-if="showFilter"
                    class="absolute top-9 right-0 border border[#D9D9D9] rounded-md p-4 bg-white shadow-lg z-10 w-80">
                    <div class="mb-3">
                        <InputLabel value="Rango de fechas" class="ml-3 mb-1" />
                        <div v-if="isMobile" class="flex items-center space-x-2">
                            <el-date-picker @change="handleStartDateChange" :disabled-date="disabledPrevDays"
                                v-model="startDate" type="date" class="!w-1/2" placeholder="Inicio" size="small" />
                            <el-date-picker @change="handleFinishDateChange" :disabled-date="disabledNextDays"
                                v-model="finishDate" type="date" class="!w-1/2" placeholder="Final" size="small" />
                        </div>
                        <div v-else>
                            <el-date-picker v-model="searchDate" type="daterange" range-separator="A"
                                start-placeholder="Fecha de inicio" end-placeholder="Fecha de fin" class="!w-full" />
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <PrimaryButton @click="searchSales" class="!py-1"
                            :disabled="isMobile ? !(startDate && finishDate) : !searchDate">
                            Aplicar
                        </PrimaryButton>
                        <ThirthButton @click="cleanFilter" class="!py-1">Limpiar</ThirthButton>
                    </div>
                </div>
            </div> -->
        </header>

        <!-- Ventas -->
        <main class="flex flex-col space-y-5 mt-10 text-sm">
            <SaleDetails v-for="(item, index) in getCashSales" :key="index" :groupedSales="item"
                @show-modal="handleShowModal" :folio="index" />
        </main>

        <DialogModal :show="showEditModal" @close="closeEditModal()">
            <template #title>
                <h1>Editar venta</h1>
            </template>
            <template #content>
                <form @submit.prevent="update">
                    <section v-for="(product, index) in form.products" :key="index"
                        class="flex items-center space-x-2 mb-1">
                        <div class="w-1/3 md:w-1/2">
                            <InputLabel value="Producto" />
                            <el-select v-model="product.product_id" filterable placeholder="Selecciona el producto"
                                no-data-text="No hay opciones registradas"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="item in products" :key="item.id" :label="item.name"
                                    :value="item.id" />
                            </el-select>
                            <InputError :message="form.errors[`products.${index}.product_id`]" />
                        </div>
                        <div class="w-1/3 md:w-1/4">
                            <InputLabel value="$ por unidad" />
                            <el-input v-model.number="product.current_price" placeholder="No olvides llenar este campo"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/[^\d.]/g, '').replace(/(\..*)\./g, '$1')" required>
                            </el-input>
                            <InputError :message="form.errors[`products.${index}.current_price`]" />
                        </div>
                        <div class="w-1/3 md:w-1/4">
                            <InputLabel value="Cantidad" />
                            <el-input v-model.number="product.quantity" placeholder="No olvides llenar este campo"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/[^\d.]/g, '').replace(/(\..*)\./g, '$1')" required>
                            </el-input>
                            <InputError :message="form.errors[`products.${index}.quantity`]" />
                        </div>
                    </section>
                </form>

            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="closeEditModal()" :disabled="editing">Cancelar</CancelButton>
                    <PrimaryButton @click="update" :disabled="editing">Guardar cambios</PrimaryButton>
                </div>
            </template>
        </DialogModal>
        <ConfirmationModal :show="showRefundConfirm" @close="showRefundConfirm = false">
            <template #title>
                <h1>Reembolsar / Cancelar venta</h1>
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
    </section>
    <el-empty v-else description="No hay ventas para mostrar" />
</template>

<script>
import Loading from '@/Components/MyComponents/Loading.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from "@/Components/InputError.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import SaleDetails from '@/Components/MyComponents/Sale/SaleDetails.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DialogModal from '@/Components/DialogModal.vue';
import { useForm } from "@inertiajs/vue3";
import { addOrUpdateBatchOfItems, getAll, getItemByAttributes } from '@/dbService.js';
import axios from 'axios';

export default {
    data() {
        const form = useForm({
            folio: null,
            products: []
        });

        return {
            form,
            products: [],
            // modales
            showEditModal: false,
            showRefundConfirm: false,
            saleFolioToRefund: null,
            // carga
            loading: false,
            refunding: false,
            editing: false,
            // filtro
            showFilter: false,
            sales: [],
            // inventario de codigos activado
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
        }
    },
    components: {
        Loading,
        InputLabel,
        InputError,
        PrimaryButton,
        ThirthButton,
        SaleDetails,
        CancelButton,
        ConfirmationModal,
        DialogModal,
    },
    props: {
        clientId: Number
    },
    computed: {
        isMobile() {
            return window.innerWidth < 768;
        },
        getCashSales() {
            // Inicializar un arreglo para almacenar todas las ventas filtradas
            let filteredSales = [];

            // Recorrer los datos por fecha
            for (let date in this.sales) {
                if (this.sales.hasOwnProperty(date)) {
                    // Recorrer las ventas del día
                    for (let saleId in this.sales[date].sales) {
                        if (this.sales[date].sales.hasOwnProperty(saleId)) {
                            let sale = this.sales[date].sales[saleId];

                            // Verificar si `credit_data` no es null
                            if (!sale.credit_data) {
                                // Agregar la venta filtrada al arreglo
                                filteredSales.push(sale);
                            }
                        }
                    }
                }
            }

            // Retornar el arreglo de ventas filtradas
            return filteredSales;
        },
    },
    methods: {
        formatSalesProductId() {
            // cambiar el id de cada venta para que coincida con id de productos en indexedDB
            Object.values(this.sales).forEach(day => {
                Object.values(day.sales).forEach(sale => {
                    sale.products.forEach(product => {
                        // revisar si el id es un numero
                        if (Number.isFinite(product.product_id)) {
                            product.product_id = product.is_global_product
                                ? 'global_' + product.product_id
                                : 'local_' + product.product_id;
                        }
                    })
                })
            });
        },
        closeEditModal() {
            this.showEditModal = false;
        },
        openEditModal(saleFolio) {
            // Preparar form con una copia profunda de las ventas seleccionadas
            let sale = this.getCashSales.find(item => item.folio == saleFolio);
            this.form.folio = sale.folio;
            this.form.products = JSON.parse(JSON.stringify(sale.products));

            // Abrir modal
            this.showEditModal = true;
        },
        openRefundModal(saleFolio) {
            this.showRefundConfirm = true;
            this.saleFolioToRefund = saleFolio;
        },
        handleShowModal(modal, saleFolio) {
            if (modal == 'edit') this.openEditModal(saleFolio);
            else if (modal == 'refund') this.openRefundModal(saleFolio);
        },
        print() {
            window.open(route('clients.print-cash-historial', this.clientId), '_blank');
        },
        update() {
            this.editing = true;
            this.form.post(route('sales.update-group-sale'), {
                onSuccess: async () => {
                    // Obtener productos de servidor
                    await this.fetchSales(false);
                    const response = await axios.get(route('products.get-all-for-indexedDB'));
                    const products = response.data.products;
                    // actualizar lista de productos en indexedDB
                    addOrUpdateBatchOfItems('products', products);

                    this.showEditModal = false;

                    this.$notify({
                        title: 'Venta actualizada',
                        message: '',
                        type: 'success',
                    });

                    // volver a formatear id de productos para que no de error al querer editar de nuevo la venta
                    this.formatSalesProductId();
                },
                onFinish: () => {
                    this.editing = false;
                }
            });
        },
        async refundSale() {
            this.refunding = true;
            try {
                let response = await axios.post(route('sales.refund', this.saleFolioToRefund));
                if (response.status === 200) {
                    this.updateIndexedDBproductsStock(response.data.updated_items);

                    this.showRefundConfirm = false;

                    // actualizar elementos de la vista (reactividad)
                    let sale = this.getCashSales.find(item => item.folio == this.saleFolioToRefund);
                    sale.products.forEach(element => {
                        element.refunded_at = new Date().toISOString();
                    });

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
        async fetchSales(loading = true) {
            this.loading = loading;
            try {
                const response = await axios.get(route('clients.get-client-sales', this.clientId));

                if (response.status === 200) {
                    this.sales = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
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
        await this.fetchSales();
        this.products = await getAll('products');
        this.formatSalesProductId();
    }
}
</script>
