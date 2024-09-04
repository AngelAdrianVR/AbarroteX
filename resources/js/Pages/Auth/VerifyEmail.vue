<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>

    <Head title="Verificación de correo" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-4 text-sm text-grayD9">
            Antes de continuar, debes de verificar tu correo dando click en el link que te enviamos.
            Si no has recibido ningún correo, con gusto te enviaremos otro. 
        </div>

        <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-600">
            Te hemos enviado un nuevo link de verificación al correo que proporcionaste en las configuraciones de tu perfil.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex flex-col space-y-2 items-center">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Volver a enviar correo de verificación
                </PrimaryButton>

                <div>
                    <Link :href="route('profile.show')"
                        class="underline text-sm text-grayD9 hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Editar perfil</Link>

                    <Link :href="route('logout')" method="post" as="button"
                        class="underline text-sm text-grayD9 hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary ms-4">
                    Cerrar sesión
                    </Link>
                </div>
            </div>
        </form>
    </AuthenticationCard>
</template>
