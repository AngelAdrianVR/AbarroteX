<template>
  <div class="scale-reader">
    <h1>Peso: {{ weight }} kg</h1>
    <h1>Producto: {{ product.name }}</h1>
    <h1>Precio: {{ product.price }} /kg</h1>
    <h1>Total: {{ total }}</h1>
    <button class="px-3 py-1 bg-primary text-white rounded-full" @click="connectScale">Conectar Báscula</button>
    <button class="px-3 py-1 bg-green-500 text-white rounded-full disabled:cursor-not-allowed disabled:bg-gray-400" @click="readWeight" :disabled="!port">Leer Peso</button>
    <button class="px-3 py-1 bg-red-500 text-white rounded-full disabled:cursor-not-allowed disabled:bg-gray-400" @click="disconnectScale" :disabled="!port">Cerrar báscula</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      port: null, // Puerto serie de la báscula
      reader: null, // Lector de datos del puerto
      weight: "0.00", // Peso leído de la báscula
      total: null, // Total a pagar 
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

        // Configurar la conexión con los parámetros adecuados para tu báscula
        await this.port.open({
            baudRate: 9600, // Ajusta el baudRate según tu báscula
            dataBits: 8,    // Ajusta según las especificaciones de tu báscula
            stopBits: 1,    // Ajusta según las especificaciones de tu báscula
            parity: "none", // Ajusta según las especificaciones de tu báscula
            flowControl: "none", // Ajusta según las especificaciones de tu báscula
        });

        const textDecoder = new TextDecoderStream();
        const readableStreamClosed = this.port.readable.pipeTo(textDecoder.writable);
        this.reader = textDecoder.readable.getReader();

        console.log("Conexión exitosa a la báscula");
        console.log("Puerto:", this.port);

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
            if (done) {
            console.log("Lectura finalizada.");
            this.reader.releaseLock();
            this.reader = null;
            return;
            }

            //total a pagar
            this.total = this.weight * this.product.price;
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
            console.log("Báscula desconectada.");
        } catch (error) {
            console.error("Error al desconectar la báscula:", error);
            alert("Error al desconectar la báscula. Intenta de nuevo.");
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
