<template>

    <Head title="Tienda" />
    <div class="bg-black2 relative overflow-x-hidden">
        <nav class="bg-transparent" data-aos="zoom-in" data-aos-duration="1200">
            <div class="max-w-8xl mx-auto px-4 md:px-7 py-3">
                <div class="flex justify-between items-center h-14 bg-[#404040]/30 rounded-full pl-3 pr-2">
                    <div class="flex">
                        <!-- Logo -->
                        <Link href="/" class="shrink-0 flex items-center">
                        <ApplicationMark class="block h-10 md:h-12 w-auto" />
                        </Link>
                    </div>
                    <div class="flex sm:items-center space-x-12 sm:ms-6">
                        <button @click="$inertia.visit('/')"
                            class="text-white focus:border-none focus:ring-0 hidden lg:block"
                            type="button">Inicio</button>
                        <Link :href="$page.props.auth.user ? route('dashboard') : route('login')">
                        <button class="buttonupgrade">
                            Iniciar sesión
                        </button>
                        </Link>
                    </div>
                </div>
            </div>
        </nav>
        <main class="bg-transparent selection:bg-primary selection:text-white pb-24 relative">
            <figure class="relative w-11/12 lg:w-5/4 mt-3 mx-auto">
                <img src="@/../../public/images/referalBanner.png" alt="Banner con anunncio de recomienda y gana"
                    :draggable="false" class="object-cover select-none w-full hidden lg:block">
                <img src="@/../../public/images/referalBannerMobile.png" alt="Banner con anunncio de recomienda y gana"
                    :draggable="false" class="object-cover select-none w-full lg:hidden">
                <p class="w-[86%] lg:w-[54%] text-white text-xl lg:text-5xl absolute top-5 lg:top-8 left-[7%] lg:left-[23%] text-center tracking-wide"
                    style="font-family: 'LeagueGothic';">
                    ¡Recomienda y gana el 50% del pago a cada referido!
                </p>
                <p
                    class="w-[96%] lg:w-[66%] text-white text-sm lg:text-2xl absolute bottom-3 left-[2%] lg:left-[17%] text-center">
                    Registrarte, genera tu cupón y compártelo con amigos y negocios!
                </p>
            </figure>
            <!-- Lista de productos -->
            <section class="mt-3 mx-3 lg:mx-40 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div @click="$inertia.visit(route('login'))"
                    class="group cursor-pointer rounded-xl bg-[#171717] border border-transparent px-6 py-5 h-80 hover:shadow-lg hover:scale-105 hover:border-[#323232] transition-all duration-300">
                    <figure class="h-2/3 w-full">
                        <img src="@/../../public/images/robot-01.png" :draggable="false"
                            alt="Báscula Electrónica 20 kg Rhino" class="object-contain h-full select-none mx-auto">
                    </figure>
                    <div class="h-1/3 text-white flex flex-col justify-between">
                        <p class="mt-2 text-sm">{{ 'Báscula Electrónica 20 kg Rhino' }}</p>
                        <p class="flex items-center justify-between mt-3">
                            <strong class="text-lg">${{ '1,799.00' }}</strong>
                            <button @click.stop="" class="items-center space-x-3 buttonwhatsapp group-hover:flex">
                                <i class="fa-brands fa-whatsapp"></i>
                                <span>Enviar mensaje</span>
                            </button>
                        </p>
                    </div>
                </div>
            </section>
            <button v-if="showScrollButton" @click="scrollToTop"
                class="fixed bottom-10 right-4 flex items-center justify-center size-10 rounded-full bg-grayD9">
                <i class="fa-solid fa-arrow-up fa-bounce text-gray37"></i>
            </button>
        </main>
        <!-- footer -->
        <footer class="bg-black1 p-4">
            <div class="border-b border-[#373737] w-full"></div>
            <div class="md:justify-between text-white text-xs my-3">
                <div class="flex justify-center space-x-3">
                    <a class="underline" target="_blank" :href="route('terms.show')">Términos y condiciones</a>
                    <a class="underline" target="_blank" :href="route('policy.show')">Política de privacidad</a>
                </div>
                <p class="mt-3">Copyright &copy; 2024-2025 | Todos los derechos reservador por Ezy Ventas</p>
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

<script>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import confetti from "canvas-confetti";
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import axios from 'axios';
import Back from '@/Components/MyComponents/Back.vue';

export default {
    data() {
        const form = useForm({
            name: null,
            last_name: null,
            phone: null,
            email: null,
            code: Math.random().toString(36).substring(2, 7).toUpperCase(), // codigo alfanumerico con 5 caracteres en mayusculas
        });

        return {
            form,
            showScrollButton: false,
            showRecuperationForm: false,
            registered: false,
            // recuperación de código
            recuperationEmail: null,
            recoveredCode: null,
            recuperationMessage: null,
            fetchingCode: false,
        }
    },
    components: {
        Head,
        Link,
        ApplicationMark,
        InputLabel,
        InputError,
        PrimaryButton,
        Back,
    },
    methods: {
        store() {
            this.form.post(route('partners.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    this.registered = true;
                    this.launchConfetti();
                },
            });
        },
        scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        },
        handleScroll() {
            this.showScrollButton = window.scrollY > 1000;
        },
        launchConfetti() {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
            })
        },
        copyText(text) {
            navigator.clipboard.writeText(text).then(() => {
                ElMessage({
                    message: 'Copiado al portapapeles',
                    type: 'success',
                });
            }).catch(err => {
                console.error('Error al copiar texto: ', err);
            });
        },
        async fetchCode() {
            this.fetchingCode = true;
            try {
                const response = await axios.post(route('landing.recover-partner'), {
                    email: this.recuperationEmail,
                });

                if (response.status === 200) {
                    this.recoveredCode = response.data.code;
                    this.recuperationMessage = response.data.message;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.fetchingCode = false;
            }
        },
    },
    mounted() {
        window.addEventListener('scroll', this.handleScroll);
    },
    beforeUnmount() {
        window.removeEventListener('scroll', this.handleScroll);
    },
};
</script>

<style scoped>
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

.buttonwhatsapp {
    width: fit-content;
    display: none;
    padding: 5px 22px;
    cursor: pointer;
    border-radius: 30px;
    text-shadow: 2px 2px 3px #05250830;
    background: linear-gradient(15deg,
            #0A7511,
            #0A7511,
            #08330B,
            #052508,
            #0A7511,
            #0A7511,
            #08330B,
            #052508) no-repeat;
    background-size: 300%;
    color: white;
    border: none;
    background-position: left center;
    transition:
        background 0.3s ease,
        color 0.3s ease;
}

.buttonwhatsapp:hover {
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

/* From Uiverse.io by ayman-ashine */
.card {
    --dark: #212121;
    --darker: #111111;
    --semidark: #2c2c2c;
    --lightgray: #e8e8e8;
    --unit: 10px;

    background-color: var(--darker);
    box-shadow: 0 0 var(--unit) var(--darker);
    border: calc(var(--unit) / 2) solid var(--darker);
    border-radius: var(--unit);
    position: relative;
    padding: var(--unit);
    overflow: hidden;
}

.card::before {
    content: "";
    position: absolute;
    width: 120%;
    height: 20%;
    top: 40%;
    left: -10%;
    background: linear-gradient(144deg, #af40ff, #5b42f3 50%, #00ddeb);
    animation: keyframes-floating-light 2.5s infinite ease-in-out;
    filter: blur(20px);
}

@keyframes keyframes-floating-light {
    0% {
        transform: rotate(-5deg) translateY(-5%);
        opacity: 0.5;
    }

    50% {
        transform: rotate(5deg) translateY(5%);
        opacity: 1;
    }

    100% {
        transform: rotate(-5deg) translateY(-5%);
        opacity: 0.5;
    }
}

.card::after {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0%;
    left: 0%;
    background: linear-gradient(144deg, #af40ff, #5b42f3 50%, #00ddeb);
    filter: blur(20px);
    pointer-events: none;
    animation: keyframes-intro 1s ease-in forwards;
}

@keyframes keyframes-intro {
    100% {
        transform: translate(-100%);
        opacity: 0;
    }
}

.card .image {
    width: 600px;
    animation: keyframes-floating-img 10s ease-in-out infinite;
}

@keyframes keyframes-floating-img {
    0% {
        transform: translate(-2%, 2%) scaleY(0.95) rotate(-5deg);
    }

    50% {
        transform: translate(2%, -2%) scaleY(1) rotate(5deg);
    }

    100% {
        transform: translate(-2%, 2%) scaleY(0.95) rotate(-5deg);
    }
}

.card .heading {
    font-weight: 400;
    font-size: 17px;
    text-align: center;
    margin-top: 8px;
    margin-bottom: 8px;
    padding-block: var(--unit);
    color: var(--lightgray);
    animation: keyframes-flash-text 0.5s infinite;
}

@keyframes keyframes-flash-text {
    50% {
        opacity: 0.5;
    }
}

.card .icons {
    display: flex;
    gap: var(--unit);
}

.card .icons a {
    display: flex;
    flex-grow: 1;
    align-items: center;
    justify-content: center;
    background-color: var(--dark);
    color: var(--lightgray);
    padding: calc(var(--unit) / 2);
    border-radius: calc(var(--unit) / 2);
}

.card .icons a:hover {
    transition: 0.2s;
    background-color: var(--semidark);
}
</style>