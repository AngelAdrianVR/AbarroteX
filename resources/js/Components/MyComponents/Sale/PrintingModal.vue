<template>
    <DialogModal :show="show" @close="$emit('close')" max-width="md">
        <template #title> Impresión de ticket </template>
        <template #content>
            <div class="flex items-start space-x-4">
                <figure class="h-24">
                    <img src="@/../../public/images/ticket.png" :draggable="false"
                        class="select-none object-contain h-full" alt="Imagen de ticket de venta">
                </figure>
                <p class="w-2/3 text-base text-gray37">¿Desea imprimir el ticket de la venta?</p>
            </div>
            <div class="mt-3">
                <p v-if="printerName">
                    Tienes registrada la impresora <b>{{ printerName }}</b>
                </p>
                <div v-else>
                    <p>No tienes ninguna impresora registrada. Selecciona una:</p>
                    <el-select v-model="printerName" placeholder="Selecciona la impresora"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="printer in getAvailablePrinters" :key="printer" :value="printer"
                            :label="printer" />
                    </el-select>
                </div>
            </div>
        </template>
        <template #footer>
            <div class="flex items-center space-x-2">
                <CancelButton @click="$emit('close')">No</CancelButton>
                <PrimaryButton @click="print" :disabled="!printerName">
                    Si, imprimir
                </PrimaryButton>
            </div>
        </template>
    </DialogModal>
</template>

<script>
import DialogModal from '@/Components/DialogModal.vue';
import CancelButton from '../CancelButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

export default {
    name: 'PrintingModal',
    data() {
        return {
            printerName: this.$page.props.auth.user.printer_config?.name ?? null,
        };
    },
    emits: ['close'],
    components: {
        DialogModal,
        CancelButton,
        PrimaryButton,
    },
    props: {
        show: {
            type: Boolean,
            required: false,
        },
    },
    computed: {
    },
    methods: {
        print() {

        },
        async getAvailablePrinters() {
            try {
                const respuestaHttp = await fetch("http://localhost:8000/impresoras");
                const listaDeImpresoras = await respuestaHttp.json();
                return listaDeImpresoras;
            } catch (e) {
                // El plugin no respondió
                console.log(e)
            }
        },
    },
};
</script>