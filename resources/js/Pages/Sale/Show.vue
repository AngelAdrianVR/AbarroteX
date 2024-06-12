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
                    @show-modal="handleShowModal" :isOutOfCashCut="is_out_of_cash_cut" />
            </main>
        </div>

        <DialogModal :show="showInstallmentModal" @close="closeInstallmentModal()">
            <template #title>
                <h1>Abonos</h1>
            </template>
            <template #content>
                <section class="border-t pt-1 border-grayD9">
                    <div class="flex items-center justify-between">
                        <span>Resumen de venta</span>
                        <span :class="statusStyles" class="rounded-full px-4 py-[2px]">{{
                            saleToSeeInstallments.credit_data.status }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 pb-2 border-b border-grayD9">
                        <p class="text-gray99">Folio de venta: <span class="text-gray37">{{ saleToSeeInstallments.folio
                                }}</span></p>
                        <span class="text-grayD9">|</span>
                        <p class="text-gray99">Total de venta: <span class="text-gray37">
                                ${{ saleToSeeInstallments.total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span></p>
                        <span class="text-grayD9">|</span>
                        <p class="text-gray99">Fecha de vencimiento: <span class="text-gray37">
                                {{ formatDate(saleToSeeInstallments.credit_data.expired_date) }}
                            </span></p>
                    </div>
                    <table class="w-full mt-4 *:text-sm">
                        <thead>
                            <tr class="*:text-start">
                                <th class="w-[13%]">Folio</th>
                                <th class="w-[29%]">Fecha</th>
                                <th class="w-[29%]">Monto abonado</th>
                                <th class="w-[29%]">Deuda restante</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-[1px]">
                            <tr v-for="(installment, index) in saleToSeeInstallments.credit_data.installments"
                                :key="index" class="*:py-[6px] *:align-top border-grayD9">
                                <td>{{ 'A' + String(installment.id).padStart(3, '0') }}</td>
                                <td>{{ formatDateTime(installment.created_at) }}</td>
                                <td>${{ installment.amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                <td>${{ calcRemainingDebt(index).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                </td>
                            </tr>
                            <tr v-if="addInstallment">
                                <td>-</td>
                                <td>{{ formatDateTime(new Date().toISOString()) }}</td>
                                <td>
                                    <el-input v-model="installmentForm.amount" required type="text" class="!w-11/12"
                                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                        :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="0.00">
                                        <template #prefix>
                                            <i class="fa-solid fa-dollar-sign"></i>
                                        </template>
                                    </el-input>
                                </td>
                                <td>
                                    ${{
                                        calcRemainingDebtWithNewInstallment().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                            ",") }}
                                </td>
                                <td>
                                    <div
                                        class="flex items-center space-x-1 *:size-5 *:flex *:items-center *:justify-center *:rounded-full *:text-[10px]">
                                        <button @click="storeInstallment" type="button"
                                            class="bg-primary text-white disabled:cursor-not-allowed disabled:bg-gray99"
                                            :disabled="addingInstallment || !installmentForm.isDirty">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                        <button @click="addInstallment = false" type="button"
                                            class="bg-grayF2 text-gray37" :disabled="addingInstallment">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else-if="saleToSeeInstallments.credit_data.status !== 'Pagado'">
                                <td colspan="4">
                                    <button @click="addInstallment = true" type="button" class="text-primary mt-2">
                                        + Agregar abono
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="closeInstallmentModal()" :disabled="addingInstallment">Cancelar</CancelButton>
                </div>
            </template>
        </DialogModal>
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
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SaleDetails from "@/Components/MyComponents/Sale/SaleDetails.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";
import { addOrUpdateBatchOfItems, getAll } from '@/dbService.js';
import axios from 'axios';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    data() {
        const form = useForm({
            folio: null,
            products: []
        });

        const installmentForm = useForm({
            credit_sale_data_id: null,
            amount: '',
        });

        return {
            form,
            installmentForm,
            products: [],
            // modales
            showEditModal: false,
            showInstallmentModal: false,
            showRefundConfirm: false,
            saleToSeeInstallments: null,
            saleFolioToRefund: null,
            addInstallment: false,
            // cargas
            addingInstallment: false,
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
        is_out_of_cash_cut: Boolean //indica si está fuera de corte actual para no mostrar opciones de editar y reembolso
    },
    computed: {
        getGroupedSales() {
            // Obtener las ventas del primer día (si hay múltiples días, se debe ajustar esta parte)
            const sales = Object.values(this.day_sales)[0].sales;

            return Object.values(sales);
        },
        statusStyles() {
            const status = this.saleToSeeInstallments.credit_data.status;
            if (status === 'Pendiente') {
                return 'bg-[#F2FEA8] text-[#794A04]';
            } else if (status === 'Parcial') {
                return 'bg-[#DADEFD] text-[#080592]';
            } else if (status === 'Pagado') {
                return 'bg-[#C4FBAA] text-[#0AA91A]';
            }
            return 'bg-[#C4FBAA] text-[#0AA91A]';
        },
    },
    methods: {
        calcRemainingDebtWithNewInstallment() {
            const totalSale = this.saleToSeeInstallments.total_sale;
            const installments = this.saleToSeeInstallments.credit_data.installments;

            let totalPaid = 0;
            installments.forEach(installment => {
                totalPaid += installment.amount;
            });

            const newInstallmentAmount = this.installmentForm.amount ? parseFloat(this.installmentForm.amount) : 0;
            return totalSale - totalPaid - newInstallmentAmount;
        },
        calcRemainingDebt(index) {
            const totalSale = this.saleToSeeInstallments.total_sale;
            const installments = this.saleToSeeInstallments.credit_data.installments;

            let totalPaid = 0;
            for (let i = 0; i <= index; i++) {
                totalPaid += installments[i].amount;
            }

            return totalSale - totalPaid;
        },
        formatSalesProductId() {
            // Obtener las ventas del primer día (si hay múltiples días, se debe ajustar esta parte)
            const sales = Object.values(this.day_sales)[0].sales;

            // cambiar el id de cada venta para que coincida con id de productos en indexedDB
            Object.values(sales).forEach(sale => {
                sale.products.forEach(product => {
                    // revisar si el id es un numero
                    if (Number.isFinite(product.product_id)) {
                        product.product_id = product.is_global_product
                            ? 'global_' + product.product_id
                            : 'local_' + product.product_id;
                    }
                })
            });
        },
        closeInstallmentModal() {
            this.showInstallmentModal = false;
        },
        closeEditModal() {
            this.showEditModal = false;
        },
        openEditModal(saleFolio) {
            // Preparar form con una copia profunda de las ventas seleccionadas
            let sale = this.getGroupedSales.find(item => item.folio == saleFolio);
            this.form.folio = sale.folio;
            this.form.products = JSON.parse(JSON.stringify(sale.products));

            // Abrir modal
            this.showEditModal = true;
        },
        openRefundModal(saleFolio) {
            this.showRefundConfirm = true;
            this.saleFolioToRefund = saleFolio;
        },
        openInstallmentModal(saleFolio) {
            this.showInstallmentModal = true;
            let sale = this.getGroupedSales.find(item => item.folio == saleFolio);
            this.saleToSeeInstallments = sale;
        },
        handleShowModal(modal, saleFolio) {
            if (modal == 'edit') this.openEditModal(saleFolio);
            else if (modal == 'refund') this.openRefundModal(saleFolio);
            else if (modal == 'installment') this.openInstallmentModal(saleFolio);
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
        formatDateTime(dateTimeString) {
            return format(parseISO(dateTimeString), 'dd MMM yy, hh:mm a', { locale: es });
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM, yyyy', { locale: es });
        },
        storeInstallment() {
            this.addingInstallment = true;
            this.installmentForm.transform((data) => ({
                ...data,
                credit_sale_data_id: this.saleToSeeInstallments.credit_data.id,
            })).post(route('installments.store'), {
                onSuccess: () => {
                    this.addInstallment = false;
                    this.installmentForm.reset();
                    this.saleToSeeInstallments = this.getGroupedSales.find(item => item.folio == this.saleToSeeInstallments.folio);
                },
                onFinish: () => {
                    this.addingInstallment = false;
                    this.installmentForm.reset();
                }
            });
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
                    let sale = this.getGroupedSales.find(item => item.folio == this.saleFolioToRefund);
                    sale.products.forEach(element => {
                        element.refunded_at = new Date().toISOString();
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
    watch: {
        'installmentForm.amount': function () {
            this.calcRemainingDebtWithNewInstallment();
        }
    },
    async mounted() {
        this.products = await getAll('products');
        this.formatSalesProductId();
    }
}
</script>