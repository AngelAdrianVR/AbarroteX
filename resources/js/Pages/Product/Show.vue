<template>
    <AppLayout :title="product.data.name">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="lg:flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Productos</h1>
                <div class="flex items-center space-x-2 my-2 lg:my-0">
                    <PrimaryButton @click="openEntryModal">Inventario</PrimaryButton>
                    <button @click="$inertia.get(route('products.edit', encodedId))" title="Editar producto"
                        class="flex items-center justify-center bg-[#EDEDED] text-primary size-8 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button @click="$inertia.get(route('products.create'))" title="Crear producto"
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
                    <ul v-else-if="productsFound?.length > 0">
                        <li @click.stop="handleProductSelected(product)" v-for="(product, index) in productsFound"
                            :key="index" class="hover:bg-gray-200 cursor-default text-sm px-5 py-2">{{
                                product.global_product_id ? product.global_product?.name : product.name }}</li>
                    </ul>
                    <p v-else class="text-center text-sm text-gray-600 px-5 py-2">No se encontraron coincidencias</p>
                </div>
            </div>
            <div class="mt-5">
                <Back :to="route('products.index')" />
            </div>
            <!-- Info de producto -->
            <div class="md:grid grid-cols-2 xl:grid-cols-3 gap-x-10 mx-2 md:mx-6">
                <!-- fotografia de producto -->
                <section class="mt-7">
                    <figure class="border h-64 md:h-96 border-grayD9 rounded-lg flex justify-center items-center">
                        <img v-if="product.data.imageCover?.length" class="h-52 md:h-80 mx-auto object-contain"
                            :src="product.data.imageCover[0]?.original_url" alt="">
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
                            <ProductInfo :product="product.data" />
                        </el-tab-pane>
                        <el-tab-pane label="Historial de movimientos" name="2">
                            <ProductHistorical ref="historyTab" :product="product.data" />
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
                <el-tabs v-model="inventoryActiveTab" @tab-click="handleInventoryTabClick">
                    <el-tab-pane label="Entrada" name="1">
                        <div class="mt-3">
                            <InputLabel value="Cantidad" class="ml-3 mb-1 text-sm" />
                            <el-input v-model="form.quantity" ref="quantityInput" @keydown.enter="entryProduct"
                                placeholder="Cantidad que entra a almacén">
                            </el-input>
                            <InputError :message="form.errors.quantity" />
                        </div>
                        <div v-if="form.quantity && product.data.cost" class="text-sm mt-2">
                            Total de compra: {{ form.quantity }} x ${{ product.data.cost }} => ${{ (product.data.cost *
                                form.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                        </div>
                        <div class="text-left mt-4 ml-6">
                            <p v-if="!product.data.cost" class="text-xs text-redDanger">
                                <i class="fa-regular fa-hand-point-down mr-2"></i>
                                Para poder descontar de caja, primero se debe especificar un
                                <Link :href="route('products.edit', encodedId)" class="underline"> precio de
                                compra al producto dando click aqui </Link>.
                            </p>
                            <el-checkbox v-model="form.is_paid_by_cash_register" name="is_paid_by_cash_register"
                                label="Se paga con dinero de caja" size="small" :disabled="!product.data.cost" />
                            <div v-if="form.is_paid_by_cash_register" class="w-1/3 mt-3">
                                <InputLabel value="Dinero a retirar de caja" class="ml-3 mb-1 text-sm" />
                                <el-input v-model="form.cash_amount" @keyup="handleChangeCashAmount"
                                    placeholder="Ej. $190"
                                    :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                    :parser="(value) => value.replace(/[^\d.]/g, '')">
                                </el-input>
                                <InputError :message="form.errors.cash_amount || cashAmountMessage" />
                            </div>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane label="Salida" name="2">
                        <div class="mt-3">
                            <InputLabel value="Cantidad" />
                            <el-input v-model="form.quantity" ref="quantityInput" @keydown.enter="entryProduct"
                                placeholder="Cantidad que sale de almacén">
                            </el-input>
                            <InputError :message="form.errors.quantity" />
                        </div>
                        <div v-if="form.quantity && product.data.cost" class="text-sm mt-2">
                            Total de pérdida: {{ form.quantity }} x ${{ product.data.cost }} => ${{
                                (product.data.cost * form.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                            }}
                        </div>
                        <div class="mt-3">
                            <InputLabel value="Motivo" />
                            <el-select filterable v-model="form.concept" clearable placeholder="Seleccione"
                                no-data-text="No hay opciones registradas"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="item in outConcepts" :key="item" :label="item" :value="item" />
                            </el-select>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </template>
            <template #footer>
                <div class="flex space-x-1">
                    <CancelButton @click="entryProductModal = false">Cancelar</CancelButton>
                    <PrimaryButton :disabled="form.processing || !form.quantity || cashAmountMessage"
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
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Back from "@/Components/MyComponents/Back.vue";
import axios from 'axios';
import { useForm, Link } from "@inertiajs/vue3";
import { addOrUpdateItem } from "@/dbService.js";
import DialogModal from '@/Components/DialogModal.vue';

export default {
    data() {
        const form = useForm({
            quantity: null,
            cash_amount: null,
            concept: 'Ajuste de inventario', // concepto por defecto
            is_paid_by_cash_register: false //es pagado con dinero de caja? para hacer el registro de movimiento
        });
        return {
            form,
            encodedId: null, //id codificado
            searchQuery: this.product.data.name,
            searchFocus: false,
            productsFound: [this.product.data],
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
            //inventario
            inventoryActiveTab: '1',
            outConcepts: [
                'Ajuste de inventario',
                'Caducidad o vencimiento',
                'Consumo del negocio',
                'Devolución a proveedor',
                'Mal estado',
                'Muestra gratis',
                'Regalo por compra',
                'Otro'
            ]
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
        DialogModal,
        Back,
        ProductInfo,
        ProductHistorical,
        Link,
    },
    props: {
        product: Object,
        cash_register: Object,
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
        handleChangeCashAmount() {
            // total redondeado a 2 decimales
            const total = Math.round((this.product.data.cost * this.form.quantity + Number.EPSILON) * 100) / 100;
            if (this.form.cash_amount > this.cash_register.current_cash) {
                this.cashAmountMessage =
                    'El monto no debe superar lo disponible en caja ($' + this.cash_register.current_cash.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ')';
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
        outProduct() {
            if (this.entryLoading) return;

            this.entryLoading = true;
            this.form.put(route('products.out', this.product.data?.id), {
                onSuccess: () => {
                    // actualizar current stock de producto en indexedDB si el seguimiento de iventario esta activo
                    // if (this.isInventoryOn) {
                    const product = {
                        id: 'local_' + this.product.data.id,
                        name: this.product.data.name,
                        code: this.product.data.code,
                        public_price: this.product.data.public_price,
                        current_stock: this.product.data.current_stock + this.form.quantity,
                        image_url: this.product.data.imageCover[0]?.original_url,
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
        },
        entryProduct() {
            if (this.entryLoading) return;

            this.entryLoading = true;
            this.form.put(route('products.entry', this.product.data?.id), {
                onSuccess: () => {
                    // actualizar current stock de producto en indexedDB si el seguimiento de iventario esta activo
                    // if (this.isInventoryOn) {
                    const product = {
                        id: 'local_' + this.product.data.id,
                        name: this.product.data.name,
                        code: this.product.data.code,
                        public_price: this.product.data.public_price,
                        current_stock: this.product.data.current_stock + this.form.quantity,
                        image_url: this.product.data.imageCover[0]?.original_url,
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
        this.encodeId(this.product.data.id);
    },
}
</script>
