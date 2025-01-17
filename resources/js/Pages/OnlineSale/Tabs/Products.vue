<template>
    <div>
        <h1 class="flex items-center space-x-5 font-bold text-3xl text-center mb-12 lg:mx-10">
            <span>Productos</span>
            <el-dropdown trigger="click" @command="handleCommand">
                <button
                    class="flex items-center space-x-2 rounded-full text-gray37 bg-grayF2 border border-grayD9 px-3 py-2 transition-all duration-200 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                    </svg>
                    <span>Categoría: {{ currentCategory }}</span>
                </button>
                <template #dropdown>
                    <el-dropdown-menu>
                        <el-dropdown-item command="Todas">
                            <span class="text-xs uppercase"
                                :class="currentCategory == 'Todas' ? 'font-bold text-primary' : null">Todas</span>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                    <el-dropdown-menu>
                        <el-dropdown-item v-for="(category, index) in categories" :key="index" :command="category">
                            <span class="text-xs uppercase"
                                :class="currentCategory == category ? 'font-bold text-primary' : null">{{ category
                                }}</span>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
        </h1>
        <h1 v-if="store.online_store_properties?.enabled_free_delivery && store.online_store_properties?.min_free_delivery"
            class="font-bold md:text-xl text-center text-primary mb-1">
            Envío gratis en compra mínima de ${{ store.online_store_properties?.min_free_delivery }}</h1>
        <div v-if="visibleProducts.length"
            class="md:grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mx-2 sm:mx-4 md:mx-9 space-y-4 md:space-y-0">
            <OnlineProductCard v-for="product in filteredProducts" :key="product" :product="product" :store="store" />
        </div>
        <el-empty v-else description="No hay productos en la tienda" />

        <!-- estado de carga -->
        <div v-if="loading" class="flex justify-center items-center py-10">
            <i class="fa-sharp fa-solid fa-circle-notch text-4xl fa-spin ml-2 text-primary"></i>
        </div>
    </div>
</template>

<script>
import OnlineProductCard from '@/Components/MyComponents/OnlineSale/OnlineProductCard.vue';

export default {
    data() {
        return {
            loading: false,
            currentCategory: 'Todas',
        };
    },
    components: {
        OnlineProductCard,
    },
    props: {
        store: Object,
        visibleProducts: Array
    },
    computed: {
        //propiedad computada que retorna un arreglo con los nombres de categorias disponibles y sin repetir de los productos basandose en visibleProduct.category.name
        categories() {
            return this.visibleProducts.map(product => product.category.name).filter((value, index, self) => self.indexOf(value) === index);
        },
        filteredProducts() {
            return this.visibleProducts.filter(product => this.currentCategory === 'Todas' || product.category.name === this.currentCategory);
        }
    },
    methods: {
        handleCommand(command) {
            this.currentCategory = command;
        },
    },
};
</script>
