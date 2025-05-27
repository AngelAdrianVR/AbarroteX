<template>
    <AppLayout :title="title">
        <Loading v-if="loading" class="mt-14" />
        <div v-else class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="lg:flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Productos</h1>
                <div class="flex items-center space-x-1 my-2 lg:my-0">
                    <PrimaryButton @click="openEntryModal">Inventario</PrimaryButton>
                    <button @click="$inertia.get(route('boutique-products.edit', encodedId))" title="Editar producto"
                        class="flex items-center justify-center bg-[#EDEDED] text-primary size-8 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button @click="$inertia.get(route('boutique-products.create'))" title="Crear producto"
                        class="flex items-center justify-center bg-[#EDEDED] text-primary size-8 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="lg:w-1/4 relative">
                <input v-model="searchQuery" @focus="searchFocus = true" @blur="handleBlur" @input="searchProducts"
                    class="input w-full pl-9" placeholder="Buscar código o nombre de producto" type="search">
                <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                <!-- Resultados de la búsqueda -->
                <div v-if="searchFocus && searchQuery"
                    class="absolute mt-1 bg-white border border-gray-300 rounded shadow-lg w-full z-10">
                    <SmallLoading v-if="searchLoading" class="my-3 mx-auto" />
                    <ul v-else-if="Object.keys(productsFound)?.length > 0">
                        <li @click.stop="handleProductSelected(set)"
                            v-for="(set, index) in Object.values(productsFound)" :key="index"
                            class="hover:bg-gray-200 cursor-default text-sm px-5 py-2">{{
                                set[0]?.global_product_id ? set[0]?.global_product?.name : set[0]?.name }}</li>
                    </ul>
                    <p v-else class="text-center text-sm text-gray-600 px-5 py-2">No se encontraron coincidencias</p>
                </div>
            </div>
            <div class="mt-5">
                <Back :to="route('boutique-products.index')" />
            </div>
            <!-- Info de producto -->
            <div class="md:grid grid-cols-2 xl:grid-cols-3 gap-x-10 mx-2 md:mx-6">
                <!-- fotografia de producto -->
                <section class="mt-7">
                    <figure class="border h-64 md:h-96 border-grayD9 rounded-lg flex justify-center items-center">
                        <img v-if="products[0].media?.length" class="h-52 md:h-80 mx-auto object-contain"
                            :src="products[0].media[0]?.original_url" alt="">
                        <div v-else>
                            <i class="fa-regular fa-image text-9xl text-gray-200"></i>
                            <p class="text-sm text-gray-300">Imagen no disponible</p>
                        </div>
                    </figure>
                </section>
                <!-- informacion de producto -->
                <section class="xl:col-span-2 my-3 lg:my-0">
                    <!-- Pestañas -->
                    <el-tabs class="" v-model="activeTab" @tab-click="updateURL">
                        <el-tab-pane label="Información del producto" name="1">
                            <ProductInfo :products="products" />
                        </el-tab-pane>
                        <el-tab-pane label="Historial de movimientos" name="2">
                            <ProductHistorical ref="historyTab" :products="products" />
                        </el-tab-pane>
                    </el-tabs>
                </section>
            </div>
        </div>
        <DialogModal :show="entryProductModal" @close="entryProductModal = false">
            <template #title>
                <h1 class="font-bold">Inventario de producto</h1>
            </template>
            <template #content>
                <p class="text-xs">
                    En caso de requerir agregar una talla nueva, ve a
                    <a :href="route('boutique-products.edit', encodedId)" target="_blank"
                        class="text-primary underline">editar
                        el producto</a> y una vez agregada presiona el botón
                    de refrescar lista de tallas:
                </p>
                <el-tabs v-model="inventoryActiveTab" @tab-click="handleInventoryTabClick">
                    <el-tab-pane label="Entrada" name="1">
                        <section v-for="(item, index) in form.sizes" :key="index"
                            class="mb-2 mx-2 flex items-center space-x-3 mt-3">
                            <div class="w-[48%]">
                                <InputLabel v-if="index == 0">
                                    <div class="flex items-center justify-between">
                                        <span>Talla *</span>
                                        <el-tooltip content="Refrescar lista de tallas" placement="right">
                                            <button @click="fetchProductsByName()" type="button"
                                                class="size-5 rounded-full bg-grayD9 text-gray37 flex items-center justify-center disabled:animate-spin disabled:opacity-50"
                                                :disabled="fetching">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-3">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                </svg>
                                            </button>
                                        </el-tooltip>
                                    </div>
                                </InputLabel>
                                <el-select @change="handleChangeSize(item)" filterable v-model="item.product_id"
                                    clearable placeholder="Seleccione" no-data-text="No hay opciones"
                                    no-match-text="No se encontraron coincidencias">
                                    <el-option v-for="size in getAvailableSizes" :key="size.product_id"
                                        :label="size.name" :value="size.product_id"
                                        :disabled="form.sizes.some(item => item.product_id == size.product_id)">
                                        <p class="flex items-center justify-between">
                                            <span>{{ size.name }}</span>
                                            <span v-if="size.short" class="text-[10px] text-gray99">
                                                ({{ size.short }})
                                            </span>
                                        </p>
                                    </el-option>
                                </el-select>
                                <InputError :message="form.errors[`sizes.${index}.product_id`]" />
                            </div>
                            <div class="w-[48%]">
                                <InputLabel v-if="index == 0" value="Cantidad *" />
                                <el-input v-model="item.quantity" ref="quantityInput"
                                    placeholder="Cantidad que ingresa de esta talla">
                                </el-input>
                                <InputError :message="form.errors[`sizes.${index}.quantity`]" />
                            </div>
                            <div class="w-[4%] flex justify-end">
                                <el-popconfirm v-if="form.sizes.length > 1" confirm-button-text="Si"
                                    cancel-button-text="No" icon-color="#373737"
                                    :title="'¿Desea eliminar la talla seleccionada?'" @confirm="deleteSize(index)">
                                    <template #reference>
                                        <button type="button">
                                            <i class="fa-regular fa-trash-can text-sm text-primary"></i>
                                        </button>
                                    </template>
                                </el-popconfirm>
                            </div>
                        </section>
                        <div class="flex">
                            <button @click="addSize" type="button" class="text-primary text-sm ml-3">+ Añadir
                                talla</button>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane label="Salida" name="2">
                        <section v-for="(item, index) in form.sizes" :key="index"
                            class="mb-2 mx-2 flex items-center space-x-3 mt-3">
                            <div class="w-[36%]">
                                <InputLabel v-if="index == 0">
                                    <div class="flex items-center justify-between">
                                        <span>Talla *</span>
                                        <el-tooltip content="Refrescar lista de tallas" placement="right">
                                            <button @click="fetchProductsByName()" type="button"
                                                class="size-5 rounded-full bg-grayD9 text-gray37 flex items-center justify-center disabled:animate-spin disabled:opacity-50"
                                                :disabled="fetching">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-3">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                </svg>
                                            </button>
                                        </el-tooltip>
                                    </div>
                                </InputLabel>
                                <el-select @change="handleChangeSize(item)" filterable v-model="item.product_id"
                                    clearable placeholder="Seleccione" no-data-text="No hay opciones"
                                    no-match-text="No se encontraron coincidencias">
                                    <el-option v-for="size in getAvailableSizes" :key="size.product_id"
                                        :label="size.name" :value="size.product_id"
                                        :disabled="form.sizes.some(item => item.product_id == size.product_id)">
                                        <p class="flex items-center justify-between">
                                            <span>{{ size.name }}</span>
                                            <span v-if="size.short" class="text-[10px] text-gray99">
                                                ({{ size.short }})
                                            </span>
                                        </p>
                                    </el-option>
                                </el-select>
                                <InputError :message="form.errors[`sizes.${index}.product_id`]" />
                            </div>
                            <div class="w-[21%]">
                                <InputLabel v-if="index == 0" value="Cantidad *" />
                                <el-input v-model="item.quantity" ref="quantityInput"
                                    placeholder="Cantidad que sale de esta talla">
                                </el-input>
                                <InputError :message="form.errors[`sizes.${index}.quantity`]" />
                            </div>
                            <div class="w-[36%]">
                                <InputLabel v-if="index == 0" value="Motivo *" />
                                <el-select filterable v-model="item.concept" clearable placeholder="Seleccione"
                                    no-data-text="No hay opciones registradas"
                                    no-match-text="No se encontraron coincidencias">
                                    <el-option v-for="item in outConcepts" :key="item" :label="item" :value="item" />
                                </el-select>
                            </div>
                            <div class="w-[4%] flex justify-end">
                                <el-popconfirm v-if="form.sizes.length > 1" confirm-button-text="Si"
                                    cancel-button-text="No" icon-color="#373737"
                                    :title="'¿Desea eliminar la talla seleccionada?'" @confirm="deleteSize(index)">
                                    <template #reference>
                                        <button type="button">
                                            <i class="fa-regular fa-trash-can text-sm text-primary"></i>
                                        </button>
                                    </template>
                                </el-popconfirm>
                            </div>
                        </section>
                        <div class="flex">
                            <button @click="addSize" type="button" class="text-primary text-sm ml-3">+ Añadir
                                talla</button>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </template>
            <template #footer>
                <div class="flex space-x-1">
                    <CancelButton @click="entryProductModal = false">Cancelar</CancelButton>
                    <PrimaryButton :disabled="form.processing || incompleteForm || cashAmountMessage"
                        @click="sendInventoryMovement" class="!rounded-full">
                        Continuar
                    </PrimaryButton>
                </div>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductInfo from './Tabs/ProductInfo.vue';
import ProductHistorical from './Tabs/ProductHistorical.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import SmallLoading from '@/Components/MyComponents/SmallLoading.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Back from "@/Components/MyComponents/Back.vue";
import axios from 'axios';
import { useForm, Link } from "@inertiajs/vue3";
import { syncIDBProducts } from "@/dbService.js";
import DialogModal from '@/Components/DialogModal.vue';

export default {
    data() {
        const form = useForm({
            sizes: [
                {
                    concept: 'Ajuste de inventario', // concepto por defecto
                    product_id: null,
                    size_name: null,
                    quantity: null,
                }
            ],
        });
        return {
            form,
            encodedId: null, //id codificado
            searchFocus: false,
            entryProductModal: false,
            products: [],
            title: '',
            searchQuery: '',
            // loading
            entryLoading: false,
            searchLoading: false,
            fetching: false,
            loading: true,
            // control de inventario activado
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
            // validaciones
            cashAmountMessage: null,
            // tabs
            activeTab: '1',
            //inventario
            inventoryActiveTab: '1',
            outConcepts: [
                'Ajuste de inventario',
                'Consumo del negocio',
                'Devolución a proveedor',
                'Mal estado',
                'Muestra gratis',
                'Regalo por compra',
                'Otro'
            ],
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        CancelButton,
        ThirthButton,
        InputLabel,
        InputError,
        SmallLoading,
        Loading,
        DialogModal,
        Back,
        ProductInfo,
        ProductHistorical,
        Link,
    },
    props: {
        product_name: String,
        cash_register: Object,
    },
    computed: {
        getAvailableSizes() {
            return this.products.map(item => {
                return {
                    product_id: item.id,
                    name: item.additional?.size?.name,
                    short: item.additional?.size?.short,
                }
            });
        },
        incompleteForm() {
            return this.form.sizes.some(size => !size.product_id || !size.quantity);
        }
    },
    methods: {
        sendInventoryMovement() {
            if (this.inventoryActiveTab == '1') {
                this.entryProduct();
            } else {
                this.outProduct();
            }
        },
        handleInventoryTabClick(tab) {

        },
        deleteSize(index) {
            this.form.sizes.splice(index, 1);
        },
        addSize() {
            const newSize = {
                concept: 'Ajuste de inventario',
                product_id: null,
                size_name: null,
                quantity: null,
            }

            this.form.sizes.push(newSize);
        },
        updateURL(tab) {
            const params = new URLSearchParams(window.location.search);
            params.set('tab', tab.props.name);
            window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
        },
        setActiveTabFromURL() {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            if (tab) {
                this.activeTab = tab;
            }
        },
        handleChangeSize(item) {
            item.size_name = this.getAvailableSizes.find(element => element.product_id == item.product_id)?.name;
        },
        handleBlur() {
            // Introducir un retraso para dar tiempo al evento click de ejecutarse antes del blur
            setTimeout(() => {
                this.searchFocus = false;
            }, 100);
        },
        openEntryModal() {
            this.entryProductModal = true;
        },
        outProduct() {
            if (this.entryLoading) return;

            this.entryLoading = true;
            this.form.put(route('boutique-products.out'), {
                onSuccess: () => {
                    this.fetchProductsByName(false);
                    // actualizar current stock de producto en indexedDB si el seguimiento de inventario esta activo
                    // if (this.isInventoryOn) {
                    syncIDBProducts();
                    // }

                    this.form.reset();
                    this.entryProductModal = false;
                    this.$notify({
                        title: 'Correcto',
                        message: '',
                        type: 'success',
                    });
                    this.$refs.historyTab.fetchHistory();

                },
                onFinish: () => this.entryLoading = false,
            });
        },
        entryProduct() {
            if (this.entryLoading) return;

            this.entryLoading = true;
            this.form.put(route('boutique-products.entry'), {
                onSuccess: () => {
                    this.fetchProductsByName(false);
                    // actualizar current stock de producto en indexedDB si el seguimiento de inventario esta activo
                    // if (this.isInventoryOn) {
                    syncIDBProducts();
                    // }

                    this.form.reset();
                    this.entryProductModal = false;
                    this.$notify({
                        title: 'Correcto',
                        message: '',
                        type: 'success',
                    });
                    this.$refs.historyTab.fetchHistory();

                },
                onFinish: () => this.entryLoading = false,
            });
        },
        handleProductSelected(set) {
            const encodedId = btoa(set[0].id.toString());
            if (set[0].global_product_id) {
                this.$inertia.get(route('global-product-store.show', encodedId))
            } else {
                this.$inertia.get(route('boutique-products.show', encodedId))
            }
        },
        encodeId(id) {
            const encodedId = btoa(id.toString());
            this.encodedId = encodedId;
        },
        async searchProducts() {
            this.searchLoading = true;
            try {
                const response = await axios.get(route('boutique-products.search'), { params: { query: this.searchQuery } });
                if (response.status == 200) {
                    this.productsFound = response.data.items;
                }

            } catch (error) {
                console.log(error);
            } finally {
                this.searchLoading = false;
            }
        },
        async fetchProductsByName(loadFullPage = false) {
            if (loadFullPage) {
                this.loading = true;
            } else {
                this.fetching = true;
            }
            try {
                const response = await axios.get(route('boutique-products.get-by-name', this.product_name));
                if (response.status == 200) {
                    this.products = response.data.items;
                }

            } catch (error) {
                console.log(error);
            } finally {
                if (loadFullPage) {
                    this.loading = false;
                } else {
                    this.fetching = false;
                }
            }
        },
    },
    async mounted() {
        await this.fetchProductsByName(true);
        this.setActiveTabFromURL();
        this.encodeId(this.products[0].id);
        this.searchProducts();

        // iniciar variables
        this.searchQuery = this.products[0].name;
        this.productsFound = [this.products[0]];
        this.title = this.products[0].name;
    },
}
</script>
