<template>

    <Head title="Ezy Ventas tutorial" />
    <div>
        <!-- Videos del tutorial  -->
        <Modal :maxWidth="'2xl'" :show="tutorialModal">
            <div class="py-7 px-7 relative">
                <!-- <button v-if="step !== 1" @click="prevStep" class="hover:bg-grayD9 cursor-pointer rounded-full size-5 text-xs"><i class="fa-solid fa-chevron-left"></i></button> -->
                <figure class="mx-auto w-32">
                    <img class="" src="@/../../public/images/black_logo.png" alt="logo">
                </figure>
                <h1 class="font-bold text-2xl text-center mb-2">Bienvenido(a) {{ $page.props.auth.user.store.name }}
                </h1>
                <p class="mb-5 text-center">Completa el siguiente tutorial para poder comenzar a utilizar Ezy Ventas</p>

                <!-- Barra de progreso -->
                <div class="grid grid-cols-3 gap-x-3 mb-4 mx-12">
                    <template v-for="index in 3" :key="index">
                        <button @click="step = index"
                            :class="{ 'border-2 border-primary w-full': index <= step, 'border-2 w-full': index > step }"></button>
                    </template>
                </div>

                <p class="font-bold mb-2 text-center" v-text="tutorialContent[step - 1].title"></p>

                <section class="flex justify-center h-72 *:h-full *:rounded-[5px]">
                    <video v-if="step === 1" controls>
                        <source src="@/../../public/Videos/Tutorial_point_EzyV.mp4" type="video/mp4">
                        Tu navegador no soporta la reproducción de video.
                    </video>

                    <video v-if="step === 2" controls>
                        <source src="@/../../public/Videos/Tutorial_productos_EzyV.mp4" type="video/mp4">
                        Tu navegador no soporta la reproducción de video.
                    </video>

                    <video v-if="step === 3" controls>
                        <source src="@/../../public/Videos/Tutorial_graficas_EzyV.mp4" type="video/mp4">
                        Tu navegador no soporta la reproducción de video.
                    </video>
                </section>
                <div class="flex justify-end items-center space-x-5 pt-2 pb-1 py-3 mt-9">
                    <button type="button" v-if="step !== 1" @click="prevStep" class="text-primary text-sm">
                        <i class="fa-solid fa-chevron-left mr-2"></i>
                        Regresar
                    </button>
                    <button @click="nextStep" class="btn-conteiner">
                        <a class="btn-content" href="#">
                            <span class="btn-title">Siguiente</span>
                            <span class="icon-arrow">
                            <svg width="33px" height="21px" viewBox="0 0 66 43" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g id="arrow" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <path id="arrow-icon-one" d="M40.1543933,3.89485454 L43.9763149,0.139296592 C44.1708311,-0.0518420739 44.4826329,-0.0518571125 44.6771675,0.139262789 L65.6916134,20.7848311 C66.0855801,21.1718824 66.0911863,21.8050225 65.704135,22.1989893 C65.7000188,22.2031791 65.6958657,22.2073326 65.6916762,22.2114492 L44.677098,42.8607841 C44.4825957,43.0519059 44.1708242,43.0519358 43.9762853,42.8608513 L40.1545186,39.1069479 C39.9575152,38.9134427 39.9546793,38.5968729 40.1481845,38.3998695 C40.1502893,38.3977268 40.1524132,38.395603 40.1545562,38.3934985 L56.9937789,21.8567812 C57.1908028,21.6632968 57.193672,21.3467273 57.0001876,21.1497035 C56.9980647,21.1475418 56.9959223,21.1453995 56.9937605,21.1432767 L40.1545208,4.60825197 C39.9574869,4.41477773 39.9546013,4.09820839 40.1480756,3.90117456 C40.1501626,3.89904911 40.1522686,3.89694235 40.1543933,3.89485454 Z" fill="#FFFFFF"></path>
                                <path id="arrow-icon-two" d="M20.1543933,3.89485454 L23.9763149,0.139296592 C24.1708311,-0.0518420739 24.4826329,-0.0518571125 24.6771675,0.139262789 L45.6916134,20.7848311 C46.0855801,21.1718824 46.0911863,21.8050225 45.704135,22.1989893 C45.7000188,22.2031791 45.6958657,22.2073326 45.6916762,22.2114492 L24.677098,42.8607841 C24.4825957,43.0519059 24.1708242,43.0519358 23.9762853,42.8608513 L20.1545186,39.1069479 C19.9575152,38.9134427 19.9546793,38.5968729 20.1481845,38.3998695 C20.1502893,38.3977268 20.1524132,38.395603 20.1545562,38.3934985 L36.9937789,21.8567812 C37.1908028,21.6632968 37.193672,21.3467273 37.0001876,21.1497035 C36.9980647,21.1475418 36.9959223,21.1453995 36.9937605,21.1432767 L20.1545208,4.60825197 C19.9574869,4.41477773 19.9546013,4.09820839 20.1480756,3.90117456 C20.1501626,3.89904911 20.1522686,3.89694235 20.1543933,3.89485454 Z" fill="#FFFFFF"></path>
                                <path id="arrow-icon-three" d="M0.154393339,3.89485454 L3.97631488,0.139296592 C4.17083111,-0.0518420739 4.48263286,-0.0518571125 4.67716753,0.139262789 L25.6916134,20.7848311 C26.0855801,21.1718824 26.0911863,21.8050225 25.704135,22.1989893 C25.7000188,22.2031791 25.6958657,22.2073326 25.6916762,22.2114492 L4.67709797,42.8607841 C4.48259567,43.0519059 4.17082418,43.0519358 3.97628526,42.8608513 L0.154518591,39.1069479 C-0.0424848215,38.9134427 -0.0453206733,38.5968729 0.148184538,38.3998695 C0.150289256,38.3977268 0.152413239,38.395603 0.154556228,38.3934985 L16.9937789,21.8567812 C17.1908028,21.6632968 17.193672,21.3467273 17.0001876,21.1497035 C16.9980647,21.1475418 16.9959223,21.1453995 16.9937605,21.1432767 L0.15452076,4.60825197 C-0.0425130651,4.41477773 -0.0453986756,4.09820839 0.148075568,3.90117456 C0.150162624,3.89904911 0.152268631,3.89694235 0.154393339,3.89485454 Z" fill="#FFFFFF"></path>
                                </g>
                            </svg>
                            </span> 
                        </a>
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Configuraciones de la tienda -->
        <Modal :maxWidth="'2xl'" :show="configModal">
            <div class="py-8 px-7 relative">
                <BackButton @click="tutorialModal = true; configModal = false" />
                <figure class="mx-auto w-32">
                    <img class="" src="@/../../public/images/black_logo.png" alt="logo">
                </figure>
                <h1 class="font-bold text-2xl text-center mb-5">Bienvenido(a) {{ $page.props.auth.user.store.name }}
                </h1>

                <p class="font-bold">Configura tu tienda.</p>
                <p class="text-sm">La configuración inicial te permite personalizar tu experiencia de venta según tus
                    necesidades. Puedes omitir este paso y ajustarlo más tarde en el módulo de configuraciones</p>

                <section class="mt-5 overflow-auto h-72">
                    <div v-for="(item, index) in settings" :key="item.id" class="mb-3 mx-2">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold">{{ item.key }}</p>
                            <el-switch @change="updateSettingValue(index)" inline-prompt
                                style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #CCCCCC"
                                active-text=" Habilitado " inactive-text=" Deshabilitado " v-model="values[index]"
                                :loading="settingLoading[index]" size="small" class="ml-2" />
                        </div>
                        <p class="text-gray99 text-sm w-5/6 text-justify">{{ item.description }}</p>
                    </div>
                </section>

                <div class="flex justify-end space-x-5 pt-2 pb-1 py-3 mt-9">
                    <button class="text-primary" @click="configModal = false; finishModal = true">Omitir</button>
                    <button @click="configModal = false; finishModal = true; launchConfetti()" class="btn-conteiner">
                        <a class="btn-content" href="#">
                            <span class="btn-title">Siguiente</span>
                            <span class="icon-arrow">
                            <svg width="33px" height="21px" viewBox="0 0 66 43" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g id="arrow" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <path id="arrow-icon-one" d="M40.1543933,3.89485454 L43.9763149,0.139296592 C44.1708311,-0.0518420739 44.4826329,-0.0518571125 44.6771675,0.139262789 L65.6916134,20.7848311 C66.0855801,21.1718824 66.0911863,21.8050225 65.704135,22.1989893 C65.7000188,22.2031791 65.6958657,22.2073326 65.6916762,22.2114492 L44.677098,42.8607841 C44.4825957,43.0519059 44.1708242,43.0519358 43.9762853,42.8608513 L40.1545186,39.1069479 C39.9575152,38.9134427 39.9546793,38.5968729 40.1481845,38.3998695 C40.1502893,38.3977268 40.1524132,38.395603 40.1545562,38.3934985 L56.9937789,21.8567812 C57.1908028,21.6632968 57.193672,21.3467273 57.0001876,21.1497035 C56.9980647,21.1475418 56.9959223,21.1453995 56.9937605,21.1432767 L40.1545208,4.60825197 C39.9574869,4.41477773 39.9546013,4.09820839 40.1480756,3.90117456 C40.1501626,3.89904911 40.1522686,3.89694235 40.1543933,3.89485454 Z" fill="#FFFFFF"></path>
                                <path id="arrow-icon-two" d="M20.1543933,3.89485454 L23.9763149,0.139296592 C24.1708311,-0.0518420739 24.4826329,-0.0518571125 24.6771675,0.139262789 L45.6916134,20.7848311 C46.0855801,21.1718824 46.0911863,21.8050225 45.704135,22.1989893 C45.7000188,22.2031791 45.6958657,22.2073326 45.6916762,22.2114492 L24.677098,42.8607841 C24.4825957,43.0519059 24.1708242,43.0519358 23.9762853,42.8608513 L20.1545186,39.1069479 C19.9575152,38.9134427 19.9546793,38.5968729 20.1481845,38.3998695 C20.1502893,38.3977268 20.1524132,38.395603 20.1545562,38.3934985 L36.9937789,21.8567812 C37.1908028,21.6632968 37.193672,21.3467273 37.0001876,21.1497035 C36.9980647,21.1475418 36.9959223,21.1453995 36.9937605,21.1432767 L20.1545208,4.60825197 C19.9574869,4.41477773 19.9546013,4.09820839 20.1480756,3.90117456 C20.1501626,3.89904911 20.1522686,3.89694235 20.1543933,3.89485454 Z" fill="#FFFFFF"></path>
                                <path id="arrow-icon-three" d="M0.154393339,3.89485454 L3.97631488,0.139296592 C4.17083111,-0.0518420739 4.48263286,-0.0518571125 4.67716753,0.139262789 L25.6916134,20.7848311 C26.0855801,21.1718824 26.0911863,21.8050225 25.704135,22.1989893 C25.7000188,22.2031791 25.6958657,22.2073326 25.6916762,22.2114492 L4.67709797,42.8607841 C4.48259567,43.0519059 4.17082418,43.0519358 3.97628526,42.8608513 L0.154518591,39.1069479 C-0.0424848215,38.9134427 -0.0453206733,38.5968729 0.148184538,38.3998695 C0.150289256,38.3977268 0.152413239,38.395603 0.154556228,38.3934985 L16.9937789,21.8567812 C17.1908028,21.6632968 17.193672,21.3467273 17.0001876,21.1497035 C16.9980647,21.1475418 16.9959223,21.1453995 16.9937605,21.1432767 L0.15452076,4.60825197 C-0.0425130651,4.41477773 -0.0453986756,4.09820839 0.148075568,3.90117456 C0.150162624,3.89904911 0.152268631,3.89694235 0.154393339,3.89485454 Z" fill="#FFFFFF"></path>
                                </g>
                            </svg>
                            </span> 
                        </a>
                    </button>
                </div>
            </div>
        </Modal>

        <!-- modal de finalización -->
        <Modal :show="finishModal">
            <div class="py-4 px-7 relative">
                <h1 class="font-bold text-center mb-5">¡Haz terminado la configuración tu tienda!</h1>
                <p class="text-sm text-center">Te invitamos a seguir explorando sobre las diversas funcionalidades y
                    herramientas del sistema.</p>

                <figure class="my-7 flex items-center justify-center select-none">
                    <img draggable="false" class="w-1/2" src="@/../../public/images/rocket.jpg" alt="">
                </figure>

                <div class="text-center">
                    <button @click="$inertia.put(route('users.tutorials-completed'))"
                    type="submit"
                    class="flex justify-center gap-2 items-center mx-auto shadow-xl text-lg bg-gray-100 backdrop-blur-md lg:font-semibold isolation-auto border-gray-50 before:absolute before:w-full before:transition-all before:duration-700 before:hover:w-full before:-left-full before:hover:left-0 before:rounded-full before:bg-primary hover:text-gray-50 before:-z-10 before:aspect-square before:hover:scale-150 before:hover:duration-700 relative z-10 px-10 py-2 overflow-hidden border-2 rounded-full group"
                    >
                    Comenzar ahora
                    <svg
                        class="w-8 h-8 justify-end group-hover:rotate-90 group-hover:bg-gray-50 text-gray-50 ease-linear duration-300 rounded-full border border-gray-700 group-hover:border-none p-2 rotate-45"
                        viewBox="0 0 16 19"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                        d="M7 18C7 18.5523 7.44772 19 8 19C8.55228 19 9 18.5523 9 18H7ZM8.70711 0.292893C8.31658 -0.0976311 7.68342 -0.0976311 7.29289 0.292893L0.928932 6.65685C0.538408 7.04738 0.538408 7.68054 0.928932 8.07107C1.31946 8.46159 1.95262 8.46159 2.34315 8.07107L8 2.41421L13.6569 8.07107C14.0474 8.46159 14.6805 8.46159 15.0711 8.07107C15.4616 7.68054 15.4616 7.04738 15.0711 6.65685L8.70711 0.292893ZM9 18L9 1H7L7 18H9Z"
                        class="fill-gray-800 group-hover:fill-gray-800"
                        ></path>
                    </svg>
                    </button>
                </div>

                <p class="text-xs text-center mt-8">¡Descubre lo que Ezy Ventas puede hacer por ti y tu negocio!</p>
            </div>
        </Modal>
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BackButton from '@/Components/MyComponents/BackButton.vue';
import Modal from "@/Components/Modal.vue";
import confetti from "canvas-confetti";
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    name: "ConfettiButton",
    data() {
        return {
            tutorialModal: true,
            step: 1,
            tutorialContent: [
                { title: 'Video tutorial para realizar tus ventas' },
                { title: 'Video tutorial para cargar tus productos' },
                { title: 'Video tutorial de análisis de ventas y gastos' }
            ],
            configModal: false,
            finishModal: false,
            loading: false,
            settingLoading: [],
            settings: [],
            values: [],
        }
    },
    components: {
        PrimaryButton,
        BackButton,
        Modal,
        Head
    },
    props: {

    },
    methods: {
        launchConfetti() {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
            });
        },
        async fetchModuleSettings() {
            try {
                this.loading = true;
                const response = await axios.get(route('stores.get-settings-by-module', {
                    store: this.$page.props.auth.user.store_id, module: 'Punto de venta'
                }));

                if (response.status === 200) {
                    this.settings = response.data.items;
                    this.settingLoading = new Array(response.data.items.length).fill(false);
                    this.values = response.data.items.map(item => {
                        return item.type == 'Bool'
                            ? Boolean(item.pivot.value)
                            : item.pivot.value;
                    });
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async updateSettingValue(index) {
            try {
                this.settingLoading[index] = true
                const response = await axios.put(route('stores.toggle-setting-value', {
                    store: this.$page.props.auth.user.store_id,
                    setting_id: this.settings[index].id
                }), { value: this.values[index] });

                if (response.status === 200) {
                    // por lo pronto no se requiere hacer nada
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.settingLoading[index] = false;
                return true;
            }
        },
        nextStep() {
            if (this.step < 3) {
                this.step++;
            } else if (!this.$page.props.auth.user.tutorials_seen) {
                this.tutorialModal = false;
                this.configModal = true;
            } else {
                this.$inertia.visit(route('sales.point'));
            }
        },
        prevStep() {
            this.step--;
        },
    },
    mounted() {
        this.fetchModuleSettings();
    }
}
</script>

<style>
/* Boton siguiente -------------------------------------- */ 
.btn-conteiner {
  display: flex;
  justify-content: center;
  --color-text: #ffffff;
  --color-background: #F68C0F;
  --color-outline: #be6906;
  --color-shadow: #00000080;
}

.btn-content {
  display: flex;
  align-items: center;
  padding: 5px 30px;
  text-decoration: none;
  font-family: 'Poppins', sans-serif;
  font-weight: 300px;
  font-size: 15px;
  color: var(--color-text);
  background: var(--color-background);
  transition: 1s;
  border-radius: 100px;
  box-shadow: 0 0 0.2em 0 var(--color-background);
}

.btn-content:hover, .btn-content:focus {
  transition: 0.5s;
  -webkit-animation: btn-content 1s;
  animation: btn-content 1s;
  outline: 0.1em solid transparent;
  outline-offset: 0.2em;
  box-shadow: 0 0 0.4em 0 var(--color-background);
}

.btn-content .icon-arrow {
  transition: 0.5s;
  margin-right: 0px;
  transform: scale(0.6);
}

.btn-content:hover .icon-arrow {
  transition: 0.5s;
  margin-right: 25px;
}

.icon-arrow {
  width: 10px;
  margin-left: 15px;
  position: relative;
  top: 6%;
}
  
/* SVG */
#arrow-icon-one {
  transition: 0.4s;
  transform: translateX(-60%);
}

#arrow-icon-two {
  transition: 0.5s;
  transform: translateX(-30%);
}

.btn-content:hover #arrow-icon-three {
  animation: color_anim 1s infinite 0.2s;
}

.btn-content:hover #arrow-icon-one {
  transform: translateX(0%);
  animation: color_anim 1s infinite 0.6s;
}

.btn-content:hover #arrow-icon-two {
  transform: translateX(0%);
  animation: color_anim 1s infinite 0.4s;
}

/* SVG animations */
@keyframes color_anim {
  0% {
    fill: white;
  }

  50% {
    fill: var(--color-background);
  }

  100% {
    fill: white;
  }
}

/* Button animations */
@-webkit-keyframes btn-content {
  0% {
    outline: 0.2em solid var(--color-background);
    outline-offset: 0;
  }
}

@keyframes btn-content {
  0% {
    outline: 0.2em solid var(--color-background);
    outline-offset: 0;
  }
}
</style>
