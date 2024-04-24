<template>
    <AppLayout title="Detalles del corte">
        <div class="px-3 lg:px-14 py-5">
            <h1 class="lg:ml-10">Detalles del corte</h1>

            <!-- back -->
            <div class="my-4">
                <Back />
            </div>

            <section class="lg:flex lg:space-x-7 md:w-[90%] mx-auto text-sm">
                <div class="w-full border border-grayD9 rounded-lg self-start">
                    <div class="p-4 flex items-center space-x-2">
                        <div class="w-3/4 space-y-1">
                            <p class="font-bold mb-3">Recuento manual de efectivo</p>
                            <p class="text-gray99">Efectivo inicial</p>
                            <p class="text-gray99">Ventas</p>
                            <p v-for="cashRegisterMovement in cash_cut.cash_register?.movements" :key="cashRegisterMovement" class="text-gray99 truncate">
                                {{ cashRegisterMovement.type + 'de efectivo. Motivo: ' + cashRegisterMovement.notes + ' ' + formatDateHour(cashRegisterMovement.created_at) }}
                            </p>
                        </div>
                        <div class="w-1/4 space-y-1">
                            <p class="font-bold mb-3 pl-4"><span class="mr-3">$</span>{{ cash_cut.counted_cash?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                            <p class="text-gray99"><span class="text-gray99 mr-3"><i class="fa-solid fa-plus text-xs px-1"></i>$</span>{{ cash_cut.started_cash?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                            <p class="text-gray99"><span class="text-gray99 mr-3"><i class="fa-solid fa-plus text-xs px-1"></i>$</span>{{ cash_cut.sales_cash?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                            <p v-for="cashRegisterMovement in cash_cut.cash_register?.movements" :key="cashRegisterMovement" class="text-gray99">
                                <i :class="cashRegisterMovement.type === 'Ingreso' ? 'fa-plus' : 'fa-minus' " class="fa-solid text-xs px-1"></i>
                                <span class="text-gray99 mr-3">$</span>{{ cashRegisterMovement.amount?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}
                            </p>
                        </div>
                    </div>
                    <footer class="bg-[#F2F2F2] text-gray99 py-2 flex px-2">
                        <p class="w-3/4 text-right pr-7">Total</p>
                        <p class="w-1/4 text-gray99 pl-4"><span class="text-gray99 mr-3">$</span>{{ cash_cut.expected_cash?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                    </footer>
                </div>

                <div class="mt-7 lg:mt-0 mx-auto lg:mx-0 w-96 border border-grayD9 rounded-lg">
                    <h2 class="py-2 bg-[#F2F2F2] text-center text-sm font-bold">Corte {{ formatDate(cash_cut.created_at) }}</h2>
                    <div class="flex justify-between space-x-1 p-5">
                        <div class="font-semibold space-y-1">
                            <p class="mb-5">Hora de corte</p>
                            <p>Efectivo al iniciar</p>
                            <p>Esperado</p>
                            <p>Recuento manual</p>
                            <p class="pb-5">Diferencia</p>
                            <p>Retiro</p>
                            <p>Restante en caja</p>
                        </div>
                        <div class="space-y-1 font-semibold">
                            <p class="mb-5 text-gray99">{{ formatDateHour(cash_cut.created_at) }}</p>
                            <p><span class="text-gray99 pr-3">$</span>{{ cash_cut.started_cash?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                            <p><span class="text-gray99 pr-3">$</span>{{ cash_cut.expected_cash?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                            <p><span class="text-gray99 pr-3">$</span>{{ cash_cut.counted_cash?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                            <p class="pb-5" :class="differenceStyles()"><span class="pr-3">$</span>{{ (cash_cut.counted_cash - cash_cut.expected_cash)?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                            <p><span class="text-gray99 pr-3">$</span>{{ cash_cut.withdrawn_cash?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                            <p><span class="text-gray99 pr-3">$</span>{{ (cash_cut.counted_cash - cash_cut.withdrawn_cash)?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                        </div>
                    </div>
                    <!-- mensaje de diferencia de efectivo -->
                    <p :class="{
                        'text-green-500 bg-green-100': (cash_cut.expected_cash - cash_cut.counted_cash) === 0,
                        'text-blue-500 bg-blue-100': (cash_cut.expected_cash - cash_cut.counted_cash) < 0,
                        'text-red-500 bg-red-100': (cash_cut.expected_cash - cash_cut.counted_cash) > 0
                    }" class="rounded-b-lg text-xs py-[2px] px-2 text-center">
                        <i v-if="(cash_cut.expected_cash - cash_cut.counted_cash) === 0" class="fa-solid fa-check mr-1"></i>
                        <i v-else-if="(cash_cut.expected_cash - cash_cut.counted_cash) < 0" class="fa-solid fa-plus mr-1"></i>
                        <i v-else class="fa-solid fa-xmark mr-1"></i>
                        {{ (cash_cut.expected_cash - cash_cut.counted_cash) === 0 ? 'Todo bien' : ((cash_cut.expected_cash - cash_cut.counted_cash) < 0 ? 'Sobrante en caja' : 'Faltante de efectivo') }}
                    </p>
                </div>
            </section>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Back from "@/Components/MyComponents/Back.vue";
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
data() {
    return {

    }
},
components:{
AppLayout,
PrimaryButton,
Back
},
props:{
cash_cut: Array
},
methods:{
    formatDateHour(dateString) {
        return format(parseISO(dateString), 'h:mm a', { locale: es });
    },
    formatDate(dateString) {
        return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
    },
    differenceStyles() {
        if ( this.cash_cut.counted_cash - this.cash_cut.expected_cash < 0 ) {
            return 'text-red-600';
        } else if ( this.cash_cut.counted_cash - this.cash_cut.expected_cash === 0 ) {
            return 'text-green-500';
        } else if ( this.cash_cut.counted_cash - this.cash_cut.expected_cash > 0 ) {
            return 'text-blue-600';
        }
    }
}
}
</script>
