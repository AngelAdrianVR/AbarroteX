<template>
    <!-- estado de carga -->
    <div v-if="loading" class="flex justify-center items-center py-10">
        <i class="fa-sharp fa-solid fa-circle-notch text-4xl fa-spin ml-2 text-primary"></i>
    </div>
    <div v-else class="flex">
        <figure class="border border-grayD9 rounded-md p-3">
            <img src="" alt="">
        </figure>
    </div>
</template>

<script>
export default {
data() {
    return {
        loading: false,
        product: null
    }
},
props:{
    productId: Number
},
methods:{
    async getProductInfo() {
        this.loading = true;
        try {
            
        } catch (error) {
            
        } finally {
            this.loading = false;
        }
    }
},
mounted() {
    this.getProductInfo();
}
}
</script>
