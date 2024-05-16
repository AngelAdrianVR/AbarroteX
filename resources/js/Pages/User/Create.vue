<template>
    <AppLayout title="Nuevo usuario">
        <div class="px-3 md:px-10 py-7">
            <Back />

                <form @submit.prevent="store"
                    class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                    <h1 class="font-bold ml-2 col-span-full">Crear nuevo usuario</h1>

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
                        <PrimaryButton :disabled="form.processing">Crear usuario</PrimaryButton>
                    </div>
                </form>

                <!-- Mensaje de cantidad alcanzada -->
                <!-- <section class="mt-10" v-else>
                    <h1 class="font-bold text-5xl text-center mb-5">Lo sentimos :(</h1>
                    <p class="text-xl text-center">Has alcanzado el límite de cajas (4) de tu plan contratado.</p>
                    <p class="text-xl text-center">Para incrementar el límite de cajas es necesario cambiar tu plan. Para cambiarlo da clic en el siguiente botón.</p>
                    <div class="flex justify-center mt-5">
                        <PrimaryButton @click="$inertia.get(route('profile.show'))" :disabled="form.processing">Cambiar de plan</PrimaryButton>
                    </div>
                </section> -->
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
        name: null,
        email: null,
        rol: null,
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
props:{},
methods:{
    store() {
        this.form.post(route("users.store"), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha creado un nuevo usuario. Su contraseña es: ezyventas",
                    type: "success",
                });
            },
        });
    },
}
}
</script>
