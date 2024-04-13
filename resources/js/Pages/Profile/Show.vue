<template>
    <AppLayout title="Perfil">
        <main class="mx-2 md:mx-56 my-10 text-sm">
            <BasicInfo />
            <Suscription class="mt-7" />
        </main>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import BasicInfo from './Partials/BasicInfo.vue';
import Suscription from './Partials/Suscription.vue';

export default {
    data() {
        return {

        }
    },
    components: {
        AppLayout,
        BasicInfo,
        Suscription,
    },
    props: {
        user: Object,
    },
    methods: {
       
    }
}
</script>