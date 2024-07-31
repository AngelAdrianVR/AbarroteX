<template>
    <AppLayout :title="products[0].name">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="lg:flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Productos</h1>
                <div class="flex items-center space-x-2 my-2 lg:my-0">
                    <ThirthButton @click="openEntryModal">
                        Entrada de producto
                    </ThirthButton>
                    <PrimaryButton @click="$inertia.get(route('boutique-products.edit', encodedId))"
                        class="!rounded-full">
                        Editar</PrimaryButton>
                    <PrimaryButton @click="$inertia.get(route('boutique-products.create'))" class="!rounded-full">
                        <i class="fa-solid fa-plus"></i> Nuevo
                    </PrimaryButton>
                </div>
            </div>
            <div class="lg:w-1/4 relative">
                <input v-model="searchQuery" @focus="searchFocus = true" @blur="handleBlur" @input="searchProducts"
                    class="input w-full pl-9" placeholder="Buscar código o nombre de producto" type="search">
                <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                <!-- Resultados de la búsqueda -->
                <div v-if="searchFocus && searchQuery"
                    class="absolute mt-1 bg-white border border-gray-300 rounded shadow-lg w-full">
                    <Loading2 v-if="searchLoading" class="my-3" />
                    <ul v-else-if="Object.keys(productsFound)?.length > 0">
                        <li @click.stop="handleProductSelected(set)" v-for="(set, index) in Object.values(productsFound)"
                            :key="index" class="hover:bg-gray-200 cursor-default text-sm px-5 py-2">{{
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
        <!-- -------------- Modal starts----------------------- -->
        <Modal :show="entryProductModal" @close="entryProductModal = false">
            <form @submit.prevent="entryProduct" class="p-4 relative">
                <i @click="entryProductModal = false"
                    class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>
                <h1 class="font-bold my-4">Ingresar producto a almacén</h1>
                <section v-for="(item, index) in form.sizes" :key="index" class="mb-2 mx-2 flex items-center space-x-3">
                    <div class="w-[48%]">
                        <InputLabel value="Talla *" />
                        <el-select @change="handleChangeSize(item)" filterable v-model="item.product_id" clearable placeholder="Seleccione"
                            no-data-text="No hay opciones" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="size in getAvailableSizes" :key="size.product_id" :label="size.name"
                                :value="size.product_id" :disabled="form.sizes.some(item => item.product_id == size.product_id)">
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
                        <InputLabel value="Cantidad *" />
                        <el-input v-model="item.quantity" ref="quantityInput"
                            placeholder="Cantidad que ingresa de esta talla">
                        </el-input>
                        <InputError :message="form.errors[`sizes.${index}.quantity`]" />
                    </div>
                    <div class="w-[4%] flex justify-end mt-5">
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
                </section>
                <div class="flex">
                    <button @click="addSize" type="button" class="text-primary text-sm">+ Añadir talla</button>
                </div>
                <div class="flex justify-end space-x-3 pt-5 py-2">
                    <CancelButton @click="entryProductModal = false">Cancelar</CancelButton>
                    <PrimaryButton :disabled="form.processing || incompleteForm || cashAmountMessage" @click="entryProduct"
                        class="!rounded-full">
                        Ingresar producto {{ form.siezes }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
        <!-- --------------------------- Modal ends ------------------------------------ -->
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductInfo from './Tabs/ProductInfo.vue';
import ProductHistorical from './Tabs/ProductHistorical.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import Loading2 from '@/Components/MyComponents/Loading2.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import Back from "@/Components/MyComponents/Back.vue";
import axios from 'axios';
import { useForm, Link } from "@inertiajs/vue3";
import { syncIDBProducts } from "@/dbService.js";

export default {
    data() {
        const form = useForm({
            sizes: [
                {
                    product_id: null,
                    size_name: null,
                    quantity: null,
                }
            ],
        });
        return {
            form,
            encodedId: null, //id codificado
            searchQuery: this.products[0].name,
            searchFocus: false,
            productsFound: [this.products[0]],
            entryProductModal: false,
            // loading
            entryLoading: false,
            searchLoading: false,
            // control de inventario activado
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
            // validaciones
            cashAmountMessage: null,
            // tabs
            activeTab: '1',
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        CancelButton,
        ThirthButton,
        InputLabel,
        InputError,
        Loading2,
        Modal,
        Back,
        ProductInfo,
        ProductHistorical,
        Link,
    },
    props: {
        products: Array,
        cash_register: Object,
    },
    computed: {
        getAvailableSizes() {
            return this.products.map(item => {
                return {
                    product_id: item.id,
                    name: item.additional?.name,
                    short: item.additional?.short,
                }
            });
        },
        incompleteForm() {
            return this.form.sizes.some(size => !size.product_id || !size.quantity);
        }
    },
    methods: {
        deleteSize(index) {
            this.form.sizes.splice(index, 1);
        },
        addSize() {
            const newSize = {
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
        entryProduct() {
            if (this.entryLoading) return;

            this.entryLoading = true;
            this.form.put(route('boutique-products.entry'), {
                onSuccess: () => {
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
    },
    mounted() {
        this.setActiveTabFromURL();
        this.encodeId(this.products[0].id);
        this.searchProducts();
    },
}
</script>
