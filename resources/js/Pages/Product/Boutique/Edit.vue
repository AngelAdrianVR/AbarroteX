<template>
    <AppLayout title="Editar producto">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="update"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Editar producto</h1>
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
                    <el-select class="w-1/2" @change="handleCategory" filterable v-model="form.category_id" clearable
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
                    <article v-for="(item, index) in form.colors" :key="index"
                        class="mb-2 border rounded-[10px] *:px-3 pb-2">
                        <!-- <div v-if="item.color" class="h-2 rounded-t-[10px]" :style="{ backgroundColor: item.color }">
                        </div> -->
                        <header class="relative">
                            <i v-if="item.color" class="fa-solid fa-shirt text-lg" :style="{ color: item.color }"></i>
                            <div class="absolute -top-2 -right-2">
                                <el-popconfirm v-if="form.colors.length > 1" confirm-button-text="Si"
                                    cancel-button-text="No" icon-color="#373737" :title="'¿Desea eliminar?'"
                                    @confirm="deleteColor(index)">
                                    <template #reference>
                                        <button type="button"
                                            class="size-6 bg-grayF2 flex items-center justify-center rounded-full">
                                            <i class="fa-regular fa-trash-can text-sm text-primary"></i>
                                        </button>
                                    </template>
                                </el-popconfirm>
                            </div>
                        </header>
                        <div class="grid grid-cols-2 gap-3 mt-2">
                            <div>
                                <div class="w-full flex items-center justify-between">
                                    <InputLabel value="Color *" />
                                    <button @click="openColorModal" v-if="index == 0" type="button"
                                        class="text-primary text-xs">
                                        Agregar nuevo color
                                    </button>
                                </div>
                                <el-select v-model="item.color" placeholder="Selecciona un color">
                                    <el-option v-for="(localColor, indexColor) in localColors" :key="indexColor"
                                        :label="localColor.name" :value="localColor.color">
                                        <div class="flex items-center">
                                            <el-tag :color="localColor.color" style="margin-right: 8px" size="small" />
                                            <span class="text-gray37">{{ localColor.name }}</span>
                                        </div>
                                    </el-option>
                                </el-select>
                                <InputError :message="form.errors[`${index}.color`]" />
                            </div>
                            <div>
                                <div class="w-full flex items-center justify-between">
                                    <InputLabel value="Tallas *" />
                                    <button @click="openSizeModal" v-if="index == 0" type="button"
                                        class="text-primary text-xs">
                                        Crear talla
                                    </button>
                                </div>
                                <el-select :ref="'size' + index" multiple filterable
                                    @change="handleChangeSizes(index, item)" v-model="selectedSizes[index]" clearable
                                    placeholder="Selecciona las tallas" collapse-tags collapse-tags-tooltip
                                    :no-data-text="form.category_id ? 'No hay tallas registradas en la categoria seleccionada' : 'Primero seleccione la categoria'"
                                    no-match-text="No se encontraron coincidencias">
                                    <el-option v-for="size in getCategorySizes" :key="size.id" :label="size.name"
                                        :value="size.name">
                                        <p class="flex items-center justify-between">
                                            <span>{{ size.name }}</span>
                                            <span v-if="size.short" class="text-[10px] text-gray99">
                                                ({{ size.short }})
                                            </span>
                                        </p>
                                    </el-option>
                                </el-select>
                                <!-- <InputError :message="form.errors[`colors.${index}.sizes.${index}.size_id`]" /> -->
                            </div>
                        </div>
                        <div class="grid grid-cols-6 gap-3 mt-3">
                            <p v-if="item.sizes.length" class="col-span-full text-[13px] text-gray37">
                                En los campos de abajo ingresa las existencias de cada talla
                                y el color seleccionado
                            </p>
                            <div v-for="(size, index2) in item.sizes" :key="index2">
                                <div>
                                    <InputLabel>
                                        <p class="w-full truncate" :title="size.size_name + ' *'">{{ size.size_name }} *
                                        </p>
                                    </InputLabel>
                                    <el-input v-model="size.current_stock" placeholder="Stock actual"
                                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                        :parser="(value) => value.replace(/[^\d.]/g, '')" />
                                    <InputError
                                        :message="form.errors[`colors.${index}.sizes.${index2}.current_stock`]" />
                                </div>
                                <div v-if="form.has_inventory_control">
                                    <InputLabel value="Cantidad mínima" />
                                    <el-input v-model="size.min_stock" placeholder="Mínimo permitido"
                                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                        :parser="(value) => value.replace(/[^\d.]/g, '')" />
                                    <InputError :message="form.errors[`colors.${index}.sizes.${index2}.min_stock`]" />
                                </div>
                                <div v-if="form.has_inventory_control">
                                    <InputLabel value="Cantidad máxima" />
                                    <el-input v-model="size.max_stock" placeholder="Máximo permitido"
                                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                        :parser="(value) => value.replace(/[^\d.]/g, '')" />
                                    <InputError :message="form.errors[`colors.${index}.sizes.${index2}.max_stock`]" />
                                </div>
                            </div>
                        </div>
                        <InputError :message="form.errors[`colors.${index}.sizes`]" />
                    </article>
                    <button @click="addColor" type="button" class="text-primary ml-3">+ Añadir color</button>
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
                    <InputFilePreview @imagen="saveImage($event); form.imageCoverCleared = false"
                        @cleared="form.imageCover = null; form.imageCoverCleared = true"
                        :imageUrl="products[0].media[0]?.original_url" />
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

        <!-- colors form -->
        <DialogModal :show="showColorFormModal" @close="showColorFormModal = false">
            <template #title> Agregar nuevos colores </template>
            <template #content>
                <p class="text-gray99 mb-3">En este apartado puedes crear colores que no se encuentren en la lista</p>
                <form @submit.prevent="storeColor" class="space-y-1">
                    <section v-for="(item, index) in colorForm.list" :key="index" class="flex space-x-3">
                        <div class="w-[50%]">
                            <InputLabel v-if="index == 0" value="Nombre del color *" />
                            <el-input v-model="item.name" placeholder="Ej. Beige" :maxlength="100" required clearable />
                            <InputError :message="colorForm.errors[`list.${index}.name`]" />
                        </div>
                        <div class="w-[25%]">
                            <InputLabel v-if="index == 0" value="Color *" />
                            <el-color-picker v-model="item.color" />
                            <InputError :message="colorForm.errors[`list.${index}.color`]" />
                        </div>
                        <div class="w-[20%]" :class="index == 0 ? 'mt-6' : 'mt-1'">
                            <el-popconfirm v-if="colorForm.list.length > 1" confirm-button-text="Si"
                                cancel-button-text="No" icon-color="#373737"
                                :title="'¿Desea eliminar este color de la lista?'"
                                @confirm="colorForm.list.splice(index, 1)">
                                <template #reference>
                                    <button type="button">
                                        <i class="fa-regular fa-trash-can text-sm text-primary"></i>
                                    </button>
                                </template>
                            </el-popconfirm>
                        </div>
                    </section>
                    <button @click="colorForm.list.push({ name: null, color: null })" type="button"
                        class="text-primary ml-3 mt-5">
                        + Añadir otro color
                    </button>
                </form>
            </template>
            <template #footer>
                <div class="flex items-center space-x-2">
                    <CancelButton @click="showColorFormModal = false" :disabled="colorForm.processing">Cancelar
                    </CancelButton>
                    <PrimaryButton @click="storeColor()" :disabled="colorForm.processing">Crear</PrimaryButton>
                </div>
            </template>
        </DialogModal>

        <!-- sizes form -->
        <DialogModal :show="showSizeFormModal" @close="showSizeFormModal = false">
            <template #title> Agregar talla </template>
            <template #content>
                <p class="text-gray99 mb-3">En este apartado puedes crear tallas que no se encuentren en la lista</p>
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
            name: this.products[0]?.name,
            code: null,
            public_price: this.products[0]?.public_price,
            currency: '$MXN',
            cost: this.products[0]?.cost,
            description: this.products[0]?.description,
            category_id: this.products[0]?.category_id,
            imageCover: null,
            has_inventory_control: Boolean(this.products[0]?.has_inventory_control),
            colors: [],
        });

        const categoryForm = useForm({
            name: null,
        });

        const sizeForm = useForm({
            name: null,
            short: null,
            category: null,
        });

        const colorForm = useForm({
            list: [
                {
                    name: null,
                    color: null,
                }
            ]
        });

        return {
            form,
            sizeForm,
            colorForm,
            categoryForm,
            localCategories: this.categories,
            localSizes: this.sizes,
            localColors: this.colors,
            showCategoryFormModal: false, //muestra formulario para agregar categoría
            showSizeFormModal: false, //muestra formulario para agregar tallas
            currencies: [
                { value: "Peso Mexicano", label: "$MXN" },
                { value: "Dolar Americano", label: "$USD" },
            ],
            selectedSizes: [[]], // Temporal para el selector de tallas
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
        products: Array,
        categories: Array,
        sizes: Array,
        colors: Array,
    },
    computed: {
        getCategorySizes() {
            const selectedCategory = this.localCategories.find(item => item.id == this.form.category_id);
            return this.localSizes.filter(item => item.category == selectedCategory?.name) ?? [];
        },
    },
    methods: {
        handleChangeSizes(index, item) {
            // Limpiar las tallas antiguas y agregar las nuevas seleccionadas
            item.sizes = this.selectedSizes[index].map(size => {
                // Verificar si la talla ya existe en item.sizes, si no, crear el objeto
                const existingSize = item.sizes.find(s => s.size_name === size);
                if (!existingSize) {
                    return {
                        size_id: this.localSizes.find(i => i.name === size)?.id,
                        size_name: size,
                        current_stock: 1,
                        min_stock: null,
                        max_stock: null,
                    };
                }
                return existingSize;
            });
        },
        openSizeModal() {
            if (this.form.category_id) {
                this.sizeForm.category = this.categories.find(item => item.id == this.form.category_id).name;
            }

            this.showSizeFormModal = true;
        },
        openColorModal() {
            this.showColorFormModal = true;
        },
        handleCategory() {
            this.form.colors.forEach(item => {
                item.sizes = [];
            });

            this.selectedSizes = Array(this.form.colors.length).fill([]);

            this.$nextTick(() => {
                this.$refs.size0[0].focus();
            });
        },
        deleteColor(index) {
            this.form.colors.splice(index, 1);
        },
        addColor() {
            const newColor = {
                color: null,
                sizes: [],
            }

            this.selectedSizes.push([]);
            this.form.colors.push(newColor);
        },
        saveImage(image) {
            this.form.imageCover = image;
        },
        getBaseCode() {
            if (!this.products[0].code) {
                return null;
            }
            let splited = this.products[0].code.split('-');
            splited.pop(); // Elimina el último elemento del array
            splited.pop(); // Elimina el último elemento del array
            return splited.join('-'); // Une los elementos restantes con guiones medios
        },
        initialFormFill() {
            this.form.code = this.getBaseCode();
            this.selectedSizes = []; // Inicializar el array de selectedSizes

            this.products.forEach(element => {
                // Busca si ya existe un color en form.colors
                const existingColor = this.form.colors.find(c => c.color === element.additional.color.color);

                const size = {
                    size_id: element.additional.size.id,
                    size_name: element.additional.size.name,
                    current_stock: element.current_stock,
                    min_stock: element.min_stock,
                    max_stock: element.max_stock,
                };

                if (existingColor) {
                    // Si el color ya existe, solo añade la talla al array de sizes
                    existingColor.sizes.push(size);

                    // Añadir el nombre de la talla al array de selectedSizes correspondiente al color
                    const colorIndex = this.form.colors.indexOf(existingColor);
                    this.selectedSizes[colorIndex].push(element.additional.size.name);
                } else {
                    // Si el color no existe, crea un nuevo objeto color con la talla
                    const newColor = {
                        id: element.id,
                        color: element.additional.color.color,
                        sizes: [size] // Crea el array con la talla
                    };

                    this.form.colors.push(newColor);

                    // Añadir un nuevo array de tallas para este color en selectedSizes
                    this.selectedSizes.push([element.additional.size.name]);
                }
            });
        },
        storeColor() {
            this.colorForm.post(route('colors.store'), {
                onSuccess: () => {
                    this.colorForm.list.forEach(element => {
                        this.localColors.unshift(element);
                    });
                    const oneColorAdded = this.colorForm.list.length == 1;
                    this.showColorFormModal = false;
                    this.$notify({
                        title: oneColorAdded ? "Color agregado" : "Colores agregados",
                        type: "success"
                    });
                    this.colorForm.reset();
                }
            });
        },
        async update() {
            try {
                if (this.form.imageCover) {
                    this.form.post(route("boutique-products.update-with-media", this.products[0].id), {
                        method: '_put',
                        onSuccess: async () => {
                            // guardar nuevo producto a IndexedDB
                            // Obtener producto mas reciente agregado
                            const response = await axios.get(route('products.get-all-for-indexedDB'));
                            const product = response.data.local_products.find(item => item.id.split('_')[1] == this.products[0].id);
                            // actualizar a indexedDB
                            if (product) {
                                addOrUpdateItem('products', product);
                            }

                            this.$notify({
                                title: "Correcto",
                                message: 'Se ha editado el producto ' + this.products[0].name,
                                type: "success",
                            });
                        },
                    });
                } else {
                    this.form.put(route("boutique-products.update", this.products[0].id), {
                        onSuccess: async () => {
                            // guardar nuevo producto a IndexedDB
                            // Obtener producto que coincida con el id editado
                            const response = await axios.get(route('products.get-all-for-indexedDB'));
                            const product = response.data.local_products.find(item => item.id.split('_')[1] == this.products[0].id);

                            // actualizar a indexedDB
                            if (product) {
                                addOrUpdateItem('products', product);
                            }

                            this.$notify({
                                title: "Correcto",
                                message: 'Se ha editado el producto ' + this.products[0].name,
                                type: "success",
                            });
                        },
                    });
                }
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
    },
    mounted() {
        this.initialFormFill();
    }
}
</script>
<style scoped>
.el-tag {
    border: none;
    aspect-ratio: 1;
}
</style>