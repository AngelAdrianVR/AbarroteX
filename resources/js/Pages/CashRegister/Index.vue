<template>
    <AppLayout title="Caja">
        <section class="mt-5 mx-2 lg:mx-8">
        <div class="text-right">
            <ThirthButton @click="$inertia.get(route('cash-registers.create'))">Crear caja</ThirthButton>
        </div>
            <el-tabs class="mx-3" v-model="activeTab" @tab-click="updateURL">
                <el-tab-pane v-for="(item, index) in cash_registers" :key="item" :label="item.name" :name="String(index + 1)">
                    <CashRegister :cash_register="item" />
                </el-tab-pane>

                <!-- Historial de cortes -->
                <el-tab-pane label="Historial de cortes" :name="String(cash_registers.length + 1)">
                    <section class="flex justify-between">
                        <div></div>
                        <div class="relative">
                            <!-- filtro -->
                            <button @click.stop="showFilter = !showFilter"
                                class="border border-[#D9D9D9] rounded-full py-1 px-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="16" width="16"
                                    id="Filter-Sort-Lines-Descending--Streamline-Ultimate">
                                    <desc>Filter Sort Lines Descending Streamline Icon: https://streamlinehq.com</desc>
                                    <defs></defs>
                                    <title>filter</title>
                                    <path d="M0.73 4.2791H23.27" fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="1"></path>
                                    <path d="M3.131 9.426H20.869" fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="1"></path>
                                    <path d="M8.7141 19.7209H15.2859" fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="1"></path>
                                    <path d="M5.531 14.573H18.469" fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="1"></path>
                                </svg>
                                <p class="text-sm ml-2">Filtrar</p>
                            </button>
                            <div v-if="showFilter"
                                class="absolute top-9 right-0 lg:-left-64 border border[#D9D9D9] rounded-md p-4 bg-white shadow-lg z-10 w-80">
                                <div class="mb-3">
                                    <InputLabel value="Rango de fechas" class="ml-3 mb-1" />
                                    <el-date-picker v-model="searchDate" type="daterange" range-separator="A"
                                        start-placeholder="Fecha de inicio" end-placeholder="Fecha de fin" class="!w-full" />
                                </div>
                                <div class="flex space-x-3">
                                    <PrimaryButton @click="filterCashCuts" class="!py-1">Aplicar</PrimaryButton>
                                    <ThirthButton @click="searchDate = null; filterCashCuts()" class="!py-1">Limpiar</ThirthButton>
                                </div>
                            </div>
                        </div>
                    </section>

                    <Loading v-if="loading" class="mt-20" />
                    <div v-else class="mt-8">
                        <p v-if="Object.keys(localCashCuts)?.length" class="text-gray66 text-[11px] mb-3">{{ Object.keys(localCashCuts)?.length }} de {{ total_cash_cuts }}
                            elementos
                        </p>
                        <CashCutsTable :items="localCashCuts" />
                        <p v-if="Object.keys(localCashCuts)?.length" class="text-gray66 text-[11px] mt-1">{{ Object.keys(localCashCuts)?.length }} de {{ total_cash_cuts }}
                            elementos
                        </p>
                        <p v-if="loadingItems" class="text-xs my-4 text-center">
                            Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                        </p>
                        <button v-else-if="Object.keys(localCashCuts)?.length && total_cash_cuts > 7 && Object.keys(localCashCuts)?.length < total_cash_cuts && !filtered"
                            @click="fetchItemsByPage" class="w-full text-primary my-4 text-xs mx-auto underline ml-6">Cargar más elementos</button>
                    </div>
                </el-tab-pane>
            </el-tabs>
        </section>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import InputLabel from "@/Components/InputLabel.vue";
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CashCutsTable from '@/Pages/CashRegister/Tabs/CashCutsTable.vue';
import CashRegister from '@/Pages/CashRegister/Tabs/CashRegister.vue';

export default {
    data() {
        return {
            activeTab: '1',
            loading: false,
            localCashCuts: this.cash_cuts,
            showFilter: false, //filtro opciones
            searchDate: null, //filtro fechas
            loadingItems: false, //para paginación
            currentPage: 1, //para paginación
            filtered: false, //bandera para saber si ya se filtró y deshabilitar la carga de elementos ya que hay un error.
        }
    },
    components: {
        AppLayout,
        CashCutsTable,
        PrimaryButton,
        ThirthButton,
        CashRegister,
        InputLabel,
        Loading
    },
    props: {
        cash_registers: Array,
        cash_cuts: Array,
        total_cash_cuts: Number
    },
    methods: {
        async filterCashCuts() {
            // si el rango de fechas no contiene nada se resetea la información
            if ( this.searchDate != null) {
                this.loading = true;
                try {
                    const response = await axios.get(route('cash-cuts.filter'), { params: { queryDate: this.searchDate } });
                    if (response.status == 200) {
                        this.localCashCuts = response.data.items;
                        this.filtered = true;
                    }

                } catch (error) {
                    console.log(error);
                } finally {
                    this.loading = false;
                    this.showFilter = false;
                }
            } else {
                this.localCashCuts = this.cash_cuts;
                this.showFilter = false;
                this.filtered = false;
                this.currentPage = 1;
            }
        },
        async fetchItemsByPage() {
            try {
                this.loadingItems = true;
                const response = await axios.get(route('cash-cuts.get-by-page', this.currentPage));

                if (response.status === 200) {
                    this.localCashCuts = {...this.localCashCuts, ...response.data.items};
                    this.currentPage++;
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.loadingItems = false;
            }
        },
        updateURL(tab) {
            const params = new URLSearchParams(window.location.search);
            params.set('tab', tab.props.name );
            window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
        },
        setActiveTabFromURL() {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            if (tab) {
                this.activeTab = tab;
            }
        }
    },
    mounted() {
        this.setActiveTabFromURL();
    }
}
</script>