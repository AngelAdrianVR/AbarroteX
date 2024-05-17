<template>
    <AppLayout title="Editar usuario">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="update"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Editar usuario</h1>

                <div class="mt-3 col-span-2">
                    <InputLabel value="Nombre de usuario*" class="ml-3 mb-1" />
                    <el-input v-model="form.name" placeholder="Escribe el nombre del usuario" :maxlength="100" clearable />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="mt-3 col-span-2">
                    <InputLabel value="Correo electrónico*" class="ml-3 mb-1" />
                    <el-input v-model="form.email" placeholder="Ingresa el corre electrónico del usuario" :maxlength="100" clearable />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="mt-5 col-span-2">
                    <InputLabel value="Rol" class="ml-3 mb-1" />
                    <el-radio-group v-model="form.rol" class="ml-4">
                        <el-radio value="Cajero" size="small">Cajero</el-radio>
                        <el-radio value="Almacen" size="small">Almacén</el-radio>
                    </el-radio-group>
                    <InputError :message="form.errors.rol" />
                </div>

                <div class="col-span-2 text-right mt-5">
                    <PrimaryButton :disabled="form.processing">Guardar cambios</PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
        name: this.user.name,
        email: this.user.email,
        rol: this.user.rol,
    });

    return {
        form,
    }
},
components:{
AppLayout,
PrimaryButton,
InputLabel,
InputError,
Back
},
props:{
    user: Object
},
methods:{
    update() {
        this.form.put(route("users.update", this.user.id), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha actualizado al usuario correctamente",
                    type: "success",
                });
            },
        });
    },
}
}
</script>
