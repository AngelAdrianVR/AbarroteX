<template>
    <AppLayout title="Egresos">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Egresos</h1>
                <div class="flex space-x-2 items-center relative">
                    <!-- Boton para crear egreso -->
                    <PrimaryButton @click="$inertia.get(route('expenses.create'))" class="">Crear</PrimaryButton>
                    <!-- filtro -->
                    <button @click.stop="showFilter = !showFilter"
                        class="border border-[#D9D9D9] rounded-full py-1 px-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="16" width="16"
                            id="Filter-Sort-Lines-Descending--Streamline-Ultimate">
                            <desc>Filter Sort Lines Descending Streamline Icon: https://streamlinehq.com</desc>
                            <defs></defs>
                            <title>filter</title>
                            <path d="M0.73 4.2791H23.27" fill="none" stroke="#000000" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="1"></path>
                            <path d="M3.131 9.426H20.869" fill="none" stroke="#000000" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="1"></path>
                            <path d="M8.7141 19.7209H15.2859" fill="none" stroke="#000000" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="1"></path>
                            <path d="M5.531 14.573H18.469" fill="none" stroke="#000000" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="1"></path>
                        </svg>
                        <p class="text-sm ml-2">Filtrar</p>
                    </button>
                    <div v-if="showFilter"
                        class="absolute top-9 right-0 lg:-left-40 border border[#D9D9D9] rounded-md p-4 bg-white shadow-lg z-10 w-80">
                        <div class="mb-3">
                            <InputLabel value="Rango de fechas" class="ml-3 mb-1" />
                            <el-date-picker v-model="searchDate" type="daterange" range-separator="A"
                                start-placeholder="Fecha de inicio" end-placeholder="Fecha de fin" class="!w-full" />
                        </div>
                        <PrimaryButton @click="filterExpenses" class="!py-1">Aplicar</PrimaryButton>
                    </div>
                </div>
            </div>

            <Loading v-if="loading" class="mt-20" />
            <div v-else class="mt-8 lg:w-11/12">
                <p v-if="Object.keys(localExpenses)?.length" class="text-gray66 text-[11px]">{{ Object.keys(localExpenses)?.length }} de {{ total_expenses }}
                    elementos
                </p>
                <RegisteredExpensesTable :expenses="localExpenses" />
                <!-- <SaleMobileIndex v-for="item in localExpenses" :key="item.id" :saleId="item.id" class="md:hidden" /> -->
                <p v-if="loadingItems" class="text-xs my-4 text-center">
                    Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                </p>
                <button v-if="(Object.keys(localExpenses)?.length < total_expenses) && !filtered"
                    @click="fetchItemsByPage" class="w-full text-primary my-4 text-xs mx-auto underline ml-6">Cargar más elementos</button>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import RegisteredExpensesTable from '@/Components/MyComponents/Expense/RegisteredExpensesTable.vue';  
// import SaleMobileIndex from '@/Components/MyComponents/Expense/SaleMobileIndex.vue';    
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import Loading from '@/Components/MyComponents/Loading.vue';
import axios from 'axios';

export default {
    data() {
        return {
            loading: false,
            localExpenses: this.groupedExpenses,
            showFilter: false, //filtro opciones
            searchDate: null, //filtro fechas
            searchClient: null, //filtro cliente
            loadingItems: false, //para paginación
            currentPage: 1, //para paginación
            filtered: false, //bandera para saber si ya se filtró y deshabilitar la carga de elementos ya que hay un error.
        }
    },
    components: {
        AppLayout,
        RegisteredExpensesTable,
        // SaleMobileIndex, 
        PrimaryButton,
        InputLabel,
        Loading
    },
    props: {
        groupedExpenses: Object,
        total_expenses: Number
    },
    methods: {
        async filterExpenses() {
            this.loading = true;
            try {
                const response = await axios.get(route('expenses.filter'), { params: { queryDate: this.searchDate } });
                if (response.status == 200) {
                    this.localExpenses = response.data.items;
                    this.filtered = true;
                }

            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
                this.showFilter = false;
            }
        },
        async fetchItemsByPage() {
            try {
                this.loadingItems = true;
                const response = await axios.get(route('expenses.get-by-page', this.currentPage));

                if (response.status === 200) {
                    this.localExpenses = {...this.localExpenses, ...response.data.items};
                    this.currentPage++;
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.loadingItems = false;
            }
        },
    }
}
</script>
