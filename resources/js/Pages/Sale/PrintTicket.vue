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

      <!-- <ThirthButton v-if="!printTicket" @click="conectarUSB" class="!py-1 !bg-green-100 !text-green-600 mr-2">
        <i class="fa-solid fa-usb text-lg mr-2"></i>
        Conectar impresora (USB)
      </ThirthButton>

      <ThirthButton @click="imprimir" class="!py-1 !bg-green-100 !text-green-600 mr-2">
        <i class="fa-solid fa-usb text-lg mr-2"></i>
        imprimir (USB)
      </ThirthButton> -->

      <PrimaryButton v-if="usbDevice && !printTicket" class="mr-2" @click="enviarDatosImpresionUSB()">
        <i class="fa-solid fa-print"></i>
        Imprimir ticket de nuevo (USB)
      </PrimaryButton>

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
      usbDevice: null, // Dispositivo de impresora USB
      UUIDService: this.$page.props.auth.user.printer_config?.UUIDService,
      UUIDCharacteristic: this.$page.props.auth.user.printer_config?.UUIDCharacteristic,

      usbDeviceSerial: null,
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
      const CORTAR_PAPEL = GS + 'V' + '\x00' + '\x00'; // Corta el papel completo

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
      ticket += 'Producto          Cant. Total   Precio\n'; // Encabezado de la tabla manual
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
      ticket += 'Metodo de pago: ' + this.sales[0].payment_method + '\n\n'; // Asegúrate de que esto siempre tenga un valor
      ticket += ALINEAR_CENTRO;
      ticket += 'GRACIAS POR SU COMPRA\n\n\n\n'; // Agregamos saltos de línea para que el papel avance

      ticket += CORTAR_PAPEL; // Comando para cortar el papel

      return ticket;
    },
    generarComandosEscPos() {
      // Comandos básicos ESC/POS (hexadecimal)
      const commands = [
        '\x1B\x40',         // Inicializar
        '\x1B\x61\x01',     // Centrar texto
        'TICKET DE VENTA\n',
        '\x1B\x45\x01',     // Negrita
        'Producto       Total\n',
        '\x1B\x45\x00',     // Fin negrita
        'Coca Cola     $10.00\n',
        '\x1D\x56\x41\x10'  // Cortar papel
      ];

      return commands.join('');
    },
    async enviarDatosImpresion() { // Para Bluetooth
      try {
        const service = await this.device.gatt.connect().then(server => server.getPrimaryService(this.UUIDService));
        const characteristic = await service.getCharacteristic(this.UUIDCharacteristic);

        const datosParaImprimir = this.generarTicketConComandos();
        const encodedData = new TextEncoder('utf-8').encode(datosParaImprimir);

        const fragmentSize = 50; // Ajusta este valor si experimentas problemas
        const fragments = this.chunkData(encodedData, fragmentSize);

        for (const fragment of fragments) {
          await characteristic.writeValue(fragment);
        }

        console.log('Datos de impresión enviados correctamente (Bluetooth)');
        alert('Ticket impreso exitosamente por Bluetooth.');
      } catch (error) {
        console.error('Error al enviar datos de impresión (Bluetooth):', error);
        alert('Error al imprimir por Bluetooth. Asegúrate de que la impresora esté conectada y que el servicio y característica UUID sean correctos.');
      }
    },
    async imprimir() {
      const datos = this.generarComandosEscPos(); // Tus comandos ESC/POS en formato hexadecimal

      try {
        // Opción 1: Envío directo al backend
        await axios.post('/api/print', {
          data: datos,
          printer: 'POS-58'
        }, {
          headers: {
            'Content-Type': 'application/octet-stream' // Para datos binarios
          }
        });

        alert('Ticket enviado a la impresora');

      } catch (error) {
        console.error('Error:', error);
      }
    },
    // Modifica tu función con esta versión de emergencia:
    async conectarUSB() {
      try {
        // Filtros específicos para tu impresora (POS-58)
        const filters = [
          {
            vendorId: 0x0416,  // 1046 en hexadecimal
            productId: 0x5011, // 20497 en hexadecimal
            classCode: 0x07    // Clase de impresora
          }
        ];

        // Paso 1: Solicitar dispositivo (evita caché problemático)
        const device = await navigator.usb.requestDevice({ filters });

        // Paso 2: Almacenar serial para persistencia
        this.usbDeviceSerial = device.serialNumber;
        console.log("Dispositivo seleccionado:", device);

        // Paso 3: Abrir y configurar inmediatamente
        await device.open();

        if (device.configuration === null) {
          await device.selectConfiguration(1); // Configuración por defecto
        }

        // Paso 4: Buscar interfaz de impresora (clase 7)
        const interfaceNumber = device.configuration.interfaces.find(
          iface => iface.alternates.some(alt => alt.interfaceClass === 7)
        )?.interfaceNumber;

        if (interfaceNumber === undefined) {
          throw new Error("Interfaz de impresora no encontrada");
        }

        // Paso 5: Reclamar interfaz
        await device.claimInterface(interfaceNumber);
        this.usbDevice = device; // Almacenar dispositivo configurado

        console.log("Impresora lista:", device);
        alert("✅ Impresora conectada. Ahora puedes imprimir.");

      } catch (error) {
        console.error("Error en conectarUSB:", error);

        // Manejo específico de errores
        if (error.name === "NotFoundError") {
          alert("🔌 No se encontró la impresora. Conéctala y vuelve a intentar.");
        } else if (error.message.includes("Access denied")) {
          alert("🔒 Permiso denegado. Reinicia la impresora y el navegador.");
        } else {
          alert(`❌ Error: ${error.message}`);
        }
      }
    },
    async enviarDatosImpresionUSB() {
      // Validación inicial
      if (!this.usbDevice && !this.usbDeviceSerial) {
        alert("⚠️ Primero conecta la impresora USB");
        return;
      }

      try {
        // Reconexión segura si es necesario
        if (!this.usbDevice?.opened) {
          const devices = await navigator.usb.getDevices();
          this.usbDevice = devices.find(d => d.serialNumber === this.usbDeviceSerial);

          if (!this.usbDevice) throw new Error("Impresora desconectada");

          await this.usbDevice.open();
          await this.usbDevice.selectConfiguration(1);

          const interfaceNumber = this.usbDevice.configuration.interfaces.find(
            iface => iface.alternates.some(alt => alt.interfaceClass === 7)
          )?.interfaceNumber;

          if (interfaceNumber !== undefined) {
            await this.usbDevice.claimInterface(interfaceNumber);
          }
        }

        // Buscar endpoint de salida
        const endpoint = this.usbDevice.configuration.interfaces
          .flatMap(iface => iface.alternates)
          .flatMap(alt => alt.endpoints)
          .find(ep => ep.direction === "out" && ["bulk", "interrupt"].includes(ep.type));

        if (!endpoint) {
          throw new Error("Endpoint de impresión no encontrado");
        }

        // Generar y enviar datos (con codificación Windows-1252)
        const datosParaImprimir = this.generarTicketConComandos(); // Reemplaza con tu función
        const encodedData = new TextEncoder("windows-1252").encode(datosParaImprimir);
        const chunkSize = endpoint.packetSize || 64; // Tamaño de paquete óptimo

        // Envío por fragmentos
        for (let i = 0; i < encodedData.length; i += chunkSize) {
          const chunk = encodedData.slice(i, i + chunkSize);
          await this.usbDevice.transferOut(endpoint.endpointNumber, chunk);
          await new Promise(resolve => setTimeout(resolve, 10)); // Pequeña pausa
        }

        alert("🖨️ Ticket impreso correctamente");

      } catch (error) {
        console.error("Error en enviarDatosImpresionUSB:", error);

        if (error.message.includes("device disconnected")) {
          alert("🚫 Impresora desconectada. Reconéctala y vuelve a intentar.");
        } else {
          alert(`❌ Error al imprimir: ${error.message}`);
        }

        // Limpieza en caso de error
        if (this.usbDevice?.opened) {
          try {
            await this.usbDevice.close();
          } catch (e) {
            console.warn("Error al cerrar dispositivo:", e);
          }
          this.usbDevice = null;
        }
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
  },
  mounted() {
    window.addEventListener('afterprint', this.handleAfterPrint);
  },
  beforeUnmount() {
    window.removeEventListener('afterprint', this.handleAfterPrint);
  }
}
</script>