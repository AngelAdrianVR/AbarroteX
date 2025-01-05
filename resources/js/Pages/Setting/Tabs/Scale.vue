<template>
    <section class="my-5 space-y-3 md:space-y-0 lg:grid grid-cols-2 gap-3">
        <!-- PUERTO ----------------------------------------->
        <!-- --------------------------------------------------------- -->
        <article class="border border-grayD9 rounded-md p-4 relative">
        <h2 class="font-bold">Puerto de conexión:</h2>
        <p class="text-sm text-gray99 mb-4">Selecciona el puerto al cual está conectada tu báscula.</p>
        <div class="flex items-center text-sm">

            <p class="mx-7">Puerto:</p>
            <div v-if="loadingPorts">
                <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            <el-select v-else v-model="form.scale_config.port" class="!w-48"
                clearable placeholder="Seleccione" no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="port in availablePorts" :key="port" :value="port" :label="port" />
            </el-select>
            <el-tooltip content="Refrescar puertos" placement="right">
                <button @click="fetchAvailablePorts()" class="rounded-full bg-gray-100 flex items-center justify-center p-1 ml-2"> 
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-primary">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </button>
            </el-tooltip>
        </div>
        </article>

        <!-- BAUD RATE ----------------------------------------->
        <!-- --------------------------------------------------------- -->
        <article class="border border-grayD9 rounded-md p-4 relative">
        <h2 class="font-bold">Baud Rate:</h2>
        <p class="text-sm text-gray99 mb-4">Es la velocidad de transmision de datos. Normalmente se encuentra en el manual de tu dispositivo</p>
        <div class="flex items-center text-sm">

            <p class="mx-7">Baud Rate:</p>
            <el-select v-model="form.scale_config.baudRate" class="!w-48"
                clearable placeholder="Seleccione" no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in baudRates" :key="item" :value="item" :label="item" />
            </el-select>
        </div>
        </article>

        <!-- PARITY ----------------------------------------->
        <!-- --------------------------------------------------------- -->
        <article class="border border-grayD9 rounded-md p-4 relative">
        <h2 class="font-bold">Paridad:</h2>
        <p class="text-sm text-gray99 mb-4">Permite verificar si se han producido errores durante la transmisión. Por defecto "none"</p>
        <div class="flex items-center text-sm">

            <p class="mx-7">Paridad:</p>
            <el-select v-model="form.scale_config.parity" class="!w-48"
                clearable placeholder="Seleccione" no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="parity in parities" :key="parity" :value="parity" :label="parity" />
            </el-select>
        </div>
        </article>

        <!-- DATA BIT ----------------------------------------->
        <!-- --------------------------------------------------------- -->
        <article class="border border-grayD9 rounded-md p-4 relative">
        <h2 class="font-bold">Bits de datos:</h2>
        <p class="text-sm text-gray99 mb-4">Indica el número de bits utilizados para representar un único carácter de datos durante la comunicación en serie</p>
        <div class="flex items-center text-sm">

            <p class="mx-7">Bits de datos:</p>
            <el-select v-model="form.scale_config.dataBit" class="!w-48"
                clearable placeholder="Seleccione" no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="dataBit in dataBits" :key="dataBit" :value="dataBit" :label="dataBit" />
            </el-select>
        </div>
        </article>

        <!-- STOP BITS ----------------------------------------->
        <!-- --------------------------------------------------------- -->
        <article class="border border-grayD9 rounded-md p-4 relative">
        <h2 class="font-bold">Bits de parada:</h2>
        <p class="text-sm text-gray99 mb-4">El bit de parada es una comprobación de error muy básica para detectar una discordancia en la velocidad de transmisión o en la longitud de bytes.</p>
        <div class="flex items-center text-sm">

            <p class="mx-7">Bits de parada:</p>
            <el-select v-model="form.scale_config.stopBit" class="!w-48"
                clearable placeholder="Seleccione" no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="stopBit in stopBits" :key="stopBit" :value="stopBit" :label="stopBit" />
            </el-select>
        </div>
        </article>

        <!-- FLOW CONTROL ----------------------------------------->
        <!-- --------------------------------------------------------- -->
        <article class="border border-grayD9 rounded-md p-4 relative">
        <h2 class="font-bold">Flujo de control:</h2>
        <p class="text-sm text-gray99 mb-4">Método en el que el dispositivo serie controla la cantidad de datos que se transmiten al mismo.</p>
        <div class="flex items-center text-sm">

            <p class="mx-7">Flujo de control:</p>
            <el-select v-model="form.scale_config.flowControl" class="!w-48"
                clearable placeholder="Seleccione" no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="flowControl in flowControls" :key="flowControl" :value="flowControl" :label="flowControl" />
            </el-select>
        </div>
        </article>

        <!-- ACTIVAR BÁSCULA EN PUNTO DE VENTA-------------->
        <!-- --------------------------------------------------------- -->
        <article class="border border-grayD9 rounded-md p-4 relative">
        <h2 class="font-bold">Habilitar báscula</h2>
        <p class="text-sm text-gray99 mb-4">Habilita el uso de báscula en punto de venta</p>
        <div class="flex items-center text-sm">

            <p class="mx-7">Habilitar:</p>
            <el-switch v-model="form.scale_config.is_enabled" class="ml-4" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
        </div>
        </article>

        <div class="text-right pt-10 md:mx-7 col-span-full">
            <PrimaryButton :disabled="form.processing" @click="updateScaleConfig">
                <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                Guadar cambios
            </PrimaryButton>
        </div>

        <!-- Sinconizar báscula -->
        <div class="col-span-full mt-5">
            <h2 class="font-bold mb-2">Prueba de sincronización</h2>
            <h1 v-if="isConnected" class="mb-2">Lectura de peso: {{ weight }} kg</h1>
            <div class="flex items-center space-x-4">
                <PrimaryButton v-if="!isConnected" @click="connectScale">Sincronizar báscula</PrimaryButton>
                <button class="px-3 py-2 bg-green-500 text-white rounded-full text-sm disabled:cursor-not-allowed disabled:bg-gray-400" v-if="isConnected" :disabled="isReading" @click="startReading">Iniciar Lectura</button>
                <button class="px-3 py-2 bg-orange-700 text-white rounded-full text-sm disabled:cursor-not-allowed disabled:bg-gray-400" v-if="isConnected" :disabled="!isReading" @click="stopReading">Detener Lectura</button>
                <button class="px-3 py-2 bg-red-500 text-white rounded-full text-sm disabled:cursor-not-allowed disabled:bg-gray-400" @click="disconnectScale" :disabled="!port">Finalizar prueba</button>
            </div>
        </div>

    </section>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {

    const form = useForm({
        scale_config: {
            port : this.$page.props.auth.user.scale_config?.port,
            baudRate : this.$page.props.auth.user.scale_config?.baudRate,
            parity : this.$page.props.auth.user.scale_config?.parity,
            dataBit : this.$page.props.auth.user.scale_config?.dataBit,
            stopBit : this.$page.props.auth.user.scale_config?.stopBit,
            flowControl : this.$page.props.auth.user.scale_config?.flowControl,
            is_enabled : !! this.$page.props.auth.user.scale_config?.is_enabled ?? true,
        }
    });

    return {
        //General
        form,
        loadingPorts: false, // Para indicar si se están cargando los puertos

        //báscula
        isConnected: false, // Estado de conexión
        port: null, // Puerto serie de la báscula
        reader: null, // Lector de datos del puerto
        weight: "0.00", // Peso leído de la báscula
        intervalId: null, // Para guardar el ID del intervalo
        isReading: false, // Para controlar el estado de lectura
        isPaused: false,   // Para indicar si la lectura fue pausada

        //options
        availablePorts: [], //puertos disponibles recuperados de la API
        baudRates: [4800, 9600, 19200, 38400, 57600, 115200], //velocidades de transmision
        parities: ['none', 'odd', 'even'], //velocidades de transmision
        dataBits: [5, 6, 7, 8], //bits de datos
        stopBits: [1, 2, 3, 4], //bits de parada
        flowControls: ['none', 'xon/xoff', 'rts/cts'], //flujo de control
    }
},
components:{
    PrimaryButton
},
props:{
    store: Object,
},
methods:{
    updateScaleConfig() {
        this.form.put(route('scale.configure', this.$page.props.auth.user.id), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Báscula configurada correctamente",
                    type: "success",
                });
            },
        });
    },
    async connectScale() {
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
            if ( response.status === 200 ) {
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
