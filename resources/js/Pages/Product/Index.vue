<template>
    <AppLayout title="Productos">
        <div ref="scrollContainer" style="height: 93vh; overflow-y: scroll;" @scroll="handleScroll" class="px-2 lg:px-10 py-5">
            <!-- tabs -->
            <div v-show="canTransfer && !!exist_global_products" class="flex justify-center">
                <ToggleButton id="start" ref="togglebutton" @update="handleToggle" :labels="['Mis productos', 'Catálogo base']"
                    class="w-3/4 md:w-[45%] lg:w-[35%] xl:w-[20%]" />
            </div>

            <section v-if="activeTab === 'Mis productos'">
                <i v-show="showScrollButton" @click="scrollToTop" class="fa-solid animate-bounce fa-arrow-up rounded-full bg-[#F2F2F2] text-gray9A py-3 px-[14px] fixed bottom-8 right-8 cursor-pointer transition-opacity duration-300"></i>
                <MyProducts />
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
            showScrollButton: false,
        };
    },
    components: {
        AppLayout,
        ToggleButton,
        MyProducts,
        BaseCatalog,
    },
    props: {
        exist_global_products: Number,
    },
    methods: {
        handleToggle(active) {
            this.activeTab = active;

            const tab = active == 'Mis productos' ? 'myproducts' : 'basecatalog';
            this.updateURL(tab);
        },
        updateURL(tab) {
            const params = new URLSearchParams(window.location.search);
            params.set('tab', tab);
            window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
        },
        handleScroll() {
            const container = this.$refs.scrollContainer;
            // const scrollHeight = container.scrollHeight;
            const scrollTop = container.scrollTop;
            // const clientHeight = container.clientHeight;

            // Determinar si has llegado al final de la vista
            if (scrollTop > 500) {
                this.showScrollButton = true;
            } else {
                this.showScrollButton = false;
            }
        },
        scrollToTop() {
            const section = document.getElementById('start');
            section.scrollIntoView({ behavior: 'smooth' });
        },
    },
    mounted() {
        // Obtener la URL actual
        const currentURL = new URL(window.location.href);
        // Extraer el valor de 'activeTab' de los parámetros de búsqueda
        const activeTabFromURL = currentURL.searchParams.get('tab');
        if (activeTabFromURL) {
            if (activeTabFromURL == 'basecatalog') {
                const tab = 'Catálogo base';
                this.$refs.togglebutton.toggle();
                // this.activeTab = tab;
            }
        }

        // resetear variable de local storage a false
        localStorage.setItem('pendentProcess', false);
    },
}
</script>

