<template>
    <Loading v-if="loading" class="mt-20" />
    <div v-else>
        <div class="lg:flex justify-between items-center mx-3 mt-5">
            <h1 class="font-bold text-lg">Productos</h1>
        </div>

        <section class="md:flex justify-between items-center">
            <div class="lg:w-1/3 relative">
                <input v-model="searchQuery" @keydown.enter="searchProducts" class="input w-full pl-9"
                    placeholder="Buscar código o nombre de producto" type="search">
                <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
            </div>
            <div class="my-4 lg:my-0 flex items-center justify-end space-x-3">
                <!-- <ThirthButton v-if="isInventoryOn" @click="openEntryModal">
                        Entrada de producto
                    </ThirthButton> -->
                <el-dropdown split-button type="primary" @click="$inertia.get(route('products.create'))" trigger="click"
                    @command="handleCommand">
                    Nuevo producto
                    <template #dropdown>
                        <el-dropdown-menu>
                            <!-- <el-dropdown-item command="chekin">Dar entrada a producto</el-dropdown-item> -->
                            <el-dropdown-item command="import">Importar productos</el-dropdown-item>
                            <el-dropdown-item command="export">Exportar productos</el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </div>
        </section>

        <div class="mt-8">
            <p v-if="localProducts.length" class="text-gray66 text-[11px]">{{ localProducts.length }} de {{
                totalProducts }} elementos
            </p>
            <ProductTable :products="localProducts" />
            <p v-if="localProducts.length" class="text-gray66 text-[11px] mt-3">{{ localProducts.length }} de {{
                totalProducts }} elementos
            </p>
            <p v-if="loadingItems" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </p>
            <button v-else-if="totalProducts > 30 && localProducts.length < totalProducts && localProducts.length"
                @click="fetchItemsByPage" class="w-full text-primary my-4 text-xs mx-auto underline ml-6">Cargar más
                elementos</button>
        </div>

        <!-- modal de importacion -->
        <DialogModal :show="showImportModal" @close="showImportModal = false">
            <template #title> Importar productos </template>
            <template #content>
                <div v-if="importWasWrong" class="flex flex-col items-center justify-center">
                    <p>Se detectaron inconvnientes con la información</p>
                    <p class="text-gray99">
                        A continuación verás una lista de la información que necesitamos que revises
                        para poder importar correctamente tus productos. Al editar tu archivo recuerda
                        guardar los cambios y vuelve a subirlo.
                    </p>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-10 text-amber-600">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <section v-for="(error, index1) in importErrors" :key="index1" class="mt-3 self-start mx-5 text-xs">
                        <p>Fila {{ error.row }} de tu archivo excel</p>
                        <ul>
                            <li v-for="(item, index2) in error.errors" :key="index2">
                                • {{ item }}
                            </li>
                        </ul>
                    </section>
                </div>
                <div v-else-if="importWasSuccessful" class="flex flex-col items-center justify-center">
                    <p>¡Listo!</p>
                    <p class="text-gray99">Tus productos se han subido con éxito.</p>
                    <svg class="mt-2" width="24" height="24" viewBox="0 0 54 43" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.5263 42.0011C8.73489 31.147 0.492597 22.5137 0.0263141 22.0011C-0.439969 21.4884 5.33881 20.5148 13.5263 29.0011C29.0463 11.4303 44.0918 -0.0470468 52.5263 0.00107837C52.8512 -0.0320255 53.3498 0.705849 53.0263 1.00108C34.3519 9.89275 24.0145 25.6913 15.0263 42.0011C14.9721 42.4953 12.5049 42.397 12.5263 42.0011Z"
                            fill="#189203" />
                    </svg>
                </div>
                <div v-else-if="isImporting" class="flex flex-col items-center justify-center">
                    <p>Procesando productos</p>
                    <p class="text-gray99">Esto podría tardar un momento, gracias por la espera.</p>
                    <svg class="animate-spin text-primary mt-4 text-center" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" id="Rotate-Right--Streamline-Sharp" height="20" width="20">
                        <desc>Rotate Right Streamline Icon: https://streamlinehq.com</desc>
                        <g id="rotate-right">
                            <path id="Vector 2754" stroke="currentColor" d="M20.2047 0.5135V4.8893H15.8289"
                                stroke-width="2"></path>
                            <path id="Ellipse 1206" stroke="currentColor"
                                d="M20.2047 4.764C18.2001 2.4929 15.2674 1.0605 12.0001 1.0605C5.9583 1.0605 1.0605 5.9583 1.0605 12C1.0605 16.194 3.4207 19.8367 6.8853 21.6726"
                                stroke-width="2"></path>
                            <path id="Ellipse 1207" stroke="currentColor"
                                d="M9.1081 22.5533C10.0293 22.8051 10.999 22.9395 11.9999 22.9395C13.4231 22.9395 14.7826 22.6678 16.0297 22.1734"
                                stroke-width="2"></path>
                            <path id="Ellipse 1208" stroke="currentColor"
                                d="M17.7655 21.2986C19.2694 20.3641 20.5299 19.0749 21.4301 17.548" stroke-width="2">
                            </path>
                            <path id="Ellipse 1209" stroke="currentColor"
                                d="M22.9395 12C22.9395 13.2879 22.717 14.5237 22.3083 15.6713" stroke-width="2"></path>
                        </g>
                    </svg>
                </div>
                <div v-else>
                    <p class="text-gray99">
                        ¡Bienvenido a la función de importación de productos! Para facilitar el proceso y asegurar que
                        todos
                        los datos se ingresen correctamente, te recomendamos seguir estos pasos simples.
                    </p>
                    <p class="mt-5 mb-1">
                        Primero, descarga la plantilla de importación haciendo click en el siguiente enlace:
                    </p>
                    <a href="@/../../files/tabla_productos.xlsx" target="_blank" class="underline text-primary">Descarga
                        la
                        plantilla</a>
                    <p class="mt-5 mb-1">
                        Una vez que hayas agregado todos los productos a la plantilla, guarda los cambios y adjunta el
                        archivo.
                        El sistema se encargará automáticamente de procesar la información y agregar tus productos.
                    </p>
                    <form @submit.prevent="importProducts" ref="importForm" class="mt-4">
                        <div>
                            <FileUploader @files-selected="importForm.file = $event" :multiple="false"
                                acceptedFormat="excel" />
                            <InputError :message="importForm.errors.file" />
                        </div>
                    </form>
                </div>
            </template>
            <template #footer>
                <div v-if="!isImporting && !importWasSuccessful && !importWasWrong" class="flex items-center space-x-2">
                    <CancelButton @click="showImportModal = false; importForm.file = []">
                        Cancelar
                    </CancelButton>
                    <PrimaryButton @click="importProducts()" :disabled="!importForm.file.length">
                        Importar
                    </PrimaryButton>
                </div>
                <div v-if="importWasWrong" class="flex items-center space-x-2">
                    <PrimaryButton @click="importWasWrong = false; importForm.file = []">
                        Ya corregí mi achivo
                    </PrimaryButton>
                </div>
            </template>
        </DialogModal>

        <!-- modal de exportacion -->
        <DialogModal :show="showExportModal" @close="showExportModal = false">
            <template #title> Exportar productos </template>
            <template #content>
                <p>
                    Al seleccionar “Exportar” el archivo se descargará automáticamente a tu dispositivo.
                </p>
                <p class="mt-2 flex items-center space-x-2">
                    <i class="fa-solid fa-exclamation text-redDanger"></i>
                    <span class="text-gray99">Nota: Los productos que Ezy ventas te facilita como catálogo base, no
                        serán exportados.</span>
                </p>
                <p v-if="totalLocalProducts" class="mt-2">
                    Hay
                    <b class="text-primary">
                        {{ totalLocalProducts }}
                    </b>
                    producto(s) disponible(s) para exportar
                </p>
                <p v-else class="mt-2 text-redDanger">No hay productos para exportar</p>
            </template>
            <template #footer>
                <div class="flex items-center space-x-2">
                    <CancelButton @click="showExportModal = false">
                        Cancelar
                    </CancelButton>
                    <a v-if="totalLocalProducts" :href="route('products.export')"
                        class="cursor-pointer text-center px-4 py-2 bg-primary border border-transparent rounded-full text-xs text-white tracking-widest active:scale-95 disabled:active:scale-100 disabled:cursor-not-allowed disabled:text-white disabled:bg-[#999999] focus:outline-none focus:ring-0 transition ease-in-out duration-100">
                        Exportar
                    </a>
                </div>
            </template>
        </DialogModal>

        <!-- modal de entrada de producto -->
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
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ProductTable from '@/Components/MyComponents/Product/ProductTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import FileUploader from '@/Components/MyComponents/FileUploader.vue';
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import DialogModal from "@/Components/DialogModal.vue";
import { useForm } from "@inertiajs/vue3";
import axios from 'axios';
import { addOrUpdateBatchOfItems } from '@/dbService.js';

export default {
    data() {
        const form = useForm({
            code: null,
            quantity: null,
        });

        const importForm = useForm({
            file: [],
        });

        return {
            form,
            importForm,
            loading: false,
            searchQuery: null,
            entryProductModal: false,
            productEntryFound: null,
            // tabs
            activeTab: 'Mis productos',
            // paginacion
            loadingItems: false,
            currentPage: 1,
            // importation
            showImportModal: false,
            isImporting: false,
            importWasSuccessful: false,
            importWasWrong: false,
            importErrors: [],
            //exportacion
            showExportModal: false,
            isExporting: false,
            // control de inventario activado
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
            // datos para la vista
            products: [],
            localProducts: [],
            totalProducts: null,
            totalLocalProducts: null,
            // carga
            loading: false,
        };
    },
    components: {
        PrimaryButton,
        CancelButton,
        ProductTable,
        ThirthButton,
        InputError,
        InputLabel,
        Loading,
        Modal,
        DialogModal,
        FileUploader,
    },
    props: {
    },
    methods: {
        handleCommand(command) {
            if (command == 'import') {
                this.showImportModal = true;
            } else if (command == 'export') {
                this.showExportModal = true;
            }
        },
        exportProducts() {
            this.$inertia.visit(route('products.export'));
        },
        openEntryModal() {
            this.entryProductModal = true;
            this.$nextTick(() => {
                this.$refs.codeInput.focus(); // Enfocar el input de código cuando se abre el modal
            });
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
        resetLocalProducts() {
            return new Promise((resolve) => {
                // Simulamos una operación asíncrona con un setTimeout que puede ajustarse al tiempo esperado
                // En tu caso, puedes reemplazar este setTimeout con una llamada real si es necesario
                setTimeout(() => {
                    this.localProducts = this.products;
                    resolve();
                }, 500); // Cambia este tiempo si es necesario
            });
        },
        async fetchDataForProductsView() {
            try {
                this.loading = true;
                const response = await axios.get(route('products.get-data-for-products-view'));

                if (response.status === 200) {
                    this.products = response.data.products;
                    this.totalProducts = response.data.total_products;
                    this.totalLocalProducts = response.data.total_local_products;
                    this.localProducts = this.products;
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
        async importProducts() {
            try {
                this.isImporting = true;
                const response = await axios.post(route('products.import'), {
                    file: this.importForm.file
                }, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.status === 200) {
                    // Obtener productos
                    const response = await axios.get(route('products.get-all-for-indexedDB'));
                    const products = response.data.products;
                    // actualizar indexedDB
                    await addOrUpdateBatchOfItems('products', products);
                    this.isImporting = false;
                    this.importWasSuccessful = true;
                    this.importWasWrong = false;
                    window.location.reload();
                }
            } catch (error) {
                this.isImporting = false;
                this.importWasWrong = true;
                this.importErrors = error.response.data.errors;
            }
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
            this.loading = true;
            if (this.searchQuery != '') {
                try {
                    const response = await axios.get(route('products.search'), { params: { query: this.searchQuery } });
                    if (response.status == 200) {
                        this.products = this.localProducts;
                        this.localProducts = response.data.items;
                    }

                } catch (error) {
                    console.log(error);
                } finally {
                    this.loading = false;
                }
            } else {
                // Aquí podemos simular una operación asíncrona para mostrar el indicador de carga
                try {
                    await this.resetLocalProducts();
                } finally {
                    this.loading = false;
                }
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
        this.fetchDataForProductsView();
    }
}
</script>