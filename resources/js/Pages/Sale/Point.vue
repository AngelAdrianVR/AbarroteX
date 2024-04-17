<template>
  <AppLayout title="Registrar venta">
    <div class="px-2 lg:px-6 py-7">
      <!-- header botones -->
      <div class="lg:flex justify-between items-center mx-3">
        <h1 class="font-bold text-lg">Registrar venta</h1>
        <div class="my-4 lg:my-0 flex items-center space-x-3">
        </div>
        <span></span>
      </div>

      <!-- cuerpo de la pagina -->
      <div class="lg:flex space-x-5 my-5">
        <!-- scaner de código  -->
        <section class="lg:w-[70%]">
          <div class="relative lg:w-1/2 mx-auto mb-4">
            <input v-model="scannerQuery" :disabled="scanning" @keydown.enter="getProductByCode" ref="scanInput"
              class="input w-full pl-9" placeholder="Escanea el producto" type="text">
            <i class="fa-solid fa-barcode text-xs text-gray99 absolute top-[10px] left-4"></i>
          </div>
          <!-- Pestañas -->
          <div class="mx-7">
            <el-tabs v-model="editableTabsValue" type="card" class="demo-tabs">
              <el-tab-pane v-for="tab in editableTabs" :key="tab.name" :label="tab.title" :name="tab.name">
                <el-popconfirm v-if="tab.saleProducts.length" confirm-button-text="Si" cancel-button-text="No"
                  icon-color="#C30303" title="Se eliminará todo el registro de productos ¿Deseas continuar?"
                  @confirm="clearTab()">
                  <template #reference>
                    <ThirthButton class="!text-[#F80505] !border-[#F80505] !py-1 !px-2 mb-2"><i
                        class="fa-regular fa-trash-can mr-2"></i> Limpiar registros</ThirthButton>
                  </template>
                </el-popconfirm>
                <SaleTable @delete-product="deleteProduct" :saleProducts="tab.saleProducts" />
              </el-tab-pane>
            </el-tabs>
          </div>
        </section>

        <!-- seccion de desgloce de montos -->
        <section class="lg:w-[30%]">
          <!-- buscador de productos -->
          <div class="relative">
            <input v-model="searchQuery" @focus="searchFocus = true" @blur="handleBlur" @input="searchProducts"
              ref="searchInput" class="input w-full pl-9" placeholder="Buscar código o nombre de producto"
              type="search">
            <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
            <!-- Resultados de la búsqueda -->
            <div v-if="searchFocus && searchQuery"
              class="absolute mt-1 bg-white border border-gray-300 rounded shadow-lg w-full z-50 h-48 overflow-auto">
              <ul v-if="productsFound?.length > 0 && !loading">
                <li @click="productFoundSelected = product; searchQuery = null"
                  v-for="(product, index) in productsFound" :key="index"
                  class="hover:bg-gray-200 cursor-default text-sm px-5 py-2">{{ product.global_product_id ?
                    product.global_product?.name : product.name }}</li>
              </ul>
              <p v-else-if="!loading" class="text-center text-sm text-gray-600 px-5 py-2">No se encontraron
                coincidencias
              </p>
              <!-- estado de carga -->
              <div v-if="loading" class="flex justify-center items-center py-10">
                <i class="fa-solid fa-square fa-spin text-4xl text-primary"></i>
              </div>
            </div>
          </div>

          <!-- Detalle de producto encontrado -->
          <div class="border border-grayD9 rounded-lg p-4 mt-5 text-xs lg:text-base">
            <div class="relative" v-if="productFoundSelected">
              <i @click="productFoundSelected = null"
                class="fa-solid fa-xmark cursor-pointer size-5 rounded-full flex items-center justify-center absolute right-3"></i>
              <figure class="h-32">
                <img class="object-contain w-32 mx-auto"
                  :src="productFoundSelected?.global_product_id ? productFoundSelected?.global_product?.media[0]?.original_url : productFoundSelected?.media[0]?.original_url">
              </figure>
              <div class="flex justify-between items-center mt-2 mb-4">
                <p class="font-bold">{{ productFoundSelected?.global_product_id ?
                  productFoundSelected?.global_product?.name :
                  productFoundSelected?.name }}</p>
                <p class="text-[#5FCB1F]">${{ productFoundSelected?.public_price }}</p>
              </div>
              <div class="flex justify-between items-center">
                <p class="text-gray99">Cantidad</p>
                <!-- <el-input-number v-model="quantity" :min="0" :max="productFoundSelected.current_stock" :precision="2" /> en caso de tomar en cuenta stock -->
                <el-input-number v-model="quantity" :min="0" :precision="2" />
              </div>
              <div class="text-center mt-7">
                <PrimaryButton @click="addSaleProduct(this.productFoundSelected); productFoundSelected = null"
                  class="!rounded-full !px-24">Agregar
                </PrimaryButton>
              </div>
            </div>
            <p v-else class="text-center text-gray-500 text-sm">Sin información de producto</p>
          </div>

          <!-- Total por cobrar -->
          <div v-if="editableTabs[editableTabsValue - 1]?.saleProducts?.length" class="border border-grayD9 rounded-lg p-4 mt-5 text-xs lg:text-base">
            <div v-if="!editableTabs[this.editableTabsValue - 1]?.paying">
              <div v-if="isDiscountOn" class="flex items-center justify-between text-lg mx-5">
                <p>Subtotal</p>
                <p class="text-gray-99">$ <strong class="ml-3">{{ calculateTotal() }}</strong></p>
              </div>
              <div v-if="isDiscountOn" class="flex items-center justify-between text-lg mx-5">
                <p class="text-[#999999]">Descuento</p>
                <el-input v-model="editableTabs[this.editableTabsValue - 1].discount" type="number" class="!w-24 !h-8" placeholder="0.00">
                    <template #prefix>
                        <i class="fa-solid fa-dollar-sign"></i>
                    </template>
                </el-input>
              </div>
              <div class="flex items-center justify-between text-lg mx-5">
                <p class="font-bold">Total</p>
                <p v-if="(calculateTotal() - editableTabs[this.editableTabsValue - 1].discount) < 0"
                  class="text-red-600 text-xs">El descuento es más grande que el total</p>
                <p v-else class="text-gray-99">$ <strong class="ml-3">{{ calculateTotal() -
                  editableTabs[this.editableTabsValue
                    - 1].discount }}</strong></p>
              </div>
              <div class="text-center mt-7">
                <PrimaryButton @click="receive()"
                  :disabled="editableTabs[this.editableTabsValue - 1]?.saleProducts?.length == 0 || (calculateTotal() - editableTabs[this.editableTabsValue - 1].discount) < 0"
                  class="!rounded-full !px-24 !bg-[#5FCB1F] disabled:!bg-[#999999]">Cobrar</PrimaryButton>
              </div>
            </div>

            <!-- cobrando -->
            <div v-else>
              <p class="text-gray-99 text-center mb-3 text-lg">Total $ <strong>{{ calculateTotal() -
                editableTabs[this.editableTabsValue - 1].discount }}</strong>
              </p>
              <div class="flex items-center justify-between mx-5 space-x-10">
                <p>Entregado</p>
                <input v-model="editableTabs[this.editableTabsValue - 1].moneyReceived" @keydown.enter="store"
                  type="number" class="input !rounded-md w-1/3" ref="receivedInput" placeholder="$0.00">
              </div>
              <div class="flex items-center justify-between mx-5 my-2 relative">
                <p>Cambio</p>
                <p
                  v-if="(calculateTotal() - editableTabs[this.editableTabsValue - 1].discount) <= editableTabs[this.editableTabsValue - 1]?.moneyReceived">
                  ${{
                    (editableTabs[this.editableTabsValue - 1]?.moneyReceived - (calculateTotal() -
                      editableTabs[this.editableTabsValue - 1].discount)).toLocaleString('en-US', {
                        minimumFractionDigits: 2
                  }) }}</p>
              </div>
              <p v-if="((calculateTotal() - editableTabs[this.editableTabsValue - 1].discount) > editableTabs[this.editableTabsValue - 1]?.moneyReceived) && editableTabs[this.editableTabsValue - 1].moneyReceived"
                class="text-xs text-primary text-center mb-3">La cantidad es insuficiente. Por favor, ingrese una
                cantidad
                igual o mayor al total de compra.</p>
              <div class="flex space-x-2 justify-end">
                <CancelButton @click="editableTabs[this.editableTabsValue - 1].paying = false">Cancelar</CancelButton>
                <PrimaryButton
                  :disabled="storeProcessing || (calculateTotal() - editableTabs[this.editableTabsValue - 1].discount) > editableTabs[this.editableTabsValue - 1]?.moneyReceived"
                  @click="store" class="!rounded-full">Aceptar</PrimaryButton>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import SaleTable from '@/Components/MyComponents/Sale/SaleTable.vue';
import axios from 'axios';

export default {
  data() {

    return {
      // descuentos activados
      isDiscountOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Hacer descuentos')?.value,
      storeProcessing: false, //cargando store de venta
      scanning: false, //cargando la busqueda de productos por escaner
      loading: false, //cargando la busqueda de productos
      scannerQuery: null, //input para scanear el codigo de producto
      searchQuery: null, //buscador
      searchFocus: false, //buscador
      productsFound: null, //buscador
      productSelected: null, //producto escaneado agergado a la lista de compras
      productFoundSelected: null, //producto seleccionado desde barra de busqueda
      quantity: 1, //cantidad para agregar del producto escaneado o buscado
      tabIndex: 1, //index del tab - componente de tabs
      editableTabsValue: "1", //tab seleccionado - componente de tabs
      editableTabs: [ //Informacion del tab - componente de tabs
        {
          title: "Registro 1",
          name: "1",
          saleProducts: [],
          paying: false,
          discount: 0,
          moneyReceived: null,
        },
        {
          title: "Registro 2",
          name: "2",
          saleProducts: [],
          paying: false,
          discount: 0,
          moneyReceived: null,
        },
        {
          title: "Registro 3",
          name: "3",
          saleProducts: [],
          paying: false,
          discount: 0,
          moneyReceived: null,
        },
      ],
    }
  },
  components: {
    AppLayout,
    PrimaryButton,
    ThirthButton,
    CancelButton,
    SaleTable
  },
  props: {
    products: Array
  },
  methods: {
    async store() {
      try {
        this.storeProcessing = true;
        const response = await axios.post(route('sales.store'), {
          data: {
            saleProducts: this.editableTabs[this.editableTabsValue - 1]?.saleProducts
          }
        });
        if (response.status === 200) {
          this.$notify({
            title: "Correcto",
            text: "Se ha registrado la venta con éxito!",
            type: "success",
          });
          this.storeProcessing = false;
          this.clearTab();
        }
      } catch (error) {
        console.log(error);
      }
    },
    deleteProduct(productId) {
      const indexToDelete = this.editableTabs[this.editableTabsValue - 1].saleProducts.findIndex(sale => sale.product.id === productId);
      this.editableTabs[this.editableTabsValue - 1].saleProducts.splice(indexToDelete, 1);
    },
    async searchProducts() {
      try {
        this.loading = true;
        const response = await axios.get(route('products.search'), { params: { query: this.searchQuery } });
        if (response.status === 200) {
          this.productsFound = response.data.items;
          this.loading = false;
        }
      } catch (error) {
        console.log(error);
      }
    },
    handleBlur() {
      // Introducir un retraso para dar tiempo al evento click de ejecutarse antes del blur
      setTimeout(() => {
        this.searchFocus = false;
      }, 80);
    },
    async getProductByCode() {
      this.scanning = true;
      //buscar primero en productos transferidos del catalogo con el codigo escaneado
      let productScaned = this.products.find(item => item.global_product?.code === this.scannerQuery);
      let is_local_product = false;

      //si no se encontró en productos transferidos se busca en productos locales
      if (productScaned == null) {
        productScaned = this.products.find(item => item.code === this.scannerQuery);
        is_local_product = true;
      }

      // si no se encontró el producto escaneado aparece un mensaje y no busca en la bd para no tardar más
      if (productScaned != null) {
        try {
          if (is_local_product) {
            const response = await axios.get(route('products.get-product-scaned', [productScaned.id, { is_local_product: is_local_product }]));

            if (response.status === 200 && response.data && response.data.item) {
              this.productSelected = response.data.item;
              this.addSaleProduct(this.productSelected);
            } else {
              console.error('La respuesta no tiene el formato esperado.');
            }
          } else {
            const response = await axios.get(route('products.get-product-scaned', [productScaned.global_product.id, { is_local_product: is_local_product }]));

            if (response.status === 200 && response.data && response.data.item) {
              this.productSelected = response.data.item;
              this.addSaleProduct(this.productSelected);
            } else {
              console.error('La respuesta no tiene el formato esperado.');
            }
          }
        } catch (error) {
          console.error('Error al realizar la solicitud:', error);
        } finally {
          this.scanning = false;
        }
      } else {
        this.$notify({
          title: "Poducto no encontrado",
          message: "El producto escaneado no esta registrado en la base de datos",
          type: "warning"
        });
        console.error('El producto escaneado no tiene la propiedad "id".');
        this.scannerQuery = null;
        this.scanning = false;
      }
    },
    addSaleProduct(product) {
      //revisa si el producto escaneado ya esta dentro del arreglo
      const existingIndex = this.editableTabs[this.editableTabsValue - 1].saleProducts.findIndex(sale => sale.product.id === product.id);
      if (existingIndex !== -1) {
        this.editableTabs[this.editableTabsValue - 1].saleProducts[existingIndex] = {
          ...this.editableTabs[this.editableTabsValue - 1].saleProducts[existingIndex],
          quantity: this.editableTabs[this.editableTabsValue - 1].saleProducts[existingIndex].quantity + this.quantity
        };
      } else {
        // Si el producto no existe, agrégalo al array
        this.editableTabs[this.editableTabsValue - 1].saleProducts.push({
          product: product,
          quantity: this.quantity
        });
      }
      this.scannerQuery = null;
      this.quantity = 1;
      this.scanning = false;
      this.scanInputFocus();
    },
    clearTab() {
      this.searchQuery = null;
      this.scannerQuery = null;
      this.searchFocus = false;
      this.productsFound = null;
      this.productSelected = null;
      this.editableTabs[this.editableTabsValue - 1].saleProducts = [];
      this.editableTabs[this.editableTabsValue - 1].paying = false;
      this.editableTabs[this.editableTabsValue - 1].discount = 0;
      this.editableTabs[this.editableTabsValue - 1].moneyReceived = null;
      this.scanInputFocus();
    },
    calculateTotal() {
      // Suma de los productos del precio y la cantidad para cada elemento en saleProducts
      const total = this.editableTabs[this.editableTabsValue - 1]?.saleProducts?.reduce((accumulator, sale) => {
        return accumulator + sale.product.public_price * sale.quantity;
      }, 0);

      // Formatear el resultado al final
      return total?.toLocaleString('en-US', { minimumFractionDigits: 2 });
    },
    receive() {
      this.editableTabs[this.editableTabsValue - 1].paying = true;
      this.receivedInputFocus();
    },
    scanInputFocus() {
      this.$nextTick(() => {
        this.$refs.scanInput.focus(); // Enfocar el input de código cuando se abre el modal
      });
    },
    receivedInputFocus() {
      this.$nextTick(() => {
        this.$refs.receivedInput.focus(); // Enfocar el input de código cuando se abre el modal
      });
    },
  },
  mounted() {
    this.$refs.scanInput.focus(); // Enfocar el input de código cuando se abre el modal
  }
}
</script>
