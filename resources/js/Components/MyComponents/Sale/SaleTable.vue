<template>
  <!-- vista desktop -->
  <div v-if="saleProducts?.length" class="w-full mx-auto text-[11px] lg:text-sm over hidden md:block">
    <div class="text-center lg:text-base flex items-center space-x-4 *:font-bold *:text-left *:text-gray37">
      <div class="pl-2 w-[45%]">Producto</div>
      <div class="w-[15%]">Precio</div>
      <div class="w-[20%]">Cantidad</div>
      <div class="w-[15%]">Importe</div>
      <div class="w-[5%]"></div>
    </div>
    <div class="overflow-auto border-b border-grayD9" :class="showFull ? 'h-[51vh]' : 'h-[28vh]'">
      <div v-for="(sale, saleIndex) in saleProducts" :key="saleIndex"
        class="mb-2 flex items-center space-x-4 border rounded-md relative">
        <div class="grid grid-cols-2 items-center min-h-12 w-[45%]">
          <img v-if="sale.product.imageUrl" class="mx-auto h-14 object-contain select-none" :draggable="false"
            :src="sale.product.imageUrl" :alt="sale.product.name">
          <div v-else
            class="size-10 mx-auto bg-white border border-grayD9 text-gray99 rounded-md text-sm flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-4">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
          </div>
          <div class="text-sm">
            <div v-if="$page.props.auth.user.store.type == 'Boutique / Tienda de Ropa / Zapatería'">
              <p class="text-xs">Categoria: {{ sale.product.additional?.size.category }}</p>
              <p class="text-xs">
                Color: {{ sale.product.additional?.color.name }}
                <i v-if="sale.product.additional?.color.color" class="fa-solid fa-shirt ml-1"
                  :style="{ color: sale.product.additional?.color.color }"></i>
              </p>
              <p class="text-xs">Talla: {{ sale.product.additional?.size.name }}</p>
            </div>
            <p class="font-bold">{{ sale.product.name }}</p>
          </div>
        </div>
        <div :class="editMode !== null ? 'w-[18%]' : 'w-[15%]'" class="text-lg flex items-center">
          <template v-if="editMode !== saleIndex">
            ${{ sale.product.public_price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}{{ getPrefix(sale) }}
            <!-- Condicional en el boton depende de la configuracion seleccionada para no poder editar precio -->
            <button v-if="isDiscountOn && $page.props.auth.user.permissions.includes('Editar precios')"
              @click.stop="startEditing(sale, saleIndex)"
              class="flex items-center justify-center text-primary hover:bg-gray-50 hover:shadow-gray-400 hover:shadow-sm size-5 rounded-md ml-2 mr-1">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-[14px]">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
              </svg>
            </button>
          </template>
          <template v-else-if="editMode == saleIndex">
            <div class="flex items-center space-x-2">
              <el-input v-model="editedPrice" @keyup.enter="handleChangePrice(sale)" type="number" step="0.01">
                <template #prefix>
                  <i class="fa-solid fa-dollar-sign"></i>
                </template>
              </el-input>
              <button :disabled="sale.product.public_price === editedPrice" @click="handleChangePrice(sale)"
                class="flex items-center justify-center rounded-full size-5 bg-primary flex-shrink-0 disabled:cursor-not-allowed disabled:bg-gray-300">
                <i class="fa-solid fa-check text-white text-[10px]"></i>
              </button>
              <button @click="editMode = false"
                class="flex items-center justify-center rounded-full size-5 bg-[#EDEDED] flex-shrink-0"><i
                  class="fa-solid fa-x mark text-black text-[10px]"></i></button>
            </div>
          </template>
        </div>
        <div class="w-[20%]">
          <el-input-number v-if="isInventoryOn" @change="handleChangeQuantity(sale)" v-model="sale.quantity" :min="0"
            size="small" :max="sale.product.current_stock" :precision="2">
            <template #suffix>
              <span v-if="sale.product.measure_unit?.trim() === 'Kilogramo'">{{
                sale.product.measure_unit?.trim() === 'Kilogramo' ? 'Kg' :
                  sale.product.measure_unit?.trim() === 'Litro' ? 'L' : ''
              }}</span>
            </template>
          </el-input-number>
          <el-input-number v-else @change="handleChangeQuantity(sale)" v-model="sale.quantity" :min="0" :precision="2"
            size="small">
            <template #suffix>
              <span v-if="sale.product.measure_unit?.trim() === 'Kilogramo'">{{
                sale.product.measure_unit?.trim() === 'Kilogramo' ? 'Kg' :
                  sale.product.measure_unit?.trim() === 'Litro' ? 'L' : ''
              }}</span>
            </template>
          </el-input-number>
        </div>
        <div class="w-[15%]">
          <p v-if="sale.product.discounted_price != null" class="text-gray99 line-through text-base ml-5">
            ${{ (sale.product.public_price * sale.quantity).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
          </p>
          <div class="flex items-center">
            <el-dropdown v-if="sale.product.promotions?.length" trigger="click">
              <button type="button" @click.stop title="Promociones"
                class="flex items-center justify-center hover:bg-grayF2 size-5 rounded-full transition-colors duration-200"
                :class="someApplicablePromotion(sale) ? 'text-[#AE080B]' : 'text-gray99'">
                <svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M4.28963 0C6.61877 2.72132 7.62955 4.40871 8.10018 8.09863C8.68001 7.68802 8.88776 7.19533 8.93416 5.7168C11.7333 11.4332 8.4584 15.0059 5.54061 15.1846C5.18334 15.2064 4.52925 15.2601 4.1119 15.125C-0.115441 13.7554 -1.60456 10.0636 2.14607 5.24023C4.57869 2.25074 4.74354 1.28426 4.28963 0ZM4.82479 7.44531C2.7427 10.1811 2.08598 12.5064 5.0035 14.0547C6.92255 13.584 7.62367 12.4473 7.80232 10.4824C7.42197 11.0129 7.17028 11.2542 6.49178 11.375C6.67028 9.76748 6.13695 8.64466 4.82479 7.44531Z"
                    fill="currentColor" />
                </svg>
              </button>
              <template #dropdown>
                <el-dropdown-menu>
                  <main class="px-3 py-2 w-72 lg:w-[410px]">
                    <section class="space-y-1">
                      <div class="flex items-center justify-between mb-2">
                        <h1 class="font-semibold lg:text-sm ml-2">
                          Producto con promoción
                        </h1>
                        <!-- <button type="button" title="Editar promociones"
                          class="flex items-center justify-center size-[22px] rounded-full bg-[#F2F2F2] text-primary"
                          @click="handleEditPromo(sale.product)">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                              d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                          </svg>
                        </button> -->
                      </div>
                      <PromotionCard v-for="promo in sale.product.promotions.filter(p => !isExpired(p.expiration_date))"
                        :key="promo.id" :promo="promo" :product="sale.product" :showGiftable="false"
                        :applied="!!isApplicablePromotion(sale, promo)" />
                    </section>
                    <section v-if="sale.product.promotions.filter(p => isExpired(p.expiration_date)).length"
                      class="mt-4 space-y-1">
                      <h1 class="text-[#6E6E6E] font-semibold lg:text-sm ml-2">
                        Promociones vencidas
                      </h1>
                      <PromotionCard v-for="promo in sale.product.promotions.filter(p => isExpired(p.expiration_date))"
                        :promo="promo" :product="sale.product" :showGiftable="false" :key="promo.id" />
                    </section>
                  </main>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
            <div class="font-bold text-[#5FCB1F] text-lg">
              <p v-if="sale.product.discounted_price != null" :class="!sale.product.promotions.length ? 'ml-5' : null">
                ${{ (Math.round(sale.product.discounted_price * sale.quantity * 10) / 10).toLocaleString('en-US', {
                  minimumFractionDigits: 2
                }) }}
              </p>
              <p v-else :class="!sale.product.promotions.length ? 'ml-5' : null">
                ${{ (sale.product.public_price * sale.quantity).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
              </p>
            </div>
          </div>
          <!-- <div v-if="sale.product.promotions?.length">
            <p v-if="someApplicablePromotion(sale)" class="text-[#AE080B] text-[10px]">Promoción aplicada</p>
            <p v-else class="text-gray99 text-[10px]">Promoción disponible</p>
          </div> -->
        </div>
        <div class="w-[5%] text-right pr-10">
          <el-popconfirm v-if="canDelete" confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303"
            title="¿Continuar?" @confirm="deleteItem(sale.product.id)">
            <template #reference>
              <i
                class="fa-regular fa-trash-can text-xs text-primary cursor-pointer p-2 hover:bg-gray-50 rounded-md hover:shadow-gray-400 hover:shadow-sm"></i>
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
        <img v-if="sale.product.imageUrl" :draggable="false" class="mx-auto w-3/4 h-24 object-contain select-none"
          :src="sale.product.imageUrl" :alt="sale.product.name">
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
            ${{ sale.product.public_price }}{{ getPrefix(sale) }}
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
        <el-input-number v-if="isInventoryOn" v-model="sale.quantity" @change="handleChangeQuantity(sale)" :min="0"
          :max="sale.product.current_stock" :precision="2" />
        <el-input-number v-else v-model="sale.quantity" @change="handleChangeQuantity(sale)" :min="0" :precision="2"
          size="small" />
        <p v-if="sale.product.discounted_price != null" class="text-gray99 line-through text-base ml-5">
          ${{ (sale.product.public_price * sale.quantity).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
        </p>
        <div class="flex items-center">
          <el-dropdown v-if="sale.product.promotions?.length" trigger="click">
            <button type="button" @click.stop title="Promociones"
              class="flex items-center justify-center hover:bg-grayF2 size-5 rounded-full transition-colors duration-200"
              :class="someApplicablePromotion(sale) ? 'text-[#AE080B]' : 'text-gray99'">
              <svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M4.28963 0C6.61877 2.72132 7.62955 4.40871 8.10018 8.09863C8.68001 7.68802 8.88776 7.19533 8.93416 5.7168C11.7333 11.4332 8.4584 15.0059 5.54061 15.1846C5.18334 15.2064 4.52925 15.2601 4.1119 15.125C-0.115441 13.7554 -1.60456 10.0636 2.14607 5.24023C4.57869 2.25074 4.74354 1.28426 4.28963 0ZM4.82479 7.44531C2.7427 10.1811 2.08598 12.5064 5.0035 14.0547C6.92255 13.584 7.62367 12.4473 7.80232 10.4824C7.42197 11.0129 7.17028 11.2542 6.49178 11.375C6.67028 9.76748 6.13695 8.64466 4.82479 7.44531Z"
                  fill="currentColor" />
              </svg>
            </button>
            <template #dropdown>
              <el-dropdown-menu>
                <main class="px-3 py-2 w-72 lg:w-[410px]">
                  <section class="space-y-1">
                    <div class="flex items-center justify-between mb-2">
                      <h1 class="font-semibold lg:text-sm ml-2">
                        Producto con promoción
                      </h1>
                      <!-- <button type="button" title="Editar promociones"
                            class="flex items-center justify-center size-[22px] rounded-full bg-[#F2F2F2] text-primary"
                            @click="handleEditPromo(sale.product)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                              stroke="currentColor" class="size-4">
                              <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                          </button> -->
                    </div>
                    <PromotionCard v-for="promo in sale.product.promotions.filter(p => !isExpired(p.expiration_date))"
                      :key="promo.id" :promo="promo" :product="sale.product" :showGiftable="false"
                      :applied="!!isApplicablePromotion(sale, promo)" />
                  </section>
                  <section v-if="sale.product.promotions.filter(p => isExpired(p.expiration_date)).length"
                    class="mt-4 space-y-1">
                    <h1 class="text-[#6E6E6E] font-semibold lg:text-sm ml-2">
                      Promociones vencidas
                    </h1>
                    <PromotionCard v-for="promo in sale.product.promotions.filter(p => isExpired(p.expiration_date))"
                      :promo="promo" :product="sale.product" :showGiftable="false" :key="promo.id" />
                  </section>
                </main>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
          <div class="font-bold text-[#5FCB1F] text-lg">
            <p v-if="sale.product.discounted_price != null" :class="!sale.product.promotions.length ? 'ml-5' : null">
              ${{ (Math.round(sale.product.discounted_price * sale.quantity * 10) / 10).toLocaleString('en-US', {
                minimumFractionDigits: 2
              }) }}
            </p>
            <p v-else :class="!sale.product.promotions.length ? 'ml-5' : null">
              ${{ (sale.product.public_price * sale.quantity).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
            </p>
          </div>
        </div>
        <!-- <div v-if="sale.product.promotions?.length">
          <p v-if="someApplicablePromotion(sale)" class="text-[#AE080B] text-[10px]">Promoción aplicada</p>
          <p v-else class="text-gray99 text-[10px]">Promoción disponible</p>
        </div> -->
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
  <div class="text-center text-gray-500 text-sm md:mt-14" v-if="saleProducts.length == 0">
    <div v-if="isScanOn" class="flex flex-col items-center justify-center text-gray99 text-sm">
      <span class="select-none">Escanea un producto para comenzar la venta</span>
      <figure class="my-7 flex items-center justify-center select-none">
        <img :draggable="false" class="w-1/3 md:w-1/4 opacity-15 select-none" src="@/../../public/images/escaner2.png"
          alt="scaner">
      </figure>
    </div>
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
import PromotionCard from '../Promotions/PromotionCard.vue';
import { isPast, parseISO } from 'date-fns';
import axios from 'axios';

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
      editedPrice: null,
      originalPrices: [],
    };
  },
  components: {
    ConfirmationModal,
    PrimaryButton,
    CancelButton,
    PromotionCard,
  },
  props: {
    saleProducts: Array,
    showFull: {
      type: Boolean,
      default: true
    }
  },
  emits: ['delete-product', 'create-gift-product'],
  methods: {
    handleChangeQuantity(sale) {
      if (sale.giftQuantity) {
        // Si es un regalo, se calcula el precio
        const pricePerUnit =
          (sale.product.public_price * (sale.quantity - sale.giftQuantity)) / sale.quantity;
        sale.product.discounted_price = Math.round(pricePerUnit * 100) / 100; // redondear a 2 decimales
      } else {
        sale.giftQuantity = null; // Resetea la cantidad de regalo si no es un regalo
      }
    },
    isExpired(date) {
      if (!date) return false; // Si no hay fecha, no está vencida
      // Convierte la fecha a objeto Date si es string
      const dateObj = typeof date === 'string' ? parseISO(date) : date;
      return isPast(dateObj);
    },
    // handleEditPromo(product) {
    //   const encodedId = btoa(product.id.toString());
    //   if (product.global_product_id) {
    //     this.$inertia.get(route('promotions.global.edit', encodedId));
    //   } else {
    //     this.$inertia.get(route('promotions.local.edit', encodedId));
    //   }
    // },
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
    async stopEditing(changeJustForThisSale) {
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
          await axios.post(route('products.change-price'), { product: this.saleProductToEdit.product, newPrice: this.editedPrice });
          this.saleProductToEdit.product.public_price = parseFloat(this.editedPrice);
          this.saleProductToEdit.originalPrice = null; //agrega bandera para indicar que no se cambio solo a la venta
          this.showChangePriceConfirmation = false;
        } else { //para un producto global
          await axios.post(route('global-product-store.change-price'), { product: this.saleProductToEdit.product, newPrice: this.editedPrice });
          this.saleProductToEdit.product.public_price = parseFloat(this.editedPrice);
          this.saleProductToEdit.originalPrice = null; //agrega bandera para indicar que no se cambio solo a la venta
          this.showChangePriceConfirmation = false;
        }

        // guardar el nuevo precio en indexedDB
        syncIDBProducts();
      }
    },
    getPrefix(sale) {
      if (sale.product.bulk_product) { //revisa si es a granel
        if (sale.product.measure_unit === 'Kilogramo') { //si es a granel revisa que unidad de medida para retornal el prefijo
          return '/Kg';
        } else if (sale.product.measure_unit === 'Litro') {
          return '/L';
        }
      }
    },
    // funcion que revisa si una promocion ya es aplicable al producto de la venta
    isApplicablePromotion(sale, promotion) {
      if (promotion.type === 'Descuento en precio fijo' || promotion.type === 'Descuento en porcentaje') {
        sale.product.discounted_price = promotion.discounted_price;
        // indicar que la promoción esta aplicada
        promotion.applied = true;
        return true;
      } else if (promotion.type === 'Precio especial por paquete') {
        const applicableQuantity = Math.floor(sale.quantity / promotion.pack_quantity);
        const remainder = sale.quantity % promotion.pack_quantity;
        if (applicableQuantity > 0) {
          // calcular el precio por unidad del paquete
          const pricePerUnit = ((applicableQuantity * promotion.pack_price) + (remainder * sale.product.public_price)) / sale.quantity;
          sale.product.discounted_price = Math.round(pricePerUnit * 100) / 100; // redondear a 2 decimales
        } else {
          sale.product.discounted_price = null; // si no se cumple la cantidad del paquete, no hay descuento
        }

        // indicar que la promoción esta aplicada
        promotion.applied = applicableQuantity > 0;
        return applicableQuantity > 0;
      } else if (promotion.type === 'Promoción tipo 2x1 o 3x2') {
        // obtener cuantas promociones se pueden aplicar
        const applicableQuantity = Math.floor(sale.quantity / promotion.buy_quantity);
        const remainder = sale.quantity % promotion.buy_quantity;
        if (applicableQuantity > 0) {
          // obtener el precio a pagar por unidad para respetar la promocion y tomando en cuenta el resto a precio original
          const pricePerUnit = (sale.product.public_price * ((applicableQuantity * promotion.pay_quantity) + remainder)) / sale.quantity;
          sale.product.discounted_price = Math.round(pricePerUnit * 100) / 100; // redondear a 2 decimales;
        } else {
          sale.product.discounted_price = null;
        }

        // indicar que la promoción esta aplicada
        promotion.applied = !!applicableQuantity;
        return applicableQuantity;
      } else if (promotion.type === 'Producto gratis al comprar otro') {
        if (sale.quantity >= promotion.min_quantity_to_gift) {
          //revisar si ya hay un producto gratis aplicado
          if (!sale.gifted_product) {
            this.$emit('create-gift-product', {
              name: promotion.giftable.name,
              quantity: promotion.quantity_to_gift
            });

            sale.gifted_product = true; // marca que se ha aplicado un producto gratis
          }
        } else if (sale.gifted_product) {
          const productId = promotion.giftable_type == 'App\\Models\\GlobalProductStore'
            ? 'global_' + promotion.giftable_id
            : 'local_' + promotion.giftable_id;
          // si la cantidad ya no es suficiente para aplicar el producto gratis, eliminarlo
          const isGift = true;
          this.$emit('delete-product', productId, isGift);
          sale.gifted_product = false; // marca que ya no se aplica el producto gratis
        }

        // indicar que la promoción esta aplicada
        promotion.applied = sale.quantity >= promotion.min_quantity_to_gift;
        return sale.quantity >= promotion.min_quantity_to_gift;
      } else {
        promotion.applied = false;
        return false; // Si no coincide con ningún tipo conocido, no es aplicable
      }
    },
    // función que devuelve si por lo menos una promoción es aplicable al producto de la venta
    someApplicablePromotion(sale) {
      return sale.product.promotions.some(promo => this.isApplicablePromotion(sale, promo));
    }
  },
};
</script>