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

const form = useForm({
    store_name: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="flex items-center justify-around mt-2 mb-8 mx-11 border-b text-sm">
            <button @click="$inertia.visit(route('login'))" type="button" class="text-[#777777] px-2">Iniciar
                sesión</button>
            <span class="text-primary px-2 border-b border-primary">Registrarse</span>
        </div>

        <form @submit.prevent="submit">
            <div>
                <div
                    class="flex items-center space-x-2 py-2 px-5 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-[#777777]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5 text-[#777777]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>
                    <input v-model="form.store_name" type="text"
                        class="text-sm w-full placeholder:text-sm placeholder:text-[#777777] border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full"
                        placeholder="Nombre de tu tienda" required autofocus>
                </div>
                <InputError :message="form.errors.store_name" />
            </div>
            <div>
                <div
                    class="flex items-center space-x-2 py-2 px-5 mt-3 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-[#777777]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5 text-[#777777]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <input id="name" v-model="form.name" type="text"
                        class="text-sm w-full placeholder:text-sm placeholder:text-[#777777] border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full"
                        placeholder="Tu nombre" required autofocus autocomplete="name">
                </div>
                <InputError :message="form.errors.name" />
            </div>
            <div>
                <div
                    class="flex items-center space-x-2 py-2 px-5 mt-3 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-[#777777]">
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
                        :type="showPassword ? 'text' : 'password'" placeholder="Contraseña" required
                        autocomplete="new-password">
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
            <div>
                <div
                    class="mt-3 flex items-center space-x-2 py-2 px-5 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-[#777777]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 text-[#777777]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <input v-model="form.password_confirmation" id="password_confirmation"
                        class="text-sm w-full placeholder:text-sm placeholder:text-[#777777] border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full"
                        :type="showPassword ? 'text' : 'password'" placeholder="Confirmar contraseña " required
                        autocomplete="new-password">
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
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="block mt-4 ml-6">
                <el-checkbox v-model="form.terms" name="terms" required size="small">
                    <div class="ms-2 text-[#777777]">
                        He leído y acepto los <a target="_blank" :href="route('terms.show')"
                            class="underline focus:outline-none text-primary">Términos y Condiciones</a> <br>
                        y el <a target="_blank" :href="route('policy.show')"
                            class="underline focus:outline-none text-primary">Aviso de privacidad</a> de Ezy Ventas
                    </div>
                </el-checkbox>
                <InputError :message="form.errors.terms" />
            </div>

            <div class="flex items-center justify-center mt-6 px-9 disabled:cursor-not-allowed disabled:opacity-25">
                <PrimaryButton class="w-full" :disabled="form.processing || !form.terms">
                    Registrarme
                </PrimaryButton>
            </div>

            <div class="mt-6 mx-9 flex items-center space-x-2 text-sm">
                <p class="text-[#777777]">¿Ya tienes cuenta?</p>
                <Link :href="route('login')" class="underline text-center text-primary focus:outline-none">
                Inicia sesión
                </Link>
            </div>

            <!-- <el-divider class="mt-4">ó</el-divider> -->

            <!-- <div class="flex items-center justify-center mt-4">
                <button
                    class="w-full border border-grayD9 rounded-full flex items-center justify-center space-x-3 py-1 text-sm text-[#777777] disabled:opacity-25 disabled:cursor-not-allowed"
                    :disabled="form.processing">
                    <img src="@/../../public/images/google_logo.png" width="25" alt="Logo de google">
                    <span>Continuar con Google</span>
                </button>
            </div> -->
        </form>
    </AuthenticationCard>
</template>
