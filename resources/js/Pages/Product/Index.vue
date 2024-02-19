<template>
    <AppLayout title="Productos">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="lg:flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Productos</h1>
                <div class="my-4 lg:my-0 flex items-center space-x-3">
                    <PrimaryButton @click="openEntryModal" class="!rounded-full !bg-[#5FCB1F]">Entrada de producto
                    </PrimaryButton>
                    <PrimaryButton @click="$inertia.get(route('products.create'))" class="!rounded-full">Nuevo producto
                    </PrimaryButton>
                </div>
            </div>

            <div class="lg:w-1/4 relative">
                <input v-model="searchQuery" @keydown.enter="searchProducts" class="input w-full pl-9"
                    placeholder="Buscar código o nombre de producto" type="search">
                <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
            </div>

            <Loading v-if="loading" class="mt-20" />
            <div v-else class="mt-8 lg:w-11/12">
                <p v-if="localProducts.length" class="text-gray66 text-[11px]">{{ localProducts.length }} de {{ total_products }} elementos
                </p>
                <ProductTable :products="localProducts" />
                <p v-if="localProducts.length" class="text-gray66 text-[11px]">{{ localProducts.length }} de {{ total_products }} elementos
                </p>
                <p v-if="loadingItems" class="text-xs my-4 text-center">
                    Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                </p>
                <button v-else-if="total_products > 15 && localProducts.length < total_products && localProducts.length" @click="fetchItemsByPage"
                    class="w-full text-primary my-4 text-xs mx-auto underline ml-6">Cargar más elementos</button>
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
                            placeholder="Escanea el código del producto" :maxlength="100" clearable>
                            <template #prefix>
                                <i class="fa-solid fa-barcode"></i>
                            </template>
                        </el-input>
                    </div>
                    <div v-if="productEntryFound?.length > 0" class="mt-3">
                        <InputLabel value="Cantidad" class="ml-3 mb-1 text-sm" />
                        <el-input v-model="form.quantity" ref="quantityInput" autofocus @keydown.enter="entryProduct"
                            placeholder="Cantidad que entra a almacén"
                            >
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
                            <img class="w-32 object-contain" :src="productEntryFound[0]?.imageCover[0]?.original_url"
                                alt="">
                        </figure>

                        <div class="col-span-2 text-left">
                            <p>Nombre: <strong class="ml-2">{{ productEntryFound[0]?.name }}</strong></p>
                            <p>Precio: <strong class="ml-2">${{ productEntryFound[0]?.public_price }}</strong></p>
                            <p>Existencias: <strong class="ml-2">{{ productEntryFound[0]?.current_stock }}</strong></p>
                        </div>
                    </div>
                    <!-- <p v-else-if="!loading && " class="mt-5 text-gray-500 text-center text-sm">No se encontró
                        ningun producto</p> -->


                    <div class="flex justify-end space-x-3 pt-7 pb-1 py-2">
                        <PrimaryButton @click="entryProduct" class="!rounded-full">Ingresar producto</PrimaryButton>
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
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ProductTable from '@/Components/MyComponents/Product/ProductTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Pagination from '@/Components/MyComponents/Pagination.vue';
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
            form,
            loading: false,
            searchQuery: null,
            searchFocus: false,
            entryProductModal: false,
            productEntryFound: null,
            localProducts: this.products.data,
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
        Pagination,
        InputError,
        Modal,
        Loading,
        InputLabel
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
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.loadingItems = false;
            }
        },
        async searchProducts() {
            this.loading = true;
            try {
                const response = await axios.get(route('products.search'), { params: { query: this.searchQuery } });
                if (response.status == 200) {
                    this.localProducts = response.data.items;
                }

            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
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
        entryProduct() {
            this.form.put(route('products.entry', this.productEntryFound[0]?.id), {
                onSuccess: () => {
                    const IndexProductEntry = this.localProducts.findIndex(item => item.code === this.form.code);
                    if (IndexProductEntry != -1) {
                        this.localProducts[IndexProductEntry].current_stock += parseInt(this.form.quantity);
                    }
                    this.$nextTick(() => {
                        this.$refs.codeInput.focus(); // Enfocar el input de código cuando se abre el modal
                    });
                    this.$notify({
                        title: "Correcto",
                        message: 'Se ha ingresado ' + this.form.quantity + ' unidades de ' + this.productEntryFound[0].name,
                        type: "success",
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
        }
    },
}
</script>
