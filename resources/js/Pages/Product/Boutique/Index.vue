<template>
    <AppLayout title="Productos">
        <div ref="scrollContainer" style="height: 93vh; overflow-y: scroll;" @scroll="handleScroll"
            class="px-2 lg:px-10 py-7">
            <section>
                <i v-show="showScrollButton" @click="scrollToTop"
                    class="fa-solid animate-bounce fa-arrow-up rounded-full bg-[#F2F2F2] text-gray9A py-3 px-[14px] fixed bottom-8 right-8 cursor-pointer transition-opacity duration-300"></i>
                <MyProducts />
            </section>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import MyProducts from './Tabs/MyProducts.vue';

export default {
    data() {
        return {
            // Permisos de rol
            canTransfer: ['Administrador'].includes(this.$page.props.auth.user.rol),
            showScrollButton: false,
        };
    },
    components: {
        AppLayout,
        MyProducts,
    },
    props: {
    },
    methods: {
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
        // resetear variable de local storage a false
        localStorage.setItem('pendentProcess', false);
    },
}
</script>
