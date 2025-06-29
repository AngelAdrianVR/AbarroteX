<template>
  <div>
    <div class="w-full md:w-[420px] mx-auto mt-6">
      <CancelButton v-if="!printTicket" @click="closeTab" class="!bg-[#EDEDED] !text-[#373737]">
        <i class="fa-solid fa-arrow-left"></i>
        Regresar a punto de venta
      </CancelButton>
    </div>

    <Head :title="'imprimir ticket folio ' + sales[0].folio" />
    <div id="text-to-print" class="w-full md:w-[420px] mx-auto text-sm border-2 border-black p-5 my-4 !text-[11px]">
      <div class="flex items-center">
        <p>{{ $page.props.auth.user.store.name }}</p>
      </div>
      <p class="flex items-center justify-between">
        <span>Folio: {{ sales[0]?.folio }}</span>
        <span>{{ formatDate(sales[0]?.created_at) }}</span>
      </p>
      <table class="mt-2 w-full table-fixed">
        <thead>
          <tr class="text-left *:font-bold *:pb-2">
            <th class="w-7/12">Producto</th>
            <th class="w-5/12 text-right">Cant/Precio/Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sale in sales" :key="sale.id">
            <td class="pr-1 align-top">
              <span class="uppercase">{{ sale.product_name }}</span>
            </td>
            <td class="text-right align-top whitespace-nowrap">
              <span>{{ sale.quantity }}x</span>
              <span class="pl-1">${{ (sale.current_price)?.toFixed(2) }} =</span>
              <span class="font-bold pl-1">${{ (sale.quantity * sale.current_price)?.toFixed(2) }}</span>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="flex justify-end mt-1 mr-6">
        <p class="font-bold text-xs">
          Total
          <span class="ml-2">
            ${{ totalSale().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
          </span>
        </p>
      </div>
      <p v-if="!printTicket" class="mt-2 border-t border-dashed border-black"></p>
      <div class="flex flex-col text-[#373737] mt-1">
        <p>Metodo de pago: {{ sales[0].payment_method }}</p>
        <span>GRACIAS POR SU COMPRA</span>
      </div>
      <p v-if="$page.props.auth.user.store.address" class="text-center mt-1 border-b border-dashed border-black">
        {{ $page.props.auth.user.store.address }}
      </p>
    </div>
    <section class="text-center lg:space-x-2 mt-7 mx-10 space-y-4">
      <ThirthButton :disabled="!UUIDService && !UUIDCharacteristic" v-if="!printTicket" @click="conectarBluetooth"
        class="!py-1 !bg-blue-100 !text-blue-600 mr-2">
        <i class="fa-brands fa-bluetooth text-lg mr-2"></i>
        Conectar impresora (Bluetooth)
      </ThirthButton>

      <PrimaryButton :disabled="!UUIDService && !UUIDCharacteristic" v-if="(device && !printTicket)" class="mr-2"
        @click="enviarDatosImpresion()">
        <i class="fa-solid fa-print"></i>
        Imprimir ticket de nuevo (Bluetooth)
      </PrimaryButton>

      <ThirthButton v-if="!printTicket && qzTrayLoaded" @click="getPrinters"
        class="!py-1 !bg-green-100 !text-green-600 mr-2">
        <i class="fa-solid fa-list-ul text-lg mr-2"></i>
        Buscar Impresoras (PC)
      </ThirthButton>

      <div v-if="showPrinterSelector && availablePrinters.length > 0" class="mt-4">
        <label for="printer-select" class="block text-sm font-medium text-gray-700">Selecciona impresora:</label>
        <select id="printer-select" v-model="selectedPrinter"
          class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
          <option v-for="printer in availablePrinters" :key="printer" :value="printer">{{ printer }}</option>
        </select>
        <PrimaryButton v-if="!printTicket" @click="printWithQZ" :disabled="!selectedPrinter"
          class="mt-4 !bg-emerald-600">
          <i class="fa-solid fa-print"></i>
          Imprimir con QZ Tray
        </PrimaryButton>
      </div>
      <p v-if="showPrinterSelector && availablePrinters.length === 0" class="text-sm text-center text-gray-500 mt-4">
        No se encontraron impresoras. Asegúrate de que QZ Tray esté en ejecución.
      </p>

      <PrimaryButton v-if="!printTicket" @click="print" class="!bg-[#EDEDED] !text-[#373737]">
        <i class="fa-solid fa-display"></i>
        Guardar en PDF
      </PrimaryButton>
    </section>
    <p v-if="!UUIDService && !UUIDCharacteristic && !qzTrayLoaded" class="text-sm text-center text-red-600 mt-4">
      No tienes ninguna impresora configurada para Bluetooth y QZ Tray no se ha cargado.
      Para conectar con una impresora térmica vía bluetooth
      <strong @click="$inertia.get(route('settings.index', { tab: 3 }))"
        class="text-primary underline cursor-pointer">configurala aquí</strong>
    </p>
  </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import { Head } from '@inertiajs/vue3';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
// import qz from 'qz-tray'; // Importar qz-tray

export default {
  data() {
    return {
      printTicket: false,
      device: null, //Dispositivo de impresora guardada al hacer vínculo
      UUIDService: this.$page.props.auth.user.printer_config?.UUIDService,
      UUIDCharacteristic: this.$page.props.auth.user.printer_config?.UUIDCharacteristic,

      showPrinterSelector: false,
      availablePrinters: [],
      selectedPrinter: null,
      qzTrayLoaded: false,
    }
  },
  components: {
    PrimaryButton,
    ThirthButton,
    Head,
    CancelButton,
  },
  props: {
    sales: Object,
  },
  methods: {
    closeTab() {
      // Cerrar la pestaña actual
      window.close();
    },
    conectarBluetooth() {
      // Solicitar al usuario que seleccione la impresora vía Bluetooth
      navigator.bluetooth.requestDevice({
        acceptAllDevices: true, // Aceptar cualquier dispositivo Bluetooth
        optionalServices: [this.UUIDService] // UUID del servicio de la impresora
      })
        .then(device => {
          this.device = device;
          this.enviarDatosImpresion();
        })
        .catch(error => {
          console.error('Error al conectar con dispositivo Bluetooth:', error);
        });
    },
    truncatedProductName(name) {
      const maxLength = 16;
      if (name.length > maxLength) {
        return name.slice(0, maxLength) + '...';
      } else {
        return name;
      }
    },
    /**
     * Genera el string del ticket con comandos ESC/POS.
     * @returns {string} El texto formateado para la impresora.
     */
    generarTicketConComandos() {
      // --- Comandos ESC/POS ---
      const ESC = '\x1B'; // Carácter de Escape
      const GS = '\x1D';  // Separador de Grupo

      // Comandos de formato
      const INICIALIZAR_IMPRESORA = ESC + '@';
      const TEXTO_NORMAL = ESC + '!' + '\x00'; // Fuente A (normal)
      const TEXTO_PEQUENO = ESC + '!' + '\x01'; // Fuente B (pequeña)
      const NEGRITA_ON = ESC + 'E' + '\x01';
      const NEGRITA_OFF = ESC + 'E' + '\x00';
      const ALINEAR_IZQUIERDA = ESC + 'a' + '\x00';
      const ALINEAR_CENTRO = ESC + 'a' + '\x01';
      const ALINEAR_DERECHA = ESC + 'a' + '\x02';
      const MODO_CONDENSADO_ON = '\x0F';
      const MODO_CONDENSADO_OFF = '\x12';

      // --- Construcción del Ticket ---
      let ticket = INICIALIZAR_IMPRESORA;

      // Encabezado
      ticket += ALINEAR_CENTRO;
      ticket += NEGRITA_ON + this.$page.props.auth.user.store.name + NEGRITA_OFF + '\n';
      if (this.$page.props.auth.user.store.address) {
        ticket += TEXTO_PEQUENO + this.$page.props.auth.user.store.address + '\n' + TEXTO_NORMAL;
      }
      ticket += ALINEAR_IZQUIERDA;
      ticket += 'Folio: ' + this.sales[0].folio + '\n';
      ticket += this.formatDate(this.sales[0]?.created_at) + '\n';
      ticket += '--------------------------------\n'; // Las impresoras térmicas suelen tener ~32 caracteres de ancho para la fuente normal

      // Cuerpo (Productos) - Usaremos la fuente pequeña para que quepa todo
      ticket += TEXTO_PEQUENO; // Activar modo condensado para ahorrar espacio
      ticket += 'Producto          Cant.  Total   Precio\n'; // Encabezado de la tabla manual
      ticket += '--------------------------------\n'; // Separador para 32 caracteres (fuente pequeña)


      this.sales.forEach(sale => {
        const nombre = sale.product_name.substring(0, 15); // Truncar nombre a 15 caracteres
        const cantidad = sale.quantity.toString();
        const precio = sale.current_price.toFixed(2);
        const totalProducto = (sale.quantity * sale.current_price).toFixed(2);

        // Alineamos manualmente con padding
        let linea = nombre.padEnd(16, ' ');
        linea += cantidad.padStart(4, ' ');
        linea += totalProducto.padStart(9, ' ');
        linea += precio.padStart(9, ' ');

        ticket += linea + '\n';
      });

      // Desactivamos el modo condensado y volvemos a la fuente normal
      ticket += TEXTO_NORMAL;
      ticket += '--------------------------------\n';

      // Total
      const totalStr = 'Total: $' + this.totalSale().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      ticket += ALINEAR_DERECHA + NEGRITA_ON + totalStr + NEGRITA_OFF + '\n\n';

      // Pie de página
      ticket += ALINEAR_IZQUIERDA;
      ticket += 'Metodo de pago: Efectivo\n\n';
      ticket += ALINEAR_CENTRO;
      ticket += 'GRACIAS POR SU COMPRA\n\n\n\n'; // Agregamos saltos de línea para que el papel avance

      return ticket;
    },
    async enviarDatosImpresion() {
      try {
        const service = await this.device.gatt.connect().then(server => server.getPrimaryService(this.UUIDService));
        const characteristic = await service.getCharacteristic(this.UUIDCharacteristic);

        // Generamos el ticket con los comandos justo antes de imprimir
        const datosParaImprimir = this.generarTicketConComandos();

        // Es importante usar un codificador que soporte los caracteres de control
        // 'utf-8' es una buena opción.
        const encodedData = new TextEncoder('utf-8').encode(datosParaImprimir);

        // Dividir los datos si son muy grandes (tu función chunkText es buena para esto)
        const fragmentSize = 20; // Este valor puede que necesites ajustarlo. 50 o 100 a veces funciona mejor.
        const fragments = this.chunkData(encodedData, fragmentSize);

        for (const fragment of fragments) {
          await characteristic.writeValue(fragment);
        }

        console.log('Datos de impresión enviados correctamente (Bluetooth)');
      } catch (error) {
        console.error('Error al enviar datos de impresión (Bluetooth):', error);
      }
    },
    chunkData(data, size) {
      const chunks = [];
      for (let i = 0; i < data.length; i += size) {
        chunks.push(data.slice(i, i + size));
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
    formatDate(dateString) {
      return format(parseISO(dateString), 'dd MMMM yyyy, h:mm a', { locale: es });
    },
    totalSale() {
      return this.sales.reduce((total, item) => {
        return total + item.current_price * item.quantity;
      }, 0);
    },
    // --- Métodos para QZ Tray ---
    async loadQZTray() {
      try {
        // Establecer la ruta a qz-tray.js si no está en la raíz o CDN
        await qz.websocket.connect();
        this.qzTrayLoaded = true;
        console.log('Conectado a QZ Tray');
      } catch (error) {
        console.error('Error al conectar con QZ Tray:', error);
        alert('No se pudo conectar con QZ Tray. Asegúrate de que esté ejecutándose.');
      }
    },
    async getPrinters() {
      try {
        if (!this.qzTrayLoaded) {
          await this.loadQZTray(); // Intentar cargar si no está cargado
          if (!this.qzTrayLoaded) return; // Si no se pudo cargar, salir
        }

        this.availablePrinters = await qz.printers.find();
        if (this.availablePrinters.length > 0) {
          // Seleccionar la impresora "POS-58" si está disponible, de lo contrario, la primera.
          this.selectedPrinter = this.availablePrinters.includes('POS-58') ? 'POS-58' : this.availablePrinters[0];
        }
        this.showPrinterSelector = true;
        console.log('Impresoras disponibles:', this.availablePrinters);
      } catch (error) {
        console.error('Error al obtener impresoras:', error);
        alert('Error al obtener la lista de impresoras. Asegúrate de que QZ Tray esté ejecutándose.');
      }
    },
    async printWithQZ() {
      if (!this.selectedPrinter) {
        alert('Por favor, selecciona una impresora.');
        return;
      }

      try {
        if (!this.qzTrayLoaded) {
          await this.loadQZTray(); // Intentar cargar si no está cargado
          if (!this.qzTrayLoaded) return; // Si no se pudo cargar, salir
        }

        const config = qz.configs.create(this.selectedPrinter);
        const data = [
          {
            type: 'raw', // Usar 'raw' para comandos ESC/POS
            format: 'command', // Indica que son comandos de impresora
            data: this.generarTicketConComandos()
          }
        ];

        await qz.print(config, data);
        console.log('Ticket enviado a impresora (QZ Tray):', this.selectedPrinter);
        alert('Ticket enviado a la impresora ' + this.selectedPrinter + ' correctamente.');
      } catch (error) {
        console.error('Error al imprimir con QZ Tray:', error);
        alert('Error al imprimir con QZ Tray. Asegúrate de que la impresora esté conectada y QZ Tray en ejecución.');
      }
    }
  },
  mounted() {
    window.addEventListener('afterprint', this.handleAfterPrint);
    // Cargar QZ Tray al montar el componente.
    // this.loadQZTray();
  },
  beforeUnmount() { // Cambiado de beforeDestroy a beforeUnmount para Vue 3
    window.removeEventListener('afterprint', this.handleAfterPrint);
    // Desconectar de QZ Tray al salir del componente.
    // qz.websocket.disconnect().catch(error => console.error('Error al desconectar de QZ Tray:', error));
  }
}
</script>