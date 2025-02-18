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
import AOS from 'aos';
import 'aos/dist/aos.css';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const windowWidth = ref(window.innerWidth);
const carouselHeight = ref('600px');
const quantity1 = ref(1);
const quantity2 = ref(1);

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
    AOS.init();
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

<style scoped>
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

/* uiverse estilos */
/* From Uiverse.io by CritCoder */
.buttonupgrade {
    width: fit-content;
    display: flex;
    padding: 5px 22px;
    cursor: pointer;
    border-radius: 30px;
    text-shadow: 2px 2px 3px rgba(221, 255, 0, 0.3);
    background: linear-gradient(15deg,
            #F68C0F,
            #e6810e,
            #bb6707,
            #905209,
            #F68C0F,
            #e6810e,
            #bb6707,
            #905209) no-repeat;
    background-size: 300%;
    color: white;
    border: none;
    background-position: left center;
    box-shadow: 0 20px 10px -14px rgba(246, 140, 15, 0.5);
    transition:
        background 0.3s ease,
        color 0.3s ease;
}

.buttonupgrade:hover {
    background-size: 320%;
    background-position: right center;
    color: white;
}


/* From Uiverse.io by rahulgarg99 */
.button1 {
    line-height: 1;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    background-color: #fff;
    color: #F68C0F;
    border-radius: 10rem;
    padding: 0.75rem 1.5rem;
    padding-left: 20px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: background-color 0.3s;
}

.button1__icon-wrapper {
    flex-shrink: 0;
    width: 25px;
    height: 25px;
    position: relative;
    color: #fff;
    background-color: #F68C0F;
    border-radius: 50%;
    display: grid;
    place-items: center;
    overflow: hidden;
}

.button1:hover {
    background-color: #F68C0F;
    color: white;
}

.button1:hover .button1__icon-wrapper {
    color: #F68C0F;
    background-color: #fff;
}

.button1__icon-svg--copy {
    position: absolute;
    transform: translate(-150%, 150%);
}

.button1:hover .button1__icon-svg:first-child {
    transition: transform 0.3s ease-in-out;
    transform: translate(150%, -150%);
}

.button1:hover .button1__icon-svg--copy {
    transition: transform 0.3s ease-in-out 0.1s;
    transform: translate(0);
}
</style>

<template>

    <Head title="Ezy Ventas" />
    <div class="bg-black2 relative overflow-hidden">
        <img class="object-contain select-none absolute right-0 top-0" :draggable="false"
            src="@/../../public/images/bg-01.png" alt="Destello neon de adorno en el fondo" data-aos="zoom-in-down"
            data-aos-duration="800">
        <nav class="bg-transparent">
            <div class="max-w-8xl mx-auto px-4 md:px-7 py-3" data-aos="zoom-in" data-aos-duration="800">
                <div class="flex justify-between items-center h-14 bg-[#404040]/30 rounded-full pl-6 pr-2">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <ApplicationMark class="block h-10 md:h-12 w-auto" />
                        </div>
                    </div>
                    <div class="flex sm:items-center space-x-12 sm:ms-6">
                        <button class="text-white focus:border-none focus:ring-0" type="button">Inicio</button>
                        <button class="text-white focus:border-none focus:ring-0" type="button">Funcionalidades</button>
                        <button class="text-white focus:border-none focus:ring-0" type="button">Precios</button>
                        <button class="text-white focus:border-none focus:ring-0" type="button">Productos</button>
                        <Link :href="$page.props.auth.user ? route('dashboard') : route('login')">
                        <button class="buttonupgrade">
                            Iniciar sesión
                        </button>
                        </Link>
                    </div>
                </div>
            </div>
        </nav>
        <main class="bg-black2 selection:bg-primary selection:text-white pb-24 overflow-hidden">
            <section class="flex relative">
                <figure class="w-[24%] mx-12 mt-10" data-aos="zoom-in-up" data-aos-duration="800">
                    <img class="object-contain select-none" :draggable="false"
                        src="@/../../public/images/landing-01.png" alt="Punto de venta. 30 dias gratis"
                        data-aos="zoom-in-down" data-aos-duration="800">
                </figure>
                <figure class="w-[27%] ml-20 mt-20 z-10" data-aos="zoom-in-up" data-aos-duration="800">
                    <img class="object-contain select-none" :draggable="false"
                        src="@/../../public/images/landing-02.png" alt="Tablet que muestra un carrito de compras"
                        data-aos="zoom-in-down" data-aos-duration="800">
                </figure>
                <img class="object-contain select-none absolute -right-[3%] top-28" :draggable="false"
                    src="@/../../public/images/landing-03.png" alt="Tablet que muestra un carrito de compras"
                    data-aos="zoom-in-down" data-aos-duration="800">
                <button @click="$inertia.visit('register')" class="button1 absolute bottom-4 right-[22%]"
                    style="--clr: #7808d0">
                    <span class="button1__icon-wrapper">
                        <svg viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg"
                            class="button1__icon-svg" width="10">
                            <path
                                d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                                fill="currentColor"></path>
                        </svg>

                        <svg viewBox="0 0 14 15" fill="none" width="10" xmlns="http://www.w3.org/2000/svg"
                            class="button1__icon-svg button1__icon-svg--copy">
                            <path
                                d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                                fill="currentColor"></path>
                        </svg>
                    </span>
                    Probar ahora
                </button>
            </section>
            <!-- funcionalidades -->
            <section class="mt-48 relative">
                <img class="object-contain select-none absolute left-0 -top-72" :draggable="false"
                    src="@/../../public/images/bg-02.png" alt="Destello neon de adorno en el fondo"
                    data-aos="zoom-in-down" data-aos-duration="800">
                <h1 class="text-5xl text-center font-extrabold text-white" data-aos="fade-up" data-aos-duration="500"
                    data-aos-offset="100">FUNCIONALIDAD DE LOS MÓDULOS</h1>
                <!-- Punto de venta -->
                <article class="mx-40 flex space-x-32 relative" data-aos="fade-left" data-aos-duration="700"
                    data-aos-offset="450">
                    <figure class="w-[40%]">
                        <img class="object-contain select-none" :draggable="false"
                            src="@/../../public/images/features-01.png"
                            alt="Carrito de compras con aparatos electronicos entrando y brillando">
                    </figure>
                    <div class="w-1/2 text-white mt-52">
                        <h2 class="font-bold text-4xl">Punto de venta</h2>
                        <p class="text-justify text-3xl mt-8">Integración con báscula, impresoras de tickets y más para
                            agilizar tus ventas.</p>
                    </div>
                </article>
                <!-- Registro de venta -->
                <article class="mt-20 relative" data-aos="fade-left" data-aos-duration="700" data-aos-offset="320">
                    <img class="object-contain select-none absolute right-0 -top-40" :draggable="false"
                        src="@/../../public/images/bg-03.png" alt="Destello neon de adorno en el fondo"
                        data-aos="zoom-in-down" data-aos-duration="800">
                    <div class="mx-40 flex space-x-32">
                        <div class="w-1/2 text-white">
                            <h2 class="font-bold text-4xl">Registro de ventas</h2>
                            <p class="text-justify text-3xl mt-8">Accede al historial completo de transacciones en
                                segundos.
                            </p>
                            <ul class="list-disc text-3xl mt-8 *:mt-3">
                                <li>Edita ventas</li>
                                <li>Haz reembolsos</li>
                                <li>Imprime tickets</li>
                            </ul>
                        </div>
                        <figure class="w-[38%] z-10">
                            <img class="object-contain select-none" :draggable="false"
                                src="@/../../public/images/features-02.png"
                                alt="Carrito de compras con aparatos electronicos entrando y brillando">
                        </figure>
                    </div>
                </article>
                <!-- Clientes -->
                <article class="mt-32 relative" data-aos="fade-left" data-aos-duration="700" data-aos-offset="320">
                    <img class="object-contain select-none absolute left-[5%] -top-40" :draggable="false"
                        src="@/../../public/images/bg-04.png" alt="Destello neon de adorno en el fondo"
                        data-aos="zoom-in-down" data-aos-duration="800">
                    <div class="mx-40 flex space-x-32 text-end">
                        <figure class="w-[65%] z-10">
                            <img class="object-contain select-none" :draggable="false"
                                src="@/../../public/images/features-03.png"
                                alt="Carrito de compras con aparatos electronicos entrando y brillando">
                        </figure>
                        <div class="w-[36%] text-white mt-36">
                            <h2 class="font-bold text-4xl">Clientes</h2>
                            <p class="text-justify text-3xl mt-8">
                                Gestiona toda la información de tus clientes en un solo lugar.
                            </p>
                            <ul class="text-2xl mt-8 *:mt-3">
                                <li class="flex items-center justify-end space-x-2">
                                    <svg width="28" height="28" viewBox="0 0 13 13" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g filter="url(#filter0_f_16318_47)">
                                            <circle cx="6.5" cy="6.5" r="2.5" fill="#0B6965" />
                                        </g>
                                        <circle cx="6.5" cy="6.5" r="2.5" fill="#13B5AD" />
                                        <circle cx="5.5" cy="5.5" r="2.5" fill="#D9D9D9" />
                                        <defs>
                                            <filter id="filter0_f_16318_47" x="0" y="0" width="13" height="13"
                                                filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix"
                                                    result="shape" />
                                                <feGaussianBlur stdDeviation="2"
                                                    result="effect1_foregroundBlur_16318_47" />
                                            </filter>
                                        </defs>
                                    </svg>
                                    <span>Historial de compras</span>
                                </li>
                                <li class="flex items-center justify-end space-x-2">
                                    <svg width="28" height="28" viewBox="0 0 13 13" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g filter="url(#filter0_f_16318_47)">
                                            <circle cx="6.5" cy="6.5" r="2.5" fill="#0B6965" />
                                        </g>
                                        <circle cx="6.5" cy="6.5" r="2.5" fill="#13B5AD" />
                                        <circle cx="5.5" cy="5.5" r="2.5" fill="#D9D9D9" />
                                        <defs>
                                            <filter id="filter0_f_16318_47" x="0" y="0" width="13" height="13"
                                                filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix"
                                                    result="shape" />
                                                <feGaussianBlur stdDeviation="2"
                                                    result="effect1_foregroundBlur_16318_47" />
                                            </filter>
                                        </defs>
                                    </svg>
                                    <span>Ventas a crédito y al contado</span>
                                </li>
                                <li class="flex items-center justify-end space-x-2">
                                    <svg width="28" height="28" viewBox="0 0 13 13" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g filter="url(#filter0_f_16318_47)">
                                            <circle cx="6.5" cy="6.5" r="2.5" fill="#0B6965" />
                                        </g>
                                        <circle cx="6.5" cy="6.5" r="2.5" fill="#13B5AD" />
                                        <circle cx="5.5" cy="5.5" r="2.5" fill="#D9D9D9" />
                                        <defs>
                                            <filter id="filter0_f_16318_47" x="0" y="0" width="13" height="13"
                                                filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix"
                                                    result="shape" />
                                                <feGaussianBlur stdDeviation="2"
                                                    result="effect1_foregroundBlur_16318_47" />
                                            </filter>
                                        </defs>
                                    </svg>
                                    <span>Cotizaciones</span>
                                </li>
                                <li>y mucho más...</li>
                            </ul>
                        </div>
                    </div>
                </article>
                <!-- Tienda en linea -->
                <article class="mt-32 flex space-x-32 bg-purple-500 px-40 py-16">
                    <!-- <img class="object-contain select-none" :draggable="false" data-aos="zoom-in-down"
                        data-aos-duration="1100" data-aos-offset="150" src="@/../../public/images/features-04.png"
                        alt="Fondo con nubes y luces de color morado y azul neon"> -->
                    <div class="w-[45%]">
                        <div class="w-[66%] text-white" data-aos="fade-down" data-aos-duration="1600"
                            data-aos-offset="300" data-aos-delay="300">
                            <h2 class="font-bold text-4xl">Tienda en línea</h2>
                            <p class="text-justify text-2xl mt-8">
                                Lleva tu negocio al mundo digital con una tienda en línea integrada.
                            </p>
                            <p class="text-lg mt-4 text-end">
                                Sincronización con inventario
                            </p>
                            <p class="text-lg text-end">
                                Seguimiento de pedidos
                            </p>
                        </div>
                        <img class="w-[20%] object-contain select-none mt-24" :draggable="false"
                            src="@/../../public/images/qr-code.png"
                            alt="Código QR con el logo de EzyVentas en el centro" data-aos="fade-right"
                            data-aos-duration="1600" data-aos-delay="900">
                    </div>
                    <div class="flex items-end space-x-10">
                        <div class="bg-[#c6c6c6]/60 rounded-[10px] h-80 w-72 relative pt-52 px-6">
                            <img class="w-[60%] object-contain select-none absolute left-[calc(50%-80px)] -top-10"
                                :draggable="false" src="@/../../public/images/robot-01.png"
                                alt="Robot pequeño, blanco con 4 ruedas y 2 ojos grandes">
                            <div>
                                <p class="text-[#1D1D1D] text-sm font-medium">Robot de entrega</p>
                                <p class="text-[#414141] text-sm font-normal">MXN</p>
                                <p class="text-[#1D1D1D] text-lg font-bold">${{ quantity1 * 90 }} K</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <el-input-number v-model="quantity1" size="small" :min="1" :max="5" />
                                <button @click=""
                                    class="size-8 group flex items-center justify-center rounded-full border border-[#3e3e3e] hover:border-white hover:bg-primary hover:border-transparent hover:text-white transition-all ease-linear duration-200 disabled:bg-gray-300 disabled:cursor-not-allowed disabled:text-black">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-5 text-[#3e3e3e] group-hover:text-white transition-all ease-linear duration-200">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="bg-[#c6c6c6]/60 rounded-[10px] h-[28rem] w-72 relative pt-[21rem] px-6">
                            <img class="w-[60%] object-contain select-none absolute left-[calc(50%-90px)] -top-28"
                                :draggable="false" src="@/../../public/images/robot-02.png"
                                alt="Robot Alto, blanco con forma humanoide">
                            <div>
                                <p class="text-[#1D1D1D] text-sm font-medium">Robot asistente</p>
                                <p class="text-[#414141] text-sm font-normal">MXN</p>
                                <p class="text-[#1D1D1D] text-lg font-bold">${{ quantity2 * 180 }} K</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <el-input-number v-model="quantity2" size="small" :min="1" :max="5" />
                                <button @click=""
                                    class="size-8 group flex items-center justify-center rounded-full border border-[#3e3e3e] hover:border-white hover:bg-primary hover:border-transparent hover:text-white transition-all ease-linear duration-200 disabled:bg-gray-300 disabled:cursor-not-allowed disabled:text-black">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-5 text-[#3e3e3e] group-hover:text-white transition-all ease-linear duration-200">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
                <!-- <article class="mt-32 relative">
                    <img class="object-contain select-none" :draggable="false" data-aos="zoom-in-down"
                        data-aos-duration="1100" data-aos-offset="150" src="@/../../public/images/features-04.png"
                        alt="Fondo con nubes y luces de color morado y azul neon">
                    <img class="w-[8%] object-contain select-none absolute left-[12%] bottom-60" :draggable="false"
                        src="@/../../public/images/qr-code.png" alt="Código QR con el logo de EzyVentas en el centro"
                        data-aos="fade-right" data-aos-duration="1600" data-aos-delay="900">
                    <div class="w-[24%] text-white absolute left-[13%] top-60" data-aos="fade-down"
                        data-aos-duration="1600" data-aos-offset="300" data-aos-delay="300">
                        <h2 class="font-bold text-4xl">Tienda en línea</h2>
                        <p class="text-justify text-2xl mt-8">
                            Lleva tu negocio al mundo digital con una tienda en línea integrada.
                        </p>
                        <p class="text-lg mt-4 text-end">
                            Sincronización con inventario
                        </p>
                        <p class="text-lg text-end">
                            Seguimiento de pedidos
                        </p>
                    </div>
                    <div class="bg-[#c6c6c6]/60 rounded-[10px] h-80 w-72 absolute left-[45%] top-60 pt-52 px-6">
                        <img class="w-[60%] object-contain select-none absolute left-[calc(50%-80px)] -top-10"
                            :draggable="false" src="@/../../public/images/robot-01.png"
                            alt="Robot pequeño, blanco con 4 ruedas y 2 ojos grandes">
                        <div>
                            <p class="text-[#1D1D1D] text-sm font-medium">Robot de entrega</p>
                            <p class="text-[#414141] text-sm font-normal">MXN</p>
                            <p class="text-[#1D1D1D] text-lg font-bold">${{ quantity1 * 90 }} K</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <el-input-number v-model="quantity1" size="small" :min="1" :max="5" />
                            <button @click=""
                                class="size-8 group flex items-center justify-center rounded-full border border-[#3e3e3e] hover:border-white hover:bg-primary hover:border-transparent hover:text-white transition-all ease-linear duration-200 disabled:bg-gray-300 disabled:cursor-not-allowed disabled:text-black">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-[#3e3e3e] group-hover:text-white transition-all ease-linear duration-200">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="bg-[#c6c6c6]/60 rounded-[10px] h-[28rem] w-72 absolute right-[13%] top-60 pt-[21rem] px-6">
                        <img class="w-[60%] object-contain select-none absolute left-[calc(50%-90px)] -top-28"
                            :draggable="false" src="@/../../public/images/robot-02.png"
                            alt="Robot Alto, blanco con forma humanoide">
                        <div>
                            <p class="text-[#1D1D1D] text-sm font-medium">Robot asistente</p>
                            <p class="text-[#414141] text-sm font-normal">MXN</p>
                            <p class="text-[#1D1D1D] text-lg font-bold">${{ quantity2 * 180 }} K</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <el-input-number v-model="quantity2" size="small" :min="1" :max="5" />
                            <button @click=""
                                class="size-8 group flex items-center justify-center rounded-full border border-[#3e3e3e] hover:border-white hover:bg-primary hover:border-transparent hover:text-white transition-all ease-linear duration-200 disabled:bg-gray-300 disabled:cursor-not-allowed disabled:text-black">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-[#3e3e3e] group-hover:text-white transition-all ease-linear duration-200">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </article> -->
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
                <p>Copyright &copy; 2024 | Todos los derechos reservador por Ezy Ventas</p>
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
    </div>
</template>
