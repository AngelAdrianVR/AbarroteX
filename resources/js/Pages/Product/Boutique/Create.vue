<template>
    <AppLayout title="Nuevo producto">
        <div class="px-3 md:px-10 py-7">
            <Back :to="route('boutique-products.index')" />

            <form v-if="products_quantity < productsLimit" @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Agregar producto</h1>
                <div class="mt-3">
                    <InputLabel value="Nombre del producto*" />
                    <el-input v-model="form.name" placeholder="Escribe el nombre del producto" :maxlength="100"
                        clearable />
                    <InputError :message="form.errors.name" />
                </div>
                <div class="mt-3">
                    <div class="flex items-center justify-between">
                        <InputLabel value="Categoría *" />
                        <button @click="showCategoryFormModal = true" type="button"
                            class="rounded-full border border-primary size-4 flex items-center justify-center">
                            <i class="fa-solid fa-plus text-primary text-[9px]"></i>
                        </button>
                    </div>
                    <el-select @change="handleCategory" class="w-1/2" filterable v-model="form.category_id" clearable
                        placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="category in localCategories" :key="category" :label="category.name"
                            :value="category.id" />
                    </el-select>
                    <InputError :message="form.errors.category_id" />
                </div>
                <div class="mt-3 col-span-full">
                    <InputLabel value="Descripción del producto (opcional)" />
                    <el-input v-model="form.description" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                        placeholder="Escribe una descripción o características separadas por renglones" :maxlength="255"
                        show-word-limit clearable />
                    <InputError :message="form.errors.description" />
                </div>
                <div v-if="canSeeCost" class="mt-3">
                    <div class="flex items-center">
                        <InputLabel value="Precio de compra" />
                        <el-tooltip content="Precio pagado por el producto al proveedor " placement="right">
                            <i class="fa-regular fa-circle-question ml-2 text-primary text-[10px]"></i>
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
                        :parser="(value) => value.replace(/[^\d.]/g, '')" class="!self-end !justify-self-end">
                        <template #prefix>
                            <i class="fa-solid fa-dollar-sign"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.public_price" />
                </div>
                <section class="col-span-full mt-3">
                    <h2 class="font-bold text-sm mt-3">Control de stock</h2>
                    <div class="flex items-center space-x-3 mt-1 mb-4">
                        <el-checkbox v-model="form.has_inventory_control" name="inventory" required size="small"
                            label="Habilitar control de stock" />
                        <el-tooltip placement="right">
                            <template #content>
                                <p>
                                    Es recomendable seleccionar esta opción si <br>
                                    el producto se surte con regularidad
                                </p>
                            </template>
                            <i class="fa-regular fa-circle-question ml-2 text-primary text-[10px]"></i>
                        </el-tooltip>
                    </div>
                    <article v-for="(item, index) in form.sizes" :key="index" class="flex items-center space-x-3 mb-2">
                        <div class="w-[33%]">
                            <div class="w-full flex items-center justify-between">
                                <InputLabel v-if="index == 0" value="Talla *" />
                                <button @click="openSizeModal" v-if="index == 0" type="button"
                                    class="text-primary text-xs">Crear
                                    talla</button>
                            </div>
                            <el-select :ref="'size' + index" filterable v-model="item.size_id" clearable
                                placeholder="Seleccione"
                                :no-data-text="form.category_id ? 'No hay tallas registradas en la categoria seleccionada' : 'Primero seleccione la categoria'"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="size in getCategorySizes" :key="size.id" :label="size.name"
                                    :value="size.id" :disabled="form.sizes.some(item => item.size_id == size.id)">
                                    <p class="flex items-center justify-between">
                                        <span>{{ size.name }}</span>
                                        <span v-if="size.short" class="text-[10px] text-gray99">({{ size.short
                                            }})</span>
                                    </p>
                                </el-option>
                            </el-select>
                            <InputError :message="form.errors[`sizes.${index}.size_id`]" />
                        </div>
                        <div class="w-[14%]">
                            <InputLabel v-if="index == 0" value="Existencias *" />
                            <el-input v-model="item.current_stock" placeholder="Stock actual"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/[^\d.]/g, '')" />
                            <InputError :message="form.errors[`sizes.${index}.current_stock`]" />
                        </div>
                        <div v-if="form.has_inventory_control" class="w-[21%]">
                            <InputLabel v-if="index == 0" value="Cantidad mínima" />
                            <el-input v-model="item.min_stock" placeholder="Mínimo permitido"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/[^\d.]/g, '')" />
                            <InputError :message="form.errors[`sizes.${index}.min_stock`]" />
                        </div>
                        <div v-if="form.has_inventory_control" class="w-[21%]">
                            <InputLabel v-if="index == 0" value="Cantidad máxima" />
                            <el-input v-model="item.max_stock" placeholder="Máximo permitido"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/[^\d.]/g, '')" />
                            <InputError :message="form.errors[`sizes.${index}.max_stock`]" />
                        </div>
                        <div class="w-[4%] flex justify-end">
                            <el-popconfirm v-if="form.sizes.length > 1" confirm-button-text="Si" cancel-button-text="No"
                                icon-color="#373737" :title="'¿Desea eliminar la talla seleccionada?'"
                                @confirm="deleteSize(index)">
                                <template #reference>
                                    <button type="button">
                                        <i class="fa-regular fa-trash-can text-sm text-primary"></i>
                                    </button>
                                </template>
                            </el-popconfirm>
                        </div>
                    </article>
                    <button @click="addSize" type="button" class="text-primary ml-3">+ Añadir talla</button>
                </section>
                <!-- <div class="mt-3 col-span-full">
                    <InputLabel value="Moneda*" />
                    <el-select v-model="form.currency" placeholder="Moneda *" :fit-input-width="true" class="!w-1/2">
                        <el-option v-for="item in currencies" :key="item.value" :label="item.label" :value="item.label">
                            <span style="float: left">{{ item.label }}</span>
                            <span style="float: right; color: #cccccc; font-size: 13px">{{ item.value }}</span>
                        </el-option>
                    </el-select>
                    <InputError :message="form.errors.currency" />
                </div> -->
                <div class="mt-7">
                    <InputLabel value="Agregar imagen" />
                    <InputFilePreview @imagen="saveImage" @cleared="form.imageCover = null" />
                </div>

                <div class="mt-3 col-span-2">
                    <InputLabel value="Código del producto (en caso de tener)" />
                    <el-input v-model="form.code" placeholder="Ej. RM-4332" :maxlength="100" clearable>
                        <template #prefix>
                            <i class="fa-solid fa-barcode"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.code" />
                </div>

                <div class="col-span-2 text-right mt-3">
                    <PrimaryButton class="!rounded-full" :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Guardar producto
                    </PrimaryButton>
                </div>
            </form>
            <div v-else class="text-center text-gray37">
                <h1 class="font-bold text-5xl text-center mb-5">¡Cima alcanzada!</h1>
                <p class="text-xl text-center">Has llegado al límite de productos ({{
                    productsLimit.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}) de tu plan contratado.</p>
                <p class="text-xl text-center">
                    Sigue creciendo tu negocio y descubre nuestros planes haciendo clic en el siguiente botón
                </p>
                <div class="flex justify-center mt-5">
                    <PrimaryButton @click="$inertia.get(route('profile.show'))" :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Explorar planes
                    </PrimaryButton>
                </div>
            </div>
        </div>

        <!-- category form -->
        <DialogModal :show="showCategoryFormModal" @close="showCategoryFormModal = false">
            <template #title> Agregar categoría </template>
            <template #content>
                <form @submit.prevent="storeCategory" ref="categoryForm">
                    <div>
                        <label class="text-sm ml-3">Nombre de la categoría *</label>
                        <el-input v-model="categoryForm.name" placeholder="Escribe el nombre de la categoría"
                            :maxlength="100" required clearable />
                        <InputError :message="categoryForm.errors.name" />
                    </div>
                </form>
            </template>
            <template #footer>
                <div class="flex items-center space-x-2">
                    <CancelButton @click="showCategoryFormModal = false" :disabled="categoryForm.processing">Cancelar
                    </CancelButton>
                    <PrimaryButton @click="storeCategory()" :disabled="categoryForm.processing">Crear</PrimaryButton>
                </div>
            </template>
        </DialogModal>

        <!-- sizes form -->
        <DialogModal :show="showSizeFormModal" @close="showSizeFormModal = false">
            <template #title> Agregar talla </template>
            <template #content>
                <p class="text-gray99 mb-3">En este apartado puedes crear tallas que no se encuentren en la lista de
                    tallas</p>
                <form @submit.prevent="storeSize" class="grid grid-cols-2 gap-3">
                    <div>
                        <InputLabel value="Categoria *" />
                        <el-select filterable v-model="sizeForm.category" clearable placeholder="Seleccione"
                            no-data-text="No hay opciones" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="category in localCategories" :key="category.id" :label="category.name"
                                :value="category.name" />
                            <InputError :message="sizeForm.errors.category" />
                        </el-select>
                    </div>
                    <div>
                        <InputLabel value="Nombre de la talla *" />
                        <el-input v-model="sizeForm.name" placeholder="Ej. Triple extra grande" :maxlength="100"
                            required clearable />
                        <InputError :message="sizeForm.errors.name" />
                    </div>
                    <div>
                        <InputLabel value="Abreviación (opcional)" />
                        <el-input v-model="sizeForm.short" placeholder="Ej. 3XL" :maxlength="100" required clearable />
                        <InputError :message="sizeForm.errors.short" />
                    </div>
                </form>
            </template>
            <template #footer>
                <div class="flex items-center space-x-2">
                    <CancelButton @click="showSizeFormModal = false" :disabled="sizeLoading">Cancelar
                    </CancelButton>
                    <PrimaryButton @click="storeSize()" :disabled="sizeLoading">Crear</PrimaryButton>
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
import { addOrUpdateItem } from "@/dbService.js";
import axios from 'axios';

export default {
    data() {
        const form = useForm({
            name: null,
            code: null,
            public_price: null,
            currency: '$MXN',
            cost: null,
            description: null,
            category_id: null,
            imageCover: null,
            has_inventory_control: false,
            sizes: [
                {
                    size_id: null,
                    current_stock: 1,
                    min_stock: null,
                    max_stock: null,
                }
            ],
        });

        const categoryForm = useForm({
            name: null,
        });

        const sizeForm = useForm({
            name: null,
            short: null,
            category: null,
        });

        return {
            form,
            sizeForm,
            categoryForm,
            localCategories: this.categories,
            localSizes: this.sizes,
            showCategoryFormModal: false, //muestra formulario para agregar categoría
            showSizeFormModal: false, //muestra formulario para agregar tallas
            currencies: [
                { value: "Peso Mexicano", label: "$MXN" },
                { value: "Dolar Americano", label: "$USD" },
            ],
            // Permisos de rol actual
            canSeeCost: ['Administrador', 'Almacenista'].includes(this.$page.props.auth.user.rol),
            productsLimit: this.$page.props.auth.user.store.plan == 'Plan Básico' ? 1500 : 3000,
            // cargas
            sizeLoading: false,
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
        products_quantity: Number, // para validar los productos limite
        categories: Array,
        sizes: Array,
    },
    computed: {
        getCategorySizes() {
            const selectedCategory = this.localCategories.find(item => item.id == this.form.category_id);

            return this.localSizes.filter(item => item.category == selectedCategory?.name) ?? [];
        },
    },
    methods: {
        openSizeModal() {
            if (this.form.category_id) {
                this.sizeForm.category = this.categories.find(item => item.id == this.form.category_id).name;
            }

            this.showSizeFormModal = true;
        },
        handleCategory() {
            this.form.sizes.forEach(item => {
                item.size_id = null;
            });

            this.$nextTick(() => {
                this.$refs.size0[0].focus();
            });
        },
        deleteSize(index) {
            this.form.sizes.splice(index, 1);
        },
        addSize() {
            const newSize = {
                size_id: null,
                current_stock: 1,
                min_stock: null,
                max_stock: null,
            }

            this.form.sizes.push(newSize);
        },
        saveImage(image) {
            this.form.imageCover = image;
        },
        async store() {
            try {
                this.form.post(route("boutique-products.store"), {
                    onSuccess: async () => {
                        // guardar nuevo producto a IndexedDB
                        // Obtener producto mas reciente agregado
                        const response = await axios.get(route('products.get-all-for-indexedDB'));
                        const product = response.data.local_products[0];

                        // agregar a indexedDB
                        await addOrUpdateItem('products', product);

                        // toast
                        this.$notify({
                            title: "Correcto",
                            message: "",
                            type: "success",
                        });
                    },
                });
            } catch (error) {
                console.error(error);
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
                    this.localCategories.unshift(response.data.item);
                    this.showCategoryFormModal = false;
                    this.categoryForm.reset();
                }
            } catch (error) {
                console.log(error)
            }
        },
        async storeSize() {
            this.sizeLoading = true;
            try {
                const response = await axios.post(route('sizes.store'), {
                    name: this.sizeForm.name,
                    short: this.sizeForm.short,
                    category: this.sizeForm.category
                });
                if (response.status === 200) {
                    this.$notify({
                        title: "Talla creada",
                        message: "",
                        type: "success",
                    });
                    this.localSizes.unshift(response.data.item);
                    this.showSizeFormModal = false;
                    this.sizeForm.reset();
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.sizeLoading = false;
            }
        },
    }
}
</script>