<template>
    <Head title="Imprimir cortes" />

    <div id="text-to-print" v-if="date" class="ticket">
        <h2 class="center">Corte de Caja</h2>
        <p class="center">Fecha: {{ formatDate(date) }}</p>
        <p class="center">Cantidad de cortes: {{ cuts.length }}</p>
        <p class="mx-auto">--------------------------------</p>

        <div v-for="(cut, index) in cuts" :key="index" class="cut">
            <p>- {{ formatTime(cut.created_at) }}</p>
            <span class="font-bold">Caja: <span class="font-thin ml-1">{{ cut.cash_register.name }}</span></span><br>
            <span class="font-bold">Usuario: <span class="font-thin">{{ cut.user.name }}</span></span><br>
            <span class="font-bold">Efectivo inicial: <span class="font-thin">${{ cut.started_cash?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></span><br>
            <span class="font-bold">Venta efectivo: <span class="font-thin">${{ ((cut.store_sales_cash || 0) + (cut.online_sales_cash || 0))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></span><br>
            <span class="font-bold">Venta tarjeta: <span class="font-thin">${{ ((cut.store_sales_card || 0) + (cut.online_sales_card || 0))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></span><br>
            <span class="font-bold">Venta total: <span class="font-thin">${{ calculateTotal(cut)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></span><br>
            <span class="font-bold">Total en movimientos: <span class="font-thin">${{ calcularTotalMovimientos(index).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></span><br>
            <span class="font-bold">Esperado en caja: <span class="font-thin">${{ cut.expected_cash.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></span><br>
            <span class="font-bold">Conteo manual de caja: <span class="font-thin">${{ cut.counted_cash?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></span><br>
            <span class="font-bold">Diferencia: <span class="font-thin">${{ cut.difference?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></span><br>
            <p class="mx-auto">--------------------------------</p>
        </div>

        <div class="totals">
            <span><strong>Total pago en efectivo:</strong> ${{ groupedCashCuts[date].total_store_sales_cash?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span><br>
            <span><strong>Total pago con tarjeta:</strong> ${{ groupedCashCuts[date].total_store_sales_card?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span><br>
            <span v-if="$page.props.auth.user.store.activated_modules.includes('Tienda en línea')"><strong>Total ventas en línea:</strong> ${{ groupedCashCuts[date].total_online_sales?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span><br v-if="$page.props.auth.user.store.activated_modules.includes('Tienda en línea')">
            <span><strong>Total general:</strong> ${{ groupedCashCuts[date].total_sales?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span><br>
            <span><strong>Diferencia total:</strong> ${{ groupedCashCuts[date].total_difference?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span><br>

            <p class="mx-auto">--------------------------------</p>
            <p class="mx-auto">--------------------------------</p>
        </div>
    </div>

    <section class="text-center mt-7 mx-4 flex flex-wrap justify-center gap-3">
        <!-- Conectar impresora -->
        <button
            :disabled="!UUIDService && !UUIDCharacteristic"
            v-if="!printTicket"
            @click="conectarBluetooth"
            class="flex items-center gap-2 px-4 py-2 border-2 border-blue-600 text-blue-600 font-semibold rounded-xl transition duration-200 hover:bg-blue-600 hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <i class="fa-brands fa-bluetooth text-lg"></i>
            Conectar impresora
        </button>

        <!-- Imprimir pantalla desde PC -->
        <button
            v-if="!printTicket"
            @click="print"
            class="flex items-center gap-2 px-4 py-2 bg-gray-800 text-white font-semibold rounded-xl transition duration-200 hover:bg-gray-900"
        >
            <i class="fa-solid fa-display"></i>
            Imprimir Pantalla
        </button>

        <!-- Imprimir desde dispositivo móvil -->
        <button
            v-if="device && !printTicket"
            @click="enviarDatosImpresion(device)"
            class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white font-semibold rounded-xl transition duration-200 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <i class="fa-solid fa-print"></i>
            Imprimir ticket
        </button>
    </section>
</template>


<script>
import { Head } from '@inertiajs/vue3';

export default {
    props: {
        groupedCashCuts: {
            type: Object,
            required: true,
        },
    },
    data() {
    return {
            cashCutMovements: [], // Arreglo para almacenar los movimientos de caja por corte
            date: Object.keys(this.groupedCashCuts)[0], // ❌ causa error si groupedCashCuts no está aún
            printTicket: false,
            device: null, //Dispositivo de impresora guardada al hacer vínculo
            text: null, //guarda el texto a pimprimir. (ticket)
            UUIDService: this.$page.props.auth.user.printer_config?.UUIDService,
            UUIDCharacteristic: this.$page.props.auth.user.printer_config?.UUIDCharacteristic,
    };
    },
    components: {
        Head,
    },
    computed: {
        // date() {
        //     if (!this.groupedCashCuts || typeof this.groupedCashCuts !== 'object') return null;
        //     const keys = Object.keys(this.groupedCashCuts);
        //     return keys.length > 0 ? keys[0] : null;
        // },
        cuts() {
            if (!this.groupedCashCuts || !this.date) return [];
            return this.groupedCashCuts[this.date]?.cuts || [];
        },
    },
  methods: {
        calcularTotalMovimientos(index) {
            const movimientos = this.cashCutMovements?.[index] || [];

            return movimientos.reduce((total, mov) => {
            return total + (mov.type === 'Ingreso' ? mov.amount : -mov.amount);
            }, 0);
        },
        formatDate(dateString) {
            if (!dateString) return '';
            
            const [year, month, day] = dateString.split('-');
            const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            const monthIndex = parseInt(month, 10) - 1;
            
            return `${day}/${monthNames[monthIndex]}/${year}`;
        },
        formatTime(datetime) {
        return new Date(datetime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },
        calculateTotal(cut) {
        return (
            (cut.store_sales_cash || 0) +
            (cut.store_sales_card || 0) +
            (cut.online_sales_cash || 0) +
            (cut.online_sales_card || 0)
        );
        },
        conectarBluetooth() {
        // Solicitar al usuario que seleccione la impresora vía Bluetooth
        navigator.bluetooth.requestDevice({
            acceptAllDevices: true, // Aceptar cualquier dispositivo Bluetooth
            optionalServices: [this.UUIDService] // UUID del servicio de la impresora
        })
        .then(device => {
            console.log('Dispositivo Bluetooth conectado:', device);
            this.device = device;
            console.log(this.device);

            // Enviar el objeto device al servidor
            axios.post(route('users.save-printer', this.$page.props.auth.user.id), {
            printer: this.device
            })
            .then(response => {
            console.log('Dispositivo guardado en la base de datos:', response.data);
            })
            .catch(error => {
            console.error('Error al guardar el dispositivo en la base de datos:', error);
            });
        })
        .catch(error => {
            console.error('Error al conectar con dispositivo Bluetooth:', error);
        });
        },
        async enviarDatosImpresion(device) {
        try {
            // Obtener el servicio de impresión
            const service = await device.gatt.connect().then(server => server.getPrimaryService(this.UUIDService));

            // Obtener el carácterística de escritura
            const characteristic = await service.getCharacteristic(this.UUIDCharacteristic);

            // // Aquí puedes enviar los datos de impresión a través de la característica de escritura
            // // Por ejemplo, puedes enviar una cadena de texto a imprimir

            // Dividir el texto en fragmentos
            const fragmentSize = 20; // Ajusta este tamaño según sea necesario
            const fragments = this.chunkText(this.text, fragmentSize);

            // Enviar cada fragmento por separado
            for (const fragment of fragments) {
                await characteristic.writeValue(new TextEncoder('utf-8').encode(fragment)); //con caracteres especiasles
            //   await characteristic.writeValue(new TextEncoder().encode(fragment)); //sin caracteres especiasles
            }

            console.log('Datos de impresión enviados correctamente');
        } catch (error) {
            console.error('Error al enviar datos de impresión:', error);
        }
        },
        chunkText(text, size) {
            const chunks = [];
            for (let i = 0; i < text.length; i += size) {
                chunks.push(text.slice(i, i + size));
            }
            return chunks;
        },
        print() {
            this.printTicket = true;
            setTimeout(() => {
                window.print();
            }, 200);
        },
        handleAfterPrint() {
            this.printTicket = false;
        },
        async getCashCutMovements(cash_cut) {
            this.loadingMovements = true;
            try {
                // Realizar una solicitud para obtener los movimientos de caja asociados al corte actual
                const response = await axios.get(route('cash-cuts.get-movements', cash_cut.id));
                if (response.status === 200) {
                    // Almacenar los movimientos de caja en el objeto cashCutMovements utilizando el ID del corte como clave
                    this.cashCutMovements.push(response.data.items);
                }
            } catch (error) {
                console.error('Error al obtener los movimientos de caja:', error);
            } finally {
                this.loadingMovements = false;
            }
        }
    },
    mounted() {
        //se recorre el arreglo de cortes para obtener los movimientos de cada uno
        Object.values(this.groupedCashCuts)[0].cuts.forEach(cut => {
            this.getCashCutMovements(cut);
        });
        window.addEventListener('afterprint', this.handleAfterPrint);
        this.text = document.getElementById('text-to-print').innerText;
        console.log(this.cashCutMovements);
    },
    beforeDestroy() {
        window.removeEventListener('afterprint', this.handleAfterPrint);
    }
};
</script>

<style scoped>
.ticket {
  font-family: monospace;
  width: 280px;
  padding: 10px;
  margin: auto;
  background: white;
  color: #000;
}

.center {
  text-align: center;
}

.cut {
  margin-bottom: 10px;
}

hr {
  border-top: 1px dashed #000;
  margin: 5px 0;
}

.totals {
  margin-top: 10px;
  text-align: left;
  font-weight: bold;
}
</style>
