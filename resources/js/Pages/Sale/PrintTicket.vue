<template>
    <Head :title="'imprimir ticket folio ' + sales[0].folio" />
    <div id="text-to-print" class="w-full md:w-[420px] mx-auto text-sm border-2 border-black p-5 my-16">
        <div class="flex items-center space-x-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="size-4 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
            </svg>
            <p>{{ $page.props.auth.user.store.name }}</p>
        </div>
        <p class="text-right mt-2">{{ formatDate(sales[0]?.created_at) }}</p>


        <!-- tabla de productos -->
        <table class="mt-2 w-full text-xs">
            <tr class="text-left *:font-bold *:pb-2">
                <th>Producto</th>
                <th class="px-[2px]"></th>
                <th class="px-[2px]">Total</th>
            </tr>
            <tr v-for="sale in sales" :key="sale">
                <td class=" px-[2px]">
                  <span class="uppercase">{{ truncatedProductName(sale.product_name) }}</span>
                </td>
                <td class="w-7"></td>
                <td class=""><span>{{ sale.quantity + '  ' }}</span><span class="pl-2">$</span>{{ (sale.quantity * sale.current_price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
            </tr>
        </table>

        <div class="flex justify-end mt-5 mr-11">
            <p class="font-bold text-xs">Total <span class="px-2">$</span> <span>{{ totalSale().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
        </div>

        <p class="h-2 border-b-2 border-[#D9D9D9] mt-5"></p>
        <p v-if="!printTicket" class="mx-auto">--------------------------------</p>
        
        <div class="flex flex-col text-[#373737] mt-3">
            <p>Metodo de pago: {{ 'Efectivo' }}</p>
            <span>GRACIAS POR SU COMPRA</span>
        </div>

        <p v-if="$page.props.auth.user.store.address" class="text-center mt-2">{{ $page.props.auth.user.store.address }}</p>
        <span v-if="!printTicket" class="mx-auto">--------------------------------</span>
        <span v-if="!printTicket" class="mx-auto">--------------------------------</span>
        <p v-if="!printTicket" class="mx-auto">--------------------------------</p>
    </div>

    <!-- Botones de conexión e impresión -->
    <section class="text-center lg:space-x-2 mt-7 mx-10">
    <ThirthButton :disabled="!UUIDService && !UUIDCharacteristic" v-if="!printTicket" @click="conectarBluetooth" class="!py-1 !border-blue-600 !text-blue-600 mr-2 mb-2">
        <i class="fa-brands fa-bluetooth text-lg mr-2"></i>
        Conectar impresora
    </ThirthButton>

    <!-- imprimir desde dispositivo movil -->
    <PrimaryButton :disabled="!UUIDService && !UUIDCharacteristic" v-if="(device && !printTicket)" class="mb-2 mr-2" @click="enviarDatosImpresion(device)">
        <i class="fa-solid fa-print"></i>
        Imprimir ticket
    </PrimaryButton>

    <!-- imprimir desde pc de escritorio o dispositivo con windows -->
    <PrimaryButton v-if="!printTicket" @click="print">
        <i class="fa-solid fa-display"></i>
        Imprimir Pantalla 
    </PrimaryButton>
    </section>
    <p v-if="!UUIDService && !UUIDCharacteristic" class="text-sm text-center text-red-600 mt-4">
        No tienes ninguna impresora configurada.
        Para conectar con una impresora térmica vía bluetooth
        <strong @click="$inertia.get(route('settings.index', { tab: 3 }))" class="text-primary underline cursor-pointer">configurala aquí</strong>
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
components:{
PrimaryButton,
ThirthButton,
Head
},
props:{
sales: Object,
},
methods:{
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
        return total + item.current_price * item.quantity;
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