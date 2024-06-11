<template>
    <article class="border border-grayD9">
        <header class="flex items-center justify-between border-b border-grayD9 text-end px-1 md:px-5 py-1">
            <div class="flex items-center space-x-3">
                <p class="text-gray99">Folio: <span class="text-gray37">{{ groupedSales.folio }}</span></p>
                <span class="text-gray99">•</span>
                <p class="text-gray99">Hora de la venta: <span class="text-gray37">{{
                    formatDateHour(groupedSales.products[0].created_at) }}</span></p>
                <span class="text-gray99">•</span>
                <p class="text-gray99">Vendedor: <span class="text-gray37">{{ groupedSales.user_name }}</span>
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <el-dropdown v-if="(canEdit || canRefund || canInstallment) && (!isOutOfCashCut || groupedSales.credit_data)"
                    trigger="click" @command="handleCommand">
                    <button @click.stop
                        class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item v-if="canEdit  && !isOutOfCashCut" :command="'edit|' + groupedSales.folio">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                                <span class="text-xs">Editar</span>
                            </el-dropdown-item>
                            <el-dropdown-item v-if="canRefund && !isOutOfCashCut" :command="'refund|' + groupedSales.folio">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                </svg>
                                <span class="text-xs">Reembolso/Cancelar</span>
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
                            <tr v-for="(sale, index) in groupedSales.products" :key="index"
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
        </main>
        <footer class="text-end md:flex" :class="groupedSales.credit_data ? 'justify-between' : 'justify-end'">
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
            <div class="font-black flex flex-col space-y-1 px-1 md:px-7 py-1">
                <div class="flex items-center justify-end"
                    :class="wasRefunded && !groupedSales.credit_data ? 'text-[#8C3DE4]' : 'text-gray37'">
                    <el-tooltip v-if="wasRefunded && !groupedSales.credit_data" placement="top">
                        <template #content>
                            <p>El reembolso de realizó a las {{ formatDateHour(groupedSales.products[0].refunded_at) }}
                            </p>
                        </template>
                        <p class="bg-[#EBEBEB] rounded-[5px] px-2 py-1 mr-2 self-end">
                            Reembolsado</p>
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
                            <p>El reembolso de realizó a las {{ formatDateHour(groupedSales.products[0].refunded_at) }}
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
                <div v-if="groupedSales.credit_data && !wasRefunded" class="flex rounded-[5px] py-1" :class="statusStyles.bg">
                    <span class="w-24 text-start" :class="statusStyles.text">
                        {{ groupedSales.credit_data.status }}</span>
                    <span class="text-start w-32">Deuda restante:</span>
                    <span class="w-12">$</span>
                    <span class="w-12">
                        {{ (groupedSales.total_sale - calcTotalInstallments).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
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
            canInstallment: this.$page.props.auth.user.rol == 'Administrador',
            canRefund: this.$page.props.auth.user.rol == 'Administrador',
            canEdit: this.$page.props.auth.user.rol == 'Administrador',
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
        expiredDateClass() {
            if (!this.groupedSales.credit_data.expired_date) {
                return 'text-gray37';
            }

            return this.isExpired ? 'text-[#DD0808]' : 'text-gray37';
        },
        statusStyles() {
            const status = this.groupedSales.credit_data?.status;
            if (status === 'Pendiente') {
                return { bg: 'bg-[#F2FEA8]', text: 'text-[#794A04]' };
            } else if (status === 'Parcial') {
                return { bg: 'bg-[#DADEFD]', text: 'text-[#080592]' };
            } else if (status === 'Pagado') {
                return { bg: 'bg-[#C4FBAA]', text: 'text-[#0AA91A]' };
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
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM, yyyy', { locale: es });
        },
        formatDateHour(dateString) {
            return format(parseISO(dateString), 'h:mm a', { locale: es });
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