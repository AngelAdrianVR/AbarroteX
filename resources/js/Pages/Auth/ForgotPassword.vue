<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>

    <Head title="Olvidé contraseña" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <h1 class="text-center text-[#777777] text-xl">¿Olvidaste tu contraseña?</h1>

        <div class="my-4 mx-5 text-sm text-[#D9D9D9]">
            Ingresa el correo de tu cuenta donde te enviaremos las instrucciones para recuperar tu contraseña.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <div
                    class="flex items-center space-x-2 py-2 px-5 mt-3 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-[#D9D9D9]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 text-[#D9D9D9]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    <input v-model="form.email" id="email"
                        class="text-sm w-full placeholder:text-sm placeholder:text-[#D9D9D9] border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full bg-transparent"
                        type="email" placeholder="Correo electrónico" required autofocus autocomplete="username">
                </div>
                <InputError :message="form.errors.email" />
            </div>
            <div class="flex items-center justify-center mt-6 px-9 disabled:cursor-not-allowed disabled:opacity-25">
                <PrimaryButton class="w-full" :disabled="form.processing">
                    Enviar
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
