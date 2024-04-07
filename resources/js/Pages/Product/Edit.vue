<template>
    <AppLayout title="Editar produco">
        <div class="px-10 py-7">
            <Back />

            <form @submit.prevent="update"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid grid-cols-2 gap-x-3">
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

                <div class="mt-3">
                    <div class="flex items-center justify-between">
                        <InputLabel value="Categoría*" class="ml-3 mb-1" />
                        <button
                            @click="showCategoryFormModal = true" type="button"
                            class="rounded-full border border-primary size-4 flex items-center justify-center">
                            <i class="fa-solid fa-plus text-primary text-[9px]"></i>
                        </button>
                    </div>
                    <el-select class="w-1/2" v-model="form.category_id" clearable
                        placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="category in localCategories" :key="category" :label="category.name" :value="category.id" />
                    </el-select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <h2 class="font-bold col-span-full text-sm mt-3 mb-2">Cantidades de stock permitidas</h2>

                <div class="mt-3">
                    <InputLabel value="Cantidad mínima" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.min_stock" placeholder="Cantidad mínima permitida en stock"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\D/g, '')" />
                    <InputError :message="form.errors.min_stock" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Cantidad máxima" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.max_stock" placeholder="Cantidad máxima permitida en stock"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\D/g, '')" />
                    <InputError :message="form.errors.max_stock" />
                </div>

                <h2 class="font-bold col-span-full text-sm my-5">Cantidades de stock permitidas</h2>

                <div>
                    <InputLabel value="Agregar imagen" class="ml-3 mb-1" />
                    <InputFilePreview @imagen="saveImage($event); form.imageCoverCleared = false"
                        @cleared="form.imageCover = null; form.imageCoverCleared = true"
                        :imageUrl="product.data.imageCover[0]?.original_url" />
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
                    <PrimaryButton class="!rounded-full" :disabled="form.processing">Guardar cambios</PrimaryButton>
                </div>
            </form>
        </div>

        <!-- category form -->
        <DialogModal :show="showCategoryFormModal" @close="showCategoryFormModal = false">
            <template #title> Agregar categoría </template>
            <template #content>
            <form @submit.prevent="storeCategory" ref="categoryForm">
                <div>
                <label class="text-sm ml-3">Nombre de la categoría *</label>
                <el-input v-model="categoryForm.name" placeholder="Escribe el nombre de la categoría" :maxlength="100" required clearable />
                <InputError :message="categoryForm.errors.name" />
                </div>
            </form>
            </template>
            <template #footer>
                <div class="flex items-center space-x-2">
                    <CancelButton @click="showCategoryFormModal = false" :disabled="categoryForm.processing">Cancelar</CancelButton>
                    <PrimaryButton @click="storeCategory()" :disabled="categoryForm.processing">Crear</PrimaryButton>
                </div>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
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
            category_id: this.product.data.category.id,
            min_stock: this.product.data.min_stock,
            max_stock: this.product.data.max_stock,
            imageCover: null,
            imageCoverCleared: false
        });

        const categoryForm = useForm({
            name: null,
        });

        return {
            form,
            categoryForm,
            localCategories: this.categories,
            showCategoryFormModal: false,
        };
    },
    components: {
        AppLayout,
        InputFilePreview,
        PrimaryButton,
        CancelButton,
        DialogModal,
        InputLabel,
        InputError,
        Back
    },
    props: {
        product: Object,
        categories: Array
    },
    methods: {
        update() {
            if (this.form.imageCover) {
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
                            title: 'Correcto',
                            message: 'Se ha editado el producto ' + this.product.data.name,
                            type: 'success',
                        });
                    },
                });
            }
        },
        async storeCategory() {
            try {
                const response = await axios.post(route('categories.store'), {
                    name: this.categoryForm.name
                });
                if (response.status === 200) {
                    this.$notify({
                        title: "Éxito",
                        message: "Se ha creado una nueva categoría",
                        type: "success",
                    });
                    this.form.category_id = response.data.item.id;
                    this.localCategories.push(response.data.item);
                    this.showCategoryFormModal = false;
                    this.categoryForm.reset();
                }
            } catch (error) {
                console.log(error)
            }
        },
        saveImage(image) {
            this.form.imageCover = image;
        },
    }
}
</script>