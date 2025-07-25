<template>
    <AppLayout title="Detalles del corte">
        <div class="px-3 lg:px-14 py-5">
            <div class="flex items-center justify-between mb-4">
                <!-- back -->
                <div class="my-4">
                    <Back :to="route('cash-registers.index', { tab: '2' })" />
                </div>

                <div class="text-center">
                    <h1 class="lg:ml-10">Detalles de cortes</h1>
                    <h1 class="lg:ml-10 font-bold">{{ formatDate(Object.values(groupedCashCuts)[0].cuts[0].created_at)
                        }}</h1>
                </div>

                <PrintButton @click="handlePrint" />
            </div>

            <div class="mt-8 mb-10 space-y-1 text-sm rounded-xl border border-grayD9 p-4 w-80">
                <div class="flex justify-between items-center space-x-2 border-b border-grayD9 pb-1 pr-2">
                    <p class="font-semibold">Total de cortes</p>
                    <p class="ml-4 font-bold">{{ Object.values(groupedCashCuts)[0].cuts?.length }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <p class="w-40">Ventas en tienda</p>
                    <p class="ml-4"><span class="mr-4">$</span>{{
                        Object.values(groupedCashCuts)[0].total_store_sales.toLocaleString('en-US',
                            { minimumFractionDigits: 2 }) }}</p>
                </div>
                <div v-if="hasModule('Tienda en línea')" class="flex items-center space-x-2">
                    <p class="w-40">Pedidos en linea</p>
                    <p class="ml-4"><span class="mr-4">$</span>{{
                        Object.values(groupedCashCuts)[0].total_online_sales.toLocaleString('en-US',
                            { minimumFractionDigits: 2 }) }}</p>
                </div>
                <div v-if="hasModule('Ordenes de servicio')" class="flex items-center space-x-2">
                    <p class="w-40">Ordenes de servicio</p>
                    <p class="ml-4"><span class="mr-4">$</span>{{
                        Object.values(groupedCashCuts)[0].total_service_orders.toLocaleString('en-US',
                            { minimumFractionDigits: 2 }) }}</p>
                </div>
                <div class="flex items-center space-x-2 font-semibold border-b border-grayD9 pb-1">
                    <p class="w-40">Total de ventas</p>
                    <p class="ml-4"><span class="mr-4">$</span>{{
                        Object.values(groupedCashCuts)[0].total_sales.toLocaleString('en-US', {
                            minimumFractionDigits:
                                2
                        }) }}</p>
                </div>
                <div class="flex items-center space-x-2 font-semibold">
                    <p class="w-40">Total de diferencias</p>
                    <p :class="getTextColorClass(Object.values(groupedCashCuts)[0].total_difference)" class="ml-4"><span
                            class="mr-4">$</span>{{
                                Object.values(groupedCashCuts)[0].total_difference.toLocaleString('en-US',
                                    { minimumFractionDigits: 2 }) }}</p>
                </div>
            </div>

            <section v-for="(cash_cut, index) in Object.values(groupedCashCuts)[0].cuts" :key="cash_cut"
                class="xl:w-[90%] text-xs md:text-sm">
                <p class="mb-2 ml-2">Notas de corte: <strong class="ml-2">{{ cash_cut.notes ?? 'Sin notas' }}</strong>
                </p>

                <article class="lg:flex lg:space-x-7 mx-auto">
                    <div
                        class="w-full border border-gray-300 rounded-2xl shadow-lg bg-white p-1 self-start transition-all ease-linear duration-200">
                        <div class="flex justify-between border-b border-grayD9 py-2 md:px-4 px-2">
                            <!-- <p>{{ cash_cut.cash_register.name + ' • ' + cash_cut.user.name }}</p> -->
                            <p>Corte realizado por: {{ cash_cut.user.name }}</p>
                            <p class="text-gray99">{{ formatDateHour(cash_cut.created_at) }}</p>
                        </div>
                        <div class="md:p-4 p-2 grid grid-cols-3 gap-1 text-sm">
                            <div class="col-span-full flex items-center space-x-2 pb-2">
                                <img class="w-7" src="@/../../public/images/dollar.webp" alt="Pago en efectivo">
                                <p class="font-bold text-gray-600">Efectivo en caja</p>
                            </div>
                            <p class="col-span-2 text-[#373737]">Efectivo inicial</p>
                            <p class="text-[#373737]">
                                $ {{ cash_cut.started_cash?.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                            </p>
                            <p class="col-span-2 text-[#373737]">Ventas en tienda</p>
                            <p class="text-[#373737]">
                                $ {{ (cash_cut.store_sales_cash)?.toLocaleString('en-US', {
                                    minimumFractionDigits: 2
                                }) }}
                            </p>
                            <p v-if="hasModule('Tienda en línea')" class="col-span-2 text-[#373737]">Pedidos en línea
                            </p>
                            <p v-if="hasModule('Tienda en línea')" class="text-[#373737]">
                                $ {{ (cash_cut.online_sales_cash)?.toLocaleString('en-US',
                                    { minimumFractionDigits: 2 }) }}
                            </p>
                            <p v-if="hasModule('Ordenes de servicio')" class="col-span-2 text-[#373737]">Órdenes de
                                servicio</p>
                            <p v-if="hasModule('Ordenes de servicio')" class="text-[#373737]">
                                $ {{ (cash_cut.service_orders_cash)?.toLocaleString('en-US', {
                                    minimumFractionDigits: 2
                                }) }}
                            </p>
                            <p class="col-span-2 text-gray99 text-right font-semibold mr-4">Subtotal de ventas</p>
                            <p class="text-gray99 font-semibold">
                                $ {{
                                    (
                                        cash_cut.store_sales_cash
                                        + cash_cut.online_sales_cash
                                        + cash_cut.service_orders_cash
                                    )
                                        ?.toLocaleString('en-US', { minimumFractionDigits: 2 })
                                }}
                            </p>
                            <p v-if="cashCutMovements[index]?.length"
                                class="col-span-2 text-primary flex items-center pt-3">
                                Movimientos de caja
                            </p>
                            <div v-if="loadingMovements">
                                <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                            </div>
                            <div v-else-if="showcashRegisterMovements" v-for="movement in cashCutMovements[index]"
                                :key="movement"
                                :title="`${movement.type} - Motivo: ${movement.notes ?? 'no registrado'} • ${formatDateHour(movement.created_at)}`"
                                class="col-span-full grid grid-cols-3 gap-1 odd:bg-[#f7f7f7] text-gray99 truncate md:w-auto">
                                <p class="col-span-2">
                                    {{ movement.type }} - Motivo: {{ movement.notes ?? 'no registrado' }}
                                    • {{ formatDateHour(movement.created_at) }}
                                </p>
                                <p>
                                    <i :class="movement.type === 'Ingreso' ? 'ml-[10px]' : 'fa-minus text-red-500'"
                                        class="fa-solid text-xs px-1"></i>
                                    $ {{ movement.amount?.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                                </p>
                            </div>
                            <p class="col-span-2 text-[#373737] font-bold text-right py-2">Total en efectivo</p>
                            <p class="text-[#373737] font-bold py-2 ml-5">
                                $ {{
                                    cash_cut.expected_cash?.toLocaleString('en-US', { minimumFractionDigits: 2 })
                                    ?? '0.00'
                                }}
                            </p>
                            <div class="flex items-center space-x-2 col-span-full py-2 border-t border-[#D9D9D9]">
                                <img class="w-7" src="@/../../public/images/card.webp" alt="Pago con tarjeta">
                                <p class="font-bold text-gray-600">Monto esperado en tarjeta</p>
                            </div>
                            <p class="col-span-2 text-[#373737]">Ventas en tienda</p>
                            <p class="text-[#373737]">
                                $ {{ (cash_cut.store_sales_card)?.toLocaleString('en-US', {
                                    minimumFractionDigits: 2
                                }) }}
                            </p>
                            <p v-if="hasModule('Tienda en línea')" class="col-span-2 text-[#373737]">Pedidos en línea
                            </p>
                            <p v-if="hasModule('Tienda en línea')" class="text-[#373737]">
                                $ {{ (cash_cut.online_sales_card)?.toLocaleString('en-US',
                                    { minimumFractionDigits: 2 }) }}
                            </p>
                            <p v-if="hasModule('Ordenes de servicio')" class="col-span-2 text-[#373737]">Órdenes de
                                servicio</p>
                            <p v-if="hasModule('Ordenes de servicio')" class="text-[#373737]">
                                $ {{ (cash_cut.service_orders_card)?.toLocaleString('en-US', {
                                    minimumFractionDigits: 2
                                }) }}
                            </p>
                            <p class="col-span-2 text-[#373737] text-right font-semibold mr-3">Total en tarjeta</p>
                            <p class="font-semibold">
                                $ {{
                                    cash_cut.expected_card?.toLocaleString('en-US', { minimumFractionDigits: 2 })
                                    ?? '0.00'
                                }}
                            </p>
                        </div>
                        <footer class="bg-[#F2F2F2] rounded-xl text-black font-bold py-2 flex px-2">
                            <p class="w-[66%] text-right pr-7">Total efectivo + tarjeta</p>
                            <p class="w-[33%] pl-4">
                                $ {{ (cash_cut.expected_cash + cash_cut.expected_card)
                                    ?.toLocaleString('en-US', { minimumFractionDigits: 2 })
                                }}
                            </p>
                        </footer>
                    </div>

                    <!-- resumen de corte en pequeño--------------------- -->
                    <div
                        class="mt-3 lg:mt-0 mx-auto lg:mx-0 w-96 md:w-[450px] border border-grayD9 self-start rounded-2xl shadow-lg p-1">
                        <h2 class="py-2 bg-[#F2F2F2] text-center text-sm font-bold rounded-xl">Resumen de corte</h2>
                        <div class="flex items-center space-x-2 px-5 pt-2">
                            <img class="w-5" src="@/../../public/images/dollar.webp" alt="Pago en efectivo">
                            <p class="text-[#37672B] font-semibold">Efectivo</p>
                        </div>
                        <div class="flex justify-between space-x-1 px-5 pt-1 text-sm">
                            <div class="font-semibold space-y-1 w-40">
                                <p>Efectivo al iniciar</p>
                                <p>Esperado en caja</p>
                                <p>Recuento manual</p>
                                <p class="pb-5">Diferencia</p>
                                <p>Retiro</p>
                                <p>Restante en caja</p>
                            </div>
                            <div class="space-y-1 font-semibold">
                                <p class="flex justify-between"><span class="text-gray99 pr-3">$</span>
                                    <span>
                                        {{
                                            cash_cut.started_cash?.toLocaleString('en-US',
                                                { minimumFractionDigits: 2 }) }}
                                    </span>
                                </p>
                                <p class="flex justify-between"><span class="text-gray99 pr-3">$</span>
                                    <span>
                                        {{
                                            cash_cut.expected_cash?.toLocaleString('en-US',
                                                { minimumFractionDigits: 2 }) }}
                                    </span>
                                </p>
                                <p class="flex justify-between"><span class="text-gray99 pr-3">$</span>
                                    <span>
                                        {{
                                            cash_cut.counted_cash?.toLocaleString('en-US',
                                                { minimumFractionDigits: 2 }) }}
                                    </span>
                                </p>
                                <p class="pb-5 flex justify-between" :class="differenceStyles(cash_cut)"><span class="pr-3">$</span>{{
                                    (cash_cut.counted_cash - cash_cut.expected_cash)?.toLocaleString('en-US',
                                        { minimumFractionDigits: 2 }) }}</p>
                                <p class="flex justify-between"><span class="text-gray99 pr-3">$</span>
                                    <span>
                                        {{
                                            cash_cut.withdrawn_cash?.toLocaleString('en-US', { minimumFractionDigits: 2 })
                                            ??
                                        '0.00' }}
                                    </span>
                                </p>
                                <p class="flex justify-between"><span class="text-gray99 pr-3">$</span>
                                    <span>
                                        {{ (cash_cut.counted_cash -
                                            cash_cut.withdrawn_cash)?.toLocaleString('en-US', { minimumFractionDigits: 2 })
                                        }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <!-- mensaje de diferencia de efectivo -->
                        <p :class="{
                            'text-green-500 bg-green-100': (cash_cut.expected_cash - cash_cut.counted_cash) === 0,
                            'text-blue-500 bg-blue-100': (cash_cut.expected_cash - cash_cut.counted_cash) < 0,
                            'text-red-500 bg-red-100': (cash_cut.expected_cash - cash_cut.counted_cash) > 0
                        }" class="rounded-xl text-xs py-[2px] px-2 text-center">
                            <i v-if="(cash_cut.expected_cash - cash_cut.counted_cash) === 0"
                                class="fa-solid fa-check mr-1"></i>
                            <i v-else-if="(cash_cut.expected_cash - cash_cut.counted_cash) < 0"
                                class="fa-solid fa-plus mr-1"></i>
                            <i v-else class="fa-solid fa-xmark mr-1"></i>
                            {{ (cash_cut.expected_cash - cash_cut.counted_cash) === 0 ? 'Todo bien' :
                                ((cash_cut.expected_cash - cash_cut.counted_cash) < 0 ? 'Sobrante en caja'
                                    : 'Faltante de efectivo') }} </p>

                                <!-- Detalles de ventas pagadas con tarjeta -->
                                <div class="flex items-center space-x-2 px-5 pt-7">
                                    <img class="w-5" src="@/../../public/images/card.webp" alt="Pago con tarjeta">
                                    <p class="text-[#05394F] font-semibold">Tarjeta</p>
                                </div>
                                <div class="flex justify-between space-x-1 px-5 pt-1 text-sm">
                                    <div class="font-semibold space-y-1 w-40">
                                        <p>Esperado</p>
                                        <p>Reportado</p>
                                        <p class="pb-5">Diferencia</p>
                                    </div>
                                    <div class="space-y-1 font-semibold">
                                        <p class="flex justify-between">
                                            <span class="text-gray99 pr-3">$</span>
                                            <span>
                                                {{ cash_cut.expected_card?.toLocaleString('en-US',
                                                    { minimumFractionDigits: 2 }) ?? '0.00' }}
                                            </span>
                                        </p>
                                        <p class="flex justify-between">
                                            <span class="text-gray99 pr-3">$</span>
                                            <span>
                                                {{ cash_cut.counted_card?.toLocaleString('en-US',
                                                    { minimumFractionDigits: 2 }) ?? '0.00' }}
                                            </span>
                                        </p>
                                        <p class="pb-5 flex justify-between" :class="differenceCardStyles(cash_cut)"><span
                                                class="pr-3">$</span>{{
                                                    (cash_cut.counted_card - cash_cut.expected_card)?.toLocaleString('en-US',
                                                        { minimumFractionDigits: 2 }) }}</p>
                                    </div>
                                </div>
                                <!-- mensaje de diferencia de efectivo -->
                                <p :class="{
                                    'text-green-500 bg-green-100': (cash_cut.expected_card - cash_cut.counted_card) === 0,
                                    'text-blue-500 bg-blue-100': (cash_cut.expected_card - cash_cut.counted_card) < 0,
                                    'text-red-500 bg-red-100': (cash_cut.expected_card - cash_cut.counted_card) > 0
                                }" class="rounded-b-xl text-xs py-[2px] px-2 text-center">
                                    <i v-if="(cash_cut.expected_card - cash_cut.counted_card) === 0"
                                        class="fa-solid fa-check mr-1"></i>
                                    <i v-else-if="(cash_cut.expected_card - cash_cut.counted_card) < 0"
                                        class="fa-solid fa-plus mr-1"></i>
                                    <i v-else class="fa-solid fa-xmark mr-1"></i>
                                    {{ (cash_cut.expected_card - cash_cut.counted_card) === 0 ? 'Todo bien' :
                                        ((cash_cut.expected_card - cash_cut.counted_card) < 0 ? 'Sobrante en caja'
                                            : 'Faltante de efectivo') }} </p>
                    </div>
                </article>
                <div class="border-b my-12"></div>
            </section>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Back from "@/Components/MyComponents/Back.vue";
import PrintButton from "@/Components/MyComponents/PrintButton.vue";
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
        PrimaryButton,
        PrintButton,
        AppLayout,
        Back
    },
    props: {
        groupedCashCuts: Object
    },
    methods: {
        handlePrint() {
            const date = this.groupedCashCuts
                ? this.groupedCashCuts[Object.keys(this.groupedCashCuts)[0]].cuts[0].created_at
                : null;

            if (date) {
                const adjustedDate = new Date(date);
                adjustedDate.setHours(adjustedDate.getHours() - 6);

                const formattedDate = adjustedDate.toISOString().split('T')[0]; // YYYY-MM-DD
                const printUrl = this.route('cash-cuts.print', formattedDate);
                window.open(printUrl, '_blank');
            } else {
                alert('No se encontró una fecha válida para imprimir.');
            }
        },
        hasModule(moduleName) {
            return this.$page.props.auth.user.store.activated_modules?.includes(moduleName);
        },
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
        differenceCardStyles(cash_cut) {
            if (cash_cut.counted_card - cash_cut.expected_card < 0) {
                return 'text-red-600';
            } else if (cash_cut.counted_card - cash_cut.expected_card === 0) {
                return 'text-green-500';
            } else if (cash_cut.counted_card - cash_cut.expected_card > 0) {
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
