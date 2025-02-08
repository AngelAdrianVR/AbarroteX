<template>
    <p class="text-sm">
        Personaliza y gestiona todos los aspectos relacionados con tu tienda en línea. <br>
        Configura los elementos esenciales para que tu tienda en línea esté lista para recibir pedidos y ofrecer una
        experiencia única a tus clientes.
    </p>
    <section class="my-5 divide-y-[1px] border border-grayD9 rounded-[5px]">
        <!-- logo --->
        <article class="p-4 lg:flex justify-between text-sm">
            <div class="lg:w-1/2">
                <h2 class="flex items-center space-x-1">
                    <span>Logo de tu tienda</span>
                    <svg width="10" height="17" viewBox="0 0 10 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M5.22711 11.5105C5.69133 11.7266 5.87956 11.4963 5.87956 11.2127C6.24845 11.4112 7.12785 10.9574 7.11355 10.5744C7.45407 10.8439 7.79438 10.5886 7.79438 10.2198M5.22711 11.5105L2.04454 4.74486M5.22711 11.5105L7.12057 12.9998M7.79438 10.2198L4.58436 3.51087M7.79438 10.2198L7.78321 12.6169M1.48408 3.55342L1.05707 2.64565C0.915293 2.34779 1.05706 1.85135 1.26983 1.73788L2.71657 1.05706C3.04291 0.929434 3.34077 1.01448 3.53923 1.32655L4.02108 2.33361M1.48408 3.55342L4.02108 2.33361M1.48408 3.55342L2.04454 4.74486M4.02108 2.33361L4.58436 3.51087M2.04454 4.74486L4.58436 3.51087M7.12057 12.9998L7.55336 13.3402C7.63846 13.397 7.7803 13.3119 7.7803 13.241L7.78321 12.6169M7.12057 12.9998C7.23462 12.7401 7.36475 12.6454 7.78321 12.6169M9 11.5495C8.99172 13.3426 8.75227 14.0675 7.11355 13.9182C6.29089 13.8432 2.77992 14.2693 3.58364 14.4349C4.06544 14.5342 7.77059 14.4796 8.23418 15.9851"
                            stroke="currentColor" stroke-width="0.826764" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </h2>
                <p class="text-[#575757] text-justify">
                    Haz clic dentro del recuadro para seleccionar el archivo de tu logo. Asegúrate de que esté en
                    formato PNG o JPEG para una mejor calidad
                </p>
            </div>
            <div>
                <InputFilePreview @imagen="storeLogo($event)" width="w-44" height="h-28" :imageUrl="storeLogoUrl"
                    @cleared="storeLogo()" />
                <p v-if="logoForm.processing" class="text-gray-400 text-end text-xs col-span-full">Guardando...</p>
            </div>
        </article>
        <!-- portada ----------------------------------------->
        <article class="p-4 lg:flex justify-between text-sm">
            <div class="lg:w-1/2">
                <h2 class="flex items-center space-x-1">
                    <span>Portada / Banner</span>
                    <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3.45312 10.9952L4.19 13.8399C4.58414 15.1936 6.70907 14.5767 6.31493 13.2572L5.50951 10.4297"
                            stroke="currentColor" stroke-width="0.8" stroke-linejoin="round" />
                        <path
                            d="M5.76 10.3614L3.18952 11.064C2.94537 11.1377 2.84679 11.0126 2.79538 10.8412L1.84678 7.24257L1.71578 6.74559C1.6472 6.5057 1.69875 6.43715 1.92142 6.36859L4.52617 5.68313C4.73181 5.64885 4.80036 5.73455 4.8689 5.94018L5.96564 9.81304C6.06846 10.1044 5.98278 10.2929 5.76 10.3614Z"
                            stroke="currentColor" stroke-width="0.8" stroke-linejoin="round" />
                        <path
                            d="M4.85938 5.88702C7.23805 5.03871 8.32021 4.00078 10.0346 1.67143M10.0346 1.67143L12.5708 11.028M10.0346 1.67143C9.89752 1.05449 10.6858 0.694605 10.8914 1.34583L13.4619 10.8052C13.6333 11.4735 12.8621 11.7991 12.5708 11.028M12.5708 11.028C10.0615 9.87255 8.63291 9.50385 6.00752 9.89697"
                            stroke="currentColor" stroke-width="0.8" stroke-linejoin="round" />
                        <path d="M13.25 4.34474L13.9697 3.625" stroke="currentColor" stroke-width="0.8"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M13.9766 5.64679L14.9705 5.35547" stroke="currentColor" stroke-width="0.8"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M13.9531 7.10352L14.9985 7.42911" stroke="currentColor" stroke-width="0.8"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M1.8566 7.29297C0.434389 7.87561 0.845616 10.3261 2.62775 10.189" stroke="currentColor"
                            stroke-width="0.8" stroke-linejoin="round" />
                    </svg>
                </h2>
                <p class="text-[#575757] text-justify">
                    Puedes subir o elegir una imagen que representen tu marca, promociones, o cualquier mensaje
                    importante que desees comunicar a tus clientes.
                </p>
            </div>
            <div>
                <div class="flex items-center space-x-1">
                    <button v-for="(banner, index) in banners" :key="index" class="rounded-xl">
                        <img :src="banner.src"
                            class="select-none object-cover rounded-xl h-28 border-2 transition-all duration-300 ease-in-out"
                            @click="bannerForm.selected = index + 1; storeBanner()"
                            :class="bannerForm.selected == index + 1 ? 'w-40 border-primary' : 'w-7 border-transparent'"
                            :draggable="false" :alt="banner.alt">
                    </button>
                    <InputFilePreview @imagen="storeBanner($event)" width="w-44" height="h-28"
                        :imageUrl="storeBannerUrl" @cleared="storeBanner()" />
                </div>
                <p v-if="bannerForm.processing" class="text-gray-400 text-end text-xs col-span-full">Guardando...</p>
            </div>
        </article>
        <!-- contacto ----------------------------------------->
        <article class="p-4 lg:flex items-center justify-between text-sm">
            <div class="lg:w-1/2">
                <h2 class="flex items-center space-x-1">
                    <span>Contacto de Whatsapp</span>
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10.6037 8.64325C10.5598 8.57068 10.4435 8.52599 10.2687 8.43922C10.0939 8.35246 9.23304 7.9281 9.07339 7.87025C8.91374 7.81241 8.79583 7.78244 8.67948 7.95754C8.56313 8.13265 8.22765 8.52599 8.12539 8.64115C8.02313 8.75631 7.92139 8.77261 7.74661 8.68532C7.23076 8.48023 6.75412 8.18648 6.33791 7.81714C5.95586 7.46498 5.62808 7.05734 5.36539 6.60769C5.26104 6.4331 5.35444 6.33845 5.44209 6.25116C5.6255 6.08265 5.77408 5.87929 5.87931 5.65274C5.90267 5.60454 5.91359 5.55116 5.91103 5.49757C5.90848 5.44398 5.89254 5.3919 5.8647 5.34617C5.82244 5.25941 5.47235 4.39807 5.32678 4.04995C5.16191 3.65241 4.42678 3.63874 4.13096 3.96161C3.19757 4.98229 3.41304 6.13863 4.23531 7.22977C4.32296 7.34651 5.44574 9.17068 7.22383 9.87111C9.00191 10.5715 9.00191 10.3375 9.32278 10.3081C10.2447 10.2245 10.8687 9.08654 10.6057 8.64483M7 1.00003C5.41552 0.995409 3.89392 1.62448 2.76908 2.74922C1.64423 3.87396 1.00801 5.40249 1 6.99945C0.999157 8.2651 1.39969 9.49789 2.14313 10.5179L1.39391 12.7465L3.69948 12.0076C4.60148 12.6042 5.64627 12.9449 6.72404 12.9939C7.80182 13.0428 8.87278 12.7983 9.82438 12.2859C10.776 11.7735 11.5731 11.0123 12.1319 10.0821C12.6907 9.15199 12.9905 8.0873 13 6.99997C12.992 5.40297 12.3558 3.87438 11.231 2.74956C10.1061 1.62474 8.58453 0.995549 7 1.00003Z"
                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </h2>
                <p class="text-[#575757] text-justify">
                    Configura el número de WhatsApp de tu tienda para que los clientes puedan contactarte fácilmente.
                    Este número se mostrará en tu tienda en línea.
                </p>
            </div>
            <div class="lg:w-1/2 grid grid-cols-2 gap-3 items-center *:text-end mt-5 lg:mt-0">
                <p>Número de teléfono:</p>
                <el-input v-model="onlinePropertiesForm.whatsapp" @blur="storeOnlineProperties('contact')"
                    @keyup.enter="storeOnlineProperties('contact')" placeholder="Ej. 3333123456"
                    :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                    :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable>
                    <template #prepend>
                        <img src="@/../../public/images/mxFlag.png" alt="Bandera de mexico en miniatura"
                            :draggable="false" class="select-none" />
                        <span class="ml-1">+52</span>
                    </template>
                </el-input>
                <p v-if="contactLoading" class="text-gray-400 text-end text-xs col-span-full">Guardando...</p>
            </div>
        </article>
        <!-- envio ----------------------------------------->
        <article class="p-4 lg:flex justify-between text-sm">
            <div class="lg:w-1/2">
                <h2 class="flex items-center space-x-1">
                    <span>Envío a domicilio</span>
                    <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M5.62661 12.8374C5.66426 15.6044 1.74336 15.4975 1.72009 12.8374M11.6343 12.8374C9.92168 12.8374 8.9615 12.8374 7.24893 12.8374C7.91346 8.44853 4.19605 8.4095 2.20078 10.2593M5.74584 12.8374H1.72009M1.72009 12.8374H1C0.999897 11.8131 1.49154 10.9169 2.20078 10.2593M2.20078 10.2593H1C1.0834 9.53579 1.29526 9.18081 2.14069 8.69099C2.17386 8.67177 2.77138 8.71402 2.80164 8.69099C3.10668 8.24515 3.27771 7.99519 3.58275 7.54936M9.83168 7.54936H1.11923V7.45923M9.83168 7.54936V7.45923M9.83168 7.54936C9.88548 7.49107 9.93226 7.43078 9.97236 7.3691M9.83168 7.54936C8.27412 9.26769 8.14928 10.5536 9.29091 10.9742C10.1591 10.9742 10.6459 10.9742 11.5141 10.9742C12.1149 10.9742 12.7648 10.0144 12.5355 8.75107C12.3174 7.54936 10.3124 3.70386 12.2351 3.52361H12.4154M9.83168 7.3691V7.45923M9.83168 7.3691H7.00764M9.83168 7.3691C9.88662 7.3691 9.91742 7.3691 9.97236 7.3691M1.11923 7.3691V7.45923M1.11923 7.3691H7.00764M1.11923 7.3691V2.80258M1.11923 7.45923H9.83168M7.00764 7.3691V5.62661M1.11923 2.80258V1.78112C1.11923 1.30043 1.47975 1 1.90035 1H6.52696C6.82739 1 7.00764 1.18026 7.00764 1.48069V5.62661M1.11923 2.80258C2.45674 2.80258 3.20662 2.80258 4.54413 2.80258M7.00764 5.62661H9.05056C9.9402 5.62661 10.4697 6.60422 9.97236 7.3691M10.8531 2.38197C11.5914 1.66264 11.9948 1.24034 12.6557 1.24034H13.7373C15.2995 1.24034 15.2394 3.52361 13.7373 3.52361H13.3767M13.3767 3.52361C13.3767 3.1951 13.3767 3.01091 13.3767 2.6824M13.3767 3.52361H12.4154M12.4154 3.52361L14.939 8.99142C15.9052 9.02606 16.4012 9.12298 17.042 9.77253M10.7931 2.62232C10.7931 2.48958 10.6854 2.38197 10.5527 2.38197M10.7931 2.62232C10.7931 2.65303 10.7873 2.6824 10.7768 2.7094M10.7931 2.62232L10.5527 2.38197M10.3124 2.62232C10.3124 2.58995 10.3188 2.55907 10.3304 2.53089M10.3124 2.62232C10.3124 2.74897 10.4103 2.85274 10.5346 2.86199M10.3124 2.62232L10.5346 2.86199M10.5527 2.38197C10.4918 2.38197 10.4361 2.40466 10.3937 2.44206M10.3937 2.44206L10.7117 2.80258M10.3937 2.44206C10.3663 2.46624 10.3445 2.49657 10.3304 2.53089M10.7117 2.80258C10.7402 2.77738 10.7628 2.7455 10.7768 2.7094M10.7117 2.80258C10.6837 2.82724 10.65 2.84551 10.6128 2.85509M10.4926 2.38197L10.7768 2.7094M10.6128 2.85509C10.5936 2.86003 10.5735 2.86266 10.5527 2.86266C10.5466 2.86266 10.5406 2.86243 10.5346 2.86199M10.6128 2.85509L10.3304 2.53089M17.1622 12.897C17.1622 14.0585 16.2206 15 15.0591 15C13.8977 15 12.9561 14.0585 12.9561 12.897C12.9561 11.7355 13.8977 10.794 15.0591 10.794C16.2206 10.794 17.1622 11.7355 17.1622 12.897ZM17.042 12.897C17.042 13.9921 16.1542 14.8798 15.0591 14.8798C13.9641 14.8798 13.0763 13.9921 13.0763 12.897C13.0763 11.8019 13.9641 10.9142 15.0591 10.9142C16.1542 10.9142 17.042 11.8019 17.042 12.897ZM10.9132 2.62232C10.9132 2.82142 10.7518 2.98283 10.5527 2.98283C10.3536 2.98283 10.1922 2.82142 10.1922 2.62232C10.1922 2.42321 10.3536 2.2618 10.5527 2.2618C10.7518 2.2618 10.9132 2.42321 10.9132 2.62232Z"
                            stroke="currentColor" stroke-width="0.808108" stroke-linecap="round" />
                    </svg>
                </h2>
                <p class="text-[#575757] text-justify">
                    Agrega el costo y las condiciones de envío a domicilio. Los clientes verán esta información al
                    realizar su pedido. <br>
                    Puedes establecer un monto mínimo de compra para que tus clientes obtengan envío gratis. ¡Una
                    excelente manera de incentivar compras mayores!
                </p>
            </div>
            <div class="lg:w-1/2 grid grid-cols-2 gap-3 items-center *:text-end mt-5 lg:mt-0">
                <p>Costo de envío:</p>
                <el-input v-model="onlinePropertiesForm.delivery_price" placeholder="Ej. 99"
                    @blur="storeOnlineProperties('delivery')" @keyup.enter="storeOnlineProperties('delivery')"
                    :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                    :parser="(value) => value.replace(/[^\d.]/g, '')">
                    <template #prefix>
                        <i class="fa-solid fa-dollar-sign"></i>
                    </template>
                </el-input>
                <p>Condiciones de envío:</p>
                <el-input v-model="onlinePropertiesForm.delivery_conditions" :autosize="{ minRows: 3, maxRows: 5 }"
                    @blur="storeOnlineProperties('delivery')" @keyup.enter="storeOnlineProperties('delivery')"
                    type="textarea" placeholder="Ej. Dentro de la zona metropolitana de Guadalajara." :maxlength="350"
                    show-word-limit clearable />
                <p>Compra mínimo para envió gratis:</p>
                <el-input v-model="onlinePropertiesForm.min_free_delivery" placeholder="Ej. 99"
                    @blur="storeOnlineProperties('delivery')" @keyup.enter="storeOnlineProperties('delivery')"
                    :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                    :parser="(value) => value.replace(/[^\d.]/g, '')">
                    <template #prefix>
                        <i class="fa-solid fa-dollar-sign"></i>
                    </template>
                </el-input>
                <p v-if="deliveryLoading" class="text-gray-400 text-end text-xs col-span-full">Guardando...</p>
            </div>
        </article>
        <!-- inventario ----------------------------------------->
        <article class="p-4 lg:flex items-center justify-between text-sm">
            <div class="lg:w-1/2">
                <h2 class="flex items-center space-x-1">
                    <span>Actualización automática de inventario</span>
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_16104_362" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                            width="17" height="17">
                            <rect width="17" height="17" fill="currentColor" />
                        </mask>
                        <g mask="url(#mask0_16104_362)">
                            <path
                                d="M6.3724 14.1673L5.38073 13.1757L6.62031 11.9007C5.1092 11.7 3.86372 11.2868 2.88385 10.6611C1.90399 10.0354 1.41406 9.31523 1.41406 8.50065C1.41406 7.52079 2.09583 6.68555 3.45937 5.99492C4.82292 5.3043 6.50226 4.95898 8.4974 4.95898C10.4925 4.95898 12.1719 5.3043 13.5354 5.99492C14.899 6.68555 15.5807 7.52079 15.5807 8.50065C15.5807 9.2326 15.1882 9.8878 14.4031 10.4663C13.6181 11.0447 12.5939 11.4757 11.3307 11.759V10.3069C12.2398 10.0708 12.9392 9.7786 13.4292 9.43034C13.9191 9.08207 14.1641 8.77218 14.1641 8.50065C14.1641 8.12287 13.6594 7.67426 12.65 7.15482C11.6406 6.63537 10.2564 6.37565 8.4974 6.37565C6.73837 6.37565 5.35417 6.63537 4.34479 7.15482C3.33542 7.67426 2.83073 8.12287 2.83073 8.50065C2.83073 8.78398 3.13177 9.12339 3.73385 9.51888C4.33594 9.91437 5.19184 10.2125 6.30156 10.4132L5.38073 9.49232L6.3724 8.50065L9.20573 11.334L6.3724 14.1673Z"
                                fill="black" />
                        </g>
                    </svg>
                </h2>
                <p class="text-[#575757] text-justify">
                    Activa la actualización automática del inventario. Cada vez que se realice una venta, el sistema
                    descontará automáticamente los productos vendidos de tu inventario.
                </p>
            </div>
            <div class="mt-3 lg:mt-0 w-24">
                <el-switch @change="storeOnlineProperties('inventory')" v-model="onlinePropertiesForm.inventory"
                    :loading="inventoryLoading" size="small" />
            </div>
        </article>
        <!-- produtos agotados -------------->
        <article class="p-4 lg:flex items-center justify-between text-sm">
            <div class="lg:w-1/2">
                <h2 class="flex items-center space-x-1">
                    <span>Bloquear ventas de productos agotados</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M13.5 5L13.0833 12.088C13.0609 12.47 12.8933 12.829 12.6148 13.0915C12.3363 13.354 11.968 13.5001 11.5853 13.5H4.41467C4.03198 13.5001 3.6637 13.354 3.38522 13.0915C3.10674 12.829 2.93912 12.47 2.91667 12.088L2.5 5M6.5 7.75L8 9.25M8 9.25L9.5 10.75M8 9.25L9.5 7.75M8 9.25L6.5 10.75M2.25 5H13.75C14.164 5 14.5 4.664 14.5 4.25V3.25C14.5 2.836 14.164 2.5 13.75 2.5H2.25C1.836 2.5 1.5 2.836 1.5 3.25V4.25C1.5 4.664 1.836 5 2.25 5Z"
                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </h2>
                <p class="text-[#575757] text-justify">
                    Habilita esta función para bloquear ventas de productos agotados. El sistema verificará el
                    inventario y
                    no permitirá pedidos de productos sin stock disponible.
                </p>
            </div>
            <div class="mt-3 lg:mt-0 w-24">
                <el-switch @change="storeOnlineProperties('soldout')" v-model="onlinePropertiesForm.sold_out_active"
                    :loading="soldOutLoading" size="small" />
            </div>
        </article>
    </section>
</template>

<script>
import InputFilePreview from '@/Components/MyComponents/InputFilePreview.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from "@inertiajs/vue3";
import banner1 from '@/../../public/images/banners/banner1.png';
import banner2 from '@/../../public/images/banners/banner2.png';
import banner3 from '@/../../public/images/banners/banner3.png';
import banner4 from '@/../../public/images/banners/banner4.png';

export default {
    data() {
        const logoForm = useForm({
            img: null,
        });

        const bannerForm = useForm({
            img: null,
            selected: this.$page.props.auth.user.store.online_store_properties.banner ?? 1, //banner por defecto
        });

        const onlinePropertiesForm = useForm({
            whatsapp: this.$page.props.auth.user.store.online_store_properties.whatsapp,
            delivery_price: this.$page.props.auth.user.store.online_store_properties.delivery_price,
            delivery_conditions: this.$page.props.auth.user.store.online_store_properties.delivery_conditions,
            min_free_delivery: this.$page.props.auth.user.store.online_store_properties.min_free_delivery,
            inventory: !!this.$page.props.auth.user.store.online_store_properties.inventory,
            sold_out_active: !!this.$page.props.auth.user.store.online_store_properties.sold_out_active,
        });

        return {
            //General
            logoForm,
            bannerForm,
            onlinePropertiesForm,
            //banners
            banners: [
                { src: banner1, alt: 'Banner por defecto #1' },
                { src: banner2, alt: 'Banner por defecto #2' },
                { src: banner3, alt: 'Banner por defecto #3' },
                { src: banner4, alt: 'Banner por defecto #4' }
            ],
            //cargas
            contactLoading: false,
            deliveryLoading: false,
            inventoryLoading: false,
            soldOutLoading: false,
        }
    },
    components: {
        PrimaryButton,
        InputFilePreview,
    },
    props: {
    },
    computed: {
        storeLogoUrl() {
            return this.$page.props.auth.user.store.media?.find(media => media.collection_name === 'logo')?.original_url;
        },
        storeBannerUrl() {
            return this.$page.props.auth.user.store.media?.find(media => media.collection_name === 'banner')?.original_url;
        },
    },
    methods: {
        storeLogo(img = null) {
            this.logoForm.img = img;
            this.logoForm.post(route("stores.store-logo"));
        },
        storeBanner(img = null) {
            this.bannerForm.img = img;
            if (img) {
                this.bannerForm.selected = 0;
            }
            this.bannerForm.post(route("stores.store-banner"));
        },
        storeOnlineProperties(configName) {
            const onlineProperties = this.$page.props.auth.user.store.online_store_properties;

            let formEdited = this.onlinePropertiesForm.delivery_price != onlineProperties.delivery_price ||
                this.onlinePropertiesForm.delivery_conditions != onlineProperties.delivery_conditions ||
                this.onlinePropertiesForm.min_free_delivery != onlineProperties.min_free_delivery ||
                this.onlinePropertiesForm.whatsapp != onlineProperties.whatsapp ||
                this.onlinePropertiesForm.inventory != onlineProperties.inventory ||
                this.onlinePropertiesForm.sold_out_active != onlineProperties.sold_out_active;

            if (formEdited) {
                if (configName == 'contact') {
                    this.contactLoading = true;
                } else if (configName == 'delivery') {
                    this.deliveryLoading = true;
                } else if (configName == 'inventory') {
                    this.inventoryLoading = true;
                } else if (configName == 'soldout') {
                    this.soldOutLoading = true;
                }

                this.onlinePropertiesForm.post(route("stores.store-online-properties"), {
                    onFinish: () => {
                        this.contactLoading = false;
                        this.deliveryLoading = false;
                        this.inventoryLoading = false;
                        this.soldOutLoading = false;
                    }
                });
            }
        },
    }
}
</script>
