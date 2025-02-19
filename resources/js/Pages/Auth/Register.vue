<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    store_name: '',
    name: '',
    type: '',
    email: '',
    address: '',
    contact_phone: '',
    password: '',
    password_confirmation: '',
    terms: false,
    activated_modules: [],  // modulos extra seleccionados desde la landing
});

const rawPhone = ref("");
const formattedPhone = ref("");

const handleInput = (event) => {
    // Remover cualquier carácter no numérico
    rawPhone.value = event.target.value.replace(/\D/g, "");
    // Limitar a 10 dígitos
    rawPhone.value = rawPhone.value.slice(0, 10);
    form.contact_phone = rawPhone.value;

    // Formatear en estilo telefónico (XXX-XXX-XXXX)
    formattedPhone.value = rawPhone.value.replace(
    /^(\d{3})(\d{3})(\d{0,4})$/,
    (_, p1, p2, p3) => `${p1}-${p2}${p3 ? `-${p3}` : ""}`
    );
};

// Ejecutar al montar el componente
onMounted(() => {
    // Verificar si existe "EzyExtraModules" en el localStorage
    const storedModules = localStorage.getItem('EzyExtraModules');
    
    // Asignar el valor al formulario o inicializarlo como un arreglo vacío
    form.activated_modules = storedModules ? JSON.parse(storedModules) : [];
});

const showPassword = ref(false);
const types = ['Abarrotes / Supermercado', 'Artículos para mascotas', 'Bisutería / Joyería', 'Boutique / Tienda de Ropa / Zapatería', 'Carnicería',
    'Cremería', 'Cerrajería', 'Dulcería', 'Farmacia', 'Frutería / Verdulería', 'Ferretería / Tlapalería', 'Juguetería', 'Librería', 'Licorería / Cervecería',
    'Artículos de limpieza', 'Materiales Para Construcción', 'Taquería', 'Agroquímicos / Orgánicos', 'Panadería', 'Papelería',
    'Tienda de plásticos', 'Refaccionaria', 'Tecnología', 'Otro'
];
// const types = ['Abarrotes', 'Accesorios', 'Bisutería/Joyería', 'Boutique / Tienda de Ropa', 'Cafetería', 'Carnicería',
//     'Cremería', 'Dulcería', 'Farmacia', 'Frutería / Verdulería', 'Ferreterías / Tlapalerías', 'Juguetería', 'Librería', 'Licorería / Cervecería',
//     'Limpieza', 'Materiales Para Construcción', 'Supermercado', ' Mueblería', 'Taquería', 'Agroquímicos / Orgánicos', 'Panadería', 'Papelería',
//     'Perfumería', 'Tienda de Pinturas', 'Tienda de plásticos', 'Refaccionaria', 'Kiosco', 'Tecnología', 'Veterinaria', 'Zapatería',
//     'Óptica', 'Otro'
// ]

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head title="Registro" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="flex items-center justify-around mt-2 mb-8 mx-11 border-b border-gray-600 text-sm">
            <button @click="$inertia.visit(route('login'))" type="button" class="text-grayD9 px-2">Iniciar
                sesión</button>
            <span class="text-[#E9A527] px-2 border-b-2 border-[#E9A527]">Registrarse</span>
        </div>

        <form @submit.prevent="submit">
            <h1 class="text-grayD9 font-bold ml-9 text-sm mb-2">Información de la tienda</h1>
            <div>
                <!-- <div
                    class="flex items-center space-x-2 py-2 mt-3 px-5 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-grayD9">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 !text-grayD9">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                    </svg>
                    <input v-model="form.type" type="text"
                        class="text-sm w-full placeholder:text-sm placeholder:text-grayD9 border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full bg-transparent"
                        placeholder="Giro. Ejemplo. Carnicería" required autofocus>
                </div> -->
                <div class="mt-3">
                    <el-select class="w-1/2 !rounded-full h-10 !bg-transparent" v-model="form.type" filterable clearable
                        placeholder="Selecciona el giro de tu tienda" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in types" :key="item" :label="item" :value="item" />
                    </el-select>
                </div>
                <InputError :message="form.errors.type" />
            </div>
            <div class="mt-3">
                <div
                    class="flex items-center space-x-2 py-2 px-5 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-grayD9">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5 text-grayD9">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>
                    <input v-model="form.store_name" type="text"
                        class="text-sm w-full placeholder:text-sm text-white placeholder:text-gray-500 border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full bg-transparent"
                        placeholder="Nombre de tu tienda" required autofocus>
                </div>
                <InputError :message="form.errors.store_name" />
            </div>
            <div>
                <div
                    class="flex items-center space-x-2 py-2 mt-3 px-5 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-grayD9">
                    <svg class="size-5" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M11.1264 6.06317C11.1264 9.69714 7.37118 12.2875 6.32874 12.9367C6.1666 13.0394 5.9598 13.0394 5.79766 12.9367C4.75549 12.2875 1 9.69714 1 6.06317C1 3.26689 3.26692 1 6.0632 1C8.85948 1 11.1264 3.26689 11.1264 6.06317Z"
                            stroke="#ccc" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M8.3131 6.06225C8.3131 7.79455 6.43784 8.87722 4.93765 8.01108C4.24139 7.60909 3.8125 6.8662 3.8125 6.06225C3.8125 4.32997 5.68776 3.24729 7.18795 4.11343C7.88421 4.5154 8.3131 5.25829 8.3131 6.06225Z"
                            stroke="#ccc" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                    <input v-model="form.address" type="text"
                        class="text-sm w-full placeholder:text-sm text-white placeholder:text-gray-500 border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full bg-transparent"
                        placeholder="Dirección" autofocus>
                </div>
                <InputError :message="form.errors.address" />
            </div>
            <div>
                <div
                    class="flex items-center space-x-2 py-2 mt-3 px-5 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-grayD9">
                    <svg class="size-5" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.48 3.73333C12.48 2.22371 11.2563 1 9.74667 1H9.59114C9.42727 1.00003 9.26717 1.04915 9.13148 1.14104C8.9958 1.23292 8.89076 1.36335 8.82991 1.51551L8.15587 3.20033C8.07638 3.39899 8.05694 3.6166 8.09993 3.8262C8.14293 4.0358 8.24649 4.22818 8.39777 4.37949L9.23881 5.22054C9.37357 5.35529 9.43015 5.55127 9.37493 5.73386C9.11283 6.59258 8.64371 7.37372 8.00885 8.00858C7.37399 8.64344 6.59285 9.11255 5.73413 9.37466C5.55155 9.43015 5.35557 9.37329 5.22081 9.23854L4.37977 8.39749C4.22845 8.24621 4.03607 8.14266 3.82647 8.09966C3.61687 8.05666 3.39926 8.07611 3.20061 8.15559L1.51551 8.82963C1.36335 8.89048 1.23292 8.99553 1.14104 9.13121C1.04915 9.26689 1.00003 9.427 1 9.59087V9.74667C1 11.2563 2.22371 12.48 3.73333 12.48H3.87C8.54673 12.48 12.3526 8.75119 12.477 4.10452L12.4784 4.10097L12.4795 4.09936L12.48 4.09741V3.73333Z"
                            stroke="#ccc" stroke-width="0.88" stroke-linejoin="round" />
                    </svg>
                    <input
                        v-model="formattedPhone"
                        @input="handleInput"
                        type="text"
                        class="text-sm w-full placeholder:text-sm text-white placeholder:text-gray-500 border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full bg-transparent"
                        placeholder="Teléfono (10 dígitos)"
                        required
                        autofocus
                    />
                </div>
                <InputError :message="form.errors.contact_phone" />
            </div>

            <h1 class="text-grayD9 font-bold ml-9 text-sm mt-3">Contacto</h1>

            <div>
                <div
                    class="flex items-center space-x-2 py-2 px-5 mt-3 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-grayD9">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5 text-grayD9">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <input id="name" v-model="form.name" type="text"
                        class="text-sm w-full placeholder:text-sm text-white placeholder:text-gray-500 border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full bg-transparent"
                        placeholder="Tu nombre" required autofocus autocomplete="name">
                </div>
                <InputError :message="form.errors.name" />
            </div>
            <div class="mt-3">
                <p class="text-grayD9 text-[10px] text-center">* Debe ser un correo válido porque lo necesitas para activar tu
                    cuenta *</p>
                <div
                    class="flex items-center space-x-2 py-2 px-5 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-grayD9">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 text-grayD9">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    <input v-model="form.email" id="email"
                        class="text-sm w-full placeholder:text-sm text-white placeholder:text-gray-500 border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full bg-transparent"
                        type="email" placeholder="Correo electrónico" required autofocus autocomplete="username">
                </div>
                <InputError :message="form.errors.email" />
            </div>
            <div>
                <div
                    class="mt-3 flex items-center space-x-2 py-2 px-5 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-grayD9">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 text-grayD9">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <input v-model="form.password" id="password"
                        class="text-sm w-full placeholder:text-sm text-white placeholder:text-gray-500 border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full bg-transparent"
                        :type="showPassword ? 'text' : 'password'" placeholder="Contraseña" required
                        autocomplete="new-password">
                    <button @click="showPassword = !showPassword" type="button" class="z-10">
                        <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-4 text-grayD9">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-4 text-grayD9">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <InputError :message="form.errors.password" />
            </div>
            <div>
                <div
                    class="mt-3 flex items-center space-x-2 py-2 px-5 w-full h-10 border border-grayD9 rounded-full placeholder:text-sm placeholder:text-grayD9">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 text-grayD9">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <input v-model="form.password_confirmation" id="password_confirmation"
                        class="text-sm w-full placeholder:text-sm text-white placeholder:text-gray-500 border-0 focus:ring-0 focus:border-grayD9 border-grayD9 border-l h-full bg-transparent"
                        :type="showPassword ? 'text' : 'password'" placeholder="Confirmar contraseña " required
                        autocomplete="new-password">
                    <button @click="showPassword = !showPassword" type="button" class="z-10">
                        <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-4 text-grayD9">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-4 text-grayD9">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="block mt-4 ml-6">
                <el-checkbox v-model="form.terms" name="terms" required size="small">
                    <div class="ms-2 text-grayD9">
                        He leído y acepto los <a target="_blank" :href="route('terms.show')"
                            class="underline focus:outline-none text-[#E9A527]">Términos y Condiciones</a> <br>
                        y el <a target="_blank" :href="route('policy.show')"
                            class="underline focus:outline-none text-[#E9A527]">Aviso de privacidad</a> de Ezy Ventas
                    </div>
                </el-checkbox>
                <InputError :message="form.errors.terms" />
            </div>

            <div class="flex items-center justify-center mt-6 px-9 disabled:cursor-not-allowed disabled:opacity-25">
                <PrimaryButton class="w-full" :disabled="form.processing || !form.terms">
                    <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    Registrarme
                </PrimaryButton>
            </div>

            <div class="mt-6 mx-9 flex items-center space-x-2 text-sm">
                <p class="text-grayD9">¿Ya tienes cuenta?</p>
                <Link :href="route('login')" class="underline text-center text-[#E9A527] focus:outline-none">
                Inicia sesión
                </Link>
            </div>

            <!-- <el-divider class="mt-4">ó</el-divider> -->

            <!-- <div class="flex items-center justify-center mt-4">
                <button
                    class="w-full border border-grayD9 rounded-full flex items-center justify-center space-x-3 py-1 text-sm text-grayD9 disabled:opacity-25 disabled:cursor-not-allowed"
                    :disabled="form.processing">
                    <img src="@/../../public/images/google_logo.png" width="25" alt="Logo de google">
                    <span>Continuar con Google</span>
                </button>
            </div> -->
        </form>
    </AuthenticationCard>
</template>
