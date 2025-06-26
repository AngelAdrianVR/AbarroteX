<template>
    <main class="flex flex-col md:flex-row gap-4">
        <!-- Lado izquierdo -->
        <section class="border border-[#D9D9D9] rounded-lg w-[40%] h-[580px]">
            <div class="flex justify-between items-center border-b border-[#D9D9D9]">
                <h1 class="text-[#373737] font-bold text-base py-3 px-4">Catálogo disponible de Ezy Ventas</h1>
                <span class="text-sm py-2 px-3">({{ selectedLeftProducts.length }} / {{ filteredGlobalProducts.length }})</span>
            </div>

            <article class="mb-1 px-3 pt-3">
                <div class="flex justify-between items-center space-x-3">
                    <!-- Buscar por nombre o código del producto -->
                    <div class="w-[90%] relative">
                        <input v-model="searchLeftQuery" @keyup.enter="searchLeftProducts"
                            class="input w-full pl-9" placeholder="Buscar por nombre o código" type="search">
                        <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                    </div>

                    <div class="relative">
                        <!-- filtro -->
                        <button @click.stop="showLeftFilter = !showLeftFilter"
                            class="border border-[#D9D9D9] rounded-full flex items-center justify-center size-8">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="16" width="16"
                                id="Filter-Sort-Lines-Descending--Streamline-Ultimate">
                                <desc>Filter Sort Lines Descending Streamline Icon: https://streamlinehq.com</desc>
                                <defs></defs>
                                <title>filter</title>
                                <path d="M0.73 4.2791H23.27" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="1"></path>
                                <path d="M3.131 9.426H20.869" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="1"></path>
                                <path d="M8.7141 19.7209H15.2859" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="1"></path>
                                <path d="M5.531 14.573H18.469" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="1"></path>
                            </svg>
                        </button>

                        <section v-if="showLeftFilter"
                            class="absolute top-9 right-0 lg:-left-40 border border[#D9D9D9] rounded-md p-4 bg-white shadow-lg z-10 w-96">
                            <div>
                                <InputLabel value="Proveedor" class="ml-3 mb-1" />
                                <el-select
                                    v-model="selectedLeftBrands"
                                    multiple
                                    filterable
                                    allow-create
                                    default-first-option
                                    :reserve-keyword="false"
                                    placeholder="Seleccione"
                                    style="width: 100%"
                                >
                                <el-option
                                    v-for="brand in brands"
                                    :key="brand.id"
                                    :label="brand.name"
                                    :value="brand.id"
                                />
                                </el-select>
                            </div>
                            <div class="mt-3">
                                <InputLabel value="Categoría" class="ml-3 mb-1" />
                                <el-select
                                    v-model="selectedLeftCategories"
                                    multiple
                                    filterable
                                    allow-create
                                    default-first-option
                                    :reserve-keyword="false"
                                    placeholder="Seleccione"
                                    style="width: 100%"
                                >
                                <el-option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :label="category.name"
                                    :value="category.id"
                                />
                                </el-select>
                            </div>
                            <div class="flex space-x-2 mt-3">
                                <PrimaryButton @click="filterGlobalProducts" class="!py-1"
                                    :disabled="!selectedLeftBrands.length && !selectedLeftCategories.length">
                                    Aplicar
                                </PrimaryButton>
                                <ThirthButton @click="cleanLeftFilter" class="!py-1">Limpiar</ThirthButton>
                            </div>
                        </section>
                    </div>
                </div>

                <!-- Checkbox para seleccionar todos -->
                <div v-if="filteredGlobalProducts.length > 0" class="flex items-center">
                    <el-checkbox
                        :model-value="allLeftSelected"
                        @change="toggleSelectAllLeft"
                        class="!text-[#979797]"
                    />
                    <p class="ml-2 text-gray-700 text-[15px]">Seleccionar todos</p>
                </div>
            </article>
            
            <SmallLoading v-if="searchLeftLoading" class="my-3 mx-auto" />

            <!-- Lista de productos -->
            <article v-else class="overflow-y-auto h-[435px]">
                <div v-for="product in filteredGlobalProducts" :key="product.key" class="flex items-center space-x-1 space-y-2 px-2">
                    <!-- Checkbox individual -->
                    <div class="mt-[6px] w-4">
                        <el-checkbox
                        :model-value="selectedLeftProducts.includes(product.key)"
                        @change="toggleLeftProduct(product.key)"
                        class="!text-[#979797]"
                        />
                    </div>

                    <p class="text-gray-700 text-[15px] w-[85%]">{{ product.label }}</p>

                    <button @click="handleLeftProductPreview(product)" 
                        class="text-primary bg-primarylight rounded-full size-6 text-xs text-left w-10 flex items-center justify-center focus:bg-gray-300 focus:text-gray-700 transition-all ease-linear duration-200">
                        <i class="fa-solid fa-eye text-sm"></i>
                    </button>
                </div>
                <el-empty v-if="!filteredGlobalProducts.length" description="No hay productos" />
            </article>
        </section>

        <!-- Botones de transferencia -->
        <section class="flex items-center space-x-3">
            <button :disabled="!selectedRightProducts.length" @click="transferToLeft" class="text-primary bg-primarylight rounded-md h-12 w-7 flex items-center justify-center hover:bg-primary hover:text-white disabled:cursor-not-allowed disabled:bg-gray-200 disabled:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>
            <button :disabled="!selectedLeftProducts.length" @click="transferToRight" class="text-primary bg-primarylight rounded-md h-12 w-7 flex items-center justify-center hover:bg-primary hover:text-white disabled:cursor-not-allowed disabled:bg-gray-200 disabled:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </section>

        <!-- Lado derecho -->
        <section class="border border-[#D9D9D9] rounded-lg w-[40%] h-[580px]">
            <div class="flex justify-between items-center border-b border-[#D9D9D9]">
                <h1 class="text-[#373737] font-bold text-base py-3 px-4">Mi tienda</h1>
                <span class="text-sm py-2 px-3">({{ selectedRightProducts.length }} / {{ filteredMyProducts.length }})</span>
            </div>

            <article class="mb-1 px-3 pt-3">
                <div class="flex justify-between items-center space-x-3">
                    <!-- Buscar por nombre o código del producto -->
                    <div class="w-[90%] relative">
                        <input v-model="searchRightQuery" @keyup.enter="searchRightProducts"
                            class="input w-full pl-9" placeholder="Buscar por nombre o código" type="search">
                        <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                    </div>

                    <div class="relative">
                        <!-- filtro -->
                        <button @click.stop="showRightFilter = !showRightFilter"
                            class="border border-[#D9D9D9] rounded-full flex items-center justify-center size-8">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="16" width="16"
                                id="Filter-Sort-Lines-Descending--Streamline-Ultimate">
                                <desc>Filter Sort Lines Descending Streamline Icon: https://streamlinehq.com</desc>
                                <defs></defs>
                                <title>filter</title>
                                <path d="M0.73 4.2791H23.27" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="1"></path>
                                <path d="M3.131 9.426H20.869" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="1"></path>
                                <path d="M8.7141 19.7209H15.2859" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="1"></path>
                                <path d="M5.531 14.573H18.469" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="1"></path>
                            </svg>
                        </button>

                        <section v-if="showRightFilter"
                            class="absolute top-9 right-0 lg:-left-40 border border[#D9D9D9] rounded-md p-4 bg-white shadow-lg z-10 w-96">
                            <div>
                                <InputLabel value="Proveedor" class="ml-3 mb-1" />
                                <el-select
                                    v-model="selectedRightBrands"
                                    multiple
                                    filterable
                                    allow-create
                                    default-first-option
                                    :reserve-keyword="false"
                                    placeholder="Seleccione"
                                    style="width: 100%"
                                >
                                <el-option
                                    v-for="brand in brands"
                                    :key="brand.id"
                                    :label="brand.name"
                                    :value="brand.id"
                                />
                                </el-select>
                            </div>
                            <div class="mt-3">
                                <InputLabel value="Categoría" class="ml-3 mb-1" />
                                <el-select
                                    v-model="selectedRightCategories"
                                    multiple
                                    filterable
                                    allow-create
                                    default-first-option
                                    :reserve-keyword="false"
                                    placeholder="Seleccione"
                                    style="width: 100%"
                                >
                                <el-option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :label="category.name"
                                    :value="category.id"
                                />
                                </el-select>
                            </div>
                            <div class="flex space-x-2 mt-3">
                                <PrimaryButton @click="filterMyProducts" class="!py-1"
                                    :disabled="!selectedRightBrands.length && !selectedRightCategories.length">
                                    Aplicar
                                </PrimaryButton>
                                <ThirthButton @click="cleanRightFilter" class="!py-1">Limpiar</ThirthButton>
                            </div>
                        </section>
                    </div>
                </div>

                <!-- Checkbox para seleccionar todos -->
                <div v-if="filteredMyProducts.length" class="flex items-center">
                    <el-checkbox
                        :model-value="allRightSelected"
                        @change="toggleSelectAllRight"
                        class="!text-[#979797]"
                    />
                    <p class="ml-2 text-gray-700 text-[15px]">Seleccionar todos</p>
                </div>
            </article>
            
            <SmallLoading v-if="searchRightLoading" class="my-3 mx-auto" />

            <!-- Lista de productos -->
            <article v-else class="overflow-y-auto h-[435px]">
                <div v-for="product in filteredMyProducts" :key="product.key" class="flex items-center space-x-1 space-y-2 px-2">
                    <!-- Checkbox individual -->
                    <div class="mt-[6px] w-4">
                        <el-checkbox
                        :model-value="selectedRightProducts.includes(product.global_product_id)"
                        @change="toggleRightProduct(product.global_product_id)"
                        class="!text-[#979797]"
                        />
                    </div>

                    <p class="text-gray-700 text-[15px] w-[85%]">{{ product.global_product.name }}</p>

                    <button @click="handleRightProductPreview(product)" 
                        class="text-primary bg-primarylight rounded-full size-6 text-xs text-left w-10 flex items-center justify-center focus:bg-gray-300 focus:text-gray-700 transition-all ease-linear duration-200">
                        <i class="fa-solid fa-eye text-sm"></i>
                    </button>
                </div>
                <el-empty v-if="!filteredMyProducts.length" description="No hay productos" />
            </article>
        </section>
    </main>

    <!-- Boton para transferir los poductos -->
    <transition enter-active-class="transition ease-in duration-300" enter-from-class="opacity-0"
        enter-to-class="opacity-100">
        <div v-if="productListHasChange" class="flex space-x-2 items-center col-span-full mt-3">
            <el-tooltip content="Revertir cambios" placement="left">
                <button @click="revertChanges"
                    class="rounded-full size-9 border border-[#c4c4c4] flex items-center justify-center"><i
                        class="fa-solid fa-rotate-left"></i></button>
            </el-tooltip>
            <PrimaryButton :disabled="processing" @click="showConfirmModal = true">
                <i v-if="processing"
                    class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                Guardar cambios
            </PrimaryButton>
        </div>
    </transition>

    <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title>
            Transferir productos
        </template>
        <template #content>
            <p>
                • Los productos de catálogo base movidos a tu tienda ya estarán disponibles para crear ventas. <br>
                • Los productos de tu tienda movidos a catálogo base ya no se mostrarán.
                ¿Deseas guardar los cambios? <br><br>
                El proceso puede demorar unos minutos, dependiendo de la cantidad de productos a cargar.

            </p>
        </template>
        <template #footer>
            <div class="flex items-center space-x-1">
                <CancelButton @click="showConfirmModal = false" :disabled="processing">Cancelar</CancelButton>
                <PrimaryButton @click="transferProducts" :disabled="processing">
                    <i v-if="processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    Si, continuar
                </PrimaryButton>
            </div>
        </template>
    </ConfirmationModal>
</template>

<script>
import SmallLoading from '@/Components/MyComponents/SmallLoading.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import axios from 'axios';

export default {
data() {
    return {
        // Parte izquierda
        searchLeftQuery: '', // Variable para almacenar la consulta de búsqueda
        showLeftFilter: false, // Controla la visibilidad del filtro
        selectedLeftBrands: [], // Almacena las marcas seleccionadas
        selectedLeftCategories: [], // Almacena las categorías seleccionadas
        selectedLeftProducts: [], // Almacena el producto seleccionado
        searchLeftLoading: false, // Controla el estado de carga durante la búsqueda
        filteredGlobalProducts: [... this.globalProducts], // Almacena los productos filtrados según la búsqueda

        // Parte derecha
        searchRightQuery: '', // Variable para almacenar la consulta de búsqueda
        showRightFilter: false, // Controla la visibilidad del filtro
        selectedRightBrands: [], // Almacena las marcas seleccionadas
        selectedRightCategories: [], // Almacena las categorías seleccionadas
        selectedRightProducts: [], // Almacena el producto seleccionado
        searchRightLoading: false, // Controla el estado de carga durante la búsqueda
        filteredMyProducts: [... this.myProducts], // Almacena los productos filtrados según la búsqueda
        initialProducts: [], // Almacena los productos iniciales para comparar cambios
        products: [], // Almacena los productos que se van a transferir

        // Botones de transferencia
        processing: false, // Controla el estado de procesamiento al guardar cambios

        //generales
        showConfirmModal: false,
    };
},
components: {
    ConfirmationModal,
    PrimaryButton,
    CancelButton,
    SmallLoading,
    ThirthButton,
    InputLabel,
},
props:{
    brands: Array,
    categories: Array,
    globalProducts: Array, // Lista de productos globales
    myProducts: Array, // Lista de productos de la tienda
},
emits: [
    'product-preview',
    'filter-global-products', 
    'clean-filter',
    'search-products'
    ], // Emite un evento cuando se selecciona un producto para vista previa
    watch: {
        globalProducts: {
            handler(newVal) {
                this.filteredGlobalProducts = [...newVal]; // Resetea los productos filtrados al cargar nuevos productos
            },
            immediate: true, // Ejecuta el watcher inmediatamente al montar el componente
        },
    },
computed: {
    allLeftSelected() {
        return this.filteredGlobalProducts.length > 0 &&
                this.filteredGlobalProducts.every(product =>
                    this.selectedLeftProducts.includes(product.key)
                );
    },
    allRightSelected() {
        return this.myProducts.length > 0 &&
               this.myProducts.every(product =>
                   this.selectedRightProducts.includes(product.key)
               );
    },
    productListHasChange() {
        const hasChanged = !this.arraysAreEqual(this.initialProducts, this.products);

        // indicar al navegador mediante el local storage que hay proceso pendiente
        const pendentProcess = JSON.parse(localStorage.getItem('pendentProcess'));
        if (!pendentProcess && hasChanged) {
            // guardar el valor en el localStorage
            localStorage.setItem('pendentProcess', true);
        }

        return hasChanged;
    },

},
methods: {
    arraysAreEqual(arr1, arr2) {
        // Si la longitud de los arrays es diferente, retornar false
        if (arr1.length !== arr2.length) {
            return false;
        }

        // Ordenar los arrays de menor a mayor
        const sortedArr1 = arr1.slice().sort();
        const sortedArr2 = arr2.slice().sort();

        // Verificar si cada elemento es igual en valor
        return sortedArr1.every((element, index) => {
            return element === sortedArr2[index];
        });
    },
    toggleLeftProduct(productId) {
        const index = this.selectedLeftProducts.indexOf(productId)
        if (index === -1) {
            this.selectedLeftProducts.push(productId)
        } else {
            this.selectedLeftProducts.splice(index, 1)
        }
    },
    toggleRightProduct(productKey) {
        const index = this.selectedRightProducts.indexOf(productKey);
        if (index === -1) {
            this.selectedRightProducts.push(productKey);
        } else {
            this.selectedRightProducts.splice(index, 1);
        }
    },
    toggleSelectAllRight() {
        if (this.allRightSelected) {
            // Deseleccionar todos
            this.selectedRightProducts = [];
        } else {
            // Seleccionar todos los productos de la lista derecha
            this.selectedRightProducts = this.filteredMyProducts.map(p => p.global_product_id);
        }
    },
    toggleSelectAllLeft() {
        if (this.allLeftSelected) {
            // Deseleccionar todos
            this.selectedLeftProducts = [];
        } else {
            // Seleccionar todos los visibles
            this.selectedLeftProducts = this.filteredGlobalProducts.map(p => p.key);
        }
    },
    searchLeftProducts() {
        // Limpia los filtros aplicados
        this.selectedLeftBrands = [];
        this.selectedLeftCategories = [];

        // Realiza la búsqueda localmente en la vista
        const query = this.searchLeftQuery.toLowerCase();
        this.filteredGlobalProducts = this.globalProducts.filter(product =>
            product.label.toLowerCase().includes(query)
        );

        this.loading('left');
    },
    searchRightProducts() {
        // Limpia los filtros aplicados
        this.selectedRightBrands = [];
        this.selectedRightCategories = [];

        const query = this.searchRightQuery.toLowerCase();

        this.filteredMyProducts = this.myProducts.filter(product =>
            product.global_product.name.toLowerCase().includes(query)
        );

        this.loading('right');
    },
    filterGlobalProducts() {
        let filtered = this.globalProducts;

        // Filtro por múltiples categorías
        if (this.selectedLeftCategories.length > 0) {
            this.filteredGlobalProducts = filtered.filter(product =>
                this.selectedLeftCategories.includes(product.category_id)
            );
        }

        // Filtro por múltiples marcas
        if (this.selectedLeftBrands.length > 0) {
            this.filteredGlobalProducts = filtered.filter(product =>
                this.selectedLeftBrands.includes(product.brand_id)
            );
        }

        this.searchLeftQuery = ''; // Limpia la consulta de búsqueda
        this.showLeftFilter = false; // Cierra el filtro después de aplicar
        this.loading('left');
    },
    filterMyProducts() {
        let filtered = this.myProducts;

        // Filtro por múltiples categorías
        if (this.selectedRightCategories.length > 0) {
            this.filteredMyProducts = filtered.filter(product =>
                this.selectedRightCategories.includes(product.global_product.category_id)
            );
        }

        // Filtro por múltiples marcas
        if (this.selectedRightBrands.length > 0) {
            this.filteredMyProducts = filtered.filter(product =>
                this.selectedRightBrands.includes(product.global_product.brand_id)
            );
        }

        this.searchRightQuery = '';      // Limpia la consulta de búsqueda
        this.showRightFilter = false;    // Cierra el filtro después de aplicar
        this.loading('right');
    },
    cleanLeftFilter() {
        // Limpia los filtros aplicados en la parte izquierda
        this.selectedLeftBrands = [];
        this.selectedLeftCategories = [];
        this.searchLeftQuery = ''; // Limpia la consulta de búsqueda
        this.filteredGlobalProducts = this.globalProducts; // Resetea los productos filtrados
        this.showLeftFilter = false; // Cierra el filtro
        this.loading('left'); // Llama a la función de loading
    },
    cleanRightFilter() {
        // Limpia los filtros aplicados en la parte derecha
        this.selectedRightBrands = [];
        this.selectedRightCategories = [];
        this.searchRightQuery = ''; // Limpia la consulta de búsqueda
        this.filteredMyProducts = this.myProducts; // Resetea los productos filtrados
        this.showRightFilter = false; // Cierra el filtro
        this.loading('right'); // Llama a la función de loading
    },
    // Mover productos seleccionados de izquierda a derecha
    transferToRight() {
        const selected = this.globalProducts.filter(product =>
            this.selectedLeftProducts.includes(product.key)
        );
        
        // Agrega al lado derecho si aún no están
        selected.forEach(product => {
            if (!this.filteredMyProducts.find(p => p.key === product.key)) {
                this.filteredMyProducts.push({
                    key: product.key,
                    global_product: {
                        id: product.key,
                        name: product.label,
                        brand_id: product.brand_id,
                        category_id: product.category_id
                    },
                });
                this.products.push(product.key); // Agrega el producto a la lista de productos transferidos
            }
        });

        // Elimina los productos seleccionados del lado izquierdo
        this.filteredGlobalProducts = this.filteredGlobalProducts.filter(
            product => !this.selectedLeftProducts.includes(product.key)
        );

        // Limpiar selección izquierda
        this.selectedLeftProducts = [];
    },
    // Mover productos seleccionados de derecha a izquierda
    transferToLeft() {
        const selected = this.myProducts.filter(product =>
            this.selectedRightProducts.includes(product.global_product.id)
        );
        // Agrega al lado izquierdo si aún no están
        selected.forEach(product => {
            const productId = product.global_product.id;

            if (!this.filteredGlobalProducts.find(p => p.key === productId)) {
                this.filteredGlobalProducts.push({
                    key: productId,
                    label: product.global_product.name,
                    // brand_id: product.global_product.brand_id || null,
                    // category_id: product.global_product.category_id || null
                });
                // Eliminar el producto del arreglo de productos en tienda (para detectar cambios)
                this.products = this.products.filter(id => id !== productId);
            }
        });

        // Elimina del lado derecho los productos transferidos
        this.filteredMyProducts = this.filteredMyProducts.filter(
            product => !this.selectedRightProducts.includes(product.global_product.id)
        );

        // Limpiar selección derecha
        this.selectedRightProducts = [];
    },

    handleLeftProductPreview(globalProduct) {
        this.$emit('product-preview', globalProduct.key);
    },
    handleRightProductPreview(product) {
        this.$emit('product-preview', product.global_product_id);
    },
    loading(side){
        if ( side === 'left' ) {
            this.searchLeftLoading = true; // Muestra el loading al iniciar la búsqueda
            setTimeout(() => {
                this.searchLeftLoading = false; // Oculta el loading después de un tiempo
            }, 1000); // Simula un retraso de 1 segundo para la búsqueda
        } else {
            this.searchRightLoading = true; // Muestra el loading al iniciar la búsqueda
            setTimeout(() => {
                this.searchRightLoading = false; // Oculta el loading después de un tiempo
            }, 1000); // Simula un retraso de 1 segundo para la búsqueda
        }
    },
    revertChanges() {
        // resetear variable de local storage a false
        localStorage.setItem('pendentProcess', false);

        this.localProductsFormater(); //formatea el arreglo de products para mostrar productos de la tienda en la parte deracha del transfer
        this.filteredGlobalProducts = [... this.globalProducts]; // Resetea los productos filtrados
        this.filteredMyProducts = [... this.myProducts]; // Resetea los productos filtrados
    },
    localProductsFormater() {
        this.products = [];
        // Utiliza map en lugar de forEach para transformar los datos
        this.globalProducts.map((globalProduct, index) => {
            // Verifica si el nombre del producto global está presente en myProducts
            if (this.myProducts.some(myProduct => myProduct.global_product.name === globalProduct.label)) {
                // Si está presente, agrega el índice al arreglo foundIndexes
                this.products.push(globalProduct.key);
            }
        });
        // inicializar numero de productos en la tienda para saber si se quitan o agregan
        this.initialProducts = [...this.products];
    },
    async transferProducts() {
        try {
            this.processing = true;
            let response = await axios.post(route('global-product-store.transfer'), { products: this.products });

            if (response.status === 200) {
                this.initialProducts = [...this.products];

                if (response.data.rejected_products.length) {
                    this.$notify({
                        title: "Límite de productos alcanzado",
                        message: "Los siguientes productos no se puedieron transferir a tu tienda debido a que llegaste al limite para tu paquete actual: " + response.data.rejected_products.join(', '),
                        type: "warning",
                    });
                } else {
                    this.$notify({
                        title: "Éxito",
                        message: "¡Se han transferido los productos a tu tienda!",
                        type: "success",
                    });
                }

                this.showConfirmModal = false;

                // resetear variable de local storage a false
                localStorage.setItem('pendentProcess', false);
            }
        } catch (error) {
            console.log(error);
            this.$notify({
                title: "No se pudo completar la petición",
                message: "Algo salió mal, vuelve a intentarlo más tarde",
                type: "error",
            });
        } finally {
            this.processing = false;
        }
    },
},
mounted() {
    this.localProductsFormater();
},
}
</script>
