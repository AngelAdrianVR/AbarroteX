<template>
    <article class="border border-grayD9 rounded-md">
        <div class="flex items-center space-x-3 border-b border-grayD9 text-end px-1 md:px-5 py-2 text-white bg-gray-200">
            <p class="text-gray99">
                Pedido el:
                <span class="text-gray37">{{ formatDateHour(onlineSale.created_at) }}</span>
            </p>
            <span class="text-gray99">•</span>
            <p class="text-gray99">
                Entregado el:
                <span class="text-gray37">
                    {{ onlineSale.delivered_at ? formatDateHour(onlineSale.delivered_at) : '--' }}
                </span>
            </p>
            <span class="text-gray99">•</span>
            <p class="text-gray99">
                Status:
                <span class="text-gray37">
                    {{ onlineSale.status }}
                </span>
            </p>
        </div>
        <header class="flex items-center justify-between border-b border-grayD9 text-end px-1 md:px-5 py-1">
            <div class="flex items-center space-x-3">
                <p class="text-gray99">Folio: <span class="text-gray37">{{ 'L-' + onlineSale.id }}</span></p>
                <span class="text-gray99">•</span>
                <p class="text-gray99">Cliente: <span class="text-gray37">{{ onlineSale.name }}</span></p>
                <span class="text-gray99">•</span>
                <p class="text-gray99">Teléfono: <span class="text-gray37">{{ onlineSale.phone }}</span>
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <el-dropdown v-if="hasOptions" trigger="click" @command="handleCommand">
                    <button @click.stop
                        class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item :command="'see|' + onlineSale.id">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span class="text-xs">Ver detalles</span>
                            </el-dropdown-item>
                        </el-dropdown-menu>
                        <el-dropdown-item v-if="canRefund && !wasRefunded && !wasCanceled && saleDelivered" :command="'refund|' + onlineSale.id">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-[14px] mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                            <span class="text-xs">Reembolsar</span>
                        </el-dropdown-item>
                        <el-dropdown-item v-if="canCancel && !wasCanceled && !wasRefunded" :command="'cancel|' + onlineSale.id">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-[14px] mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                            <span class="text-xs">Cancelar</span>
                        </el-dropdown-item>
                    </template>
                </el-dropdown>
            </div>
        </header>
        <main class="border-b border-grayD9 px-1 md:px-5 py-1">
            <Accordion :active="false" :id="parseInt(onlineSale.id)" position="center" title="Ver detalles">
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
                            <tr v-for="(product, index) in onlineSale.products" :key="index"
                                class="*:px-2 *:py-[6px] *:align-top border-grayD9">
                                <td>
                                    <button v-if="product.product_id" @click="viewProduct(product)"
                                        class="text-start text-primary underline">
                                        {{ product.name }}
                                    </button>
                                    <el-tooltip v-else content="El producto fue eliminado" placement="right">
                                        <!-- product_name es para ventas normales y name para ventas en linea -->
                                        <span class="text-red-700">{{ product.product_name ?? product.name }}</span>
                                    </el-tooltip>
                                </td>
                                <td>${{ product.price }}</td>
                                <td>{{ product.quantity?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                <td class="text-end pb-1">${{ (product.price *
                                    product.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                            </tr>
                        </tbody>
                    </table>
                </template>
            </Accordion>
        </main>
        <footer class="text-end md:flex justify-end">
            <div class="font-black flex flex-col space-y-1 px-1 md:px-7 py-1">
                <div class="flex items-center justify-end" :class="wasRefunded || wasCanceled ? 'text-[#8C3DE4]' : 'text-gray37'">
                    <el-tooltip v-if="wasRefunded" placement="top">
                        <template #content>
                            <p>
                                El reembolso se realizó a las {{ formatDateHour(onlineSale.refunded_at) }}
                            </p>
                        </template>
                        <p class="bg-[#EBEBEB] rounded-[5px] px-2 py-1 mr-2 self-end">
                            Reembolsado</p>
                    </el-tooltip>
                    <el-tooltip v-else-if="wasCanceled" placement="top">
                        <template #content>
                            <p>
                                Se canceló a las {{ formatDateHour(onlineSale.refunded_at) }}
                            </p>
                        </template>
                        <p class="bg-[#EBEBEB] rounded-[5px] px-2 py-1 mr-2 self-end">
                            Cancelado</p>
                    </el-tooltip>
                    <span class="text-start w-32">Total de la venta:</span>
                    <span class="w-12">$</span>
                    <span class="w-12">
                        {{ onlineSale.total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                    </span>
                </div>
            </div>
        </footer>
    </article>
</template>

<script>
import Accordion from '@/Components/MyComponents/Accordion.vue';
import { parseISO, format } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    name: 'OnlineSaleDetails',
    data() {
        return {
            // Permisos de rol actual
            canSeeDetails: this.$page.props.auth.user.permissions.includes('Ventas registradas'),
            canRefund: this.$page.props.auth.user.permissions.includes('Reembolsar ventas'),
            canCancel: this.$page.props.auth.user.permissions.includes('Cancelar ventas en linea'),
        };
    },
    components: {
        Accordion,
    },
    props: {
        onlineSale: Object,
    },
    emits: ['show-modal'],
    computed: {
        hasOptions() {
            return this.canSeeDetails;
        },
        wasRefunded() {
            return this.onlineSale.refunded_at && this.onlineSale.status === 'Reembolsado';
        },
        wasCanceled() {
            return this.onlineSale.refunded_at && this.onlineSale.status === 'Cancelado';
        },
        saleDelivered() {
            return this.onlineSale.status === 'Entregado';
        },
    },
    methods: {
        markAsRefunded() {
            this.onlineSale.refunded_at = new Date().toISOString();
            this.onlineSale.status = 'Reembolsado';
        },
        markAsCanceled() {
            this.onlineSale.refunded_at = new Date().toISOString();
            this.onlineSale.status = 'Cancelado';
        },
        formatDateHour(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy, h:mm a', { locale: es });
        },
        viewProduct(product) {
            const productId = product.product_id;

            if (product.isLocal) {
                window.open(route('products.show', productId), '_blank');
            } else {
                window.open(route('global-product-store.show', productId), '_blank');
            }
        },
        print(daySales) {
            window.open(route('sales.print-ticket', daySales), '_blank');
        },
        handleCommand(command) {
            const name = command.split('|')[0];
            const saleId = command.split('|')[1];

            if (name === 'see') {
                this.$inertia.visit(route('online-sales.show', saleId));
            } else if (name === 'refund') {
                this.$emit('show-modal', name, saleId);
            } else if (name === 'cancel') {
                this.$emit('show-modal', name, saleId);
            }
        },
    },
};
</script>