<template>
    <AppLayout title="Editar produco">
        <div class="px-10 py-7">
            <Back />

            <form @submit.prevent="update" class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 grid grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Editar producto</h1>
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
                      :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      :parser="(value) => value.replace(/\$\s?|(,*)/g, '')">
                      <template #prefix>
                        <i class="fa-solid fa-dollar-sign"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.cost" />
                </div>
                <div class="mt-3">
                    <InputLabel value="Precio de venta al público*" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.public_price" placeholder="ingresa el precio"
                      :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      :parser="(value) => value.replace(/\$\s?|(,*)/g, '')">
                      <template #prefix>
                        <i class="fa-solid fa-dollar-sign"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.public_price" />
                </div>
                <div class="mt-3">
                    <InputLabel value="Existencia actual" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.current_stock" placeholder="ingresa la cantidad actual en stock"
                      :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      :parser="(value) => value.replace(/\$\s?|(,*)/g, '')" />
                    <InputError :message="form.errors.current_stock" />
                </div>

                <h2 class="font-bold col-span-full text-sm my-5">Cantidades de stock permitidas</h2>
                
                <div class="mt-3">
                    <InputLabel value="Cantidad mínima" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.min_stock" placeholder="Catidad mínima permitida en stock"
                      :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      :parser="(value) => value.replace(/\$\s?|(,*)/g, '')" />
                    <InputError :message="form.errors.min_stock" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Cantidad máxima" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.max_stock" placeholder="Catidad máxima permitida en stock"
                      :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      :parser="(value) => value.replace(/\$\s?|(,*)/g, '')" />
                    <InputError :message="form.errors.max_stock" />
                </div>

                <h2 class="font-bold col-span-full text-sm my-5">Cantidades de stock permitidas</h2>

                <div>
                    <InputLabel value="Agregar imagen" class="ml-3 mb-1" />
                    <InputFilePreview 
                        @imagen="saveImage($event); form.imageCoverCleared = false" 
                        @cleared="form.imageCover = null; form.imageCoverCleared = true"
                        :imageUrl="product.data.imageCover[0]?.original_url" />
                </div>

                <div class="mt-3 col-span-2">
                    <InputLabel value="Código del producto*" class="ml-3 mb-1" />
                    <el-input v-model="form.code" placeholder="Escribe el código del producto" :maxlength="100" clearable>
                        <template #prefix>
                        <i class="fa-solid fa-barcode"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.code" />
                </div>

                <div class="col-span-2 text-right mt-3">
                    <PrimaryButton class="!rounded-full" :disabled="form.processing">Guardar cambios</PrimaryButton>
                </div>
            </form>
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
      name: this.product.data.name,
      code: this.product.data.code,
      public_price: this.product.data.public_price,
      cost: this.product.data.cost,
      current_stock: this.product.data.current_stock,
      min_stock: this.product.data.min_stock,
      max_stock: this.product.data.max_stock,
      imageCover: null,
      imageCoverCleared: false
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
product: Object
},
methods:{
    update() {
        if (this.form.imageCover) {
            console.log('con imagen');
            this.form.post(route("products.update-with-media", this.product.data.id), {
                method: '_put',
                onSuccess: () => {
                  this.$notify({
                  title: "Correcto",
                  message: 'Se ha editado el producto ' + this.product.data.name,
                  type: "success",
                      });
                  },
              });
        } else {
            this.form.put(route("products.update", this.product.data.id), {
                onSuccess: () => {
                    ElNotification({
                        title: 'Success',
                        message: 'Se ha editado el producto ' + this.product.data.name,
                        type: 'success',
                        position: 'bottom-right',
                    });
                },
            });
        }
    },
    saveImage(image) {
        this.form.imageCover = image;
    },
}
}
</script>