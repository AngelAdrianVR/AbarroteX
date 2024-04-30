<template>
    <AppLayout title="Productos">
        <div class="px-2 lg:px-10 py-7">
            <!-- tabs -->
            <div class="flex items-center justify-center text-sm">
                <button class="text-white bg-primary rounded-full px-5 py-1 z-10 -mr-5 cursor-default">Mis
                    productos</button>
                <button @click="$inertia.get(route('global-product-store.select'))"
                    class="text-primary bg-primarylight rounded-full px-6 py-1 z-0">Catálogo base</button>
            </div>
            <!-- header botones -->
            <div class="lg:flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Productos</h1>
                <div class="my-4 lg:my-0 flex items-center space-x-3">
                    <!-- <ThirthButton v-if="isInventoryOn" @click="openEntryModal">
                        Entrada de producto
                    </ThirthButton> -->
                    <PrimaryButton @click="$inertia.get(route('products.create'))" class="!rounded-full">
                        Nuevo producto
                    </PrimaryButton>
                </div>
            </div>

            <div class="lg:w-1/4 relative">
                <input v-model="searchQuery" @keydown.enter="searchProducts" class="input w-full pl-9"
                    placeholder="Buscar código o nombre de producto" type="search">
                <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
            </div>

            <Loading v-if="loading" class="mt-20" />
            <div v-else class="mt-8">
                <p v-if="localProducts.length" class="text-gray66 text-[11px]">{{ localProducts.length }} de {{
                    total_products }} elementos
                </p>
                <ProductTable :products="localProducts" />
                <p v-if="localProducts.length" class="text-gray66 text-[11px] mt-3">{{ localProducts.length }} de {{
                    total_products }} elementos
                </p>
                <p v-if="loadingItems" class="text-xs my-4 text-center">
                    Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                </p>
                <button v-else-if="total_products > 30 && localProducts.length < total_products && localProducts.length"
                    @click="fetchItemsByPage" class="w-full text-primary my-4 text-xs mx-auto underline ml-6">Cargar más
                    elementos</button>
            </div>
        </div>

        <!-- -------------- Modal starts----------------------- -->
        <Modal :show="entryProductModal" @close="entryProductModal = false">
            <div class="p-4 relative">
                <i @click="closeEntryModal"
                    class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

                <h1 class="font-bold my-4">Ingresar producto a almacén</h1>
                <section class="text-center mt-5 mb-2 mx-5">
                    <div class="mt-3 col-span-2">
                        <InputLabel value="Código del producto*" class="ml-3 mb-1" />
                        <el-input v-model="form.code" @keydown.enter="getProduct" ref="codeInput"
                            placeholder="Escanea o teclea el código del producto" :maxlength="100" clearable>
                            <template #prefix>
                                <i class="fa-solid fa-barcode"></i>
                            </template>
                        </el-input>
                    </div>
                    <div v-if="productEntryFound?.length > 0" class="mt-3">
                        <InputLabel value="Cantidad" class="ml-3 mb-1 text-sm" />
                        <el-input v-model="form.quantity" ref="quantityInput" autofocus
                            @keydown.enter="entryProduct(productEntryFound[0])"
                            placeholder="Cantidad que entra a almacén"
                            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            :parser="(value) => value.replace(/\D/g, '')">
                            <template #prefix>
                                <i class="fa-solid fa-hashtag"></i>
                            </template>
                        </el-input>
                        <InputError :message="form.errors.quantity" />
                    </div>

                    <!-- estado de carga -->
                    <div v-if="loading" class="flex justify-center items-center py-10">
                        <i class="fa-solid fa-square fa-spin text-4xl text-primary"></i>
                    </div>

                    <!-- informacion del producto escaneado -->
                    <div v-if="productEntryFound?.length > 0" class="mt-5 grid grid-cols-3">
                        <figure class="w-32 ml-16">
                            <img class="w-32 object-contain"
                                :src="productEntryFound[0]?.global_product_id ? productEntryFound[0]?.global_product.media[0]?.original_url : productEntryFound[0]?.media[0]?.original_url">
                        </figure>

                        <div class="col-span-2 text-left">
                            <p>Nombre: <strong class="ml-2">{{ productEntryFound[0]?.global_product_id ?
                                productEntryFound[0]?.global_product.name : productEntryFound[0]?.name }}</strong>
                            </p>
                            <p>Precio: <strong class="ml-2">${{ productEntryFound[0]?.public_price }}</strong></p>
                            <p>Existencias: <strong class="ml-2">{{ productEntryFound[0]?.current_stock }}</strong></p>
                        </div>
                    </div>
                    <!-- <p v-else-if="!loading && " class="mt-5 text-gray-500 text-center text-sm">No se encontró
                        ningun producto</p> -->

                    <div class="flex justify-end space-x-3 pt-7 pb-1 py-2">
                        <PrimaryButton @click="entryProduct(productEntryFound[0])" class="!rounded-full"
                            :disabled="!form.quantity">Ingresar
                            producto</PrimaryButton>
                        <CancelButton @click="closeEntryModal">Cancelar</CancelButton>
                    </div>
                </section>
            </div>
        </Modal>
        <!-- --------------------------- Modal ends ------------------------------------ -->
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ProductTable from '@/Components/MyComponents/Product/ProductTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    data() {
        const form = useForm({
            code: null,
            quantity: null,
        });

        return {
            // control de inventario activado
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
            form,
            loading: false,
            searchQuery: null,
            searchFocus: false,
            entryProductModal: false,
            productEntryFound: null,
            localProducts: this.products,
            // paginacion
            loadingItems: false,
            currentPage: 1,
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        CancelButton,
        ProductTable,
        ThirthButton,
        InputError,
        InputLabel,
        Loading,
        Modal
    },
    props: {
        products: Object,
        total_products: Number,
    },
    methods: {
        openEntryModal() {
            this.entryProductModal = true;
            this.$nextTick(() => {
                this.$refs.codeInput.focus(); // Enfocar el input de código cuando se abre el modal
            });
        },
        async fetchItemsByPage() {
            try {
                this.loadingItems = true;
                const response = await axios.get(route('products.get-by-page', this.currentPage));

                if (response.status === 200) {
                    this.localProducts = [...this.localProducts, ...response.data.items];
                    this.currentPage++;

                    // Actualiza la URL con la pagina
                    if (this.currentPage > 1) {
                        const currentURL = new URL(window.location.href);
                        currentURL.searchParams.set('page', this.currentPage);
                        window.history.replaceState({}, document.title, currentURL.href);
                    }
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.loadingItems = false;
            }
        },
        async searchProducts() {
            if (this.searchQuery != '') {
                try {
                    this.loading = true;
                    const response = await axios.get(route('products.search'), { params: { query: this.searchQuery } });
                    if (response.status == 200) {
                        this.localProducts = response.data.items;
                    }

                } catch (error) {
                    console.log(error);
                } finally {
                    this.loading = false;
                }
            } else {
                this.localProducts = this.products;
            }
        },
        async getProduct() {
            try {
                this.loading = true;
                const response = await axios.get(route('products.search'), { params: { query: this.form.code } });
                if (response.status == 200) {
                    this.productEntryFound = response.data.items;
                    this.$nextTick(() => {
                        this.$refs.quantityInput.focus(); // Enfocar el input de cantidad cuando se encuentra el producto
                    });
                }

            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        entryProduct(product) {
            console.log(product);
            let routePage;
            if (product.global_product_id) {
                routePage = 'global-product-store.entry';
            } else {
                routePage = 'products.entry';
            }

            this.form.put(route(routePage, product.id), {
                onSuccess: () => {
                    if (product.global_product_id) {
                        const IndexProductEntry = this.localProducts.findIndex(item => item.global_product?.name === product.global_product?.name);
                        console.log(IndexProductEntry);
                        if (IndexProductEntry !== -1) {
                            this.localProducts[IndexProductEntry].current_stock += parseInt(this.form.quantity);
                        }
                        this.$notify({
                            title: "Correcto",
                            message: 'Se ha ingresado ' + this.form.quantity + ' unidades de ' + product.global_product?.name,
                            type: "success",
                        });
                    } else {
                        const IndexProductEntry = this.localProducts.findIndex(item => item.code === product.code);
                        if (IndexProductEntry !== -1) {
                            this.localProducts[IndexProductEntry].current_stock += parseInt(this.form.quantity);
                        }
                        this.$notify({
                            title: "Correcto",
                            message: 'Se ha ingresado ' + this.form.quantity + ' unidades de ' + this.localProducts[IndexProductEntry].name,
                            type: "success",
                        });
                    }
                    this.$nextTick(() => {
                        this.$refs.codeInput.focus(); // Enfocar el input de código cuando se abre el modal
                    });

                    this.form.reset();
                    this.productEntryFound = null;
                },
            });
        },
        closeEntryModal() {
            this.form.reset();
            this.productEntryFound = null;
            this.entryProductModal = false;
        },
        async fetchAllItemsForCurrentPage() {
            try {
                this.loading = true;
                const response = await axios.get(route('products.get-all-until-page', this.currentPage));

                if (response.status === 200) {
                    this.localProducts = response.data.items;
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        // Obtener la URL actual
        const currentURL = new URL(window.location.href);
        // Extraer el valor de 'currentTab' de los parámetros de búsqueda
        const currentTabFromURL = currentURL.searchParams.get('page');

        if (currentTabFromURL) {
            this.currentPage = currentTabFromURL;
            this.fetchAllItemsForCurrentPage();
        }
    }
}
</script>
