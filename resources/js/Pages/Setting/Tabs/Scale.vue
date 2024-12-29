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
        <p class="text-sm text-gray99 mb-4">Permite verificar si se han producido errores durante la transmisión. Por defaul "none"</p>
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
        <h2 class="font-bold">Activar báscula</h2>
        <p class="text-sm text-gray99 mb-4">Imprime el ticket de cada venta realizada automáticamente</p>
        <div class="flex items-center text-sm">

            <p class="mx-7">Activar báscula:</p>
            <el-switch v-model="form.scale_config.is_enabled" class="ml-4" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
        </div>
        </article>

        <div class="text-right pt-10 md:mx-7 col-span-full">
            <PrimaryButton :disabled="form.processing" @click="updateScaleConfig">
                <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                Guadar cambios
            </PrimaryButton>
        </div>

        <h2>Lectura de Báscula</h2>
        <div class="flex items-center space-x-4">
            <PrimaryButton @click="connectToScale">Conectar báscula</PrimaryButton>
            <button class="bg-gray-200 py-1 rounded-full px-4 cursor-pointer" @click="disconnectScale">Desconectar Báscula</button>
        </div>

        <p id="weight-display">Peso: -- kg</p>

        <!-- <div class="col-span-full">
            <h2>Cálculo de Precio</h2>
            <div v-if="product">
            <p>Producto: {{ product.name }}</p>
            <p>Precio por Kg: ${{ product.pricePerKg }}</p>
            <p>Peso: {{ weight }} kg</p>
            <p>Total: ${{ calculateTotal() }}</p>
            </div>
        </div> -->
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
        form,
        scaleConfig: null, //configuracion de la báscula parseada
        loadingPorts: false, //cargando puertos
        port: null,
        readWeightInterval: null,
        isConnected: false, // Controla el estado de la conexión

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
    async connectToScale() {
        try {
            // Solicitar acceso al dispositivo serial
            this.port = await navigator.serial.requestPort();

            // Configurar la conexión con los parámetros adecuados para tu báscula
            await this.port.open({
                baudRate: 9600, // Ajusta el baudRate según tu báscula
                dataBits: 7,    // Ajusta según las especificaciones de tu báscula
                stopBits: 1,    // Ajusta según las especificaciones de tu báscula
                parity: "even", // Ajusta según las especificaciones de tu báscula
                flowControl: "none", // Ajusta según las especificaciones de tu báscula
                });

            const decoder = new TextDecoderStream();
            const inputDone = this.port.readable.pipeTo(decoder.writable);
            const inputStream = decoder.readable.getReader();

            console.log("Conectado a la báscula. Leyendo datos...");
            console.log("Decoder", decoder);

            this.isConnected = true; // Marca la conexión como activa

                // console.log(decoder);
            // Leer datos continuamente en un bucle asíncrono
            while (this.isConnected) {
                const { value, done } = await inputStream.read();
                if (done) {
                    console.log("Conexión cerrada.");
                    this.disconnectScale(); // Desconectar si la conexión se cierra
                    return;
                }

                if (value) {
                    const trimmedValue = value.trim();

                    // Verifica si el valor recibido parece ser un peso válido (ajusta según el formato de tu báscula)
                    if (this.isValidWeight(trimmedValue)) {
                    console.log("Peso recibido:", trimmedValue); // Muestra el peso en consola
                    this.updateWeightDisplay(trimmedValue); // Actualiza la interfaz
                    } else {
                    console.log("Datos no válidos:", trimmedValue); // Muestra si los datos no son válidos
                    }
                }
            }
        } catch (error) {
            console.error("Error al conectar con la báscula:", error);
        }
        },
        // Función para validar si el dato recibido es un peso válido
        isValidWeight(weight) {
        // Aquí puedes personalizar la validación según el formato de datos de la báscula
        // Ejemplo de validación básica para un número con decimales
        const regex = /^[0-9]+(\.[0-9]{1,2})?$/;
        return regex.test(weight);
        },
        updateWeightDisplay(weight) {
        const weightDisplay = document.getElementById("weight-display");
        if (weightDisplay) {
            weightDisplay.textContent = `Peso: ${weight} kg`; // Ajusta la unidad
        }
    },
    disconnectScale() {
        if (this.port) {
            this.port.close(); // Cerrar la conexión
        }
        this.isConnected = false; // Marca la conexión como desconectada
        console.log("Báscula desconectada.");
    },
    // async readWeight() {
    //   try {
    //     const response = await axios.get(route('scale.read'));
    //         if ( response.status === 200 ) {
    //             this.weight = parseFloat(response.data.weight);
    //         }
    //   } catch (error) {
    //     console.error('Error leyendo el peso:', error);
    //   }
    // },
    // calculateTotal() {
    //   return (this.weight * this.product.pricePerKg).toFixed(2);
    // },
    
},
mounted() {
    this.fetchAvailablePorts();
},
}
</script>
