<template>
    <AppLayout title="Editar produco">
        <div class="px-4 md:px-10 py-7">
            <Back />

            <form @submit.prevent="update"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Editar producto transferido</h1>
                <div class="mt-3 col-span-2">
                    <InputLabel value="Nombre del producto*" class="ml-3 mb-1" />
                    <el-input disabled v-model="form.name" placeholder="Escribe el nombre del producto" :maxlength="100"
                        clearable />
                    <InputError :message="form.errors.name" />
                </div>
                <div v-if="canSeeCost" class="mt-3">
                    <div class="flex items-center">
                        <InputLabel value="Precio de compra" />
                        <el-tooltip content="Precio pagado por el producto al proveedor " placement="right">
                            <i class="fa-regular fa-circle-question ml-2 text-primary text-xs"></i>
                        </el-tooltip>
                    </div>
                    <el-input v-model="form.cost" placeholder="ingresa el precio"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #prefix>
                            <i class="fa-solid fa-dollar-sign"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.cost" />
                </div>
                <div class="mt-3">
                    <InputLabel value="Precio de venta al público*" />
                    <el-input v-model="form.public_price" placeholder="ingresa el precio"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #prefix>
                            <i class="fa-solid fa-dollar-sign"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.public_price" />
                </div>
                <div class="mt-3 col-span-full w-1/2">
                    <InputLabel value="Existencia actual" />
                    <el-input v-model="form.current_stock" placeholder="ingresa la cantidad actual en stock"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')" />
                    <InputError :message="form.errors.current_stock" />
                </div>

                <div class="mt-3">
                    <div class="flex items-center justify-between">
                        <InputLabel value="Categoría" class="ml-3 mb-1" />
                        <!-- <button
                            @click="showCategoryFormModal = true" type="button"
                            class="rounded-full border border-primary size-4 flex items-center justify-center">
                            <i class="fa-solid fa-plus text-primary text-[9px]"></i>
                        </button> -->
                    </div>
                    <el-select disabled class="w-1/2" v-model="form.category_id" clearable placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="category in localCategories" :key="category" :label="category.name"
                            :value="category.id" />
                    </el-select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <div class="mt-3">
                    <div class="flex items-center justify-between">
                        <InputLabel value="Proveedor" class="ml-3 mb-1" />
                        <!-- <button
                            @click="showBrandFormModal = true" type="button"
                            class="rounded-full border border-primary size-4 flex items-center justify-center">
                            <i class="fa-solid fa-plus text-primary text-[9px]"></i>
                        </button> -->
                    </div>
                    <el-select disabled class="w-1/2" v-model="form.brand_id" clearable placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="brand in localBrands" :key="brand" :label="brand.name" :value="brand.id" />
                    </el-select>
                    <InputError :message="form.errors.brand_id" />
                </div>

                <h2 class="font-bold col-span-full text-sm mt-3 mb-2">Cantidades de stock permitidas</h2>

                <div class="mt-3">
                    <InputLabel value="Cantidad mínima" />
                    <el-input v-model="form.min_stock" placeholder="Cantidad mínima permitida en stock"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')" />
                    <InputError :message="form.errors.min_stock" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Cantidad máxima" />
                    <el-input v-model="form.max_stock" placeholder="Cantidad máxima permitida en stock"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')" />
                    <InputError :message="form.errors.max_stock" />
                </div>
                <div class="mt-3 col-span-full">
                    <InputLabel value="Descripción del producto (opcional)" />
                    <el-input v-model="form.description" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                        placeholder="Escribe una descripción o características separadas en renglones" :maxlength="255"
                        show-word-limit clearable />
                    <InputError :message="form.errors.description" />
                </div>
                <div class="mt-3">
                    <InputLabel value="Agregar imagen" class="ml-3 mb-1" />
                    <InputFilePreview @imagen="saveImage($event); form.imageCoverCleared = false"
                        @cleared="form.imageCover = null; form.imageCoverCleared = true"
                        :imageUrl="global_product_store.global_product?.media[0]?.original_url" :disabled="true" />
                </div>
                <div class="mt-3 col-span-2">
                    <InputLabel value="Código del producto (en caso de tener)" class="ml-3 mb-1" />
                    <el-input disabled v-model="form.code" placeholder="Escribe el código del producto" :maxlength="100"
                        clearable>
                        <template #prefix>
                            <i class="fa-solid fa-barcode"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.code" />
                </div>
                <div v-if="$page.props.auth.user.store.activated_modules?.includes('Tienda en línea')" class="mt-3">
                    <div class="flex items-center w-[50%] lg:w-[30%]">
                        <el-checkbox v-model="form.show_in_online_store" label="Mostrar en tienda en línea" />
                        <el-tooltip placement="top">
                            <template #content>
                                <div>
                                    <p>
                                        Desactivar esta opción si no quieres que <br>
                                        este produco se muestre en tu tienda en <br>
                                        linea.
                                    </p>
                                </div>
                            </template>
                            <i class="fa-regular fa-circle-question ml-2 text-primary text-[10px]"></i>
                        </el-tooltip>
                    </div>
                </div>
                <div class="col-span-2 text-right mt-3">
                    <PrimaryButton class="!rounded-full" :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Guardar cambios
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <!-- category form -->
        <!-- <DialogModal :show="showCategoryFormModal" @close="showCategoryFormModal = false">
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
        </DialogModal> -->

        <!-- brand form -->
        <!-- <DialogModal :show="showBrandFormModal" @close="showBrandFormModal = false">
            <template #title> Agregar proveedor </template>
            <template #content>
            <form @submit.prevent="storeBrand">
                <div>
                <label class="text-sm ml-3">Nombre del proveedor *</label>
                <el-input v-model="brandForm.name" placeholder="Escribe el nombre del proveedor" :maxlength="100" required clearable />
                <InputError :message="brandForm.errors.name" />
                </div>
            </form>
            </template>
            <template #footer>
                <div class="flex items-center space-x-2">
                    <CancelButton @click="showBrandFormModal = false" :disabled="brandForm.processing">Cancelar</CancelButton>
                    <PrimaryButton @click="storeBrand()" :disabled="brandForm.processing">Crear</PrimaryButton>
                </div>
            </template>
        </DialogModal> -->
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
// import { addOrUpdateItem } from "@/dbService.js";
import axios from 'axios';

export default {
    data() {
        const form = useForm({
            name: this.global_product_store.global_product?.name,
            code: this.global_product_store.global_product?.code,
            public_price: this.global_product_store.public_price,
            cost: this.global_product_store.cost,
            description: this.global_product_store.description,
            current_stock: this.global_product_store.current_stock,
            category_id: this.global_product_store.global_product?.category_id,
            brand_id: this.global_product_store.global_product?.brand_id,
            min_stock: this.global_product_store.min_stock,
            max_stock: this.global_product_store.max_stock,
            show_in_online_store: !!this.global_product_store.show_in_online_store,
            imageCover: null,
            imageCoverCleared: false
        });

        // const categoryForm = useForm({
        //     name: null,
        // });

        // const brandForm = useForm({
        //     name: null,
        // });

        return {
            form,
            // brandForm,
            // categoryForm,
            localCategories: this.categories,
            localBrands: this.brands,
            showCategoryFormModal: false, //muestra formulario para agregar categoría
            showBrandFormModal: false, //muestra formulario para agregar proveedor
            // permisos de rol
            canSeeCost: ['Administrador', 'Almacenista'].includes(this.$page.props.auth.user.rol),
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
        global_product_store: Object,
        categories: Array,
        brands: Array
    },
    methods: {
        async update() {
            try {
                this.form.put(route("global-product-store.update", this.global_product_store.id), {
                    onSuccess: async () => {
                        // guardar a IndexedDB
                        // Obtener producto mas reciente agregado
                        // const productId = `global_${this.global_product_store.id}`;
                        // const response = await axios.get(route('products.get-by-id-for-indexedDB', productId));
                        // const product = response.data.product;
                        // // actualizar a indexedDB
                        // if (product) {
                        //     addOrUpdateItem('products', product);
                        // }

                        this.$notify({
                            title: 'Se ha editado el producto ' + this.global_product_store.global_product?.name,
                            type: "success",
                        });
                    },
                });
            } catch (error) {
                console.error(error)
            }
        },
        // async storeCategory() {
        //     try {
        //         const response = await axios.post(route('categories.store'), {
        //             name: this.categoryForm.name
        //         });
        //         if (response.status === 200) {
        //             this.$notify({
        //                 title: "Éxito",
        //                 message: "Se ha creado una nueva categoría",
        //                 type: "success",
        //             });
        //             this.form.category_id = response.data.item.id;
        //             this.localCategories.push(response.data.item);
        //             this.showCategoryFormModal = false;
        //             this.categoryForm.reset();
        //         }
        //     } catch (error) {
        //         console.log(error)
        //     }
        // },
        // async storeBrand() {
        //     try {
        //         const response = await axios.post(route('brands.store'), {
        //             name: this.brandForm.name
        //         });
        //         if (response.status === 200) {
        //             this.$notify({
        //                 title: "Éxito",
        //                 message: "Se ha creado una nueva proveedor",
        //                 type: "success",
        //             });
        //             this.localBrands.push(response.data.item);
        //             this.form.brand_id = response.data.item.id;
        //             this.showBrandFormModal = false;
        //             this.brandForm.reset();
        //         }
        //     } catch (error) {
        //         console.log(error)
        //     }
        // },
        saveImage(image) {
            this.form.imageCover = image;
        },
    }
}
</script>