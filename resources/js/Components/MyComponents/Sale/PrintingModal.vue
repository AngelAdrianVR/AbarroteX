<template>
  <DialogModal :show="show" @close="$emit('close')" max-width="md">
    <template #title>
      {{ printType === "Ticket" ? "Impresión de ticket" : "Impresión de etiqueta" }}
    </template>
    <template #content>
      <div v-if="printType === 'Ticket'" class="flex items-start space-x-4">
        <figure class="h-24">
          <img src="@/../../public/images/ticket.png" :draggable="false" class="select-none object-contain h-full"
            alt="Imagen de ticket de venta" />
        </figure>
        <p class="w-2/3 text-base text-gray37">¿Desea imprimir el ticket de la venta?</p>
      </div>
      <div v-else class="flex items-start space-x-4">
        <figure class="h-24">
          <img src="@/../../public/images/label.png" :draggable="false" class="select-none object-contain h-full"
            alt="Imagen de ticket de venta" />
        </figure>
        <p class="w-2/3 text-base text-gray37">
          ¿Desea imprimir la etiqueta del servicio?
        </p>
      </div>
      <div class="mt-3">
        <div>
          <p>Impresora conectada por USB:</p>
          <div class="flex items-center space-x-2">
            <el-select v-model="selectedPrinter" placeholder="Selecciona la impresora"
              no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
              <el-option v-for="printer in availablePrinters" :key="printer" :value="printer" :label="printer" />
            </el-select>
            <button type="button" @click="getAvailablePrinters" title="Actualizar impresoras disponibles"
              class="size-6 bg-[#EDEDED] text-gray37 flex items-center justify-center rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
              </svg>
            </button>
          </div>
          <el-divider> Ó </el-divider>
          <ThirthButton @click="connectBluetooth" class="!py-px !bg-blue-100 !text-blue-600 mr-2">
            <i class="fa-brands fa-bluetooth text-lg mr-2"></i>
            Conectar impresora Bluetooth
          </ThirthButton>
        </div>
      </div>
    </template>
    <template #footer>
      <div class="flex items-center space-x-2">
        <CancelButton @click="$emit('close')" :disabled="printing">No</CancelButton>
        <PrimaryButton @click="printByUSB" :disabled="!selectedPrinter || printing">
          <i v-if="printing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
          Si, imprimir
        </PrimaryButton>
      </div>
    </template>
  </DialogModal>
</template>

<script>
import DialogModal from "@/Components/DialogModal.vue";
import CancelButton from "../CancelButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { format, parseISO } from "date-fns";
import es from "date-fns/locale/es";
import axios from "axios";
import ThirthButton from "../ThirthButton.vue";

export default {
  name: "PrintingModal",
  data() {
    return {
      ticketPrinterName:
        this.$page.props.auth.user.printer_config?.ticketPrinterName ?? null,
      labelPrinterName:
        this.$page.props.auth.user.printer_config?.labelPrinterName ?? null,
      selectedPrinter: null, // Impresora seleccionada por el usuario
      printType: null, // Tipo de impresión: 'Ticket' o 'Etiqueta'
      availablePrinters: [],
      saleFolio: null,
      serial: null,
      customData: null,
      printing: false,
      sales: [], // ventas relacionadas al folio
      device: null, // Dispositivo de impresora Bluetooth guardada al hacer vínculo
      UUIDService: this.$page.props.auth.user.printer_config?.UUIDService,
      UUIDCharacteristic: this.$page.props.auth.user.printer_config?.UUIDCharacteristic,
    };
  },
  emits: ["close"],
  components: {
    DialogModal,
    CancelButton,
    PrimaryButton,
    ThirthButton,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
  },
  computed: {},
  methods: {
    generateTicketCommands(hasCut = true) {
      const ESC = "\x1B";
      const GS = "\x1D";
      const INICIALIZAR_IMPRESORA = ESC + "@";
      const NEGRITA_ON = ESC + "E" + "\x01";
      const NEGRITA_OFF = ESC + "E" + "\x00";
      const ALINEAR_IZQUIERDA = ESC + "a" + "\x00";
      const ALINEAR_CENTRO = ESC + "a" + "\x01";
      const CORTAR_PAPEL = GS + "V" + "\x00" + "\x00";

      // --- 1. Determinar el ancho del ticket y separador dinámicamente ---
      const ticketWidthSetting = this.$page.props.auth.user.printer_config?.ticketWidth;
      // Ancho en caracteres: 48 para 80mm, 32 para 58mm (o por defecto)
      const anchoTicket = ticketWidthSetting === "80mm" ? 48 : 32;
      const separador = "-".repeat(anchoTicket) + "\n";

      let ticket = INICIALIZAR_IMPRESORA;

      // Encabezado
      ticket += ALINEAR_CENTRO;
      if (this.$page.props.auth.user.store) {
        ticket += NEGRITA_ON + this.$page.props.auth.user.store.name + NEGRITA_OFF + '\n';
        if (this.$page.props.auth.user.store.address) {
          ticket += this.$page.props.auth.user.store.address + '\n';
        }

        if (this.$page.props.auth.user.printer_config?.ticketContactInfo) {
          ticket += this.$page.props.auth.user.printer_config?.ticketContactInfo + '\n';
        }
      }
      
      ticket += ALINEAR_IZQUIERDA;
      ticket += "Folio: " + this.sales[0].folio + "\n";
      ticket += this.formatDate(this.sales[0]?.created_at) + "\n";
      ticket += separador;

      // --- 2. Cuerpo (Productos) con columnas dinámicas ---
      // Definir anchos de columna
      const colAnchoTotal = 12; // Espacio para "$1,234.56"
      const colAnchoCant = 4; // Espacio para "Cant"
      const colAnchoNombre = anchoTicket - colAnchoTotal - colAnchoCant;

      // Crear encabezado dinámico
      let header = "Pzs".padEnd(colAnchoCant);
      header += "Producto".padEnd(colAnchoNombre);
      header += "Total".padStart(colAnchoTotal);

      ticket += NEGRITA_ON + header + "\n" + NEGRITA_OFF;
      ticket += separador;

      this.sales.forEach((sale) => {
        const cantidad = sale.quantity.toString();
        // Truncar el nombre del producto dinámicamente
        const nombre = sale.product_name.substring(0, colAnchoNombre - 1); // -1 para un espacio de margen
        const totalProducto = (sale.quantity * sale.current_price).toFixed(2);

        // Alineamos las columnas manualmente con los anchos dinámicos
        let linea = "";
        linea += cantidad.padEnd(colAnchoCant);
        linea += this.removeAccents(nombre).padEnd(colAnchoNombre);
        linea += ("$" + totalProducto).padStart(colAnchoTotal); // Añadimos '$' y alineamos
        ticket += linea + "\n";

        if (sale.promotions_applied && sale.promotions_applied.length > 0) {
          sale.promotions_applied.forEach((promo) => {
            // 1. Reinicia la variable 'linea' en CADA iteración para no acumular texto.
            let lineaPromo = "";

            // 2. Indenta la descripción para mayor claridad y ajústala al ancho de las primeras dos columnas.
            const textoPromo = "Descuento por promo";
            // lineaPromo += textoPromo.padEnd(colAnchoCant + colAnchoNombre);
            lineaPromo += textoPromo.padEnd(colAnchoCant + colAnchoNombre - 1);

            // 3. Ajusta el descuento al ancho EXACTO de la columna de total.
            const discounted = promo.discount.toFixed(2);
            const textoDescuento = "-$" + discounted;
            lineaPromo += textoDescuento.padStart(colAnchoTotal);

            ticket += lineaPromo + "\n";
          });
        }
      });

      ticket += separador;

      // --- 3. Total alineado a la derecha con padStart ---
      const totalStr =
        "Total: $" +
        this.totalSale()
          .toFixed(2)
          .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      // Usamos padStart en lugar de ALINEAR_DERECHA para un control preciso
      ticket += NEGRITA_ON + totalStr.padStart(anchoTicket) + NEGRITA_OFF + "\n";

      // Pie de página
      ticket += ALINEAR_IZQUIERDA;
      ticket += "Metodo de pago: " + this.sales[0].payment_method + "\n";
      ticket += ALINEAR_CENTRO;
      ticket += "GRACIAS POR SU COMPRA";

      return ticket;
    },
    formatDate(dateString) {
      return format(parseISO(dateString), "dd MMMM yyyy, h:mm a", { locale: es });
    },
    totalSale() {
      return this.sales.reduce((total, item) => {
        const priceToUse =
          item.discounted_price !== null ? item.discounted_price : item.current_price;
        return total + priceToUse * item.quantity;
      }, 0);
    },
    connectBluetooth() {
      navigator.bluetooth
        .requestDevice({
          acceptAllDevices: true,
          optionalServices: [this.UUIDService],
        })
        .then((device) => {
          this.device = device;
          this.printByBluetooth();
        });
      // .catch(error => {
      //     console.error('Error al conectar con dispositivo Bluetooth:', error);
      //     alert('Error al conectar con la impresora Bluetooth. Asegúrate de que esté encendida y visible.');
      // });
    },
    chunkData(data, size) {
      const chunks = [];
      for (let i = 0; i < data.length; i += size) {
        chunks.push(data.slice(i, i + size));
      }
      return chunks;
    },
    removeAccents(text = "") {
      if (!text) return "";
      return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    },
    setTicketMode() {
      this.printType = "Ticket";
      this.selectedPrinter = this.ticketPrinterName;
    },
    setLabelMode() {
      this.printType = "Etiqueta";
      this.selectedPrinter = this.labelPrinterName;
    },
    async printByUSB() {
      this.printing = true;

      let ticketText = "";
      if (this.customData) {
        // Si se proporciona customData, lo usamos directamente
        ticketText = this.customData;
      } else {
        // obtenemos las ventas si no se han cargado
        if (!this.sales.length && this.saleFolio) {
          await this.getSalesByFolio();
        }
        // Generamos el ticket para venta de productos
        ticketText = this.generateTicketCommands(false);
      }

      let listaDeOperaciones = [];
      // Lista de operaciones para el plugin
      if (this.printType == "Ticket") {
        const logo = this.$page.props.auth.user.store.media?.find(
          (m) => m.collection_name == "ticketLogo"
        )?.original_url;

        if (logo) {
          listaDeOperaciones.push({
            nombre: "DescargarImagenDeInternetEImprimir",
            argumentos: [logo, 380, 0, false],
          });
        }
        listaDeOperaciones.push({
          nombre: "TextoSegunPaginaDeCodigos",
          argumentos: [0, "cp850", ticketText],
        });
        listaDeOperaciones.push({
          nombre: "Feed",
          argumentos: [this.$page.props.auth.user.printer_config?.ticketFinalWhiteLines],
        });
        /* {
              nombre: "EscribirTexto",
              argumentos: [ticketText],
            }, */
      } else {
        listaDeOperaciones = [
          {
            nombre: "EscribirTexto",
            argumentos: [ticketText],
          },
        ];
      }

      // Payload para la petición HTTP
      const cargaUtil = {
        serial: this.serial, // Serial del plugin
        operaciones: listaDeOperaciones,
        nombreImpresora: this.selectedPrinter,
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
        if (!respuesta.ok) {
          alert(
            `Error al imprimir por USB: ${respuesta.message}\nAsegúrate que el nombre de la impresora "${this.selectedPrinter}" es correcto.`
          );
        }
      } catch (error) {
        console.error("Error de conexión con el plugin:", error);
        alert(
          "No se pudo conectar con el plugin de impresión. Verifica que el programa esté ejecutándose en tu computadora y no esté bloqueado por un firewall."
        );
      } finally {
        this.printing = false;
        this.$emit("close"); // Cerrar el modal después de imprimir
      }
    },
    async printByBluetooth() {
      this.printing = true;
      try {
        const service = await this.device.gatt
          .connect()
          .then((server) => server.getPrimaryService(this.UUIDService));
        const characteristic = await service.getCharacteristic(this.UUIDCharacteristic);

        let ticketText = "";
        if (this.customData) {
          // Si se proporciona customData, lo usamos directamente
          ticketText = this.customData;
        } else {
          // obtenemos las ventas si no se han cargado
          if (!this.sales.length && this.saleFolio) {
            await this.getSalesByFolio();
          }
          // Generamos el ticket para venta de productos
          ticketText = this.generateTicketCommands(false);
        }
        const encodedData = new TextEncoder("utf-8").encode(ticketText);

        const fragmentSize = 50;
        const fragments = this.chunkData(encodedData, fragmentSize);

        for (const fragment of fragments) {
          await characteristic.writeValue(fragment);
        }
      } catch (error) {
        alert(
          "Error al imprimir por Bluetooth. Asegúrate de que la impresora esté conectada. " +
          error.message
        );
      } finally {
        this.printing = false;
        this.$emit("close");
      }
    },
    async getAvailablePrinters() {
      try {
        const respuestaHttp = await fetch("http://localhost:8000/impresoras");
        const listaDeImpresoras = await respuestaHttp.json();
        this.availablePrinters = listaDeImpresoras;
        if (this.availablePrinters.length > 0) {
          this.selectedPrinter = this.availablePrinters[0];
        }
      } catch (e) {
        // El plugin no respondió
        console.log(e);
      }
    },
    async getParzibyteSerial() {
      try {
        const response = await axios.get(route("users.get-parzibyte-serial"));
        if (response.status === 200) {
          this.serial = response.data.serial;
        } else {
          console.error("Error al obtener serial: ", response.status);
        }
      } catch (error) {
        console.error("Error al obtener serial: ", error);
      }
    },
    async getSalesByFolio() {
      try {
        const response = await axios.get(route("sales.get-by-folio", this.saleFolio));
        if (response.status === 200) {
          this.sales = response.data.sales;
        }
      } catch (error) {
        console.error("Error al obtener ventas: ", error);
      }
    },
  },
  async mounted() {
    this.setTicketMode();
    if (this.selectedPrinter) {
      this.availablePrinters.push(this.selectedPrinter);
    }

    if (!this.serial) {
      await this.getParzibyteSerial();
    }
  },
};
</script>
