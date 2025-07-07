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
                    <li>Conecta el cable USB desde la impresora al dispositivo donde tengas tu sistema de punto
                        de venta (computadora).</li>
                    <li>Asegúrate de que la impresora esté encendida.</li>
                    <li>Da click en
                        <button @click="getAvailablePrinters" type="button" class="text-primary underline">
                            Buscar impresoras
                        </button>. Espera a que la ventana emergente se cierre automáticamente.
                    </li>
                    <li>
                        Selecciona la impresora correspondiente para usarlas como predeterminadas.
                        <i class="fa-regular fa-hand-point-down"></i>
                    </li>
                </ol>
                </p>
            </article>
            <article class="text-sm p-4 lg:flex items-center justify-between">
                <div class="lg:w-1/2">
                    <p class="text-[#575757]">
                        Elige el dispositivo que utilizarás para imprimir los tickets. Asegúrate de qué esté
                        correctamente conectado y configurado.
                    </p>
                </div>
                <div>
                    <div class="flex items-center space-x-2 mt-3 lg:mt-0">
                        <p>Impresora de tickets:</p>
                        <el-select v-model="form.printer_config.ticketPrinterName" @change="updatePrinterName"
                            class="!w-72" placeholder="Selecciona la impresora"
                            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="printer in availablePrinters" :key="printer" :value="printer"
                                :label="printer" />
                        </el-select>
                    </div>
                    <p v-if="loadingTicketName" class="text-gray-400 text-end text-xs">Guardando...</p>
                </div>
            </article>
            <article class="text-sm p-4 lg:flex items-center justify-between">
                <div class="lg:w-1/2">
                    <p class="text-[#575757]">
                        Elige el dispositivo que utilizarás para imprimir las etiquetas. Asegúrate de qué esté
                        correctamente conectado y configurado.
                    </p>
                </div>
                <div>
                    <div class="flex items-center space-x-2 mt-3 lg:mt-0">
                        <p>Impresora de etiquetas:</p>
                        <el-select v-model="form.printer_config.labelPrinterName" @change="updateLabelPrinterName"
                            class="!w-72" placeholder="Selecciona la impresora"
                            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="printer in availablePrinters" :key="printer" :value="printer"
                                :label="printer" />
                        </el-select>
                    </div>
                    <p v-if="loadingLabelName" class="text-gray-400 text-end text-xs">Guardando...</p>
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
                        <p>UUID Service:</p>
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
                        <p>UUID Characteristic:</p>
                        <el-input v-model="form.printer_config.UUIDCharacteristic" @blur="updateUUIDCharacteristic"
                            @keyup.enter="updateUUIDCharacteristic" maxlength="255" class="!w-72" clearable
                            placeholder="Escribe el UUID Service de tu impresora" />
                    </div>
                    <p v-if="loadingCharacteristic" class="text-gray-400 text-end text-xs">Guardando...</p>
                </div>
            </article>
        </section>
        <section class="my-5 divide-y-[1px] border border-grayD9 rounded-[5px]">
            <article class="text-sm rounded-t-md p-4">
                <div class="flex items-center space-x-2">
                    <span class="font-bold">Impresión de ticket</span>
                </div>
            </article>
            <article class="text-sm p-4 lg:flex items-center justify-between">
                <div class="lg:w-1/2">
                    <p class="text-[#575757]">
                        Elige el tamaño del ticket de venta que vas a usar en tu impresora.
                    </p>
                </div>
                <div>
                    <div class="flex items-center space-x-2 mt-3 lg:mt-0">
                        <p>Ancho de ticket:</p>
                        <el-select v-model="form.printer_config.ticketWidth" @change="updateTicketWidth" class="!w-72"
                            placeholder="Selecciona el ancho" no-data-text="No hay opciones registradas"
                            no-match-text="No se encontraron coincidencias">
                            <el-option v-for="width in ['58mm', '80mm']" :key="width" :value="width" :label="width" />
                        </el-select>
                    </div>
                    <p v-if="loadingTicketWidth" class="text-gray-400 text-end text-xs">Guardando...</p>
                </div>
            </article>
        </section>
        <section class="my-5 divide-y-[1px] border border-grayD9 rounded-[5px]">
            <article class="text-sm rounded-t-md p-4">
                <div class="flex items-center space-x-2">
                    <span class="font-bold">Impresión de etiqueta</span>
                </div>
            </article>
            <article class="text-sm p-4 lg:flex items-center justify-between">
                <div class="lg:w-1/2">
                    <p class="text-[#575757]">
                        Resolución (DPI o puntos por pulgada): Debe coincidir con la capacidad de su impresora (ej: 203,
                        300 o 600 DPI). Consulte el manual del equipo.
                    </p>
                </div>
                <div>
                    <div class="flex items-center space-x-2 mt-3 lg:mt-1 justify-self-end">
                        <p>Resolución:</p>
                        <el-select v-model="form.printer_config.labelResolution" @change="updateLabelResolution"
                            class="!w-72" placeholder="Selecciona la resolución"
                            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="resolution in ['203', '300', '600']" :key="resolution" :value="resolution"
                                :label="resolution" />
                        </el-select>
                    </div>
                    <p v-if="loadingLabelResolution" class="text-gray-400 text-end text-xs">Guardando...</p>
                </div>
            </article>
            <!-- <article class="text-sm p-4 lg:flex items-center justify-between">
                <div class="lg:w-1/2">
                    <p class="text-[#575757]">
                        Fuente: Elige entre las tipografías preinstaladas en tu impresora. <br>
                        Algunas impresoras pueden no soportar todas las fuentes, asi que puede que
                        al hacer el cambio no se vea reflejado en la etiqueta.
                    </p>
                </div>
                <div>
                    <div class="flex items-center space-x-2 mt-3 lg:mt-0 justify-self-end">
                        <p>Fuente:</p>
                        <el-select v-model="form.printer_config.labelFont" @change="updateLabelFont" class="!w-72"
                            placeholder="Selecciona la fuente" no-data-text="No hay opciones registradas"
                            no-match-text="No se encontraron coincidencias">
                            <el-option v-for='font in ["1", "2", "3", "4", "5"]' :key="font" :value="font"
                                :label="font" />
                        </el-select>
                    </div>
                    <p v-if="loadingLabelFont" class="text-gray-400 text-end text-xs">Guardando...</p>
                </div>
            </article> -->
            <article class="text-sm p-4 lg:flex items-center justify-between">
                <div class="lg:w-1/2">
                    <p class="text-[#575757]">
                        Mostrar texto de código de barras:
                        Controla si se muestra el valor bajo el código y su alineación
                    </p>
                </div>
                <div>
                    <div class="flex items-center space-x-2 mt-3 lg:mt-0 justify-self-end">
                        <p>Ver código:</p>
                        <el-select v-model="form.printer_config.labelBarCodeHumanReadable"
                            @change="updateBarCodeHumanReadable" class="!w-72" placeholder="Selecciona una opción"
                            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for='barCode in barCodeOption' :key="barCode.value" :value="barCode.value"
                                :label="barCode.label" />
                        </el-select>
                    </div>
                    <p v-if="loadingLabelBarCodeHumanReadable" class="text-gray-400 text-end text-xs">Guardando...</p>
                </div>
            </article>
            <article class="text-sm p-4 lg:flex items-center justify-between">
                <div class="lg:w-1/2">
                    <p class="text-[#575757]">
                        Indica las dimensiones de la etiqueta que vas a usar en tu impresora.
                    </p>
                </div>
                <div>
                    <div class="flex items-center space-x-2 mt-3 lg:mt-0 justify-self-end">
                        <p>Ancho (milimetros):</p>
                        <el-input-number v-model="form.printer_config.labelWidth" @blur="updateLabelSize"
                            @keyup.enter="updateLabelSize" :min="1" :max="5000" class="!w-24" size="small" clearable />
                    </div>
                    <div class="flex items-center space-x-2 mt-3 lg:mt-1 justify-self-end">
                        <p>Alto (milimetros):</p>
                        <el-input-number v-model="form.printer_config.labelHeight" @blur="updateLabelSize"
                            @keyup.enter="updateLabelSize" :min="1" :max="5000" class="!w-24" size="small" clearable />
                    </div>
                    <p v-if="loadingLabelSize" class="text-gray-400 text-end text-xs">Guardando...</p>
                </div>
            </article>
            <article class="text-sm p-4 lg:flex items-center justify-between">
                <div class="lg:w-1/2">
                    <p class="text-[#575757]">
                        Espacio entre líneas: Ajusta la separación vertical entre las líneas de texto.
                        El valor es en "Puntos" y generalmete 8 puntos es 1 milímetro, pero depende de la resolución
                        de tu impresora.<br>
                        Valor más bajo = líneas más pegadas <br>
                        Valor más alto = líneas más separadas
                    </p>
                </div>
                <div>
                    <div class="flex items-center space-x-2 mt-3 lg:mt-0 justify-self-end">
                        <p>Espacio (en puntos):</p>
                        <el-input-number v-model="form.printer_config.labelLineHeight" @blur="updateLabelLineHeight"
                            @keyup.enter="updateLabelLineHeight" :min="1" :max="5000" class="!w-24" size="small"
                            clearable />
                    </div>
                    <p v-if="loadingLabelLineHeight" class="text-gray-400 text-end text-xs">Guardando...</p>
                </div>
            </article>
            <article class="text-sm p-4 lg:flex items-center justify-between">
                <div class="lg:w-1/2">
                    <p class="text-[#575757]">
                        Espacio físico entre cada etiqueta en el rollo (en milimetros) <br>
                        Ajuste típico: 2-3mm (verificar en el rollo). <br>
                        Un valor de 0 significa que no hay espacio.
                    </p>
                </div>
                <div>
                    <div class="flex items-center space-x-2 mt-3 lg:mt-0 justify-self-end">
                        <p>Espacio entre etiquetas (mm):</p>
                        <el-input-number v-model="form.printer_config.labelGap" @blur="updateLabelGap"
                            @keyup.enter="updateLabelGap" :min="0" :max="5000" class="!w-24" size="small" clearable />
                    </div>
                    <p v-if="loadingLabelGap" class="text-gray-400 text-end text-xs">Guardando...</p>
                </div>
            </article>
        </section>
        <p class="flex items-center text-[10px] lg:text-sm mx-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-4 mr-2 flex-shrink-0">
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
                UUIDService: this.$page.props.auth.user.printer_config?.UUIDService ?? null,
                UUIDCharacteristic: this.$page.props.auth.user.printer_config?.UUIDCharacteristic ?? null,
                ticketPrinterName: this.$page.props.auth.user.printer_config?.ticketPrinterName ?? null,
                labelPrinterName: this.$page.props.auth.user.printer_config?.labelPrinterName ?? null,
                ticketWidth: this.$page.props.auth.user.printer_config?.ticketWidth ?? null,
                labelResolution: this.$page.props.auth.user.printer_config?.labelResolution ?? null,
                labelWidth: this.$page.props.auth.user.printer_config?.labelWidth ?? null,
                labelHeight: this.$page.props.auth.user.printer_config?.labelHeight ?? null,
                labelLineHeight: this.$page.props.auth.user.printer_config?.labelLineHeight ?? null,
                labelFont: this.$page.props.auth.user.printer_config?.labelFont ?? null,
                labelBarCodeHumanReadable: this.$page.props.auth.user.printer_config?.labelBarCodeHumanReadable ?? null,
                labelGap: this.$page.props.auth.user.printer_config?.labelGap ?? null,
            }
        });

        return {
            form,
            availablePrinters: [],
            barCodeOption: [
                { value: '0', label: 'No mostrar' },
                { value: '1', label: 'Mostrar alineado a la izquierda' },
                { value: '2', label: 'Mostrar centrado' },
                { value: '3', label: 'Mostrar alineado a la derecha' },
            ],
            // cargas
            loadingTicketName: false,
            loadingLabelName: false,
            loadingSevice: false,
            loadingCharacteristic: false,
            loadingTicketWidth: false,
            loadingLabelResolution: false,
            loadingLabelSize: false,
            loadingLabelLineHeight: false,
            loadingLabelFont: false,
            loadingLabelBarCodeHumanReadable: false,
            loadingLabelGap: false,
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
        updateBarCodeHumanReadable() {
            // enviar solicitud solo si hubo algun cambio en campo
            if (this.form.printer_config.labelBarCodeHumanReadable !== this.$page.props.auth.user.printer_config?.labelBarCodeHumanReadable) {
                this.loadingLabelBarCodeHumanReadable = true;
                this.form.put(route('users.update-printer-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingLabelBarCodeHumanReadable = false;
                    },
                });
            }
        },
        updateLabelGap() {
            // enviar solicitud solo si hubo algun cambio en campo
            if (this.form.printer_config.labelGap !== this.$page.props.auth.user.printer_config?.labelGap) {
                this.loadingLabelGap = true;
                this.form.put(route('users.update-printer-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingLabelGap = false;
                    },
                });
            }
        },
        updateLabelFont() {
            // enviar solicitud solo si hubo algun cambio en campo
            if (this.form.printer_config.labelFont !== this.$page.props.auth.user.printer_config?.labelFont) {
                this.loadingLabelFont = true;
                this.form.put(route('users.update-printer-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingLabelFont = false;
                    },
                });
            }
        },
        updateLabelLineHeight() {
            // enviar solicitud solo si hubo algun cambio en campo
            if (this.form.printer_config.labelLineHeight !== this.$page.props.auth.user.printer_config?.labelLineHeight) {
                this.loadingLabelLineHeight = true;
                this.form.put(route('users.update-printer-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingLabelLineHeight = false;
                    },
                });
            }
        },
        updateLabelSize() {
            // enviar solicitud solo si hubo algun cambio en campo
            if (this.form.printer_config.labelWidth !== this.$page.props.auth.user.printer_config?.labelWidth ||
                this.form.printer_config.labelHeight !== this.$page.props.auth.user.printer_config?.labelHeight) {
                this.loadingLabelSize = true;
                this.form.put(route('users.update-printer-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingLabelSize = false;
                    },
                });
            }
        },
        updateLabelResolution() {
            // enviar solicitud solo si hubo algun cambio en campo
            if (this.form.printer_config.labelResolution !== this.$page.props.auth.user.printer_config?.labelResolution) {
                this.loadingLabelResolution = true;
                this.form.put(route('users.update-printer-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingLabelResolution = false;
                    },
                });
            }
        },
        updateTicketWidth() {
            // enviar solicitud solo si hubo algun cambio en campo
            if (this.form.printer_config.ticketWidth !== this.$page.props.auth.user.printer_config?.ticketWidth) {
                this.loadingTicketWidth = true;
                this.form.put(route('users.update-printer-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingTicketWidth = false;
                    },
                });
            }
        },
        updatePrinterName() {
            // enviar solicitud solo si hubo algun cambio en campo
            if (this.form.printer_config.ticketPrinterName !== this.$page.props.auth.user.printer_config?.ticketPrinterName) {
                this.loadingTicketName = true;
                this.form.put(route('users.update-printer-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingTicketName = false;
                    },
                });
            }
        },
        updateLabelPrinterName() {
            // enviar solicitud solo si hubo algun cambio en campo
            if (this.form.printer_config.labelPrinterName !== this.$page.props.auth.user.printer_config?.labelPrinterName) {
                this.loadingLabelName = true;
                this.form.put(route('users.update-printer-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingLabelName = false;
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
}
</script>
