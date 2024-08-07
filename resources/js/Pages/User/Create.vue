<template>
    <AppLayout title="Nuevo usuario">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form v-if="total_users < 2 || $page.props.auth.user.store_id == 10" @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Crear nuevo usuario</h1>

                <div class="mt-3 col-span-2">
                    <InputLabel value="Nombre de usuario*" class="ml-3 mb-1" />
                    <el-input v-model="form.name" placeholder="Escribe el nombre del usuario" :maxlength="100"
                        clearable />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="mt-3 col-span-2">
                    <InputLabel value="Correo electrónico*" class="ml-3 mb-1" />
                    <el-input v-model="form.email" placeholder="Ingresa el corre electrónico del usuario"
                        :maxlength="100" clearable />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="mt-5 col-span-2">
                    <InputLabel value="Rol" class="ml-3 mb-1" />
                    <el-radio-group v-model="form.rol" class="ml-4">
                        <el-radio value="Cajero" size="small">Cajero</el-radio>
                        <el-radio value="Almacenista" size="small">Almacenista</el-radio>
                        <!-- <el-radio value="Supervisor" size="small">Supervisor</el-radio> -->
                    </el-radio-group>
                    <InputError :message="form.errors.rol" />
                </div>

                <div class="col-span-2 text-right mt-5">
                    <PrimaryButton :disabled="form.processing">Crear usuario</PrimaryButton>
                </div>
            </form>

            <!-- Mensaje de limite alcanzado -->
            <section class="mt-10" v-else>
                <h1 class="font-bold text-5xl text-center mb-5">¡Cima alcanzada!</h1>
                <p class="text-xl text-center">Has llegado al límite de usuarios (2) de tu plan contratado.</p>
                <p class="text-xl text-center">
                    Sigue creciendo tu negocio y descubre nuestros planes haciendo clic en el siguiente botón
                </p>
                <div class="flex justify-center mt-5">
                    <PrimaryButton @click="$inertia.get(route('profile.show'))" :disabled="form.processing">
                        Explorar planes
                    </PrimaryButton>
                </div>
            </section>
        </div>

        <!-- Confirmación de contraseña de usuario -->
        <ConfirmationModal :show="showPasswordConfirm" @close="showPasswordConfirm = false">
            <template #title>
                <h1 class="text-center">¡Se ha creado un nuevo usuario!</h1>
            </template>
            <template #content>
                <h2 class="text-center">IMPORTANTE</h2>
                <p class="text-center my-5">
                    El nuevo usuario puede iniciar sesión ingresando el correo registrado y la siguiente
                    contraseña que podrá cambiar una vez logueado.
                </p>
                <p class="text-center font-bold">Contraseña: ezyventas</p>
                <div class="border-t border-grayD9 w-40 mx-auto"></div>
                <div class="border-t border-grayD9 w-32 mx-auto mt-1"></div>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <PrimaryButton @click="$inertia.get(route('settings.index', {tab: 2}))">De acuerdo</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
        <!-- Confirmación de contraseña de usuario -->
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
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
            showPasswordConfirm: false,
        }
    },
    components: {
        AppLayout,
        ConfirmationModal,
        PrimaryButton,
        InputLabel,
        InputError,
        Back
    },
    props: {
        total_users: Number,
    },
    methods: {
        store() {
            this.form.post(route("users.store"), {
                onSuccess: () => {
                    this.$notify({
                        title: "Correcto",
                        message: "Se ha creado un nuevo usuario. Su contraseña es: ezyventas",
                        type: "success",
                    });
                    this.showPasswordConfirm = true;
                },
            });
        },
    }
}
</script>
