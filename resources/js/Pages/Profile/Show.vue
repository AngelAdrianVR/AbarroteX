<template>
    <AppLayout title="Perfil">
        <main class="mx-2 md:mx-56 mt-10 text-sm">
            <section>
                <h1 class="ml-3">Información básica</h1>
                <div class="rounded-[5px] border border-grayD9 px-3 py-4 mt-3">
                    <h2>Información de la Tienda</h2>
                    <div class="grid grid-cols-3 gap-1 mt-2">
                        <b>Nombre de la Tienda </b>
                        <b class="col-span-2">Nombre de la Tienda </b>
                        <p>{{ $page.props.auth.user.store.name }}</p>
                        <p class="col-span-2">Nombre de la Tienda </p>
                    </div>  
                </div>
            </section>
        </main>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    data() {
        return {

        }
    },
    components: {
        AppLayout,
    },
    props: {
        user: Object,
    },
    methods: {

    }
}
</script>