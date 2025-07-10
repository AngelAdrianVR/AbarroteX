<template>
    <article class="border border-grayD9 rounded-md text-xs lg:text-base">
        <header
            class="flex items-center justify-between border-b border-grayD9 text-end px-1 lg:px-5 py-1 text-white bg-gray-200">
            <div class="flex items-center space-x-1 lg:space-x-3">
                <p class="text-gray99">
                    Orden:
                    <a as="button" :href="route('service-reports.show', encodeId(groupedSales.id))"
                        target="_blank" class="text-primary hover:underline">
                        Folio:{{ String(groupedSales.folio).padStart(4, '0') }}
                    </a>
                </p>
                <span class="text-gray99">•</span>
                <p class="text-gray99">Hora de la venta: <span class="text-gray37">{{
                    formatHour(groupedSales.created_at) }}</span></p>
                <span class="text-gray99">•</span>
                <p class="text-gray99">Cliente: <span class="text-gray37">{{ groupedSales.client_name }}</span>
                </p>
                <!-- <span class="text-gray99">•</span> -->
                <!-- <p class="text-gray99">Vendedor: <span class="text-gray37">{{ groupedSales.client_name }}</span>
                </p> -->
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
                            <!-- <el-dropdown-item v-if="canEdit && !isOutOfCashCut && !wasCancelled && !isPaid"
                                :command="'edit|' + groupedSales.folio">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                                <span class="text-xs">Editar</span>
                            </el-dropdown-item>
                            <el-dropdown-item v-if="canRefund && !isOutOfCashCut && !wasCancelled && !isPaid"
                                :command="'refund|' + groupedSales.folio">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                </svg>
                                <span class="text-xs">Reembolso</span>
                            </el-dropdown-item> -->
                            <el-dropdown-item v-if="!wasCancelled" :command="'print|' + groupedSales.folio">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                </svg>

                                <span class="text-xs">Imprimir ticket</span>
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
                    <div class="overflow-x-scroll text-xs lg:text-sm space-y-5">
                        <table class="w-full">
                            <thead>
                                <tr class="*:px-2 text-[#999999]">
                                    <th class="w-[50%] text-start">Servicio realizado</th>
                                    <th class="w-[10%] text-start">Precio</th>
                                    <th class="w-[10%] text-start">Cantidad</th>
                                    <th class="w-[30%] text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-[1px]">
                                <!-- Fila principal del producto -->
                                <tr class="*:px-2 *:py-[6px] *:align-top border-grayD9 text-[#373737]">
                                    <td><p>{{ groupedSales.service_description }}</p></td>
                                    <td>${{ groupedSales.service_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                    <td>1</td>
                                    <td class="text-end pb-1">${{ groupedSales.service_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Refacciones -->
                        <table class="w-full">
                            <thead>
                                <tr class="*:px-2 text-[#999999]">
                                    <th class="w-[50%] text-start">Refacción</th>
                                    <th class="w-[10%] text-start">Precio</th>
                                    <th class="w-[10%] text-start">Cantidad</th>
                                    <th class="w-[30%] text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-[1px]">
                                <tr v-for="(part, index) in groupedSales.spare_parts" :key="index" class="*:px-2 *:py-[6px] *:align-top border-grayD9">
                                    <td><p>{{ part.name }}</p></td>
                                    <td>${{ parseFloat(part.unitPrice).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                    <td>{{ part.quantity }}</td>
                                    <td class="text-end pb-1">${{ (parseFloat(part.unitPrice) * parseFloat(part.quantity))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
            </Accordion>
        </main>
        <footer class="text-end md:flex text-xs lg:text-sm">
            <section class="w-[80%]">
                <div v-if="groupedSales.payment_method === 'Tarjeta'"
                    class="py-1 flex items-center justify-start space-x-2 self-end">
                    <img class="w-5" src="@/../../public/images/card.webp" alt="Pago con tarjeta">
                    <p class="text-[#05394F] font-semibold">Pago con Tarjeta</p>
                </div>
                <div v-else class="py-1 flex items-center justify-start space-x-2 self-end">
                    <img class="w-5" src="@/../../public/images/dollar.webp" alt="Pago en efectivo">
                    <p class="text-[#37672B] font-semibold">Pago en Efectivo</p>
                </div>
            </section>
            <div class="font-black flex flex-col space-y-1 px-1 md:px-7 py-1">
                <div class="flex items-center justify-end text-gray37">
                    <span class="text-start w-40">
                        Subtotal:
                    </span>
                    <span class="w-12">$</span>
                    <span class="w-12">
                        {{ groupedSales.total_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                    </span>
                </div>
                <div class="flex items-center justify-end text-gray37">
                    <span class="text-start w-40">Anticipo recibido:</span>
                    <span class="w-12">-$</span>
                    <span class="w-12">
                        {{ groupedSales.advance_payment?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}
                    </span>
                </div>
                <div class="flex items-center justify-end text-gray37">
                    <span class="text-start w-40">Total restante:</span>
                    <span class="w-12">$</span>
                    <span class="w-12">
                        {{ (groupedSales.total_cost - groupedSales.advance_payment)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                    </span>
                </div>
                <div v-if="wasCancelled" class="flex items-center text-red-600 text-xs md:text-base italic my-1">
                    <svg class="size-[14px] mr-2" width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.97288 4.41695L6.69492 6.69492M4.41695 8.97288L6.69492 6.69492M6.69492 6.69492L4.41695 4.41695M6.69492 6.69492L8.97288 8.97288M12.3898 6.69492C12.3898 9.84013 9.84013 12.3898 6.69492 12.3898C3.5497 12.3898 1 9.84013 1 6.69492C1 3.5497 3.5497 1 6.69492 1C9.84013 1 12.3898 3.5497 12.3898 6.69492Z" stroke="currentColor" stroke-width="1.13898" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Orden cancelada
                </div>
                <section v-if="wasCancelled">
                    <div class="flex items-center justify-end text-gray37">
                        <span class="text-start w-40">Monto de revisión:</span>
                        <span class="w-12">$</span>
                        <span class="w-12">
                            {{ (parseFloat(groupedSales?.aditionals?.review_amount) || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                        </span>
                    </div>
                    <div class="flex items-center justify-end text-gray37">
                        <span class="text-start w-40">Anticipo recibido:</span>
                        <span class="w-12">$</span>
                        <span class="w-12">
                            {{ groupedSales.advance_payment?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}
                        </span>
                    </div>
                    <div v-if="groupedSales.aditionals?.review_amount < groupedSales.advance_payment" class="flex items-center justify-end relative"
                        :class="wasCancelled ? 'text-[#8C3DE4]' : 'text-gray37'">
                        <span class="text-start w-40">Total devuelto:</span>
                        <span class="w-12">$</span>
                        <span class="w-12">
                            {{ (groupedSales.advance_payment - (parseFloat(groupedSales?.aditionals?.review_amount) || 0))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                        </span>

                        <!-- <el-tooltip class="absolute left-0" placement="top"> -->
                            <p class="bg-[#EBEBEB] rounded-[5px] px-2 py-1 mr-2 self-end absolute -left-28">
                                Reembolsado
                            </p>
                        <!-- </el-tooltip> -->
                    </div>
                    <div v-else class="flex items-center justify-end text-gray37">
                        <span class="text-start w-40">Total pagado:</span>
                        <span class="w-12">$</span>
                        <span class="w-12">
                            {{ ((parseFloat(groupedSales?.aditionals?.review_amount) || 0) - groupedSales.advance_payment)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                        </span>
                    </div>
                </section>
                <!-- <div class="flex items-center justify-end"
                    :class="wasCancelled ? 'text-[#8C3DE4]' : 'text-gray37'">
                    <el-tooltip v-if="wasCancelled" placement="top">
                        <template #content>
                            <p>El reembolso se realizó el {{ formatDateHour(groupedSales.products[0].refunded_at) }}
                            </p>
                        </template>
                        <p class="bg-[#EBEBEB] rounded-[5px] px-2 py-1 mr-2 self-end">
                            Reembolsado
                        </p>
                    </el-tooltip>
                </div> -->
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
                !this.isOutOfCashCut && !this.wasCancelled
            );
        },
        wasCancelled() {
            return this.groupedSales.status === 'Cancelada';
        },
    },
    methods: {
        percentageDiscount() {
            return this.groupedSales.quote.percentage * 0.01 * this.subtotal;
        },
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
        print(folio) {
            window.open(route('sales.print-ticket', folio), '_blank');
        },
        handleCommand(command) {
            const modalName = command.split('|')[0];
            const saleFolio = command.split('|')[1];

            if (modalName === 'print') {
                this.print(saleFolio);
            }

            this.$emit('show-modal', modalName, saleFolio);
        },
    },
};
</script>