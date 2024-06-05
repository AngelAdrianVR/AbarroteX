<template>
    <AppLayout :title="'Venta del día'">
        <div class="md:px-10 px-2 py-7 text-xs md:text-sm">
            <div class="flex justify-between items-center">
                <Back />
                <div class="flex items-center space-x-2">
                    <!-- ** descomentar cuando se haga una plantilla para imprimir todas las ventas del día **  -->
                    <!-- <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303"
                        title="¿Continuar?" @confirm="print(Object.values(day_sales)[0].sales[0]?.created_at)">
                        <template #reference>
                            <i @click.stop
                                class="fa-solid fa-print text-primary hover:bg-gray-200 cursor-pointer bg-grayED rounded-full p-[6px]"></i>
                        </template>
</el-popconfirm> -->
                    <!-- <el-popconfirm v-if="canRefund" confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303"
                        title="¿Continuar?" @confirm="deleteItem(Object.values(day_sales)[0].sales[0]?.id)">
                        <template #reference>
                            <i @click.stop
                                class="fa-regular fa-trash-can text-primary cursor-pointer hover:bg-gray-200 rounded-full p-2"></i>
                        </template>
                    </el-popconfirm> -->
                </div>
            </div>

            <!-- Información de la venta -->
            <div class="mt-7 lg:mx-16 text-gray99">
                <p>Total de productos vendidos: <span class="font-thin ml-2 text-gray37">{{
                    Object.values(day_sales)[0].total_quantity }}</span></p>
                <p>Fecha de venta: <span class="font-thin ml-2 text-gray37">{{
                    formatDate(Object.keys(day_sales)[0]) }}</span></p>
                <p>Total de venta: <span class="font-black ml-2 text-gray37">${{
                    Object.values(day_sales)[0].total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                        }}</span></p>
            </div>

            <!-- Productos -->
            <section class="flex flex-col space-y-5 lg:mx-16 mt-10">
                <article v-for="(group, index) in getGroupedSales" :key="index"
                    class="border border-grayD9 *:px-1 *:md:px-5 *:py-1">
                    <div class="flex items-center justify-between border-b border-grayD9 text-end">
                        <div class="flex items-center space-x-3">
                            <p class="text-gray99">Folio: <span class="text-gray37">{{ index }}</span></p>
                            <span class="text-gray99">•</span>
                            <p class="text-gray99">Hora de la venta: <span class="text-gray37">{{
                                formatDateHour(group[0].created_at) }}</span></p>
                            <span class="text-gray99">•</span>
                            <p class="text-gray99">Vendedor: <span class="text-gray37">{{ group[0].user.name }}</span>
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <el-dropdown v-if="canEdit && canRefund && group.some(item => !item?.refunded_at)"
                                trigger="click" @command="handleCommand">
                                <button @click.stop
                                    class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'edit|' + index">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                            </svg>
                                            <span class="text-xs">Editar</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item v-if="canRefund" :command="'refund|' + index">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                            </svg>
                                            <span class="text-xs">Reembolso/Cancelar</span>
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </div>
                    </div>
                    <div class="border-b border-grayD9">
                        <table class="w-full">
                            <thead>
                                <tr class="*:px-2">
                                    <th class="w-[55%] text-start">Producto</th>
                                    <th class="w-[15%] text-start">Precio</th>
                                    <th class="w-[15%] text-start">Cantidad</th>
                                    <th class="w-[15%] text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-[1px]">
                                <tr v-for="(sale, index2) in group" :key="index2"
                                    class="*:px-2 *:py-[6px] *:align-top border-grayD9">
                                    <td>
                                        <button v-if="sale.product_id" @click="viewProduct(sale)"
                                            class="text-start text-primary underline">
                                            {{ sale.product_name }}
                                        </button>
                                        <el-tooltip v-else content="El producto fue eliminado" placement="right">
                                            <span class="text-red-700">{{ sale.product_name }}</span>
                                        </el-tooltip>
                                    </td>
                                    <td>${{ sale.current_price }}</td>
                                    <td>{{ sale.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                    <td class="text-end pb-1">${{ (sale.current_price *
                                        sale.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <!--*** descomentar cuado se guarden los descuentos sobre la venta total ***-->
                        <!-- <div class="text-gray99 flex items-center justify-end space-x-2 *:w-12 px-3">
                            <span>Subtotal:</span>
                            <span class="text-gray37">$</span>
                            <span class="text-gray37">{{ Object.values(day_sales)[0].total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") }}</span>
                        </div>
                        <div class="text-gray99 flex items-center justify-end space-x-2 *:w-12 px-3">
                            <span>Descuento:</span>
                            <span class="text-gray37">$</span>
                            <span class="text-gray37">21.50</span>
                        </div> -->
                        <div :class="group.some(item => item?.refunded_at) ? 'text-[#8C3DE4]' : 'text-gray37'"
                            class="font-black flex items-center justify-end space-x-2 px-2">
                            <el-tooltip v-if="group.some(item => item?.refunded_at)" placement="top">
                                <template #content>
                                    <p>El reembolso de realizó a las {{ formatDateHour(group[0].refunded_at) }}</p>
                                </template>
                                <p class="bg-[#EBEBEB] rounded-[5px] px-2 py-1 mr-2">
                                    Reembolsado</p>
                            </el-tooltip>
                            <span class="text-start w-12">Total:</span>
                            <span class="w-12">$</span>
                            <span class="w-12">{{ calcTotal(group).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                }}</span>
                        </div>
                    </div>
                </article>
            </section>
        </div>
        <DialogModal :show="showEditModal" @close="closeEditModal()">
            <template #title>
                <h1>Editar venta</h1>
            </template>
            <template #content>
                <form @submit.prevent="update">
                    <section v-for="(sale, index) in form.sales" :key="index" class="flex items-center space-x-2 mb-1">
                        <div class="w-1/2">
                            <InputLabel value="Producto" />
                            <el-select v-model="sale.product_id" filterable placeholder="Selecciona el producto"
                                no-data-text="No hay opciones registradas"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="item in products" :key="item.id" :label="item.name"
                                    :value="item.id" />
                            </el-select>
                            <InputError :message="form.errors[`sales.${index}.product_id`]" />
                        </div>
                        <div class="w-1/4">
                            <InputLabel value="Precio por unidad" />
                            <el-input v-model.number="sale.current_price" placeholder="No olvides llenar este campo"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/[^\d.]/g, '').replace(/(\..*)\./g, '$1')" required>
                            </el-input>
                            <InputError :message="form.errors[`sales.${index}.current_price`]" />
                        </div>
                        <div class="w-1/4">
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
                    <!-- <CancelButton @click="showRefundConfirm = false" :disabled="refunding">Cancelar</CancelButton> -->
                    <PrimaryButton @click="refundSale" :disabled="refunding">Continuar</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
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
            // Permisos de rol actual
            canRefund: this.$page.props.auth.user.rol == 'Administrador',
            canEdit: this.$page.props.auth.user.rol == 'Administrador',
            // guardar estado de la vista en casi de ser necesario
            buffer: null,
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        ThirthButton,
        CancelButton,
        InputLabel,
        InputError,
        Back,
        ConfirmationModal,
        DialogModal,
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
        formatDateHour(dateString) {
            return format(parseISO(dateString), 'h:mm a', { locale: es });
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
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const saleFolio = command.split('|')[1];

            if (commandName == 'edit') {
                this.openEditModal(saleFolio);
            } else if (commandName == 'refund') {
                this.showRefundConfirm = true;
                this.saleFolioToRefund = saleFolio;
            }
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
        calcTotal(sales) {
            return sales.reduce((accumulator, currentValue) => {
                const subtotal = currentValue.quantity * currentValue.current_price;
                return accumulator + subtotal;
            }, 0);
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
        viewProduct(product) {
            const productId = product.product_id.split('_')[1];

            if (product.is_global_product) {
                window.open(route('global-product-store.show', productId), '_blank');
            } else {
                window.open(route('products.show', productId), '_blank');
            }
        },
        print(daySales) {
            window.open(route('sales.print-ticket', daySales), '_blank');
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
        async deleteItem(saleId) {
            try {
                const response = await axios.delete(route('sales.destroy', saleId));
                if (response.status == 200) {

                    this.$notify({
                        title: 'Correcto',
                        message: 'Se ha eliminado la venta del día',
                        type: 'success',
                        position: 'bottom-right',
                    });

                    this.$inertia.get(route('sales.index'));
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'Error',
                    message: 'No se pudo eliminar la venta. Inténte más tarde',
                    type: 'error',
                    position: 'bottom-right',
                });
            }
        },
    },
    async mounted() {
        this.products = await getAll('products');
        this.formatSalesProductId();
    }
}
</script>