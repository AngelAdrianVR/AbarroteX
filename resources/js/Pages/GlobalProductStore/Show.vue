<template>
    <AppLayout :title="global_product_store.global_product?.name">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="lg:flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Productos</h1>
                <div class="flex items-center space-x-3 my-2 lg:my-0">
                    <ThirthButton @click="openEntryModal">Entrada de producto</ThirthButton>
                    <PrimaryButton @click="$inertia.get(route('global-product-store.edit', encodedId))"
                        class="!rounded-full">Editar</PrimaryButton>
                    <PrimaryButton @click="goToCreate()" class="!rounded-full">
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
                    <ul v-else-if="productsFound?.length > 0">
                        <li @click.stop="handleProductSelected(product)" v-for="(product, index) in productsFound"
                            :key="index" class="hover:bg-gray-200 cursor-default text-sm px-5 py-2">{{
                                product.global_product_id ? product.global_product?.name : product.name }}</li>
                    </ul>
                    <p v-else class="text-center text-sm text-gray-600 px-5 py-2">No se encontraron coincidencias</p>
                </div>
            </div>
            <div class="mt-5">
                <Back :to="getRoute()" />
            </div>

            <!-- Info de producto -->
            <div class="md:grid grid-cols-2 xl:grid-cols-3 gap-x-10 mx-2 md:mx-6">
                <!-- fotografia de producto -->
                <section class="mt-7">
                    <figure class="border h-64 md:h-96 border-grayD9 rounded-lg flex justify-center items-center">
                        <img v-if="global_product_store.global_product.media?.length"
                            class="h-52 md:h-80 mx-auto object-contain"
                            :src="global_product_store.global_product.media[0]?.original_url" alt="">
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
                            <ProductInfo :product="global_product_store" />
                        </el-tab-pane>
                        <el-tab-pane label="Historial de movimientos" name="2">
                            <ProductHistorical ref="historyTab" :product="global_product_store" />
                        </el-tab-pane>
                    </el-tabs>
                </section>
            </div>
        </div>

        <!-- -------------- Modal starts----------------------- -->
        <Modal :show="entryProductModal" @close="entryProductModal = false">
            <div class="p-4 relative">
                <i @click="entryProductModal = false"
                    class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

                <h1 class="font-bold my-4">Ingresar producto a almacén</h1>
                <section class="text-center mt-5 mb-2 mx-5">
                    <div class="mt-3">
                        <InputLabel value="Cantidad" class="ml-3 mb-1 text-sm" />
                        <el-input v-model="form.quantity" ref="quantityInput" @keydown.enter="entryProduct"
                            placeholder="Cantidad que entra a almacén">
                        </el-input>
                        <InputError :message="form.errors.quantity" />
                    </div>
                    <div v-if="form.quantity && global_product_store.cost" class="text-sm mt-2">
                        Total de compra: {{ form.quantity }} x ${{ global_product_store.cost }} => ${{
                            (global_product_store.cost * form.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                    </div>
                    <div class="text-left mt-4 ml-6">
                        <p v-if="!global_product_store.cost" class="text-xs text-redDanger flex items-center space-x-2">
                            <i class="fa-regular fa-hand-point-down"></i>
                            <span>
                                Para poder descontar de caja, primero se debe especificar un
                                <Link :href="route('global-product-store.edit', encodedId)" class="underline"> precio de
                                compra al producto dando click aqui </Link>.
                            </span>
                        </p>
                        <el-checkbox v-model="form.is_paid_by_cash_register" name="is_paid_by_cash_register"
                            label="Se paga con dinero de caja" size="small" :disabled="!global_product_store.cost" />
                        <div v-if="form.is_paid_by_cash_register" class="w-1/3 mt-3">
                            <InputLabel value="Dinero a retirar de caja" class="ml-3 mb-1 text-sm" />
                            <el-input v-model="form.cash_amount" @keyup="handleChangeCashAmount" placeholder="Ej. $575"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/[^\d.]/g, '')">
                            </el-input>
                            <InputError :message="form.errors.cash_amount || cashAmountMessage" />
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 pt-7 pb-1 py-2">
                        <CancelButton @click="entryProductModal = false">Cancelar</CancelButton>
                        <PrimaryButton :disabled="form.processing || !form.quantity || cashAmountMessage"
                            @click="entryProduct" class="!rounded-full">Ingresar
                            producto
                        </PrimaryButton>
                    </div>
                </section>
            </div>
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
import { addOrUpdateItem } from '@/dbService.js';

export default {
    data() {
        const form = useForm({
            quantity: null,
            cash_amount: null,
            is_paid_by_cash_register: false //es pagado con dinero de caja? para hacer el registro de movimiento
        });
        return {
            form,
            encodedId: null, //id codificado
            searchQuery: this.global_product_store.global_product.name,
            searchFocus: false,
            productsFound: [this.global_product_store],
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
        global_product_store: Object,
        cash_register: Object,
    },
    methods: {
        getRoute() {
            if (this.$page.props.auth.user.store.type == 'Boutique / Tienda de Ropa / Zapatería') {
                return route('boutique-products.index');
            } else {
                return route('products.index');
            }
        },
        goToCreate() {
            if (this.$page.props.auth.user.store.type == 'Boutique / Tienda de Ropa / Zapatería') {
                this.$inertia.get(route('boutique-products.create'));
            } else {
                this.$inertia.get(route('products.create'));
            }
        },
        updateURL(tab) {
            const params = new URLSearchParams(window.location.search);
            params.set('tab', tab.props.name);
            window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
        },
        handleChangeCashAmount() {
            // total redondeado a 2 decimales
            const total = Math.round((this.global_product_store.cost * this.form.quantity + Number.EPSILON) * 100) / 100;
            if (this.form.cash_amount > this.cash_register.current_cash) {
                this.cashAmountMessage =
                    'El monto no debe superar lo disponible en caja ($' + this.cash_register.current_cash.toFixed(1).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ')';
            } else if (this.form.cash_amount > total) {
                this.cashAmountMessage =
                    'El monto no debe superar el total del gasto ($' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ')';
            } else {
                this.cashAmountMessage = null;
            }
        },
        handleBlur() {
            // Introducir un retraso para dar tiempo al evento click de ejecutarse antes del blur
            setTimeout(() => {
                this.searchFocus = false;
            }, 100);
        },
        openEntryModal() {
            this.entryProductModal = true;
            this.$nextTick(() => {
                this.$refs.quantityInput.focus(); // Enfocar el input de código cuando se abre el modal
            });
        },
        entryProduct() {
            if (!this.entryLoading) {
                this.entryLoading = true;
                this.form.put(route('global-product-store.entry', this.global_product_store.id), {
                    onSuccess: () => {
                        // actualizar current stock de producto en indexedDB si el seguimiento de iventario esta activo
                        // if (this.isInventoryOn) {
                        const product = {
                            id: 'global_' + this.global_product_store.id,
                            name: this.global_product_store.global_product.name,
                            code: this.global_product_store.global_product.code,
                            public_price: this.global_product_store.public_price,
                            current_stock: this.global_product_store.current_stock + this.form.quantity,
                            image_url: this.global_product_store.global_product.media[0]?.original_url,
                        };
                        addOrUpdateItem('products', product);
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
            }
        },
        setActiveTabFromURL() {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            if (tab) {
                this.activeTab = tab;
            }
        },
        handleProductSelected(product) {
            const encodedId = btoa(product.id.toString());
            if (product.global_product_id) {
                this.$inertia.get(route('global-product-store.show', encodedId))
            } else {
                this.$inertia.get(route('products.show', encodedId))
            }
        },
        encodeId(id) {
            const encodedId = btoa(id.toString());
            this.encodedId = encodedId;
        },
        async searchProducts() {
            this.searchLoading = true;
            try {
                const response = await axios.get(route('products.search'), { params: { query: this.searchQuery } });
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
        this.encodeId(this.global_product_store.id);
    }
}
</script>
