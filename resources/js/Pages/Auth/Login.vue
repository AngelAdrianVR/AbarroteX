<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>

    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
            <h1 class="font-extrabold mt-3 text-center text-2xl text-primary">Ezy Ventas</h1>
        </template>

        <div class="flex items-center justify-around mt-5 mb-8 mx-11 border-b text-sm">
            <span class="text-primary px-2 border-b border-primary">Iniciar sesión</span>
            <button @click="$inertia.visit(route('register'))" type="button"
                class="text-[#777777] px-2">Registrarse</button>
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <div
                    class="flex items-center space-x-2 py-2 px-5 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-[#777777]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 text-[#777777]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    <input v-model="form.email" id="email"
                        class="text-sm w-full placeholder:text-sm placeholder:text-[#777777] border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full"
                        type="email" placeholder="Correo electrónico" required autofocus autocomplete="username">
                </div>
                <InputError :message="form.errors.email" />
            </div>
            <div>
                <div
                    class="mt-3 flex items-center space-x-2 py-2 px-5 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-[#777777]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 text-[#777777]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <input v-model="form.password" id="password"
                        class="text-sm w-full placeholder:text-sm placeholder:text-[#777777] border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full"
                        :type="showPassword ? 'text' : 'password'" placeholder="Contraseña" required>
                    <button @click="showPassword = !showPassword" type="button" class="z-10">
                        <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-4 text-[#777777]">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-4 text-[#777777]">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <InputError :message="form.errors.password" />
            </div>

            <div class="block mt-4 ml-6">
                <el-checkbox v-model="form.remember" name="remember" label="Mantener sesión abierta"
                    size="small" />
            </div>

            <Link v-if="canResetPassword" :href="route('password.request')"
                class="block mt-6 underline text-sm text-center text-[#777777] focus:outline-none">
            Olvide mi contraseña
            </Link>

            <div class="flex items-center justify-center mt-6 px-9 disabled:cursor-not-allowed disabled:opacity-25">
                <PrimaryButton class="w-full" :disabled="form.processing">
                    Ingresar
                </PrimaryButton>
            </div>

            <div class="mt-6 mx-9 flex items-center space-x-2 text-sm">
                <p class="text-[#777777]">¿No tienes cuenta?</p>
                <Link :href="route('register')" class="underline text-center text-primary focus:outline-none">
                Crear cuenta
                </Link>
            </div>

            <el-divider class="mt-4">ó</el-divider>

            <div class="flex items-center justify-center mt-4">
                <button
                    class="w-full border border-grayD9 rounded-full flex items-center justify-center space-x-3 py-1 text-sm text-[#777777] disabled:opacity-25 disabled:cursor-not-allowed"
                    :disabled="form.processing">
                    <img src="@/../../public/images/google_logo.png" width="25" alt="Logo de google">
                    <span>Continuar con Google</span>
                </button>
            </div>
        </form>
    </AuthenticationCard>
</template>