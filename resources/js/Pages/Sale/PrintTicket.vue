<template>

  <Head :title="'imprimir ticket folio ' + sales[0].folio" />
  <div id="text-to-print" class="w-full md:w-[420px] mx-auto text-sm border-2 border-black p-5 my-16 !text-[11px]">
    <div class="flex items-center space-x-3">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        class="size-4 mr-1">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
      </svg>
      <p>{{ $page.props.auth.user.store.name }}</p>
    </div>
    <p class="text-right">{{ formatDate(sales[0]?.created_at) }}</p>
    <!-- tabla de productos -->
    <table class="w-full table-">
      <tr class="text-left *:font-bold">
        <th class="w-[40%]">Producto</th>
        <th class="w-[12%]">Cantidad</th>
        <th class="w-[12%]">Total</th>
      </tr>
      <template v-for="sale in sales" :key="sale">
        <tr class="*:pb-1">
          <td>
            <!-- <span class="uppercase">{{ truncatedProductName(sale.product_name) }}</span> -->
            <p class="uppercase leading-snug">{{ sale.product_name }}</p>
          </td>
          <td>
            <p>{{ sale.quantity + ' ' }}</p>
          </td>
          <td>
            <p class="ml-1">
              ${{ (sale.quantity * sale.current_price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
            </p>
          </td>
        </tr>
        <tr v-for="promo in sale.promotions_applied" class="italic *:align-top">
          <td>
            <!-- <span class="uppercase">{{ truncatedProductName(sale.product_name) }}</span> -->
            <p class="uppercase leading-snug flex space-x-1">
              <svg width="7" height="13" viewBox="0 0 10 16" fill="none" class="shrink-0"
                xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M4.28948 0C6.61872 2.72141 7.63037 4.40864 8.101 8.09863C8.68078 7.68803 8.88761 7.19526 8.93401 5.7168C11.7333 11.4332 8.45922 15.0058 5.54143 15.1846C5.18415 15.2064 4.52916 15.2602 4.11175 15.125C-0.11532 13.7553 -1.60454 10.0634 2.14593 5.24023C4.57877 2.25049 4.74345 1.28434 4.28948 0ZM4.82464 7.44531C2.74274 10.1809 2.08633 12.5065 5.00335 14.0547C6.92271 13.584 7.62449 12.4474 7.80315 10.4824C7.42276 11.0129 7.17113 11.2542 6.49261 11.375C6.6711 9.76745 6.13689 8.64469 4.82464 7.44531Z"
                  fill="currentColor" />
              </svg>
              <span>{{ promo.description }}</span>
            </p>
          </td>
          <td></td>
          <td>
            <p>
              -${{ promo.discount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
            </p>
          </td>
        </tr>
        <tr v-if="sale.discounted_price != null && !sale.promotions_applied" class="italic *:align-top">
          <td>
            <!-- <span class="uppercase">{{ truncatedProductName(sale.product_name) }}</span> -->
            <p class="uppercase leading-snug flex space-x-1">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-3">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
              </svg>
              <span>Regalo por promoción</span>
            </p>
          </td>
          <td></td>
          <td>
            <p>
              -${{ calculateTotalDiscount(sale).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
            </p>
          </td>
        </tr>
      </template>
    </table>
    <div class="flex justify-end mt-1 mr-8">
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
    <!-- <span v-if="!printTicket" class="mx-auto">--------------------------------</span>
    <span v-if="!printTicket" class="mx-auto">--------------------------------</span>
    <p v-if="!printTicket" class="mx-auto">--------------------------------</p> -->
  </div>

  <section class="text-center mt-7 mx-4 flex flex-wrap justify-center gap-3">
    <!-- Conectar impresora -->
    <button :disabled="!UUIDService && !UUIDCharacteristic" v-if="!printTicket" @click="conectarBluetooth"
      class="flex items-center gap-2 px-4 py-2 border-2 border-blue-600 text-blue-600 font-semibold rounded-xl transition duration-200 hover:bg-blue-600 hover:text-white disabled:opacity-50 disabled:cursor-not-allowed">
      <i class="fa-brands fa-bluetooth text-lg"></i>
      Conectar impresora
    </button>

    <!-- Imprimir desde dispositivo móvil -->
    <button :disabled="!UUIDService && !UUIDCharacteristic" v-if="device && !printTicket"
      @click="enviarDatosImpresion(device)"
      class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white font-semibold rounded-xl transition duration-200 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
      <i class="fa-solid fa-print"></i>
      Imprimir ticket
    </button>

    <!-- Imprimir pantalla desde PC -->
    <button v-if="!printTicket" @click="print"
      class="flex items-center gap-2 px-4 py-2 bg-gray-800 text-white font-semibold rounded-xl transition duration-200 hover:bg-gray-900">
      <i class="fa-solid fa-display"></i>
      Imprimir Pantalla
    </button>
  </section>

  <p v-if="!UUIDService && !UUIDCharacteristic" class="text-sm text-center text-red-600 mt-4">
    No tienes ninguna impresora configurada.
    Para conectar con una impresora térmica vía bluetooth
    <strong @click="$inertia.get(route('settings.index', { tab: 3 }))"
      class="text-primary underline cursor-pointer">configurala aquí</strong>
  </p>

</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import { Head } from '@inertiajs/vue3';
import { format, parseISO } from 'date-fns';
import html2canvas from 'html2canvas'; // para convertir la url en imagen
import es from 'date-fns/locale/es';

export default {
  data() {
    return {
      printTicket: false,
      device: null, //Dispositivo de impresora guardada al hacer vínculo
      text: null, //guarda el texto a pimprimir. (ticket)
      UUIDService: this.$page.props.auth.user.printer_config?.UUIDService,
      UUIDCharacteristic: this.$page.props.auth.user.printer_config?.UUIDCharacteristic,
    }
  },
  components: {
    PrimaryButton,
    ThirthButton,
    Head,
  },
  props: {
    sales: Object,
  },
  methods: {
    // conectarBluetooth() {
    //   // Solicitar al usuario que seleccione la impresora vía Bluetooth
    //   navigator.bluetooth.requestDevice({
    //     acceptAllDevices: true, // Aceptar cualquier dispositivo Bluetooth
    //     optionalServices: [this.UUIDService] // UUID del servicio de la impresora
    //   })
    //   .then(device => {
    //     console.log('Dispositivo Bluetooth conectado:', device);
    //     this.device = device;

    //   })
    //   .catch(error => {
    //     console.error('Error al conectar con dispositivo Bluetooth:', error);
    //   });
    // },
    calculateTotalDiscount(sale) {
      const originalTotal = sale.current_price * sale.quantity;
      const discountTotal = sale.discounted_price * sale.quantity;

      return originalTotal - discountTotal;
    },
    calculateSubTotal(sale) {
      const priceToUse = sale.discounted_price >= 0
        ? sale.discounted_price
        : sale.current_price;

      return sale.quantity * priceToUse;
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
    truncatedProductName(name) {
      const maxLength = 17;
      if (name.length > maxLength) {
        return name.slice(0, maxLength) + '..';
      } else {
        return name;
      }
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

    // // ----------------- Convertir a imagen ----------------------------------------------------
    //  async convertToImage() {

    //      // Obtener el contenedor que deseas imprimir
    //     const element = document.getElementById('text-to-print');

    //     // Convertir el contenedor a un canvas
    //     const canvas = await html2canvas(element);

    //     // Convertir el canvas a un data URL
    //     const dataUrl = canvas.toDataURL('image/png');

    //     // Convertir el data URL a un array buffer
    //     const response = await fetch(dataUrl);
    //     const blob = await response.blob();
    //     const arrayBuffer = await blob.arrayBuffer();

    //     // Llamar al método para enviar los datos de impresión
    //     this.enviarDatosImpresion(this.device, arrayBuffer);
    // },
    // async enviarDatosImpresion(device, arrayBuffer) {
    //     try {
    //     // Obtener el servicio de impresión
    //     const service = await device.gatt.connect().then(server => server.getPrimaryService(this.UUIDService));

    //     // Obtener la característica de escritura
    //     const characteristic = await service.getCharacteristic(this.UUIDCharacteristic);

    //     // Dividir el array buffer en fragmentos
    //     const fragmentSize = 20; // Ajusta este tamaño según sea necesario
    //     for (let i = 0; i < arrayBuffer.byteLength; i += fragmentSize) {
    //         const fragment = arrayBuffer.slice(i, i + fragmentSize);
    //         await characteristic.writeValue(fragment);
    //     }

    //     console.log('Datos de impresión enviados correctamente');
    //     } catch (error) {
    //     console.error('Error al enviar datos de impresión:', error);
    //     }
    // },
    // // ----------------------------------------------------------------------------

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
        const priceToUse = item.discounted_price != null && item.discounted_price >= 0
          ? item.discounted_price
          : item.current_price;
        return total + priceToUse * item.quantity;
      }, 0);
    }
  },
  mounted() {
    window.addEventListener('afterprint', this.handleAfterPrint);
    this.text = document.getElementById('text-to-print').innerText;
  },
  beforeDestroy() {
    window.removeEventListener('afterprint', this.handleAfterPrint);
  }
}
</script>