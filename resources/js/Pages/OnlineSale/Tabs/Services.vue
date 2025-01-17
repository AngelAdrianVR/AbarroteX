<template>
    <div>
        <h1 class="flex items-center space-x-5 font-bold text-3xl text-center mb-12 lg:mx-10">
            <span>Servicios</span>
            <el-dropdown trigger="click" @command="handleCommand">
                <button
                    class="flex items-center space-x-2 rounded-full text-gray37 bg-grayF2 border border-grayD9 px-3 py-2 transition-all duration-200 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                    </svg>
                    <span>Categorías</span>
                </button>
                <template #dropdown>
                    <el-dropdown-menu>
                        <el-dropdown-item command="all">
                            <span class="text-xs uppercase">Todas</span>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                    <el-dropdown-menu>
                        <el-dropdown-item v-for="(category, index) in categories" :key="index" :command="index">
                            <span class="text-xs uppercase">{{ category }}</span>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
        </h1>

        <div v-if="visibleServices.length"
            class="md:grid md:grid-cols-2 lg:grid-cols-3 gap-5 mx-2 sm:mx-4 md:mx-9 space-y-4 md:space-y-0">
            <OnlineServiceCard v-for="service in visibleServices" :key="service" :service="service" :store="store" />
        </div>
        <el-empty v-else description="No hay Servicios disponibles" />

        <!-- estado de carga -->
        <div v-if="loading" class="flex justify-center items-center py-10">
            <i class="fa-sharp fa-solid fa-circle-notch text-4xl fa-spin ml-2 text-primary"></i>
        </div>
    </div>
</template>

<script>
import OnlineServiceCard from '@/Components/MyComponents/OnlineSale/OnlineServiceCard.vue';

export default {
    data() {
        return {
            loading: false,
        }
    },
    components: {
        OnlineServiceCard,
    },
    props: {
        visibleServices: Array,
        store: Object
    },
    computed: {
        //propiedad computada que retorna un arreglo con los nombres de categorias disponibles y sin repetir de los servicios basandose en visibleServices.category
        categories() {
            return this.visibleServices.map(service => service.category).filter((value, index, self) => self.indexOf(value) === index);
        },
    },
    methods: {

    }
}
</script>