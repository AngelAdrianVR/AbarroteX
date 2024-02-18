<template>
    <AppLayout title="Nuevo producto">
        <div class="px-10 py-7">
            <Back />

            <form v-if="products_quantity < 500 " @submit.prevent="store" class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Agregar producto</h1>
                <div class="mt-3 col-span-2">
                    <InputLabel value="Nombre del producto*" class="ml-3 mb-1" />
                    <el-input v-model="form.name" placeholder="Escribe el nombre del producto" :maxlength="100" clearable />
                    <InputError :message="form.errors.name" />
                </div>
                <div class="mt-3">
                    <div class="flex items-center ml-3 mb-1">
                        <InputLabel value="Precio de compra" class="text-sm" />
                        <el-tooltip content="Precio pagado por el producto al proveedor " placement="right">
                            <i class="fa-regular fa-circle-question ml-2 text-primary text-xs"></i>
                        </el-tooltip>
                    </div>
                    <el-input v-model="form.cost" placeholder="ingresa el precio"
                      :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')">
                      <template #prefix>
                        <i class="fa-solid fa-dollar-sign"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.cost" />
                </div>
                <div class="mt-3">
                    <InputLabel value="Precio de venta al público*" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.public_price" placeholder="ingresa el precio"
                      :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')">
                      <template #prefix>
                        <i class="fa-solid fa-dollar-sign"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.public_price" />
                </div>
                <div class="mt-3">
                    <InputLabel value="Existencia actual" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.current_stock" placeholder="ingresa la cantidad actual en stock"
                      :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')" />
                    <InputError :message="form.errors.current_stock" />
                </div>

                <h2 class="font-bold col-span-full text-sm my-5">Cantidades de stock permitidas</h2>
                
                <div class="mt-3">
                    <InputLabel value="Cantidad mínima" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.min_stock" placeholder="Catidad mínima permitida en stock"
                      :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      :parser="(value) => value.replace(/\D/g, '')" />
                    <InputError :message="form.errors.min_stock" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Cantidad máxima" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.max_stock" placeholder="Catidad máxima permitida en stock"
                      :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      :parser="(value) => value.replace(/\D/g, '')" />
                    <InputError :message="form.errors.max_stock" />
                </div>

                <h2 class="font-bold col-span-full text-sm my-5">Cantidades de stock permitidas</h2>

                <div>
                    <InputLabel value="Agregar imagen" class="ml-3 mb-1" />
                    <InputFilePreview @imagen="saveImage" @cleared="form.imageCover = null" />
                </div>

                <div class="mt-3 col-span-2">
                    <InputLabel value="Código del producto (en caso de tener)" class="ml-3 mb-1" />
                    <el-input v-model="form.code" placeholder="Escribe el código del producto" :maxlength="100" clearable>
                        <template #prefix>
                        <i class="fa-solid fa-barcode"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.code" />
                </div>

                <div class="col-span-2 text-right mt-3">
                    <PrimaryButton class="!rounded-full" :disabled="form.processing">Guardar producto</PrimaryButton>
                </div>
            </form>
            <div v-else class="text-center text-gray-500">
                <p class="text-3xl mb-3">¡Lo sentimos!</p>
                <p class="">Has llegado al límite de productos disponibles (500). Para poder aumentar el límite ponte en contacto con el equipo de DTW</p>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
      name: null,
      code: null,
      public_price: null,
      cost: null,
      current_stock: null,
      min_stock: null,
      max_stock: null,
      imageCover: null,
    });

    return {
       form,
    };
},
components:{
AppLayout,
PrimaryButton,
InputLabel, 
InputError,
InputFilePreview,
Back
},
props:{
products_quantity: Number
},
methods:{
    store() {
      this.form.post(route("products.store"), {
        onSuccess: () => {
         this.$notify({
          title: "Correcto",
          message: "Se ha agregado un nuevo producto",
          type: "success",
        });
        },
      });
    },
    saveImage(image) {
        this.form.imageCover = image;
    },
}
}
</script>