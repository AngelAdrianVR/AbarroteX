<template>
    <AppLayout title="Gastos">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mx-3">
                <h1 class="font-bold">Gastos</h1>
                <div class="flex space-x-2 items-center relative">
                    <!-- Boton para crear gasto -->
                    <PrimaryButton v-if="canCreate" @click="$inertia.get(route('expenses.create'))" class="!py-[6px]">
                        Crear
                    </PrimaryButton>
                    <!-- filtro -->
                    <!-- <button @click.stop="showFilter = !showFilter"
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
                    </button> -->
                    <!-- <div v-if="showFilter"
                        class="absolute top-9 right-0 lg:-left-40 border border[#D9D9D9] rounded-md p-4 bg-white shadow-lg z-10 w-80">
                        <div v-if="isMobile" class="flex items-center space-x-2">
                            <el-date-picker @change="handleStartDateChange" :disabled-date="disabledPrevDays"
                                v-model="startDate" type="date" class="!w-1/2" placeholder="Inicio" size="small" />
                            <el-date-picker @change="handleFinishDateChange" :disabled-date="disabledNextDays"
                                v-model="finishDate" type="date" class="!w-1/2" placeholder="Final" size="small" />
                        </div>
                        <div v-else>
                            <el-date-picker v-model="searchDate" type="daterange" range-separator="A"
                                start-placeholder="Fecha de inicio" end-placeholder="Fecha de fin" class="!w-full" />
                        </div>
                        <div class="flex space-x-2 mt-3">
                            <PrimaryButton @click="filterExpenses" class="!py-1"
                                :disabled="isMobile ? !(startDate && finishDate) : !searchDate">
                                Aplicar
                            </PrimaryButton>
                            <ThirthButton @click="cleanFilter" class="!py-1">Limpiar</ThirthButton>
                        </div>
                    </div> -->
                </div>
            </div>

            <Loading v-if="loading" class="mt-20" />
            <div v-else-if="data" class="mt-8">
                <RegisteredExpensesTable :items="data.items" @refresh-data="handleRefreshing"
                    :pagination="data.pagination" />
                <!-- <RegisteredExpensesTable :expenses="localExpenses" /> -->
            </div>
            <!-- <Loading v-if="loading" class="mt-20" />
            <div v-else class="mt-8">
                <p v-if="Object.keys(localExpenses)?.length" class="text-gray66 text-[11px] mb-3">
                    {{ Object.keys(localExpenses)?.length }} de {{ total_expenses }}
                    elementos
                </p>
                <RegisteredExpensesTable :expenses="localExpenses" />
                <p v-if="Object.keys(localExpenses)?.length" class="text-gray66 text-[11px] mt-1">
                    {{ Object.keys(localExpenses)?.length }} de {{ total_expenses }}
                    elementos
                </p>
                <p v-if="loadingItems" class="text-xs my-4 text-center">
                    Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                </p>
                <button
                    v-else-if="Object.keys(localExpenses)?.length && total_expenses > 30 && Object.keys(localExpenses)?.length < total_expenses && !filtered"
                    @click="fetchItemsByPage" class="w-full text-primary my-4 text-xs mx-auto underline ml-6">Cargar más
                    elementos</button>
            </div> -->
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import RegisteredExpensesTable from '@/Components/MyComponents/Expense/RegisteredExpensesTable.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import Loading from '@/Components/MyComponents/Loading.vue';
import axios from 'axios';

export default {
    data() {
        return {
            // loading: false,
            // localExpenses: this.groupedExpenses,
            // // filtro
            // showFilter: false,
            // searchDate: null,
            // startDate: null, //vista movil
            // finishDate: null, //vista movil
            // searchClient: null, //filtro cliente
            // loadingItems: false, //para paginación
            // currentPage: 1, //para paginación
            // filtered: false, //bandera para saber si ya se filtró y deshabilitar la carga de elementos ya que hay un error.
            // // Permisos de rol actual
            canCreate: this.$page.props.auth.user.permissions.includes('Crear gastos'),
            loading: false,
            data: null,
            //para paginación
            currentPage: null,
            pageSize: null,
        }
    },
    components: {
        AppLayout,
        RegisteredExpensesTable,
        PrimaryButton,
        ThirthButton,
        InputLabel,
        Loading,
    },
    props: {
        // groupedExpenses: Object,
        // total_expenses: Number
    },
    computed: {
        // isMobile() {
        //     return window.innerWidth < 768;
        // }
    },
    methods: {
        handleRefreshing(page, pageSize) {
            this.currentPage = page;
            this.pageSize = pageSize;
            this.fetchData();
        },
        getPageFromUrl() {
            const params = new URLSearchParams(window.location.search);
            const page = params.get('page');
            const pageSize = params.get('pageSize');
            if (page) {
                this.currentPage = parseInt(page);
            } else {
                this.currentPage = 1;
            }
            if (pageSize) {
                this.pageSize = parseInt(pageSize);
            } else {
                this.pageSize = 100;
            }
        },
        async fetchData() {
            try {
                this.loading = true;
                const response = await axios.get(route('expenses.get-data-for-table', { page: this.currentPage, pageSize: this.pageSize }));

                if (response.status === 200) {
                    this.data = response.data.data;
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
        // handleStartDateChange(value) {
        //     this.startDate = value;
        //     // Si finishDate es nulo, aplica la regla de deshabilitación
        //     if (!this.finishDate) {
        //         this.disabledPrevDays();
        //     }
        // },
        // handleFinishDateChange(value) {
        //     this.finishDate = value;
        //     // Si startDate es nulo, aplica la regla de deshabilitación
        //     if (!this.startDate) {
        //         this.disabledNextDays();
        //     }
        // },
        // disabledPrevDays(date) {
        //     if (this.finishDate) {
        //         return date.getTime() > new Date(this.finishDate).getTime();
        //     }
        //     return false;
        // },
        // disabledNextDays(date) {
        //     if (this.startDate) {
        //         return date.getTime() < new Date(this.startDate).getTime();
        //     }
        //     return false;
        // },
        // cleanFilter() {
        //     this.localExpenses = this.groupedExpenses;
        //     this.searchDate = null;
        //     this.startDate = null;
        //     this.finishDate = null;
        //     this.showFilter = false;
        //     this.filtered = false;
        //     this.currentPage = 1;
        // },
        // async filterExpenses() {
        //     let range = this.searchDate;
        //     if (this.isMobile) {
        //         range = [this.startDate, this.finishDate];
        //     }

        //     this.loading = true;
        //     try {
        //         const response = await axios.get(route('expenses.filter'), { params: { queryDate: range } });
        //         if (response.status == 200) {
        //             this.localExpenses = response.data.items;
        //             this.filtered = true;
        //         }

        //     } catch (error) {
        //         console.log(error);
        //     } finally {
        //         this.loading = false;
        //         this.showFilter = false;
        //     }
        // },
        // async fetchItemsByPage() {
        //     try {
        //         this.loadingItems = true;
        //         const response = await axios.get(route('expenses.get-by-page', this.currentPage));

        //         if (response.status === 200) {
        //             this.localExpenses = { ...this.localExpenses, ...response.data.items };
        //             this.currentPage++;
        //         }
        //     } catch (error) {
        //         console.log(error)
        //     } finally {
        //         this.loadingItems = false;
        //     }
        // },
    },
    mounted() {
        this.getPageFromUrl(); //obtiene la variable page de la url.
        this.fetchData();
        // resetear variable de local storage a false
        localStorage.setItem('pendentProcess', false);
    }
}
</script>
