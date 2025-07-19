<template>
    <AppLayout :title="'Detalles del cliente'">
        <section class="mx-2 lg:mx-10 mt-7">
            <Back :to="route('clients.index')" />

            <h1 class="font-bold mt-4">Detalles del cliente</h1>

            <article
                class="flex flex-col lg:flex-row items-end lg:items-center space-y-2 lg:space-y-0 space-x-3 justify-between mt-5">
                <el-select @change="handleSelect()" class="w-full md:!w-1/4" filterable v-model="clientId" clearable
                    placeholder="Buscar cliente" no-data-text="No hay opciones registradas"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in clients" :key="item" :label="item.name" :value="item.id" />
                </el-select>
                <div class="flex items-center space-x-2">
                    <PrimaryButton @click="$inertia.get(route('clients.edit', encodedId))">Editar</PrimaryButton>
                    <button @click="showGeneralInstallmentModal = true" title="Agregar abono"
                        class="flex items-center justify-center bg-[#EDEDED] text-primary size-[34px] rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                    </button>
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

            <section class="text-center text-sm lg:text-base my-5">
                <div v-if="client.debt > 0">
                    <el-tag size="large" type="danger" style="font-size: 16px;">Deuda pendiente</el-tag>
                    <h3 class="font-bold">
                        ${{ client.debt?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}
                    </h3>
                </div>
                <div v-else>
                    <el-tag size="large" type="success" style="font-size: 16px;">Saldo a favor</el-tag>
                    <h3 class="font-bold">
                        ${{ Math.abs(client.debt)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                    </h3>
                </div>
            </section>

            <!-- Pestañas -->
            <el-tabs class="mx-3 mb-6" v-model="activeTab" @tab-click="updateURL">
                <el-tab-pane label="Ventas a crédito" name="1">
                    <CreditSales :clientId="client.id" ref="creditSales" />
                </el-tab-pane>
                <el-tab-pane label="Ventas al contado" name="2">
                    <CashSales :clientId="client.id" />
                </el-tab-pane>
                <!-- <el-tab-pane v-if="this.$page.props.auth.user.store.activated_modules?.includes('Cotizaciones')" label="Cotizaciones" name="3">
                    <Quotes :clientId="client.id" />
                </el-tab-pane> -->
                <el-tab-pane v-if="this.$page.props.auth.user.store.activated_modules?.includes('Renta de productos')"
                    label="Rentas" name="4">
                    <Rentals :clientId="client.id" />
                </el-tab-pane>
            </el-tabs>
        </section>
        <DialogModal :show="showGeneralInstallmentModal" @close="showGeneralInstallmentModal = false" max-width="md">
            <template #title>
                <h1 class="font-bold">Registrar abono</h1>
            </template>
            <template #content>
                <section>
                    <ol class="mb-4 text-justify text-xs text-gray37 *:list-decimal list-inside space-y-1">
                        <li> <b>Aplicación a deudas:</b> El pago se asignará primero a la deuda más antigua.</li>
                        <li> <b>Saldo restante:</b> Si el pago excede el monto de esa deuda, el saldo sobrante se
                            aplicará a la
                            siguiente deuda pendiente (en orden cronológico), y así sucesivamente.</li>
                        <li> <b>Saldo a favor:</b> Si el monto abonado supera el total de las deudas, el excedente se
                            registrará
                            como saldo a favor del cliente.</li>
                    </ol>
                    <p v-if="client.debt > 0">Deuda: ${{ client.debt?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                        }}</p>
                    <p v-else>Saldo a favor: $ {{ Math.abs(client.debt)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                        ",") }}</p>
                    <div class="mt-3">
                        <InputLabel value="Monto a abonar *" />
                        <el-input-number v-model="installmentForm.amount" placeholder="Ej: 1,000" />
                        <InputError :message="installmentForm.errors.amount" />
                    </div>
                </section>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="closeInstallmentModal" :disabled="installmentForm.processing">Cancelar
                    </CancelButton>
                    <PrimaryButton @click="storeInstallment"
                        :disabled="!installmentForm.amount || installmentForm.processing">
                        <i v-if="installmentForm.processing"
                            class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Registrar abono
                    </PrimaryButton>
                </div>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CreditSales from '@/Pages/Client/Tabs/CreditSales.vue';
import CashSales from '@/Pages/Client/Tabs/CashSales.vue';
import Quotes from '@/Pages/Client/Tabs/Quotes.vue';
import Rentals from '@/Pages/Client/Tabs/Rentals.vue';
import Back from "@/Components/MyComponents/Back.vue";
import DialogModal from '@/Components/DialogModal.vue';
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';

export default {
    data() {
        const installmentForm = useForm({
            amount: null,
        });

        return {
            installmentForm,
            clientId: this.client.id, //guarda el id del cliente seleccionado para ingresar a sus detalles desde el select
            activeTab: '1',
            encodedId: null, //id codificado
            showGeneralInstallmentModal: false,
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        CancelButton,
        ThirthButton,
        CreditSales,
        CashSales,
        Back,
        Quotes,
        Rentals,
        DialogModal,
        InputLabel,
        InputError,
    },
    props: {
        client: Object,
        clients: Array,
    },
    methods: {
        storeInstallment() {
            this.installmentForm.post(route('clients.store-installment', this.client.id), {
                onSuccess: () => {
                    this.$notify({
                        title: "Abono registrado",
                        type: "success"
                    });
                    // refrescar ventas a credito
                    this.$refs.creditSales.fetchSales();
                    //resetear formulario
                    this.installmentForm.reset();
                },
                onError: (err) => {
                    console.log(err);
                }
            });
        },
        closeInstallmentModal() {
            this.installmentForm.reset();
            this.showGeneralInstallmentModal = false;
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
