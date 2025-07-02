<template>
    <article class="border border-grayD9 rounded-md text-xs lg:text-base">
        <header
            class="flex items-center justify-between border-b border-grayD9 text-end px-1 lg:px-5 py-1 text-white bg-gray-200">
            <div class="flex items-center space-x-1 lg:space-x-3">
                <p class="text-gray99">Folio: <span class="text-gray37">{{ groupedSales.folio }}</span></p>
                <span class="text-gray99">•</span>
                <p class="text-gray99">Hora de la venta: <span class="text-gray37">{{
                    formatHour(groupedSales.products[0].created_at) }}</span></p>
                <span class="text-gray99">•</span>
                <p class="text-gray99">Vendedor: <span class="text-gray37">{{ groupedSales.user_name }}</span>
                </p>
                <span class="text-gray99">•</span>
                <p class="text-gray99">Cliente: <span class="text-gray37">{{ groupedSales.client_name }}</span>
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <!-- Se agregó el true para que de siempre verdadero porque se agregó la opción de imprimir ticket -->
                <el-dropdown v-if="hasOptions" trigger="click" @command="handleCommand">
                    <button @click.stop
                        class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item v-if="canEdit && !isOutOfCashCut && !wasRefunded && !isPaid"
                                :command="'edit|' + groupedSales.folio">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                                <span class="text-xs">Editar</span>
                            </el-dropdown-item>
                            <el-dropdown-item v-if="canRefund && !isOutOfCashCut && !wasRefunded && !isPaid"
                                :command="'refund|' + groupedSales.folio">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                </svg>
                                <span class="text-xs">Reembolso</span>
                            </el-dropdown-item>
                            <el-dropdown-item v-if="!wasRefunded" :command="'printing|' + groupedSales.folio">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                </svg>
                                <span class="text-xs">Imprimir ticket</span>
                            </el-dropdown-item>
                            <el-dropdown-item v-if="canInstallment && groupedSales.credit_data"
                                :command="'installment|' + groupedSales.folio">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                                <span class="text-xs">Ver abonos</span>
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </div>
        </header>
        <main class="border-b border-grayD9 px-1 md:px-5 py-1">
            <Accordion :active="false" :id="parseInt(groupedSales.folio)" position="center" title="Ver detalles">
                <template #trigger>
                    <button class="text-primary">
                        Ver detalles
                    </button>
                </template>
                <template #content>
                    <div class="overflow-x-scroll text-xs lg:text-sm">
                        <table class="w-full">
                            <thead>
                                <tr class="*:px-2">
                                    <th class="w-[50%] text-start">Producto</th>
                                    <th class="w-[10%] text-start">Precio</th>
                                    <th class="w-[10%] text-start">Cantidad</th>
                                    <th class="w-[30%] text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-[1px]">
                                <template v-for="(sale, index) in groupedSales.products" :key="index">
                                    <!-- Fila principal del producto -->
                                    <tr class="*:px-2 *:py-[6px] *:align-top border-grayD9">
                                        <td>
                                            <button v-if="sale.product_id" @click="viewProduct(sale)"
                                                class="text-start text-primary underline">
                                                {{ sale.product_name }}
                                            </button>
                                            <el-tooltip v-else content="El producto fue eliminado" placement="right">
                                                <span class="text-red-700">{{ sale.product_name }}</span>
                                            </el-tooltip>
                                        </td>
                                        <td v-if="sale.original_price">
                                            <el-tooltip
                                                :content="'El precio fue modificado de $' + sale.original_price + ' a $' + sale.current_price"
                                                placement="bottom">
                                                <span class="border-b border-dashed border-primary cursor-default">${{
                                                    sale.current_price }}</span>
                                            </el-tooltip>
                                        </td>
                                        <td v-else>
                                            ${{ sale.current_price }}
                                        </td>
                                        <td>{{ sale.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                        <td class="text-end pb-1">
                                            ${{ (sale.current_price *
                                                sale.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                        </td>
                                    </tr>
                                    <!-- Fila de promociones (ocupa todas las columnas) -->
                                    <tr v-if="sale.promotions_applied || (sale.discounted_price != null && !sale.promotions_applied)"
                                        class="*:px-2 *:py-[6px] border-grayD9 italic">
                                        <td colspan="4" class="!p-0">
                                            <div v-if="sale.promotions_applied" class="px-2 py-1 bg-gray-50">
                                                <div v-for="promo in sale.promotions_applied"
                                                    class="flex items-center justify-end gap-4 w-full">
                                                    <span class="flex items-center gap-2 text-[#AE080B] max-w-[70%]">
                                                        <svg width="10" height="16" viewBox="0 0 10 16" fill="none"
                                                            class="shrink-0" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M4.28948 0C6.61872 2.72141 7.63037 4.40864 8.101 8.09863C8.68078 7.68803 8.88761 7.19526 8.93401 5.7168C11.7333 11.4332 8.45922 15.0058 5.54143 15.1846C5.18415 15.2064 4.52916 15.2602 4.11175 15.125C-0.11532 13.7553 -1.60454 10.0634 2.14593 5.24023C4.57877 2.25049 4.74345 1.28434 4.28948 0ZM4.82464 7.44531C2.74274 10.1809 2.08633 12.5065 5.00335 14.0547C6.92271 13.584 7.62449 12.4474 7.80315 10.4824C7.42276 11.0129 7.17113 11.2542 6.49261 11.375C6.6711 9.76745 6.13689 8.64469 4.82464 7.44531Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                        <span class="truncate" :title="promo.description">{{
                                                            promo.description }}</span>
                                                    </span>
                                                    <span class="shrink-0">-${{ promo.discount }}</span>
                                                </div>
                                            </div>
                                            <div v-else-if="sale.discounted_price != null && !sale.promotions_applied"
                                                class="px-2 py-1 bg-gray-50 flex items-center justify-end gap-4">
                                                <span class="flex items-center gap-2 text-[#AE080B]">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                                    </svg>
                                                    <span>Regalo por promoción</span>
                                                </span>
                                                <span class="shrink-0">
                                                    -${{
                                                        calculateTotalDiscount(sale).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                            ",") }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </Accordion>
        </main>
        <footer class="text-end md:flex items-end justify-between text-xs lg:text-sm">
            <div>
                <div v-if="groupedSales.products[0].payment_method === 'Tarjeta'"
                    class="py-1 ml-3 flex items-center justify-start space-x-2 self-end">
                    <img class="w-5" src="@/../../public/images/card.webp" alt="Pago con tarjeta">
                    <p class="text-[#05394F] font-semibold">Pago con Tarjeta</p>
                </div>
                <div v-else class="py-1 flex items-center justify-start space-x-2 self-end ml-3">
                    <img class="w-5" src="@/../../public/images/dollar.webp" alt="Pago en efectivo">
                    <p class="text-[#37672B] font-semibold">Pago en Efectivo</p>
                </div>
                <div v-if="groupedSales.credit_data"
                    class="flex items-center space-x-3 self-end border-0 md:border-t md:border-r rounded-tr-[5px] border-grayD9 pt-2 pb-3 pl-6 pr-9">
                    <span class="text-gray99">Fecha de vencimiento:</span>
                    <p :class="expiredDateClass" class="flex items-center space-x-2">
                        <span v-if="wasRefunded" class="text-gray99">
                            <i class="fa-solid fa-minus"></i>
                        </span>
                        <span v-else>
                            {{ groupedSales.credit_data.expired_date ? formatDate(groupedSales.credit_data.expired_date)
                                : 'No especificada' }}
                        </span>
                        <svg v-if="isExpired" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </p>
                </div>
            </div>
            <div class="font-black flex flex-col space-y-1 px-1 md:px-7 py-1">
                <div class="flex items-center justify-end"
                    :class="wasRefunded && !groupedSales.credit_data ? 'text-[#8C3DE4]' : 'text-gray37'">
                    <el-tooltip v-if="wasRefunded && !groupedSales.credit_data" placement="top">
                        <template #content>
                            <p>El reembolso se realizó el {{ formatDateHour(groupedSales.products[0].refunded_at) }}
                            </p>
                        </template>
                        <p class="bg-[#EBEBEB] rounded-[5px] px-2 py-1 mr-2 self-end">
                            Reembolsado
                        </p>
                    </el-tooltip>
                    <span class="text-start w-32">Total de la venta:</span>
                    <span class="w-12">$</span>
                    <span class="w-12">
                        {{ groupedSales.total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                    </span>
                </div>
                <div v-if="groupedSales.credit_data" class="flex items-center justify-end"
                    :class="wasRefunded ? 'text-[#8C3DE4]' : 'text-gray37'">
                    <el-tooltip v-if="wasRefunded" placement="top">
                        <template #content>
                            <p>El reembolso se realizó el {{ formatDateHour(groupedSales.products[0].refunded_at) }}
                            </p>
                        </template>
                        <p class="bg-[#EBEBEB] rounded-[5px] px-2 py-1 mr-2 self-end">
                            Reembolsado</p>
                    </el-tooltip>
                    <span class="text-start w-32">Total abonado:</span>
                    <span class="w-12">$</span>
                    <span class="w-12">
                        {{ calcTotalInstallments.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                    </span>
                </div>
                <div v-if="groupedSales.credit_data && !wasRefunded" class="flex rounded-[5px] py-1"
                    :class="statusStyles.bg">
                    <span class="w-24 text-start" :class="statusStyles.text">
                        {{ groupedSales.credit_data.status }}</span>
                    <span class="text-start w-32">Deuda restante:</span>
                    <span class="w-12">$</span>
                    <span class="w-12">
                        {{ (groupedSales.total_sale - calcTotalInstallments).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                            ",") }}
                    </span>
                </div>
            </div>
        </footer>
    </article>
</template>

<script>
import Accordion from '@/Components/MyComponents/Accordion.vue';
import { parseISO, isBefore, format } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    name: 'SaleDetails',
    data() {
        return {
            // Permisos de rol actual
            canInstallment: this.$page.props.auth.user.permissions.includes('Registrar abonos'),
            canRefund: this.$page.props.auth.user.permissions.includes('Reembolsar ventas'),
            canEdit: this.$page.props.auth.user.permissions.includes('Editar ventas'),
        };
    },
    components: {
        Accordion,
    },
    props: {
        groupedSales: Object,
        isOutOfCashCut: Boolean //indica si está fuera de corte actual para no mostrar opciones de editar y reembolso
    },
    emits: ['show-modal'],
    computed: {
        hasOptions() {
            return (
                (this.canEdit && !this.isOutOfCashCut && !this.wasRefunded) ||
                (this.canRefund && !this.isOutOfCashCut && !this.wasRefunded) ||
                (this.canInstallment && this.groupedSales.credit_data)
            );
        },
        wasRefunded() {
            return this.groupedSales.products.some(item => item?.refunded_at);
        },
        isExpired() {
            if (!this.groupedSales.credit_data.expired_date) {
                return false;
            }
            const expiredDate = parseISO(this.groupedSales.credit_data.expired_date);
            const today = new Date();
            return isBefore(expiredDate, today) && this.groupedSales.credit_data.status !== 'Pagado' && !this.wasRefunded;
        },
        isPaid() {
            // Verifica si existe la propiedad 'credit_data' y su propiedad 'status'
            if (!this.groupedSales.credit_data || !this.groupedSales.credit_data.status) {
                return false;
            }
            // Retorna true si el status es 'Pagado'
            return this.groupedSales.credit_data.status === 'Pagado';
        },
        expiredDateClass() {
            if (!this.groupedSales.credit_data.expired_date) {
                return 'text-gray37';
            }

            return this.isExpired ? 'text-[#DD0808]' : 'text-gray37';
        },
        statusStyles() {
            const status = this.groupedSales.credit_data?.status;
            if (status === 'Pendiente') {
                return { bg: 'bg-[#FAFFDD]', text: 'text-[#EFCE21]' };
            } else if (status === 'Parcial') {
                return { bg: 'bg-[#F1F2FE]', text: 'text-[#2D29FF]' };
            } else if (status === 'Pagado') {
                return { bg: 'bg-[#E6FDDB]', text: 'text-[#08B91A]' };
            }
            return { bg: 'bg-[#C4FBAA]', text: 'text-[#0AA91A]' };
        },
        calcTotalInstallments() {
            return this.groupedSales.credit_data.installments.reduce((accumulator, currentValue) => {
                return accumulator + currentValue.amount;
            }, 0);
        },
    },
    methods: {
        encodeId(id) {
            const encodedId = btoa(id.toString());
            return encodedId;
        },
        calculateTotalDiscount(sale) {
            const originalTotal = sale.current_price * sale.quantity;
            const discountTotal = sale.discounted_price * sale.quantity;

            return originalTotal - discountTotal;
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM, yyyy', { locale: es });
        },
        formatHour(dateString) {
            return format(parseISO(dateString), 'h:mm a', { locale: es });
        },
        formatDateHour(dateString) {
            return format(parseISO(dateString), 'dd MMMM • h:mm a', { locale: es });
        },
        viewProduct(sale) {
            const productId = sale.product_id.split('_')[1];
            const encodedId = btoa(productId);
            if (sale.is_global_product) {
                window.open(route('global-product-store.show', encodedId), '_blank');
            } else if (this.$page.props.auth.user.store.type == 'Boutique / Tienda de Ropa / Zapatería') {
                window.open(route('boutique-products.show', encodedId), '_blank');
            } else {
                window.open(route('products.show', encodedId), '_blank');
            }
        },
        handleCommand(command) {
            const modalName = command.split('|')[0];
            const saleFolio = command.split('|')[1];
            this.$emit('show-modal', modalName, saleFolio);
        },
    },
};
</script>