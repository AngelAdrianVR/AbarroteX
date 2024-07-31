<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { syncIDBProducts } from '@/dbService.js';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import SideNav from '@/Components/MyComponents/SideNav.vue';
import NotificationsCenter from '@/Components/MyComponents/NotificationsCenter.vue';
import OnlineSalesNotifications from '@/Components/MyComponents/OnlineSalesNotifications.vue';
// import NavLink from '@/Components/NavLink.vue';
import axios from 'axios';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const syncInterval = ref(null);

const calculateDaysSinceStoreCreated = (date) => {
    const oneDay = 24 * 60 * 60 * 1000; // Horas * minutos * segundos * milisegundos
    const startDate = new Date(date);
    const currentDate = new Date();

    // Calcula la diferencia en días
    const diffDays = Math.round(Math.abs((currentDate - startDate) / oneDay));

    return diffDays;
};

const calculateRemainigFreeDays = (date) => {
    const trialDays = 2;
    return trialDays - calculateDaysSinceStoreCreated(date);
};

const logout = () => {
    router.post(route('logout'));
};

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
                            <div class="flex">
                                <!-- Dias de prueba escritorio y tablet -->
                                <section v-if="$page.props.auth.user.store.suscription_period == 'Periodo de prueba'"
                                    class="hidden xl:flex space-x-4 bg-[#ededed] text-gray37 px-6 py-2 rounded-[5px] text-xs">
                                    <div v-if="calculateRemainigFreeDays($page.props.auth.user.store.created_at) > 0">
                                        <p class="font-bold">Te quedan {{
                                            calculateRemainigFreeDays($page.props.auth.user.store.created_at) }}
                                            días de tu prueba gratuita</p>
                                        <p>¡Paga tu suscripción en cualquier momento! Tu pago
                                            comenzará a contar al finalizar el
                                            periodo de
                                            prueba.</p>
                                    </div>
                                    <div v-else>
                                        <p class="font-bold">Tus días de prueba han expirado</p>
                                        <p>Para continuar disfrutando de los beneficios, te invitamos a realizar el pago
                                            de
                                            tu suscripción.</p>
                                    </div>
                                    <button type="button" @click="$inertia.visit(route('profile.show'))"
                                        class="underline text-primary">
                                        Pagar suscripción
                                        <i class="fa-solid fa-arrow-right-long ml-1 text-[10px]"></i>
                                    </button>
                                </section>
                                <!-- Logo -->
                                <div class="md:hidden shrink-0 flex items-center">
                                    <Link :href="route('dashboard')">
                                    <ApplicationMark class="block h-11 w-auto" />
                                    </Link>
                                </div>
                            </div>
                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                <!-- notificaciones de tienda en linea -->
                                <OnlineSalesNotifications v-if="$page.props.auth.user.store.plan == 'Plan Intermedio'" />
                                <!-- notifications -->
                                <NotificationsCenter />

                                <!-- Settings Dropdown -->
                                <div class="ms-3 relative">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <button v-if="$page.props.jetstream.managesProfilePhotos"
                                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                                <img class="h-8 w-8 rounded-full object-cover"
                                                    :src="$page.props.auth.user.profile_photo_url"
                                                    :alt="$page.props.auth.user.store.name">
                                            </button>

                                            <span v-else class="inline-flex rounded-md">
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-4 mr-1">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                                    </svg>
                                                    {{ $page.props.auth.user.store.name }}
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
                                            <!-- <DropdownLink :href="route('internal-invoices.index')">
                                                Facturación
                                            </DropdownLink> -->
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
                                <!-- notificaciones de tienda en linea -->
                                <OnlineSalesNotifications v-if="$page.props.auth.user.store.plan == 'Plan Intermedio'" class="-mr-4" />
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
                            <ResponsiveNavLink :href="route('sales.point')" :active="route().current('sales.point')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24" height="16" width="16" id="Shopping-Basket-2--Streamline-Core"><desc>Shopping Basket 2 Streamline Icon: https://streamlinehq.com</desc><g id="shopping-basket-2--shopping-basket"><path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M19.625389714285713 7.932533357142858H2.6646102857142857C2.4237190714285712 7.927597714285714 2.184897642857143 7.976954142857143 1.9656595714285712 8.076940714285715C1.7464214999999998 8.176927285714285 1.5524984999999998 8.32499657142857 1.398219857142857 8.510162785714286C1.2439412142857142 8.695169785714285 1.1332872857142857 8.9126565 1.0745372142857144 9.146223857142857C1.0157871428571428 9.379950428571428 1.0102146428571428 9.623866714285715 1.0584565714285714 9.859822285714285L2.8252574999999998 18.693667714285713C2.900406642857143 19.061771142857143 3.1021311428571425 19.392140785714286 3.3957222857142857 19.626822642857142C3.689154214285714 19.861663714285715 4.0556655 19.986169285714286 4.431411214285714 19.978527H17.858588785714286C18.234493714285712 19.986169285714286 18.601005 19.861663714285715 18.894436928571427 19.626822642857142C19.187868857142856 19.392140785714286 19.38975257142857 19.061771142857143 19.4647425 18.693667714285713L21.231543428571428 9.859822285714285C21.279785357142856 9.623866714285715 21.274212857142857 9.379950428571428 21.215462785714287 9.146223857142857C21.156712714285714 8.9126565 21.046058785714283 8.695169785714285 20.891780142857144 8.510162785714286C20.7375015 8.32499657142857 20.5435785 8.176927285714285 20.32434042857143 8.076940714285715S19.866280928571427 7.927597714285714 19.625389714285713 7.932533357142858Z" stroke-width="1.71"></path><path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M14.357307428571428 2.3111545714285713L17.569614857142856 7.932533357142858" stroke-width="1.71"></path><path id="Vector_3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M4.720385142857142 7.932533357142858L7.932692571428571 2.3111545714285713" stroke-width="1.71"></path></g></svg>
                                <span>Punto de venta</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24" height="16" width="16" id="Graph-Bar-Increase--Streamline-Core"><desc>Graph Bar Increase Streamline Icon: https://streamlinehq.com</desc><g id="graph-bar-increase--up-product-performance-increase-arrow-graph-business-chart"><path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m1.9742412214285712 10.412709814285714 18.30965877857143 -8.326907142857143" stroke-width="1.71"></path><path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m16.860792857142858 0.7960714285714285 3.4231071428571425 1.2896357142857142 -1.2737142857142858 3.4231071428571425" stroke-width="1.71"></path><path id="Vector_3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M21.095892857142857 21.49392857142857h-3.9803571428571427v-11.145c0 -0.2111340642857143 0.08390592857142856 -0.4136227928571429 0.2330897142857143 -0.562902107142857C17.49796842857143 9.636731228571428 17.700488999999997 9.552857142857142 17.911607142857143 9.552857142857142h2.3882142857142856c0.21111814285714284 0 0.41363871428571425 0.0838740857142857 0.5629817142857143 0.2331693214285714 0.14918378571428573 0.14927931428571428 0.2330897142857143 0.35176804285714286 0.2330897142857143 0.562902107142857v11.145Z" stroke-width="1.71"></path><path id="Vector_4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M13.135178571428572 21.49392857142857h-3.9803571428571427l0 -8.756785714285714c0 -0.2111340642857143 0.0838740857142857 -0.4136227928571429 0.2331693214285714 -0.562902107142857C9.537270064285714 12.024945514285713 9.739758792857144 11.941071428571428 9.950892857142858 11.941071428571428h2.3882142857142856c0.2111340642857143 0 0.4136227928571429 0.0838740857142857 0.562902107142857 0.2331693214285714 0.1492952357142857 0.14927931428571428 0.2331693214285714 0.35176804285714286 0.2331693214285714 0.562902107142857v8.756785714285714Z" stroke-width="1.71"></path><path id="Vector_5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M5.1744642857142855 21.49392857142857H1.1941071428571428l0 -6.368571428571428c0 -0.2111340642857143 0.08387249357142856 -0.4136227928571429 0.23316454499999997 -0.562902107142857C1.5765637392857141 14.4131598 1.7790445071428573 14.329285714285714 1.9901785714285714 14.329285714285714h2.3882142857142856c0.2111340642857143 0 0.4136227928571429 0.0838740857142857 0.562902107142857 0.2331693214285714 0.1492952357142857 0.14927931428571428 0.2331693214285714 0.35176804285714286 0.2331693214285714 0.562902107142857l0 6.368571428571428Z" stroke-width="1.71"></path></g></svg>
                                <span>Reportes</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('sales.index')"
                                :active="route().current('sales.index') || route().current('sales.show')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24" height="16" width="16" id="Task-List--Streamline-Core"><desc>Task List Streamline Icon: https://streamlinehq.com</desc><g id="task-list--task-list-work"><path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M19.72665 19.72665C19.72665 20.14044792857143 19.56218164285714 20.53736914285714 19.269705 20.830005C18.976909928571427 21.122481642857142 18.580147928571428 21.286949999999997 18.166349999999998 21.286949999999997H4.12365C3.709852071428571 21.286949999999997 3.312930857142857 21.122481642857142 3.020295 20.830005C2.727659142857143 20.53736914285714 2.5633500000000002 20.14044792857143 2.5633500000000002 19.72665V2.5633500000000002C2.5633500000000002 2.1495520714285714 2.727659142857143 1.752630857142857 3.020295 1.459995C3.312930857142857 1.1675183571428571 3.709852071428571 1.00305 4.12365 1.00305H12.839199214285713C13.252997142857142 1.00305 13.649759142857143 1.1675183571428571 13.942395 1.459995L19.269705 6.787305C19.56218164285714 7.079940857142856 19.72665 7.476702857142857 19.72665 7.890500785714285V19.72665Z" stroke-width="1.71"></path><path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M12.016539 10.169812499999999H15.917289" stroke-width="1.71"></path><path id="Vector_3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M12.016539 15.582142928571427H15.917289" stroke-width="1.71"></path><path id="Vector_4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M5.982636 15.493779L7.289944500000001 16.8010875L9.468791999999999 13.750541785714285" stroke-width="1.71"></path><path id="Vector_5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M5.982636 9.983850214285713L7.289944500000001 11.291317928571427L9.468791999999999 8.240772214285714" stroke-width="1.71"></path></g></svg>
                                <span>Ventas registradas</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('expenses.index')" :active="route().current('expenses.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.565 -0.565 18 18" height="16" width="16" id="Cash-Payment-Bills--Streamline-Ultimate"><desc>Cash Payment Bills Streamline Icon: https://streamlinehq.com</desc><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M2.2289628083333333 8.990585333333334h1.7572916666666667c0.04804435416666666 -0.0018275833333333332 0.09594109583333332 0.0065371249999999995 0.14051304166666664 0.024531791666666667 0.04457194583333333 0.018064958333333332 0.08479986666666665 0.045338125 0.11802673749999999 0.0801325 0.03322687083333333 0.03472408333333333 0.05869354166666667 0.076125875 0.0747130125 0.121464 0.016019470833333334 0.045338125 0.022226225000000002 0.09355820833333332 0.018205541666666665 0.141497125v6.613742916666667c0.004020683333333334 0.047938916666666664 -0.0021860708333333334 0.096159 -0.018205541666666665 0.141497125 -0.016019470833333334 0.045338125 -0.041486141666666664 0.08673991666666667 -0.0747130125 0.121464 -0.03322687083333333 0.034794375 -0.07345479166666666 0.06206754166666666 -0.11802673749999999 0.0801325 -0.04457194583333333 0.017994666666666666 -0.0924686875 0.026359374999999997 -0.14051304166666664 0.024531791666666667h-1.7572916666666667" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m7.255505833333332 7.43967 -1.350991775 1.682079583333333c-0.08872917083333333 0.11056879166666665 -0.20030313333333333 0.20068270833333332 -0.3270811833333333 0.2640857916666666 -0.12678507916666668 0.063473375 -0.2657727916666667 0.09875979166666667 -0.4074667333333333 0.10353962499999998h-0.8322533333333333" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M4.337712808333333 14.430808875c1.5070533333333334 1.1422395833333332 2.881944275 1.909051375 3.769728025 1.909051375h4.4093962499999995c0.5342166666666667 0 0.8702108333333333 -0.0379575 1.1021733333333332 -0.7345479166666666 0.3541997083333333 -1.7781682916666666 0.5997285 -3.576229125 0.7352508333333333 -5.384341666666667 0 -0.3669225 -0.36762541666666665 -0.7345479166666666 -1.1021733333333332 -0.7345479166666666h-4.169701666666667" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.4185122083333335 8.579097916666667 6.330432354166667 0.9651045833333333c-0.007753170833333333 -0.054391691666666665 -0.0037324874999999993 -0.10981667083333334 0.011787912499999999 -0.1625213625 0.015513370833333331 -0.052704691666666664 0.04216797083333333 -0.10146180333333332 0.07815027499999999 -0.14297887333333334 0.03598933333333333 -0.04151636708333333 0.0804699 -0.0748226675 0.13043321666666666 -0.09766745916666665 0.04997034583333333 -0.022844791666666666 0.1042566 -0.03469456083333333 0.15920359583333332 -0.03474938833333333h5.309797354166666" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m9.72766375 9.489445291666666 -0.6438716666666666 -6.437999691666666c-0.005271875 -0.049183079166666664 -0.00007029166666666666 -0.09892146249999999 0.015253291666666667 -0.14596064583333332 0.015253291666666667 -0.047039183333333325 0.04034741666666666 -0.0903177625 0.07352508333333332 -0.1270100125 0.03317766666666667 -0.03669225 0.07373595833333332 -0.06596872916666667 0.11900379166666666 -0.08591047499999999 0.045267833333333334 -0.019948775 0.09419083333333333 -0.03011295 0.14367616666666666 -0.02983178333333333h4.850125c0.05110204166666667 -0.0006115375 0.10164175 0.009925183333333334 0.148245125 0.03086507083333333 0.046603374999999995 0.020946916666666666 0.08807545833333333 0.051797929166666666 0.12153429166666666 0.09040211249999999 0.033458833333333333 0.0386112125 0.05813120833333333 0.08404774583333333 0.07218954166666666 0.13315350416666666 0.014128624999999999 0.04909872916666666 0.017362041666666665 0.10068578333333333 0.009489375 0.15116222916666666l-0.9700249999999999 6.466819275" stroke-width="1.13"></path><path stroke="currentColor" d="M11.735896666666667 6.5869266458333335c-0.14557404166666665 0 -0.26359374999999996 -0.11801267916666668 -0.26359374999999996 -0.26359374999999996s0.11801970833333332 -0.26359374999999996 0.26359374999999996 -0.26359374999999996" stroke-width="1.13"></path><path stroke="currentColor" d="M11.735896666666667 6.5869266458333335c0.14564433333333332 0 0.26359374999999996 -0.11801267916666668 0.26359374999999996 -0.26359374999999996s-0.11794941666666667 -0.26359374999999996 -0.26359374999999996 -0.26359374999999996" stroke-width="1.13"></path></svg>
                                <span>Gastos</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.plan == 'Plan Intermedio'"
                                :href="route('quotes.index')" :active="route().current('quotes.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                <span>Cotizaciones</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.plan == 'Plan Intermedio'"
                                :href="route('rentals.index')" :active="route().current('rentals.*')">
                                <svg stroke="currentColor" width="16" height="16" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.32656 2.03516H1.67656C1.45776 2.03516 1.24791 2.1406 1.0932 2.32828C0.938482 2.51596 0.851562 2.77052 0.851562 3.03594V15.0453C0.851562 15.3107 0.938482 15.5653 1.0932 15.753C1.24791 15.9407 1.45776 16.0461 1.67656 16.0461H12.4016C12.6204 16.0461 12.8302 15.9407 12.9849 15.753C13.1397 15.5653 13.2266 15.3107 13.2266 15.0453V3.03594C13.2266 2.77052 13.1397 2.51596 12.9849 2.32828C12.8302 2.1406 12.6204 2.03516 12.4016 2.03516H6.62656" stroke="currentColor" stroke-width="1.0" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.62813 8.03866L4.97813 6.53816L3.32813 8.03932V1.53426C3.32805 1.46849 3.33867 1.40335 3.35937 1.34256C3.38007 1.28178 3.41044 1.22654 3.44875 1.18C3.48706 1.13346 3.53256 1.09655 3.58264 1.07136C3.63273 1.04617 3.68641 1.0332 3.74063 1.0332H6.21563C6.32502 1.0332 6.42996 1.08592 6.50729 1.17976C6.58468 1.27361 6.62813 1.40088 6.62813 1.53359V8.03866Z" stroke="currentColor" stroke-width="1.0" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.32812 13.043H9.10313" stroke="currentColor" stroke-width="1.0" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.32812 10.041H10.7531" stroke="currentColor" stroke-width="1.0" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.7523 7.03906H8.27734" stroke="currentColor" stroke-width="1.0" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span>Renta de productos</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('products.index')"
                                :active="route().current('products.*') || route().current('global-product-store.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.565 -0.565 18 18" height="16" width="16" id="Warehouse-Cart-Packages-2--Streamline-Ultimate"><desc>Warehouse Cart Packages 2 Streamline Icon: https://streamlinehq.com</desc><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M0.5313916445833333 11.598125H11.748549166666665c0.25276883333333333 0.0004217499999999999 0.49731354166666664 -0.08997333333333334 0.6890692083333333 -0.254737 0.191685375 -0.16476366666666667 0.31785891666666666 -0.39293041666666667 0.35546495833333336 -0.6428875833333333l1.3102366666666667 -8.748500833333333c0.03753575 -0.24962679583333333 0.1633578333333333 -0.4775123791666666 0.3546214583333333 -0.6422268416666667 0.19133391666666666 -0.16472149166666666 0.4353865833333333 -0.25534150833333336 0.6878039583333333 -0.2553977416666667h1.2012845833333334" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M2.1129534416666664 5.271875h3.163125s0.5271874999999999 0 0.5271874999999999 0.5271874999999999v3.163125s0 0.5271874999999999 -0.5271874999999999 0.5271874999999999h-3.163125s-0.5271874999999999 0 -0.5271874999999999 -0.5271874999999999v-3.163125s0 -0.5271874999999999 0.5271874999999999 -0.5271874999999999Z" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M6.330453441666666 3.163125H10.5479675s0.5271874999999999 0 0.5271874999999999 0.5271874999999999v5.271875s0 0.5271874999999999 -0.5271874999999999 0.5271874999999999H6.330453441666666s-0.5271874999999999 0 -0.5271874999999999 -0.5271874999999999v-5.271875s0 -0.5271874999999999 0.5271874999999999 -0.5271874999999999Z" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M1.5857659416666667 14.497656249999999c0 0.17305808333333333 0.03409145833333333 0.3444291666666666 0.10032729583333333 0.5043427083333333 0.06622880833333333 0.15991354166666666 0.1633156583333333 0.30520641666666665 0.28570047916666663 0.42758420833333327 0.12238482083333332 0.12237779166666667 0.26767066666666667 0.2194505833333333 0.42757717916666665 0.285735625 0.1599065125 0.06621475 0.331284625 0.10030620833333333 0.5043637958333334 0.10030620833333333s0.3444643125 -0.03409145833333333 0.5043637958333334 -0.10030620833333333c0.1599065125 -0.06628504166666666 0.3051993875 -0.1633578333333333 0.42758420833333327 -0.285735625 0.12238482083333332 -0.12237779166666667 0.21946464166666665 -0.26767066666666667 0.28570047916666663 -0.42758420833333327 0.06622880833333333 -0.15991354166666666 0.10032026666666667 -0.331284625 0.10032026666666667 -0.5043427083333333 0 -0.3495604583333333 -0.1388541583333333 -0.6847814166666666 -0.38602074583333335 -0.9319269166666667 -0.24716658749999998 -0.24721579166666666 -0.5824016041666666 -0.3860418333333333 -0.9319480041666667 -0.3860418333333333 -0.3495464 0 -0.6847743875 0.13882604166666668 -0.931940975 0.3860418333333333 -0.24716658749999998 0.24714550000000002 -0.38602777499999996 0.5823664583333333 -0.38602777499999996 0.9319269166666667Z" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M8.966405 14.497656249999999c0 0.3495604583333333 0.13882604166666668 0.6847814166666666 0.3860418333333333 0.9319269166666667 0.24714550000000002 0.24721579166666666 0.5823664583333333 0.3860418333333333 0.9319269166666667 0.3860418333333333 0.3495604583333333 0 0.6847814166666666 -0.13882604166666668 0.9319269166666667 -0.3860418333333333 0.24714550000000002 -0.24714550000000002 0.3860418333333333 -0.5823664583333333 0.3860418333333333 -0.9319269166666667 0 -0.3495604583333333 -0.13889633333333332 -0.6847814166666666 -0.3860418333333333 -0.9319269166666667 -0.24714550000000002 -0.24721579166666666 -0.5823664583333333 -0.3860418333333333 -0.9319269166666667 -0.3860418333333333 -0.3495604583333333 0 -0.6847814166666666 0.13882604166666668 -0.9319269166666667 0.3860418333333333 -0.24721579166666666 0.24714550000000002 -0.3860418333333333 0.5823664583333333 -0.3860418333333333 0.9319269166666667Z" stroke-width="1.13"></path></svg>
                                <span>Productos</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.plan == 'Plan Intermedio'"
                                :href="route('services.index')" :active="route().current('services.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" /></svg>
                                <span>Servicios</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.plan == 'Plan Intermedio'"
                                :href="route('clients.index')" :active="route().current('clients.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24" id="User-Check-Validate--Streamline-Core" height="16" width="16"><desc>User Check Validate Streamline Icon: https://streamlinehq.com</desc><g id="user-check-validate--actions-close-checkmark-check-geometric-human-person-single-success-up-user"><path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.960714285714285 8.756785714285714c2.1982875642857143 0 3.9803571428571427 -1.7820695785714284 3.9803571428571427 -3.9803571428571427S10.15900185 0.7960714285714285 7.960714285714285 0.7960714285714285 3.9803571428571427 2.578141007142857 3.9803571428571427 4.776428571428571 5.762426721428571 8.756785714285714 7.960714285714285 8.756785714285714Z" stroke-width="1.71"></path><path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.960714285714285 19.901785714285715H0.7960714285714285l0 -0.8631006428571428c0.012675049285714285 -1.2135312857142857 0.33272123785714286 -2.4041357142857143 0.930241307142857 -3.4604110500000003 0.5975312142857143 -1.0563231 1.4530373357142858 -1.9439586642857143 2.4866087142857145 -2.580003814285714 1.0335873 -0.6360610714285715 2.211518271428571 -0.9997542642857142 3.423759921428571 -1.0571510142857143 0.10807465714285713 -0.005110778571428572 0.2161174714285714 -0.007785578571428572 0.3240329142857143 -0.00800847857142857 0.10791544285714287 0.00022289999999999997 0.21597417857142856 0.0028977 0.32404883571428567 0.00800847857142857 1.21224165 0.057396749999999996 2.3901726214285715 0.42108994285714285 3.423759921428571 1.0571510142857143 0.5943469285714286 0.36574705714285716 1.1298004928571428 0.8146835785714285 1.5911875714285713 1.3310632714285713" stroke-width="1.71"></path><path id="Vector_3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m21.49392857142857 13.533214285714285 -6.368571428571428 7.960714285714285 -3.184285714285714 -2.3882142857142856" stroke-width="1.71"></path></g></svg>
                                <span>Clientes</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('cash-registers.index')"
                                :active="route().current('cash-registers.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" /></svg>
                                <span>Caja</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.store.plan == 'Plan Intermedio'"
                                :href="route('online-sales.index')" :active="route().current('online-sales.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" /></svg>
                                <span>Tienda en línea</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('settings.index')" :active="route().current('settings.*')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                <span>Configuraciones</span>
                            </ResponsiveNavLink>
                        </div>

                        <!-- Responsive Settings Options -->
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <div class="flex items-center px-4">
                                <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                                    <img class="h-10 w-10 rounded-full object-cover"
                                        :src="$page.props.auth.user.profile_photo_url"
                                        :alt="$page.props.auth.user.store.name">
                                </div>

                                <div>
                                    <div class="font-medium text-base text-white">
                                        {{ $page.props.auth.user.store.name }}
                                    </div>
                                    <div class="font-medium text-sm text-gray-500">
                                        {{ $page.props.auth.user.email }}
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
                                        <i class="fa-solid fa-arrow-right-from-bracket text-xs text-red-600 rotate-180"></i>
                                        <span class="text-red-600">Cerrar sesión</span>
                                    </ResponsiveNavLink>
                                </form>

                            </div>
                        </div>
                    </div>
                </nav>

                <div class="overflow-y-auto h-[calc(100vh-3rem)] bg-white">
                    <!-- Dias de prueba vista movil -->
                    <section v-if="$page.props.auth.user.store.suscription_period == 'Periodo de prueba'"
                        class="xl:hidden space-x-1 bg-[#ededed] text-gray37 px-2 py-1 text-[10px]">
                        <div v-if="calculateRemainigFreeDays($page.props.auth.user.store.created_at) > 0">
                            <p class="font-bold">
                                Te quedan {{ calculateRemainigFreeDays($page.props.auth.user.store.created_at) }}
                                días de tu prueba gratuita. <span class="font-normal">¡Paga tu suscripción en cualquier
                                    momento! Tu pago
                                    comenzará a
                                    contar al finalizar el periodo de prueba.</span>
                            </p>
                        </div>
                        <p v-else>
                            Tus días de prueba han expirado. <br>
                            Para continuar disfrutando de los beneficios, te invitamos a realizar el pago de
                            tu suscripción.
                        </p>
                        <div class="flex justify-end mt-1">
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
</template>
