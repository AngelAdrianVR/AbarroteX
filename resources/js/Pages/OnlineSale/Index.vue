<template>
    <AppLayout title="Tienda en línea">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mb-4">
                <h1 class="font-bold text-lg">Tienda en línea</h1>
                <div class="flex items-center space-x-2">
                    <div class="border-2 border-green-500 rounded-full lg:flex items-center text-sm hidden">
                        <button @click="copyText"
                            class="flex items-center py-[5px] px-4 m-[2px] bg-green-500 text-white font-bold rounded-full shadow-lg transition-transform transform hover:bg-green-600">
                            <i class="fas fa-copy mr-2"></i>
                            <span>{{ 'Copiar' }}</span>
                        </button>
                        <p class="px-2 text-gray77">{{ encodedUrlStore }}</p>
                    </div>
                    <Link :href="route('online-sales.client-index', encodedStoreId ?? 0)">
                    <ThirthButton>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4 mr-1">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                        </svg>
                        Ver tienda en línea
                    </ThirthButton>
                    </Link>
                </div>
            </div>

            <!-- Url de mi tienda en movil -->
            <div class="border-2 border-green-500 rounded-full flex items-center text-sm lg:hidden w-full">
                <button @click="copyText"
                    class="flex items-center py-[5px] px-4 m-[2px] bg-green-500 text-white font-bold rounded-full shadow-lg transition-transform transform hover:bg-green-600">
                    <i class="fas fa-copy mr-2"></i>
                    <span>{{ 'Copiar' }}</span>
                </button>
                <p class="px-2 text-gray77 truncate">{{ encodedUrlStore }}</p>
            </div>

            <!-- tabs options -->
            <el-tabs v-model="activeTab" @tab-click="updateURL">
                <el-tab-pane label="Pedidos" name="1">
                    <!-- Index de pedidos -->
                    <template #label>
                        <div class="flex items-center space-x-1">
                            <svg width="22" height="19" viewBox="0 0 22 19" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.72524 15.6484C6.77183 19.0724 1.91988 18.9401 1.89108 15.6484M14.1595 15.6484C12.0402 15.6484 10.852 15.6484 8.73279 15.6484C9.55513 10.2173 4.95498 10.1689 2.48591 12.4581M6.87279 15.6484H1.89108M1.89108 15.6484H1C0.999873 14.3808 1.60826 13.2718 2.48591 12.4581M2.48591 12.4581H1C1.10321 11.5627 1.36537 11.1234 2.41156 10.5173C2.4526 10.4935 3.19201 10.5458 3.22945 10.5173C3.60693 9.96558 3.81857 9.65626 4.19605 9.10456M11.9288 9.10456H1.14755V8.99303M11.9288 9.10456V8.99303M11.9288 9.10456C11.9954 9.03243 12.0533 8.95782 12.1029 8.8815M11.9288 9.10456C10.0014 11.2309 9.84694 12.8222 11.2597 13.3427C12.334 13.3427 12.9364 13.3427 14.0107 13.3427C14.7543 13.3427 15.5585 12.1549 15.2748 10.5916C15.0049 9.10456 12.5237 4.34592 14.903 4.12286H15.1261M11.9288 8.8815V8.99303M11.9288 8.8815H8.43421M11.9288 8.8815C11.9968 8.8815 12.0349 8.8815 12.1029 8.8815M1.14755 8.8815V8.99303M1.14755 8.8815H8.43421M1.14755 8.8815V3.23061M1.14755 8.99303H11.9288M8.43421 8.8815V6.72524M1.14755 3.23061V1.9666C1.14755 1.37177 1.59367 1 2.11414 1H7.83938C8.21115 1 8.43421 1.22306 8.43421 1.59483V6.72524M1.14755 3.23061C2.80265 3.23061 3.7306 3.23061 5.38571 3.23061M8.43421 6.72524H10.9622C12.0631 6.72524 12.7183 7.93499 12.1029 8.8815M13.1929 2.71014C14.1065 1.81999 14.6056 1.29742 15.4235 1.29742H16.7618C18.695 1.29742 18.6207 4.12286 16.7618 4.12286H16.3157M16.3157 4.12286C16.3157 3.71634 16.3157 3.48842 16.3157 3.08191M16.3157 4.12286H15.1261M15.1261 4.12286L18.2489 10.8891C19.4446 10.9319 20.0583 11.0519 20.8513 11.8556M13.1185 3.00755C13.1185 2.84329 12.9853 2.71014 12.8211 2.71014M13.1185 3.00755C13.1185 3.04556 13.1114 3.0819 13.0984 3.11531M13.1185 3.00755L12.8211 2.71014M12.5237 3.00755C12.5237 2.9675 12.5316 2.92929 12.5459 2.89441M12.5237 3.00755C12.5237 3.16428 12.6449 3.29269 12.7987 3.30414M12.5237 3.00755L12.7987 3.30414M12.8211 2.71014C12.7457 2.71014 12.6768 2.73822 12.6244 2.78449M12.6244 2.78449L13.0178 3.23061M12.6244 2.78449C12.5905 2.81441 12.5634 2.85194 12.5459 2.89441M13.0178 3.23061C13.0531 3.19943 13.081 3.15999 13.0984 3.11531M13.0178 3.23061C12.9832 3.26114 12.9415 3.28374 12.8954 3.2956M12.7467 2.71014L13.0984 3.11531M12.8954 3.2956C12.8717 3.30171 12.8468 3.30497 12.8211 3.30497C12.8136 3.30497 12.8061 3.30469 12.7987 3.30414M12.8954 3.2956L12.5459 2.89441M21 15.722C21 17.1593 19.8349 18.3244 18.3976 18.3244C16.9604 18.3244 15.7952 17.1593 15.7952 15.722C15.7952 14.2848 16.9604 13.1197 18.3976 13.1197C19.8349 13.1197 21 14.2848 21 15.722ZM20.8513 15.722C20.8513 17.0772 19.7527 18.1757 18.3976 18.1757C17.0425 18.1757 15.9439 17.0772 15.9439 15.722C15.9439 14.3669 17.0425 13.2684 18.3976 13.2684C19.7527 13.2684 20.8513 14.3669 20.8513 15.722ZM13.2672 3.00755C13.2672 3.25394 13.0675 3.45367 12.8211 3.45367C12.5747 3.45367 12.375 3.25394 12.375 3.00755C12.375 2.76116 12.5747 2.56143 12.8211 2.56143C13.0675 2.56143 13.2672 2.76116 13.2672 3.00755Z" stroke="currentColor" stroke-width="0.371769" stroke-linecap="round"/>
                            </svg>
                            <p>Pedidos</p>
                        </div>
                    </template>
                    <OnlineOrders :orders="online_orders" :totalOnlineOrders="total_online_orders" :clients="clients" />
                </el-tab-pane>
                <el-tab-pane label="Ajustes generales" name="2">
                    <!-- Configuraciones de tienda en linea -->
                    <template #label>
                        <div class="flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <p>Ajustes generales</p>
                        </div>
                    </template>
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
            encodedStoreId: null, //id de tienda codificada
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        ThirthButton,
        OnlineOrders,
        OnlineStore,
        Link
    },
    props: {
        banners: Object, //imagenes banners para tienda online
        online_orders: Array, //imagenes banners para tienda online
        logo: Object, //imagenes logo para tienda online
        total_online_orders: Number, //total de ordenes para paginación
        cash_registers: Array, //cajas registradoras de la tienda
        clients: Array //clientes de la tienda
    },
    methods: {
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
            this.encodedStoreId = encodedId;
        },
    },
    mounted() {
        this.setActiveTabFromURL();
        this.encodeUrlStore(); //codifica el id de la tienda por seguridad
    }
}
</script>
