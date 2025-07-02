<template>
    <div>
        <section class="my-5 divide-y-[1px] border border-grayD9 rounded-[5px]">
            <article class="text-sm rounded-t-md p-4">
                <div class="flex items-center space-x-2">
                    <span class="font-bold">Conexión por cable USB</span>
                    <div class="bg-[#EDEDED] rounded-full size-5 flex items-center justify-center">
                        <i class="fa-brands fa-usb text-xs"></i>
                    </div>
                </div>
                <p class="text-[#575757] text-sm mt-2">
                    Para configurar tu impresora de tickets utilizando un cable USB, sigue estos pasos:
                <ol class="*:list-decimal list-inside mt-2 ml-2">
                    <li class="">Conecta el cable USB desde la impresora al dispositivo donde tengas tu sistema de punto
                        de venta (computadora).</li>
                    <li class="">Asegúrate de que la impresora esté encendida y reconocida por el sistema.</li>
                    <li class="">Selecciona la impresora para registrarla.</li>
                </ol>
                </p>
            </article>
            <!-- impresora ----------------------------------------->
            <article class="text-sm p-4 lg:flex items-center justify-between">
                <div class="lg:w-1/2">
                    <h2>Impresora</h2>
                    <p class="text-[#575757]">
                        Elige el dispositivo que utilizarás para imprimir los tickets. Asegúrate de qué esté
                        correctamente conectado y configurado.
                    </p>
                </div>
                <div>
                    <div class="flex items-center space-x-2 mt-3 lg:mt-0">
                        <p class="">Impresora:</p>
                        <el-select v-model="form.printer_config.name" @change="updatePrinterName" class="!w-72"
                            placeholder="Selecciona la impresora" no-data-text="No hay opciones registradas"
                            no-match-text="No se encontraron coincidencias">
                            <el-option v-for="printer in availablePrinters" :key="printer" :value="printer"
                                :label="printer" />
                        </el-select>
                    </div>
                    <p v-if="loadingName" class="text-gray-400 text-end text-xs">Guardando...</p>
                </div>
            </article>
        </section>
        <section class="my-5 divide-y-[1px] border border-grayD9 rounded-[5px]">
            <article class="text-sm rounded-t-md p-4">
                <div class="flex items-center space-x-2">
                    <span class="font-bold">Conexión por bluetooth</span>
                    <div class="bg-[#CCD8FD] rounded-full size-5 flex items-center justify-center">
                        <i class="fa-brands fa-bluetooth-b text-xs"></i>
                    </div>
                </div>
                <p class="text-[#575757] text-sm mt-2">
                    Para poder conectar tu impresora de tickets a tu sistema de punto de venta de manera inalámbrica,
                    asegúrate de encender el Bluetooth en el dispositivo desde el que deseas realizar la conexión (ya
                    sea
                    una computadora o un teléfono móvil). Esto permitirá que el dispositivo detecte la impresora y
                    puedas
                    establecer la conexión correctamente utilizando el Código UUID de servicio y el Código UUID
                    characteristic.
                </p>
            </article>
            <!-- UUID SERVICE ----------------------------------------->
            <article class="text-sm p-4 lg:flex items-center justify-between">
                <div class="lg:w-1/2">
                    <h2>Código UUID Service</h2>
                    <p class="text-[#575757]">
                        Puedes encontrarlo en las especificaciones del fabricante. Si no lo
                        encuentras contactar a soporte de Ezy Ventas
                    </p>
                </div>
                <div>
                    <div class="flex items-center space-x-2 mt-3 lg:mt-0">
                        <p class="">UUID Service:</p>
                        <el-input v-model="form.printer_config.UUIDService" @blur="updateUUIDService"
                            @keyup.enter="updateUUIDService" maxlength="255" class="!w-72" clearable
                            placeholder="Escribe el UUID Service de tu impresora" />
                    </div>
                    <p v-if="loadingSevice" class="text-gray-400 text-end text-xs">Guardando...</p>
                </div>
            </article>
            <!-- UUID Characteristic ----------------------------------------->
            <article class="text-sm p-4 lg:flex items-center justify-between">
                <div class="lg:w-1/2">
                    <h2>Código UUID Characteristic</h2>
                    <p class="text-[#575757]">
                        Puedes encontrarlo en las especificaciones del fabricante. Si no lo
                        encuentras contactar a soporte de Ezy Ventas
                    </p>
                </div>
                <div>
                    <div class="flex items-center space-x-2 mt-3 lg:mt-0">
                        <p class="">UUID Characteristic:</p>
                        <el-input v-model="form.printer_config.UUIDCharacteristic" @blur="updateUUIDCharacteristic"
                            @keyup.enter="updateUUIDCharacteristic" maxlength="255" class="!w-72" clearable
                            placeholder="Escribe el UUID Service de tu impresora" />
                    </div>
                    <p v-if="loadingCharacteristic" class="text-gray-400 text-end text-xs">Guardando...</p>
                </div>
            </article>
        </section>
        <p class="flex items-center text-sm mx-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
            </svg>
            Para activar la impresion de tickets automáticos, ve a configuraciones en la pestaña
            <Link :href="route('settings.index')" class="text-primary underline ml-2"> Punto de Venta</Link>
        </p>
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm, Link } from "@inertiajs/vue3";

export default {
    data() {
        const form = useForm({
            printer_config: {
                name: this.$page.props.auth.user.printer_config?.name ?? null,
                UUIDService: this.$page.props.auth.user.printer_config?.UUIDService ?? null,
                UUIDCharacteristic: this.$page.props.auth.user.printer_config?.UUIDCharacteristic ?? null,
            }
        });

        return {
            form,
            availablePrinters: [],
            // cargas
            loadingName: false,
            loadingSevice: false,
            loadingCharacteristic: false,
        }
    },
    components: {
        PrimaryButton,
        Link,
    },
    props: {
        store: Object,
    },
    methods: {
        updatePrinterName() {
            // enviar solicitud solo si hubo algun cambio en campo
            if (this.form.printer_config.name !== this.$page.props.auth.user.printer_config?.name) {
                this.loadingName = true;
                this.form.put(route('users.update-printer-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingName = false;
                    },
                });
            }
        },
        updateUUIDService() {
            // enviar solicitud solo si hubo algun cambio en campo
            if (this.form.printer_config.UUIDService !== this.$page.props.auth.user.printer_config?.UUIDService) {
                this.loadingSevice = true;
                this.form.put(route('users.update-printer-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingSevice = false;
                    },
                });
            }
        },
        updateUUIDCharacteristic() {
            // enviar solicitud solo si hubo algun cambio en campo
            if (this.form.printer_config.UUIDCharacteristic !== this.$page.props.auth.user.printer_config?.UUIDCharacteristic) {
                this.loadingCharacteristic = true;
                this.form.put(route('users.update-printer-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingCharacteristic = false;
                    },
                });
            }
        },
        async getAvailablePrinters() {
            try {
                const respuestaHttp = await fetch("http://localhost:8000/impresoras");
                const listaDeImpresoras = await respuestaHttp.json();
                this.availablePrinters = listaDeImpresoras;
            } catch (e) {
                // El plugin no respondió
                console.log(e)
            }
        },
    },
    mounted() {
        this.getAvailablePrinters();
    }
}
</script>
