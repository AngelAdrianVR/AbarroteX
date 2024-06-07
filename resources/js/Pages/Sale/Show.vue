<template>
    <AppLayout :title="'Venta del día'">
        <div class="md:px-10 px-2 py-7 text-xs md:text-sm">
            <div class="flex justify-between items-center">
                <Back :to="route('sales.index')" />
            </div>

            <!-- Información de la venta -->
            <header class="mt-7 lg:mx-16 text-gray99">
                <p>Total de productos vendidos: <span class="font-thin ml-2 text-gray37">{{
                    Object.values(day_sales)[0].total_quantity }}</span></p>
                <p>Fecha de venta: <span class="font-thin ml-2 text-gray37">{{
                    formatDate(Object.keys(day_sales)[0]) }}</span></p>
                <p>Total de venta: <span class="font-black ml-2 text-gray37">${{
                    Object.values(day_sales)[0].total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                        }}</span></p>
            </header>

            <!-- Productos -->
            <main class="flex flex-col space-y-5 lg:mx-16 mt-10">
                <SaleDetails v-for="(item, index) in getGroupedSales" :key="index" :groupedSales="item"
                    @show-modal="handleShowModal" :folio="index" />
            </main>
        </div>

        <DialogModal :show="showEditModal" @close="closeEditModal()">
            <template #title>
                <h1>Editar venta</h1>
            </template>
            <template #content>
                <form @submit.prevent="update">
                    <section v-for="(sale, index) in form.sales" :key="index" class="flex items-center space-x-2 mb-1">
                        <div class="w-1/3 md:w-1/2">
                            <InputLabel value="Producto" />
                            <el-select v-model="sale.product_id" filterable placeholder="Selecciona el producto"
                                no-data-text="No hay opciones registradas"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="item in products" :key="item.id" :label="item.name"
                                    :value="item.id" />
                            </el-select>
                            <InputError :message="form.errors[`sales.${index}.product_id`]" />
                        </div>
                        <div class="w-1/3 md:w-1/4">
                            <InputLabel value="Precio por unidad" />
                            <el-input v-model.number="sale.current_price" placeholder="No olvides llenar este campo"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/[^\d.]/g, '').replace(/(\..*)\./g, '$1')" required>
                            </el-input>
                            <InputError :message="form.errors[`sales.${index}.current_price`]" />
                        </div>
                        <div class="w-1/3 md:w-1/4">
                            <InputLabel value="Cantidad" />
                            <el-input v-model.number="sale.quantity" placeholder="No olvides llenar este campo"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/[^\d.]/g, '').replace(/(\..*)\./g, '$1')" required>
                            </el-input>
                            <InputError :message="form.errors[`sales.${index}.quantity`]" />
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
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import SaleDetails from "@/Components/MyComponents/Sale/SaleDetails.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";
import { addOrUpdateBatchOfItems, getAll } from '@/dbService.js';
import axios from 'axios';

export default {
    data() {
        const form = useForm({
            folio: null,
            sales: []
        });

        return {
            form,
            products: [],
            // modales
            showEditModal: false,
            showRefundConfirm: false,
            saleFolioToRefund: null,
            saleFolioToEdit: null,
            // cargas
            refunding: false,
            editing: false,
            // inventario de codigos activado
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        CancelButton,
        InputLabel,
        InputError,
        Back,
        ConfirmationModal,
        DialogModal,
        SaleDetails,
    },
    props: {
        day_sales: Object,
    },
    computed: {
        getGroupedSales() {
            // Obtener las ventas del primer día (si hay múltiples días, se debe ajustar esta parte)
            const sales = Object.values(this.day_sales)[0].sales;

            // Agrupar las ventas por group_id
            const salesGroupedByGroupId = sales.reduce((groupedSales, sale) => {
                // Obtener el group_id de la venta
                const groupId = sale.group_id;

                // Verificar si ya existe un array para este group_id, de lo contrario, crear uno nuevo
                if (!groupedSales[groupId]) {
                    groupedSales[groupId] = [];
                }

                // Agregar la venta al array correspondiente al group_id
                groupedSales[groupId].push(sale);

                return groupedSales;
            }, {});

            // Resultado: objeto donde las claves son los group_id y los valores son arrays de ventas
            return salesGroupedByGroupId;
        }
    },
    methods: {
        formatSalesProductId() {
            // Obtener las ventas del primer día (si hay múltiples días, se debe ajustar esta parte)
            const sales = Object.values(this.day_sales)[0].sales;

            // cambiar el id de cada venta para que coincida con id de productos en indexedDB
            sales.forEach(sale => {
                // revisar si el id es un numero
                if (Number.isFinite(sale.product_id)) {
                    sale.product_id = sale.is_global_product
                        ? 'global_' + sale.product_id
                        : 'local_' + sale.product_id;
                }
            });
        },
        closeEditModal() {
            this.showEditModal = false;
        },
        openEditModal(saleFolio) {
            this.saleFolioToEdit = saleFolio;

            // Preparar form con una copia profunda de las ventas seleccionadas
            this.form.folio = saleFolio;
            this.form.sales = JSON.parse(JSON.stringify(this.getGroupedSales[saleFolio]));

            // Abrir modal
            this.showEditModal = true;
        },
        openRefundModal(saleFolio) {
            this.showRefundConfirm = true;
            this.saleFolioToRefund = saleFolio;
        },
        openInstallmentModal(saleFolio) {
            this.showRefundConfirm = true;
            this.saleFolioToRefund = saleFolio;
        },
        handleShowModal(modal, saleFolio) {
            if (modal == 'edit') this.openEditModal(saleFolio);
            else if (modal == 'refund') this.openRefundModal(saleFolio);
            else if (modal == 'installments') this.openInstallmentModal(saleFolio);
        },
        update() {
            this.editing = true;
            this.form.post(route('sales.update-group-sale'), {
                onSuccess: async () => {
                    // Obtener productos de servidor
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
        formatDate(dateString) {
            const months = {
                'January': 'Enero',
                'February': 'Febrero',
                'March': 'Marzo',
                'April': 'Abril',
                'May': 'Mayo',
                'June': 'Junio',
                'July': 'Julio',
                'August': 'Agosto',
                'September': 'Septiembre',
                'October': 'Octubre',
                'November': 'Noviembre',
                'December': 'Diciembre'
            };

            const [day, month, year] = dateString.split('-');
            return `${day} ${months[month]}, ${year}`;
        },
        async refundSale() {
            this.refunding = true;
            try {
                let response = await axios.post(route('sales.refund', this.saleFolioToRefund));
                if (response.status === 200) {
                    // Obtener productos de servidor
                    response = await axios.get(route('products.get-all-for-indexedDB'));
                    const products = response.data.products;
                    // actualizar lista de productos en indexedDB
                    addOrUpdateBatchOfItems('products', products);

                    this.showRefundConfirm = false;

                    // actualizar elementos de la vista (reactividad)
                    this.getGroupedSales[this.saleFolioToRefund].forEach(element => {
                        element.refunded_at = 1;
                    });

                    this.$notify({
                        title: 'Venta reembolsada / cancelada',
                        message: '',
                        type: 'success',
                    });
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'No se pudo procesar la peticion',
                    message: '',
                    type: 'error',
                });
            } finally {
                this.refunding = false;
            }
        },
    },
    async mounted() {
        this.products = await getAll('products');
        this.formatSalesProductId();
    }
}
</script>