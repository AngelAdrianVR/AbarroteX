<template>
    <AppLayout title="Nueva orden de servicio">
        <div class="px-3 md:px-10 py-5">
            <Back :to="route('service-reports.index')" />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-[75%] xl:w-[65%] mx-auto mt-1 lg:grid lg:grid-cols-2 gap-3">
                <div class="flex items-center justify-between col-span-full mb-3">
                    <h1 class="font-bold ml-2 col-span-full">Crear orden de servicio</h1>
                    <!-- <div class="text-sm text-right">
                        <p>Orden de servicio</p>
                        <p>No. {{ String(folio).padStart(4, '0') }}</p>
                    </div> -->
                </div>
                <div class="col-span-full">
                    <InputLabel value="Fecha del servicio*" />
                    <el-date-picker v-model="form.service_date" type="date" class="!w-1/2"
                        placeholder="Selecciona una fecha" format="DD MMM, YY" value-format="YYYY-MM-DD" />
                    <InputError :message="form.errors.service_date" />
                </div>

                <h1 class="font-semibold text-gray37 ml-2 col-span-full mt-3">Datos del cliente</h1>

                <div>
                    <InputLabel value="Nombre del cliente*" />
                    <el-input v-model="form.client_name" placeholder="Ej. Carlos Pérez" clearable />
                    <InputError :message="form.errors.client_name" />
                </div>
                <div>
                    <InputLabel value="Número de Teléfono del cliente*" />
                    <el-input v-model="form.client_phone_number"
                        :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                        :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                        placeholder="Escribe el número de teléfono" />
                    <InputError :message="form.errors.client_phone_number" />
                </div>
                <h1 class="font-semibold text-gray37 ml-2 col-span-full mt-3">Datos del equipo</h1>
                <div>
                    <InputLabel value="Marca" />
                    <el-input v-model="form.product_details.brand" placeholder="Ej. Samsung" clearable />
                    <InputError :message="form.errors['product_details.brand']" />
                </div>
                <div>
                    <InputLabel value="Modelo" />
                    <el-input v-model="form.product_details.model" placeholder="Ej. Galaxy s21" clearable />
                    <InputError :message="form.errors['product_details.model']" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="IMEI" />
                    <el-input v-model="formattedImei" class="!w-1/2" placeholder="Escribe el código IMEI del equipo"
                        clearable maxlength="18" />
                    <InputError :message="form.errors['product_details.imei']" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Problema reportado*" />
                    <el-input v-model="form.observations" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                        placeholder="Describe el problema mencionado por el cliente" :maxlength="1000" show-word-limit
                        clearable />
                    <InputError :message="form.errors.observations" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Estado previo y características del equipo" />
                    <el-input v-model="form.description" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                        placeholder="Describe el estado del equipo" :maxlength="1000" show-word-limit clearable />
                    <InputError :message="form.errors.description" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Servicios a realizar*" />
                    <el-input v-model="form.service_description" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                        placeholder="Describe el trabajo a realizar" :maxlength="1000" show-word-limit clearable />
                    <InputError :message="form.errors.service_description" />
                </div>

                <h1 class="font-semibold text-gray37 ml-2 col-span-full mt-3">Refacciones</h1>

                <SparePartInput @syncItems="syncItems" class="col-span-full" />

                <section class="grid grid-cols-3 gap-3 col-span-full mt-5">
                    <div>
                        <InputLabel value="Costo total del servicio*" />
                        <el-input v-model="form.service_cost" placeholder="Ej. 2,500" clearable
                            :formatter="(value) => `${Number(value).toLocaleString('es-MX')}`"
                            :parser="(value) => value.replace(/[^\d.]/g, '')">
                            <template #prepend>
                                $
                            </template>
                        </el-input>
                        <InputError :message="form.errors.service_cost" />
                    </div>
                    <div>
                        <InputLabel value="Anticipo (opcional)" />
                        <el-input v-model="form.advance_payment" placeholder="Ej. 500" clearable
                            :formatter="(value) => `${Number(value).toLocaleString('es-MX')}`"
                            :parser="(value) => value.replace(/[^\d.]/g, '')">
                            <template #prepend>
                                $
                            </template>
                        </el-input>
                        <InputError :message="form.errors.advance_payment" />
                    </div>
                    <div v-if="form.advance_payment">
                        <InputLabel value="Método de pago del anticipo" />
                        <el-select v-model="form.payment_method" clearable placeholder="Selecciona el método de pago"
                            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="payment_method in payment_methods" :key="payment_method"
                                :label="payment_method"
                                :value="payment_method" />
                        </el-select>
                    </div>
                </section>

                <div>
                    <InputLabel value="Responsable del servicio*" />
                    <el-input v-model="form.technician_name" placeholder="Ej. Luis García" clearable />
                    <InputError :message="form.errors.technician_name" />
                </div>
                <div>
                    <div class="flex space-x-2">
                        <InputLabel value="Porcentage de comisión" />
                        <el-tooltip placement="top">
                            <template #content>
                                <p>Al cambiar la orden al estatus "Pagado", se <br>
                                    registrará automáticamente un gasto con <br>
                                    el concepto "Comisión", incluyendo el <br>
                                    nombre del responsable del servicio.</p>
                            </template>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4 text-primary">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                        </el-tooltip>
                    </div>
                    <el-input v-model="form.comision_percentage" placeholder="Ej. 10" clearable
                        @input="onPercentageInput" autocomplete="off">
                        <template #append>%</template>
                    </el-input>
                    <InputError :message="form.errors.comision_percentage" />
                </div>

                <section class="col-span-full grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <InputLabel value="Evidencias (max. 5 imágenes)" />
                        <el-upload class="upload-demo" drag :on-change="handleChange" :on-remove="handleRemoveImage"
                            :on-exceed="handleExceed" :on-preview="handlePictureCardPreview"
                            v-model:file-list="fileList" :before-upload="beforeUpload" :multiple="true" :limit="5"
                            list-type="picture-card" :auto-upload="false">
                            <i class="el-icon-upload"></i>
                            <div class="el-upload__text">Arrastra o haz clic para subir</div>
                        </el-upload>
                    </div>

                    <div class="mt-3 ml-4">
                        <p>Accesorios</p>
                        <el-checkbox-group v-model="form.aditionals.accessories">
                            <el-checkbox label="SIM"></el-checkbox>
                            <el-checkbox label="Cargador"></el-checkbox>
                            <el-checkbox label="Memoria"></el-checkbox>
                            <el-checkbox label="Batería"></el-checkbox>
                        </el-checkbox-group>
                    </div>
                </section>

                <section class="grid grid-cols-3 col-span-full mt-5">
                    <PatronMobil @syncPattern="syncPattern" @syncPassword="syncPassword" class="col-span-2" />
                    <article class="mt-24 text-sm space-y-1">
                        <!-- <p class="flex">
                            <span class="w-32">Refacciones</span><span class="ml-3">$</span><span
                                class="w-16 text-right">{{
                                    totalSpareParts?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </p> -->
                        <p class="flex">
                            <span class="w-44">Costo total del servicio</span><span class="ml-3">$</span><span
                                class="w-16 text-right">{{
                                    form.service_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</span>
                        </p>
                        <p class="flex">
                            <span class="w-44">Anticipo</span><span class="ml-[2px]">- $</span><span
                                class="w-16 text-right">{{
                                    form.advance_payment?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00'
                                }}</span>
                        </p>
                        <p class="flex font-bold">
                            <span class="w-44">Restante por pagar</span><span class="ml-3">$</span><span
                                class="w-16 text-right">{{
                                    total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </p>
                    </article>
                </section>

                <el-dialog v-model="dialogVisible">
                    <img class="mx-auto" w-full :src="dialogImageUrl" alt="Preview Image" />
                </el-dialog>

                <div class="col-span-full text-right mt-5">
                    <PrimaryButton :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Crear reporte
                    </PrimaryButton>
                </div>
            </form>
            <!-- modal de impresión -->
            <PrintingModal :show="showPrintingModal" @close="closePrintingModal" ref="printingModal" />
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import SparePartInput from "@/Components/MyComponents/SparePartInput.vue";
import PatronMobil from "@/Components/MyComponents/PatronMobil.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";
import PrintingModal from '../../Components/MyComponents/Sale/PrintingModal.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    data() {
        const form = useForm({
            service_date: format(new Date(), "yyyy-MM-dd"), // Establece la fecha de hoy por defecto,
            client_name: null,
            client_phone_number: null,
            product_details: {
                brand: null,
                model: null,
            },
            spare_parts: [
                {
                    name: '',
                    unitPrice: null,
                    quantity: null,
                },
            ],
            observations: null,
            technician_name: null,
            description: null,
            service_cost: null, // costo unicamente de mano de obra
            total_cost: null, // costo total
            service_description: null, //descripcion de los servicios que se harán
            // payment_method: null,
            advance_payment: null, // anticipo
            comision_percentage: null, // comisión de la persona que realizó el servicio
            media: [], //imagenes de evidencia
            aditionals: {
                unlockPattern: null, //patrton de desbloqueo
                unlockPassword: null, //contraseña o pin de desbloqueo
                accessories: [], // Arreglo con los accesorios seleccionados
            }
        });

        return {
            form,
            payment_methods: ['Efectivo', 'Tarjeta', 'Transferencia'],
            showPrintingModal: false,
            newFolio: null,
            //uploader
            uploading: false,
            uploadPercentage: 0,
            fileList: [], // archivos
            dialogVisible: false, //imagen element-plus
            dialogImageUrl: '', //imagen element-plus
            automaticPrinting: this.$page.props.auth.user.store.settings.find(item => item.name == 'Impresión automática de tickets')?.value,
        }
    },
    components: {
        SparePartInput,
        PrimaryButton,
        PatronMobil,
        InputLabel,
        InputError,
        AppLayout,
        Back,
        PrintingModal,
    },
    props: {
        folio: Number
    },
    watch: {
        'form.service_cost'(val) {
            this.form.service_cost = Number(val);
        },
        'form.advance_payment'(val) {
            this.form.advance_payment = Number(val);
        }
    },
    computed: {
        formattedImei: {
            get() {
                const raw = this.form.product_details?.imei || '';
                return raw.replace(/\D/g, '').slice(0, 15).replace(/(.{4})/g, '$1 ').trim();
            },
            set(value) {
                const clean = value.replace(/\D/g, '').slice(0, 15);
                if (!this.form.product_details) {
                    this.form.product_details = {};
                }
                this.form.product_details.imei = clean;
            },
        },
        total() {
            this.form.total_cost = this.form.service_cost;
            return this.form.service_cost - this.form.advance_payment;
        },
        totalSpareParts() {
            if (!this.form.spare_parts[0].name) return 0;

            return this.form.spare_parts?.reduce((total, sp) => {
                return total + (Number(sp.quantity) * Number(sp.unitPrice));
            }, 0);
        }
    },
    methods: {
        store() {
            this.form.transform((data) => ({
                ...data,
                spare_parts: data.spare_parts[0]?.unitPrice ? data.spare_parts : [],
            })).post(route("service-reports.store-phones"), {
                onSuccess: (page) => {
                    this.$notify({
                        title: "Correcto",
                        type: "success",
                    });

                    // Abrir modal de impresión automáticamente
                    if (this.automaticPrinting) {
                        const newFolio = page.props.folio - 1;
                        if (newFolio) {
                            this.showPrintingModal = true;
                            this.newFolio = newFolio;
                            // Ejecutar código DESPUÉS de que el DOM se actualice.
                            this.$nextTick(() => {
                                this.$refs.printingModal.customData = this.generateServiceTicketCommands(false);
                            });
                        } else {
                            console.error("No se recibió el folio para la impresión automática.");
                        }
                    } else {
                        this.$inertia.visit(route('service-reports.index'));
                    }
                },
                onError: (err) => {
                    console.log(err);
                }
            });
        },
        closePrintingModal() {
            if (this.$refs.printingModal.printType == 'Etiqueta') {
                this.$inertia.visit(route('service-reports.index'));
            } else {
                this.$refs.printingModal.setLabelMode();
                this.$refs.printingModal.customData = this.generateTSPLLabelCommands(false);
            }
        },
        generateServiceTicketCommands(hasCut = true) {
            const ESC = '\x1B';
            const GS = '\x1D';
            const INICIALIZAR_IMPRESORA = ESC + '@';
            const NEGRITA_ON = ESC + 'E' + '\x01';
            const NEGRITA_OFF = ESC + 'E' + '\x00';
            const ALINEAR_IZQUIERDA = ESC + 'a' + '\x00';
            const ALINEAR_CENTRO = ESC + 'a' + '\x01';
            const ALINEAR_DERECHA = ESC + 'a' + '\x02';
            const CORTAR_PAPEL = GS + 'V' + '\x00' + '\x00';

            // --- 1. Determinar el ancho del ticket dinámicamente ---
            const ticketWidthSetting = this.$page.props.auth.user.printer_config?.ticketWidth;
            // Ancho en caracteres: 48 para 80mm, 32 para 58mm (o por defecto)
            const anchoTicket = ticketWidthSetting === '80mm' ? 48 : 32;
            const separador = '-'.repeat(anchoTicket) + '\n';

            let ticket = INICIALIZAR_IMPRESORA;

            // Encabezado
            ticket += ALINEAR_CENTRO;
            if (this.$page.props.auth.user.store) {
                ticket += NEGRITA_ON + this.$page.props.auth.user.store.name + NEGRITA_OFF + '\n';
                if (this.$page.props.auth.user.store.address) {
                    ticket += this.$page.props.auth.user.store.address + '\n';
                }

                if (this.$page.props.auth.user.printer_config?.ticketContactInfo) {
                    ticket += this.$page.props.auth.user.printer_config?.ticketContactInfo + '\n';
                }
            }
            ticket += separador;
            ticket += NEGRITA_ON + 'ORDEN DE SERVICIO' + NEGRITA_OFF + '\n';
            ticket += separador;

            // Datos del Cliente y Equipo
            ticket += ALINEAR_IZQUIERDA;
            ticket += 'Fecha: ' + this.formatDate() + '\n';
            ticket += 'Cliente: ' + (this.form.client_name || 'N/A') + '\n';
            ticket += 'Telefono: ' + (this.form.client_phone_number || 'N/A') + '\n\n';

            ticket += NEGRITA_ON + 'Detalles del Equipo:' + NEGRITA_OFF + '\n';
            ticket += 'Marca: ' + (this.form.product_details.brand || 'N/A') + '\n';
            ticket += 'Modelo: ' + (this.form.product_details.model || 'N/A') + '\n';
            ticket += separador;

            // Descripción del Servicio y problemas reportados (se ajustan automáticamente al saltar de línea)
            if (this.form.observations) {
                ticket += NEGRITA_ON + 'Problemas reportados:' + NEGRITA_OFF + '\n';
                ticket += this.form.observations + '\n\n';
            }
            if (this.form.service_description) {
                ticket += NEGRITA_ON + 'Servicio:' + NEGRITA_OFF + '\n';
                ticket += this.form.service_description + '\n';
            }
            ticket += separador;

            // --- 2. Ajustar sección de refacciones con columnas dinámicas ---
            // if (this.form.spare_parts && this.form.spare_parts.length > 0 && this.form.spare_parts[0].name) {
            //     ticket += ALINEAR_CENTRO + NEGRITA_ON + 'Refacciones' + NEGRITA_OFF + '\n';

            //     // Definir anchos de columna
            //     const colAnchoPrecio = 10; // " $1,234.56"
            //     const colAnchoCant = 4;    // "Cant "
            //     const colAnchoNombre = anchoTicket - colAnchoPrecio - colAnchoCant;

            //     // Crear encabezado dinámico
            //     let header = 'Pzs'.padEnd(colAnchoCant);
            //     header += 'Concepto'.padEnd(colAnchoNombre);
            //     header += 'Importe'.padStart(colAnchoPrecio);

            //     ticket += NEGRITA_ON + header + NEGRITA_OFF + '\n';
            //     ticket += separador;
            //     ticket += ALINEAR_IZQUIERDA;

            //     this.form.spare_parts.forEach(part => {
            //         const cantidad = part.quantity.toString();
            //         // Trunca el nombre del producto para que quepa en su columna
            //         const nombre = part.name.substring(0, colAnchoNombre - 1); // -1 por el espacio
            //         const totalProducto = (part.quantity * part.unitPrice).toFixed(2);

            //         let linea = '';
            //         linea += cantidad.padEnd(colAnchoCant);
            //         linea += this.removeAccents(nombre).padEnd(colAnchoNombre);
            //         linea += ('$' + totalProducto).padStart(colAnchoPrecio);

            //         ticket += linea + '\n';
            //     });
            //     ticket += separador;
            // }

            // --- 3. Ajustar sección de costos para alineación derecha ---
            const formatCurrency = (value) => {
                return '$' + parseFloat(value || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            };

            // let subtotalRefacciones = 0;
            // if (this.form.spare_parts && this.form.spare_parts.length > 0 && this.form.spare_parts[0].name) {
            //     subtotalRefacciones = this.form.spare_parts.reduce((acc, part) => acc + (part.quantity * part.unitPrice), 0);
            //     let subtotalStr = 'Subtotal Refacciones: ' + formatCurrency(subtotalRefacciones);
            //     ticket += subtotalStr.padStart(anchoTicket) + '\n';
            // }

            // let costoServicioStr = 'Mano de Obra: ' + formatCurrency(this.form.service_cost);
            // ticket += costoServicioStr.padStart(anchoTicket) + '\n';

            let totalStr = 'Total del servicio: ' + formatCurrency(this.form.total_cost);
            ticket += NEGRITA_ON + totalStr.padStart(anchoTicket) + NEGRITA_OFF + '\n';

            if (this.form.advance_payment > 0) {
                let anticipoStr = 'Anticipo: ' + formatCurrency(this.form.advance_payment);
                ticket += NEGRITA_OFF + anticipoStr.padStart(anchoTicket) + '\n';

                let restanteStr = 'Resta: ' + formatCurrency(this.form.total_cost - this.form.advance_payment);
                ticket += NEGRITA_ON + restanteStr.padStart(anchoTicket) + NEGRITA_OFF + '\n';
            }

            // --- 4. Generar Código de Barras con el Folio ---
            // Asegúrate que el folio exista y no esté vacío
            if (this.newFolio) {
                const SET_BARCODE_HEIGHT = GS + 'h' + '\x50'; // Altura del código: 80 dots
                const SET_BARCODE_WIDTH = GS + 'w' + '\x03';  // Ancho del código: multiplicador 3
                const PRINT_HRI_BELOW = GS + 'H' + '\x02';   // Imprimir texto legible debajo del código

                // Comando para imprimir CODE128: GS k m n [d1...dn]
                // m = 73 (0x49) para CODE128
                // n = longitud de los datos
                const folio = String(this.newFolio).padStart(3, '0');
                const printBarcodeCommand = GS + 'k' + '\x49' + String.fromCharCode(folio.length) + folio;

                ticket += ALINEAR_CENTRO;
                ticket += SET_BARCODE_HEIGHT;
                ticket += SET_BARCODE_WIDTH;
                ticket += PRINT_HRI_BELOW;
                ticket += '\n' + printBarcodeCommand + '\n';
            }
            // --- Fin del código de barras ---

            // terminos y condiciones
            ticket += '\n' + this.$page.props.auth.user.printer_config?.ticketTerms + '\n\n';
            // firma
            ticket += '_______________________________\n';
            ticket += 'Firma de cliente acepta condiciones\n';

            // Pie de página
            ticket += '\n' + ALINEAR_CENTRO;
            ticket += 'GRACIAS POR SU PREFERENCIA';

            return ticket;
        },
        generateTSPLLabelCommands() {
            // --- 1. Configuración de la Etiqueta ---
            // Define aquí las dimensiones de tu etiqueta en milímetros.
            const labelConfig = {
                widthMM: this.$page.props.auth.user.printer_config?.labelWidth,  // Ancho de la etiqueta en mm
                heightMM: this.$page.props.auth.user.printer_config?.labelHeight, // Alto de la etiqueta en mm
                gapMM: this.$page.props.auth.user.printer_config?.labelGap,     // Espacio entre etiquetas en mm
                dotsPerMM: Math.round(this.$page.props.auth.user.printer_config?.labelResolution / 25.4)  // Resolución de la impresora (203 dpi = 8 dots/mm)
            };

            // --- 2. Comandos Iniciales (¡La parte más importante!) ---
            // SIZE: Define el tamaño de la etiqueta.
            // GAP: Define la separación entre etiquetas. Esto corrige el problema de sobreimpresión.
            // CODEPAGE: Define la tabla de caracteres. 1252 es para Latin-1 (incluye acentos, ñ).
            // CLS: Limpia el búfer de la impresora antes de empezar a dibujar.
            let commands = '';
            commands += `SIZE ${labelConfig.widthMM} mm, ${labelConfig.heightMM} mm\n`;
            commands += `GAP ${labelConfig.gapMM} mm, 0 mm\n`;
            // commands += `COUNTRY 003\n`;
            // commands += `CODEPAGE 1252\n`;
            commands += `CLS\n`;

            // --- 3. Coordenadas y Diseño ---
            let currentY = 15; // Posición Y inicial (margen superior en dots)
            const startX = 15; // Posición X inicial (margen izquierdo en dots)
            const rightMargin = 15;
            const lineHeight = this.$page.props.auth.user.printer_config?.labelLineHeight; // Espacio entre líneas
            const font = `"${this.$page.props.auth.user.printer_config?.labelFont}"`; // Fuente a utilizar. Las comillas dobles son importantes.
            const fontAvgCharWidth = 12; // Ancho promedio de un carácter en dots. Ajusta según la fuente.

            /**
             * Función auxiliar para añadir texto y manejar saltos de línea automáticos.
             * @param {string} label - La etiqueta del campo (ej. "Nombre:").
             * @param {string} value - El valor del campo.
             */
            const addTextLine = (label, value) => {
                if (!value) return; // No añadir si el valor está vacío

                let fullText = `${label} ${value}`;

                // --- CÁLCULO DINÁMICO DEL MÁXIMO DE CARACTERES ---
                // 1. Calcula el ancho total disponible para el texto en dots.
                const availableWidth = (labelConfig.widthMM * labelConfig.dotsPerMM) - startX - rightMargin;
                // 2. Calcula cuántos caracteres caben en ese espacio. Usamos Math.floor para redondear hacia abajo.
                const maxLength = Math.floor(availableWidth / fontAvgCharWidth);

                const textParts = [];
                while (fullText.length > maxLength) {
                    let chunk = fullText.substring(0, maxLength);
                    let lastSpace = chunk.lastIndexOf(' ');
                    if (lastSpace > 0) {
                        chunk = chunk.substring(0, lastSpace);
                    }
                    textParts.push(chunk);
                    fullText = fullText.substring(chunk.length).trim();
                }
                textParts.push(fullText);

                // Imprime cada parte del texto
                textParts.forEach(part => {
                    // Se usa TEXT y se escapa el contenido para evitar conflictos con comillas
                    commands += `TEXT ${startX},${currentY},${font},0,1,1,"${part.replace(/"/g, '\\"')}"\n`;
                    currentY += lineHeight;
                });
            };

            // --- 4. Contenido de la Etiqueta ---
            addTextLine("Fecha:", this.formatDateTime());
            addTextLine("Nombre:", this.removeAccents(this.form.client_name.slice(0, 20)));
            addTextLine("Equipo:", this.removeAccents(this.form.product_details?.brand) + ' ' + this.removeAccents(this.form.product_details?.model));
            addTextLine("Total:", '$' + (this.totalSpareParts + parseFloat(this.form.service_cost))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            addTextLine("Desbloqueo:", this.form.aditionals?.unlockPassword ?? 'Por patron');
            addTextLine("Problemas:", this.removeAccents(this.form.observations));
            // addTextLine("Servicio:", this.removeAccents(this.form.service_description));
            // addTextLine("Tecnico:", this.removeAccents(this.form.technician_name));
            addTextLine("Telefono cliente:", this.form.client_phone_number);

            // --- 5. Código de Barras ---
            if (this.newFolio) {
                // currentY += 5; // Espacio extra antes del código de barras
                const folioPadded = String(this.newFolio).padStart(3, '0');
                const humanReadable = this.$page.props.auth.user.printer_config?.labelBarCodeHumanReadable || 0;

                // BARCODE X,Y,"TIPO",ALTURA,LEER_HUMANO,ROTACION,ANCHO_ESTRECHO,ANCHO_ANCHO,"CONTENIDO"
                const barcodeHeight = 22;    // Altura del código en dots
                const narrowWidth = 2;     // Ancho de la barra más estrecha
                const wideWidth = 5;       // Ancho de la barra más ancha

                // Centrar el código de barras (opcional)
                const barcodeX = startX;

                commands += `BARCODE ${barcodeX},${currentY},"128",${barcodeHeight},${humanReadable},0,${narrowWidth},${wideWidth},"${folioPadded}"\n`;
                currentY += barcodeHeight + 20; // Actualizar Y después del barcode
            }

            // --- 6. Comando de Impresión ---
            // PRINT N, M -> Imprime N copias de la etiqueta M veces.
            // Usamos PRINT 1 para imprimir una sola copia de la etiqueta diseñada.
            commands += 'PRINT 1\n';

            // console.log("Comandos TSPL Generados:\n", commands); // Útil para depuración
            return commands;
        },
        removeAccents(text = '') {
            if (!text) return '';
            // cambiar ñ por n
            text = text.replace(/ñ/g, 'n').replace(/Ñ/g, 'N');
            // Normalizar el texto y eliminar los acentos
            return text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        },
        formatDate(dateString = new Date().toISOString()) {
            return format(parseISO(dateString), 'dd MMMM yyyy, h:mm a', { locale: es });
        },
        formatDateTime(dateString = new Date().toISOString()) {
            return format(parseISO(dateString), 'dd MMMM yyyy, h:mm a', { locale: es });
        },
        onPercentageInput(value) {
            // Elimina cualquier carácter no numérico (excepto punto decimal si quieres permitirlo)
            let cleanValue = value.replace(/[^\d]/g, '');

            // Limita entre 0 y 100
            let numericValue = parseInt(cleanValue);
            if (isNaN(numericValue)) {
                numericValue = '';
            } else if (numericValue > 100) {
                numericValue = 100;
            }

            this.form.comision_percentage = numericValue;
        },
        // sincroniza los items de refacciones
        syncItems(spareParts) {
            this.form.spare_parts = spareParts;
        },
        // sincroniza los puntos seleccionados del patron
        syncPattern(unlockItem) {
            this.form.aditionals.unlockPattern = unlockItem;
        },
        syncPassword(unlockItem) {
            this.form.aditionals.unlockPassword = unlockItem;
        },
        // uploader
        beforeUpload(file) {
            const isImage = file.type.startsWith('image/');
            if (!isImage) {
                ElMessage.error('Solo se permiten imágenes.');
            }
            return isImage;
        },
        handleChange(file, fileList) {
            this.form.media = fileList.map(item => item.raw); // Actualiza form.media con los archivos
        },
        handleSuccess(response, file, fileList) {
            ElMessage.success('Archivo subido correctamente');
            this.uploading = false;
            this.uploadPercentage = 0;
        },
        handleRemoveImage(file, fileList) {
            // Remover de form.media
            const mediaIndex = this.form.media.indexOf(file.raw);
            if (mediaIndex !== -1) {
                this.form.media.splice(mediaIndex, 1); // Elimina el archivo de form.media
            }
            // Remover del componente
            const mediaUploadIndex = this.fileList.indexOf(file);
            if (mediaUploadIndex !== -1) {
                this.fileList.splice(mediaUploadIndex, 1); // Elimina el archivo de form.media
            }
        },
        handleExceed() {
            ElMessage.warning('Has alcanzado el límite de archivos');
        },
        handlePictureCardPreview(file) {
            this.dialogImageUrl = file.url;
            this.dialogVisible = true;
        },
    },
}
</script>
<style scoped>
.upload-demo {
    border: 2px dashed #d9d9d9;
    border-radius: 6px;
    padding: 20px;
    text-align: center;
}
</style>
