<template>
    <AppLayout title="Productos">
        <div class="px-2 lg:px-10 py-7">
            <!-- tabs -->
            <div  v-if="canTransfer" class="flex justify-center">
                <ToggleButton @update="handleToggle" :labels="['Mis productos', 'Catálogo base']"
                    class="w-3/4 md:w-[45%] lg:w-[35%] xl:w-[20%]" />
            </div>
            <!-- <div v-if="canTransfer" class="flex items-center justify-center text-sm">
                <button class="text-white bg-[#E9A527] rounded-full px-5 py-1 z-10 -mr-5 cursor-default">Mis
                    productos</button>
                <button @click="$inertia.get(route('global-product-store.select'))"
                    class="text-gray99 bg-[#EDEDED] rounded-full px-6 py-1 z-0">Catálogo base</button>
            </div> -->

            <section v-if="activeTab === 'Mis productos'">
                <MyProducts :products="products" :totalProducts="total_products" />
            </section>
            <section v-else>
                <BaseCatalog />
            </section>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import MyProducts from './Tabs/MyProducts.vue';
import BaseCatalog from './Tabs/BaseCatalog.vue';
import ToggleButton from "@/Components/MyComponents/ToggleButton.vue";


export default {
    data() {
        return {
            activeTab: 'Mis productos',
            // Permisos de rol
            canTransfer: ['Administrador'].includes(this.$page.props.auth.user.rol),
        };
    },
    components: {
        AppLayout,
        ToggleButton,
        MyProducts,
        BaseCatalog,
    },
    props: {
        products: Object,
        total_products: Number,
    },
    methods: {
        handleToggle(active) {
            this.activeTab = active;
        },
    },
    mounted() {
        // // Obtener la URL actual
        // const currentURL = new URL(window.location.href);
        // // Extraer el valor de 'currentTab' de los parámetros de búsqueda
        // const currentTabFromURL = currentURL.searchParams.get('page');

        // if (currentTabFromURL) {
        //     this.currentPage = currentTabFromURL;
        //     this.fetchAllItemsForCurrentPage();
        // }

        // // resetear variable de local storage a false
        // localStorage.setItem('pendentProcess', false);
    }
}
</script>
