<template>
    <main class="flex flex-col md:flex-row gap-4">
        <!-- Lado izquierdo -->
        <section class="border border-[#D9D9D9] rounded-lg w-[40%] h-[550px]">
            <div class="flex justify-between border-b border-[#D9D9D9]">
                <h1 class="text-[#373737] font-bold text-base py-3 px-4">Catálogo disponible de Ezy Ventas</h1>
                <span class="text-sm py-2 px-3">({{ selectedLeftProducts.length }} / {{ filteredGlobalProducts.length }})</span>
            </div>

            <article class="my-1 p-3">
                <div class="flex justify-between items-center space-x-3">
                    <!-- Buscar por nombre o código del producto -->
                    <div class="w-[90%] relative">
                        <input v-model="searchLeftQuery" @keyup.enter="searchProducts"
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
                                <ThirthButton @click="cleanFilter" class="!py-1">Limpiar</ThirthButton>
                            </div>
                        </section>
                    </div>
                </div>
            </article>
            
            <SmallLoading v-if="searchLeftLoading" class="my-3 mx-auto" />

            <!-- Lista de productos -->
            <article v-else class="overflow-y-auto h-[425px]">
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

                    <button @click="handleProductPreview(product)" 
                        class="text-primary bg-primarylight rounded-full size-6 text-xs text-left w-10 flex items-center justify-center focus:bg-gray-300 focus:text-gray-700 transition-all ease-linear duration-200">
                        <i class="fa-solid fa-eye text-sm"></i>
                    </button>
                </div>
                <el-empty v-if="!filteredGlobalProducts.length" description="No hay productos" />
            </article>
        </section>

        <!-- Botones de transferencia -->
        <section class="flex items-center space-x-3">
            <button class="text-primary bg-primarylight rounded-md h-12 w-7 flex items-center justify-center hover:bg-primary hover:text-white transition-all ease-linear duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>
            <button class="text-primary bg-primarylight rounded-md h-12 w-7 flex items-center justify-center hover:bg-primary hover:text-white transition-all ease-linear duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </section>

        <!-- Lado derecho -->
        <section class="border border-[#D9D9D9] rounded-lg w-[40%] h-[550px]">
            <div class="flex justify-between border-b border-[#D9D9D9]">
                <h1 class="text-[#373737] font-bold text-base py-3 px-4">Mi tienda</h1>
                <span class="text-sm py-2 px-3">({{ selectedRightProducts.length }} / {{ myProducts.length }})</span>
            </div>

            <article class="my-1 p-3">
                <div class="flex justify-between items-center space-x-3">
                    <!-- Buscar por nombre o código del producto -->
                    <div class="w-[90%] relative">
                        <input v-model="searchRightQuery" @keyup.enter="searchProducts"
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
                                <PrimaryButton @click="filterGlobalProducts" class="!py-1"
                                    :disabled="!selectedRightBrands.length && !selectedRightCategories.length">
                                    Aplicar
                                </PrimaryButton>
                                <ThirthButton @click="cleanFilter" class="!py-1">Limpiar</ThirthButton>
                            </div>
                        </section>
                    </div>
                </div>
            </article>
            
            <SmallLoading v-if="searchRightLoading" class="my-3 mx-auto" />

            <!-- Lista de productos -->
            <article v-else class="overflow-y-auto h-[425px]">
                <div v-for="product in myProducts" :key="product.key" class="flex items-center space-x-1 space-y-2 px-2">
                    <!-- Checkbox individual -->
                    <div class="mt-[6px] w-4">
                        <el-checkbox
                        :model-value="selectedRightProducts.includes(product.key)"
                        @change="toggleProduct(product.key)"
                        class="!text-[#979797]"
                        />
                    </div>

                    <p class="text-gray-700 text-[15px] w-[85%]">{{ product.global_product.name }}</p>

                    <button @click="handleProductPreview(product)" 
                        class="text-primary bg-primarylight rounded-full size-6 text-xs text-left w-10 flex items-center justify-center focus:bg-gray-300 focus:text-gray-700 transition-all ease-linear duration-200">
                        <i class="fa-solid fa-eye text-sm"></i>
                    </button>
                </div>
                <el-empty v-if="!myProducts.length" description="No hay productos" />
            </article>
        </section>
    </main>
</template>

<script>
import SmallLoading from '@/Components/MyComponents/SmallLoading.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import { ref } from 'vue'

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
        filteredGlobalProducts: this.globalProducts, // Almacena los productos filtrados según la búsqueda

        // Parte derecha
        searchRightQuery: '', // Variable para almacenar la consulta de búsqueda
        showRightFilter: false, // Controla la visibilidad del filtro
        selectedRightBrands: [], // Almacena las marcas seleccionadas
        selectedRightCategories: [], // Almacena las categorías seleccionadas
        selectedRightProducts: [], // Almacena el producto seleccionado
        searchRightLoading: false, // Controla el estado de carga durante la búsqueda
    };
},
components: {
    PrimaryButton,
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
methods: {
    toggleLeftProduct(productId) {
      const index = this.selectedLeftProducts.indexOf(productId)
      if (index === -1) {
        this.selectedLeftProducts.push(productId)
      } else {
        this.selectedLeftProducts.splice(index, 1)
      }
    },
    // searchProducts() {
    //     this.$emit('search-products', this.searchLeftQuery);
    //     // Limpia los filtros aplicados
    //     this.selectedLeftBrands = [];
    //     this.selectedLeftCategories = [];
    //     this.loading(); // Llama a la función de loading
    // },
    searchProducts() {
        // Limpia los filtros aplicados
        this.selectedLeftBrands = [];
        this.selectedLeftCategories = [];

        // Realiza la búsqueda localmente en la vista
        const query = this.searchLeftQuery.toLowerCase();
        this.filteredGlobalProducts = this.globalProducts.filter(product =>
            product.label.toLowerCase().includes(query)
        );

        this.loading();
    },
    filterGlobalProducts() {
        let filtered = this.globalProducts;

        // Filtro por múltiples categorías
        if (this.selectedLeftCategories.length > 0) {
            filtered = filtered.filter(product =>
                this.selectedLeftCategories.includes(product.category_id)
            );
        }

        // Filtro por múltiples marcas
        if (this.selectedLeftBrands.length > 0) {
            filtered = filtered.filter(product =>
                this.selectedLeftBrands.includes(product.brand_id)
            );
        }

        // Actualiza la lista global con los resultados filtrados
        this.filteredGlobalProducts = filtered.map(product => ({
            key: product.id,
            label: product.name
        })).sort((a, b) => a.label.localeCompare(b.label));

        this.loading();
    },
    // filterGlobalProducts() {
    //     this.$emit('filter-global-products', {
    //         brand_ids: this.selectedLeftBrands,
    //         category_ids: this.selectedLeftCategories
    //     });
    //     this.searchLeftQuery = ''; // Limpia la consulta de búsqueda
    //     this.showLeftFilter = false; // Cierra el filtro después de aplicar
    //     this.loading(); // Llama a la función de loading
    // },
    cleanFilter() {
        // Limpia los filtros aplicados
        this.selectedLeftBrands = [];
        this.selectedLeftCategories = [];
        this.$emit('clean-filter');
    },
    handleProductPreview(globalProduct) {
        this.$emit('product-preview', globalProduct.key);
    },
    loading(){
        this.searchLeftLoading = true; // Muestra el loading al iniciar la búsqueda
        setTimeout(() => {
            this.searchLeftLoading = false; // Oculta el loading después de un tiempo
        }, 1000); // Simula un retraso de 1 segundo para la búsqueda
    }
}
}
</script>
