<template>
    <Loading v-if="loading" class="mt-20" />
    <div v-else>
        <div class="lg:flex justify-between items-center mx-3 mt-5">
            <h1 class="font-bold text-lg">Productos</h1>
        </div>

        <section class="md:flex justify-between items-center">
            <article class="flex items-center space-x-5 lg:w-1/3">
                <div class="lg:w-full relative">
                    <input v-model="searchQuery" @keydown.enter="searchProducts" class="input w-full pl-9"
                        placeholder="Buscar código o nombre de producto" type="search" ref="scanInput" />
                    <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                </div>
                <el-tag @close="closedTag" v-if="searchedWord" closable type="primary">
                    {{ searchedWord }}
                </el-tag>
            </article>
            <div class="my-4 lg:my-0 flex items-center justify-end space-x-3">
                <!-- <ThirthButton v-if="isInventoryOn" @click="openInventoryModal">
                        Entrada de producto
                    </ThirthButton> -->
                <el-dropdown split-button type="primary" @click="$inertia.get(route('products.create'))" trigger="click"
                    @command="handleCommand">
                    Nuevo producto
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item command="inventory">Ajuste de inventario</el-dropdown-item>
                            <el-dropdown-item command="import">Importar productos</el-dropdown-item>
                            <el-dropdown-item command="export">Exportar productos</el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </div>
        </section>

        <!-- <section v-if="isInventoryOn">
            costo de almacén: {{ products }}
        </section> -->

        <div class="mt-8">
            <!-- <p v-if="searchedWord" class="text-gray66 text-[11px]">
                {{ localProducts.length }} elementos encontrados
            </p>
            <p v-else-if="localProducts.length" class="text-gray66 text-[11px]">{{ localProducts.length }} de {{
                totalProducts }} elementos
            </p> -->
            <ProductTable :products="localProducts" :totalItems="localProducts.length" />
            <!-- <p v-if="searchedWord" class="text-gray66 text-[11px]">
                {{ localProducts.length }} elementos encontrados
            </p> -->
            <!-- <p v-else-if="localProducts.length" class="text-gray66 text-[11px] mt-3">{{ localProducts.length }} de {{
                totalProducts }} elementos
            </p>
            <p v-if="loadingItems" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </p> -->
            <!-- <button
                v-else-if="!searchedWord && totalProducts > 30 && localProducts.length < totalProducts && localProducts.length"
                @click="fetchItemsByPage" class="w-full text-primary my-4 text-xs mx-auto underline ml-6">
                Cargar más elementos
            </button> -->
        </div>

        <!-- modal de importacion -->
        <DialogModal :show="showImportModal" @close="showImportModal = false">
            <template #title> Importar productos </template>
            <template #content>
                <div v-if="importWasWrong" class="flex flex-col items-center justify-center">
                    <p>Se detectaron inconvenientes con la información</p>
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
                        <i v-if="isImporting" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
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
        <!-- modal de entrada/salida de producto -->
        <DialogModal :show="showInventoryModal" @close="showInventoryModal = false">
            <template #title>Ajuste de inventario</template>
            <template #content>
                <p class="text-gray99">
                    Para actualizar las existencias de un producto, teclea o escanea el código. <br>
                    También puedes actualizar el precio a público del producto, si lo deseas.
                </p>
                <section>
                    <div class="mt-3 col-span-2">
                        <InputLabel value="Código del producto*" />
                        <el-input v-model="form.code" @keydown.enter="getProduct" ref="codeInput"
                            placeholder="Escanea o teclea el código del producto" :maxlength="100" clearable>
                            <template #prefix>
                                <i class="fa-solid fa-barcode"></i>
                            </template>
                        </el-input>
                    </div>
                    <div v-if="productEntryFound?.length > 0" class="mt-3">
                        <InputLabel value="Existencias actuales" />
                        <el-input v-model="form.quantity" ref="quantityInput" autofocus
                            @keydown.enter="updateInventory(productEntryFound[0])"
                            placeholder="Cantidad real en existencia"
                            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            :parser="(value) => value.replace(/\D/g, '')">
                            <template #prefix>
                                <i class="fa-solid fa-hashtag"></i>
                            </template>
                        </el-input>
                        <InputError :message="form.errors.quantity" />
                    </div>
                    <SmallLoading v-if="fetchingProduct" class="mx-auto mt-4" />
                    <div v-else-if="productEntryFound?.length > 0" class="mt-4 md:grid grid-cols-3">
                        <figure class="w-24 md:w-32 mx-auto md:ml-16">
                            <img class="w-24 md:w-32 object-contain"
                                :src="productEntryFound[0]?.global_product_id ? productEntryFound[0]?.global_product.media[0]?.original_url : productEntryFound[0]?.media[0]?.original_url">
                        </figure>
                        <div class="col-span-2 grid grid-cols-4 gap-y-1 self-start">
                            <span>Nombre:</span>
                            <strong class="col-span-3">
                                {{ productEntryFound[0]?.global_product_id ?
                                    productEntryFound[0]?.global_product.name : productEntryFound[0]?.name }}
                                {{ productEntryFound[0]?.global_product_id ?
                                    productEntryFound[0]?.global_product.name : productEntryFound[0]?.name }}
                            </strong>
                            <span>Precio:</span>
                            <div v-if="editPrice" class="col-span-3 flex items-center space-x-2">
                                <el-input v-model="priceForm.public_price" autofocus
                                    @keydown.enter="updatePrice(productEntryFound[0])" class="!w-1/3"
                                    placeholder="Precio público actual"
                                    :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                    :parser="(value) => value.replace(/[^\d.]/g, '')" />
                                <div v-if="!priceForm.processing" class="flex items-center space-x-1">
                                    <button @click="updatePrice(productEntryFound[0])" type="button"
                                        :disabled="!priceForm.public_price || priceForm.processing"
                                        class="text-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    </button>
                                    <button @click="priceForm.public_price = null; editPrice = false"
                                        :disabled="priceForm.processing" type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <i v-else class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-primary"></i>
                            </div>
                            <div v-else class="col-span-3 flex items-center space-x-2">
                                <strong>
                                    ${{ productEntryFound[0]?.public_price?.toFixed(1).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                </strong>
                                <button
                                    @click="priceForm.public_price = productEntryFound[0]?.public_price; editPrice = true"
                                    type="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                    </svg>
                                </button>
                            </div>
                            <span>Existencias:</span>
                            <strong class="col-span-3">{{ productEntryFound[0]?.current_stock }}</strong>
                        </div>
                    </div>
                </section>
            </template>
            <template #footer>
                <div class="flex items-center space-x-2">
                    <CancelButton @click="closeInventoryModal" :disabled="form.processing || priceForm.processing">
                        Cancelar</CancelButton>
                    <PrimaryButton @click="updateInventory(productEntryFound[0])" class="!rounded-full"
                        :disabled="!form.quantity || form.processing || priceForm.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Actualizar stock
                    </PrimaryButton>
                </div>
            </template>
        </DialogModal>
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
// import { addOrUpdateBatchOfItems } from '@/dbService.js';
import emitter from '@/eventBus.js';
import SmallLoading from '@/Components/MyComponents/SmallLoading.vue';

export default {
    data() {
        const form = useForm({
            code: null,
            quantity: null,
        });

        const priceForm = useForm({
            public_price: null,
        });

        const importForm = useForm({
            file: [],
        });

        return {
            form,
            priceForm,
            importForm,
            loading: false,
            fetchingProduct: false,
            searchQuery: null,
            // ajuste inventario
            showInventoryModal: false,
            editPrice: false,
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
            searchedWord: null, //palabra con la que se hizo la última busqueda.
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
        SmallLoading,
    },
    props: {
    },
    methods: {
        handleCommand(command) {
            if (command == 'import') {
                this.showImportModal = true;
            } else if (command == 'export') {
                this.showExportModal = true;
            } else if (command == 'inventory') {
                this.openInventoryModal();
            }
        },
        exportProducts() {
            this.$inertia.visit(route('products.export'));
        },
        openInventoryModal() {
            this.showInventoryModal = true;
            this.$nextTick(() => {
                this.$refs.codeInput.focus(); // Enfocar el input de código cuando se abre el modal
            });
        },
        updateInventory(product) {
            if (!this.form.quantity) {
                return;
            }
            let routePage;
            if (product.global_product_id) {
                routePage = 'global-product-store.inventory-update';
            } else {
                routePage = 'products.inventory-update';
            }
            this.form.put(route(routePage, product.id), {
                onSuccess: () => {
                    if (product.global_product_id) {
                        // Actualiza el precio en la lista local mostrados en la tabla de productos
                        const IndexProductEntry = this.localProducts.findIndex(item => item.global_product?.name === product.global_product?.name);
                        if (IndexProductEntry !== -1) {
                            this.localProducts[IndexProductEntry].current_stock = parseInt(this.form.quantity);
                        }
                        this.$notify({
                            title: "Correcto",
                            message: 'Se han actualizado las existencias de ' + product.global_product?.name,
                            type: "success",
                        });
                    } else {
                        // Actualiza el precio en la lista local mostrados en la tabla de productos
                        const IndexProductEntry = this.localProducts.findIndex(item => item.code === product.code);
                        if (IndexProductEntry !== -1) {
                            this.localProducts[IndexProductEntry].current_stock = parseInt(this.form.quantity);
                        }
                        this.$notify({
                            title: "Correcto",
                            message: 'Se han actualizado las existencias de ' + product.name,
                            type: "success",
                        });
                    }
                    this.$nextTick(() => {
                        this.$refs.codeInput.focus(); // Enfocar el input de código cuando se abre el modal
                    });

                    this.form.reset();
                    this.productEntryFound = null;
                },
                onError: (err) => {
                    console.log(err);
                }
            });
        },
        updatePrice(product) {
            if (!this.priceForm.public_price) {
                return;
            }

            let routePage;
            if (product.global_product_id) {
                routePage = 'global-product-store.price-update';
            } else {
                routePage = 'products.price-update';
            }
            this.priceForm.put(route(routePage, product.id), {
                onSuccess: () => {
                    if (product.global_product_id) {
                        // Actualiza el precio en la lista local mostrados en la tabla de productos
                        const IndexProductEntry = this.localProducts.findIndex(item => item.global_product?.name === product.global_product?.name);
                        if (IndexProductEntry !== -1) {
                            this.localProducts[IndexProductEntry].public_price = parseFloat(this.priceForm.public_price);
                        }
                        product.public_price = parseFloat(this.priceForm.public_price);
                        this.$notify({
                            title: "Correcto",
                            message: 'Se ha actualizado el precio a público' + product.global_product?.name,
                            type: "success",
                        });
                    } else {
                        // Actualiza el precio en la lista local mostrados en la tabla de productos
                        const IndexProductEntry = this.localProducts.findIndex(item => item.code === product.code);
                        if (IndexProductEntry !== -1) {
                            this.localProducts[IndexProductEntry].public_price = parseFloat(this.priceForm.public_price);
                        }
                        product.public_price = parseFloat(this.priceForm.public_price);
                        this.$notify({
                            title: "Correcto",
                            message: 'Se ha actualizado el precio a público' + product.name,
                            type: "success",
                        });
                    }

                    this.editPrice = false;
                },
                onError: (err) => {
                    console.log(err);
                }
            });
        },
        closeInventoryModal() {
            this.form.reset();
            this.productEntryFound = null;
            this.showInventoryModal = false;
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
        getPageFromUrl() {
            const params = new URLSearchParams(window.location.search);
            const page = params.get('page');
            if (page) {
                this.currentPage = parseInt(page);
            }
        },
        closedTag() {
            this.localProducts = this.products;
            this.searchedWord = null;
        },
        inputFocus() {
            this.$nextTick(() => {
                this.$refs.scanInput.focus();
            });
        },
        async fetchDataForProductsView() {
            try {
                this.loading = true;
                const response = await axios.post(route('products.get-data-for-products-view'), { page: this.currentPage });

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
                    // const response = await axios.get(route('products.get-all-for-indexedDB'));
                    // const products = response.data.products;
                    // // actualizar indexedDB
                    // await addOrUpdateBatchOfItems('products', products);
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
                    // location.reload(); se requiere recargar la pagina para guardar el parametro de page en la url
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
                        // this.products = this.localProducts;
                        this.localProducts = response.data.items;
                        this.searchedWord = this.searchQuery;
                        this.searchQuery = null;
                    }

                } catch (error) {
                    console.log(error);
                } finally {
                    this.loading = false;
                    this.inputFocus();
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
                this.fetchingProduct = true;
                const response = await axios.get(route('products.search'), { params: { query: this.form.code } });
                if (response.status == 200) {
                    if (response.data.items.length == 0) {
                        this.$notify({
                            title: "No se encontró el producto",
                            type: "info",
                        });
                        this.productEntryFound = [];
                    } else {
                        this.productEntryFound = response.data.items;
                        this.$nextTick(() => {
                            this.$refs.quantityInput.focus(); // Enfocar el input de cantidad cuando se encuentra el producto
                        });
                    }
                }

            } catch (error) {
                console.log(error);
            } finally {
                this.fetchingProduct = false;
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
        this.getPageFromUrl(); //obtiene la variable page de la url.
        this.fetchDataForProductsView();

        // Escucha el evento personalizado para actualizar carrito
        emitter.on('product-deleted', this.fetchDataForProductsView);
    },
    beforeUnmount() {
        // Elimina el listener del evento cuando se desmonta el componente
        emitter.off('product-deleted', this.fetchDataForProductsView);
    }
}
</script>