<template>
    <Loading v-if="loading" class="mt-20" />
    <div v-else>
        <div class="px-2 py-7">
            <div class="flex justify-between items-center mb-4">
                <h1 class="font-bold text-lg">Catálogo base</h1>
            </div>
            <p class="text-sm">
                Selecciona los productos disponibles en el catálogo base que vendas en tu tienda,
                transfierelos a tu tienda y personalízalos según tus necesidades. Puedes editarlos
                en cualquier momento, añadir información como el precio de venta y las existencias,
                ¡y estarán listos para vender!
            </p>

            <!-- transfer -->
            <section class="mt-10 flex flex-col items-center xl:flex-row xl:items-start xl:space-x-5">
                <div class="mx-auto xl:w-2/3 relative">
                    <el-transfer class="w-full" v-model="products" filterable filter-placeholder="Buscar producto"
                        :titles="['Catálogo base', 'Mi tienda']" :data="globalProducts"
                        @left-check-change="handleLeftCheckChange" @right-check-change="handleLeftCheckChange">
                    </el-transfer>
                    <!-- ventana de filtro izquierdo -->
                    <div v-if="showLeftFilter"
                        class="absolute bottom-20 left-0 border border[#D9D9D9] rounded-md p-4 bg-white shadow-lg z-50 w-80">
                        <div>
                            <InputLabel value="Categoría" class="ml-3 mb-1" />
                            <el-select v-model="leftFilterCategory" clearable filterable placeholder="Seleccione"
                                no-data-text="No hay opciones registradas"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="item in categories" :key="item" :label="item.name" :value="item.id" />
                            </el-select>
                        </div>
                        <div class="my-3">
                            <InputLabel value="Proveedor" class="ml-3 mb-1" />
                            <el-select v-model="leftFilterBrand" clearable filterable placeholder="Seleccione"
                                no-data-text="No hay opciones registradas"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="item in brands" :key="item.id" :label="item.name" :value="item.id" />
                            </el-select>
                        </div>
                        <div class="flex space-x-2">
                            <PrimaryButton @click="filterGlobalProducts" class="!py-1">Aplicar</PrimaryButton>
                            <CancelButton @click="showLeftFilter = false" class="!py-1">Cancelar</CancelButton>
                        </div>
                    </div>
                    <!-- ventana de filtro derecho -->
                    <div v-if="showRightFilter"
                        class="absolute bottom-20 -right-8 border border[#D9D9D9] rounded-md p-4 bg-white shadow-lg z-50 w-80">
                        <div>
                            <InputLabel value="Categoría" class="ml-3 mb-1" />
                            <el-select v-model="rightFilterCategory" clearable filterable placeholder="Seleccione"
                                no-data-text="No hay opciones registradas"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="item in categories" :key="item" :label="item.name" :value="item.id" />
                            </el-select>
                        </div>
                        <div class="my-3">
                            <InputLabel value="Proveedor" class="ml-3 mb-1" />
                            <el-select v-model="rightFilterBrand" clearable filterable placeholder="Seleccione"
                                no-data-text="No hay opciones registradas"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="item in brands" :key="item.id" :label="item.name" :value="item.id" />
                            </el-select>
                        </div>
                        <div class="flex space-x-2">
                            <PrimaryButton @click="filterGlobalProducts" class="!py-1">Aplicar</PrimaryButton>
                            <CancelButton @click="showRightFilter = false" class="!py-1">Cancelar</CancelButton>
                        </div>
                    </div>
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
                </div>
                <!-- vista previa de producto -->
                <Loading v-if="loadingProduct" class="mt-28" />
                <div v-else-if="productInfo"
                    class="rounded-lg border border-[#D9D9D9] w-3/4 mt-5 xl:mt-0 xl:w-1/2 min-h-[400px]">
                    <p class="border-b border-[#D9D9D9] font-bold px-5 py-2">Vista previa del producto</p>
                    <div class="py-3 px-7 h-full w-full">
                        <figure class="h-[200px]">
                            <img class="h-full mx-auto rounded-md" :src="productInfo?.media[0]?.original_url">
                        </figure>
                        <div class="mt-7 text-sm grid grid-cols-3 gap-x-3 gap-y-1">
                            <p>Nombre:</p>
                            <p class="font-bold col-span-2">{{ productInfo?.name ?? '-' }}</p>
                            <p>Categoría:</p>
                            <p class="font-bold col-span-2">{{ productInfo?.category?.name ?? '-' }}</p>
                            <p>Proveedor:</p>
                            <p class="font-bold col-span-2">{{ productInfo?.brand?.name ?? '-' }}</p>
                            <p>Precio sugerido:</p>
                            <p class="font-bold col-span-2">${{
                                productInfo?.public_price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                    ",") ??
                                '-'
                            }}</p>
                            <p>Código:</p>
                            <p class="font-bold col-span-2">{{ productInfo?.code ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

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
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Back from "@/Components/MyComponents/Back.vue";
import Loading from '@/Components/MyComponents/Loading.vue';
import axios from 'axios';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { addOrUpdateBatchOfItems } from '@/dbService.js';

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
            // filtros
            showLeftFilter: false, //muestra filtro izquierdo
            showRightFilter: false, //muestra filtro derecho
            leftFilterCategory: null, //información para fltrar por categoría izquierdo
            leftFilterBrand: null, //información para fltrar por Proveedor izquierdo
            rightFilterCategory: null, //información para fltrar por categoría derecho
            rightFilterBrand: null, //información para fltrar por Proveedor derecho
            showConfirmModal: false,
            // Permisos de rol
            canTransfer: ['Administrador'].includes(this.$page.props.auth.user.rol),
            // datos para la vista
            totalGlobalProducts: [],
            globalProducts: [],
            myProducts: [],
            categories: [],
            brands: [],
        };
    },
    components: {
        PrimaryButton,
        CancelButton,
        InputLabel,
        Loading,
        Back,
        ConfirmationModal,
    },
    props: {
    },
    computed: {
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
            this.globalProducts = this.totalGlobalProducts.map(product => ({
                key: product.id,
                label: product.name
            })).sort((a, b) => a.label.localeCompare(b.label));
        },
        handleLeftCheckChange(checkedProducts) {
            // Verificar si hay al menos un elemento seleccionado
            if (checkedProducts.length > 0) {
                const lastSelection = checkedProducts[checkedProducts.length - 1]; // Obtener el último elemento del arreglo

                this.getLeftProductCheckedInfo(lastSelection);
            }
        },
        revertChanges() {
            // resetear variable de local storage a false
            localStorage.setItem('pendentProcess', false);

            this.localProductsFormater(); //formatea el arreglo de products para mostrar productos de la tienda en la parte deracha del transfer
            this.globalProductsFormater(); //formatea los productos globales para que el transfer los renderice
        },
        async fetchDataForBaseCatalogView() {
            try {
                this.loading = true;
                const response = await axios.get(route('global-product-store.get-data-for-base-catalog-view'));
                if (response.status === 200) {
                    this.totalGlobalProducts = response.data.global_products;
                    this.myProducts = response.data.my_products;
                    this.categories = response.data.categories;
                    this.brands = response.data.brands;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async filterGlobalProducts() {
            try {
                const response = await axios.get(route('global-products.filter', { category_id: this.leftFilterCategory, brand_id: this.leftFilterBrand }));
                if (response.status === 200) {
                    this.globalProducts = response.data.items;
                    this.leftFilterCategory = null;
                    this.leftFilterBrand = null;
                    this.showLeftFilter = false;
                    //formatea el nuevo arreglo de productos globales filtrado
                    this.globalProducts = this.globalProducts.map(product => ({
                        key: product.id,
                        label: product.name
                    }));
                }
            } catch (error) {
                console.log(error);
            }
        },
        // recupera la informacion del producto seleccionado
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
        async transferProducts() {
            try {
                this.processing = true;
                let response = await axios.post(route('global-product-store.transfer'), { products: this.products });

                if (response.status === 200) {
                    this.initialProducts = this.products;

                    // Obtener productos
                    const response2 = await axios.get(route('products.get-all-for-indexedDB'));
                    const products = response2.data.products;

                    // actualizar lista de productos en indexedDB
                    await addOrUpdateBatchOfItems('products', products);

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
    async mounted() {
        await this.fetchDataForBaseCatalogView();

        // resetear variable de local storage a false
        localStorage.setItem('pendentProcess', false);

        this.globalProductsFormater()
        this.localProductsFormater(); //formatea el arreglo de products para mostrar productos de la tienda en la parte deracha del transfer
    }
};
</script>
