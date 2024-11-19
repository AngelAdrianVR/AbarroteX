<template>
    <div>
        <h1 class="font-bold text-3xl text-center mb-12">Servicios</h1>

        <div v-if="visibleServices.length"
            class="md:grid md:grid-cols-2 lg:grid-cols-3 gap-5 mx-2 sm:mx-4 md:mx-9 space-y-4 md:space-y-0">
            <OnlineServiceCard v-for="service in visibleServices" :key="service" :service="service"
                :store="store" />
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
components:{
    OnlineServiceCard,
},
props:{
    visibleServices: Array,
    store: Object
},
methods:{

}
}
</script>