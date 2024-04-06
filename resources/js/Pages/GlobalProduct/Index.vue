<template>
    <AppLayout title="Productos globales">

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

export default {
data() {
    return {

    }
},
components:{
AppLayout,
PrimaryButton
},
props:{
global_products: Array
},
methods:{
    
}
}
</script>