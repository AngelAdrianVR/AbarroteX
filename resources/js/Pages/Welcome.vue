<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import Simulator from '@/Components/MyComponents/Landing/Simulator.vue';
import imageCarousel1 from '@/../../public/images/landing-02.png';
import imageCarousel2 from '@/../../public/images/landing-02-2.png';
import imageCarousel3 from '@/../../public/images/landing-02-3.png';
import imageCarousel4 from '@/../../public/images/landing-02-4.png';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const windowWidth = ref(window.innerWidth);
const carouselHeight = ref('600px');

const updateCarouselHeight = () => {
    const width = window.innerWidth;
    if (width < 430) {
        carouselHeight.value = '400px';
    } else if (width >= 430 && width < 1024) {
        carouselHeight.value = '600px';
    } else if (width >= 1024 && width < 1350) {
        carouselHeight.value = '530px';
    } else {
        carouselHeight.value = '600px';
    }
};

// Definir la URL de WhatsApp como una propiedad computada
const whatsappLink = computed(() => {
  const text = encodeURIComponent('¡Hola!, quisiera más información sobre el punto de venta');
  return `https://wa.me/523312155731?text=${text}`;
});

onMounted(() => {
    updateCarouselHeight();
    window.addEventListener('resize', updateCarouselHeight);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', updateCarouselHeight);
});

const imageSets = ref([
    [
        imageCarousel1
    ],
    [
        imageCarousel2
    ],
    [
        imageCarousel3
    ],
    [
        imageCarousel4
    ],
]);
</script>

<style>
/* Estilos personalizados para los botones de navegación del carrusel */
.el-carousel__arrow {
    background-color: rgba(99, 98, 98, 0.562);
    /* Fondo semitransparente */
    border-radius: 50%;
    /* Bordes redondeados */
    color: white;
    /* Color del icono */
}

.el-carousel__arrow--left {
    left: 10px;
    /* Ajusta la posición de la flecha izquierda */
}

.el-carousel__arrow--right {
    right: 10px;
    /* Ajusta la posición de la flecha derecha */
}

/* Puedes agregar más estilos personalizados aquí */
.el-carousel__arrow:hover {
    background-color: rgba(131, 130, 130, 0.562);
    /* Cambia el fondo al hacer hover */
    /* transform: scale(1.1); Efecto de escalado al hacer hover */
}

/* Cambia el color del indicador activo */
.el-carousel__indicator--active {
    color: #F68C0F;
    /* Color del indicador activo (puedes cambiarlo al que prefieras) */
}
</style>

<template>

    <Head title="Ezy Ventas" />

    <nav class="bg-black1">
        <div class="max-w-8xl mx-auto px-4 md:px-7 py-3">
            <div class="flex justify-between items-center h-12 bg-white rounded-full px-4">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <ApplicationMark class="block h-10 md:h-12 w-auto" />
                    </div>
                </div>

                <div class="flex sm:items-center space-x-2 sm:ms-6">
                    <Link :href="$page.props.auth.user ? route('dashboard') : route('login')">
                    <PrimaryButton class="!py-[5px] md:!py-2">Ingresar</PrimaryButton>
                    </Link>
                    <!-- <Link :href="route('register')">
                        <PrimaryButton class="!py-[5px] md:!py-2 !bg-gray37">Registrate</PrimaryButton>
                    </Link> -->
                </div>
            </div>
        </div>
    </nav>

    <main class="bg-black1 selection:bg-primary selection:text-white pb-24">
        <section class="lg:flex justify-center">
            <div class="pt-8 lg:mt-10 px-3 mx-2 xl:w-4/5">
                <!-- Carousel -->
                <div class="block text-center">
                </div>
                <figure class="flex flex-col lg:flex-row lg:justify-between xl:space-x-20 lg:w-full">
                    <img class="lg:w-1/2 object-contain" src="@/../../public/images/landing-01.png" alt="landing_page">
                    <div class="lg:w-1/2 mt-5 lg:mt-0 p-4">
                        <el-carousel trigger="click" :height="carouselHeight">
                            <el-carousel-item v-for="(imageSet, index) in imageSets" :key="index">
                                <img v-for="(image, imgIndex) in imageSet" :key="imgIndex" :src="image" alt="">
                            </el-carousel-item>
                        </el-carousel>
                        <div class="flex justify-end space-x-3 mt-4">
                            <Link :href="$page.props.auth.user ? route('register') : route('login')">
                            <PrimaryButton class="!text-sm">Probar ahora</PrimaryButton>
                            </Link>
                            <a :href="whatsappLink" target="_blank">
                                <ThirthButton class="!border-white !text-white !text-sm">Contáctanos</ThirthButton>
                            </a>
                        </div>
                    </div>
                </figure>
            </div>
        </section>

        <!-- funcionalidades -->
        <section class="my-12">
            <h1 class="text-3xl text-center font-bold text-white">FUNCIONALIDAD DE LOS MÓDULOS</h1>

            <article class="my-7">
                <!-- CONOCE TUS GANANCIAS AL INSTANTE -->
                <div class="lg:flex justify-between items-center space-x-10 mt-12 lg:mt-0">
                    <div class="lg:w-1/2">
                        <h2 class="text-2xl text-center text-white">CONOCE TUS GANANCIAS AL INSTANTE</h2>

                        <!-- texto -->
                        <section>
                            <div class="mt-7 flex space-x-5 pl-5">
                                <svg class="mt-3" width="25" height="16" viewBox="0 0 20 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_12910_382)">
                                        <path
                                            d="M0.067827 11C3.41655 7.91095 6.88516 6.34423 10.7075 7.75342C16.216 9.2872 20.4009 5.49928 19.9686 0C17.7469 1.48223 14.0277 2.00781 9.89685 1.18005C2.84959 -0.236297 -0.531564 5.29163 0.067827 11Z"
                                            fill="#858488" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_12910_382">
                                            <rect width="25" height="16" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="text-white text-lg w-[450px]">Representación visual de ingresos, gastos y
                                    ganancias lo que facilita la situación financiera del negocio.</p>
                            </div>

                            <div class="mt-4 flex space-x-5 pl-5">
                                <svg class="mt-3" width="25" height="16" viewBox="0 0 20 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_12910_382)">
                                        <path
                                            d="M0.067827 11C3.41655 7.91095 6.88516 6.34423 10.7075 7.75342C16.216 9.2872 20.4009 5.49928 19.9686 0C17.7469 1.48223 14.0277 2.00781 9.89685 1.18005C2.84959 -0.236297 -0.531564 5.29163 0.067827 11Z"
                                            fill="#858488" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_12910_382">
                                            <rect width="25" height="16" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="text-white text-lg w-[450px]">Comparación en diferentes períodos.</p>
                            </div>

                            <div class="mt-4 flex space-x-5 pl-5">
                                <svg class="mt-3" width="25" height="16" viewBox="0 0 20 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_12910_382)">
                                        <path
                                            d="M0.067827 11C3.41655 7.91095 6.88516 6.34423 10.7075 7.75342C16.216 9.2872 20.4009 5.49928 19.9686 0C17.7469 1.48223 14.0277 2.00781 9.89685 1.18005C2.84959 -0.236297 -0.531564 5.29163 0.067827 11Z"
                                            fill="#858488" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_12910_382">
                                            <rect width="25" height="16" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="text-white text-lg w-[450px]">Tendencias de ventas y gastos, ayudando a prever
                                    necesidades futuras.</p>
                            </div>
                        </section>
                    </div>

                    <!-- imagen -->
                    <figure class="lg:w-1/2">
                        <img class="w-full" src="@/../../public/images/function_1.png" alt="ganancias">
                    </figure>
                </div>
            </article>

            <!-- GESTIONA TU INVENTARIO -->
            <article class="my-7">
                <div class="lg:flex justify-between items-center space-x-10 mt-12 lg:mt-0">
                    <!-- imagen -->
                    <figure class="lg:w-1/2 pl-5">
                        <img class="w-full" src="@/../../public/images/function_2.png" alt="ganancias">
                    </figure>

                    <div class="lg:w-1/2">
                        <h2 class="text-2xl text-center text-white">GESTIONA TU INVENTARIO</h2>

                        <!-- texto -->
                        <section>
                            <div class="mt-7 flex space-x-5">
                                <svg class="mt-3" width="25" height="16" viewBox="0 0 20 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_12910_382)">
                                        <path
                                            d="M0.067827 11C3.41655 7.91095 6.88516 6.34423 10.7075 7.75342C16.216 9.2872 20.4009 5.49928 19.9686 0C17.7469 1.48223 14.0277 2.00781 9.89685 1.18005C2.84959 -0.236297 -0.531564 5.29163 0.067827 11Z"
                                            fill="#858488" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_12910_382">
                                            <rect width="25" height="16" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="text-white text-lg w-[450px]">Registra y gestiona tus productos.</p>
                            </div>

                            <div class="mt-4 flex space-x-5">
                                <svg class="mt-3" width="25" height="16" viewBox="0 0 20 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_12910_382)">
                                        <path
                                            d="M0.067827 11C3.41655 7.91095 6.88516 6.34423 10.7075 7.75342C16.216 9.2872 20.4009 5.49928 19.9686 0C17.7469 1.48223 14.0277 2.00781 9.89685 1.18005C2.84959 -0.236297 -0.531564 5.29163 0.067827 11Z"
                                            fill="#858488" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_12910_382">
                                            <rect width="25" height="16" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="text-white text-lg w-[450px]">Catálogo base para Abarrotes y Papelerías que
                                    incluye productos de diversas categorías y marcas reconocidas.</p>
                            </div>

                            <div class="mt-4 flex space-x-5">
                                <svg class="mt-3" width="25" height="16" viewBox="0 0 20 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_12910_382)">
                                        <path
                                            d="M0.067827 11C3.41655 7.91095 6.88516 6.34423 10.7075 7.75342C16.216 9.2872 20.4009 5.49928 19.9686 0C17.7469 1.48223 14.0277 2.00781 9.89685 1.18005C2.84959 -0.236297 -0.531564 5.29163 0.067827 11Z"
                                            fill="#858488" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_12910_382">
                                            <rect width="25" height="16" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="text-white text-lg w-[450px]">Alertas de bajo stock.</p>
                            </div>

                            <div class="mt-4 flex space-x-5">
                                <svg class="mt-3" width="25" height="16" viewBox="0 0 20 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_12910_382)">
                                        <path
                                            d="M0.067827 11C3.41655 7.91095 6.88516 6.34423 10.7075 7.75342C16.216 9.2872 20.4009 5.49928 19.9686 0C17.7469 1.48223 14.0277 2.00781 9.89685 1.18005C2.84959 -0.236297 -0.531564 5.29163 0.067827 11Z"
                                            fill="#858488" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_12910_382">
                                            <rect width="25" height="16" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="text-white text-lg w-[450px]">Actualización de inventario en tiempo real.</p>
                            </div>
                        </section>
                    </div>

                </div>

            </article>

            <!-- CORTES DE CAJA -->
            <article class="my-7">
                <div class="lg:flex justify-between items-center space-x-10 mt-12 lg:mt-0">
                    <div class="lg:w-1/2">
                        <h2 class="text-2xl text-center text-white">CORTES DE CAJA</h2>

                        <!-- texto -->
                        <section>
                            <div class="mt-7 flex space-x-5 pl-5">
                                <svg class="mt-3" width="25" height="16" viewBox="0 0 20 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_12910_382)">
                                        <path
                                            d="M0.067827 11C3.41655 7.91095 6.88516 6.34423 10.7075 7.75342C16.216 9.2872 20.4009 5.49928 19.9686 0C17.7469 1.48223 14.0277 2.00781 9.89685 1.18005C2.84959 -0.236297 -0.531564 5.29163 0.067827 11Z"
                                            fill="#858488" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_12910_382">
                                            <rect width="25" height="16" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="text-white text-lg w-[450px]">Realiza cortes de manera rápida y precisa.</p>
                            </div>

                            <div class="mt-4 flex space-x-5 pl-5">
                                <svg class="mt-3" width="25" height="16" viewBox="0 0 20 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_12910_382)">
                                        <path
                                            d="M0.067827 11C3.41655 7.91095 6.88516 6.34423 10.7075 7.75342C16.216 9.2872 20.4009 5.49928 19.9686 0C17.7469 1.48223 14.0277 2.00781 9.89685 1.18005C2.84959 -0.236297 -0.531564 5.29163 0.067827 11Z"
                                            fill="#858488" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_12910_382">
                                            <rect width="25" height="16" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="text-white text-lg w-[450px]">Seguimiento de transacciones en tiempo real.</p>
                            </div>

                            <div class="mt-4 flex space-x-5 pl-5">
                                <svg class="mt-3" width="25" height="16" viewBox="0 0 20 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_12910_382)">
                                        <path
                                            d="M0.067827 11C3.41655 7.91095 6.88516 6.34423 10.7075 7.75342C16.216 9.2872 20.4009 5.49928 19.9686 0C17.7469 1.48223 14.0277 2.00781 9.89685 1.18005C2.84959 -0.236297 -0.531564 5.29163 0.067827 11Z"
                                            fill="#858488" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_12910_382">
                                            <rect width="25" height="16" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="text-white text-lg w-[450px]">Realiza ingresos y retiros de efectivo desde
                                    caja, permitiendo mayor transparencia y control.</p>
                            </div>
                        </section>
                    </div>

                    <!-- imagen -->
                    <figure class="lg:w-1/2 pl-5">
                        <img class="w-full" src="@/../../public/images/function_3.png" alt="ganancias">
                    </figure>

                </div>
            </article>

            <!-- ¿Por qué elegir Ezy Ventas? -->
            <article class="my-7">
                <div class="lg:flex justify-between items-center space-x-10 mt-12 lg:mt-0">
                    <!-- imagen -->
                    <figure class="lg:w-1/2 pl-12">
                        <img class="w-2/3 mx-auto" src="@/../../public/images/function_4.png" alt="ganancias">
                    </figure>

                    <div class="lg:w-1/2 mt-5">
                        <h2 class="text-2xl text-center text-white">¿POR QUÉ ELEGIR EZY VENTAS?</h2>

                        <!-- texto -->
                        <section>
                            <div class="mt-7 flex space-x-5 pl-5">
                                <svg width="39" height="31" viewBox="0 0 39 31" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.19472 30.7548C6.41169 22.8071 0.361582 16.4854 0.0193154 16.11C-0.322951 15.7347 3.91886 15.0218 9.92875 21.2357C21.3209 8.36968 32.3648 -0.0344495 38.556 0.000789626C38.7945 -0.0234503 39.1605 0.51685 38.923 0.733029C25.2154 7.24386 17.6274 18.8122 11.0298 30.7548C10.99 31.1167 9.17897 31.0447 9.19472 30.7548Z"
                                        fill="#99FB77" />
                                </svg>
                                <p class="text-white text-lg w-[450px]">Compatible con cualquier dispositivo.</p>
                            </div>

                            <div class="mt-4 flex space-x-5 pl-5">
                                <svg width="39" height="31" viewBox="0 0 39 31" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.19472 30.7548C6.41169 22.8071 0.361582 16.4854 0.0193154 16.11C-0.322951 15.7347 3.91886 15.0218 9.92875 21.2357C21.3209 8.36968 32.3648 -0.0344495 38.556 0.000789626C38.7945 -0.0234503 39.1605 0.51685 38.923 0.733029C25.2154 7.24386 17.6274 18.8122 11.0298 30.7548C10.99 31.1167 9.17897 31.0447 9.19472 30.7548Z"
                                        fill="#99FB77" />
                                </svg>
                                <p class="text-white text-lg w-[450px]">Ingresa desde cualquier lugar.</p>
                            </div>

                            <div class="mt-4 flex space-x-5 pl-5">
                                <svg width="39" height="31" viewBox="0 0 39 31" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.19472 30.7548C6.41169 22.8071 0.361582 16.4854 0.0193154 16.11C-0.322951 15.7347 3.91886 15.0218 9.92875 21.2357C21.3209 8.36968 32.3648 -0.0344495 38.556 0.000789626C38.7945 -0.0234503 39.1605 0.51685 38.923 0.733029C25.2154 7.24386 17.6274 18.8122 11.0298 30.7548C10.99 31.1167 9.17897 31.0447 9.19472 30.7548Z"
                                        fill="#99FB77" />
                                </svg>
                                <p class="text-white text-lg w-[450px]">No requiere de instalaciones.</p>
                            </div>

                            <div class="mt-4 flex space-x-5 pl-5">
                                <svg width="39" height="31" viewBox="0 0 39 31" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.19472 30.7548C6.41169 22.8071 0.361582 16.4854 0.0193154 16.11C-0.322951 15.7347 3.91886 15.0218 9.92875 21.2357C21.3209 8.36968 32.3648 -0.0344495 38.556 0.000789626C38.7945 -0.0234503 39.1605 0.51685 38.923 0.733029C25.2154 7.24386 17.6274 18.8122 11.0298 30.7548C10.99 31.1167 9.17897 31.0447 9.19472 30.7548Z"
                                        fill="#99FB77" />
                                </svg>
                                <p class="text-white text-lg w-[450px]">Gestión de inventario y seguimiento de ventas en
                                    tiempo real.</p>
                            </div>

                            <div class="mt-4 flex space-x-5 pl-5">
                                <svg width="39" height="31" viewBox="0 0 39 31" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.19472 30.7548C6.41169 22.8071 0.361582 16.4854 0.0193154 16.11C-0.322951 15.7347 3.91886 15.0218 9.92875 21.2357C21.3209 8.36968 32.3648 -0.0344495 38.556 0.000789626C38.7945 -0.0234503 39.1605 0.51685 38.923 0.733029C25.2154 7.24386 17.6274 18.8122 11.0298 30.7548C10.99 31.1167 9.17897 31.0447 9.19472 30.7548Z"
                                        fill="#99FB77" />
                                </svg>
                                <p class="text-white text-lg w-[450px]">Productos pre-cargados para tiendas de abarrotes
                                    y papelerías.</p>
                            </div>

                            <div class="flex justify-start space-x-3 mt-10">
                                <Link :href="$page.props.auth.user ? route('register') : route('login')">
                                <PrimaryButton class="!text-sm">Probar ahora</PrimaryButton>
                                </Link>
                                <a :href="whatsappLink" target="_blank">
                                    <ThirthButton class="!border-white !text-white !text-sm">Contáctanos</ThirthButton>
                                </a>
                            </div>
                        </section>
                    </div>
                </div>
            </article>
        </section>

        <!-- Simulador -->
        <section class="mt-56 text-center">
            <h2 class="text-[#999999] text-lg mb-9">SIMULADOR</h2>

            <p class="text-[#999999]">Personaliza tu suscripción con los módulos que necesitas.</p>
            <p class="text-white underline">Paga únicamente por lo que utilizas</p>
            <p class="text-primary uppercase">Con días de regalo para que lo pruebes</p>
            <!-- <p class="text-[#999999] mt-5">Elige el giro y te recomendamos que módulos son importantes para tu negocio</p> -->

            <div class="mt-14 text-left xl:w-[75%] xl:mx-auto mx-5">
                <Simulator id="simulator" />
            </div>
        </section>
    </main>

    <!-- footer -->
    <footer class="bg-black1 p-5 -mt-12">
        <div class="border-b border-[#373737] w-full"></div>
        <div class="flex items-center md:justify-between text-white text-sm my-3">
            <p>Copyright c 2024 | Todos los derechos reservador por Ezy Ventas</p>
            <div class="flex space-x-3">
                <a class="underline" target="_blank" :href="route('terms.show')">Términos y condiciones</a>
                <a class="underline" target="_blank" :href="route('policy.show')">Política de privacidad</a>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <figure class="mt-4">
                <img class="w-20 lg:w-[40%]" src="@/../../public/images/white_logo.png" alt="">
            </figure>
            <figure class="mt-4 cursor-pointer">
                <a class="flex justify-end items-center" href="https://app.dtw.com.mx/" target="_blank">
                    <p class="text-white text-xl">BY</p>
                    <img class="w-20 lg:w-[10%]" src="@/../../public/images/DTW_logo_blanco.png" alt="">
                </a>
            </figure>
        </div>
    </footer>
</template>
