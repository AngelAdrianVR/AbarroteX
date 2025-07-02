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
      <section class="w-full md:w-[420px] mx-auto text-center mt-4 p-4 border rounded-md shadow-sm">
        <h3 class="font-bold text-base mb-2 text-gray-700">Impresión por USB (PC/Laptop)</h3>
        <p class="text-xs text-gray-500 mb-3">
          Requiere el
          <a href="https://parzibyte.me/http-esc-pos-desktop-docs/es/guia/introduccion.html" target="_blank"
            class="text-blue-600 underline hover:text-blue-800">
            plugin de impresión
          </a>
          instalado y en ejecución.
        </p>
        <div class="flex items-center justify-center space-x-2 mb-4">
          <label for="printer-name" class="text-sm font-medium text-gray-700">Nombre de la impresora:</label>
          <input type="text" id="printer-name" v-model="printerName"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm w-40">
        </div>
        <PrimaryButton @click="imprimirTicketUsb()" class="w-full justify-center">
          <i class="fa-solid fa-print mr-2"></i>
          Imprimir ticket (USB)
        </PrimaryButton>
      </section>

      <!-- <PrimaryButton v-if="!printTicket" @click="print" class="!bg-[#EDEDED] !text-[#373737]">
        <i class="fa-solid fa-display"></i>
        Guardar en PDF
      </PrimaryButton> -->
    </section>
    <p v-if="!UUIDService && !UUIDCharacteristic" class="text-sm text-center text-red-600 mt-4">
      No tienes ninguna impresora configurada para Bluetooth.
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
import axios from 'axios';

export default {
  data() {
    return {
      printTicket: false,
      device: null, // Dispositivo de impresora Bluetooth guardada al hacer vínculo
      UUIDService: this.$page.props.auth.user.printer_config?.UUIDService,
      UUIDCharacteristic: this.$page.props.auth.user.printer_config?.UUIDCharacteristic,
      printerName: this.$page.props.auth.user.printer_config?.name ?? null,
      serial: null,
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
    closeTab() {
      window.close();
    },
    conectarBluetooth() {
      navigator.bluetooth.requestDevice({
        acceptAllDevices: true,
        optionalServices: [this.UUIDService]
      })
        .then(device => {
          this.device = device;
          this.enviarDatosImpresion();
        })
        .catch(error => {
          console.error('Error al conectar con dispositivo Bluetooth:', error);
          alert('Error al conectar con la impresora Bluetooth. Asegúrate de que esté encendida y visible.');
        });
    },
    /**
    * --- AJUSTADO PARA 58mm: Genera el string del ticket con comandos ESC/POS. ---
    * @param {boolean} incluirCorte - Si es true, añade el comando para cortar el papel al final.
    * @returns {string} El texto formateado para la impresora.
    */
    generarTicketConComandos(incluirCorte = true) {
      const ESC = '\x1B';
      const GS = '\x1D';
      const INICIALIZAR_IMPRESORA = ESC + '@';
      const NEGRITA_ON = ESC + 'E' + '\x01';
      const NEGRITA_OFF = ESC + 'E' + '\x00';
      const ALINEAR_IZQUIERDA = ESC + 'a' + '\x00';
      const ALINEAR_CENTRO = ESC + 'a' + '\x01';
      const ALINEAR_DERECHA = ESC + 'a' + '\x02';
      const CORTAR_PAPEL = GS + 'V' + '\x00' + '\x00';

      let ticket = INICIALIZAR_IMPRESORA;

      // Encabezado
      ticket += ALINEAR_CENTRO;
      ticket += NEGRITA_ON + this.$page.props.auth.user.store.name + NEGRITA_OFF + '\n';
      if (this.$page.props.auth.user.store.address) {
        ticket += this.$page.props.auth.user.store.address + '\n';
      }
      ticket += ALINEAR_IZQUIERDA;
      ticket += 'Folio: ' + this.sales[0].folio + '\n';
      ticket += this.formatDate(this.sales[0]?.created_at) + '\n';
      // Ancho para impresora de 58mm (aprox. 32 caracteres)
      ticket += '--------------------------------\n';

      // Cuerpo (Productos) - Formato para 32 caracteres
      // Se ha rediseñado la tabla para que quepa
      let header = 'Cant Producto'.padEnd(24) + 'Total\n';
      ticket += NEGRITA_ON + header + NEGRITA_OFF;
      ticket += '--------------------------------\n';

      this.sales.forEach(sale => {
        const cantidad = sale.quantity.toString();
        // Truncamos el nombre del producto para que quepa
        const nombre = sale.product_name.substring(0, 15);
        const totalProducto = (sale.quantity * sale.current_price).toFixed(2);

        // Alineamos las columnas manualmente con padEnd y padStart
        let linea = '';
        linea += cantidad.padEnd(2, ' ');
        linea += ' ' + nombre.padEnd(18, ' '); // 2 + 1 + 18 = 21 caracteres
        linea += totalProducto.padStart(11, ' '); // Alineado a la derecha, 11 caracteres
        ticket += linea + '\n';
      });

      ticket += '--------------------------------\n';

      // Total
      const totalStr = 'Total: $' + this.totalSale().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      ticket += ALINEAR_DERECHA + NEGRITA_ON + totalStr + NEGRITA_OFF + '\n\n';

      // Pie de página
      ticket += ALINEAR_IZQUIERDA;
      ticket += 'Metodo de pago: ' + this.sales[0].payment_method + '\n\n';
      ticket += ALINEAR_CENTRO;
      ticket += 'GRACIAS POR SU COMPRA\n\n\n';

      if (incluirCorte) {
        ticket += CORTAR_PAPEL;
      }

      ticket += '\n\n';

      return ticket;
    }, 
    async imprimirTicketUsb() {
      // Generamos el ticket SIN el comando de corte, ya que el plugin lo hará por nosotros.
      const ticketTexto = this.generarTicketConComandos(false);

      // Lista de operaciones para el plugin
      const listaDeOperaciones = [
        {
          nombre: "EscribirTexto",
          argumentos: [ticketTexto],
        },
        // {
        //   nombre: "CorteCompleto", // El plugin se encarga del corte
        //   argumentos: [],
        // }
      ];

      // Payload para la petición HTTP
      const cargaUtil = {
        serial: this.serial, // Serial del plugin
        operaciones: listaDeOperaciones,
        nombreImpresora: this.printerName,
        // 'serial' no es necesario si usas 'nombreImpresora'
      };

      try {
        const respuestaHttp = await fetch("http://localhost:8000/imprimir", {
          method: "POST",
          body: JSON.stringify(cargaUtil),
          headers: {
            "Content-Type": "application/json",
          },
        });
        const respuesta = await respuestaHttp.json();
        if (respuesta.ok) {
          console.log("Impreso correctamente vía USB");
        } else {
          console.error("Error del plugin:", respuesta.message);
          alert(`Error al imprimir por USB: ${respuesta.message}\nAsegúrate que el nombre de la impresora "${this.printerName}" es correcto.`);
        }
      } catch (error) {
        console.error("Error de conexión con el plugin:", error);
        alert("No se pudo conectar con el plugin de impresión. Verifica que el programa esté ejecutándose en tu computadora y no esté bloqueado por un firewall.");
      }
    },
    async enviarDatosImpresion() { // Para Bluetooth
      try {
        const service = await this.device.gatt.connect().then(server => server.getPrimaryService(this.UUIDService));
        const characteristic = await service.getCharacteristic(this.UUIDCharacteristic);

        // --- MODIFICADO: Se llama a la función con `incluirCorte = true` ---
        const datosParaImprimir = this.generarTicketConComandos(true);
        const encodedData = new TextEncoder('utf-8').encode(datosParaImprimir);

        const fragmentSize = 50;
        const fragments = this.chunkData(encodedData, fragmentSize);

        for (const fragment of fragments) {
          await characteristic.writeValue(fragment);
        }
      } catch (error) {
        alert('Error al imprimir por Bluetooth. Asegúrate de que la impresora esté conectada.');
      }
    },
    async getParzibyteSerial() {
      try {
        const response = await axios.get(route('users.get-parzibyte-serial'));
        if (response.status === 200) {
          this.serial = response.data.serial;
        } else {
          console.error('Error al obtener serial: ', response.status);
        }
      } catch (error) {
        console.error('Error al obtener serial: ', error);
      }
    },
  },
  async mounted() {
    window.addEventListener('afterprint', this.handleAfterPrint);
    await this.getParzibyteSerial();
  },
  beforeUnmount() {
    window.removeEventListener('afterprint', this.handleAfterPrint);
  }
}
</script>