<template>
  <!-- vista desktop -->
  <div v-if="saleProducts?.length" class="w-full mx-auto text-[11px] lg:text-sm over hidden md:block">
    <div class="text-center lg:text-base flex items-center space-x-4 mb-3 border-b border-primary">
      <div class="font-bold pb-3 pl-2 text-left w-[45%]">Producto</div>
      <div class="font-bold pb-3 text-left w-[15%]">Precio</div>
      <div class="font-bold pb-3 text-left w-[20%]">Cantidad</div>
      <div class="font-bold pb-3 text-left w-[15%]">Importe</div>
      <div class="font-bold pb-3 text-left w-[5%]"></div>
    </div>
    <div class="overflow-auto h-[500px]">
      <div v-for="(sale, index) in saleProducts" :key="index"
        class="mb-2 flex items-center space-x-4 border rounded-full relative">
        <div class="grid grid-cols-2 items-center min-h-14 w-[45%]">
          <img class="mx-auto h-14 object-contain" v-if="sale.product.imageUrl" :src="sale.product.imageUrl"
            :alt="sale.product.name">
          <div v-else
            class="size-10 mx-auto bg-white border border-grayD9 text-gray99 rounded-md text-sm flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-4">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
          </div>
          <div class="text-sm">
            <p class="text-xs">Categoria: {{ sale.product.additional?.category }}</p>
            <p class="font-bold">{{ sale.product.name }}</p>
            <span v-if="$page.props.auth.user.store.type == 'Boutique / Tienda de Ropa / Zapatería'"
              class="text-gray99">
              ({{ sale.product.additional?.name }})
            </span>
          </div>
        </div>
        <div :class="editMode !== null ? 'w-[35%]' : 'w-[15%]'" class="text-lg flex items-center">
          <template v-if="editMode !== index">
            ${{ sale.product.public_price }}
            <!-- Condicional en el boton depende de la configuracion seleccionada para no poder editar precio -->
            <button v-if="isDiscountOn && $page.props.auth.user.permissions.includes('Editar precios')"
              @click.stop="startEditing(sale, index)"
              class="flex items-center justify-center text-primary bg-gray-200 size-5 rounded-full ml-2 mr-1">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-[14px]">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
              </svg>
            </button>
          </template>
          <template v-else-if="editMode == index">
            <div class="flex items-center space-x-2">
              <el-input v-model="editedPrice" @keyup.enter="handleChangePrice(sale)" type="number" step="0.01">
                <template #prefix>
                  <i class="fa-solid fa-dollar-sign"></i>
                </template>
              </el-input>
              <button @click="handleChangePrice(sale)"
                class="flex items-center justify-center rounded-full size-5 bg-primary flex-shrink-0"><i
                  class="fa-solid fa-check text-white text-[10px]"></i></button>
              <button @click="editMode = false"
                class="flex items-center justify-center rounded-full size-5 bg-[#EDEDED] flex-shrink-0"><i
                  class="fa-solid fa-x mark text-black text-[10px]"></i></button>
            </div>
          </template>
        </div>
        <div class="w-[20%]">
          <el-input-number v-if="isInventoryOn" v-model="sale.quantity" :min="0" size="small"
            :max="sale.product.current_stock" :precision="2" />
          <el-input-number v-else v-model="sale.quantity" :min="0" :precision="2" size="small" />
        </div>
        <div class="text-[#5FCB1F] font-bold w-[15%] text-lg">${{ (sale.product.public_price *
          sale.quantity).toLocaleString('en-US', {
            minimumFractionDigits: 2
          }) }}</div>
        <div class="w-[5%] text-right">
          <el-popconfirm v-if="canDelete" confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303"
            title="¿Continuar?" @confirm="deleteItem(sale.product.id)">
            <template #reference>
              <i
                class="fa-regular fa-trash-can mr-2 text-primary cursor-pointer p-2 hover:bg-gray-100 rounded-full"></i>
            </template>
          </el-popconfirm>
        </div>
      </div>
    </div>
  </div>

  <!-- vista movil -->
  <div :class="saleProducts.length ? 'min-h-[230px]' : 'min-h-[40px]'" class="overflow-y-auto md:hidden text-[11px]">
    <div v-for="(sale, index) in saleProducts" :key="index"
      class="mb-2 grid grid-cols-3 gap-2 border rounded-md items-center relative">
      <figure>
        <img class="mx-auto w-3/4 h-24 object-contain" v-if="sale.product.imageUrl" :src="sale.product.imageUrl"
          :alt="sale.product.name">
        <div v-else
          class="size-24 mx-auto bg-white border border-grayD9 text-gray99 rounded-md text-sm flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.8"
            stroke="currentColor" class="size-10">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
          </svg>
        </div>
      </figure>
      <div class="col-span-2 flex flex-col space-y-1 justify-center py-1">
        <div class="text-base">
          <p class="text-xs">Categoria: {{ sale.product.additional?.category }}</p>
          <p class="font-bold">{{ sale.product.name }}</p>
          <span v-if="$page.props.auth.user.store.type == 'Boutique / Tienda de Ropa / Zapatería'" class="text-gray99">
            ({{ sale.product.additional?.name }})
          </span>
        </div>
        <div class="flex items-center space-x-2 text-lg">
          <template v-if="editMode !== index">
            ${{ sale.product.public_price }}
            <!-- Condicional en el boton depende de la configuracion seleccionada para no poder editar precio -->
            <button @click.stop="startEditing(sale, index)"
              class="flex items-center justify-center text-primary bg-gray-200 size-4 rounded-full ml-2 mr-1">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-3">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
              </svg>
            </button>
          </template>
          <template v-else-if="editMode == index">
            <div class="flex items-center space-x-2">
              <div class="w-1/2">
                <el-input v-model="editedPrice" @keyup.enter="handleChangePrice(sale)" type="number" step="0.01">
                  <template #prefix>
                    <i class="fa-solid fa-dollar-sign"></i>
                  </template>
                </el-input>
              </div>
              <button @click="handleChangePrice(sale)"
                class="flex items-center justify-center rounded-full size-5 bg-primary flex-shrink-0"><i
                  class="fa-solid fa-check text-white text-[10px]"></i></button>
              <button @click="editMode = false"
                class="flex items-center justify-center rounded-full size-5 bg-[#EDEDED] flex-shrink-0"><i
                  class="fa-solid fa-x mark text-black text-[10px]"></i></button>
            </div>
          </template>
        </div>
        <el-input-number v-if="isInventoryOn" v-model="sale.quantity" :min="0" :max="sale.product.current_stock"
          :precision="2" />
        <el-input-number v-else v-model="sale.quantity" :min="0" :precision="2" size="small" />
        <div class="text-[#5FCB1F] font-bold text-lg">
          ${{ (sale.product.public_price * sale.quantity).toLocaleString('en-US', {
            minimumFractionDigits: 2
          }) }}</div>
        <div class="self-end text-lg">
          <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?"
            @confirm="deleteItem(sale.product.id)" class="justify-self-end">
            <template #reference>
              <button class="mr-2 text-primary cursor-pointer size-7 hover:bg-gray-100 rounded-full">
                <i class="fa-regular fa-trash-can"></i>
              </button>
            </template>
          </el-popconfirm>
        </div>
      </div>
    </div>
  </div>
  <div class="text-center text-gray-500 text-sm mt-14" v-if="saleProducts.length == 0">
    <p v-if="isScanOn" class="flex items-center justify-center text-gray99 text-sm">
      Escanea un producto para comenzar la venta
      <i class="fa-regular fa-hand-point-up ml-3"></i>
    </p>
    <p v-else class="flex items-center justify-center text-gray99 text-sm">
      Busca un producto para comenzar la venta
      <i class="hidden lg:inline fa-regular fa-hand-point-right ml-3"></i>
      <i class="lg:hidden fa-regular fa-hand-point-down ml-3"></i>
    </p>
  </div>

  <!-- Modal para preguntar si se quiere cambiar el precio definitivamente o solo en esa venta -->
  <ConfirmationModal :show="showChangePriceConfirmation" @close="showChangePriceConfirmation = false">
    <template #title>
      <h1>Confirmar cambio de precio</h1>
    </template>
    <template #content>
      <p>
        ¿Deseas cambiar el precio definitivo del producto o sólo en esta venta?
      </p>
    </template>
    <template #footer>
      <div class="flex items-center space-x-1">
        <CancelButton @click="stopEditing(true)">Sólo en esta venta</CancelButton>
        <PrimaryButton @click="stopEditing(false)">Cambiar precio al producto</PrimaryButton>
      </div>
    </template>
  </ConfirmationModal>
</template>

<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import { syncIDBProducts } from "@/dbService.js";

export default {
  data() {
    return {
      // inventario de codigos activado
      isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
      // escaneo de codigos activado
      isScanOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Escanear productos')?.value,
      // descuentos activados
      isDiscountOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Hacer descuentos')?.value,
      // Permisos de rol actual
      canDelete: this.$page.props.auth.user.rol == 'Administrador',
      // otros
      showChangePriceConfirmation: false, //confirmacion para cambio de precio
      saleProductToEdit: null, //guarda el producto al que se va editar el precio
      quantity: 1,
      editMode: null,
      editedPrice: null
    };
  },
  components: {
    ConfirmationModal,
    PrimaryButton,
    CancelButton
  },
  props: {
    saleProducts: Array
  },
  emits: ['delete-product'],
  methods: {
    handleChangePrice(sale) {
      this.saleProductToEdit = sale;
      this.showChangePriceConfirmation = true;
    },
    deleteItem(productId) {
      this.$emit('delete-product', productId);
    },
    startEditing(sale, index) {
      this.editMode = index;
      this.editedPrice = sale.product.public_price;
    },
    stopEditing(changeJustForThisSale) {
      this.editMode = null;

      //si se cambia el precio sólo para la venta
      if (changeJustForThisSale) {
        // Actualizamos el precio en el objeto de venta directamente.
        this.saleProductToEdit.originalPrice = this.saleProductToEdit.product.public_price; //agrega bandera para indicar que se le cambió el precio solo para esa venta
        this.saleProductToEdit.product.public_price = parseFloat(this.editedPrice);
        this.showChangePriceConfirmation = false;

      } else { //se cambia el precio definitivo al producto.
        //si es local manda al controlador de locales 
        if (this.saleProductToEdit.product.id.split('_')[0] === 'local') {
          axios.post(route('products.change-price'), { product: this.saleProductToEdit.product, newPrice: this.editedPrice });
          this.saleProductToEdit.product.public_price = parseFloat(this.editedPrice);
          this.saleProductToEdit.originalPrice = null; //agrega bandera para indicar que no se cambio solo a la venta
          this.showChangePriceConfirmation = false;
        } else { //para un producto global
          axios.post(route('global-product-store.change-price'), { product: this.saleProductToEdit.product, newPrice: this.editedPrice });
          this.saleProductToEdit.product.public_price = parseFloat(this.editedPrice);
          this.saleProductToEdit.originalPrice = null; //agrega bandera para indicar que no se cambio solo a la venta
          this.showChangePriceConfirmation = false;
        }

        // guardar el nuevo precio en indexedDB
        syncIDBProducts();
      }
    },
  },
};
</script>