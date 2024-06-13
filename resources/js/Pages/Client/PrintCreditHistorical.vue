<template>

    <Head title="Historial de ventas a credito" />
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
                <div v-show="showAdditionalElements"
                    class="border-2 border-primary px-3 py-2 rounded-[5px] text-center font-bold">
                    Presiona Crl+P para imprimir o<br>
                    guardar en PDF
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
                <p>Saldo total pendiente de pago</p>
                <p class="font-bold">${{ client.debt.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
            </section>
        </header>
        <main>
            <h1 class="font-bold mx-4">Historial de ventas a crédito</h1>
            <table class="w-full table-fixed text-sm mt-4 border">
                <thead>
                    <tr class="*:text-start *:py-2 bg-white">
                        <th class="w-[7%] pl-5">Folio</th>
                        <th class="w-[12%]">Fecha</th>
                        <th class="w-[7%]">Productos</th>
                        <th class="w-[9%]">Total</th>
                        <th class="w-[9%]">Abonado</th>
                        <th class="w-[9%]">Restante</th>
                        <th class="w-[13%] pr-5">Vence el</th>
                    </tr>
                </thead>
                <tbody class="text-xs">
                    <tr v-for="item in getCreditSales" :key="item.folio" class="*:py-1 *:align-top">
                        <td class="pl-5">{{ item.folio }}</td>
                        <td>{{ formatDateTime(item.products[0].created_at) }}</td>
                        <td>{{ item.products.length }}</td>
                        <td>${{ item.total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                        <td>
                            ${{
                                calcTotalInstallmentsAmount(item.credit_data.installments).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                    ",")
                            }}
                        </td>
                        <td>
                            <div class="flex items-center">
                                <span class="w-5 flex items-center">
                                    <svg v-if="item.credit_data.status === 'Pagado'" width="10" height="15"
                                        viewBox="0 0 10 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10 3.64286L7.82609 5.14286C7.06 4.34972 6.61626 3.97109 5.86957 3.64286C4.35509 3.21336 3.88105 3.44276 3.47826 4.28571C3.20547 5.9064 4.29285 6.22922 6.52174 6.64286C9.0781 7.37766 10 8.14286 10 10.2857C10 12.4286 9.07235 13.2794 6.52174 13.9286V15H3.47826V13.9286C1.5648 13.3571 0.836281 12.7995 0 11.5714L2.3913 10.2857C2.7486 10.7626 3.34041 11.0917 4.13043 11.3571C5.95515 11.6448 6.41405 11.3653 6.52174 10.2857C6.48082 9.5207 5.78069 9.22483 4.13043 8.78571C1.20561 7.76731 0.217391 6.85714 0.217391 4.71429C0.217391 2.57143 1.40515 1.79838 3.47826 1.28571V0H6.52174V1.28571C8.20346 1.74645 8.99144 2.18172 10 3.64286Z"
                                            fill="#27BE0E" />
                                    </svg>
                                    <svg v-else-if="item.credit_data.status === 'Parcial'" width="12" height="16"
                                        viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M6 15V11.3636C4.61274 11.1618 3.96205 10.8211 3.3913 10L1 11.3636C1.85248 12.6878 2.63836 13.2776 4.47826 13.8636V15H6Z"
                                            fill="#2D29FF" />
                                        <path
                                            d="M6 4.42857V1H4.40895V2.28571C2.59105 2.28571 1.00995 4 1 5.71429C0.986766 7.9954 2.17154 8.93511 6 10V7.42857C4.54857 6.87628 4.18779 6.43821 4.40895 5.28571C4.78166 4.45888 5.183 4.33398 6 4.42857Z"
                                            fill="#2D29FF" />
                                        <path
                                            d="M11 4.4L8.82609 5.8C8.06 5.05974 7.61626 4.70635 6.86957 4.4C5.35509 3.99913 4.88105 4.21324 4.47826 5C4.20547 6.51264 5.29285 6.81394 7.52174 7.2C10.0781 7.88581 11 8.6 11 10.6C11 12.6 10.0724 13.3941 7.52174 14V15H4.47826V14C2.5648 13.4667 1.83628 12.9462 1 11.8L3.3913 10.6C3.7486 11.0451 4.34041 11.3522 5.13043 11.6C6.95515 11.8684 7.41405 11.6076 7.52174 10.6C7.48082 9.88599 6.78069 9.60984 5.13043 9.2C2.20561 8.24949 1.21739 7.4 1.21739 5.4C1.21739 3.4 2.40515 2.67849 4.47826 2.2V1H7.52174V2.2C9.20346 2.63002 9.99144 3.03627 11 4.4Z"
                                            stroke="#2D29FF" />
                                    </svg>
                                    <svg v-else-if="item.credit_data.status === 'Pendiente'"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4 text-[#EFCE21]">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <svg v-else-if="item.credit_data.status === 'Reembolsado'"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4 text-[#8C3DE4]">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                    </svg>
                                </span>
                                <span>
                                    ${{ calcRemainingDebt(item).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                </span>
                            </div>
                        </td>
                        <td class="pr-5">
                            <div class="flex items-center space-x-1">
                                <span>{{ formatDate(item.credit_data.expired_date) }}</span>
                                <svg v-if="isExpired(item)" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="size-3 text-[#DD0808]">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </main>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import { format, parseISO, isBefore } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    name: 'creditSaleHistorical',
    data() {
        return {
            showAdditionalElements: true,
            // carga
            loading: false,
            sales: [],
        }
    },
    components: {
        Head,
        ApplicationMark,
        Loading,
    },
    props: {
        client: Object,
    },
    computed: {
        getCreditSales() {
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
                            if (sale.credit_data) {
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
        isExpired(sale) {
            const expiredDate = parseISO(sale.credit_data.expired_date);
            const today = new Date();
            return isBefore(expiredDate, today) && sale.credit_data.status !== 'Pagado' && !this.wasRefunded(sale.products);
        },
        calcRemainingDebt(sale) {
            const totalSale = sale.total_sale;
            const totalPaid = this.calcTotalInstallmentsAmount(sale.credit_data.installments);

            return totalSale - totalPaid;
        },
        calcTotalInstallmentsAmount(installments) {
            return installments.reduce((accum, item) => {
                return accum += item.amount;
            }, 0);
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
            window.print();
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
        // this.print();
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