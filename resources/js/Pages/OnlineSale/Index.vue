<template>
    <AppLayout title="Tienda en línea">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mb-4">
                <h1 class="font-bold text-lg">Tienda en línea</h1>
                <div class="flex items-center space-x-2">
                    <div class="border-2 border-green-500 rounded-full lg:flex items-center text-sm hidden">
                        <button @click="copyText" class="flex items-center py-[5px] px-4 m-[2px] bg-green-500 text-white font-bold rounded-full shadow-lg transition-transform transform hover:bg-green-600">
                            <i class="fas fa-copy mr-2"></i>
                            <span>{{ 'Copiar' }}</span>
                        </button>
                        <p class="px-2 text-gray77">{{ encodedUrlStore }}</p>
                    </div>
                    <Link :href="route('online-sales.client-index', $page.props.auth.user.store_id)">
                        <ThirthButton>
                            <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4 mr-1"
                            >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"
                            />
                            </svg>
                            Ver tienda en línea
                        </ThirthButton>
                    </Link>
                </div>
            </div>

            <!-- Url de mi tienda en movil -->
            <div class="border-2 border-green-500 rounded-full flex items-center text-sm lg:hidden w-full">
                <button @click="copyText" class="flex items-center py-[5px] px-4 m-[2px] bg-green-500 text-white font-bold rounded-full shadow-lg transition-transform transform hover:bg-green-600">
                    <i class="fas fa-copy mr-2"></i>
                    <span>{{ 'Copiar' }}</span>
                </button>
                <p class="px-2 text-gray77 truncate">{{ encodedUrlStore }}</p>
            </div>

            <!-- tabs options -->
            <el-tabs v-model="activeTab" @tab-click="updateURL">
                <el-tab-pane label="Pedidos" name="1">
                    <OnlineOrders :orders="online_orders" :totalOnlineOrders="total_online_orders" />
                </el-tab-pane>
                <el-tab-pane label="Ajustes generales" name="2">
                    <OnlineStore :banners="banners" :logo="logo" :cash_registers="cash_registers" />
                </el-tab-pane>
            </el-tabs>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import OnlineStore from '@/Pages/OnlineSale/Tabs/OnlineStore.vue';
import OnlineOrders from '@/Pages/OnlineSale/Tabs/OnlineOrders.vue';
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
data() {
    return {
        activeTab: '1',
        encodedUrlStore: null, //url codificada de mi tienda
    }
},
components:{
AppLayout,
PrimaryButton,
ThirthButton,
OnlineOrders,
OnlineStore,
Link
},
props:{
banners: Object, //imagenes banners para tienda online
online_orders: Array, //imagenes banners para tienda online
logo: Object, //imagenes logo para tienda online
total_online_orders: Number, //total de ordenes para paginación
cash_registers: Array //cajas registradoras de la tienda
},
methods:{
    updateURL(tab) {
        const params = new URLSearchParams(window.location.search);
        params.set('tab', tab.props.name);
        window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
    },
    setActiveTabFromURL() {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab');
        if (tab) {
            this.activeTab = tab;
        }
    },
    copyText() {
      navigator.clipboard.writeText(this.encodedUrlStore).then(() => {
        ElMessage({
          message: 'Url copiado al portapapeles',
          type: 'success',
        });
      }).catch(err => {
        console.error('Error al copiar texto: ', err);
      });
    },
    encodeUrlStore() {
      const baseUrl = 'https://ezyventas.com/online-sales-client-index/';
      const encodedId = btoa(this.$page.props.auth.user.store_id.toString());
      this.encodedUrlStore = baseUrl + encodedId;
    },
},
mounted() {
    this.setActiveTabFromURL();
    this.encodeUrlStore(); //codifica el id de la tienda por seguridad
}
}
</script>

