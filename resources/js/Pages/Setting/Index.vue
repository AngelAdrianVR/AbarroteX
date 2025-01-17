<template>
    <AppLayout title="Configuraciones">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mb-4">
                <h1 class="font-bold text-lg">Configuraciones</h1>
            </div>

            <!-- tabs options -->
            <el-tabs v-model="activeTab" @tab-click="updateURL">
                <el-tab-pane v-if="canSeePointSettings" label="Punto de venta" name="1">
                    <template #label>
                        <div class="flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                            </svg>
                            <p>Punto de venta</p>
                        </div>
                    </template>
                    <Point />
                </el-tab-pane>
                <el-tab-pane v-if="canSeeUsers" label="Usuarios" name="2">
                    <template #label>
                        <div class="flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            <p>Usuarios</p>
                        </div>
                    </template>
                    <Users :users="users" :roles="roles" />
                </el-tab-pane>
                <el-tab-pane v-if="canSeePrinterSettings" label="Impresora" name="3">
                    <template #label>
                        <div class="flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                            </svg>
                            <p>Impresora</p>
                        </div>
                    </template>
                    <Printer />
                </el-tab-pane>
                <el-tab-pane label="Báscula" name="4">
                    <template #label>
                        <div class="flex items-center space-x-1">
                            <svg width="77" height="73" viewBox="0 0 77 73" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" class="size-4">
                                <path d="M5.83797 42.9021L4.29033 64.6428C4.29033 65.6746 4.80621 66.4115 5.46948 66.4115H69.7336C71.2636 66.4232 72.313 65.8956 72.313 64.6428L70.4706 42.9021M5.83797 42.9021H70.4706M5.83797 42.9021C2.0802 42.9021 1.04867 40.6174 2.89008 35.3849L13.871 5.09529C14.3869 3.54765 15.4923 2 17.04 2H58.0894C60.5951 2 62.0691 2.88437 63.0272 5.09529L74.8187 37.7432C75.6294 40.0279 73.6396 42.9021 70.4706 42.9021M52.2685 57.2731L51.7527 57.8626M51.9001 58.3048L52.637 57.4204L52.7107 57.3467L51.8264 58.3785M60.8174 57.2731L61.6281 58.3785M60.8174 50.0507L61.7018 51.1562M51.8264 50.0507L52.8581 51.0825M62.4388 68.6961H68.2609M8.63968 68.7698H14.0933M14.4618 52.2616V56.8309C14.4618 57.2731 14.8303 57.6415 15.2724 57.6415H36.6447C37.6765 57.6415 37.7501 57.2731 37.7501 56.8309V52.2616C37.7501 51.672 37.3817 51.672 36.6447 51.672H15.1987C14.7566 51.672 14.4618 51.7457 14.4618 52.2616ZM69.5874 67.0748C69.5874 69.3541 67.7397 71.2019 65.4604 71.2019C63.1811 71.2019 61.3333 69.3541 61.3333 67.0748H69.5874ZM15.3461 67.0748C15.3461 69.3541 13.4984 71.2019 11.2191 71.2019C8.93978 71.2019 7.09203 69.3541 7.09203 67.0748H15.3461ZM53.0792 57.8626C53.0792 58.3104 52.7163 58.6733 52.2685 58.6733C51.8208 58.6733 51.4579 58.3104 51.4579 57.8626C51.4579 57.4149 51.8208 57.052 52.2685 57.052C52.7163 57.052 53.0792 57.4149 53.0792 57.8626ZM61.9966 57.8626C61.9966 58.3104 61.6336 58.6733 61.1859 58.6733C60.7382 58.6733 60.3753 58.3104 60.3753 57.8626C60.3753 57.4149 60.7382 57.052 61.1859 57.052C61.6336 57.052 61.9966 57.4149 61.9966 57.8626ZM53.0792 50.6403C53.0792 51.088 52.7163 51.451 52.2685 51.451C51.8208 51.451 51.4579 51.088 51.4579 50.6403C51.4579 50.1926 51.8208 49.8296 52.2685 49.8296C52.7163 49.8296 53.0792 50.1926 53.0792 50.6403ZM61.9966 50.6403C61.9966 51.088 61.6336 51.451 61.1859 51.451C60.7382 51.451 60.3753 51.088 60.3753 50.6403C60.3753 50.1926 60.7382 49.8296 61.1859 49.8296C61.6336 49.8296 61.9966 50.1926 61.9966 50.6403ZM16.3042 53.4408V55.6517H35.5392V53.4408H16.3042Z" stroke="currentColor" stroke-width="2.9479"/>
                            </svg>
                            <p>Báscula</p>
                        </div>
                    </template>
                    <Scale />
                </el-tab-pane>
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
import Scale from '@/Pages/Setting/Tabs/Scale.vue';

export default {
    data() {
        return {
            activeTab: '1',
            canSeePointSettings: this.$page.props.auth.user.permissions.includes('Ver configuraciones de punto de venta'),
            canSeeUsers: this.$page.props.auth.user.permissions.includes('Ver usuarios'),
            canSeePrinterSettings: this.$page.props.auth.user.permissions.includes('Ver configuraciones de impresoras'),
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        Printer,
        Users,
        Point,
        Scale,
    },
    props: {
        users: Array,
        roles: Array,
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