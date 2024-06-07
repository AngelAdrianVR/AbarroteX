<template>
    <article class="border border-grayD9 *:px-1 *:md:px-5 *:py-1">
        <div class="flex items-center justify-between border-b border-grayD9 text-end">
            <div class="flex items-center space-x-3">
                <p class="text-gray99">Folio: <span class="text-gray37">{{ folio }}</span></p>
                <span class="text-gray99">•</span>
                <p class="text-gray99">Hora de la venta: <span class="text-gray37">{{
                    formatDateHour(groupedSales[0].created_at) }}</span></p>
                <span class="text-gray99">•</span>
                <p class="text-gray99">Vendedor: <span class="text-gray37">{{ groupedSales[0].user.name }}</span>
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <el-dropdown v-if="canEdit && canRefund && groupedSales.some(item => !item?.refunded_at)"
                    trigger="click" @command="handleCommand">
                    <button @click.stop
                        class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item :command="'edit|' + folio">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                                <span class="text-xs">Editar</span>
                            </el-dropdown-item>
                            <el-dropdown-item v-if="canRefund" :command="'refund|' + folio">
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
            <Accordion :active="false" :id="parseInt(folio)" position="center" title="Ver detalles">
                <template #trigger>
                    <button class="text-primary">
                        Ver detalles
                    </button>
                </template>
                <template #content>
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
                            <tr v-for="(sale, index) in groupedSales" :key="index"
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
                </template>
            </Accordion>
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
            <div :class="groupedSales.some(item => item?.refunded_at) ? 'text-[#8C3DE4]' : 'text-gray37'"
                class="font-black flex items-center justify-end space-x-2 px-2">
                <el-tooltip v-if="groupedSales.some(item => item?.refunded_at)" placement="top">
                    <template #content>
                        <p>El reembolso de realizó a las {{ formatDateHour(groupedSales[0].refunded_at) }}</p>
                    </template>
                    <p class="bg-[#EBEBEB] rounded-[5px] px-2 py-1 mr-2">
                        Reembolsado</p>
                </el-tooltip>
                <span class="text-start w-12">Total:</span>
                <span class="w-12">$</span>
                <span class="w-12">{{ calcTotal(groupedSales).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                    }}</span>
            </div>
        </div>
    </article>
</template>

<script>
import Accordion from '@/Components/MyComponents/Accordion.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    name: 'SaleDetails',
    data() {
        return {
            // Permisos de rol actual
            canRefund: this.$page.props.auth.user.rol == 'Administrador',
            canEdit: this.$page.props.auth.user.rol == 'Administrador',
        };
    },
    components: {
        Accordion,
    },
    props: {
        folio: String,
        groupedSales: Array,
        installments: {
            default: [],
            type: Array
        }
    },
    emits: ['show-modal'],
    methods: {
        formatDateHour(dateString) {
            return format(parseISO(dateString), 'h:mm a', { locale: es });
        },
        calcTotal(sales) {
            return sales.reduce((accumulator, currentValue) => {
                const subtotal = currentValue.quantity * currentValue.current_price;
                return accumulator + subtotal;
            }, 0);
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
        handleCommand(command) {
            const modalName = command.split('|')[0];
            const saleFolio = command.split('|')[1];

            this.$emit('show-modal', modalName, saleFolio);
        },
    },
};
</script>