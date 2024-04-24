<template>
  <AppLayout title="Registrar venta">
    <div class="px-2 lg:px-6 py-7">
      <!-- header botones -->
      <div class="md:flex justify-between items-center mx-3">
        <h1 class="font-bold text-lg">Registrar venta</h1>
        <!-- Dinero en caja -->
        <div class="flex items-center space-x-3">
          <svg @click="showCashRegisterMoney = !showCashRegisterMoney" v-if="!showCashRegisterMoney" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              stroke-width="1.5" stroke="currentColor" class="size-4 text-[#777777] cursor-pointer">
              <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
              <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
          </svg>
          <svg @click="showCashRegisterMoney = !showCashRegisterMoney" v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              stroke-width="1.5" stroke="currentColor" class="size-4 text-[#777777] cursor-pointer">
              <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
          </svg>
          <p class="text-base font-semibold" :class="showCashRegisterMoney ? 'blur-sm' : ''">Caja: ${{ localCurrentCash?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") }}</p>
        </div>
        <!-- Dropdon -->
        <div class="border border-primary rounded-full px-4 pt-[3px] mt-3 md:mt-0">
         <el-col :span="3">
          <el-dropdown>
            <span class="text-sm text-primary w-44 flex items-center">
              <svg class="mr-2" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <mask id="mask0_9380_424" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="12" height="12">
                  <rect width="12" height="12" fill="#D9D9D9"/>
                </mask>
                <g mask="url(#mask0_9380_424)">
                  <path d="M2.5 10.5C2.225 10.5 1.98958 10.4021 1.79375 10.2063C1.59792 10.0104 1.5 9.775 1.5 9.5V2.5C1.5 2.225 1.59792 1.98958 1.79375 1.79375C1.98958 1.59792 2.225 1.5 2.5 1.5H9.5C9.775 1.5 10.0104 1.59792 10.2063 1.79375C10.4021 1.98958 10.5 2.225 10.5 2.5V9.5C10.5 9.775 10.4021 10.0104 10.2063 10.2063C10.0104 10.4021 9.775 10.5 9.5 10.5H2.5ZM6 8C6.31667 8 6.60417 7.90833 6.8625 7.725C7.12083 7.54167 7.3 7.3 7.4 7H9.5V2.5H2.5V7H4.6C4.7 7.3 4.87917 7.54167 5.1375 7.725C5.39583 7.90833 5.68333 8 6 8Z" fill="#F68C0F"/>
                </g>
              </svg>
              Movimientos de caja
              <i class="fa-solid fa-angle-down text-xs ml-2"></i>
            </span>
            <template #dropdown>
              <el-dropdown-menu>
                <el-dropdown-item @click="cashRegisterModal = true; form.cashRegisterMovementType = 'Ingreso'"><i class="fa-solid fa-circle-arrow-down text-xs mr-3"></i>Ingresar efectivo</el-dropdown-item>
                <el-dropdown-item @click="cashRegisterModal = true; form.cashRegisterMovementType = 'Retiro'"><i class="fa-solid fa-circle-arrow-up text-xs mr-3"></i>Retirar efectivo</el-dropdown-item>
                <el-dropdown-item @click="handleCashCut"><i class="fa-solid fa-cash-register text-xs mr-3"></i>Hacer corte</el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
          </el-col>
        </div>
      </div>

      <!-- cuerpo de la pagina -->
      <div class="lg:flex space-x-5 my-5">
        <!-- scaner de código  -->
        <section class="lg:w-[70%]">
          <div v-if="isScanOn" class="relative lg:w-1/2 mx-auto mb-4">
            <input v-model="scannerQuery" :disabled="scanning" @keydown.enter="getProductByCode" ref="scanInput"
              class="input w-full pl-9" placeholder="Escanea o teclea el código del producto" type="text">
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
                <el-input-number v-if="isInventoryOn" v-model="quantity" :min="0" :max="productFoundSelected.current_stock" :precision="2" />
                <el-input-number v-else v-model="quantity" :min="0" :precision="2" />
              </div>
              <div class="text-center mt-7">
                <PrimaryButton @click="addSaleProduct(this.productFoundSelected); productFoundSelected = null"
                  class="!rounded-full !px-24">Agregar
                </PrimaryButton>
              </div>
            </div>
            <p v-else class="text-center text-gray99 text-sm">
              Busca el producto
              <i class="fa-regular fa-hand-point-up ml-3"></i>
            </p>
          </div>

          <!-- Total por cobrar -->
          <div v-if="editableTabs[editableTabsValue - 1]?.saleProducts?.length"
            class="border border-grayD9 rounded-lg p-4 mt-5 text-xs lg:text-base">
            <div v-if="!editableTabs[this.editableTabsValue - 1]?.paying">
              <div v-if="isDiscountOn" class="flex items-center justify-between text-lg mx-5">
                <p>Subtotal</p>
                <p class="text-gray-99">$ <strong class="ml-3">{{
                  calculateTotal().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") }}</strong></p>
              </div>
              <!-- <div v-if="isDiscountOn" class="flex items-center justify-between text-lg mx-5">
                <p class="text-[#999999]">Descuento</p>
                <el-input v-model="editableTabs[this.editableTabsValue - 1].discount" type="number" class="!w-24 !h-8"
                  placeholder="0.00">
                  <template #prefix>
                    <i class="fa-solid fa-dollar-sign"></i>
                  </template>
                </el-input>
              </div> -->
              <div class="flex items-center justify-between text-lg mx-5">
                <p class="font-bold">Total</p>
                <p v-if="(calculateTotal() - editableTabs[this.editableTabsValue - 1].discount) < 0"
                  class="text-red-600 text-xs">El descuento es más grande que el total</p>
                <p v-else class="text-gray-99">$ <strong class="ml-3">{{ (calculateTotal() -
                  editableTabs[this.editableTabsValue
                    - 1].discount)?.toLocaleString('en-US', {
                      minimumFractionDigits: 2
                    }) }}</strong></p>
              </div>
              <div class="text-center mt-7">
                <PrimaryButton @click="receive()"
                  :disabled="editableTabs[this.editableTabsValue - 1]?.saleProducts?.length == 0 || (calculateTotal() - editableTabs[this.editableTabsValue - 1].discount) < 0"
                  class="!rounded-full !px-24 !bg-[#5FCB1F] disabled:!bg-[#999999]">Cobrar</PrimaryButton>
              </div>
            </div>

            <!-- cobrando -->
            <div v-else>
              <p class="text-gray-99 text-center mb-3 text-lg">Total $ <strong>{{ (calculateTotal() -
                editableTabs[this.editableTabsValue - 1].discount)?.toLocaleString('en-US', {
                  minimumFractionDigits: 2
                }) }}</strong>
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

    <!-- -------------- Modal Ingreso o retiro de dinero en caja starts----------------------- -->
    <Modal :show="cashRegisterModal" @close="cashRegisterModal = false; form.reset">
      <div class="p-4 relative">
        <i @click="cashRegisterModal = false"
          class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

        <form class="mt-5 mb-2 md:grid grid-cols-2 gap-3" @submit.prevent="storeCashRegisterMovement">
          <h2 v-if="form.cashRegisterMovementType === 'Ingreso'" class="font-bold col-span-full">Ingresar efectivo a caja</h2>
          <h2 v-if="form.cashRegisterMovementType === 'Retiro'" class="font-bold col-span-full">Retirar efectivo a caja</h2>

          <div class="mt-2">
              <InputLabel v-if="form.cashRegisterMovementType === 'Ingreso'" value="Monto a ingresar" class="ml-3 mb-1 text-sm" />
              <InputLabel v-if="form.cashRegisterMovementType === 'Retiro'" value="Monto a retirar" class="ml-3 mb-1 text-sm" />
              <el-input v-model="form.registerAmount" step="0.01" type="number" placeholder="ingresa el monto">
                  <template #prefix>
                  <i class="fa-solid fa-dollar-sign"></i>
                  </template>
              </el-input>
              <InputError :message="form.errors.registerAmount" />
          </div>

          <div class="col-span-full mt-2">
              <InputLabel value="Motivo (opcional)" class="text-sm ml-2" />
              <el-input v-model="form.registerNotes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                  placeholder="Escribe tus notas" :maxlength="200" show-word-limit clearable />
          </div>

          <div class="flex justify-end space-x-3 pt-2 pb-1 py-2 col-span-full">
              <CancelButton @click="cashRegisterModal = false">Cancelar</CancelButton>
              <PrimaryButton :disabled="!form.registerAmount || form.processing">Confirmar</PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
    <!-- --------------------------- Modal Ingreso o retiro de dinero en caja ends ------------------------------------ -->


    <!-- -------------- Modal corte de caja starts----------------------- -->
    <Modal :show="cashCutModal" @close="cashCutModal = false; form.reset">
      <div class="p-4 relative">
        <i @click="cashCutModal = false; cutForm.reset()"
          class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

        <form class="mt-5 mb-2" @submit.prevent="storeCashCut">
          <h2 class="font-bold col-span-full">Hacer corte de caja</h2>
          <p class="col-span-full">Por favor, cuenta el dinero en caja e ingrésalo para proceder con el corte.</p>
          <div class="rounded-full h-3 bg-[#F2F2F2] my-2"></div>

          <section class="w-full flex justify-end space-x-3 text-sm">
            <div class="w-44 space-y-2">
              <p>Efectivo esperado en caja</p>
              <p>Recuento manual</p>
              <p>Diferencia</p>
            </div>
            <div class="w-44 space-y-2">
              <div v-if="cutLoading">
                <i  class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
              </div>
              <p v-else>${{ (cutForm.totalSaleForCashCut + cutForm.totalCashMovements)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") }}</p>
              <el-input @input="difference()" v-model="cutForm.counted_cash" type="number" step="0.01" class="!w-24 !h-6"
                placeholder="0.00">
                <template #prefix>
                  <i class="fa-solid fa-dollar-sign"></i>
                </template>
              </el-input>
              <p v-if="cutForm.counted_cash" :class="{
                  'text-green-500': (cutForm.difference) === 0,
                  'text-blue-500': (cutForm.difference) < 0,
                  'text-red-500': (cutForm.difference) > 0
              }">
                  <!-- Se multiplica por -1 para cambiar el signo y si sobra sea positivo y si falta negativo -->
                  ${{ (cutForm.difference * -1 )?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") }}
              </p>
              <p v-if="cutForm.counted_cash" :class="{
                  'text-green-500 bg-green-100': (cutForm.difference) === 0,
                  'text-blue-500 bg-blue-100': (cutForm.difference) < 0,
                  'text-red-500 bg-red-100': (cutForm.difference) > 0
              }" class="rounded-full text-xs inline py-[2px] px-2">
                  <!-- Icono de marca de verificación si la diferencia es 0 -->
                  <i v-if="(cutForm.difference) === 0" class="fa-solid fa-check mr-1"></i>
                  <!-- Icono de sobrante en caja si la diferencia es negativa -->
                  <i v-else-if="(cutForm.difference) < 0" class="fa-solid fa-plus mr-1"></i>
                  <!-- Icono de faltante de efectivo si la diferencia es positiva -->
                  <i v-else class="fa-solid fa-xmark mr-1"></i>
                  <!-- Muestra el mensaje correspondiente -->
                  {{ (cutForm.difference) === 0 ? 'Todo bien' : ((cutForm.difference) < 0 ? 'Sobrante en caja' : 'Faltante de efectivo') }}
              </p>
            </div>
          </section>

          <div v-if="cutForm.counted_cash" class="flex items-center space-x-3 mt-3">
            <div class="w-full">
              <InputLabel value="Monto a retirar de caja" class="text-sm ml-2" />
              <el-input v-model="cutForm.amount_withdrawn" type="number" step="0.01" class="!w-1/2 !h-6"
                placeholder="0.00">
                <template #prefix>
                  <i class="fa-solid fa-dollar-sign"></i>
                </template>
              </el-input>
            </div>
            <p v-if="cutForm.amount_withdrawn" class="w-full mt-3 text-sm font-bold">Efectivo que dejarás en caja: ${{ (cutForm.counted_cash - cutForm.amount_withdrawn)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") }}</p>
          </div>


          <div class="col-span-full mt-2">
              <InputLabel value="Comentarios (opcional)" class="text-sm ml-2" />
              <el-input v-model="cutForm.notes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                  placeholder="Escribe auí cualquier comentario relacionado al corte" :maxlength="255" show-word-limit clearable />
          </div>

          <div class="flex justify-end space-x-3 pt-2 pb-1 py-2 col-span-full">
              <CancelButton @click="cashCutModal = false; cutForm.reset()">Cancelar</CancelButton>
              <PrimaryButton :disabled="!cutForm.counted_cash || cutForm.processing">Hacer corte</PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
    <!-- --------------------------- Modal corte de caja ends ------------------------------------ -->
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import SaleTable from '@/Components/MyComponents/Sale/SaleTable.vue';
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/vue3";
import axios from 'axios';

export default {
  data() {

    const form = useForm({
        cashRegisterMovementType: null, //que tipo de movimiento es
        registerAmount: null, //Dinero ingresado o sacado de caja
        registerNotes: null, //notas al entrar o sacar dinero
    });

    const cutForm = useForm({
        counted_cash: null,
        difference: null,
        notes: null,
        totalSaleForCashCut: null, //dinero esperado de ventas hechas para hacer corte
        totalCashMovements: null, //dinero de movimientos de caja para hacer corte
        amount_withdrawn: null, //dinero retirado de caja tras haber hecho el corte
    });

    return {
      form,
      cutForm,
      
      showCashRegisterMoney: true, //muestra u oculta el dinero de caja
      localCurrentCash: this.cash_register.current_cash, //dinero de caja local
      cashRegisterModal: false, //muestra el modal para ingresar o retirar dinero de la caja
      cashCutModal: false, //muestra el modal para el corte de caja
      // inventario de codigos activado
      isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
      // descuentos activados
      isDiscountOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Hacer descuentos')?.value,
      // escaneo de codigos activado
      isScanOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Escanear productos')?.value,

      storeProcessing: false, //cargando store de venta
      scanning: false, //cargando la busqueda de productos por escaner
      loading: false, //cargando la busqueda de productos
      cutLoading: false, //cargando monto total esperado para corte
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
    InputLabel,
    InputError,
    SaleTable,
    Modal
  },
  props: {
    products: Array,
    cash_register: Object
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
          this.fetchCurrentCash();
        }
      } catch (error) {
        console.log(error);
      }
    },
    storeCashRegisterMovement() {
      this.form.post(route('cash-register-movements.store'), {
          onSuccess: () => {
              this.$notify({
                  title: "Correcto",
                  message: "Se ha registrado el movimiento de caja",
                  type: "success",
              });
              this.cashRegisterModal = false;
              this.form.reset();
              this.fetchCurrentCash();
          },
      });
    },
    storeCashCut() {
      this.cutForm.post(route('cash-cuts.store'), {
          onSuccess: () => {
              this.$notify({
                  title: "Correcto",
                  message: "Se ha realizado el corte de caja",
                  type: "success",
              });
              this.cashCutModal = false;
              this.cutForm.reset();
          },
      });
    },
    difference() {
      //  Se hace la resta al reves para cambiar el signo y si sobra sea positivo y si falta negativo
      this.cutForm.difference = (this.cutForm.totalSaleForCashCut + this.cutForm.totalCashMovements) - this.cutForm.counted_cash
    },  
    deleteProduct(productId) {
      const indexToDelete = this.editableTabs[this.editableTabsValue - 1].saleProducts.findIndex(sale => sale.product.id === productId);
      this.editableTabs[this.editableTabsValue - 1].saleProducts.splice(indexToDelete, 1);
    },
    async fetchCurrentCash() {
      try {
        const response = await axios.get(route('cash-registers.fetch-current-cash'));
        if (response.status === 200) {
          this.localCurrentCash = response.data.item;
        }
      } catch (error) { 
        console.log(error);
      }
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
    async fetchTotalSaleForCashCut() {
      try {
        const response = await axios.get(route('cash-cuts.fetch-total-sales-for-cash-cut'));
        if ( response.status === 200 ) {
          this.cutForm.totalSaleForCashCut = response.data;
        }
      } catch (error) {
        console.log(error);
      }
    },
    async fetchTotalCashMovements() {
      try {
        this.cutLoading = true;
        const response = await axios.get(route('cash-register-movements.fetch-total-cash-movements'));
        if ( response.status === 200 ) {
          this.cutForm.totalCashMovements = response.data;
          this.cutLoading = false;
        }
      } catch (error) {
        console.log(error);
      }
    },
    handleCashCut() {
      this.fetchTotalSaleForCashCut();
      this.fetchTotalCashMovements();
      this.cashCutModal = true;
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
      this.inputFocus();
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
      this.inputFocus();
    },
    calculateTotal() {
      // Suma de los productos del precio y la cantidad para cada elemento en saleProducts
      const total = this.editableTabs[this.editableTabsValue - 1]?.saleProducts?.reduce((accumulator, sale) => {
        return accumulator + sale.product.public_price * sale.quantity;
      }, 0);

      // Formatear el resultado al final
      // return total?.toLocaleString('en-US', { minimumFractionDigits: 2 }); formatea el total con comas pero me manda a NaN despues de 1000
      return total;
    },
    receive() {
      this.editableTabs[this.editableTabsValue - 1].paying = true;
      this.receivedInputFocus();
    },
    inputFocus() {
      this.$nextTick(() => {
        if (this.isScanOn) {
          this.$refs.scanInput.focus();
        } else {
          this.$refs.searchInput.focus();
        }
      });
    },
    receivedInputFocus() {
      this.$nextTick(() => {
        this.$refs.receivedInput.focus(); // Enfocar el input de código cuando se abre el modal
      });
    },
  },
  mounted() {
    if (this.isScanOn) {
      this.$refs.scanInput.focus(); // Enfocar el input de código cuando se abre el modal
    } else {
      this.$refs.searchInput.focus(); // Enfocar el input de buscar producto cuando se abre el modal
    }
  }
}
</script>
