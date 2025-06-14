<script setup>
import { ref, onMounted, onUnmounted, computed, reactive } from 'vue';
import { syncIDBProducts } from '@/dbService.js';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Modal from '@/Components/Modal.vue';
import SmallLoading from '@/Components/MyComponents/SmallLoading.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import axios from 'axios'
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import SideNav from '@/Components/MyComponents/SideNav.vue';
import NeonButton from '@/Components/MyComponents/NeonButton.vue';
import NotificationsCenter from '@/Components/MyComponents/NotificationsCenter.vue';
import OnlineSalesNotifications from '@/Components/MyComponents/OnlineSalesNotifications.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const syncInterval = ref(null);
const notificationsCenterRef = ref(null);
const onlineNotificationsCenterRef = ref(null);

// Modal para agregar stock por proveedor
const showInventoryModal = ref(false)
const searchQuery = ref('')
const searchLoading = ref(false)
const loadingProviders = ref(false)
const productsFound = ref([])
const providers = ref([])
const selectedProviders = ref([])
const stockUpdates = reactive({})

// lee las notificaciones
const readNotifications = () => {
    notificationsCenterRef.value?.readNotifications()
    onlineNotificationsCenterRef.value?.readNotifications()
}

// calcula dias restantes de la suscripcion a fecha de hoy
const calculateRemainingDays = (nextPayment) => {
    const oneDay = 24 * 60 * 60 * 1000; // Horas * minutos * segundos * milisegundos
    const startDate = new Date(nextPayment);
    const currentDate = new Date();

    // Calcula la diferencia en días
    const diffDays = Math.round((startDate - currentDate) / oneDay);

    return diffDays;
};

const logout = () => {
    router.post(route('logout'));
};

const handleOpenInventory = () => {
    //recupera los proveedores y abre el modal
    if (providers.value.length > 0) {
        showInventoryModal.value = true;
        return;
    } else {
        fetchProviders();
        showInventoryModal.value = true;
    }
};

const fetchProviders = async () => {
    loadingProviders.value = true
    try {
        if (!providers.value.length) {
            const response = await axios.get(route('brand.fetch-all'))
            if (response.status === 200) {
                providers.value = response.data
            } else {
                return
            }
        }
    } catch (error) {
        console.error('Error al obtener proveedores:', error)
    } finally {
        loadingProviders.value = false
    }
}

// Buscar productos
const searchProducts = async () => {
    searchLoading.value = true
    try {
        const response = await axios.get(route('products.search'), {
            params: { query: searchQuery.value }
        })
        if (response.status === 200) {
            productsFound.value = response.data.items
        }
    } catch (error) {
        console.error(error)
    } finally {
        searchLoading.value = false
    }
}

// Filtrar productos por proveedor
const filterByProvider = async () => {
    searchLoading.value = true
    try {
        const response = await axios.get(route('products.filter-by-provider'), {
            params: { providers: selectedProviders.value }
        })
        if (response.status === 200) {
            productsFound.value = response.data.items
        }
    } catch (error) {
        console.error('Error al filtrar por proveedor:', error)
    } finally {
        searchLoading.value = false
    }
}

// actualiza el stock de los productos seleccionados
const updateProductStock = async () => {
    try {
        const payload = Object.entries(stockUpdates).map(([productId, quantity]) => {
            const product = productsFound.value.find(p => p.id == productId)
            return {
                id: productId,
                quantity,
                global_product_id: product?.global_product_id ?? null,
            }
        })

        const response = await axios.post(route('products.massive-update-stock'), { updates: payload })

        if (response.status === 200) {
            ElMessage.success('Stock actualizado correctamente.')

            if (selectedProviders.value.length > 0) {
                // Si hay proveedores seleccionados, filtra los productos por proveedor
                await filterByProvider()
            } else {
                // Si no hay proveedores seleccionados, recarga todos los productos
                await searchProducts()
            }
            // Limpia los campos
        }
    } catch (error) {
        console.error('Error al actualizar el stock:', error)
        ElMessage.error('Error al actualizar el stock.')
    }
}


const providerOptions = computed(() =>
    providers.map(provider => ({
        id: provider.id,
        name: provider.name,
    }))
)

const page = usePage()

const isInventoryOn = computed(() => {
    return page.props.auth.user.store.settings.find(
        item => item.name === 'Control de inventario'
    )?.value
})

onMounted(() => {
    // sincronizacion periodica de IDB para todos los usuarios autenticados
    syncInterval.value = setInterval(() => {
        syncIDBProducts();
    }, 300000); // 5 minutos
});

onUnmounted(() => {
    // Limpiar el intervalo cuando el componente se desmonte
    clearInterval(syncInterval.value);
});

</script>

<template>
    <div>

        <Head :title="title" />

        <Banner />

        <div class="overflow-hidden h-screen md:flex bg-white">
            <!-- sidenav -->
            <aside class="col-span-2 w-auto">
                <SideNav />
            </aside>

            <!-- resto de pagina -->
            <main class="w-full">
                <nav class="bg-white border-b border-grayD9">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-12">
                            <div class="flex space-x-3">
                                <!-- Nombre de usuario loggeado -->
                                <div class="md:flex shrink-0 hidden items-center text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4 mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                    </svg>
                                    <span>{{ $page.props.auth.user.store.name }}</span>
                                </div>
                                <!-- Logo en movil -->
                                <div class="md:hidden shrink-0 flex items-center">
                                    <Link :href="route('dashboard')">
                                    <ApplicationMark class="block h-11 w-auto" />
                                    </Link>
                                </div>
                            </div>
                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                <!-- Refiere y gana -->
                                <NeonButton @click="$inertia.visit(route('referrals.index'))"
                                    class="text-sm px-3 py-1 flex items-center space-x-2 mr-5">
                                    <svg width="19" height="20" viewBox="0 0 19 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M3.53838 5.14977C1.13208 3.23977 5.59093 -2.64669 9.05757 5.06725C13.588 -3.14196 18.1882 3.30957 14.3667 5.14973C16.7642 3.09548 13.6587 -1.44344 9.05757 5.06725C6.3694 -0.664971 2.09586 2.67639 3.53838 5.14977Z"
                                            fill="#B339FF" />
                                        <path
                                            d="M9.05757 5.06725C5.59093 -2.64669 1.13208 3.23977 3.53838 5.14977C2.09586 2.67639 6.3694 -0.664971 9.05757 5.06725ZM9.05757 5.06725C13.588 -3.14197 18.1882 3.30957 14.3667 5.14973C16.7642 3.09548 13.6587 -1.44344 9.05757 5.06725Z"
                                            stroke="#B339FF" stroke-width="0.14154" />
                                        <path
                                            d="M0 9.03148V6.12991C-0.00026239 5.28067 0.0707705 5.06836 0.778472 5.06836H17.7633C18.5418 5.06836 18.5418 5.49298 18.5418 6.12991V9.03148C18.5418 9.73918 18.3295 10.0223 17.7633 10.0223H0.778472C0.0707708 10.0223 0 9.66841 0 9.03148Z"
                                            fill="#D38CFF" />
                                        <path
                                            d="M0.777344 19.2916V10.1622L17.6914 10.1621V19.2916C17.6914 19.8576 17.4083 19.9993 16.8421 19.9993H1.62658C0.989654 19.9993 0.777344 19.7868 0.777344 19.2916Z"
                                            fill="#D38CFF" />
                                        <path d="M7.21875 19.9992V10.1621H11.1111V19.9992H7.21875Z" fill="#B339FF" />
                                        <path d="M6.93359 10.0223V5.06836H11.5336V10.0223H6.93359Z" fill="#B339FF" />
                                    </svg>
                                    <span>Recomienda y gana </span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </NeonButton>

                                <section @click="handleOpenInventory" class="relative flex justify-center items-center">
                                    <div class="group flex justify-center transition-all">
                                        <!-- modal de inventario para agregar stock por proveedor -->

                                        <button class="mx-2 flex items-center justify-end text-gray-500 bg-white hover:text-gray-700 focus:outline-none rounded-[5px] p-2 mb-2 focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150 mt-[10px]">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                            </svg>
                                        </button>
                                        <span
                                            class="absolute opacity-0 group-hover:opacity-100 group-hover:translate-y-12 duration-700 text-xs">Inventario</span>
                                    </div>
                                </section>

                                <section v-if="$page.props.auth.user.store.activated_modules?.includes('Tienda en línea')" @click="readNotifications" class="relative flex justify-center items-center">
                                    <div
                                        class="group flex justify-center transition-all"
                                    >
                                        <!-- notificaciones de tienda en linea -->
                                        <OnlineSalesNotifications ref="onlineNotificationsCenterRef" />
                                        <span
                                            class="absolute opacity-0 group-hover:opacity-100 group-hover:translate-y-12 duration-700 text-xs">Pedidos</span>
                                    </div>
                                </section>

                                <section @click="readNotifications" class="relative flex justify-center items-center">
                                    <div class="group flex justify-center transition-all">
                                        <!-- notifications -->
                                        <NotificationsCenter ref="notificationsCenterRef" />
                                        <span
                                            class="absolute opacity-0 group-hover:opacity-100 group-hover:translate-y-12 duration-700 text-xs">Notificaciones</span>
                                    </div>
                                </section>

                                <!-- Settings Dropdown -->
                                <div class="ms-3 relative">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <button v-if="$page.props.jetstream.managesProfilePhotos"
                                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                                <img class="size-8 rounded-full object-cover"
                                                    :src="$page.props.auth.user.profile_photo_url"
                                                    :alt="$page.props.auth.user.name">
                                            </button>

                                            <span v-else class="inline-flex rounded-md">
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-4 mr-1">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    </svg>
                                                    {{ $page.props.auth.user.name }}
                                                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <!-- Account Management -->
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                Cuenta
                                            </div>
                                            <DropdownLink :href="route('profile.show')">
                                                Perfil y suscripción
                                            </DropdownLink>
                                            <DropdownLink :href="route('payments.index')">
                                                Facturación
                                            </DropdownLink>
                                            <DropdownLink :href="route('supports.index')">
                                                Soporte
                                            </DropdownLink>
                                            <div class="border-t border-gray-200" />

                                            <!-- Authentication -->
                                            <form @submit.prevent="logout">
                                                <DropdownLink as="button">
                                                    Cerrar sesión
                                                </DropdownLink>
                                            </form>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>

                            <!-- Hamburger -->
                            <div class="-me-2 flex items-center sm:hidden">
                                <!-- modal de inventario para agregar stock por proveedor -->
                                <button @click="handleOpenInventory"
                                    class="flex items-center justify-end ml-5 text-gray-500 bg-white hover:text-gray-700 focus:outline-none rounded-[5px] p-2 mb-2 focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150 mt-[10px]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                    </svg>
                                </button>
                                <!-- notificaciones de tienda en linea -->
                                <OnlineSalesNotifications
                                    v-if="$page.props.auth.user.store.activated_modules?.includes('Tienda en línea')"
                                    class="-mr-4" />
                                <!-- notifications -->
                                <NotificationsCenter />
                                <button
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                                    @click="showingNavigationDropdown = !showingNavigationDropdown">
                                    <svg class="size-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path
                                            :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                        <path
                                            :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Responsive Navigation Menu -->
                    <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                        class="sm:hidden bg-[#232323]">
                        <div class="pt-2 pb-3 space-y-px">
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.activated_modules?.includes('Punto de venta') &&
                                $page.props.auth.user.permissions?.includes('Punto de venta')"
                                :href="route('sales.point')" :active="route().current('sales.point')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24"
                                    height="16" width="16" id="Shopping-Basket-2--Streamline-Core">
                                    <desc>Shopping Basket 2 Streamline Icon: https://streamlinehq.com</desc>
                                    <g id="shopping-basket-2--shopping-basket">
                                        <path id="Vector" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M19.625389714285713 7.932533357142858H2.6646102857142857C2.4237190714285712 7.927597714285714 2.184897642857143 7.976954142857143 1.9656595714285712 8.076940714285715C1.7464214999999998 8.176927285714285 1.5524984999999998 8.32499657142857 1.398219857142857 8.510162785714286C1.2439412142857142 8.695169785714285 1.1332872857142857 8.9126565 1.0745372142857144 9.146223857142857C1.0157871428571428 9.379950428571428 1.0102146428571428 9.623866714285715 1.0584565714285714 9.859822285714285L2.8252574999999998 18.693667714285713C2.900406642857143 19.061771142857143 3.1021311428571425 19.392140785714286 3.3957222857142857 19.626822642857142C3.689154214285714 19.861663714285715 4.0556655 19.986169285714286 4.431411214285714 19.978527H17.858588785714286C18.234493714285712 19.986169285714286 18.601005 19.861663714285715 18.894436928571427 19.626822642857142C19.187868857142856 19.392140785714286 19.38975257142857 19.061771142857143 19.4647425 18.693667714285713L21.231543428571428 9.859822285714285C21.279785357142856 9.623866714285715 21.274212857142857 9.379950428571428 21.215462785714287 9.146223857142857C21.156712714285714 8.9126565 21.046058785714283 8.695169785714285 20.891780142857144 8.510162785714286C20.7375015 8.32499657142857 20.5435785 8.176927285714285 20.32434042857143 8.076940714285715S19.866280928571427 7.927597714285714 19.625389714285713 7.932533357142858Z"
                                            stroke-width="1.71"></path>
                                        <path id="Vector_2" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M14.357307428571428 2.3111545714285713L17.569614857142856 7.932533357142858"
                                            stroke-width="1.71"></path>
                                        <path id="Vector_3" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M4.720385142857142 7.932533357142858L7.932692571428571 2.3111545714285713"
                                            stroke-width="1.71"></path>
                                    </g>
                                </svg>
                                <span>Punto de venta</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.activated_modules?.includes('Reportes') &&
                                $page.props.auth.user.permissions?.includes('Reportes')" :href="route('dashboard')"
                                :active="route().current('dashboard')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24"
                                    height="16" width="16" id="Graph-Bar-Increase--Streamline-Core">
                                    <desc>Graph Bar Increase Streamline Icon: https://streamlinehq.com</desc>
                                    <g
                                        id="graph-bar-increase--up-product-performance-increase-arrow-graph-business-chart">
                                        <path id="Vector" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m1.9742412214285712 10.412709814285714 18.30965877857143 -8.326907142857143"
                                            stroke-width="1.71"></path>
                                        <path id="Vector_2" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m16.860792857142858 0.7960714285714285 3.4231071428571425 1.2896357142857142 -1.2737142857142858 3.4231071428571425"
                                            stroke-width="1.71"></path>
                                        <path id="Vector_3" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M21.095892857142857 21.49392857142857h-3.9803571428571427v-11.145c0 -0.2111340642857143 0.08390592857142856 -0.4136227928571429 0.2330897142857143 -0.562902107142857C17.49796842857143 9.636731228571428 17.700488999999997 9.552857142857142 17.911607142857143 9.552857142857142h2.3882142857142856c0.21111814285714284 0 0.41363871428571425 0.0838740857142857 0.5629817142857143 0.2331693214285714 0.14918378571428573 0.14927931428571428 0.2330897142857143 0.35176804285714286 0.2330897142857143 0.562902107142857v11.145Z"
                                            stroke-width="1.71"></path>
                                        <path id="Vector_4" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M13.135178571428572 21.49392857142857h-3.9803571428571427l0 -8.756785714285714c0 -0.2111340642857143 0.0838740857142857 -0.4136227928571429 0.2331693214285714 -0.562902107142857C9.537270064285714 12.024945514285713 9.739758792857144 11.941071428571428 9.950892857142858 11.941071428571428h2.3882142857142856c0.2111340642857143 0 0.4136227928571429 0.0838740857142857 0.562902107142857 0.2331693214285714 0.1492952357142857 0.14927931428571428 0.2331693214285714 0.35176804285714286 0.2331693214285714 0.562902107142857v8.756785714285714Z"
                                            stroke-width="1.71"></path>
                                        <path id="Vector_5" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5.1744642857142855 21.49392857142857H1.1941071428571428l0 -6.368571428571428c0 -0.2111340642857143 0.08387249357142856 -0.4136227928571429 0.23316454499999997 -0.562902107142857C1.5765637392857141 14.4131598 1.7790445071428573 14.329285714285714 1.9901785714285714 14.329285714285714h2.3882142857142856c0.2111340642857143 0 0.4136227928571429 0.0838740857142857 0.562902107142857 0.2331693214285714 0.1492952357142857 0.14927931428571428 0.2331693214285714 0.35176804285714286 0.2331693214285714 0.562902107142857l0 6.368571428571428Z"
                                            stroke-width="1.71"></path>
                                    </g>
                                </svg>
                                <span>Reportes</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.activated_modules?.includes('Ventas registradas') &&
                                $page.props.auth.user.permissions?.includes('Ventas registradas')"
                                :href="route('sales.index')"
                                :active="route().current('sales.index') || route().current('sales.show')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24"
                                    height="16" width="16" id="Task-List--Streamline-Core">
                                    <desc>Task List Streamline Icon: https://streamlinehq.com</desc>
                                    <g id="task-list--task-list-work">
                                        <path id="Vector" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M19.72665 19.72665C19.72665 20.14044792857143 19.56218164285714 20.53736914285714 19.269705 20.830005C18.976909928571427 21.122481642857142 18.580147928571428 21.286949999999997 18.166349999999998 21.286949999999997H4.12365C3.709852071428571 21.286949999999997 3.312930857142857 21.122481642857142 3.020295 20.830005C2.727659142857143 20.53736914285714 2.5633500000000002 20.14044792857143 2.5633500000000002 19.72665V2.5633500000000002C2.5633500000000002 2.1495520714285714 2.727659142857143 1.752630857142857 3.020295 1.459995C3.312930857142857 1.1675183571428571 3.709852071428571 1.00305 4.12365 1.00305H12.839199214285713C13.252997142857142 1.00305 13.649759142857143 1.1675183571428571 13.942395 1.459995L19.269705 6.787305C19.56218164285714 7.079940857142856 19.72665 7.476702857142857 19.72665 7.890500785714285V19.72665Z"
                                            stroke-width="1.71"></path>
                                        <path id="Vector_2" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" d="M12.016539 10.169812499999999H15.917289"
                                            stroke-width="1.71"></path>
                                        <path id="Vector_3" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" d="M12.016539 15.582142928571427H15.917289"
                                            stroke-width="1.71"></path>
                                        <path id="Vector_4" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5.982636 15.493779L7.289944500000001 16.8010875L9.468791999999999 13.750541785714285"
                                            stroke-width="1.71"></path>
                                        <path id="Vector_5" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5.982636 9.983850214285713L7.289944500000001 11.291317928571427L9.468791999999999 8.240772214285714"
                                            stroke-width="1.71"></path>
                                    </g>
                                </svg>
                                <span>Ventas registradas</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.activated_modules?.includes('Gastos') &&
                                $page.props.auth.user.permissions?.includes('Gastos')" :href="route('expenses.index')"
                                :active="route().current('expenses.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.565 -0.565 18 18"
                                    height="16" width="16" id="Cash-Payment-Bills--Streamline-Ultimate">
                                    <desc>Cash Payment Bills Streamline Icon: https://streamlinehq.com</desc>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.2289628083333333 8.990585333333334h1.7572916666666667c0.04804435416666666 -0.0018275833333333332 0.09594109583333332 0.0065371249999999995 0.14051304166666664 0.024531791666666667 0.04457194583333333 0.018064958333333332 0.08479986666666665 0.045338125 0.11802673749999999 0.0801325 0.03322687083333333 0.03472408333333333 0.05869354166666667 0.076125875 0.0747130125 0.121464 0.016019470833333334 0.045338125 0.022226225000000002 0.09355820833333332 0.018205541666666665 0.141497125v6.613742916666667c0.004020683333333334 0.047938916666666664 -0.0021860708333333334 0.096159 -0.018205541666666665 0.141497125 -0.016019470833333334 0.045338125 -0.041486141666666664 0.08673991666666667 -0.0747130125 0.121464 -0.03322687083333333 0.034794375 -0.07345479166666666 0.06206754166666666 -0.11802673749999999 0.0801325 -0.04457194583333333 0.017994666666666666 -0.0924686875 0.026359374999999997 -0.14051304166666664 0.024531791666666667h-1.7572916666666667"
                                        stroke-width="1.13"></path>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="m7.255505833333332 7.43967 -1.350991775 1.682079583333333c-0.08872917083333333 0.11056879166666665 -0.20030313333333333 0.20068270833333332 -0.3270811833333333 0.2640857916666666 -0.12678507916666668 0.063473375 -0.2657727916666667 0.09875979166666667 -0.4074667333333333 0.10353962499999998h-0.8322533333333333"
                                        stroke-width="1.13"></path>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.337712808333333 14.430808875c1.5070533333333334 1.1422395833333332 2.881944275 1.909051375 3.769728025 1.909051375h4.4093962499999995c0.5342166666666667 0 0.8702108333333333 -0.0379575 1.1021733333333332 -0.7345479166666666 0.3541997083333333 -1.7781682916666666 0.5997285 -3.576229125 0.7352508333333333 -5.384341666666667 0 -0.3669225 -0.36762541666666665 -0.7345479166666666 -1.1021733333333332 -0.7345479166666666h-4.169701666666667"
                                        stroke-width="1.13"></path>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.4185122083333335 8.579097916666667 6.330432354166667 0.9651045833333333c-0.007753170833333333 -0.054391691666666665 -0.0037324874999999993 -0.10981667083333334 0.011787912499999999 -0.1625213625 0.015513370833333331 -0.052704691666666664 0.04216797083333333 -0.10146180333333332 0.07815027499999999 -0.14297887333333334 0.03598933333333333 -0.04151636708333333 0.0804699 -0.0748226675 0.13043321666666666 -0.09766745916666665 0.04997034583333333 -0.022844791666666666 0.1042566 -0.03469456083333333 0.15920359583333332 -0.03474938833333333h5.309797354166666"
                                        stroke-width="1.13"></path>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="m9.72766375 9.489445291666666 -0.6438716666666666 -6.437999691666666c-0.005271875 -0.049183079166666664 -0.00007029166666666666 -0.09892146249999999 0.015253291666666667 -0.14596064583333332 0.015253291666666667 -0.047039183333333325 0.04034741666666666 -0.0903177625 0.07352508333333332 -0.1270100125 0.03317766666666667 -0.03669225 0.07373595833333332 -0.06596872916666667 0.11900379166666666 -0.08591047499999999 0.045267833333333334 -0.019948775 0.09419083333333333 -0.03011295 0.14367616666666666 -0.02983178333333333h4.850125c0.05110204166666667 -0.0006115375 0.10164175 0.009925183333333334 0.148245125 0.03086507083333333 0.046603374999999995 0.020946916666666666 0.08807545833333333 0.051797929166666666 0.12153429166666666 0.09040211249999999 0.033458833333333333 0.0386112125 0.05813120833333333 0.08404774583333333 0.07218954166666666 0.13315350416666666 0.014128624999999999 0.04909872916666666 0.017362041666666665 0.10068578333333333 0.009489375 0.15116222916666666l-0.9700249999999999 6.466819275"
                                        stroke-width="1.13"></path>
                                    <path stroke="currentColor"
                                        d="M11.735896666666667 6.5869266458333335c-0.14557404166666665 0 -0.26359374999999996 -0.11801267916666668 -0.26359374999999996 -0.26359374999999996s0.11801970833333332 -0.26359374999999996 0.26359374999999996 -0.26359374999999996"
                                        stroke-width="1.13"></path>
                                    <path stroke="currentColor"
                                        d="M11.735896666666667 6.5869266458333335c0.14564433333333332 0 0.26359374999999996 -0.11801267916666668 0.26359374999999996 -0.26359374999999996s-0.11794941666666667 -0.26359374999999996 -0.26359374999999996 -0.26359374999999996"
                                        stroke-width="1.13"></path>
                                </svg>
                                <span>Gastos</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.activated_modules?.includes('Cotizaciones') &&
                                $page.props.auth.user.permissions?.includes('Cotizaciones')"
                                :href="route('quotes.index')" :active="route().current('quotes.*')">
                                <svg width="18" height="15" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12.5 13C8.3995 13 6.1005 13 2 13C1 13 0.999994 12.5 1 11.5V2C1 1.5 1 1 2 1C2 1 6.35894 1 6.5 1C6.64106 1 7.5 2.5 8 2.5C8.5 2.5 15 2.5 15 2.5C15.5 2.5 16 3 16 3.5C16 4.67157 16 5.32843 16 6.5M14 13.3562L14.5127 13.7406C14.8543 13.997 15.3021 14.1252 15.75 14.1251M15.75 14.1251C16.1977 14.1251 16.6455 13.9969 16.9873 13.7406C17.6709 13.2278 17.6709 12.3972 16.9873 11.8844C16.646 11.6277 16.198 11.5 15.75 11.5C15.3271 11.5 14.9042 11.3717 14.5816 11.1156C13.9364 10.6028 13.9364 9.77217 14.5816 9.25942C14.9042 9.00304 15.3271 8.87485 15.75 8.87485M15.75 14.1251V15M15.75 8.87485C16.1729 8.87485 16.5958 9.00304 16.9184 9.25942M15.75 8.87485V8"
                                        stroke="currentColor" />
                                </svg>
                                <span>Cotizaciones</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.activated_modules?.includes('Renta de productos') &&
                                $page.props.auth.user.permissions?.includes('Renta de productos')"
                                :href="route('rentals.index')" :active="route().current('rentals.*')">
                                <svg stroke="currentColor" width="16" height="16" viewBox="0 0 15 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M3.32656 2.03516H1.67656C1.45776 2.03516 1.24791 2.1406 1.0932 2.32828C0.938482 2.51596 0.851562 2.77052 0.851562 3.03594V15.0453C0.851562 15.3107 0.938482 15.5653 1.0932 15.753C1.24791 15.9407 1.45776 16.0461 1.67656 16.0461H12.4016C12.6204 16.0461 12.8302 15.9407 12.9849 15.753C13.1397 15.5653 13.2266 15.3107 13.2266 15.0453V3.03594C13.2266 2.77052 13.1397 2.51596 12.9849 2.32828C12.8302 2.1406 12.6204 2.03516 12.4016 2.03516H6.62656"
                                        stroke="currentColor" stroke-width="1.0" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M6.62813 8.03866L4.97813 6.53816L3.32813 8.03932V1.53426C3.32805 1.46849 3.33867 1.40335 3.35937 1.34256C3.38007 1.28178 3.41044 1.22654 3.44875 1.18C3.48706 1.13346 3.53256 1.09655 3.58264 1.07136C3.63273 1.04617 3.68641 1.0332 3.74063 1.0332H6.21563C6.32502 1.0332 6.42996 1.08592 6.50729 1.17976C6.58468 1.27361 6.62813 1.40088 6.62813 1.53359V8.03866Z"
                                        stroke="currentColor" stroke-width="1.0" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M3.32812 13.043H9.10313" stroke="currentColor" stroke-width="1.0"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M3.32812 10.041H10.7531" stroke="currentColor" stroke-width="1.0"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.7523 7.03906H8.27734" stroke="currentColor" stroke-width="1.0"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>Renta de productos</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.activated_modules?.includes('Productos') &&
                                $page.props.auth.user.permissions?.includes('Productos')"
                                :href="route('products.index')"
                                :active="route().current('products.*') || route().current('global-product-store.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.565 -0.565 18 18"
                                    height="16" width="16" id="Warehouse-Cart-Packages-2--Streamline-Ultimate">
                                    <desc>Warehouse Cart Packages 2 Streamline Icon: https://streamlinehq.com</desc>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M0.5313916445833333 11.598125H11.748549166666665c0.25276883333333333 0.0004217499999999999 0.49731354166666664 -0.08997333333333334 0.6890692083333333 -0.254737 0.191685375 -0.16476366666666667 0.31785891666666666 -0.39293041666666667 0.35546495833333336 -0.6428875833333333l1.3102366666666667 -8.748500833333333c0.03753575 -0.24962679583333333 0.1633578333333333 -0.4775123791666666 0.3546214583333333 -0.6422268416666667 0.19133391666666666 -0.16472149166666666 0.4353865833333333 -0.25534150833333336 0.6878039583333333 -0.2553977416666667h1.2012845833333334"
                                        stroke-width="1.13"></path>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.1129534416666664 5.271875h3.163125s0.5271874999999999 0 0.5271874999999999 0.5271874999999999v3.163125s0 0.5271874999999999 -0.5271874999999999 0.5271874999999999h-3.163125s-0.5271874999999999 0 -0.5271874999999999 -0.5271874999999999v-3.163125s0 -0.5271874999999999 0.5271874999999999 -0.5271874999999999Z"
                                        stroke-width="1.13"></path>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.330453441666666 3.163125H10.5479675s0.5271874999999999 0 0.5271874999999999 0.5271874999999999v5.271875s0 0.5271874999999999 -0.5271874999999999 0.5271874999999999H6.330453441666666s-0.5271874999999999 0 -0.5271874999999999 -0.5271874999999999v-5.271875s0 -0.5271874999999999 0.5271874999999999 -0.5271874999999999Z"
                                        stroke-width="1.13"></path>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M1.5857659416666667 14.497656249999999c0 0.17305808333333333 0.03409145833333333 0.3444291666666666 0.10032729583333333 0.5043427083333333 0.06622880833333333 0.15991354166666666 0.1633156583333333 0.30520641666666665 0.28570047916666663 0.42758420833333327 0.12238482083333332 0.12237779166666667 0.26767066666666667 0.2194505833333333 0.42757717916666665 0.285735625 0.1599065125 0.06621475 0.331284625 0.10030620833333333 0.5043637958333334 0.10030620833333333s0.3444643125 -0.03409145833333333 0.5043637958333334 -0.10030620833333333c0.1599065125 -0.06628504166666666 0.3051993875 -0.1633578333333333 0.42758420833333327 -0.285735625 0.12238482083333332 -0.12237779166666667 0.21946464166666665 -0.26767066666666667 0.28570047916666663 -0.42758420833333327 0.06622880833333333 -0.15991354166666666 0.10032026666666667 -0.331284625 0.10032026666666667 -0.5043427083333333 0 -0.3495604583333333 -0.1388541583333333 -0.6847814166666666 -0.38602074583333335 -0.9319269166666667 -0.24716658749999998 -0.24721579166666666 -0.5824016041666666 -0.3860418333333333 -0.9319480041666667 -0.3860418333333333 -0.3495464 0 -0.6847743875 0.13882604166666668 -0.931940975 0.3860418333333333 -0.24716658749999998 0.24714550000000002 -0.38602777499999996 0.5823664583333333 -0.38602777499999996 0.9319269166666667Z"
                                        stroke-width="1.13"></path>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.966405 14.497656249999999c0 0.3495604583333333 0.13882604166666668 0.6847814166666666 0.3860418333333333 0.9319269166666667 0.24714550000000002 0.24721579166666666 0.5823664583333333 0.3860418333333333 0.9319269166666667 0.3860418333333333 0.3495604583333333 0 0.6847814166666666 -0.13882604166666668 0.9319269166666667 -0.3860418333333333 0.24714550000000002 -0.24714550000000002 0.3860418333333333 -0.5823664583333333 0.3860418333333333 -0.9319269166666667 0 -0.3495604583333333 -0.13889633333333332 -0.6847814166666666 -0.3860418333333333 -0.9319269166666667 -0.24714550000000002 -0.24721579166666666 -0.5823664583333333 -0.3860418333333333 -0.9319269166666667 -0.3860418333333333 -0.3495604583333333 0 -0.6847814166666666 0.13882604166666668 -0.9319269166666667 0.3860418333333333 -0.24721579166666666 0.24714550000000002 -0.3860418333333333 0.5823664583333333 -0.3860418333333333 0.9319269166666667Z"
                                        stroke-width="1.13"></path>
                                </svg>
                                <span>Productos</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.activated_modules?.includes('Servicios') &&
                                $page.props.auth.user.permissions?.includes('Servicios')"
                                :href="route('services.index')" :active="route().current('services.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                </svg>
                                <span>Servicios</span>
                            </ResponsiveNavLink>
                            <!-- solo para DM compresores por el momento -->
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.id == 6"
                                :href="route('service-reports.index')" :active="route().current('service-reports.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.5 -0.5 22 22"
                                    id="Website-Build--Streamline-Ultimate" height="17" width="19">
                                    <desc>Website Build Streamline Icon: https://streamlinehq.com</desc>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.125 10.5c0.11602499999999999 0 0.22732499999999997 0.0461125 0.3094 0.1281 0.0819875 0.082075 0.1281 0.193375 0.1281 0.3094V17.5c0 0.46409999999999996 -0.18436249999999998 0.9092125 -0.512575 1.237425S19.276600000000002 19.25 18.8125 19.25h-16.625c-0.46412624999999996 0 -0.9092475 -0.18436249999999998 -1.2374337499999999 -0.512575C0.6218747499999999 18.409212500000002 0.4375 17.964100000000002 0.4375 17.5V3.5c0 -0.46412624999999996 0.18437475 -0.9092475 0.51256625 -1.2374337499999999C1.2782525 1.93437125 1.72337375 1.75 2.1875 1.75H11.375c0.11602499999999999 0 0.22732499999999997 0.046095 0.3094 0.12814375 0.0819875 0.08204 0.1281 0.1933225 0.1281 0.30935625V9.625c0 0.23204999999999998 0.092225 0.45464999999999994 0.2562875 0.6187125S12.455449999999999 10.5 12.6875 10.5H20.125Z"
                                        stroke-width="1.5"></path>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M0.4375 6.125h11.375" stroke-width="1.5"></path>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.8125 7h8.75" stroke-width="1.5"></path>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.8125 3.5h8.75" stroke-width="1.5"></path>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M18.8125 1.75v8.75" stroke-width="1.5"></path>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.3125 1.75v8.75" stroke-width="1.5"></path>
                                    <path stroke="currentColor"
                                        d="M3.5 4.15625c-0.12081125 0 -0.21875 -0.09793875 -0.21875 -0.21875s0.09793875 -0.21875 0.21875 -0.21875"
                                        stroke-width="1.5"></path>
                                    <path stroke="currentColor"
                                        d="M3.5 4.15625c0.12081125 0 0.21875 -0.09793875 0.21875 -0.21875s-0.09793875 -0.21875 -0.21875 -0.21875"
                                        stroke-width="1.5"></path>
                                    <path stroke="currentColor"
                                        d="M6.125 4.15625c-0.12081125 0 -0.21875 -0.09793875 -0.21875 -0.21875s0.09793875 -0.21875 0.21875 -0.21875"
                                        stroke-width="1.5"></path>
                                    <path stroke="currentColor"
                                        d="M6.125 4.15625c0.12081125 0 0.21875 -0.09793875 0.21875 -0.21875s-0.09793875 -0.21875 -0.21875 -0.21875"
                                        stroke-width="1.5"></path>
                                    <g>
                                        <path stroke="currentColor"
                                            d="M8.75 4.15625c-0.12081125 0 -0.21875 -0.09793875 -0.21875 -0.21875s0.09793875 -0.21875 0.21875 -0.21875"
                                            stroke-width="1.5"></path>
                                        <path stroke="currentColor"
                                            d="M8.75 4.15625c0.1208375 0 0.21875 -0.09793875 0.21875 -0.21875s-0.0979125 -0.21875 -0.21875 -0.21875"
                                            stroke-width="1.5"></path>
                                    </g>
                                </svg>
                                <span>Reportes de servicio</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.activated_modules?.includes('Clientes') &&
                                $page.props.auth.user.permissions?.includes('Clientes')" :href="route('clients.index')"
                                :active="route().current('clients.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24"
                                    id="User-Check-Validate--Streamline-Core" height="16" width="16">
                                    <desc>User Check Validate Streamline Icon: https://streamlinehq.com</desc>
                                    <g
                                        id="user-check-validate--actions-close-checkmark-check-geometric-human-person-single-success-up-user">
                                        <path id="Vector" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M7.960714285714285 8.756785714285714c2.1982875642857143 0 3.9803571428571427 -1.7820695785714284 3.9803571428571427 -3.9803571428571427S10.15900185 0.7960714285714285 7.960714285714285 0.7960714285714285 3.9803571428571427 2.578141007142857 3.9803571428571427 4.776428571428571 5.762426721428571 8.756785714285714 7.960714285714285 8.756785714285714Z"
                                            stroke-width="1.71"></path>
                                        <path id="Vector_2" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M7.960714285714285 19.901785714285715H0.7960714285714285l0 -0.8631006428571428c0.012675049285714285 -1.2135312857142857 0.33272123785714286 -2.4041357142857143 0.930241307142857 -3.4604110500000003 0.5975312142857143 -1.0563231 1.4530373357142858 -1.9439586642857143 2.4866087142857145 -2.580003814285714 1.0335873 -0.6360610714285715 2.211518271428571 -0.9997542642857142 3.423759921428571 -1.0571510142857143 0.10807465714285713 -0.005110778571428572 0.2161174714285714 -0.007785578571428572 0.3240329142857143 -0.00800847857142857 0.10791544285714287 0.00022289999999999997 0.21597417857142856 0.0028977 0.32404883571428567 0.00800847857142857 1.21224165 0.057396749999999996 2.3901726214285715 0.42108994285714285 3.423759921428571 1.0571510142857143 0.5943469285714286 0.36574705714285716 1.1298004928571428 0.8146835785714285 1.5911875714285713 1.3310632714285713"
                                            stroke-width="1.71"></path>
                                        <path id="Vector_3" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m21.49392857142857 13.533214285714285 -6.368571428571428 7.960714285714285 -3.184285714285714 -2.3882142857142856"
                                            stroke-width="1.71"></path>
                                    </g>
                                </svg>
                                <span>Clientes</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.activated_modules?.includes('Caja') &&
                                $page.props.auth.user.permissions?.includes('Caja')"
                                :href="route('cash-registers.index')" :active="route().current('cash-registers.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
                                </svg>
                                <span>Caja</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.activated_modules?.includes('Tienda en línea') &&
                                $page.props.auth.user.permissions?.includes('Tienda en línea')"
                                :href="route('online-sales.index')" :active="route().current('online-sales.*')">
                                <svg width="16" height="18" viewBox="0 0 10 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4 1H2.5C2.10218 1 1.72064 1.15804 1.43934 1.43934C1.15804 1.72064 1 2.10218 1 2.5V13.5C1 13.8978 1.15804 14.2794 1.43934 14.5607C1.72064 14.842 2.10218 15 2.5 15H7.5C7.89782 15 8.27936 14.842 8.56066 14.5607C8.84196 14.2794 9 13.8978 9 13.5V2.5C9 2.10218 8.84196 1.72064 8.56066 1.43934C8.27936 1.15804 7.89782 1 7.5 1H6M4 1V2H6V1M4 1H6M4 13.5H6"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M6.48168 9.1185H3.93649C3.80702 9.1109 3.77403 9.0824 3.71987 8.9741L2.90758 6.55526H2.32995C2.00414 6.55526 2.00505 6.08594 2.32995 6.08594H3.12419C3.32275 6.08594 3.39496 6.55526 3.48521 6.55526H7.00516C7.20372 6.55526 7.29011 6.74059 7.22177 6.93433L6.73439 8.10765C6.66219 8.25206 6.60803 8.30621 6.39142 8.30621H4.00869L4.13505 8.63113H6.48168C6.82465 8.63113 6.8066 9.1185 6.48168 9.1185Z"
                                        fill="currentColor" />
                                    <path
                                        d="M4.69564 9.49821C4.69564 9.70757 4.52593 9.87728 4.31657 9.87728C4.10722 9.87728 3.9375 9.70757 3.9375 9.49821C3.9375 9.28886 4.10722 9.11914 4.31657 9.11914C4.52593 9.11914 4.69564 9.28886 4.69564 9.49821Z"
                                        fill="currentColor" />
                                    <path
                                        d="M6.5401 9.49821C6.5401 9.70757 6.37039 9.87728 6.16103 9.87728C5.95168 9.87728 5.78196 9.70757 5.78196 9.49821C5.78196 9.28886 5.95168 9.11914 6.16103 9.11914C6.37039 9.11914 6.5401 9.28886 6.5401 9.49821Z"
                                        fill="currentColor" />
                                </svg>
                                <span>Tienda en línea</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.activated_modules?.includes('Configuraciones') &&
                                $page.props.auth.user.permissions?.includes('Configuraciones')"
                                :href="route('settings.index')" :active="route().current('settings.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span>Configuraciones</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('tutorials.index')"
                                :active="route().current('tutorials.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    id="Youtube-Clip-Logo--Streamline-Logos" height="18" width="18">
                                    <desc>Youtube Clip Logo Streamline Icon: https://streamlinehq.com</desc>
                                    <path stroke="currentColor" stroke-linejoin="round"
                                        d="M1.5 12c0 -1.477 0.071 -2.87 0.164 -4.038 0.14 -1.764 1.538 -3.12 3.303 -3.243C6.663 4.6 8.98 4.5 12 4.5c3.02 0 5.337 0.1 7.033 0.219 1.765 0.123 3.163 1.48 3.303 3.243 0.093 1.169 0.164 2.56 0.164 4.038 0 1.53 -0.076 2.969 -0.174 4.163a3.374 3.374 0 0 1 -3.166 3.121c-1.713 0.117 -4.11 0.216 -7.16 0.216s-5.447 -0.099 -7.16 -0.216a3.374 3.374 0 0 1 -3.166 -3.121A51.642 51.642 0 0 1 1.5 12Z"
                                        stroke-width="1.4"></path>
                                    <path stroke="currentColor" stroke-linejoin="round" d="M10 15V9l5.5 3 -5.5 3Z"
                                        stroke-width="1.4">
                                    </path>
                                </svg>
                                <span>Tutoriales</span>
                            </ResponsiveNavLink>
                            <div class="h-px border-t border-[#505050] pb-2"></div>
                            <div
                                class="bg-white text-[#296A6B] px-2 py-1 mx-auto w-[90%] rounded-[5px] flex items-center justify-between">
                                <p class="flex items-center space-x-2">
                                    <svg width="14" height="15" viewBox="0 0 14 15" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5.05977 4.58333C5.05977 2.97619 2.38895 1.04686 0 0V5.41667C0 6.4881 2.19578 8.3558 5.05977 9.70238V4.58333Z"
                                            fill="#296A6B" />
                                        <path
                                            d="M13.1554 1.30952C9.70286 2.20238 6.0122 6.0119 6.0122 7.2619V15C6.24814 14.8761 6.37889 14.8076 6.60747 14.7024C6.81582 11.966 7.36268 10.7301 9.22664 9.04762C10.3447 7.23105 11.1886 6.41959 13.1554 5.47619V1.30952Z"
                                            fill="#296A6B" />
                                        <path
                                            d="M8.57185 13.6905L7.32179 14.3452C7.46736 12.5964 7.77634 11.832 8.63137 10.7738C8.49837 11.7538 8.45423 12.343 8.57185 13.6905Z"
                                            fill="#296A6B" />
                                        <path
                                            d="M13.1554 8.86905C12.7277 10.4821 11.933 11.4738 9.10759 13.4524C8.89584 11.8189 8.98881 11.1955 9.34569 10.1786C10.0674 8.96169 10.9512 8.52226 13.1554 8.09524V8.86905Z"
                                            fill="#296A6B" />
                                        <path
                                            d="M13.1554 7.2619C11.9268 7.49322 11.3339 7.782 10.2981 8.33333C11.1244 7.13512 11.787 6.60828 13.1554 6.07143V7.2619Z"
                                            fill="#296A6B" />
                                    </svg>
                                    <span class="text-xs">¡Controla tus finanzas gratis!</span>
                                </p>
                                <a as="button" href="https://finanzas.dtw.com.mx" target="_blank"
                                    class="rounded-full bg-[#296A6B] text-white px-3 py-1 text-xs">Registrarse</a>
                            </div>
                            <div class="h-px pb-1"></div>
                            <div @click="$inertia.visit(route('referrals.index'))"
                                class="bg-[#DCA4FE] text-[#9800F6] px-2 py-2 mx-auto w-[90%] rounded-[5px] flex items-center justify-between">
                                <p class="flex items-center space-x-2">
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M3.61665 3.58397C1.58147 1.96855 5.35264 -3.01004 8.28462 3.51418C12.1163 -3.42894 16.0071 2.02758 12.7749 3.58394C14.8027 1.84652 12.1761 -1.99237 8.28462 3.51418C6.01105 -1.33397 2.3966 1.49206 3.61665 3.58397Z"
                                            fill="#9800F6" />
                                        <path
                                            d="M8.28462 3.51418C5.35264 -3.01004 1.58147 1.96855 3.61665 3.58397C2.3966 1.49206 6.01105 -1.33397 8.28462 3.51418ZM8.28462 3.51418C12.1163 -3.42894 16.0071 2.02758 12.7749 3.58394C14.8027 1.84652 12.1761 -1.99237 8.28462 3.51418Z"
                                            stroke="#9800F6" stroke-width="0.119711" />
                                        <path
                                            d="M0.617188 6.86752V4.41345C0.616966 3.69519 0.677043 3.51562 1.2756 3.51562H15.6409C16.2993 3.51562 16.2993 3.87476 16.2993 4.41345V6.86752C16.2993 7.46607 16.1197 7.7055 15.6409 7.7055H1.2756C0.677043 7.7055 0.617188 7.40622 0.617188 6.86752Z"
                                            fill="#B133FF" />
                                        <path
                                            d="M1.28125 15.5457V7.82433L15.5867 7.82422V15.5457C15.5867 16.0244 15.3472 16.1442 14.8684 16.1442H1.99951C1.46082 16.1442 1.28125 15.9645 1.28125 15.5457Z"
                                            fill="#B133FF" />
                                        <path d="M6.71875 16.1441V7.82422H10.0108V16.1441H6.71875Z" fill="#9800F6" />
                                        <path d="M6.48438 7.7055V3.51562H10.375V7.7055H6.48438Z" fill="#9800F6" />
                                    </svg>
                                    <span class="text-xs">
                                        Recomienda y gana el 50% del pago a cada referido
                                    </span>
                                </p>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </div>
                        </div>

                        <!-- Responsive Settings Options -->
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <div class="flex items-center px-4">
                                <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                                    <img class="size-10 rounded-full object-cover"
                                        :src="$page.props.auth.user.profile_photo_url"
                                        :alt="$page.props.auth.user.name">
                                </div>
                                <div>
                                    <div class="font-medium text-sm text-white flex items-center space-x-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                        </svg>
                                        <span>{{ $page.props.auth.user.store.name }}</span>
                                    </div>
                                    <div class="font-medium text-xs text-gray-400 flex items-center space-x-1 mt-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-7">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <div class="flex flex-col">
                                            <span>{{ $page.props.auth.user.name }}</span>
                                            <span>{{ $page.props.auth.user.email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink :href="route('profile.show')"
                                    :active="route().current('profile.show')">
                                    Perfil y suscripción
                                </ResponsiveNavLink>

                                <!-- Authentication -->
                                <form method="POST" @submit.prevent="logout">
                                    <ResponsiveNavLink as="button">
                                        <i
                                            class="fa-solid fa-arrow-right-from-bracket text-xs text-red-600 rotate-180"></i>
                                        <span class="text-red-600">Cerrar sesión</span>
                                    </ResponsiveNavLink>
                                </form>

                            </div>
                        </div>
                    </div>
                </nav>

                <div class="overflow-y-auto h-[calc(100vh-3rem)]">
                    <!-- mensaje de suscripcion a punto de expirar-->
                    <section
                        v-if="calculateRemainingDays($page.props.auth.user.store.next_payment) <= 7 || $page.props.auth.user.store.suscription_period == 'Periodo de prueba'"
                        class="space-x-1 bg-[#ededed] text-gray37 px-2 py-1 text-[11px] md:text-xs lg:px-10">
                        <div v-if="calculateRemainingDays($page.props.auth.user.store.next_payment) > 0">
                            Tu suscripción expira en
                            <strong>
                                {{ calculateRemainingDays($page.props.auth.user.store.next_payment) }} días.
                            </strong> <br>
                            Para continuar disfrutando de los beneficios, te invitamos a realizar el pago de
                            tu suscripción.
                        </div>
                        <p v-else>
                            Tu suscripción <strong>ha expirado.</strong> <br>
                            Para continuar disfrutando de los beneficios, te invitamos a realizar el pago de
                            tu suscripción.
                        </p>
                        <div v-if="$page.props.auth.user.rol == 'Administrador'" class="flex justify-end mt-1">
                            <button type="button" @click="$inertia.visit(route('profile.show'))"
                                class="underline text-primary">
                                Pagar suscripción
                                <i class="fa-solid fa-arrow-right-long ml-1 text-[10px]"></i>
                            </button>
                        </div>
                    </section>
                    <slot />
                </div>
            </main>
        </div>
    </div>

    <!-- -------------- Modal de actualizacion de inventario ----------------------- -->
    <Modal :show="showInventoryModal" @close="showInventoryModal = false" maxWidth="5xl">
      <div class="p-4 relative">
        <i @click="showInventoryModal = false"
          class="fa-solid fa-xmark cursor-pointer text-sm flex items-center justify-center absolute right-5"></i>

        <h2 class="font-bold">Inventario</h2>

        <p class="text-sm">Registra entradas de productos por proveedor o de forma individual. Ideal para capturar surtidos y mantener actualizado tu inventario.</p>
        
        <SmallLoading v-if="loadingProviders" class="my-3 mx-auto" />

        <section v-else class="mt-5 py-2">
            <article class="flex justify-between items-center">
                <!-- Buscar por nombre o código del producto -->
                <div class="lg:w-1/4 relative">
                    <input v-model="searchQuery" @keyup.enter="searchProducts"
                        class="input w-full pl-9" placeholder="Buscar por nombre o código" type="search">
                    <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                </div>


                <div class="flex space-x-2 max-w-lg">
                    <el-select
                        v-model="selectedProviders"
                        multiple
                        filterable
                        allow-create
                        default-first-option
                        :reserve-keyword="false"
                        placeholder="Selecciona proveedores"
                        style="width: 100%"
                    >
                    <el-option
                        v-for="provider in providers"
                        :key="provider.id"
                        :label="provider.name"
                        :value="provider.id"
                    />
                    </el-select>
                    <el-button type="primary" @click="filterByProvider" class="!px-4 !py-2">
                        <i class="fa-solid fa-magnifying-glass mr-1"></i> Filtrar
                    </el-button>
                </div>
            </article>

            <SmallLoading v-if="searchLoading" class="my-3 mx-auto" />

            <div v-else class="overflow-auto my-7 max-h-[500px]">
                <table v-if="productsFound?.length" class="w-full table-fixed">
                    <thead>
                        <tr class="*:text-start *:pb-2 *:px-4 *:text-sm border-b border-primary">
                            <th class="w-16">Imagen</th>
                            <th class="w-32">Código</th>
                            <th class="w-44">Nombre de producto</th>
                            <th class="w-28">Proveedor</th>
                            <th class="w-28">Existencias</th>
                            <th class="w-32">Cant. a agregar</th>
                            <th class="w-28">Existencias mínimas</th>
                            <th class="w-28">Existencias Máximas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in productsFound" :key="product.id"
                            class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight">
                            <td class="rounded-s-full">
                                <img v-if="product.global_product_id ? product.global_product?.media[0]?.original_url : product.media[0]?.original_url"
                                    class="size-10 bg-white object-contain rounded-md"
                                    :src="product.global_product_id ? product.global_product?.media[0]?.original_url : product.media[0]?.original_url">
                                <div v-else
                                    class="size-10 bg-white text-gray99 rounded-md text-sm flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                </div>
                            </td>
                            <td>{{ (product.global_product_id ? product.global_product?.code : product.code) ?? '-' }}</td>
                            <td>{{ product.global_product_id ? product.global_product?.name : product.name }}</td>
                            <td>{{ (product.global_product_id ? product.global_product?.brand?.name : product.brand?.name) ?? '-' }}</td>
                            <td>
                                <p :class="product.current_stock < product.min_stock && isInventoryOn ? 'text-redDanger' : ''">
                                    {{ product.current_stock ?? '-' }}
                                    <i v-if="product.current_stock < product.min_stock && isInventoryOn"
                                        class="fa-solid fa-arrow-down mx-1 text-[11px]"></i>
                                    <span v-if="product.current_stock < product.min_stock && isInventoryOn"
                                        class="text-[11px]">Bajo
                                        stock</span>
                                </p>
                            </td>
                            <td>
                                <el-input-number
                                    size="small"
                                    v-model="stockUpdates[product.id]"
                                    :min="0"
                                    :max="999"
                                    :model-value="stockUpdates[product.id] ?? 0"
                                />
                            </td>
                            <td>{{ product.min_stock ?? '-' }}</td>
                            <td>{{ product.max_stock ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
                <el-empty v-else description="No hay productos para mostrar. Búscalos por nombre, código o proveedor" />
            </div>


            <div class="flex items-center justify-end mt-4 space-x-3">
                <CancelButton @click="showInventoryModal = false;">Cancelar</CancelButton>
                <PrimaryButton :disabled="!productsFound.length" @click="updateProductStock">Registrar entradas</PrimaryButton>
            </div>
        </section>
      </div>
    </Modal>
</template>
