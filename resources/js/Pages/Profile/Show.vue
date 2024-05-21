<template>
    <AppLayout title="Perfil">
        <main class="mx-2 lg:mx-56 my-10 text-sm">
            <BasicInfo />
            <Security :sessions="sessions" class="mt-7" />
            <Suscription v-if="canSeeSuscription" class="mt-7" />
        </main>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import BasicInfo from './Partials/BasicInfo.vue';
import Security from './Partials/Security.vue';
import Suscription from './Partials/Suscription.vue';

export default {
    data() {
        return {
            // Permisos de rol actual
            canSeeSuscription: ['Administrador'].includes(this.$page.props.auth.user.rol),
        }
    },
    components: {
        AppLayout,
        BasicInfo,
        Security,
        Suscription,
    },
    props: {
        user: Object,
        sessions: Array,
    },
    methods: {

    },
    mounted() {
        // resetear variable de local storage a false
        localStorage.setItem('pendentProcess', false);
    }
}
</script>