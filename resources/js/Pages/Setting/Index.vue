<template>
    <AppLayout title="Configuraciones">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mb-4">
                <h1 class="font-bold text-lg">Configuraciones</h1>
            </div>

            <!-- tabs options -->
            <el-tabs v-model="activeTab" @tab-click="updateURL">
                <el-tab-pane label="Punto de venta" name="1">
                    <Point />
                </el-tab-pane>
                <el-tab-pane label="Usuarios" name="2">
                    <Users :users="users" />
                </el-tab-pane>
                <el-tab-pane label="Impresora" name="3">
                    <Printer />
                </el-tab-pane>
                <!-- <el-tab-pane label="Productos" name="4">
                    Productos
                </el-tab-pane> -->
                <!-- <el-tab-pane label="Notificaciones" name="5">
                    Notificaciones
                </el-tab-pane> -->
            </el-tabs>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Point from '@/Pages/Setting/Tabs/Point.vue';
import Users from '@/Pages/Setting/Tabs/Users.vue';
import Printer from '@/Pages/Setting/Tabs/Printer.vue';

export default {
    data() {
        return {
            activeTab: '1',
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        Printer,
        Users,
        Point
    },
    props: {
        users: Array,
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
    },
    mounted() {
        this.setActiveTabFromURL();
        // resetear variable de local storage a false
        localStorage.setItem('pendentProcess', false);
    }
}
</script>