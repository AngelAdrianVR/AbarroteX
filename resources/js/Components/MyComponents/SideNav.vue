<template>
    <!-- sidebar -->
    <div class="h-screen hidden md:block shadow-lg relative">
        <i @click="small = false" v-if="small"
            class="fa-solid fa-angle-right text-center text-xs pt-[2px] text-white rounded-full size-5 bg-primary absolute top-24 -right-3 cursor-pointer hover:scale-125 transition-transform ease-linear duration-150"></i>
        <i @click="small = true" v-else
            class="fa-solid fa-angle-left text-center text-xs pt-[2px] text-white rounded-full size-5 bg-primary absolute top-24 -right-3 cursor-pointer hover:scale-125 transition-transform ease-linear duration-150"></i>
        <div class="bg-[#232323] h-full overflow-auto">
            <!-- Logo -->
            <div class="flex items-center justify-center mt-7">
                <Link v-if="small" :href="route('sales.point')">
                <figure class="">
                    <img class="w-16 px-2 mb-[52px]" src="@/../../public/images/isologo.png" alt="logo">
                </figure>
                </Link>
                <Link v-else :href="route('sales.point')">
                <figure class="">
                    <img class="w-32 px-2 mb-8" src="@/../../public/images/white_logo.png" alt="logo">
                </figure>
                </Link>
            </div>
            <nav class="pr-2 text-white">
                <!-- Con barra pequeña -->
                <section v-if="small">
                    <div v-for="(menu, index) in menus" :key="index">
                        <button v-if="menu.show" @click="handleClickInMenu(index)" :active="menu.active"
                            :title="menu.label"
                            class="w-full text-center py-2 pr-3 pl-5 justify-between rounded-r-[10px] mt-2 transition ease-linear duration-150"
                            :class="menu.active ? 'bg-[#393939] text-primary border-l-2 border-primary' : 'hover:text-primary hover:bg-[#393939] text-[#9A9A9A]'">
                            <span v-html="menu.icon"></span>
                        </button>
                    </div>
                </section>

                <!-- Con barra grande -->
                <section v-else v-for="(menu, index) in menus" :key="index">
                    <!-- Con submenues -->
                    <div v-if="menu.show">
                        <Accordion v-if="menu.options.length" :icon="menu.icon" :active="menu.active"
                            :title="menu.label" :id="index">
                            <div v-for="(option, index2) in menu.options" :key="index2">
                                <button @click="handleClickInMenu(index)" v-if="option.show" :active="option.active"
                                    :title="option.label"
                                    class="w-full text-start pl-6 pr-2 mt-2 flex justify-between text-xs rounded-md py-1 transition ease-linear duration-150"
                                    :class="option.active ? 'bg-[#393939] text-primary' : 'hover:text-primary hover:bg-gradient-to-r from-gray-800 to-black1 text-gray-700'">
                                    <p class="w-full truncate"> {{ option.label }}</p>
                                </button>
                            </div>
                        </Accordion>
                        <!-- Sin submenues -->
                        <button v-else-if="menu.show" @click="handleClickInMenu(index)" :active="menu.active"
                            :title="menu.label"
                            class="w-full text-start pl-5 pr-3 py-2 mt-2 border-l-2 text-xs rounded-r-[10px] transition ease-linear duration-150"
                            :class="menu.active ? 'bg-[#393939] text-primary border-primary' : 'hover:text-primary border-transparent hover:bg-[#393939] text-[#9A9A9A]'">
                            <p class="w-full truncate flex items-center">
                                <span class="mr-2" v-html="menu.icon"></span>
                                <span>{{ menu.label }}</span>
                            </p>
                        </button>
                    </div>
                </section>
            </nav>
        </div>
    </div>

    <ConfirmationModal :show="showGoToRouteConfirmation" @close="showGoToRouteConfirmation = false">
        <template #title>
            <h1>Proceso pendiente</h1>
        </template>
        <template #content>
            <p>
                Tienes un proceso sin completar en esta vista. Si cambias de vista, se borrarán los cambios o
                procesos que no has finalizado. ¿Continuar de todas formas?
            </p>
        </template>
        <template #footer>
            <div class="flex items-center space-x-1">
                <CancelButton @click="showGoToRouteConfirmation = false">Cancelar</CancelButton>
                <DangerButton @click="goToRoute()">Continuar</DangerButton>
            </div>
        </template>
    </ConfirmationModal>
</template>

<script>
import Accordion from './Accordion.vue';
import { Link } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from "@/Components/DangerButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";

export default {
    data() {
        return {
            small: true,
            collapsedMenu: null,
            routeToGo: null,
            showGoToRouteConfirmation: false,
            menus: [
                {
                    label: 'Punto de venta',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24" height="22" width="22" id="Shopping-Basket-2--Streamline-Core"><desc>Shopping Basket 2 Streamline Icon: https://streamlinehq.com</desc><g id="shopping-basket-2--shopping-basket"><path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M19.625389714285713 7.932533357142858H2.6646102857142857C2.4237190714285712 7.927597714285714 2.184897642857143 7.976954142857143 1.9656595714285712 8.076940714285715C1.7464214999999998 8.176927285714285 1.5524984999999998 8.32499657142857 1.398219857142857 8.510162785714286C1.2439412142857142 8.695169785714285 1.1332872857142857 8.9126565 1.0745372142857144 9.146223857142857C1.0157871428571428 9.379950428571428 1.0102146428571428 9.623866714285715 1.0584565714285714 9.859822285714285L2.8252574999999998 18.693667714285713C2.900406642857143 19.061771142857143 3.1021311428571425 19.392140785714286 3.3957222857142857 19.626822642857142C3.689154214285714 19.861663714285715 4.0556655 19.986169285714286 4.431411214285714 19.978527H17.858588785714286C18.234493714285712 19.986169285714286 18.601005 19.861663714285715 18.894436928571427 19.626822642857142C19.187868857142856 19.392140785714286 19.38975257142857 19.061771142857143 19.4647425 18.693667714285713L21.231543428571428 9.859822285714285C21.279785357142856 9.623866714285715 21.274212857142857 9.379950428571428 21.215462785714287 9.146223857142857C21.156712714285714 8.9126565 21.046058785714283 8.695169785714285 20.891780142857144 8.510162785714286C20.7375015 8.32499657142857 20.5435785 8.176927285714285 20.32434042857143 8.076940714285715S19.866280928571427 7.927597714285714 19.625389714285713 7.932533357142858Z" stroke-width="1.71"></path><path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M14.357307428571428 2.3111545714285713L17.569614857142856 7.932533357142858" stroke-width="1.71"></path><path id="Vector_3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M4.720385142857142 7.932533357142858L7.932692571428571 2.3111545714285713" stroke-width="1.71"></path></g></svg>',
                    route: route('sales.point'),
                    active: route().current('sales.point'),
                    options: [],
                    dropdown: false,
                    show: ['Administrador', 'Cajero', 'Almacenista'].includes(this.$page.props.auth.user.rol)
                },
                {
                    label: 'Reportes',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24" height="22" width="22" id="Graph-Bar-Increase--Streamline-Core"><desc>Graph Bar Increase Streamline Icon: https://streamlinehq.com</desc><g id="graph-bar-increase--up-product-performance-increase-arrow-graph-business-chart"><path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m1.9742412214285712 10.412709814285714 18.30965877857143 -8.326907142857143" stroke-width="1.71"></path><path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m16.860792857142858 0.7960714285714285 3.4231071428571425 1.2896357142857142 -1.2737142857142858 3.4231071428571425" stroke-width="1.71"></path><path id="Vector_3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M21.095892857142857 21.49392857142857h-3.9803571428571427v-11.145c0 -0.2111340642857143 0.08390592857142856 -0.4136227928571429 0.2330897142857143 -0.562902107142857C17.49796842857143 9.636731228571428 17.700488999999997 9.552857142857142 17.911607142857143 9.552857142857142h2.3882142857142856c0.21111814285714284 0 0.41363871428571425 0.0838740857142857 0.5629817142857143 0.2331693214285714 0.14918378571428573 0.14927931428571428 0.2330897142857143 0.35176804285714286 0.2330897142857143 0.562902107142857v11.145Z" stroke-width="1.71"></path><path id="Vector_4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M13.135178571428572 21.49392857142857h-3.9803571428571427l0 -8.756785714285714c0 -0.2111340642857143 0.0838740857142857 -0.4136227928571429 0.2331693214285714 -0.562902107142857C9.537270064285714 12.024945514285713 9.739758792857144 11.941071428571428 9.950892857142858 11.941071428571428h2.3882142857142856c0.2111340642857143 0 0.4136227928571429 0.0838740857142857 0.562902107142857 0.2331693214285714 0.1492952357142857 0.14927931428571428 0.2331693214285714 0.35176804285714286 0.2331693214285714 0.562902107142857v8.756785714285714Z" stroke-width="1.71"></path><path id="Vector_5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M5.1744642857142855 21.49392857142857H1.1941071428571428l0 -6.368571428571428c0 -0.2111340642857143 0.08387249357142856 -0.4136227928571429 0.23316454499999997 -0.562902107142857C1.5765637392857141 14.4131598 1.7790445071428573 14.329285714285714 1.9901785714285714 14.329285714285714h2.3882142857142856c0.2111340642857143 0 0.4136227928571429 0.0838740857142857 0.562902107142857 0.2331693214285714 0.1492952357142857 0.14927931428571428 0.2331693214285714 0.35176804285714286 0.2331693214285714 0.562902107142857l0 6.368571428571428Z" stroke-width="1.71"></path></g></svg>',
                    route: route('dashboard'),
                    active: route().current('dashboard'),
                    options: [],
                    dropdown: false,
                    show: ['Administrador'].includes(this.$page.props.auth.user.rol)
                },
                {
                    label: 'Ventas registradas',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24" height="22" width="22" id="Task-List--Streamline-Core"><desc>Task List Streamline Icon: https://streamlinehq.com</desc><g id="task-list--task-list-work"><path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M19.72665 19.72665C19.72665 20.14044792857143 19.56218164285714 20.53736914285714 19.269705 20.830005C18.976909928571427 21.122481642857142 18.580147928571428 21.286949999999997 18.166349999999998 21.286949999999997H4.12365C3.709852071428571 21.286949999999997 3.312930857142857 21.122481642857142 3.020295 20.830005C2.727659142857143 20.53736914285714 2.5633500000000002 20.14044792857143 2.5633500000000002 19.72665V2.5633500000000002C2.5633500000000002 2.1495520714285714 2.727659142857143 1.752630857142857 3.020295 1.459995C3.312930857142857 1.1675183571428571 3.709852071428571 1.00305 4.12365 1.00305H12.839199214285713C13.252997142857142 1.00305 13.649759142857143 1.1675183571428571 13.942395 1.459995L19.269705 6.787305C19.56218164285714 7.079940857142856 19.72665 7.476702857142857 19.72665 7.890500785714285V19.72665Z" stroke-width="1.71"></path><path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M12.016539 10.169812499999999H15.917289" stroke-width="1.71"></path><path id="Vector_3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M12.016539 15.582142928571427H15.917289" stroke-width="1.71"></path><path id="Vector_4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M5.982636 15.493779L7.289944500000001 16.8010875L9.468791999999999 13.750541785714285" stroke-width="1.71"></path><path id="Vector_5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M5.982636 9.983850214285713L7.289944500000001 11.291317928571427L9.468791999999999 8.240772214285714" stroke-width="1.71"></path></g></svg>',
                    route: route('sales.index'),
                    active: route().current('sales.index') || route().current('sales.show'),
                    options: [],
                    dropdown: false,
                    show: ['Administrador', 'Cajero'].includes(this.$page.props.auth.user.rol)
                },
                {
                    label: 'Gastos',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.565 -0.565 18 18" height="22" width="22" id="Cash-Payment-Bills--Streamline-Ultimate"><desc>Cash Payment Bills Streamline Icon: https://streamlinehq.com</desc><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M2.2289628083333333 8.990585333333334h1.7572916666666667c0.04804435416666666 -0.0018275833333333332 0.09594109583333332 0.0065371249999999995 0.14051304166666664 0.024531791666666667 0.04457194583333333 0.018064958333333332 0.08479986666666665 0.045338125 0.11802673749999999 0.0801325 0.03322687083333333 0.03472408333333333 0.05869354166666667 0.076125875 0.0747130125 0.121464 0.016019470833333334 0.045338125 0.022226225000000002 0.09355820833333332 0.018205541666666665 0.141497125v6.613742916666667c0.004020683333333334 0.047938916666666664 -0.0021860708333333334 0.096159 -0.018205541666666665 0.141497125 -0.016019470833333334 0.045338125 -0.041486141666666664 0.08673991666666667 -0.0747130125 0.121464 -0.03322687083333333 0.034794375 -0.07345479166666666 0.06206754166666666 -0.11802673749999999 0.0801325 -0.04457194583333333 0.017994666666666666 -0.0924686875 0.026359374999999997 -0.14051304166666664 0.024531791666666667h-1.7572916666666667" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m7.255505833333332 7.43967 -1.350991775 1.682079583333333c-0.08872917083333333 0.11056879166666665 -0.20030313333333333 0.20068270833333332 -0.3270811833333333 0.2640857916666666 -0.12678507916666668 0.063473375 -0.2657727916666667 0.09875979166666667 -0.4074667333333333 0.10353962499999998h-0.8322533333333333" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M4.337712808333333 14.430808875c1.5070533333333334 1.1422395833333332 2.881944275 1.909051375 3.769728025 1.909051375h4.4093962499999995c0.5342166666666667 0 0.8702108333333333 -0.0379575 1.1021733333333332 -0.7345479166666666 0.3541997083333333 -1.7781682916666666 0.5997285 -3.576229125 0.7352508333333333 -5.384341666666667 0 -0.3669225 -0.36762541666666665 -0.7345479166666666 -1.1021733333333332 -0.7345479166666666h-4.169701666666667" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.4185122083333335 8.579097916666667 6.330432354166667 0.9651045833333333c-0.007753170833333333 -0.054391691666666665 -0.0037324874999999993 -0.10981667083333334 0.011787912499999999 -0.1625213625 0.015513370833333331 -0.052704691666666664 0.04216797083333333 -0.10146180333333332 0.07815027499999999 -0.14297887333333334 0.03598933333333333 -0.04151636708333333 0.0804699 -0.0748226675 0.13043321666666666 -0.09766745916666665 0.04997034583333333 -0.022844791666666666 0.1042566 -0.03469456083333333 0.15920359583333332 -0.03474938833333333h5.309797354166666" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m9.72766375 9.489445291666666 -0.6438716666666666 -6.437999691666666c-0.005271875 -0.049183079166666664 -0.00007029166666666666 -0.09892146249999999 0.015253291666666667 -0.14596064583333332 0.015253291666666667 -0.047039183333333325 0.04034741666666666 -0.0903177625 0.07352508333333332 -0.1270100125 0.03317766666666667 -0.03669225 0.07373595833333332 -0.06596872916666667 0.11900379166666666 -0.08591047499999999 0.045267833333333334 -0.019948775 0.09419083333333333 -0.03011295 0.14367616666666666 -0.02983178333333333h4.850125c0.05110204166666667 -0.0006115375 0.10164175 0.009925183333333334 0.148245125 0.03086507083333333 0.046603374999999995 0.020946916666666666 0.08807545833333333 0.051797929166666666 0.12153429166666666 0.09040211249999999 0.033458833333333333 0.0386112125 0.05813120833333333 0.08404774583333333 0.07218954166666666 0.13315350416666666 0.014128624999999999 0.04909872916666666 0.017362041666666665 0.10068578333333333 0.009489375 0.15116222916666666l-0.9700249999999999 6.466819275" stroke-width="1.13"></path><path stroke="currentColor" d="M11.735896666666667 6.5869266458333335c-0.14557404166666665 0 -0.26359374999999996 -0.11801267916666668 -0.26359374999999996 -0.26359374999999996s0.11801970833333332 -0.26359374999999996 0.26359374999999996 -0.26359374999999996" stroke-width="1.13"></path><path stroke="currentColor" d="M11.735896666666667 6.5869266458333335c0.14564433333333332 0 0.26359374999999996 -0.11801267916666668 0.26359374999999996 -0.26359374999999996s-0.11794941666666667 -0.26359374999999996 -0.26359374999999996 -0.26359374999999996" stroke-width="1.13"></path></svg>',
                    route: route('expenses.index'),
                    active: route().current('expenses.*'),
                    options: [],
                    dropdown: false,
                    show: ['Administrador'].includes(this.$page.props.auth.user.rol)
                },
                {
                    label: 'Cotizaciones',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>',
                    route: route('quotes.index'),
                    active: route().current('quotes.*'),
                    options: [],
                    dropdown: false,
                    show: ['Administrador'].includes(this.$page.props.auth.user.rol)
                },
                {
                    label: 'Productos',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.565 -0.565 18 18" height="22" width="22" id="Warehouse-Cart-Packages-2--Streamline-Ultimate"><desc>Warehouse Cart Packages 2 Streamline Icon: https://streamlinehq.com</desc><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M0.5313916445833333 11.598125H11.748549166666665c0.25276883333333333 0.0004217499999999999 0.49731354166666664 -0.08997333333333334 0.6890692083333333 -0.254737 0.191685375 -0.16476366666666667 0.31785891666666666 -0.39293041666666667 0.35546495833333336 -0.6428875833333333l1.3102366666666667 -8.748500833333333c0.03753575 -0.24962679583333333 0.1633578333333333 -0.4775123791666666 0.3546214583333333 -0.6422268416666667 0.19133391666666666 -0.16472149166666666 0.4353865833333333 -0.25534150833333336 0.6878039583333333 -0.2553977416666667h1.2012845833333334" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M2.1129534416666664 5.271875h3.163125s0.5271874999999999 0 0.5271874999999999 0.5271874999999999v3.163125s0 0.5271874999999999 -0.5271874999999999 0.5271874999999999h-3.163125s-0.5271874999999999 0 -0.5271874999999999 -0.5271874999999999v-3.163125s0 -0.5271874999999999 0.5271874999999999 -0.5271874999999999Z" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M6.330453441666666 3.163125H10.5479675s0.5271874999999999 0 0.5271874999999999 0.5271874999999999v5.271875s0 0.5271874999999999 -0.5271874999999999 0.5271874999999999H6.330453441666666s-0.5271874999999999 0 -0.5271874999999999 -0.5271874999999999v-5.271875s0 -0.5271874999999999 0.5271874999999999 -0.5271874999999999Z" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M1.5857659416666667 14.497656249999999c0 0.17305808333333333 0.03409145833333333 0.3444291666666666 0.10032729583333333 0.5043427083333333 0.06622880833333333 0.15991354166666666 0.1633156583333333 0.30520641666666665 0.28570047916666663 0.42758420833333327 0.12238482083333332 0.12237779166666667 0.26767066666666667 0.2194505833333333 0.42757717916666665 0.285735625 0.1599065125 0.06621475 0.331284625 0.10030620833333333 0.5043637958333334 0.10030620833333333s0.3444643125 -0.03409145833333333 0.5043637958333334 -0.10030620833333333c0.1599065125 -0.06628504166666666 0.3051993875 -0.1633578333333333 0.42758420833333327 -0.285735625 0.12238482083333332 -0.12237779166666667 0.21946464166666665 -0.26767066666666667 0.28570047916666663 -0.42758420833333327 0.06622880833333333 -0.15991354166666666 0.10032026666666667 -0.331284625 0.10032026666666667 -0.5043427083333333 0 -0.3495604583333333 -0.1388541583333333 -0.6847814166666666 -0.38602074583333335 -0.9319269166666667 -0.24716658749999998 -0.24721579166666666 -0.5824016041666666 -0.3860418333333333 -0.9319480041666667 -0.3860418333333333 -0.3495464 0 -0.6847743875 0.13882604166666668 -0.931940975 0.3860418333333333 -0.24716658749999998 0.24714550000000002 -0.38602777499999996 0.5823664583333333 -0.38602777499999996 0.9319269166666667Z" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M8.966405 14.497656249999999c0 0.3495604583333333 0.13882604166666668 0.6847814166666666 0.3860418333333333 0.9319269166666667 0.24714550000000002 0.24721579166666666 0.5823664583333333 0.3860418333333333 0.9319269166666667 0.3860418333333333 0.3495604583333333 0 0.6847814166666666 -0.13882604166666668 0.9319269166666667 -0.3860418333333333 0.24714550000000002 -0.24714550000000002 0.3860418333333333 -0.5823664583333333 0.3860418333333333 -0.9319269166666667 0 -0.3495604583333333 -0.13889633333333332 -0.6847814166666666 -0.3860418333333333 -0.9319269166666667 -0.24714550000000002 -0.24721579166666666 -0.5823664583333333 -0.3860418333333333 -0.9319269166666667 -0.3860418333333333 -0.3495604583333333 0 -0.6847814166666666 0.13882604166666668 -0.9319269166666667 0.3860418333333333 -0.24721579166666666 0.24714550000000002 -0.3860418333333333 0.5823664583333333 -0.3860418333333333 0.9319269166666667Z" stroke-width="1.13"></path></svg>',
                    route: route('products.index'),
                    active: route().current('products.*') || route().current('global-product-store.*'),
                    options: [],
                    dropdown: false,
                    show: ['Administrador', 'Cajero', 'Almacenista'].includes(this.$page.props.auth.user.rol)
                },
                {
                    label: 'Servicios',
                    icon: '<svg stroke="currentColor" width="25" height="25" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path stroke="currentColor" d="M8 6.81671V8.14825L9.12432 8.28841C9.47568 8.32345 9.51081 8.39353 9.51081 8.81402C9.64715 9.44223 9.75349 9.72861 10.1432 10.1806C10.4243 10.4259 10.3892 10.496 10.1432 10.7763L9.44054 11.6173L10.4243 12.5633L11.3027 11.8976C11.6189 11.6173 11.6449 11.6664 11.8649 11.8625C12.3871 12.2411 12.7103 12.37 13.3405 12.4933C13.6919 12.4933 13.7622 12.5633 13.7622 13.0189L13.8324 14H15.2027L15.3432 12.8787C15.4135 12.4933 15.4135 12.4933 15.8 12.4933C16.4681 12.3763 16.7113 12.2258 17.1703 11.9326C17.4514 11.7224 17.5216 11.6523 17.8027 11.9326L18.6459 12.5633L19.6297 11.5822L18.9622 10.7412C18.7865 10.496 18.6811 10.4609 18.9622 10.1456C19.3154 9.61741 19.4214 9.30261 19.5243 8.74394C19.5548 8.41817 19.6652 8.32881 19.9811 8.28841L21 8.18329V6.78167L19.9811 6.67655C19.5595 6.67655 19.5595 6.60647 19.4892 6.1159C19.3796 5.60772 19.252 5.33532 18.927 4.88949C18.7162 4.60916 18.6811 4.57412 18.927 4.25876L19.5595 3.38275L18.6108 2.43666L17.7324 3.13747C17.4514 3.34771 17.3811 3.31267 17.1 3.06739C16.5216 2.71359 16.2227 2.59539 15.6595 2.50674C15.3081 2.43666 15.3081 2.43666 15.3081 1.98113L15.2378 1H13.7973L13.727 1.98113C13.727 2.43666 13.6568 2.43666 13.2351 2.50674C12.6727 2.64096 12.3717 2.75608 11.9 3.06739C11.6189 3.27763 11.5838 3.31267 11.1622 2.9973L10.3541 2.43666L9.40541 3.38275L10.073 4.22372C10.2486 4.469 10.2838 4.57412 10.073 4.85445C9.7612 5.40386 9.64731 5.71422 9.51081 6.25606C9.40541 6.57143 9.51081 6.64151 9.01892 6.71159L8 6.81671Z" stroke="#F68C0F" stroke-width="0.8"/><path stroke="currentColor" d="M11 7.50042C11 12.5 18 12.3681 18 7.50042C18 2.63274 11 2.72135 11 7.50042Z" stroke="#F68C0F" stroke-width="0.8"/><path stroke="currentColor" d="M16.9282 6.59882L14.3881 9L13.0991 7.85471C12.7959 7.52224 13.2506 7.042 13.6298 7.33755L14.3881 8.03943L16.3216 6.08164C16.6628 5.82296 17.1936 6.22933 16.9282 6.59882Z" fill="#F68C0F"/><path stroke="currentColor" d="M1.82759 14H1V20H1.82759C2.24138 20 2.5 19.8255 2.5 19.4545V14.5018C2.5 14.1091 2.31897 14 1.82759 14Z" stroke="#F68C0F" stroke-width="0.8"/><path stroke="currentColor" d="M4 19.0295V15.241C6.1939 14.9113 7.23835 14.9033 8.67458 15.3204L13.7421 17.3039C14.5902 17.5617 14.2386 18.7122 13.225 18.3948L9.62604 17.3039C9.31578 17.165 9.08825 17.5022 9.46057 17.6609L13.225 18.7915C14.0321 18.8803 14.3179 18.6759 14.5902 17.9386L19.3268 16.6295C20.4437 16.213 20.9815 17.4427 19.9473 17.9386C18.9131 18.4345 14.4661 20.4775 14.4661 20.4775C13.4484 20.98 12.9374 21.0725 12.1288 20.9535L6.95781 19.1485C5.85692 18.858 5.20413 18.8356 4 19.0295Z" stroke="#F68C0F" stroke-width="0.8"/></svg>',
                    route: route('services.index'),
                    active: route().current('services.*'),
                    options: [],
                    dropdown: false,
                    show: ['Administrador'].includes(this.$page.props.auth.user.rol)
                },
                {
                    label: 'Clientes',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>',
                    route: route('clients.index'),
                    active: route().current('clients.*'),
                    options: [],
                    dropdown: false,
                    show: ['Administrador', 'Cajero'].includes(this.$page.props.auth.user.rol)
                },
                {
                    label: 'Caja',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" /></svg>',
                    route: route('cash-registers.index'),
                    active: route().current('cash-registers.*'),
                    options: [],
                    dropdown: false,
                    show: ['Administrador', 'Cajero'].includes(this.$page.props.auth.user.rol)
                },
                {
                    label: 'Tienda en línea',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" /></svg>',
                    route: route('online-sales.index'),
                    active: route().current('online-sales.*'),
                    options: [],
                    dropdown: false,
                    show: ['Administrador'].includes(this.$page.props.auth.user.rol)
                },
                {
                    label: 'Configuraciones',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>',
                    route: route('settings.index'),
                    active: route().current('settings.*'),
                    options: [],
                    dropdown: false,
                    show: ['Administrador'].includes(this.$page.props.auth.user.rol)
                },

                //ejemplo para usar submenues
                //     label: 'Comunidad',
                //     icon: '<i class="fa-solid fa-people-roof text-sm mr-2"></i>',
                //     // route: route('posts.index'),
                //     active: route().current('posts.*') || route().current('community-events.*')|| route().current('neighbors.*'),
                //     options: [
                //         {
                //             label: 'Muro de noticias',
                //             route: route('posts.index'),
                //             show: true,
                //         },
                //         {
                //             label: 'Eventos',
                //             route: route('community-events.index'),
                //             show: true,
                //         },
                //         {
                //             label: 'Directorio de vecinos',
                //             route: route('neighbors.index'),
                //             show: true,
                //         },
                //     ],
                //     dropdown: true,
                //     show: true
                // },
            ],
        }
    },
    components: {
        ApplicationMark,
        Accordion,
        DropdownLink,
        Dropdown,
        Link,
        ConfirmationModal,
        DangerButton,
        CancelButton,
    },
    methods: {
        handleClickInMenu(index) {
            if (this.menus[index].options.length) {
                if (this.collapsedMenu === index) {
                    this.collapsedMenu = null;
                } else {
                    this.collapsedMenu = index;
                }
            } else {
                // revisar si hay proceso pendiente para no cambiar de vista sin preguntar
                const pendentProcess = JSON.parse(localStorage.getItem('pendentProcess'));
                if (pendentProcess) {
                    this.routeToGo = this.menus[index].route;
                    this.showGoToRouteConfirmation = true;
                } else {
                    this.goToRoute(this.menus[index].route)
                }
            }
        },
        goToRoute(route = null) {
            // resetear variable de local storage a false
            localStorage.setItem('pendentProcess', false);

            // ir a la ruta solicitada
            if (route) {
                this.$inertia.get(route);
            } else {
                this.$inertia.get(this.routeToGo);
            }
        },
        logout() {
            this.$inertia.post(route('logout'));
        }
    },
    mounted() {
    }
}
</script>