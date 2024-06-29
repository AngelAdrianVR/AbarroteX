<template>
    <AppLayout :title="'Detalles de renta R-' + product_rental.id">
        <div class="md:px-10 px-2 py-7 text-xs md:text-sm">
            <div class="flex justify-between items-center">
                <Back :to="route('product-rentals.index')" />
            </div>
            <!-- Información de la venta -->
            <header class="">

            </header>
            <!-- Productos -->
            <main class="flex flex-col space-y-5 lg:mx-16 mt-8">
                <el-tabs v-model="activeTab" @tab-click="updateURL">
                    <el-tab-pane label="Información de la renta" name="1">
                        <GeneralInfo :rent="product_rental" />
                    </el-tab-pane>
                    <el-tab-pane label="Pagos" name="2">
                        <Payments />
                    </el-tab-pane>
                </el-tabs>
            </main>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import GeneralInfo from './Tabs/GeneralInfo.vue';
import Payments from './Tabs/Payments.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";

export default {
    data() {
        return {
            products: [],
            // modales
            showEditModal: false,
            showInstallmentModal: false,
            showRefundConfirm: false,
            saleToSeeInstallments: null,
            saleFolioToRefund: null,
            addInstallment: false,
            // cargas
            addingInstallment: false,
            refunding: false,
            editing: false,
            changingDay: false,
            // inventario de codigos activado
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
            // tabs
            activeTab: '1',
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        CancelButton,
        InputLabel,
        InputError,
        Back,
        ConfirmationModal,
        DialogModal,
        GeneralInfo,
        Payments,
    },
    props: {
        product_rental: Object,
    },
    computed: {
       
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