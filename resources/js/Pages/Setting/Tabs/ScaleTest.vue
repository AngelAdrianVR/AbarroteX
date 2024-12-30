<template>
  <div class="scale-reader">
    <h1>Peso: {{ weight }} kg</h1>
    <h1>Producto: {{ product.name }}</h1>
    <h1>Precio: {{ product.price }} /kg</h1>
    <h1>Total: ${{ (weight * product.price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</h1>
    <button class="px-3 py-1 bg-primary text-white rounded-full disabled:cursor-not-allowed disabled:bg-gray-400" @click="connectScale" :disabled="isConnected">Conectar Báscula</button>
    <button class="px-3 py-1 bg-green-500 text-white rounded-full disabled:cursor-not-allowed disabled:bg-gray-400" @click="readWeight" :disabled="!port">Leer Peso</button>
    <button class="px-3 py-1 bg-red-500 text-white rounded-full disabled:cursor-not-allowed disabled:bg-gray-400" @click="disconnectScale" :disabled="!port">Cerrar báscula</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isConnected: false, // Estado de conexión
      port: null, // Puerto serie de la báscula
      reader: null, // Lector de datos del puerto
      weight: "0.00", // Peso leído de la báscula
      product: {
        name: "manzana", // Nombre del producto
        price: 50, // Precio por kg
      },
    };
  },
  methods: {
    async connectScale() {
      try {
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
    async readWeight() {
        if (!this.port || !this.port.readable || !this.port.writable) {
            alert("Conecta la báscula primero.");
            return;
        }

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
            console.log("Datos leídos:", value);
            if (done) {
              console.log("Lectura finalizada.");
              this.reader.releaseLock();
              this.reader = null;
              return;
            }

            this.weight = this.parseWeight(value);
            
        } catch (error) {
            console.error("Error al leer datos:", error);
        }
    },
    async disconnectScale() {
        try {
            if (this.reader) {
                await this.reader.cancel(); // Cancela cualquier lectura activa
                this.reader.releaseLock(); // Libera el lector
                this.reader = null;
            }

            if (this.port) {
                await this.port.close(); // Cierra el puerto solo después de liberar el lector
                this.port = null;
            }

            this.isConnected = false; // Actualiza el estado de conexión
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
  },
};
</script>

<style scoped>
.scale-reader {
  text-align: center;
}
button {
  margin: 10px;
  padding: 10px;
  font-size: 16px;
  cursor: pointer;
}
</style>
