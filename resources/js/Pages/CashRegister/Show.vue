<template>
    <AppLayout title="Detalles del corte">
        <div class="px-3 lg:px-14 py-5">
            <div class="text-center">
                <h1 class="lg:ml-10">Detalles de cortes</h1>
                <h1 class="lg:ml-10 font-bold">{{ formatDate(Object.values(groupedCashCuts)[0].cuts[0].created_at) }}</h1>
            </div>

            <!-- back -->
            <div class="my-4">
                <Back :to="route('cash-registers.index', {tab: '2'})"/>
            </div>

            <div class="flex mt-8 mb-10 text-sm">
                <div class="w-44 space-y-2">
                    <p>Total de cortes</p>
                    <p>Total de ventas</p>
                    <p>Total de diferencias</p>
                </div>
                <div class="space-y-2">
                    <p class="ml-4 font-bold">{{ Object.values(groupedCashCuts)[0].cuts?.length }}</p>
                    <p class="ml-4 font-bold">${{ Object.values(groupedCashCuts)[0].total_sales.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                    <p :class="getTextColorClass(Object.values(groupedCashCuts)[0].total_difference)" class="ml-4 font-bold">${{ Object.values(groupedCashCuts)[0].total_difference.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                </div>
            </div>

            <section v-for="(cash_cut, index) in Object.values(groupedCashCuts)[0].cuts" :key="cash_cut" class="xl:w-[90%] text-xs lg:text-sm">
                <p class="mb-2 ml-2">Notas de corte: <strong class="ml-2">{{ cash_cut.notes }}</strong></p>
                <article class="lg:flex lg:space-x-7 mx-auto">
                    <div class="w-full border border-grayD9 rounded-lg self-start">
                        <div class="flex justify-between border-b border-grayD9 py-2 px-4">
                            <p>{{ cash_cut.cash_register.name + ' • ' + cash_cut.user.name }}</p>
                            <p class="text-gray99">{{ formatDateHour(cash_cut.created_at) }}</p>
                        </div>
                        <div class="p-4 flex items-center space-x-2">
                            <div class="w-3/4 space-y-1">
                                <!-- <p class="font-bold mb-3">Recuento manual de efectivo</p> -->
                                <p class="text-gray99">Efectivo inicial</p>
                                <p class="text-gray99">Ventas</p>

                                <p  v-if="cashCutMovements[index]?.length"
                                    class="text-primary flex items-center">Movimientos de caja 
                                    <i :class="showcashRegisterMovements ? 'fa-angle-down' : 'fa-angle-up'" class="fa-solid ml-4"></i>
                                </p>

                                <div v-if="loadingMovements">
                                    <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                                </div>
                                <p v-else-if="showcashRegisterMovements" v-for="cashRegisterMovement in cashCutMovements[index]"
                                    :key="cashRegisterMovement"
                                    :title="cashRegisterMovement.type + ' -  Motivo: ' + (cashRegisterMovement.notes ?? 'no registrado') + ' • ' + formatDateHour(cashRegisterMovement.created_at)"
                                    class="text-gray99 truncate w-64 md:w-auto">
                                    {{ cashRegisterMovement.type + ' - Motivo: ' + (cashRegisterMovement.notes ??
                                        'no registrado') + ' • ' + formatDateHour(cashRegisterMovement.created_at) }}
                                </p>
                            </div>
                            <div class="w-1/4 space-y-1">
                                <!-- <p class="font-bold mb-3 pl-4"><span class="mr-3">$</span>{{
                                    cash_cut.counted_cash?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p> -->
                                <p class="text-gray99"><span class="text-gray99 mr-3"><i
                                    class="fa-solid fa-plus text-xs px-1"></i>$</span>{{
                                    cash_cut.started_cash?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                                <p class="text-gray99 pb-5"><span class="text-gray99 mr-3"><i
                                    class="fa-solid fa-plus text-xs px-1"></i>$</span>{{
                                    cash_cut.sales_cash?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                                <div v-if="loadingMovements">
                                    <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                                </div>

                                <p v-if="showcashRegisterMovements" v-for="cashRegisterMovement in cashCutMovements[index]"

                                    :key="cashRegisterMovement" class="text-gray99">
                                    <i :class="cashRegisterMovement.type === 'Ingreso' ? 'fa-plus' : 'fa-minus'"
                                        class="fa-solid text-xs px-1"></i>
                                    <span class="text-gray99 mr-3">$</span>{{
                                        cashRegisterMovement.amount?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}
                                </p>
                            </div>
                        </div>
                        <footer class="bg-[#F2F2F2] text-black font-bold py-2 flex px-2">
                            <p class="w-3/4 text-right pr-7">Efectivo esperado</p>
                            <p class="w-1/4 pl-4">
                                <span class="mr-3">
                                    $
                                </span>
                                <b>{{ cash_cut.expected_cash?.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</b>
                            </p>
                        </footer>
                    </div>

                    <!-- resumen de corte en pequeño--------------------- -->
                    <div class="mt-7 lg:mt-0 mx-auto lg:mx-0 sm:w-96 border border-grayD9 rounded-lg self-start">
                        <h2 class="py-2 bg-[#F2F2F2] text-center text-sm font-bold rounded-t-lg">Resumen de corte</h2>
                        <div class="flex justify-between space-x-1 p-5">
                            <div class="font-semibold space-y-1">
                                <p>Efectivo al iniciar</p>
                                <p>Esperado</p>
                                <p>Recuento manual</p>
                                <p class="pb-5">Diferencia</p>
                                <p>Retiro</p>
                                <p>Restante en caja</p>
                            </div>
                            <div class="space-y-1 font-semibold">
                                <p><span class="text-gray99 pr-3">$</span>{{ cash_cut.started_cash?.toLocaleString('en-US',
                                    {minimumFractionDigits: 2}) }}</p>
                                <p><span class="text-gray99 pr-3">$</span>{{ cash_cut.expected_cash?.toLocaleString('en-US',
                                    {minimumFractionDigits: 2}) }}</p>
                                <p><span class="text-gray99 pr-3">$</span>{{ cash_cut.counted_cash?.toLocaleString('en-US',
                                    {minimumFractionDigits: 2}) }}</p>
                                <p class="pb-5" :class="differenceStyles(cash_cut)"><span class="pr-3">$</span>{{
                                    (cash_cut.counted_cash - cash_cut.expected_cash)?.toLocaleString('en-US',
                                    {minimumFractionDigits: 2}) }}</p>
                                <p><span class="text-gray99 pr-3">$</span>{{
                                    cash_cut.withdrawn_cash?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                                <p><span class="text-gray99 pr-3">$</span>{{ (cash_cut.counted_cash -
                                    cash_cut.withdrawn_cash)?.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                            </div>
                        </div>
                        <!-- mensaje de diferencia de efectivo -->
                        <p :class="{
                            'text-green-500 bg-green-100': (cash_cut.expected_cash - cash_cut.counted_cash) === 0,
                            'text-blue-500 bg-blue-100': (cash_cut.expected_cash - cash_cut.counted_cash) < 0,
                            'text-red-500 bg-red-100': (cash_cut.expected_cash - cash_cut.counted_cash) > 0
                        }" class="rounded-b-lg text-xs py-[2px] px-2 text-center">
                            <i v-if="(cash_cut.expected_cash - cash_cut.counted_cash) === 0"
                                class="fa-solid fa-check mr-1"></i>
                            <i v-else-if="(cash_cut.expected_cash - cash_cut.counted_cash) < 0"
                                class="fa-solid fa-plus mr-1"></i>
                            <i v-else class="fa-solid fa-xmark mr-1"></i>
                            {{ (cash_cut.expected_cash - cash_cut.counted_cash) === 0 ? 'Todo bien' :
                                ((cash_cut.expected_cash - cash_cut.counted_cash) < 0 ? 'Sobrante en caja'
                                : 'Faltante de efectivo' ) }} </p>
                    </div>
                </article>
                <div class="border-b my-7"></div>
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
import axios from 'axios';

export default {
    data() {
        return {
            loadingMovements: false,
            cashCutMovements: [], // Arreglo para almacenar los movimientos de caja por corte
            showcashRegisterMovements: true, //muestra u oculta los moviimientos de caja
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        Back
    },
    props: {
        groupedCashCuts: Object
    },
    methods: {
        formatDateHour(dateString) {
            return format(parseISO(dateString), 'h:mm a', { locale: es });
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        differenceStyles(cash_cut) {
            if (cash_cut.counted_cash - cash_cut.expected_cash < 0) {
                return 'text-red-600';
            } else if (cash_cut.counted_cash - cash_cut.expected_cash === 0) {
                return 'text-green-500';
            } else if (cash_cut.counted_cash - cash_cut.expected_cash > 0) {
                return 'text-blue-600';
            }
        },
        getTextColorClass(difference) {
            if (difference < 0) {
                return 'text-red-500'; // Rojo para diferencias negativas
            } else if (difference === 0) {
                return 'text-green-500'; // Verde para diferencias igual a 0
            } else {
                return 'text-blue-500'; // Azul para diferencias positivas
            }
        },
        async getCashCutMovements(cash_cut) {
                this.loadingMovements = true;
                try {
                    // Realizar una solicitud para obtener los movimientos de caja asociados al corte actual
                    const response = await axios.get(route('cash-cuts.get-movements', cash_cut.id));
                    if (response.status === 200) {
                        // Almacenar los movimientos de caja en el objeto cashCutMovements utilizando el ID del corte como clave
                        this.cashCutMovements.push(response.data.items);
                    }
                } catch (error) {
                    console.error('Error al obtener los movimientos de caja:', error);
                } finally {
                    this.loadingMovements = false;
                }
        }
    },
    mounted() {
        //se recorre el arreglo de cortes para obtener los movimientos de cada uno
       Object.values(this.groupedCashCuts)[0].cuts.forEach(cut => {
            this.getCashCutMovements(cut);
        });
    }
}
</script>
