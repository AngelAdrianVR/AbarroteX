<template>
    <AppLayout title="Catálogo de Productos">
        <main class="p-6 max-w-[90rem] mx-auto">
          <div class="flex justify-between items-end">
            <h1 class="text-2xl font-bold text-center">Catálogo de Productos</h1>
            <div v-if="balance !== null" class="text-center text-gray-700">
              <span class="text-lg font-semibold">Balance disponible:</span>
              <span class="text-green-600 font-bold text-xl"> ${{ parseFloat(balance).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} MXN</span>
            </div>
          </div>

            <SmallLoading class="mx-auto mt-5" v-if="loading" />

            <!-- Categoria -->
            <section class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 my-10" v-if="!loading">
              <div @click="SelectCategory(category)"
                  v-for="category in categories" :key="category"
                  class="cursor-pointer p-4 rounded-xl border hover:shadow-lg transition duration-200 hover:bg-gray-50"
                  :class="{ 'border-primary bg-gray-50 text-primary': category?.label === categorySelected?.label }">
                  <p>{{ category?.label }}</p>
              </div>
            </section>

            <el-divider v-if="categorySelected" />

            <!-- Subcategoría -->
            <section class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-7 gap-4 my-10" v-if="categorySelected">
              <div @click="SelectSubcategory(subcategory)"
                  v-for="subcategory in categorySelected.subcategories" :key="subcategory" 
                  class="cursor-pointer p-4 rounded-xl border hover:shadow-lg transition duration-200"
                  :class="{ 'border-primary': subcategory.label === subcategorySelected?.label }"
                  >
                  <img :src="subcategory.image" :alt="subcategory.label">
              </div>
            </section>

            <el-divider v-if="subcategorySelected" />

            <!-- Productos filtrados por subcategoría -->
            <section class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-5 gap-4 my-10" v-if="subcategorySelected">
              <div @click="seleccionarProducto(product)"
                v-for="product in filteredProducts" 
                :key="product.sku"
                class="cursor-pointer p-4 rounded-xl border hover:shadow-lg transition duration-200 hover:bg-gray-50"
                :class="{ 'border-primary bg-gray-50': productoSeleccionado?.sku === product.sku }"
              >
                <div class="font-medium text-gray-800">{{ product.name }}</div>
                <div class="text-sm text-gray-500 truncate">{{ product.description }}</div>
              </div>
            </section>

            <div v-if="!loading">
              <!-- <div v-for="(productos, categoriaId) in productosPorCategoria" :key="categoriaId" class="mb-6">
                  <h2 class="text-xl font-semibold mb-2 text-gray-700 border-b pb-1">Categoría {{ categoriaId }}</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        v-for="producto in productos"
                        :key="producto.sku"
                        @click="seleccionarProducto(producto)"
                        class="cursor-pointer p-4 rounded-xl border hover:shadow-lg transition duration-200 bg-white hover:bg-gray-50"
                        :class="{ 'border-primary': productoSeleccionado?.sku === producto.sku }"
                    >
                        <p class="font-medium text-gray-800">{{ producto.name }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ producto.description }}</p>
                    </div>
                  </div>
              </div> -->

              <!-- Informacion del producto seleccionado -->
              <div v-if="productoSeleccionado" class="mt-8 p-4 rounded-xl border bg-blue-50">
                  <h3 class="text-lg font-bold mb-2">Detalles del producto seleccionado:</h3>
                  <p><strong>Nombre:</strong> {{ productoSeleccionado.name }}</p>
                  <p><strong>SKU:</strong> {{ productoSeleccionado.sku }}</p>
                  <p><strong>Importe:</strong> {{ productoSeleccionado.minAmount }} - {{ productoSeleccionado.maxAmount }} MXN</p>
                  <p><strong>Comisión:</strong> {{ productoSeleccionado.fee }} MXN</p>
              </div>

              <!-- Realizar venta -->
              <div v-if="productoSeleccionado" class="mt-6 border-t pt-4">
                <h4 class="font-bold mb-2">Realizar Venta</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-600">reference / Número</label>
                    <input v-model="reference" type="text" class="w-full border p-2 rounded" placeholder="Ej. 5546757886" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-600">amount</label>
                    <input v-model="amount" type="number" step="0.01" class="w-full border p-2 rounded" placeholder="Ej. 50.00" />
                  </div>
                </div>

                <button
                  :disabled="realizandoVenta || !reference || !amount"
                  @click="realizarVenta"
                  class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                >
                  {{ realizandoVenta ? 'Procesando...' : 'Realizar Venta' }}
                </button>

                <div v-if="resultadoVenta" class="mt-4 p-3 rounded bg-green-100 text-green-700">
                  Venta realizada. ID Transacción: {{ resultadoVenta.transactionId }}
                </div>
              </div>

               <!-- Estatus de la venta -->
              <div v-if="resultadoVenta" class="mt-4 p-3 rounded bg-green-100 text-green-700">
                Venta realizada. ID Transacción: {{ resultadoVenta.transactionId }}

                <button
                  class="ml-4 px-3 py-1 text-sm bg-gray-700 text-white rounded hover:bg-black transition"
                  @click="verificarEstadoVenta"
                  :disabled="verificandoEstado"
                >
                  {{ verificandoEstado ? 'Verificando...' : 'Verificar estado' }}
                </button>

                <div v-if="estadoVenta" class="mt-3 text-sm text-gray-800">
                  <p><strong>Estado:</strong> {{ estadoVenta.transactionStatusDescription }}</p>
                  <p><strong>Código:</strong> {{ estadoVenta.codeTransaction }} - {{ estadoVenta.codeDescription }}</p>
                </div>
              </div>
            </div>
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import SmallLoading from '@/Components/MyComponents/SmallLoading.vue';
import axios from 'axios';

// recargas images
import telcel from "../../../../public/images/prontipagos/telcel.png";
import alo from "../../../../public/images/prontipagos/alo_b.png";
import maztiempo from "../../../../public/images/prontipagos/maztiempo_b.png";
import att_nextel from "../../../../public/images/prontipagos/att_nextel.png";
import movistar from "../../../../public/images/prontipagos/movistar.png";
import iusacel from "../../../../public/images/prontipagos/iusacel.png";
import virgin from "../../../../public/images/prontipagos/virgin.png";
import cierto from "../../../../public/images/prontipagos/cierto_b.png";
import unefon from "../../../../public/images/prontipagos/unefon_B.png";
import flashMobile from "../../../../public/images/prontipagos/FlashMobile.png";

export default {
  name: 'CatalogoProductos',
  data() {
    return {
      categories: [
        {
          label: 'Recargas de tiempo aire',
          icon: '', 
          subcategories: [
            { id: 1, label: 'Telcel', image: telcel },
            { id: 49, label: 'alo', image: alo },
            { id: 74, label: 'maz tiempo', image: maztiempo },
            { id: 40, label: 'at&t nextel', image: att_nextel },
            { id: 45, label: 'Movistar', image: movistar },
            { id: 38, label: 'iusacell', image: iusacel },
            { id: 41, label: 'virgin mobile', image: virgin },
            { id: 73, label: 'cierto', image: cierto },
            { id: 39, label: 'unefon', image: unefon },
            { id: 160, label: 'flash mobile', image: flashMobile },
          ]
        },
        {
          label: 'Pago de servicios',
          icon: '',
        },
        {
          label: 'Tarjetas de regalo digitales',
          icon: '',
        }, 
        {
          label: 'Pagos bancarios',
          icon: '',
        }
      ],
      categorySelected: null, 
      subcategorySelected: null,
      catalogo: [],
      productoSeleccionado: null,
      loading: false,

      // Realizar venta
      reference: '',
      amount: '',
      resultadoVenta: null,
      realizandoVenta: false,
      balance: null,

      // Estatus de la venta
      estadoVenta: null,
      verificandoEstado: false,
    };
  },
  components: {
    AppLayout,
    SmallLoading,
  },
  computed: {
    productosPorCategoria() {
      const agrupados = {};
      this.catalogo.forEach((producto) => {
        if (!agrupados[producto.categoryId]) {
          agrupados[producto.categoryId] = [];
        }
        agrupados[producto.categoryId].push(producto);
      });
      return agrupados;
    },
    filteredProducts() {
      if (!this.subcategorySelected) return [];
      return this.catalogo.filter(p => p.categoryId === this.subcategorySelected.id);
    }
  },
  methods: {
    SelectCategory(category) {
      this.categorySelected = category;
      this.subcategorySelected = null; // reiniciamos la subcategoría
    },
    SelectSubcategory(subcategory) {
      this.subcategorySelected = subcategory;
    },
    async obtenerCatalogo() {
      this.loading = true;
      try {
        const response = await axios.get(route('prontipagos.get-catalog-products'));
        this.catalogo = response.data;
      } catch (error) {
        console.error('Error al obtener catálogo:', error);
      } finally {
        this.loading = false;
      }
    },
    async obtenerBalance() {
      try {
        const response = await axios.get(route('prontipagos.get-current-balance'));
        this.balance = response.data.balance;
      } catch (error) {
        console.error('Error al obtener balance:', error);
      }
    },
    seleccionarProducto(producto) {
      this.productoSeleccionado = producto;
    },
    async realizarVenta() {
      this.realizandoVenta = true;
      this.resultadoVenta = null;

      const transacctionId = Date.now(); // puedes usar un generador UUID si lo prefieres

      try {
        const response = await axios.post(route('prontipagos.make-sale'), {
          sku: this.productoSeleccionado.sku,
          amount: parseFloat(this.amount),
          reference: this.reference,
          transacctionId,
          requiresRegionalization: this.productoSeleccionado.name.toLowerCase().includes('telcel'),
        });

        this.resultadoVenta = response.data;
        this.reference = '';
        this.amount = '';

        // ✅ Actualizar balance después de la venta
        await this.obtenerBalance();

      } catch (error) {
        alert('Error al realizar la venta: ' + (error.response?.data?.error || error.message));
      } finally {
        this.realizandoVenta = false;
      }
    },
    async verificarEstadoVenta() {
      this.verificandoEstado = true;
      this.estadoVenta = null;

      try {
        const response = await axios.get(`prontipagos/sale/satus/${this.resultadoVenta.transactionId}`);
        this.estadoVenta = response.data;
      } catch (error) {
        alert('Error al consultar el estado: ' + (error.response?.data?.error || error.message));
      } finally {
        this.verificandoEstado = false;
      }
    }
  },
  mounted() {
    this.obtenerCatalogo();
    this.obtenerBalance();
  },
};
</script>
