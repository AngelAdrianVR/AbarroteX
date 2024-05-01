<template>
    <Head title="imprimir ticket" />
    <div class="w-full md:w-[420px] mx-auto text-sm border-2 border-black p-5 my-16">
        <div class="flex items-center space-x-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="size-4 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
            </svg>
            {{ $page.props.auth.user.store.name }}
        </div>
        <p class="text-right mt-2">{{ formatDate(Object.values(day_sales)[0].sales[0]?.created_at) }}</p>


        <!-- tabla de productos -->
        <table class="mt-2 w-full text-xs">
            <tr class="text-left *:font-bold *:pb-2">
                <th>Producto</th>
                <th class="px-[2px]">Cantidad</th>
                <th class="px-[2px]">Total</th>
            </tr>
            <tr v-for="sale in Object.values(day_sales)[0].sales" :key="sale">
                <td class="uppercase px-[2px]">{{ sale.product_name }}</td>
                <td>{{ sale.quantity }}</td>
                <td class=""><span class="px-2">$</span>{{ (sale.quantity * sale.current_price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
            </tr>
        </table>

        <div class="flex justify-end mt-5 mr-11">
            <p class="font-bold text-xs">Total <span class="px-2">$</span> <span>{{ Object.values(day_sales)[0].total_sale?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
        </div>

        <p class="h-2 border-b-2 border-[#D9D9D9] my-5"></p>
        
        <div class="flex justify-between text-[#373737]">
            <p>Método de pago:</p>
            <p>{{ 'Efectivo' }}</p>
        </div>

        <p class="text-center mt-2">{{ $page.props.auth.user.store.address }}</p>

        <div v-if="!printTicket" class="flex justify-center mt-7">
            <PrimaryButton @click="print">Imprimir ticket</PrimaryButton>
        </div>
    </div>


</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head } from '@inertiajs/vue3';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
data() {
    return {
        printTicket: false,
    }
},
components:{
PrimaryButton,
Head
},
props:{
day_sales: Object,
},
methods:{
    formatDate(dateString) {
        return format(parseISO(dateString), 'dd MMMM yyyy, h:mm a', { locale: es });
    },
    print() {
        this.printTicket = true;
        setTimeout(() => {
        window.print();
      }, 80);
    }
}
}
</script>