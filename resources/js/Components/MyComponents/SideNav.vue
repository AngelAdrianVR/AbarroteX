<template>
    <!-- sidebar -->
    <div class="h-screen hidden md:block shadow-lg relative">
        <i @click="small = false" v-if="small" class="fa-solid fa-angle-right text-center text-xs pt-[2px] text-white rounded-full size-5 bg-primary absolute top-24 -right-3 cursor-pointer hover:scale-125 transition-transform ease-linear duration-150"></i>
        <i @click="small = true" v-else class="fa-solid fa-angle-left text-center text-xs pt-[2px] text-white rounded-full size-5 bg-primary absolute top-24 -right-3 cursor-pointer hover:scale-125 transition-transform ease-linear duration-150"></i>
        <div class="bg-[#232323] h-full overflow-auto">
            <!-- Logo -->
            <div class="flex items-center justify-center mt-7">
                <Link v-if="small" :href="route('sales.index')">
                    <ApplicationMark />
                </Link>
                <Link v-else :href="route('sales.index')">
                    <figure class="">
                        <img class="w-36 px-2" src="@/../../public/images/logo.png" alt="logo">
                    </figure>
                </Link>
            </div>
            <nav class="pr-2 pt-20 text-white">
                <!-- Con barra pequeña -->
                <template v-if="small">
                    <div v-for="(menu, index) in menus" :key="index">
                        <button v-if="menu.show" @click="goToRoute(menu.route)" :active="menu.active" :title="menu.label"
                            class="w-full text-center py-2 pr-3 pl-5 justify-between rounded-r-[10px] mt-2 transition ease-linear duration-150"
                            :class="menu.active ? 'bg-[#393939] text-primary border-l-2 border-primary' : 'hover:text-primary hover:bg-[#393939] text-[#9A9A9A]'">
                            <span v-html="menu.icon"></span>
                        </button>
                    </div>
                </template>

                <!-- Con barra grande -->
                <template v-else v-for="(menu, index) in menus" :key="index">
                    <!-- Con submenues -->
                    <div v-if="menu.show">
                        <Accordion v-if="menu.options.length" :icon="menu.icon" :active="menu.active" :title="menu.label"
                            :id="index">
                            <div v-for="(option, index2) in menu.options" :key="index2">
                                <button @click="goToRoute(option.route)" v-if="option.show" :active="option.active"
                                    :title="option.label"
                                    class="w-full text-start pl-6 pr-2 mt-2 flex justify-between text-xs rounded-md py-1 transition ease-linear duration-150"
                                    :class="option.active ? 'bg-[#393939] text-primary' : 'hover:text-primary hover:bg-gradient-to-r from-gray-800 to-black1 text-gray-700'">
                                    <p class="w-full truncate"> {{ option.label }}</p>
                                </button>
                            </div>
                        </Accordion>
                        <!-- Sin submenues -->
                        <button v-else-if="menu.show" @click="goToRoute(menu.route)" :active="menu.active" :title="menu.label"
                            class="w-full text-start pl-4 pr-3 mt-2 border-l-2 flex items-center justify-between text-xs rounded-r-[10px] py-1 transition ease-linear duration-150"
                            :class="menu.active ? 'bg-[#393939] text-primary border-primary' : 'hover:text-primary border-transparent hover:bg-[#393939] text-[#9A9A9A]'">
                            <p class="w-full text-sm truncate"><span class="mr-2" v-html="menu.icon"></span> {{ menu.label }}</p>
                        </button>
                    </div>
                </template>
            </nav>
        </div>
    </div>
</template>

<script>
import Accordion from './Accordion.vue';
import { Link } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

export default {
    data() {
        return {
            small: true,
            collapsedMenu: null,
            menus: [
                {
                    label: 'Punto de venta',
                    icon: '<i class="fa-solid fa-basket-shopping text-lg"></i>',
                    route: route('sales.point'),
                    active: route().current('sales.point'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Análisis de ventas',
                    icon: '<i class="fa-solid fa-chart-simple text-lg"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboard'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Ventas registradas',
                    icon: '<i class="fa-solid fa-receipt text-lg"></i>',
                    route: route('sales.index'),
                    active: route().current('sales.index') || route().current('sales.show'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Gastos',
                    icon: '<i class="fa-solid fa-sack-xmark text-lg"></i>',
                    route: route('expenses.index'),
                    active: route().current('expenses.*'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Productos',
                    icon: '<i class="fa-regular fa-clipboard text-lg"></i>',
                    route: route('products.index'),
                    active: route().current('products.*'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Configuraciones',
                    icon: '<i class="fa-solid fa-gears text-lg"></i>',
                    route: route('settings.index'),
                    active: route().current('settings.*'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Catálogo base',
                    icon: '<i class="fa-solid fa-earth-americas text-lg"></i>',
                    route: route('global-products.index'),
                    active: route().current('global-products.*'),
                    options: [],
                    dropdown: false,
                    show: true
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
        Link
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
                this.goToRoute(this.menus[index].route)
            }
            },
            goToRoute(route) {
                this.$inertia.get(route);
            },
            logout() {
                this.$inertia.post(route('logout'));
            }
    },
    mounted() {
    }
}
</script>