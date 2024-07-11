<template>
    <AppLayout :title="'Detalles del cliente'">
        <section class="mx-2 lg:mx-10 mt-7">
            <Back :to="route('clients.index')" />

            <h1 class="font-bold mt-4">Detalles del cliente</h1>

            <article class="flex flex-col lg:flex-row items-end lg:items-center space-y-2 lg:space-y-0 space-x-3 justify-between mt-5">
                <el-select @change="handleSelect()" class="w-full md:!w-1/4" filterable v-model="clientId" clearable
                    placeholder="Buscar cliente" no-data-text="No hay opciones registradas"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in clients" :key="item" :label="item.name" :value="item.id" />
                </el-select>
                <div class="flex items-center space-x-3">
                    <!-- <ThirthButton>Registrar abono</ThirthButton> -->
                    <PrimaryButton @click="$inertia.get(route('clients.edit', encodedId))">Editar</PrimaryButton>
                </div>
            </article>

            <!-- Información del cliente -->
            <header class="mt-7 lg:mx-16 text-sm lg:text-base space-y-1">
                <p class="font-bold">Nombre: <span class="font-thin ml-5">{{
                    client.name }}</span></p>
                <p class="font-bold">Teléfono: <span class="font-thin ml-5">{{
                    client.phone }}</span></p>
                <p class="font-bold">Dirección: <span class="font-thin ml-5">
                        {{ client.street ? client.street + ' ' + client.ext_number + ', Col. ' + client.suburb + ' ' +
                            client.int_number + '. ' + client.town + ', ' + client.polity_state : '-' }}
                    </span></p>
            </header>

            <div class="text-center text-sm lg:text-base my-5">
                <h2>Saldo total pendiente de pago</h2>
                <h3 class="font-bold">${{ client.debt?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}
                </h3>
            </div>

            <!-- Pestañas -->
            <el-tabs class="mx-3 mb-6" v-model="activeTab" @tab-click="updateURL">
                <el-tab-pane label="Ventas a crédito" name="1">
                    <CreditSales :clientId="client.id" />
                </el-tab-pane>
                <el-tab-pane label="Ventas al contado" name="2">
                    <CashSales :clientId="client.id" />
                </el-tab-pane>
            </el-tabs>
        </section>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CreditSales from '@/Pages/Client/Tabs/CreditSales.vue';
import CashSales from '@/Pages/Client/Tabs/CashSales.vue';
import Back from "@/Components/MyComponents/Back.vue";

export default {
    data() {
        return {
            clientId: this.client.id, //guarda el id del cliente seleccionado para ingresar a sus detalles desde el select
            activeTab: '1',
            encodedId: null //id codificado
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        ThirthButton,
        CreditSales,
        CashSales,
        Back
    },
    props: {
        client: Object,
        clients: Array
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
        handleSelect() {
            const encodedId = btoa(this.clientId.toString());
            this.$inertia.get(route('clients.show', encodedId))
        },
        encodeId(id) {
            const encodedId = btoa(id.toString());
            this.encodedId = encodedId;
        },
    },
    mounted() {
        this.setActiveTabFromURL();
        this.encodeId(this.client.id);
    }
};
</script>
