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
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="size-4 mr-1">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
        </svg>
        <p>{{ $page.props.auth.user.store.name }}</p>
      </div>
      <p class="flex items-center justify-between">
        <span>Folio: {{ sales[0]?.folio }}</span>
        <span>{{ formatDate(sales[0]?.created_at) }}</span>
      </p>
      <!-- tabla de productos -->
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
    <!-- Botones de conexión e impresión -->
    <section class="text-center lg:space-x-2 mt-7 mx-10 space-y-4">
      <ThirthButton :disabled="!UUIDService && !UUIDCharacteristic" v-if="!printTicket" @click="conectarBluetooth"
        class="!py-1 !bg-blue-100 !text-blue-600 mr-2">
        <i class="fa-brands fa-bluetooth text-lg mr-2"></i>
        Conectar impresora
      </ThirthButton>
  
      <!-- imprimir desde dispositivo movil -->
      <PrimaryButton :disabled="!UUIDService && !UUIDCharacteristic" v-if="(device && !printTicket)" class="mr-2"
        @click="enviarDatosImpresion()">
        <i class="fa-solid fa-print"></i>
        Imprimir ticket de nuevo
      </PrimaryButton>
  
      <!-- imprimir desde pc de escritorio o dispositivo con windows -->
      <PrimaryButton v-if="!printTicket" @click="print" class="!bg-[#EDEDED] !text-[#373737]">
        <i class="fa-solid fa-display"></i>
        Guardar en PDF
      </PrimaryButton>
        </section>
    <p v-if="!UUIDService && !UUIDCharacteristic" class="text-sm text-center text-red-600 mt-4">
      No tienes ninguna impresora configurada.
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

export default {
  data() {
    return {
      printTicket: false,
      device: null, //Dispositivo de impresora guardada al hacer vínculo
      // text: null, //guarda el texto a pimprimir. (ticket)
      UUIDService: this.$page.props.auth.user.printer_config?.UUIDService,
      UUIDCharacteristic: this.$page.props.auth.user.printer_config?.UUIDCharacteristic,
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
      // ticket += TEXTO_PEQUENO + MODO_CONDENSADO_ON; // Activar modo condensado para ahorrar espacio
      ticket += TEXTO_PEQUENO; // Activar modo condensado para ahorrar espacio
      ticket += 'Producto        Cant.   Total   Precio\n'; // Encabezado de la tabla manual
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
      // ticket += MODO_CONDENSADO_OFF + TEXTO_NORMAL;
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

        console.log('Datos de impresión enviados correctamente');
      } catch (error) {
        console.error('Error al enviar datos de impresión:', error);
      }
    },

    // Renombré tu función 'chunkText' a 'chunkData' para reflejar que ahora trabaja con un ArrayBuffer
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
    }
  },
  mounted() {
    window.addEventListener('afterprint', this.handleAfterPrint);
    // this.text = document.getElementById('text-to-print').innerText;
  },
  beforeDestroy() {
    window.removeEventListener('afterprint', this.handleAfterPrint);
  }
}
</script>