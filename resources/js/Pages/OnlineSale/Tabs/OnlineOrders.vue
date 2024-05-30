<template>
    <div class="flex justify-between items-center mx-3">
        <h1 class="font-bold text-lg">Pedidos en línea</h1>
        <div class="flex items-center space-x-4">
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
                    class="absolute top-9 right-0 lg:-left-64 border border-[#D9D9D9] rounded-md p-4 bg-white shadow-lg z-50 w-80">
                    <div class="mb-3">
                        <InputLabel value="Rango de fechas" class="ml-3 mb-1" />
                        <el-date-picker v-model="searchDate" type="daterange" range-separator="A"
                            start-placeholder="Fecha de inicio" end-placeholder="Fecha de fin" class="!w-full" />
                    </div>
                    <div class="flex space-x-3">
                        <PrimaryButton @click="filterOnlineSales" class="!py-1">Aplicar</PrimaryButton>
                        <ThirthButton @click="cleanFilter" class="!py-1">Limpiar</ThirthButton>
                    </div>
                </div>
            </div>
            <PrimaryButton class="!py-1">Registrar pedido</PrimaryButton>
        </div>
    </div>
    <p class="my-4 mx-3 text-gray99 text-sm">En esta sección solo se muestran los pedidos que se encuentran pendientes, procesando y pedidos cancelados. 
        Los pedidos entregados, puedes revisarlos en el módulo de <span @click="$inertia.get(route('sales.index'))" class="text-primary cursor-pointer ml-1">ventas registradas</span>
    </p>

    <Loading v-if="loading" class="mt-20" />
    <div class="mt-8" v-else>
        <p v-if="localOrders?.length" class="text-gray66 text-[11px] mb-3">{{ localOrders?.length }} de {{ totalOnlineOrders }}
            elementos
        </p>
        <div class="overflow-auto">
            <table v-if="localOrders?.length" class="w-full">
                <thead>
                    <tr class="*:text-left *:pb-2 *:px-4 *:text-sm">
                        <th># Pedido</th>
                        <th>Fecha de pedido</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estatus</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr @click="$inertia.visit(route('online-sales.show', online_order.id))"
                        v-for="online_order in localOrders" :key="online_order"
                        class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight cursor-pointer">
                        <td class="rounded-s-full">{{ online_order.id }}</td>
                        <td>{{ formatDate(online_order.created_at) }}</td>
                        <td>{{ online_order.name }}</td>
                        <td>${{ online_order.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                        <td class="flex items-center space-x-2">
                            <span v-html="getStatusIcon(online_order.status)"></span>
                            <p>{{ online_order.status }}</p>
                        </td>
                        <td class="rounded-e-full text-end">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <button @click.stop
                                    class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'processing|' + online_order.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2 text-blue-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            <span class="text-xs">Procesando</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item :command="'delivered|' + online_order.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2 text-green-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                            <span class="text-xs">Entregado</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item :command="'cancel|' + online_order.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2 text-red-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                            </svg>
                                            <span class="text-xs">Cancelar pedido</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item :command="'see|' + online_order.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span class="text-xs">Ver</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item :command="'edit|' + online_order.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                            </svg>
                                            <span class="text-xs">Editar</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item :command="'delete|' + online_order.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor"
                                                class="size-[14px] mr-2 text-red-600">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                            <span class="text-xs text-red-600">Eliminar</span>
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </td>
                    </tr>
                </tbody>
            </table>
            <el-empty v-else description="No hay ordenes registradas" />
            <p v-if="localOrders?.length" class="text-gray66 text-[11px] mt-1">{{ localOrders?.length }} de {{ totalOnlineOrders }}
                elementos
            </p>
            <p v-if="loadingItems" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </p>
            <button v-else-if="localOrders?.length && totalOnlineOrders > 15 && localOrders?.length < totalOnlineOrders && !filtered"
                @click="fetchItemsByPage" class="w-full text-primary my-4 text-xs mx-auto underline ml-6">Cargar más elementos</button>
        </div>
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import InputLabel from "@/Components/InputLabel.vue";
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import axios from 'axios';

export default {
data() {
    return {
        localOrders: this.orders, //ordenes locales 
        loading: false,
        totalOnlineOrders: null, //items totales para paginación
        showFilter: false, //filtro opciones
        searchDate: null, //filtro fechas
        loadingItems: false, //para paginación
        filtered: false, //bandera para saber si ya se filtró y deshabilitar la carga de elementos ya que hay un error.
        currentPage: 1, //para paginación
    }
},
components:{
PrimaryButton,
ThirthButton,
InputLabel,
Loading
},
props:{
orders: Array
},
methods:{
    handleCommand(command) {
        const commandName = command.split('|')[0];
        const data = command.split('|')[1];

        if (commandName == 'see') {
            this.$inertia.get(route('sales.show', data));
        } else if (commandName == 'processing' || commandName == 'delivered' || commandName == 'cancel') {
            updateStatus(commandName, data);
        } else if (commandName == 'delete') {
            this.showDeleteConfirm = true;
            this.itemIdToDelete = data;
        }
    },
    async updateStatus(status, orderId) {
        try {
            const response = await axios.put(route('online-sales.update-status', orderId), { status: status });
            if ( response.status === 200 ) {

            }
        } catch (error) {
            console.log(error);
        }
    },
    async filterOnlineSales() {
        if ( this.searchDate != null) {
            this.loading = true;
            try {
                const response = await axios.get(route('online-sales.filter'), { params: { queryDate: this.searchDate } });
                if (response.status == 200) {
                    this.localOrders = response.data.items;
                    this.filtered = true;
                }

            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
                this.showFilter = false;
            }
        } else {
            this.localOrders = this.orders;
            this.showFilter = false;
            this.filtered = false;
            this.currentPage = 1;
        }
    },
    async fetchItemsByPage() {
        try {
            this.loadingItems = true;
            const response = await axios.post(route('sales.get-by-page', this.currentPage), {cashRgisterId: this.cashRegister.id});

            if (response.status === 200) {
                this.sales = {...this.sales, ...response.data.items};
                this.currentPage++;
            }
        } catch (error) {
            console.log(error)
        } finally {
            this.loadingItems = false;
        }
    },
    cleanFilter() {
        this.localOrders = this.orders;
        this.showFilter = false;
        this.filtered = false;
        this.currentPage = 1;
    },
    formatDate(dateString) {
      return format(parseISO(dateString), 'dd MMMM yyyy, h:mm a', { locale: es });
    },
    getStatusIcon(status) {
      switch (status) {
        case 'Pendiente':
          return `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[17px] text-amber-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            `;
        case 'Procesando':
          return `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[17px] text-blue-500">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>`;
        case 'Entregado':
          return `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[17px] text-green-500">
              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>`;
        case 'Cancelado':
          return `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[17px] text-red-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>`;
        default:
          return '';
      }
    }
},
}
</script>