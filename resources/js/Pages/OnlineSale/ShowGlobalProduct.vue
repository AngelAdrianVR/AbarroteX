<template>
    <OnlineStoreLayout :title="global_product.name">
        Producto global
    </OnlineStoreLayout>
</template>

<script>
import OnlineStoreLayout from '@/Layouts/OnlineStoreLayout.vue';

export default {
data() {
    return {

    }
},
components:{
OnlineStoreLayout
},
props:{
global_product: Object
},
methods:{}
}
</script>
