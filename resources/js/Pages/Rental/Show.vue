<template>
    <AppLayout :title="'Detalles de renta R-' + rental.id">
        <div class="md:px-10 px-2 py-7 text-xs md:text-sm">
            <div class="flex justify-between items-center">
                <Back :to="route('rentals.index')" />
            </div>
            <header class="flex flex-col lg:flex-row items-end lg:items-center space-y-2 lg:space-y-0 space-x-3 justify-between mt-5">
                <el-select @change="handleSelect()" class="!w-full md:!w-1/4" filterable v-model="rentId" clearable
                    placeholder="Buscar registro de renta" no-data-text="No hay opciones registradas"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in rentals" :key="item.id" :label="item.folio" :value="item.id" />
                </el-select>
                <div class="flex items-center space-x-3">
                    <el-dropdown split-button type="primary" @click="$inertia.get(route('rentals.edit', encodedId))">
                    Editar
                    <template #dropdown>
                        <el-dropdown-menu>
                        <el-dropdown-item @click="printContract()">Imprimir contrato</el-dropdown-item>
                        <el-dropdown-item @click="$inertia.get(route('rental-payments.create', {rentalId: rental.id}))">Registrar pago</el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                    </el-dropdown>
                </div>
            </header>
            <!-- Productos -->
            <main class="flex flex-col space-y-5 mt-8">
                <el-tabs v-model="activeTab" @tab-click="updateURL">
                    <el-tab-pane label="Información de la renta" name="1">
                        <GeneralInfo :rent="rental" />
                    </el-tab-pane>
                    <el-tab-pane label="Pagos" name="2">
                        <Payments :payments="rental.payments" />
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
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";

export default {
    data() {
        return {
            // selector
            rentId: this.rental.id,
            // otros
            encodedId: null,
            // tabs
            activeTab: '1',
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        ThirthButton,
        CancelButton,
        InputLabel,
        InputError,
        Back,
        GeneralInfo,
        Payments,
    },
    props: {
        rental: Object,
        rentals: Array,
    },
    computed: {

    },
    methods: {
        handleSelect() {
            const encodedId = btoa(this.rentId.toString());
            this.$inertia.get(route('rentals.show', encodedId))
        },
        printContract() {
            window.open(route('rentals.print-contract', this.rental.id), '_blank');
        },
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
        encodeId(id) {
            const encodedId = btoa(id.toString());
            this.encodedId = encodedId;
        },
    },
    mounted() {
        this.setActiveTabFromURL();
        this.encodeId(this.rental.id);
        // resetear variable de local storage a false
        localStorage.setItem('pendentProcess', false);
    }
}
</script>