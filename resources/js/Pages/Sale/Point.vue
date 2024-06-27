<template>
  <AppLayout title="Registrar venta">
    <div v-if="!isOnline" class="w-2/3 ml-auto mt-3 rounded-s-[5px] px-4 py-1 bg-[#232323] text-white text-xs">
      <p class="text-sm flex items-center space-x-3 font-semibold">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24"
          id="Wifi-Disabled--Streamline-Core" height="16" width="16">
          <desc>Wifi Disabled Streamline Icon: https://streamlinehq.com</desc>
          <g id="wifi-disabled--wireless-wifi-internet-server-network-disabled-off-offline-connection">
            <path id="Vector 2432" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="m0.7960714285714285 0.7960714285714285 20.697857142857142 20.697857142857142" stroke-width="2.1">
            </path>
            <path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="M11.145 21.09573364285714c1.151915357142857 0 2.0857071428571428 -0.9337917857142857 2.0857071428571428 -2.0857071428571428s-0.9337917857142857 -2.0857071428571428 -2.0857071428571428 -2.0857071428571428c-1.1518994357142855 0 -2.0857071428571428 0.9337917857142857 -2.0857071428571428 2.0857071428571428s0.9338077071428571 2.0857071428571428 2.0857071428571428 2.0857071428571428Z"
              stroke-width="2.1"></path>
            <path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="M7.212454907142857 14.32936532142857c0.5177170928571428 -0.53150505 1.1366626285714285 -0.9539483142857142 1.820281007142857 -1.2423809142857143"
              stroke-width="2.1"></path>
            <path id="Vector_3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="M3.757441221428571 11.6385006c0.9691532785714285 -0.9748053857142858 2.0014509428571428 -1.5694389 2.0014509428571428 -1.5694389"
              stroke-width="2.1"></path>
            <path id="Vector_4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="M8.83744367142857 8.825040878571428s0.9409086642857143 -0.26260804285714284 2.3155170428571425 -0.26260804285714284c1.3745924571428572 0 2.7356357785714285 0.2717628642857143 4.004828378571428 0.7996378285714286 1.2691448357142856 0.5278749642857142 2.4215378357142856 1.3014812571428571 3.390675192857143 2.276286642857143"
              stroke-width="2.1"></path>
            <path id="Vector_5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="M5.543936957142857 5.503400999999999c1.775701007142857 -0.7357929 3.678980421428571 -1.1145159214285714 5.601094885714286 -1.1145159214285714 1.9221144642857142 0 3.8253938785714285 0.3787230214285714 5.601126728571429 1.1145159214285714 1.7757169285714285 0.7357929 3.389035285714286 1.8142308642857141 4.74777 3.1737298071428572"
              stroke-width="2.1"></path>
            <path id="Vector_6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="M0.7960714285714285 8.677305942857142c0.57410124 -0.5744133 1.1653052785714284 -1.0461015428571427 1.87291725 -1.5767946"
              stroke-width="2.1"></path>
          </g>
        </svg>
        <span>Sin conexión a Internet</span>
      </p>
      <p class="text-xs">
        Las ventas que realices se guardan en el dispositivo que estas utilizando y
        luego se transfieren automáticamente a la nube cuando tengas internet.
        ¡Así nunca perderán información!. <br>
        <b>Es importante que no recargues la página para poder registrar ventas</b>
      </p>
    </div>
    <div v-if="syncingData || syncingIDB" class="w-2/3 ml-auto mt-3 rounded-s-[5px] px-4 py-1 bg-secondary text-gray37 text-xs">
      <p class="text-sm flex items-center space-x-3 font-semibold">
        <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          id="Rotate-Right--Streamline-Sharp" height="16" width="16">
          <desc>Rotate Right Streamline Icon: https://streamlinehq.com</desc>
          <g id="rotate-right">
            <path id="Vector 2754" stroke="currentColor" d="M20.2047 0.5135V4.8893H15.8289" stroke-width="2"></path>
            <path id="Ellipse 1206" stroke="currentColor"
              d="M20.2047 4.764C18.2001 2.4929 15.2674 1.0605 12.0001 1.0605C5.9583 1.0605 1.0605 5.9583 1.0605 12C1.0605 16.194 3.4207 19.8367 6.8853 21.6726"
              stroke-width="2"></path>
            <path id="Ellipse 1207" stroke="currentColor"
              d="M9.1081 22.5533C10.0293 22.8051 10.999 22.9395 11.9999 22.9395C13.4231 22.9395 14.7826 22.6678 16.0297 22.1734"
              stroke-width="2"></path>
            <path id="Ellipse 1208" stroke="currentColor"
              d="M17.7655 21.2986C19.2694 20.3641 20.5299 19.0749 21.4301 17.548" stroke-width="2"></path>
            <path id="Ellipse 1209" stroke="currentColor" d="M22.9395 12C22.9395 13.2879 22.717 14.5237 22.3083 15.6713"
              stroke-width="2"></path>
          </g>
        </svg>
        <span>Sincronizando datos</span>
      </p>
      <p v-if="syncingIDB" class="text-xs">
        Por favor, evita recargar la página y espera a que los datos se carguen en el dispositivo. Una vez terminado el proceso, ya podrás registrar ventas
      </p>
      <p v-else class="text-xs">
        Por favor, evita recargar la página y espera a que los datos se carguen a la nube.
      </p>
    </div>
    <div class="px-2 lg:px-6 py-7">
      <!-- header botones -->
      <div class="lg:flex justify-between items-center mx-3">
        <h1 class="font-bold text-lg">Registrar venta</h1>
        <!-- Dinero en caja -->
        <div v-if="isShowCahsOn" class="mt-4 lg:mt-0 flex items-center justify-center space-x-3 text-gray99">
          <svg @click="showCashRegisterMoney = !showCashRegisterMoney" v-if="showCashRegisterMoney"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
            class="size-4 cursor-pointer">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
          </svg>
          <svg @click="showCashRegisterMoney = !showCashRegisterMoney" v-else xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 cursor-pointer">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
          </svg>
          <p v-if="this.$page.props.auth?.user?.cash_register_id" class="text-sm flex items-center space-x-2">
            Efectivo en "{{ asignedCashRegister?.name }}":
            <b :class="(localCurrentCash >= asignedCashRegister?.max_cash) && isMaxCashOn ? 'text-red-600' : ''"
              class="ml-2">
              {{ showCashRegisterMoney ? '$' + localCurrentCash?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") :
                '*****' }}
            </b>
            <el-tooltip
              v-if="(localCurrentCash >= asignedCashRegister?.max_cash) && isMaxCashOn && showCashRegisterMoney"
              content="Se llegó al límite de dinero permitido en caja. Es recomendable hacer corte" placement="bottom">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" class="size-4 text-red-600">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
              </svg>
            </el-tooltip>
          </p>
          <p v-else class="text-sm text-red-600">No tienes una caja asignada</p>
        </div>
        <!-- Dropdown -->
        <div class="inline-block border border-primary rounded-full px-4 pt-[3px] mt-3 md:mt-0">
          <el-col :span="3">
            <el-dropdown trigger="click">
              <p class="text-sm text-primary w-44 flex items-center">
                <svg class="mr-2" width="12" height="12" viewBox="0 0 12 12" fill="none"
                  xmlns="http://www.w3.org/2000/svg">
                  <mask id="mask0_9380_424" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="12"
                    height="12">
                    <rect width="12" height="12" fill="#D9D9D9" />
                  </mask>
                  <g mask="url(#mask0_9380_424)">
                    <path
                      d="M2.5 10.5C2.225 10.5 1.98958 10.4021 1.79375 10.2063C1.59792 10.0104 1.5 9.775 1.5 9.5V2.5C1.5 2.225 1.59792 1.98958 1.79375 1.79375C1.98958 1.59792 2.225 1.5 2.5 1.5H9.5C9.775 1.5 10.0104 1.59792 10.2063 1.79375C10.4021 1.98958 10.5 2.225 10.5 2.5V9.5C10.5 9.775 10.4021 10.0104 10.2063 10.2063C10.0104 10.4021 9.775 10.5 9.5 10.5H2.5ZM6 8C6.31667 8 6.60417 7.90833 6.8625 7.725C7.12083 7.54167 7.3 7.3 7.4 7H9.5V2.5H2.5V7H4.6C4.7 7.3 4.87917 7.54167 5.1375 7.725C5.39583 7.90833 5.68333 8 6 8Z"
                      fill="#F68C0F" />
                  </g>
                </svg>
                <span>Movimientos de caja</span>
                <i class="fa-solid fa-angle-down text-[10px] ml-2 mt-px"></i>
              </p>
              <template #dropdown>
                <el-dropdown-menu>
                  <el-dropdown-item v-if="$page.props.auth.user.store.plan != 'Plan Básico'"
                    @click="showCashRegisterSelectionModal = true"><i
                      class="fa-solid fa-arrows-rotate text-xs mr-3"></i>Cambiar de caja</el-dropdown-item>
                  <el-dropdown-item :disabled="!asignedCashRegister"
                    @click="cashRegisterModal = true; form.cashRegisterMovementType = 'Ingreso'"><i
                      class="fa-solid fa-circle-arrow-up text-xs mr-3"></i>Ingresar efectivo</el-dropdown-item>
                  <el-dropdown-item :disabled="!asignedCashRegister"
                    @click="cashRegisterModal = true; form.cashRegisterMovementType = 'Retiro'"><i
                      class="fa-solid fa-circle-arrow-down text-xs mr-3"></i>Retirar efectivo</el-dropdown-item>
                  <el-dropdown-item :disabled="!asignedCashRegister" @click="handleCashCut"><i
                      class="fa-solid fa-cash-register text-xs mr-3"></i>Hacer
                    corte</el-dropdown-item>
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
              <!-- <div class="m-4 flex justify-between items-center">
                <div class="flex items-center space-x-3 w-full md:w-1/2">
                  <p class="font-bold">Cliente</p>
                  <el-tooltip content="Si no es necesario agregar un cliente específico, no selecciones ninguna opción"
                    placement="top">
                    <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center px-1">
                      <i class="fa-solid fa-info text-primary text-[7px]"></i>
                    </div>
                  </el-tooltip>
                  <el-select v-model="editableTabs[this.editableTabsValue - 1].client_id" clearable filterable
                    placeholder="Seleccione" no-data-text="No hay opciones registradas"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="client in clients" :key="client" :label="client.name" :value="client.id" />
                  </el-select>
                  <button @click="showClientFormModal = true" type="button"
                    class="rounded-full  border-primary size-5 flex items-center justify-center text-primary text-lg">
                    <i class="fa-solid fa-circle-plus"></i>
                  </button>
                </div>
              </div> -->
              <el-tab-pane v-for="tab in editableTabs" :key="tab.name" :label="tab.title" :name="tab.name">
                <el-popconfirm v-if="tab.saleProducts.length" confirm-button-text="Si" cancel-button-text="No"
                  icon-color="#C30303" title="Se eliminará todo el registro de productos ¿Deseas continuar?"
                  @confirm="clearTab()">
                  <template #reference>
                    <ThirthButton class="!text-[#F80505] !border-[#F80505] !py-1 !px-2 mb-2"><i
                        class="fa-regular fa-trash-can mr-2"></i> Limpiar registro</ThirthButton>
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
              class="absolute mt-1 bg-white border border-gray-300 rounded shadow-lg w-full z-50 max-h-48 overflow-auto">
              <ul v-if="productsFound?.length > 0 && !loading">
                <li @click="selectProductFromList(product)" v-for="(product, index) in productsFound" :key="index"
                  class="hover:bg-gray-200 cursor-pointer text-xs px-3 py-2 flex space-x-2">
                  <span class="w-4/5">{{ product.name }}</span>
                  <span v-if="product.code" class="w-1/5 text-[10px] text-gray99">
                    {{ product.code }}
                  </span>
                </li>
              </ul>
              <p v-else-if="!loading" class="text-center text-sm text-gray-600 px-5 py-2">
                No se encontraron coincidencias
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
              <figure class="h-36">
                <img v-if="productFoundSelected.imageUrl" :src="productFoundSelected.imageUrl"
                  :alt="productFoundSelected.name" class="object-contain h-36 mx-auto">
                <p v-else class="text-center text-xs text-gray99 pt-10 px-8">Este producto no tiene imagen registrada
                </p>
              </figure>
              <div class="flex justify-between items-center mt-2 mb-4">
                <p class="font-bold">{{ productFoundSelected.name }}</p>
                <p class="text-[#5FCB1F]">${{ productFoundSelected.public_price }}</p>
              </div>
              <div class="flex justify-between items-center">
                <p class="text-gray99">Cantidad</p>
                <el-input-number v-if="isInventoryOn" v-model="quantity" :min="0"
                  :max="productFoundSelected.current_stock" :precision="2" />
                <el-input-number v-else v-model="quantity" :min="0" :precision="2" />
              </div>
              <div class="text-center mt-7">
                <PrimaryButton @click="addSaleProduct(productFoundSelected); productFoundSelected = null"
                  class="!rounded-full !px-24" :disabled="quantity == 0">
                  Agregar
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
            <div v-if="!editableTabs[editableTabsValue - 1]?.paying">
              <!-- <div v-if="isDiscountOn" class="flex items-center justify-between text-lg mx-5">
                <p>Subtotal</p>
                <p class="text-gray-99">$ <strong class="ml-3">{{
                  calculateTotal().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") }}</strong></p>
              </div> -->
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
                  :disabled="editableTabs[this.editableTabsValue - 1]?.saleProducts?.length == 0 || (calculateTotal() - editableTabs[this.editableTabsValue - 1].discount) < 0 || !this.$page.props.auth?.user?.cash_register_id"
                  class="!rounded-full !px-24 !bg-[#5FCB1F] disabled:!bg-[#999999]">Cobrar</PrimaryButton>
                <p v-if="!this.$page.props.auth?.user?.cash_register_id" class="text-xs text-red-600 mt-1">
                  Para cobrar asigna una caja registradora <span @click="cashRegisterModal = true"
                    class="underline cursor-pointer text-primary">asignar una</span>
                </p>
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
                class="text-xs text-primary text-center mb-3">
                La cantidad es insuficiente. Por favor, ingrese una cantidad igual o mayor al total de compra.
              </p>
              <div class="flex space-x-2 justify-end">
                <CancelButton @click="editableTabs[this.editableTabsValue - 1].paying = false">Cancelar</CancelButton>
                <PrimaryButton :disabled="storeProcessing" @click="store" class="!rounded-full">
                  <i v-if="storeProcessing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                  Aceptar
                </PrimaryButton>
                <!-- <PrimaryButton
                  :disabled="storeProcessing || (calculateTotal() - editableTabs[this.editableTabsValue - 1].discount) > editableTabs[this.editableTabsValue - 1]?.moneyReceived"
                  @click="store" class="!rounded-full">Aceptar</PrimaryButton> boton con validaciones de deshabilitar-->
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>

    <!-- -------------- Modal selección de caja starts----------------------- -->
    <Modal :show="showCashRegisterSelectionModal" @close="showCashRegisterSelectionModal = false">
      <div class="py-4 px-7 relative">
        <i @click="showCashRegisterSelectionModal = false"
          class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

        <h1 class="font-bold mt-2">Elegir caja</h1>
        <p class="text-gray99">Selecciona la caja de trabajo</p>

        <section class="flex justify-between space-x-7 w-2/3 mx-auto text-sm mt-5">
          <div class="flex flex-col items-center" v-for="(item, index) in cash_registers" :key="item">
            <input :disabled="!item.is_active" v-model="selectedCashRegisterId" :id="'suscription-' + index"
              :aria-describedby="'suscription-text-' + index" type="radio" :value="item.id"
              class="size-3 text-primary focus:ring-0 disabled:cursor-not-allowed">
            <label :class="!item.is_active ? 'text-grayD9 cursor-not-allowed' : 'text-[#373737]'"
              :for="'suscription-' + index">{{ item.name }}</label>
            <label :class="!item.is_active ? 'text-grayD9 cursor-not-allowed' : 'text-[#373737]'"
              :for="'suscription-' + index" class="mt-4">
              <svg width="100" height="66" viewBox="0 0 88 54" fill="none" xmlns="http://www.w3.org/2000/svg"
                stroke="currentColor">
                <path
                  d="M67.8222 13.681L69.8833 27.0754L69.9784 27.6933M67.8222 13.681H71.6168M67.8222 13.681H62.1173M71.9444 40.4698H1M71.9444 40.4698L71.1465 35.2848M71.9444 40.4698L69.2213 49.3273M1 40.4698L2.9526 13.681H30.289H38.5333H58.1012M1 40.4698L4.25433 51.0556H6.42388M71.1465 35.2848L80.6226 35.6911M71.1465 35.2848L69.9784 27.6933M81.9243 35.7469L86.2634 35.9329L87 27.6933M81.9243 35.7469L79.5378 50.4075H68.8893M81.9243 35.7469L80.6226 35.6911M68.8893 50.4075L68.69 51.0556H67.1713M68.8893 50.4075L69.2213 49.3273M69.2213 49.3273H78.453L80.6226 35.6911M6.42388 51.0556L7.07475 53H66.3035L67.1713 51.0556M6.42388 51.0556H67.1713M71.6631 15.6253C71.7742 17.6943 72.0838 19.7969 72.5952 22.3225H81.2734C80.8859 19.771 80.6341 17.66 80.5452 15.6253M71.6631 15.6253H69.8833L71.5104 22.5385H72.5952M71.6631 15.6253C71.7276 17.7194 72.0007 19.2862 72.5952 22.5385M71.6631 15.6253L72.5952 22.5385M71.6631 15.6253C71.6283 14.9779 71.6129 14.3337 71.6168 13.681M72.5952 22.5385H82.3582L80.8395 15.6253H80.5452M80.5452 15.6253C80.416 12.6681 80.6308 9.87213 81.2734 6.11961L80.6226 6.76773L80.1887 6.11961L79.9717 6.55169L79.5378 6.11961L79.1039 6.55169L78.8869 6.11961L78.453 6.55169L78.1055 5.90357L77.5852 6.76773L77.1513 5.90357L76.7174 6.55169L76.5004 5.90357L76.0665 6.55169L75.6326 6.11961L75.1987 6.55169L74.7648 5.90357L74.3309 6.55169L74.1139 5.90357L73.68 6.55169L73.2461 5.90357L72.8122 6.55169L72.3783 5.90357C71.8862 8.95569 71.6307 11.3723 71.6168 13.681M87 27.6933H69.9784M87 27.6933L81.2734 13.681H80.5452M61.9312 10.5057H60.6907H58.1012M61.9312 10.5057H64.7043L64.9213 1L45.8292 1.43208L46.2632 10.5057H58.1012M61.9312 10.5057L62.1173 13.681M62.1173 13.681H58.1012M58.1012 10.5057V13.681M4.90519 14.7612H66.7374L70.4257 39.1735H2.9526L4.90519 14.7612ZM6.42388 16.0574H11.6308L11.1969 20.5942H5.98997L6.42388 16.0574ZM12.2817 16.0574H17.4886L17.2716 20.5942H11.8478L12.2817 16.0574ZM18.1395 16.0574H23.3464L23.1294 20.5942H17.9225L18.1395 16.0574ZM23.9973 16.0574H29.2042V20.5942H23.7803L23.9973 16.0574ZM29.855 16.0574H35.062V20.5942H29.855V16.0574ZM35.7128 16.0574H40.9198V20.5942H35.7128V16.0574ZM4.68824 32.2603H16.4038L16.1869 37.4452H4.25433L4.68824 32.2603ZM29.6381 32.2603H41.5706V37.4452H29.6381V32.2603ZM17.2716 32.2603H22.6955V37.4452H17.0547L17.2716 32.2603ZM23.5633 32.2603H28.9872V37.4452H23.5633V32.2603ZM48.2962 16.0574H53.7201L54.371 20.5942H48.9471L48.2962 16.0574ZM54.371 16.0574L55.0218 20.5942H60.2288L59.5779 16.0574H54.371ZM60.4457 16.0574L61.0966 20.5942H66.3035L65.6527 16.0574H60.4457ZM46.2632 1.86415L46.6971 10.0736H64.2704L64.4874 1.43208L46.2632 1.86415ZM47.3479 2.94435V9.20947H63.6196V2.94435H47.3479ZM5.98997 21.2423H11.1969L10.763 25.7791H5.55606L5.98997 21.2423ZM11.8478 21.2423H17.0547L16.8377 25.7791H11.4139L11.8478 21.2423ZM17.9225 21.2423H23.1294L22.9125 25.7791H17.7056L17.9225 21.2423ZM23.7803 21.2423H28.9872V25.7791H23.5633L23.7803 21.2423ZM29.855 21.2423H35.062V25.7791H29.855V21.2423ZM35.9298 21.2423H41.1367V25.7791H35.9298V21.2423ZM5.55606 26.6433H10.763L10.3291 31.1801H5.12215L5.55606 26.6433ZM11.6308 26.6433H16.8377L16.6208 31.1801H11.1969L11.6308 26.6433ZM17.7056 26.6433H22.9125L22.6955 31.1801H17.4886L17.7056 26.6433ZM23.7803 26.6433H28.9872V31.1801H23.5633L23.7803 26.6433ZM29.855 26.6433H35.062V31.1801H29.855V26.6433ZM36.3637 26.6433H41.5706V31.1801H36.3637V26.6433ZM48.9471 21.4584H54.371L54.8049 26.2112H49.381L48.9471 21.4584ZM55.0218 21.4584H60.2288L60.8796 26.2112H55.4558L55.0218 21.4584ZM61.0966 21.4584H66.3035L67.1713 26.2112H61.7475L61.0966 21.4584ZM49.381 26.8593H54.8049L55.2388 31.8282H49.8149L49.381 26.8593ZM55.6727 26.8593H60.8796L61.5305 31.8282H56.1066L55.6727 26.8593ZM61.7475 26.8593H67.1713L67.8222 31.8282H62.1814L61.7475 26.8593ZM49.8149 32.4763H61.7475L62.3983 37.4452H50.2488L49.8149 32.4763ZM62.3983 32.4763H67.8222L68.69 37.4452H63.0492L62.3983 32.4763Z"
                  stroke="#373737" stroke-width="0.4" />
              </svg>
            </label>
            <p v-if="!item.is_active" class="text-sm text-red-400">Deshabilitada</p>
          </div>
        </section>

        <div class="flex justify-between space-x-1 pt-2 pb-1 py-2 mt-5 col-span-full">

          <p v-if="cash_registers.length == 1 && $page.props.auth.user.store.plan != 'Plan Básico'" class="text-gray99">
            Por ahora solo tienes una caja. <span @click="$inertia.get(route('cash-registers.create'))"
              class="text-primary cursor-pointer hover:underline ml-1">Crear caja</span></p>
              
          <span v-else></span>
          <PrimaryButton :disabled="!selectedCashRegisterId" @click="asignCashRegister">Confirmar</PrimaryButton>
        </div>
      </div>
    </Modal>
    <!-- --------------------------- Modal selección de caja ends ------------------------------------ -->

    <!-- -------------- Modal Ingreso o retiro de dinero en caja starts----------------------- -->
    <Modal :show="cashRegisterModal" @close="cashRegisterModal = false; form.reset">
      <div class="p-4 relative">
        <i @click="cashRegisterModal = false"
          class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

        <form class="mt-5 mb-2 md:grid grid-cols-2 gap-3" @submit.prevent="storeCashRegisterMovement">
          <h2 v-if="form.cashRegisterMovementType === 'Ingreso'" class="font-bold col-span-full">Ingresar efectivo a
            caja
          </h2>
          <h2 v-if="form.cashRegisterMovementType === 'Retiro'" class="font-bold col-span-full">Retirar efectivo a caja
          </h2>

          <div class="mt-2">
            <InputLabel v-if="form.cashRegisterMovementType === 'Ingreso'" value="Monto a ingresar *"
              class="ml-3 mb-1 text-sm" />
            <InputLabel v-if="form.cashRegisterMovementType === 'Retiro'" value="Monto a retirar *"
              class="ml-3 mb-1 text-sm" />
            <el-input v-model="form.registerAmount" type="text" placeholder="ingresa el monto"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')">
              <template #prefix>
                <i class="fa-solid fa-dollar-sign"></i>
              </template>
            </el-input>
            <p class="text-red-500 text-xs"
              v-if="form.cashRegisterMovementType === 'Retiro' && form.registerAmount > localCurrentCash">
              *El monto no debe exceder el dinero actual de tu caja (${{ localCurrentCash }})
            </p>
            <InputError :message="form.errors.registerAmount" />
          </div>

          <div class="col-span-full mt-2">
            <InputLabel value="Motivo (opcional)" class="text-sm ml-2" />
            <el-input v-model="form.registerNotes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
              placeholder="Escribe tus notas" :maxlength="255" show-word-limit clearable />
          </div>

          <div class="flex justify-end space-x-1 pt-2 pb-1 py-2 col-span-full">
            <CancelButton @click="cashRegisterModal = false">Cancelar</CancelButton>
            <PrimaryButton
              :disabled="!form.registerAmount || form.processing || (form.cashRegisterMovementType === 'Retiro' && form.registerAmount > asignedCashRegister?.current_cash)">
              Confirmar</PrimaryButton>
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
                <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
              </div>
              <p v-else>${{ (asignedCashRegister?.started_cash + cutForm.totalStoreSale + cutForm.totalOnlineSale +
                cutForm.totalCashMovements)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
              <el-input @input="difference()" v-model="cutForm.counted_cash" type="text" placeholder="0.00"
                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                :parser="(value) => value.replace(/[^\d.]/g, '')" class="!w-24 !h-6">
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
                ${{ (cutForm.difference * -1)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
              </p>
              <p v-if="cutForm.counted_cash" :class="{
                'text-green-500 bg-green-100': (cutForm.difference) === 0,
                'text-blue-500 bg-blue-100': (cutForm.difference) < 0,
                'text-red-500 bg-red-100': (cutForm.difference) > 0
              }" class="rounded-full text-xs inline py-[2px] px-2">
                <!-- Icono de proveedor de verificación si la diferencia es 0 -->
                <i v-if="(cutForm.difference) === 0" class="fa-solid fa-check mr-1"></i>
                <!-- Icono de sobrante en caja si la diferencia es negativa -->
                <i v-else-if="(cutForm.difference) < 0" class="fa-solid fa-plus mr-1"></i>
                <!-- Icono de faltante de efectivo si la diferencia es positiva -->
                <i v-else class="fa-solid fa-xmark mr-1"></i>
                <!-- Muestra el mensaje correspondiente -->
                {{ (cutForm.difference) === 0 ? 'Todo bien' : ((cutForm.difference) < 0 ? 'Sobrante en caja'
                  : 'Faltante de efectivo') }} </p>
            </div>
          </section>

          <div v-if="cutForm.counted_cash" class="flex items-center space-x-3 mt-3">
            <div class="w-full">
              <InputLabel value="Monto a retirar de caja" class="text-sm ml-2" />
              <el-input v-model="cutForm.withdrawn_cash" type="number" step="0.01" class="!w-1/2 !h-6"
                placeholder="0.00">
                <template #prefix>
                  <i class="fa-solid fa-dollar-sign"></i>
                </template>
              </el-input>
              <InputError :message="cutForm.errors.withdrawn_cash" />
            </div>
            <p v-if="cutForm.withdrawn_cash" class="w-full mt-3 text-sm font-bold">Efectivo que dejarás en caja: ${{
              (cutForm.counted_cash - cutForm.withdrawn_cash)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
          </div>
          <div class="col-span-full mt-2">
            <InputLabel value="Comentarios (opcional)" class="text-sm ml-2" />
            <el-input v-model="cutForm.notes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
              placeholder="Escribe aquí cualquier comentario relacionado al corte" :maxlength="255" show-word-limit
              clearable />
          </div>

          <div class="flex justify-end space-x-1 pt-2 pb-1 py-2 col-span-full">
            <CancelButton @click="cashCutModal = false; cutForm.reset()">Cancelar</CancelButton>
            <PrimaryButton :disabled="!cutForm.counted_cash || cutForm.processing || (cutForm.totalCashMovements == 0 && cutForm.totalStoreSale == 0)">Hacer corte</PrimaryButton>
          </div>
          <p v-if="cutForm.totalCashMovements == 0 && cutForm.totalStoreSale == 0" 
            class="text-xs text-red-600 text-right">*Para hacer corte es necesario que haya almenos una venta o movimiento de caja registrado</p>
        </form>
      </div>
    </Modal>
    <!-- --------------------------- Modal corte de caja ends ------------------------------------ -->

    <!-- client form -->
    <DialogModal :show="showClientFormModal" @close="showClientFormModal = false; resetClientForm()">
      <template #title> Agregar cliente </template>
      <template #content>
        <form @submit.prevent="storeClient" class="md:grid grid-cols-2 gap-x-3">
          <div class="mt-3">
            <InputLabel value="Nombre*" class="ml-3 mb-1" />
            <el-input v-model="clientForm.name" placeholder="Escribe el nombre del cliente" :maxlength="100" clearable />
            <InputError :message="clientForm.errors.name" />
          </div>
          <div class="mt-3">
            <InputLabel class="mb-1 ml-2" value="Teléfono *" />
            <el-input v-model="clientForm.phone"
            :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
            :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
            placeholder="Escribe el número de teléfono" />
            <InputError :message="clientForm.errors.phone" />
          </div>
          <div class="mt-3 col-span-full">
            <InputLabel value="RFC (opcional)" class="ml-3 mb-1" />
            <el-input v-model="clientForm.rfc" placeholder="Escribe el RFC en caso de tenerlo" :maxlength="100" clearable />
            <InputError :message="clientForm.errors.rfc" />
          </div>
        </form>
      </template>
      <template #footer>
        <div class="flex items-center space-x-2">
          <CancelButton @click="showClientFormModal = false; resetClientForm()" :disabled="clientForm.processing">Cancelar</CancelButton>
          <PrimaryButton @click="storeClient()" :disabled="clientForm.processing">Crear</PrimaryButton>
        </div>
      </template>
    </DialogModal>

    <!-- Modal para advertir que se ha excedido del dinero permitido en caja -->
    <ConfirmationModal :show="showLimitCashModal" @close="showLimitCashModal = false">
      <template #title>
        <h1>Se ha llegado al límite de dinero permitido en caja</h1>
      </template>
      <template #content>
        <p>
          ¿Deseas realizar corte para no exceder el límite de dinero permitido en caja?
        </p>
      </template>
      <template #footer>
        <div class="flex items-center space-x-1">
          <CancelButton @click="showLimitCashModal = false">Cancelar</CancelButton>
          <PrimaryButton @click="showLimitCashModal = false; handleCashCut()">Hacer corte</PrimaryButton>
        </div>
      </template>
    </ConfirmationModal>
    <!-- Modal para advertir que se ha excedido del dinero permitido en caja -->
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from "@/Components/DialogModal.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import SaleTable from '@/Components/MyComponents/Sale/SaleTable.vue';
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/vue3";
import axios from 'axios';
import { format } from 'date-fns';
import { getItemByPartialAttributes, getItemByAttributes, addOrUpdateBatchOfItems, syncIDBProducts, getAll } from '@/dbService.js';

export default {
  data() {
    const form = useForm({
      cashRegisterMovementType: null, //que tipo de movimiento es
      registerAmount: null, //Dinero ingresado o sacado de caja
      registerNotes: null, //notas al entrar o sacar dinero
    });

    const clientForm = useForm({
      name: null,
      rfc: null,
      phone: null,
    });

    const cutForm = useForm({
      counted_cash: null,
      difference: null,
      notes: null,
      totalStoreSale: null, //dinero esperado de ventas hechas para hacer corte
      totalOnlineSale: null, //dinero esperado de ventas en linea para hacer corte
      totalCashMovements: null, //dinero de movimientos de caja para hacer corte
      withdrawn_cash: null, //dinero retirado de caja tras haber hecho el corte
    });

    return {
      form,
      cutForm,
      clientForm,

      selectedCashRegisterId: this.$page.props.auth.user.cash_register_id, //id de la caja registradora seleccionada
      asignedCashRegister: this.$page.props.auth.user.cash_register_id, // caja registradora asignada a la venta de el usuario logueado
      showCashRegisterSelectionModal: false, //muestra u oculta el modal de selección de caja

      // conexion a internet
      isOnline: navigator.onLine, // Verificar el estado de conexión al cargar el componente
      syncingData: false,
      syncingIDB: false,

      showLimitCashModal: false, //muestra u oculta el modal de excedencia de dinero permitido en caja
      showClientFormModal: false, //muestra u oculta el modal de creación de cliente
      showCashRegisterMoney: true, //muestra u oculta el dinero de caja
      localCurrentCash: 0, //dinero de caja local
      cashRegisterModal: false, //muestra el modal para ingresar o retirar dinero de la caja
      cashCutModal: false, //muestra el modal para el corte de caja
      // inventario de codigos activado
      isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
      // mostrar dinero en caja activado
      isShowCahsOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Mostrar dinero en caja')?.value,
      // descuentos activados
      isDiscountOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Hacer descuentos')?.value,
      // escaneo de codigos activado
      isScanOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Escanear productos')?.value,
      // monto maximo en caja activado
      isMaxCashOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Aviso de monto máximo en caja')?.value,

      storeProcessing: false, //cargando store de venta
      scanning: false, //cargando la busqueda de productos por escaner
      loading: false, //cargando la busqueda de productos
      cutLoading: false, //cargando monto total esperado para corte
      scannerQuery: null, //input para scanear el codigo de producto
      //buscador
      searchQuery: null,
      searchFocus: false,
      productsFound: null,
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
          client_id: null,
        },
        {
          title: "Registro 2",
          name: "2",
          saleProducts: [],
          paying: false,
          discount: 0,
          moneyReceived: null,
          client_id: null,
        },
        {
          title: "Registro 3",
          name: "3",
          saleProducts: [],
          paying: false,
          discount: 0,
          moneyReceived: null,
          client_id: null,
        },
      ],
    }
  },
  components: {
    AppLayout,
    ConfirmationModal,
    PrimaryButton,
    ThirthButton,
    CancelButton,
    DialogModal,
    InputLabel,
    InputError,
    SaleTable,
    Modal
  },
  props: {
    products: Array,
    cash_registers: Array,
    clients: Array
  },
  methods: {
    storeClient() {
      this.clientForm.post(route('clients.store'), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha creado un nuevo cliente",
            type: "success",
          });
          this.showClientFormModal = false;
        },
      });
    },
    resetClientForm() {
      this.clientForm.reset();
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() < today.getTime();
    },
    sumCashForSale() {
      this.localCurrentCash += this.editableTabs[this.editableTabsValue - 1]?.saleProducts.reduce((accum, item) => {
        return accum += item.product.public_price * item.quantity;
      }, 0);
    },
    selectProductFromList(product) {
      // crear link virtual de imagen blob si es que tiene imagen el producto
      if (product.image && !product.imageUrl) {
        const imageUrl = URL.createObjectURL(product.image);
        product = { ...product, imageUrl };
      }

      this.productFoundSelected = product;
      this.searchQuery = null;

      // revisar si hay stock del producto para dejar 1 como default a vender
      if ((product.current_stock && this.isInventoryOn) || !this.isInventoryOn) {
        this.quantity = 1;
      } else {
        this.quantity = 0;
      }
    },
    async updateCurrentStockInIndexedDB() {
      // Obtener la pestaña actual y sus productos
      const saleProducts = this.editableTabs[this.editableTabsValue - 1]?.saleProducts;

      // Asegurarse de que existan productos en la pestaña
      if (!saleProducts) {
        return;
      }

      // Mapear los productos para actualizar su stock
      const products = await Promise.all(saleProducts.map(async (item) => {
        // Obtener productos por código
        let foundProducts = await getItemByAttributes('products', { code: item.product.code });

        // Verificar si se encontró el producto
        if (foundProducts.length > 0) {
          // Actualizar el stock
          foundProducts[0].current_stock = item.product.current_stock <= item.quantity ? 0 : item.product.current_stock - item.quantity;
          return foundProducts[0];
        }

        // Manejar el caso donde no se encuentre el producto
        return null;
      }));

      // Filtrar productos que no fueron encontrados
      const validProducts = products.filter(product => product !== null);

      // Actualizar los productos en IndexedDB
      await addOrUpdateBatchOfItems('products', validProducts);
    },
    async store() {
      if (this.storeProcessing) return;

      this.storeProcessing = true;

      if (this.isOnline) {
        await this.processOnlineSale();
      } else {
        this.processOfflineSale();
      }

      this.storeProcessing = false;
    },
    async processOnlineSale() {
      try {
        const response = await axios.post(route('sales.store', { cash_register_id: this.asignedCashRegister?.id }), {
          data: {
            saleProducts: this.editableTabs[this.editableTabsValue - 1]?.saleProducts
          }
        });
        if (response.status === 200) {
          if (this.isInventoryOn) {
            this.updateCurrentStockInIndexedDB();
          }
          this.clearTab();
          this.fetchCashRegister();

          this.$notify({
            title: "Correcto",
            message: "Se ha registrado la venta",
            type: "success",
          });

          localStorage.setItem('pendentProcess', false);
        }
      } catch (error) {
        console.error(error);
      }
    },
    processOfflineSale() {
      this.saveToLocalStorage();
      if (this.isInventoryOn) {
        this.updateCurrentStockInIndexedDB();
      }
      this.sumCashForSale();
      this.clearTab();
    },
    async asignCashRegister() {
      try {
        const response = await axios.put(route('cash-registers.asign', [this.$page.props.auth?.user.id, this.selectedCashRegisterId]));
        if (response.status === 200) {
          this.$notify({
            title: "Correcto",
            text: "Se te asignó una caja con éxito!",
            type: "success",
          });
          location.reload();
        }
      } catch (error) {
        console.log(error);
      }
    },
    storeCashRegisterMovement() {
      this.form.post(route('cash-register-movements.store', { cash_register_id: this.asignedCashRegister?.id }), {
        onSuccess: () => {
          this.$notify({
            title: "Correcto",
            message: "Se ha registrado el movimiento de caja",
            type: "success",
          });
          this.cashRegisterModal = false;
          this.form.reset();
          this.fetchCashRegister();
        },
      });
    },
    storeCashCut() {
      this.cutForm.post(route('cash-cuts.store', { cash_register_id: this.asignedCashRegister?.id }), {
        onSuccess: () => {
          this.$notify({
            title: "Correcto",
            message: "Se ha realizado el corte de caja",
            type: "success",
          });
          this.cashCutModal = false;
          this.fetchCashRegister();
          this.cutForm.reset();
        },
      });
    },
    difference() {
      //  Se hace la resta al reves para cambiar el signo y si sobra sea positivo y si falta negativo
      this.cutForm.difference = (this.cutForm.totalStoreSale + this.cutForm.totalOnlineSale + this.cutForm.totalCashMovements + this.asignedCashRegister?.started_cash) - this.cutForm.counted_cash
    },
    deleteProduct(productId) {
      const indexToDelete = this.editableTabs[this.editableTabsValue - 1].saleProducts.findIndex(sale => sale.product.id === productId);
      this.editableTabs[this.editableTabsValue - 1].saleProducts.splice(indexToDelete, 1);
    },
    async fetchCashRegister() {
      try {
        const response = await axios.get(route('cash-registers.fetch-cash-register', this.asignedCashRegister?.id));
        if (response.status === 200) {
          this.localCurrentCash = response.data.item.current_cash;
          if ((this.localCurrentCash >= this.asignedCashRegister?.max_cash) && this.isMaxCashOn && this.isOnline) {
            this.showLimitCashModal = true;
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
    async searchProducts() {
      try {
        this.productsFound = await getItemByPartialAttributes('products', { name: this.searchQuery, code: this.searchQuery });
      } catch (error) {
        console.log(error);
      }
    },
    async fetchTotalSaleForCashCut() {
      try {
        const response = await axios.get(route('cash-cuts.fetch-total-sales-for-cash-cut', this.asignedCashRegister?.id));
        if (response.status === 200) {
          this.cutForm.totalStoreSale = response.data.store_sales; //ventas en tienda
          this.cutForm.totalOnlineSale = response.data.online_sales; // ventas en linea
        }
      } catch (error) {
        console.log(error);
      }
    },
    async fetchTotalCashMovements() {
      try {
        this.cutLoading = true;
        const response = await axios.get(route('cash-register-movements.fetch-total-cash-movements', this.asignedCashRegister?.id));
        if (response.status === 200) {
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
      }, 100);
    },
    async getProductByCode() {
      this.scanning = true;

      let foundProducts = await getItemByAttributes('products', { code: this.scannerQuery });
      let productScaned = foundProducts[0];

      // si no se encontró el producto escaneado aparece un mensaje y no busca en la bd para no tardar más
      if (productScaned != null) {
        // agregar la imagen al producto si es que no la tiene
        if (productScaned.image && !productScaned.imageUrl) {
          const imageUrl = URL.createObjectURL(productScaned.image);
          productScaned = { ...productScaned, imageUrl };
        }

        this.addSaleProduct(productScaned);
      } else {
        this.$notify({
          title: "Producto no encontrado",
          message: "El producto escaneado no esta registrado en la base de datos",
          type: "warning"
        });
        this.scannerQuery = null;
        this.scanning = false;
      }
    },
    addSaleProduct(product) {
      //revisa si el producto a agregar ya esta dentro del arreglo
      const existingIndex = this.editableTabs[this.editableTabsValue - 1].saleProducts.findIndex(sale => {
        return sale.product.id == product.id;
      });
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

      // indicar al navegador mediante el local storage que hay proceso pendiente
      const pendentProcess = JSON.parse(localStorage.getItem('pendentProcess'));
      if (!pendentProcess) {
        // guardar el valor en el localStorage
        localStorage.setItem('pendentProcess', true);
      }
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
    handleOnline() {
      const storedData = JSON.parse(localStorage.getItem('sales')) || [];
      this.isOnline = true;
      if (storedData.length) {
        this.syncData();
      }
    },
    handleOffline() {
      this.isOnline = false;
    },
    saveToLocalStorage() {
      // Obtén los datos actuales almacenados en el Local Storage
      let storedData = JSON.parse(localStorage.getItem('sales')) || [];

      const dataToStore = {
        created_at: format(new Date(), 'yyyy-MM-dd HH:mm'),
        saleProducts: this.editableTabs[this.editableTabsValue - 1]?.saleProducts
      };

      // Agrega el nuevo objeto al arreglo
      storedData.push(dataToStore);

      // Vuelve a guardar el arreglo en el Local Storage
      localStorage.setItem('sales', JSON.stringify(storedData));
      this.form.reset();

      this.$notify({
        title: 'Correcto',
        message: 'Se registró la venta en almacenamiento local. Cuando tengas conexión a internet se guardarán en la nube',
        type: 'success'
      });
    },
    async syncData() {
      this.syncingData = true;
      try {
        const localStorageItems = JSON.parse(localStorage.getItem('sales'));
        const response = await axios.post(route('sales.sync-localstorage'), {
          sales: localStorageItems,
        });

        if (response.status === 200) {
          // eliminar datos en almacenamiento local
          localStorage.removeItem('sales');
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.syncingData = false;
      }
    },
  },
  async mounted() {
    // redirigir a los tutoriales si no los ha finalizado
    if (!this.$page.props.auth.user.tutorials_seen) {
        this.$inertia.visit(route('tutorials.index'));
    }
    // sincronizar productos
    const productsInIDB = await getAll('products');
    if (!productsInIDB.length) {
      // mostrar carga solo si recien se estan cargando los productos
      this.syncingIDB = true;
    }
    await syncIDBProducts();
    this.syncingIDB = false;

    //verificar si el usuario tiene una caja asignada
    if (!this.$page.props.auth?.user?.cash_register_id) {
      this.showCashRegisterSelectionModal = true;
    } else {
      this.asignedCashRegister = this.cash_registers.find(item => item.id == this.$page.props.auth?.user?.cash_register_id);
      this.localCurrentCash = this.asignedCashRegister?.current_cash;
    }
    if (this.isScanOn) {
      this.$refs.scanInput.focus(); // Enfocar el input de código cuando se abre el modal
    } else {
      this.$refs.searchInput.focus(); // Enfocar el input de buscar producto cuando se abre el modal
    }

    // resetear variable de local storage a false
    localStorage.setItem('pendentProcess', false);

    // Agregar escuchadores de eventos online/offline
    window.addEventListener('online', this.handleOnline);
    window.addEventListener('offline', this.handleOffline);
  },
  beforeUnmount() {
    // Eliminar los escuchadores de eventos al desmontar el componente
    window.removeEventListener('online', this.handleOnline);
    window.removeEventListener('offline', this.handleOffline);
  },
}
</script>