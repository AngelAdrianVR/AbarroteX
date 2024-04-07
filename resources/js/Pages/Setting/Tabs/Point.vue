<template>
    <div class="text-sm">
        <p>Puedes activar la configuración para mostrar la acción en el punto de venta, y si no lo necesitas, también
            tienes la opción de desactivarla</p>

        <section class="mt-5 *:flex *:items-center">
            <div class="">
                <p class="w-1/6">Escanear productos</p>
                <el-switch v-model="value1" :loading="loading" :before-change="beforeChange1" />
            </div>
        </section>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loading: false,
            value1: false,
        }
    },
    props: {

    },
    methods: {
        beforeChange1() {
            this.loading = true
            return new Promise((resolve) => {
                setTimeout(() => {
                    this.loading = false
                    this.$notify({
                        title: "Correcto",
                        message: "Se ha agregado un nuevo producto",
                        type: "success",
                    });
                    return resolve(true)
                }, 1000)
            });
        },
    },
    mounted() {

    }
}
</script>