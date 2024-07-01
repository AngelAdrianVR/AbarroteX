<template>

    <Head title="Historial de ventas al contado" />
    <Loading v-if="loading" />
    <div v-else class="relative">
        <svg class="absolute top-0 -right-10 -z-10" width="370" height="230" viewBox="0 0 370 285" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M79 285C394.244 117.201 373.151 55.8632 0.5 0.5H224C495.775 68.6854 460.378 131.277 79 285Z"
                fill="#F2F2F2" />
        </svg>
        <svg class="absolute top-32 -left-16 -z-10" width="393" height="342" viewBox="0 0 393 342" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path
                d="M392.5 33.4979C130.131 15.0786 45.3213 83.8944 0.99956 341.998V148C-0.166192 -7.29008 88.7681 -30.032 392.5 33.4979Z"
                fill="#F2F2F2" />
        </svg>
        <header class="mx-10 mb-3">
            <section class="flex items-center justify-between text-gray37 text-sm">
                <ApplicationMark class="block h-11 w-auto" />
                <div v-show="showAdditionalElements" @click="print">
                    <PrimaryButton>
                        Imprimir o guardar PDF 
                    </PrimaryButton> 
                </div>
                <p class="flex items-center space-x-2 self-end">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>
                    <span>{{ $page.props.auth.user.store.name }}</span>
                </p>
            </section>
            <section class="flex items-start justify-between mt-3 text-sm">
                <div class="grid grid-cols-3 gap-x-3">
                    <p class="font-bold">Cliente</p>
                    <p class="col-span-2">{{ client.name }}</p>
                    <p class="font-bold">Teléfono</p>
                    <p class="col-span-2">{{ client.phone }}</p>
                    <p class="font-bold">Dirección</p>
                    <p class="col-span-2">
                        {{ client.street ? client.street + ' ' + client.ext_number + ', Col. ' + client.suburb + ' ' +
                            client.int_number + '. ' + client.town + ', ' + client.polity_state : '-' }}
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-x-3 gap-y-1">
                    <p class="font-bold">Fecha</p>
                    <p class="col-span-2">{{ formatDate(new Date().toISOString()) }}</p>
                </div>
            </section>
            <section class="flex flex-col items-center justify-center space-y-1 mt-3 text-sm">
                <p>Total en ventas</p>
                <p class="font-bold">
                    ${{ getTotalSalesAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                </p>
            </section>
        </header>
        <main>
            <h1 class="font-bold mx-4">Historial de ventas al contado</h1>
            <table class="w-full table-auto text-sm mt-4 border">
                <thead>
                    <tr class="*:text-start *:py-2 bg-white">
                        <th class="pl-5">Folio</th>
                        <th>Fecha</th>
                        <th>Productos</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody class="text-xs">
                    <tr v-for="item in getCashSales" :key="item.folio" class="*:py-1 *:align-top">
                        <td class="pl-5">{{ item.folio }}</td>
                        <td>{{ formatDateTime(item.products[0].created_at) }}</td>
                        <td>{{ item.products.length }}</td>
                        <td>${{ item.total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                    </tr>
                </tbody>
            </table>
        </main>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    name: 'cashSaleHistorical',
    data() {
        return {
            showAdditionalElements: true,
            // carga
            loading: false,
            sales: [],
        }
    },
    components: {
        ApplicationMark,
        PrimaryButton,
        Loading,
        Head,
    },
    props: {
        client: Object,
    },
    computed: {
        getTotalSalesAmount() {
            return this.getCashSales.reduce((accum, item) => {
                return accum += item.total_sale;
            }, 0);
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
        wasRefunded(products) {
            return products.some(item => item?.refunded_at);
        },
        formatDateTime(dateTimeString) {
            return format(parseISO(dateTimeString), 'dd MMM yy, hh:mm a', { locale: es });
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMM yyyy', { locale: es });
        },
        handleBeforePrint() {
            this.showAdditionalElements = false;
        },
        handleAfterPrint() {
            this.showAdditionalElements = true;
        },
        print() {
            this.showAdditionalElements = false;
            setTimeout(() => {
                window.print();
            }, 100);
        },
        async fetchSales(loading = true) {
            this.loading = loading;
            try {
                const response = await axios.get(route('clients.get-client-sales', this.client.id));

                if (response.status === 200) {
                    this.sales = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
    },
    async mounted() {
        await this.fetchSales();
        window.addEventListener('beforeprint', this.handleBeforePrint);
        window.addEventListener('afterprint', this.handleAfterPrint);
    },
    beforeDestroy() {
        window.removeEventListener('beforeprint', this.handleBeforePrint);
        window.removeEventListener('afterprint', this.handleAfterPrint);
    },
};
</script>
<style>
tbody tr:nth-child(odd) {
    background-color: #f2f2f2;
}
</style>