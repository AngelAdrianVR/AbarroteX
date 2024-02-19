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
        <div class="grid grid-cols-2 items-center h-14 w-[45%]">
          <img class="mx-auto h-14 object-contain" :src="sale.product.imageCover[0]?.original_url">
          <p class="font-bold">{{ sale.product.name }}</p>
        </div>
        <div class="w-[15%]">${{ sale.product.public_price }}</div>
        <div class="w-[20%]">
          <el-input-number v-model="sale.quantity" :min="0" :max="sale.product.current_stock" :precision="2" />
        </div>
        <div class="text-[#5FCB1F] font-bold w-[15%]">${{ (sale.product.public_price * sale.quantity).toLocaleString('en-US', {
          minimumFractionDigits: 2
        }) }}</div>
        <div class="w-[5%] text-right">
          <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?"
            @confirm="deleteItem(sale.product.id)">
            <template #reference>
              <i class="fa-regular fa-trash-can mr-2 text-primary cursor-pointer p-2 hover:bg-gray-100 rounded-full"></i>
            </template>
          </el-popconfirm>
        </div>
      </div>
    </div>
  </div>
  <!-- vista movil -->
  <div class="overflow-y-auto h-[300px] md:hidden text-[11px]">
    <div v-for="(sale, index) in saleProducts" :key="index"
      class="mb-2 grid grid-cols-3 gap-2 border rounded-md items-center relative">
      <figure>
        <img class="mx-auto w-3/4 object-contain" :src="sale.product.imageCover[0]?.original_url" alt="">
      </figure>
      <div class="col-span-2 flex flex-col space-y-1 justify-center py-1">
        <p class="font-bold">{{ sale.product.name }}</p>
        <p>${{ sale.product.public_price }} / unidad</p>
        <el-input-number v-model="sale.quantity" :min="0" :max="sale.product.current_stock" :precision="2" size="small" />
        <div class="text-[#5FCB1F] font-bold">${{ (sale.product.public_price * sale.quantity).toLocaleString('en-US', {
          minimumFractionDigits: 2
        }) }}</div>
        <div class="self-end">
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
  <p class="text-center text-gray-500 text-sm mt-14" v-if="saleProducts?.length == 0">Escanea un producto para comenzar a
    generar la venta</p>
</template>

<script>
export default {
  data() {
    return {
      quantity: 1
    };
  },
  props: {
    saleProducts: Array
  },
  emits: ['delete-product'],
  methods: {
    deleteItem(productId) {
      this.$emit('delete-product', productId);
    }
  },
};
</script>