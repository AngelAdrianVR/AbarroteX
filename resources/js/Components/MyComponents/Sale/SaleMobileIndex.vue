<template>
    <div>
        <div class="border border-grayD9 rounded-[20px] py-2 px-4 min-h-32 text-xs mb-2">
            <Loading v-if="loading" class="pt-4" />
            <div v-else>
                <header class="flex items-center justify-between">
                    <h1 class="text-gray99"><b class="mr-1">Folio</b>{{ String(sale.folio).padStart(3, '0') }}</h1>
                    <div class="flex items-center space-x-1">
                        <!-- <button class="flex items-center justify-center text-primary bg-grayED rounded-full size-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                        </button> -->
                        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?" @confirm="print(sale)">
                            <template #reference>
                                <i @click.stop class="fa-solid fa-print text-primary cursor-pointer bg-grayED rounded-full p-[6px]"></i>
                            </template>
                        </el-popconfirm>
                        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#D37E2D"
                            title="¿Continuar?" @confirm="deleteItem(sale.id)">
                            <template #reference>
                                <button
                                    class="flex items-center justify-center text-primary bg-grayED rounded-full size-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </template>
                        </el-popconfirm>
                    </div>
                </header>
                <Link :href="route('sales.show', saleId)">
                <main class="text-gray99 mt-1 *:flex *:items-center *:space-x-1 *:text-gray37">
                    <p>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span>Nombre:</span>
                        <span class="font-bold">{{ sale.client?.name }}</span>
                    </p>
                    <p>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                        </svg>
                        <span>Fecha de venta:</span>
                        <span class="font-bold">{{ sale.created_at }}</span>
                    </p>
                    <p>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                        </svg>
                        <span>Modo de pago:</span>
                        <span class="font-bold">{{ sale.has_credit ? 'A crédito' : 'Al contado' }}</span>
                    </p>
                    <p>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Total</span>
                        <span class="font-bold">${{ sale.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                    </p>
                    <p>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" />
                        </svg>
                        <span>Estatus:</span>
                        <span class="font-bold" :class="sale.status?.color">{{ sale.status?.label }}</span>
                    </p>
                    <p v-if="sale.has_credit">
                        <span>Saldo:</span>
                        <span class="font-bold">${{ pendentAmount }}</span>
                    </p>
                </main>
                </Link>
            </div>
        </div>
    </div>
</template>

<script>
import Loading from "@/Components/MyComponents/Loading.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {
        return {
            sale: null,
            loading: true,
        };
    },
    components: {
        Link,
        Loading,
    },
    props: {
        saleId: Number,
    },
    methods:{
        async fetchSale() {
            try {
                const response = await axios.get(route('sales.get-by-id-copy', this.saleId));

                if (response.status === 200) {
                    this.sale = response.data.sale;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async deleteItem(saleId) {
            try {
                const response = await axios.delete(route('sales.destroy', saleId));
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
                    message: 'No se pudo eliminar la venta. Inténte más tarde',
                    type: 'error',
                });
            }
        },
        print(sale) {
            window.open(route('sales.print-ticket', sale.id), '_blank');
        // this.$inertia.get(route('sales.print-ticket', sale.id));
        },
    },
     computed: {
        pendentAmount() {
            return this.sale.total - this.sale.payments.reduce((total, payment) => total + payment.amount, 0);
        }
    },    
    async mounted() {
        await this.fetchSale();
    }
}
</script>