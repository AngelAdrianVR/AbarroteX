<template>
    <div>
        <div class="px-2 py-4">
            <div class="flex justify-between items-center mb-2">
                <h1 class="font-bold text-lg">Catálogo base</h1>
            </div>
            <p class="text-sm">
                Selecciona los productos disponibles en el catálogo base que vendas en tu tienda,
                transfierelos a tu tienda y personalízalos según tus necesidades. Puedes editarlos
                en cualquier momento, añadir información como el precio de venta y las existencias,
                ¡y estarán listos para vender!
            </p>

            <!-- Selector de giro -->
            <div class="custom-style text-center mt-2">
                <el-segmented @change="fetchDataForBaseCatalogView" v-model="storeType" :options="options" />
            </div>

            <Loading v-if="loading" class="mt-20" />
            
            <!-- Transfer 2.0 (hecho desde 0) -->
            <section v-else class="mt-8 flex flex-col items-center xl:flex-row xl:items-start xl:space-x-5">
                <div class="mx-auto xl:w-[90%] relative">
                    <Transfer 
                        :brands="brands" 
                        :categories="categories" 
                        :globalProducts="globalProducts"
                        :myProducts="myProducts"
                        @product-preview="getLeftProductCheckedInfo($event)"
                    />
                        
                </div>
                
                <!-- Vista previa de producto -->
                <Loading v-if="loadingProduct" class="mt-28 sm:w-3/4 xl:w-[39%]" />

                <div v-else-if="productInfo" class="rounded-2xl shadow-lg border border-gray-200 w-full sm:w-3/4 xl:w-[40%] mt-5 xl:mt-0 bg-white p-6">
                    <p class="border-b border-gray-300 font-semibold text-lg pb-3">Vista previa del producto</p>
                    <div class="py-5 flex flex-col items-center">
                        <figure class="h-48 w-48 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                            <img v-if="productInfo?.media[0]?.original_url" :src="productInfo.media[0].original_url" 
                                class="object-contain h-full w-full">
                            <span v-else class="text-gray-400 text-sm">Sin imagen</span>
                        </figure>
                        <div class="mt-6 text-sm grid grid-cols-2 gap-y-2 w-full px-4">
                            <p class="text-gray-600">Nombre:</p>
                            <p class="font-medium text-gray-900">{{ productInfo?.name ?? '-' }}</p>
                            <p class="text-gray-600">Categoría:</p>
                            <p class="font-medium text-gray-900">{{ productInfo?.category?.name ?? '-' }}</p>
                            <p class="text-gray-600">Proveedor:</p>
                            <p class="font-medium text-gray-900">{{ productInfo?.brand?.name ?? '-' }}</p>
                            <p class="text-gray-600">Precio sugerido:</p>
                            <p class="font-medium text-green-600">${{ productInfo?.public_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',') ?? '-' }}</p>
                            <p class="text-gray-600">Código:</p>
                            <p class="font-medium text-gray-900">{{ productInfo?.code ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

            </section>
        </div>
    </div>
</template>

<script>
import Transfer from "@/Components/MyComponents/Product/Transfer.vue";
import Loading from '@/Components/MyComponents/Loading.vue';
import axios from 'axios';
// import { addOrUpdateBatchOfItems } from '@/dbService.js';

export default {
    name: 'SelectGlobalProduct',
    data() {
        return {
            products: [],
            initialProducts: [],
            productInfo: null,
            processing: false,
            // carga
            loadingProduct: false,
            loading: false,

            // Permisos de rol
            canTransfer: ['Administrador'].includes(this.$page.props.auth.user.rol),
            // datos para la vista
            totalGlobalProducts: [],
            globalProducts: [],
            myProducts: [],
            categories: [],
            brands: [],
            storeType: 'Abarrotes / Supermercado',
            options: ['Abarrotes / Supermercado', 'Papelería', 'Ferretería'],
        };
    },
    components: {
        Transfer,
        Loading,
    },
    props: {
    },
    methods: {
        //recorre el arreglo de productos globales revisando que productos estan guardados en la tienda
        //y guarda en el arreglo products el (index + 1) del producto en el primer arreglo para mostrarlo en 
        //la parte derecha del transfer.
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
            this.initialProducts = this.products;
        },
        globalProductsFormater() {
            // Filtra los productos que NO estén en this.products
            const filtered = this.totalGlobalProducts.filter(product => {
                return !this.products.includes(product.id);
            });

            // Formatea y ordena los productos filtrados
            this.globalProducts = filtered.map(product => ({
                key: product.id,
                label: product.name,
                brand_id: product.brand_id,
                category_id: product.category_id
            })).sort((a, b) => a.label.localeCompare(b.label));
        },
        async fetchDataForBaseCatalogView() {
            try {
                this.loading = true;
                const response = await axios.get(route('global-product-store.get-data-for-base-catalog-view'), {
                    params: {
                        store_type: this.storeType
                    }
                });
                if (response.status === 200) {
                    this.totalGlobalProducts = response.data.global_products;
                    this.myProducts = response.data.my_products;
                    this.categories = response.data.categories;
                    this.brands = response.data.brands;
                    this.globalProductsFormater();
                    this.localProductsFormater();
                    console.log(this.totalGlobalProducts);
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        // recupera la informacion del producto seleccionado para mostrar sus detalles en la vista previa
        async getLeftProductCheckedInfo(productId) {
            try {
                this.loadingProduct = true;
                const response = await axios.get(route('global-products.fetch-info-product', productId));

                if (response.status === 200) {
                    this.productInfo = response.data.item;
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: "Advertencia",
                    message: "No se pudo cargar la información del producto",
                    type: "warning",
                });
            } finally {
                this.loadingProduct = false;

            }
        },
    },
    async created() {
        await this.fetchDataForBaseCatalogView();

        // resetear variable de local storage a false
        localStorage.setItem('pendentProcess', false);

        this.globalProductsFormater()
        this.localProductsFormater(); //formatea el arreglo de products para mostrar productos de la tienda en la parte deracha del transfer
    }
};
</script>

<style scoped>
::v-deep(.el-transfer-panel) {
  width: 300px;
  height: 350px;
  padding: 10px;
}

::v-deep(.el-transfer-panel__body) {
  font-size: 12px;
}

::v-deep(.el-transfer__buttons button) {
  font-size: 12px;
  padding: 10px 16px;
}

.custom-style .el-segmented {
  --el-segmented-item-selected-color: var(--el-text-color-primary);
  --el-segmented-item-selected-bg-color: #f3c791;
  --el-border-radius-base: 16px;
}
</style>
