<template>
    <p>
        En esta sección puedes conectar y configurar la báscula que utilizarás en tu sistema de punto de venta.
        Asegúrate de que la báscula sea compatible y esté correctamente conectada para que los pesos se reflejen
        automáticamente al realizar una venta.
    </p>
    <section class="my-5 divide-y-[1px] border border-grayD9 rounded-[5px]">
        <article class="rounded-t-md p-4 text-sm">
            <h2 class="font-bold flex items-center space-x-2">
                <span class="text-gray37">Guía rápida para configuración de la bascula</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                </svg>
            </h2>
            <ol class="text-[#575757] mt-2 list-decimal list-inside">
                <li>Asegúrate de que la báscula esté conectada físicamente al dispositivo.</li>
                <li>Configura cada uno de los parámetros según las especificaciones del fabricante de la báscula.</li>
                <li>Guarda la configuración y realiza una prueba para confirmar que los datos se reciben correctamente.
                </li>
            </ol>
            <p class="text-[#575757] mt-2">A continuación, te explicamos cada uno de los parámetros a
                configurar.</p>
        </article>
        <!-- PUERTO ----------------------------------------->
        <article class="p-4 lg:flex items-center justify-between text-sm">
            <div class="lg:w-1/2">
                <h2>Puerto de conexión</h2>
                <p class="text-[#575757] text-justify">
                    Selecciona el puerto al que está conectada la báscula (por ejemplo, COM1, COM2, etc.). Este puerto
                    puede variar según el dispositivo que estés utilizando y cómo esté conectado (USB, Serial, etc.).
                    <br><br>
                    En caso de que no veas el puerto, haz clic en el icono de actualizar para buscar puertos
                    disponibles. Esto puede tardar unos segundos si el dispositivo acaba de conectarse.
                </p>
            </div>
            <div>
                <div class="flex items-center space-x-2 mt-3 lg:mt-0">
                    <p>Puerto:</p>
                    <i v-if="loadingPorts" class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                    <el-select v-model="form.scale_config.port" @change="updateScaleConfig('port')" class="!w-60"
                        placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="port in availablePorts" :key="port" :value="port" :label="port" />
                    </el-select>
                    <el-tooltip content="Refrescar puertos" placement="top">
                        <button @click="fetchAvailablePorts()" class="flex items-center justify-center ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-[13px] text-primary">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </button>
                    </el-tooltip>
                </div>
                <p v-if="loadingPort" class="text-gray-400 text-end text-xs">Guardando...</p>
            </div>
        </article>
        <!-- PARITY ----------------------------------------->
        <article class="p-4 lg:flex items-center justify-between text-sm">
            <div class="lg:w-1/2">
                <h2>Paridad</h2>
                <p class="text-[#575757] text-justify">
                    Selecciona la opción que corresponda a la configuración de tu báscula. Si no estás seguro, consulta
                    el manual del dispositivo. Por defecto, muchas básculas utilizan “Ninguna (None)”
                </p>
            </div>
            <div class="mr-5">
                <div class="flex items-center space-x-2 mt-3 lg:mt-0">
                    <p>Paridad:</p>
                    <el-select v-model="form.scale_config.parity" @change="updateScaleConfig('parity')" class="!w-48"
                        placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="parity in parities" :key="parity" :value="parity" :label="parity" />
                    </el-select>
                </div>
                <p v-if="loadingParity" class="text-gray-400 text-end text-xs">Guardando...</p>
            </div>
        </article>
        <!-- Stop BIT ----------------------------------------->
        <article class="p-4 lg:flex items-center justify-between text-sm">
            <div class="lg:w-1/2">
                <h2>Bits de parada</h2>
                <p class="text-[#575757] text-justify">
                    La mayoría de las básculas funcionan con 1 bit de parada, pero verifica las especificaciones del
                    fabricante.
                </p>
            </div>
            <div class="mr-5">
                <div class="flex items-center space-x-2 mt-3 lg:mt-0">
                    <p>Bits de parada:</p>
                    <el-select v-model="form.scale_config.stopBit" @change="updateScaleConfig('stopBit')" class="!w-48"
                        placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="stopBit in stopBits" :key="stopBit" :value="stopBit" :label="stopBit" />
                    </el-select>
                </div>
                <p v-if="loadingStopBits" class="text-gray-400 text-end text-xs">Guardando...</p>
            </div>
        </article>
        <!-- Data BITS ----------------------------------------->
        <article class="p-4 lg:flex items-center justify-between text-sm">
            <div class="lg:w-1/2">
                <h2>Bits de datos</h2>
                <p class="text-[#575757] text-justify">
                    Verifica las especificaciones de la báscula para seleccionar la opción correcta. Las opciones mas
                    comunes son: 7 bits o 8 bits
                </p>
            </div>
            <div class="mr-5">
                <div class="flex items-center space-x-2 mt-3 lg:mt-0">
                    <p>Bits de datos:</p>
                    <el-select v-model="form.scale_config.dataBit" @change="updateScaleConfig('dataBit')" class="!w-48"
                        placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="dataBit in dataBits" :key="dataBit" :value="dataBit" :label="dataBit" />
                    </el-select>
                </div>
                <p v-if="loadingDataBits" class="text-gray-400 text-end text-xs">Guardando...</p>
            </div>
        </article>
        <!-- BAUD RATE ----------------------------------------->
        <article class="p-4 lg:flex items-center justify-between text-sm">
            <div class="lg:w-1/2">
                <h2>Baud Rate (Tasa de Baudios)</h2>
                <p class="text-[#575757] text-justify">
                    Configura el mismo baud rate que aparece en la báscula o en su manual. Ejemplos más comunes: 9600,
                    19200, 38400
                </p>
            </div>
            <div class="mr-5">
                <div class="flex items-center space-x-2 mt-3 lg:mt-0">
                    <p>Baud Rate:</p>
                    <el-select v-model="form.scale_config.baudRate" @change="updateScaleConfig('baudRate')"
                        class="!w-48" placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in baudRates" :key="item" :value="item" :label="item" />
                    </el-select>
                </div>
                <p v-if="loadingBaud" class="text-gray-400 text-end text-xs">Guardando...</p>
            </div>
        </article>
        <!-- FLOW CONTROL ----------------------------------------->
        <article class="p-4 lg:flex items-center justify-between text-sm">
            <div class="lg:w-1/2">
                <h2>Flujo de control</h2>
                <p class="text-[#575757] text-justify">
                    El flujo de control ayuda a prevenir la pérdida de datos en la comunicación. Usa "Ninguno (None)" si
                    no estás seguro, pero verifica las instrucciones del fabricante para asegurarte.
                </p>
            </div>
            <div class="mr-5">
                <div class="flex items-center space-x-2 mt-3 lg:mt-0">
                    <p>Flujo de control</p>
                    <el-select v-model="form.scale_config.flowControl" @change="updateScaleConfig('flowControl')"
                        class="!w-48" placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="flowControl in flowControls" :key="flowControl" :value="flowControl"
                            :label="flowControl" />
                    </el-select>
                </div>
                <p v-if="loadingFlowControl" class="text-gray-400 text-end text-xs">Guardando...</p>
            </div>
        </article>
        <!-- ACTIVAR BÁSCULA EN PUNTO DE VENTA-------------->
        <article class="p-4 text-sm">
            <h2>Prueba de sincronización</h2>
            <p class="text-[#575757] text-justify">
                Haz clic para verificar si la báscula esta correctamente conectada y sincronizada con el sistema
            </p>
            <div class="inline-flex items-center space-x-12 border border-grayD9 rounded-xl p-4 mt-4">
                <p class="text-[#3F9E03] text-xs flex items-center space-x-2 justify-center">
                    <span>Sincronización exitosa</span>
                    <svg width="14" height="11" viewBox="0 0 9 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M2.12186 7.11465C1.47962 5.27606 0.083442 3.81365 0.0044574 3.72681C-0.0745272 3.63997 0.904352 3.47505 2.29125 4.91255C4.92022 1.9362 7.46881 -0.00796936 8.89754 0.000182668C8.95258 -0.00542487 9.03703 0.119565 8.98224 0.169575C5.81895 1.67575 4.06786 4.3519 2.54534 7.11465C2.53615 7.19837 2.11822 7.18172 2.12186 7.11465Z"
                            fill="#189203" />
                    </svg>
                </p>
                <p class="text-[#9E0303] text-xs flex items-center space-x-2 justify-center">
                    <i class="fa-solid fa-x text-[11px]"></i>
                    <span>No se detectó la báscula.</span>
                </p>
                <button v-if="!isConnected" @click="connectScale" class="bg-[#EDEDED] rounded-full px-5 py-1">
                    Probar sincronización
                </button>
            </div>
        </article>
        <!-- <article class="p-4 relative">
            <h2 class="font-bold">Habilitar báscula</h2>
            <p class="text-sm text-gray99 mb-4">Habilita el uso de báscula en punto de venta</p>
            <div class="flex items-center text-sm">

                <p class="mx-7">Habilitar:</p>
                <el-switch v-model="form.scale_config.is_enabled" class="ml-4" size="small"
                    style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9" />
            </div>
        </article> -->

        <!-- <div class="text-right pt-10 md:mx-7 col-span-full">
            <PrimaryButton :disabled="form.processing" @click="updateScaleConfig">
                <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                Guadar cambios
            </PrimaryButton>
        </div> -->
        <!-- Sinconizar báscula -->
        <!-- <div class="col-span-full mt-5">
            <h2 class="font-bold mb-2">Prueba de sincronización</h2>
            <h1 v-if="isConnected" class="mb-2">Lectura de peso: {{ weight }} kg</h1>
            <div class="flex items-center space-x-4">
                <PrimaryButton v-if="!isConnected" @click="connectScale">Sincronizar báscula</PrimaryButton>
                <button
                    class="px-3 py-2 bg-green-500 text-white rounded-full text-sm disabled:cursor-not-allowed disabled:bg-gray-400"
                    v-if="isConnected" :disabled="isReading" @click="startReading">Iniciar Lectura</button>
                <button
                    class="px-3 py-2 bg-orange-700 text-white rounded-full text-sm disabled:cursor-not-allowed disabled:bg-gray-400"
                    v-if="isConnected" :disabled="!isReading" @click="stopReading">Detener Lectura</button>
                <button
                    class="px-3 py-2 bg-red-500 text-white rounded-full text-sm disabled:cursor-not-allowed disabled:bg-gray-400"
                    @click="disconnectScale" :disabled="!port">Finalizar prueba</button>
            </div>
        </div> -->
        <DialogModal :show="showScaleModal" @close="showScaleModal = false" max-width="md">
            <template #title>
                <h1>Sincronizar</h1>
            </template>
            <template #content>
                <p>Coloca un objeto en la báscula para verificar que el sistema detecte el peso correctamente</p>
                <div class="flex items-center justify-center space-x-2 mt-5">
                    <span>Peso:</span>
                    <span class="text-center border-b border-grayD9 w-32">{{ weight }}</span>
                </div>
                <p class="text-[#3F9E03] text-sm flex items-center space-x-2 justify-center mt-4">
                    <span>Sincronización exitosa</span>
                    <svg width="11" height="10" viewBox="0 0 9 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M2.12186 7.11465C1.47962 5.27606 0.083442 3.81365 0.0044574 3.72681C-0.0745272 3.63997 0.904352 3.47505 2.29125 4.91255C4.92022 1.9362 7.46881 -0.00796936 8.89754 0.000182668C8.95258 -0.00542487 9.03703 0.119565 8.98224 0.169575C5.81895 1.67575 4.06786 4.3519 2.54534 7.11465C2.53615 7.19837 2.11822 7.18172 2.12186 7.11465Z"
                            fill="#189203" />
                    </svg>
                </p>
                <p class="text-[#9E0303] text-sm flex items-center space-x-2 justify-center mt-4">
                    <i class="fa-solid fa-x text-[11px]"></i>
                    <span>No se detectó la báscula.</span>
                </p>
                <p class="text-[#9E0303] text-sm text-center mt-1">
                    Asegúrate de que este conectada, con las configuraciones correctas y coloca un objeto para la
                    prueba.
                </p>
            </template>
            <template #footer>
                <PrimaryButton @click="showScaleModal = false">Finalizar sincronización</PrimaryButton>
            </template>
        </DialogModal>
    </section>
</template>

<script>
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from "@inertiajs/vue3";
import axios from 'axios';

export default {
    data() {

        const form = useForm({
            scale_config: {
                port: this.$page.props.auth.user.scale_config?.port,
                baudRate: this.$page.props.auth.user.scale_config?.baudRate,
                parity: this.$page.props.auth.user.scale_config?.parity,
                dataBit: this.$page.props.auth.user.scale_config?.dataBit,
                stopBit: this.$page.props.auth.user.scale_config?.stopBit,
                flowControl: this.$page.props.auth.user.scale_config?.flowControl,
                is_enabled: !!this.$page.props.auth.user.scale_config?.is_enabled ?? true,
            }
        });

        return {
            //General
            form,
            loadingPorts: false, // Para indicar si se están cargando los puertos

            //báscula
            showScaleModal: false, // Para mostrar/ocultar el modal de báscula
            isConnected: false, // Estado de conexión
            port: null, // Puerto serie de la báscula
            reader: null, // Lector de datos del puerto
            weight: "0.00", // Peso leído de la báscula
            intervalId: null, // Para guardar el ID del intervalo
            isReading: false, // Para controlar el estado de lectura
            isPaused: false,   // Para indicar si la lectura fue pausada

            //cargas
            loadingPort: false,
            loadingBaud: false,
            loadingDataBits: false,
            loadingStopBits: false,
            loadingFlowControl: false,
            loadingParity: false,

            //options
            availablePorts: [], //puertos disponibles recuperados de la API
            baudRates: [4800, 9600, 19200, 38400, 57600, 115200], //velocidades de transmision
            parities: ['none', 'odd', 'even'], //velocidades de transmision
            dataBits: [5, 6, 7, 8], //bits de datos
            stopBits: [1, 2, 3, 4], //bits de parada
            flowControls: ['none', 'xon/xoff', 'rts/cts'], //flujo de control
        }
    },
    components: {
        PrimaryButton,
        DialogModal,
    },
    props: {
        store: Object,
    },
    methods: {
        updateScaleConfig(configName) {
            if (configName == 'port') {
                this.loadingPort = true;
            } else if (configName == 'baudRate') {
                this.loadingBaud = true;
            } else if (configName == 'dataBit') {
                this.loadingDataBits = true;
            } else if (configName == 'stopBit') {
                this.loadingStopBits = true;
            } else if (configName == 'flowControl') {
                this.loadingFlowControl = true;
            } else if (configName == 'parity') {
                this.loadingParity = true;
            }

            // this.loadingPorts = true;
            this.form.put(route('scale.configure', this.$page.props.auth.user.id), {
                onSuccess: () => {
                },
                onFinish: () => {
                    this.loadingPort = false;
                    this.loadingBaud = false;
                    this.loadingDataBits = false;
                    this.loadingStopBits = false;
                    this.loadingFlowControl = false;
                    this.loadingParity = false;
                },
            });
        },
        async connectScale() {
            this.showScaleModal = true;
            try {
                if (this.port) {
                    await this.port.close(); // Cierra el puerto solo después de liberar el lector
                    this.port = null;
                }

                // Solicitar al usuario seleccionar un dispositivo serie
                this.port = await navigator.serial.requestPort();

                // Configurar la conexión con los parámetros adecuados para tu báscula tomados de la base de datos
                await this.port.open({
                    baudRate: this.$page.props.auth.user.scale_config?.baudRate ?? 9600,
                    dataBits: this.$page.props.auth.user.scale_config?.dataBit ?? 8,
                    stopBits: this.$page.props.auth.user.scale_config?.stopBit ?? 1,
                    parity: this.$page.props.auth.user.scale_config?.parity ?? "none",
                    flowControl: this.$page.props.auth.user.scale_config?.flowControl ?? "none",
                });

                const textDecoder = new TextDecoderStream();
                const readableStreamClosed = this.port.readable.pipeTo(textDecoder.writable);
                this.reader = textDecoder.readable.getReader();

                console.log("Conexión exitosa a la báscula");
                console.log("Puerto:", this.port);

                this.isConnected = true; // Marca la conexión como activa

                this.$notify({
                    title: "Correcto",
                    message: "Báscula sincronizada",
                    type: "success",
                });

            } catch (error) {
                console.error("Error al conectar la báscula:", error);
                alert("No se pudo conectar a la báscula. Verifica que esté correctamente conectada.");
            }
        },
        async startReading() {
            if (!this.port || !this.port.readable || !this.port.writable) {
                alert("Conecta la báscula primero.");
                return;
            }

            if (this.isReading) {
                alert("La lectura ya está en curso.");
                return;
            }

            this.isReading = true;

            this.intervalId = setInterval(async () => {
                try {
                    // Enviar comando para solicitar datos (si es necesario)
                    const textEncoder = new TextEncoder();
                    const writer = this.port.writable.getWriter();
                    await writer.write(textEncoder.encode("COMANDO_PESO\n")); // Cambia "COMANDO_PESO" al comando requerido por tu báscula
                    writer.releaseLock();

                    // Leer datos
                    if (!this.reader) {
                        const textDecoder = new TextDecoder();
                        this.reader = this.port.readable.getReader();
                    }

                    const { value, done } = await this.reader.read();
                    if (done) {
                        console.log("Lectura finalizada.");
                        this.reader.releaseLock();
                        this.reader = null;
                        this.stopReading(); // Detiene la lectura si es el final
                        return;
                    }

                    console.log("Datos leídos:", value);
                    this.weight = this.parseWeight(value);

                } catch (error) {
                    console.error("Error al leer datos:", error);
                    this.stopReading(); // Detener lectura en caso de error
                }
            }, 200); // Intervalo de 500 ms
        },
        stopReading() {
            if (this.intervalId) {
                clearInterval(this.intervalId); // Detiene el intervalo
                this.intervalId = null;
                console.log("Intervalo detenido.");
            }

            this.isReading = false;
            this.isPaused = true; // Marca la lectura como pausada
            console.log("Lectura detenida.");
        },
        async disconnectScale() {
            try {
                if (this.reader) {
                    await this.reader.cancel(); // Cancela cualquier lectura activa
                    this.reader.releaseLock(); // Libera el lector
                    this.reader = null;
                }

                // if (this.port) {
                //     await this.port.close(); // Cierra el puerto solo después de liberar el lector
                //     this.port = null;
                // }

                this.isConnected = false; // Actualiza el estado de conexión
                this.weight = "0.00"; // Reinicia el peso leído
                this.$notify({
                    title: "Correcto",
                    message: "Báscula desconectada",
                    type: "success",
                });
                console.log("Báscula desconectada.");
            } catch (error) {
                console.error("Error al desconectar la báscula:", error);
                alert("Comunicación con la báscula cerrada. Presiona nuevamente para desconectar");
            }
        },
        parseWeight(data) {
            // Ajustar esta lógica según el formato de los datos enviados por la báscula
            const weight = data.trim(); // Quitar espacios en blanco
            return parseFloat(weight) || "0.00";
        },
        async fetchAvailablePorts() {
            this.loadingPorts = true;
            try {
                const response = await axios.get(route('scale.get-ports'));
                if (response.status === 200) {
                    this.availablePorts = response.data;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loadingPorts = false;
            }
        },
    },
    mounted() {
        this.fetchAvailablePorts();
    },
}
</script>
