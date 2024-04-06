<template>
    <div class="text-[15px]">
        <p>Puedes activar la configuración para mostrar la acción en el punto de venta, y si no lo necesitas, también tienes la opción de desactivarla</p>

        <section class="flex w-full space-x-2 mt-5">
            <div class="w-48 space-y-2">
                <p>Escanear productos</p>
                <p>Editar precio unitario</p>
                <p>Agregar clientes</p>
                <p>Descuentos</p>
                <p>Inventario</p>
            </div>
            <div class="space-y-2">
                <el-switch
                    v-model="value1"
                    :loading="loading"
                    :before-change="beforeChange1"
                />
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
props:{

},
methods:{
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
    }
}
}
</script>

<style scoped>
.el-switch {
  --el-switch-on-color: #F68C0F; /* Cambia este valor al color que desees */
}
.el-switch {
  --el-switch-off-color: #CCCCCC; /* Cambia este valor al color que desees */
}
</style>