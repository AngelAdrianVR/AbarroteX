<template>
    <div class="flex justify-between items-center mx-3">
        <h1 class="font-bold text-lg">Historial de ventas</h1>
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
                </div>
                <div class="flex space-x-2">
                    <PrimaryButton @click="searchSales" class="!py-1"
                        :disabled="isMobile ? !(startDate && finishDate) : !searchDate">
                        Aplicar
                    </PrimaryButton>
                    <ThirthButton @click="cleanFilter" class="!py-1">Limpiar</ThirthButton>
                </div>
            </div>
        </div>
    </div>
    <Loading v-if="loading" class="mt-20" />
    <div class="mt-8" v-else>
        <!-- <section class="text-center mt-3" v-if="['Administrador'].includes($page.props.auth.user.rol)">
            <el-tag :key="`Total registrado: $${getTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}`"
                type="success" effect="plain" style="font-size: 19px;">
                {{ `Total registrado: $${getTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}` }}
            </el-tag>
        </section> -->
        <p v-if="Object.keys(sales)?.length" class="text-gray66 text-[11px] mb-3">
            {{ Object.keys(sales)?.length }} de {{ totalSales }} elementos
        </p>
        <div class="overflow-auto">
            <table v-if="Object.keys(sales)?.length" class="w-full">
                <thead>
                    <tr class="*:text-left *:pb-2 *:px-4 *:text-sm border-b border-primary">
                        <th>Fecha</th>
                        <th>Ventas en tienda</th>
                        <th v-if="$page.props.auth.user.store.activated_modules.includes('Tienda en línea')">
                            Ventas en linea
                        </th>
                        <th v-if="$page.props.auth.user.store.activated_modules.includes('Cotizaciones')">
                            Cotizaciones
                        </th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr @click="$inertia.visit(route('sales.show', { date: index, cashRegisterId: this.cashRegister.id }))"
                        v-for="(sale, index) in sortedData()" :key="index"
                        class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight cursor-pointer">
                        <td class="rounded-s-full">{{ formatDate(index) }}</td>
                        <td>
                            {{ sale.normal_folios != 1 ? sale.normal_folios + ' ventas' : sale.normal_folios + ' venta'
                            }}
                            ({{ sale.total_normal_quantity }} productos en total)
                        </td>
                        <td v-if="$page.props.auth.user.store.activated_modules.includes('Tienda en línea')">
                            {{ sale.online_folios != 1 ? sale.online_folios + ' ventas' : sale.online_folios + ' venta'
                            }}
                            ({{ sale.total_online_quantity }} productos en total)
                        </td>
                        <td v-if="$page.props.auth.user.store.activated_modules.includes('Cotizaciones')">
                            {{ sale.quote_folios != 1 ? sale.quote_folios + ' ventas' : sale.quote_folios + ' venta'
                            }}
                            ({{ sale.total_quote_quantity }} productos en total)
                        </td>
                        <td>
                            ${{ sale.total_day_sale?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") }}
                        </td>
                        <td class="rounded-e-full text-end">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <button @click.stop
                                    class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'see|' + index">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span class="text-xs">Ver</span>
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </td>
                    </tr>
                </tbody>
            </table>
            <el-empty v-else description="No hay ventas registradas" />
            <p v-if="Object.keys(sales)?.length" class="text-gray66 text-[11px] mt-1">
                {{ Object.keys(sales)?.length }} de {{ totalSales }} elementos
            </p>
            <p v-if="loadingItems" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </p>
            <button
                v-else-if="Object.keys(sales)?.length && totalSales > 30 && Object.keys(sales)?.length < totalSales && !filtered"
                @click="fetchItemsByPage" class="text-primary w-full my-4 text-xs underline">
                Cargar más elementos
            </button>
        </div>
    </div>

    <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
        <template #title>
            <h1>Eliminar ventas</h1>
        </template>
        <template #content>
            <p>
                Se eliminará las ventas del dia seleccionado, esto es un proceso irreversible. ¿Continuar
                de todas formas?
            </p>
        </template>
        <template #footer>
            <div class="flex items-center space-x-1">
                <CancelButton @click="showDeleteConfirm = false">Cancelar</CancelButton>
                <DangerButton @click="deleteItem">Eliminar</DangerButton>
            </div>
        </template>
    </ConfirmationModal>
</template>

<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import Loading from '@/Components/MyComponents/Loading.vue';
import DangerButton from "@/Components/DangerButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import axios from 'axios';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    data() {
        return {
            showDeleteConfirm: false,
            loading: false,
            itemIdToDelete: null,
            totalSales: null,
            sales: {},
            // filtro
            showFilter: false,
            searchDate: null,
            startDate: null, //vista movil
            finishDate: null, //vista movil
            // carga
            loadingItems: false, //para paginación
            currentPage: 1, //para paginación
            filtered: false, //bandera para saber si ya se filtró y deshabilitar la carga de elementos ya que hay un error.
        };
    },
    components: {
        ConfirmationModal,
        PrimaryButton,
        DangerButton,
        CancelButton,
        ThirthButton,
        InputLabel,
        Loading
    },
    props: {
        cashRegister: Object
    },
    computed: {
        isMobile() {
            return window.innerWidth < 768;
        },
        getTotal() {
            return Object.values(this.sales)?.reduce((accum, item) => {
                return accum += item.total_sale + item.online_sales_total;
            }, 0);
        },
    },
    methods: {
        sortedData() {
            // Convertir el objeto en un array de entradas
            const entries = Object.entries(this.sales);

            // Ordenar el array por la fecha de la más reciente a la más antigua
            entries.sort((a, b) => new Date(b[0]) - new Date(a[0]));

            // Convertir el array de vuelta en un objeto
            const sortedObject = Object.fromEntries(entries);

            return sortedObject;
        },
        handleStartDateChange(value) {
            this.startDate = value;
            // Si finishDate es nulo, aplica la regla de deshabilitación
            if (!this.finishDate) {
                this.disabledPrevDays();
            }
        },
        handleFinishDateChange(value) {
            this.finishDate = value;
            // Si startDate es nulo, aplica la regla de deshabilitación
            if (!this.startDate) {
                this.disabledNextDays();
            }
        },
        disabledPrevDays(date) {
            if (this.finishDate) {
                return date.getTime() > new Date(this.finishDate).getTime();
            }
            return false;
        },
        disabledNextDays(date) {
            if (this.startDate) {
                return date.getTime() < new Date(this.startDate).getTime();
            }
            return false;
        },
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const data = command.split('|')[1];

            if (commandName == 'see') {
                this.$inertia.get(route('sales.show', { date: data, cashRegisterId: this.cashRegister.id }));
            } else if (commandName == 'print') {
                this.print(data);
            } else if (commandName == 'delete') {
                this.showDeleteConfirm = true;
                this.itemIdToDelete = data;
            }
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM, yyyy', { locale: es });
        },
        formatBaseDate(dateString) {
            const months = {
                'January': '01',
                'February': '02',
                'March': '03',
                'April': '04',
                'May': '05',
                'June': '06',
                'July': '07',
                'August': '08',
                'September': '09',
                'October': '10',
                'November': '11',
                'December': '12'
            };

            const [day, month, year] = dateString.split('-');
            return `${year}-${months[month]}-${day}`;
        },
        print(saleDay) {
            window.open(route('sales.print-ticket', saleDay), '_blank');
        },
        cleanFilter() {
            this.fetchCashRegisterSales();
            this.searchDate = null;
            this.startDate = null;
            this.finishDate = null;
            this.showFilter = false;
            this.filtered = false;
            this.currentPage = 1;
        },
        async deleteItem() {
            try {
                const response = await axios.delete(route('sales.destroy', this.itemIdToDelete));
                if (response.status == 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Se ha eliminado la venta',
                        type: 'success',
                    });
                    location.reload();
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'Error',
                    message: 'No se pudo eliminar la venta. Intente más tarde',
                    type: 'error',
                });
            }
        },
        async fetchCashRegisterSales() {
            this.loading = true;
            try {
                const response = await axios.get(route('sales.fetch-cash-register-sales', this.cashRegister.id));
                if (response.status === 200) {
                    this.sales = response.data.groupedSales;
                    this.totalSales = response.data.total_sales;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async searchSales() {
            let range = this.searchDate;
            if (this.isMobile) {
                range = [this.startDate, this.finishDate];
            }

            this.loading = true;
            try {
                const response = await axios.get(route('sales.search'), { params: { queryDate: range, cashRegisterId: this.cashRegister.id } });
                if (response.status == 200) {
                    this.sales = response.data.items;
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
                const response = await axios.post(route('sales.get-by-page', this.currentPage), { cashRgisterId: this.cashRegister.id });

                if (response.status === 200) {
                    this.sales = { ...this.sales, ...response.data.items };
                    this.currentPage++;
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.loadingItems = false;
            }
        },
    },
    mounted() {
        this.fetchCashRegisterSales();
    }
}
</script>
