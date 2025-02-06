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
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24"
                                height="18" width="18" id="Shopping-Basket-2--Streamline-Core">
                                <desc>Shopping Basket 2 Streamline Icon: https://streamlinehq.com</desc>
                                <g id="shopping-basket-2--shopping-basket">
                                    <path id="Vector" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M19.625389714285713 7.932533357142858H2.6646102857142857C2.4237190714285712 7.927597714285714 2.184897642857143 7.976954142857143 1.9656595714285712 8.076940714285715C1.7464214999999998 8.176927285714285 1.5524984999999998 8.32499657142857 1.398219857142857 8.510162785714286C1.2439412142857142 8.695169785714285 1.1332872857142857 8.9126565 1.0745372142857144 9.146223857142857C1.0157871428571428 9.379950428571428 1.0102146428571428 9.623866714285715 1.0584565714285714 9.859822285714285L2.8252574999999998 18.693667714285713C2.900406642857143 19.061771142857143 3.1021311428571425 19.392140785714286 3.3957222857142857 19.626822642857142C3.689154214285714 19.861663714285715 4.0556655 19.986169285714286 4.431411214285714 19.978527H17.858588785714286C18.234493714285712 19.986169285714286 18.601005 19.861663714285715 18.894436928571427 19.626822642857142C19.187868857142856 19.392140785714286 19.38975257142857 19.061771142857143 19.4647425 18.693667714285713L21.231543428571428 9.859822285714285C21.279785357142856 9.623866714285715 21.274212857142857 9.379950428571428 21.215462785714287 9.146223857142857C21.156712714285714 8.9126565 21.046058785714283 8.695169785714285 20.891780142857144 8.510162785714286C20.7375015 8.32499657142857 20.5435785 8.176927285714285 20.32434042857143 8.076940714285715S19.866280928571427 7.927597714285714 19.625389714285713 7.932533357142858Z"
                                        stroke-width="1.71"></path>
                                    <path id="Vector_2" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M14.357307428571428 2.3111545714285713L17.569614857142856 7.932533357142858"
                                        stroke-width="1.71">
                                    </path>
                                    <path id="Vector_3" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M4.720385142857142 7.932533357142858L7.932692571428571 2.3111545714285713"
                                        stroke-width="1.71">
                                    </path>
                                </g>
                            </svg>
                            <p>Punto de venta</p>
                        </div>
                    </template>
                    <Point />
                </el-tab-pane>
                <el-tab-pane v-if="canSeeUsers" label="Usuarios" name="2">
                    <template #label>
                        <div class="flex items-center space-x-1">
                            <svg width="16" height="16" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M11 4V6M11 6V8M11 6H13M11 6H9M7.5 3.25C7.5 3.84674 7.26295 4.41903 6.84099 4.84099C6.41903 5.26295 5.84674 5.5 5.25 5.5C4.65326 5.5 4.08097 5.26295 3.65901 4.84099C3.23705 4.41903 3 3.84674 3 3.25C3 2.65326 3.23705 2.08097 3.65901 1.65901C4.08097 1.23705 4.65326 1 5.25 1C5.84674 1 6.41903 1.23705 6.84099 1.65901C7.26295 2.08097 7.5 2.65326 7.5 3.25ZM1 11.8233V11.75C1 10.6228 1.44777 9.54183 2.2448 8.7448C3.04183 7.94777 4.12283 7.5 5.25 7.5C6.37717 7.5 7.45817 7.94777 8.2552 8.7448C9.05223 9.54183 9.5 10.6228 9.5 11.75V11.8227C8.21699 12.5954 6.74707 13.0025 5.24933 13C3.69533 13 2.24133 12.57 1 11.8227V11.8233Z"
                                    stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p>Usuarios</p>
                        </div>
                    </template>
                    <Users :users="users" :roles="roles" />
                </el-tab-pane>
                <el-tab-pane v-if="canSeePrinterSettings" label="Impresora" name="3">
                    <template #label>
                        <div class="flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                            </svg>
                            <p>Impresora</p>
                        </div>
                    </template>
                    <Printer />
                </el-tab-pane>
                <el-tab-pane label="Báscula" name="4">
                    <template #label>
                        <div class="flex items-center space-x-1">
                            <svg width="77" height="73" viewBox="0 0 77 73" fill="none"
                                xmlns="http://www.w3.org/2000/svg" stroke="currentColor" class="size-4">
                                <path
                                    d="M5.83797 42.9021L4.29033 64.6428C4.29033 65.6746 4.80621 66.4115 5.46948 66.4115H69.7336C71.2636 66.4232 72.313 65.8956 72.313 64.6428L70.4706 42.9021M5.83797 42.9021H70.4706M5.83797 42.9021C2.0802 42.9021 1.04867 40.6174 2.89008 35.3849L13.871 5.09529C14.3869 3.54765 15.4923 2 17.04 2H58.0894C60.5951 2 62.0691 2.88437 63.0272 5.09529L74.8187 37.7432C75.6294 40.0279 73.6396 42.9021 70.4706 42.9021M52.2685 57.2731L51.7527 57.8626M51.9001 58.3048L52.637 57.4204L52.7107 57.3467L51.8264 58.3785M60.8174 57.2731L61.6281 58.3785M60.8174 50.0507L61.7018 51.1562M51.8264 50.0507L52.8581 51.0825M62.4388 68.6961H68.2609M8.63968 68.7698H14.0933M14.4618 52.2616V56.8309C14.4618 57.2731 14.8303 57.6415 15.2724 57.6415H36.6447C37.6765 57.6415 37.7501 57.2731 37.7501 56.8309V52.2616C37.7501 51.672 37.3817 51.672 36.6447 51.672H15.1987C14.7566 51.672 14.4618 51.7457 14.4618 52.2616ZM69.5874 67.0748C69.5874 69.3541 67.7397 71.2019 65.4604 71.2019C63.1811 71.2019 61.3333 69.3541 61.3333 67.0748H69.5874ZM15.3461 67.0748C15.3461 69.3541 13.4984 71.2019 11.2191 71.2019C8.93978 71.2019 7.09203 69.3541 7.09203 67.0748H15.3461ZM53.0792 57.8626C53.0792 58.3104 52.7163 58.6733 52.2685 58.6733C51.8208 58.6733 51.4579 58.3104 51.4579 57.8626C51.4579 57.4149 51.8208 57.052 52.2685 57.052C52.7163 57.052 53.0792 57.4149 53.0792 57.8626ZM61.9966 57.8626C61.9966 58.3104 61.6336 58.6733 61.1859 58.6733C60.7382 58.6733 60.3753 58.3104 60.3753 57.8626C60.3753 57.4149 60.7382 57.052 61.1859 57.052C61.6336 57.052 61.9966 57.4149 61.9966 57.8626ZM53.0792 50.6403C53.0792 51.088 52.7163 51.451 52.2685 51.451C51.8208 51.451 51.4579 51.088 51.4579 50.6403C51.4579 50.1926 51.8208 49.8296 52.2685 49.8296C52.7163 49.8296 53.0792 50.1926 53.0792 50.6403ZM61.9966 50.6403C61.9966 51.088 61.6336 51.451 61.1859 51.451C60.7382 51.451 60.3753 51.088 60.3753 50.6403C60.3753 50.1926 60.7382 49.8296 61.1859 49.8296C61.6336 49.8296 61.9966 50.1926 61.9966 50.6403ZM16.3042 53.4408V55.6517H35.5392V53.4408H16.3042Z"
                                    stroke="currentColor" stroke-width="2.9479" />
                            </svg>
                            <p>Báscula</p>
                        </div>
                    </template>
                    <Scale />
                </el-tab-pane>
                <el-tab-pane label="Tienda en línea" name="5">
                    <template #label>
                        <div class="flex items-center space-x-1">
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
                            <p>Tienda en línea</p>
                        </div>
                    </template>
                    <OnlineSale />
                </el-tab-pane>
                <!-- <el-tab-pane label="Notificaciones" name="6">
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
import OnlineSale from '@/Pages/Setting/Tabs/OnlineSale.vue';

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
        OnlineSale,
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