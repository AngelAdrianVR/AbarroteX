<template>
    <section>
        <h1 class="mx-3 flex items-center justify-between">
            <span>Suscripción</span>
            <el-tag :type="$page.props.auth.user.store.is_active ? 'success' : 'danger'" effect="light">
                {{ $page.props.auth.user.store.is_active ? 'Activa' : 'Inactiva' }}
            </el-tag>
        </h1>
        <div class="rounded-[5px] border border-grayD9 px-4 py-4 mt-3 relative">
            <p
                class="absolute top-2 left-0 py-1 px-4 rounded-e-full text-xs text-white bg-gradient-to-r from-[#2e19af] from-10% via-[#670ff7] via-40% to-[#853ff9] to-80%">
                Suscrito desde {{ formatDate($page.props.auth.user.store.created_at) }}
            </p>
            <article class="mt-6">
                <div class="">
                    <div class="">
                        <div v-if="!edit" class="grid grid-cols-3 gap-2">
                            <p class="flex flex-col">
                                <b>Suscripción: <span>{{ $page.props.auth.user.store.suscription_period
                                        }}</span></b>
                                <span class="text-xs text-gray99">
                                    Próximo pago: {{ formatDate($page.props.auth.user.store.next_payment) }}
                                </span>
                            </p>
                            <p class="flex flex-col">
                                <b>$ {{ getSuscriptionAmount().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</b>
                            </p>
                            <button v-if="!$page.props.auth.user.store.last_payment" @click="edit = true"
                                class="text-primary text-xs justify-self-end">
                                Pagar suscripción
                            </button>
                            <el-tooltip v-else placement="top">
                                <template #content>
                                    <p v-if="$page.props.auth.user.store.last_payment.status === 'En revisión'">Tu
                                        comprobante de pago está siendo validado y se <br>
                                        completará dentro de un plazo máximo de 24 horas.</p>
                                    <p v-else-if="$page.props.auth.user.store.last_payment.status === 'Aprobado'">Tu
                                        pago ha sido aprobado. Disfruta de tu
                                        suscripción</p>
                                    <p v-else>
                                        <b>Tu pago ha sido rechazado por el siguiente motivo:</b>
                                        <br> {{ $page.props.auth.user.store.last_payment.rejected_reason }}
                                        <br> <button @click="prepareReUpload()" class="text-primary underline">
                                            Click aqui para subir otro comprobante</button>
                                    </p>
                                </template>
                                <p class="flex items-center space-x-2 text-xs justify-self-end rounded-full px-3"
                                    :class="getStatusStyles()">
                                    <span v-html="getStatusIcon()"></span>
                                    {{ $page.props.auth.user.store.last_payment.status }}
                                </p>
                            </el-tooltip>
                        </div>
                        <div v-else class="flex flex-col space-y-3 col-span-full mt-7">
                            <button @click="edit = false" type="button" class="my-px text-[9px] self-start">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <h2 class="font-bold my-4">Selecciona la suscripción que deseas obtener</h2>
                            <div v-for="(item, index) in suscriptions" :key="index"
                                class="relative pb-2 border-b border-grayD9">
                                <!-- mensaje de mejor opcion -->
                                <div v-if="item.name == 'Anual'" class="bg-primarylight absolute top-0 -left-4 w-48">
                                    <div class="relative">
                                        <p class="text-primary font-bold text-center">Mejor opción</p>
                                        <i
                                            class="fa-solid fa-play text-lg text-white absolute right-0 -bottom-1 rotate-180"></i>
                                    </div>
                                </div>
                                <div :class="item.name == 'Anual' ? 'mt-7' : ''" class="flex">
                                    <div class="flex items-center h-5">
                                        <input v-model="form.suscription_period"
                                            @change="handleChangeSuscriptionPeriod()" :id="'suscription-' + index"
                                            :aria-describedby="'suscription-text-' + index" type="radio"
                                            :value="item.name" class="size-3 text-primary border-gray-300 focus:ring-0">
                                    </div>
                                    <div class="ms-2 text-sm text-gray37 w-full">
                                        <div class="grid grid-cols-2">
                                            <label :for="'suscription-' + index" class="font-medium">
                                                {{ item.title }}
                                            </label>
                                            <label :for="'suscription-' + index" class="font-bold">
                                                $ {{ item.amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                            </label>
                                        </div>
                                        <p :id="'suscription-text-' + index" class="text-xs font-normal text-gray99">
                                            {{ item.description }}
                                        </p>
                                    </div>
                                </div>
                                <p v-if="$page.props.auth.user.store.suscription_period == item.title"
                                    class="ml-4 text-xs text-green-600">
                                    Actual</p>
                            </div>

                            <div v-if="form.suscription_period" class="col-span-full pl-5 pt-7 pb-3 rounded-lg">
                                <p>Para completar el pago de tu suscripción, por favor, deposita o transfiere el dinero
                                    a los siguientes datos bancarios:</p>

                                <div class="flex">
                                    <div class="w-44 pt-5 space-y-1">
                                        <p>Número de cuenta:</p>
                                        <p>Clabe interbancaria:</p>
                                        <p>Nombre de beneficiario:</p>
                                        <p>Banco:</p>
                                        <p>Monto de pago:</p>
                                    </div>
                                    <div class="w-44 pt-5 space-y-1">
                                        <p>537286726492</p>
                                        <p>678679678665342245</p>
                                        <p>Miguel O. Vazquez</p>
                                        <p>Nu México</p>
                                        <p class="font-bold">{{ amountToPay }}</p>
                                    </div>
                                </div>
                                <p class="pt-5">A continuación, ingresa una foto del comprobante de pago, es importante
                                    que se muestren todos los datos.</p>

                                <a v-if="this.$page.props.auth.user.store.last_payment"
                                    :href="this.$page.props.auth.user.store.last_payment.media[0].original_url"
                                    target="_blank" class="underline text-primary">Ver comprobante anterior</a>
                                <div class="my-2">
                                    <InputFilePreview @imagen="saveImage" @cleared="form.image = null" />
                                </div>
                                <p class="text-gray99 text-xs">Tu información será validada en un plazo de 24 horas. Una
                                    vez
                                    confirmada, podrás continuar disfrutando de tu suscripción. Puedes verificar el
                                    estado de tu suscripción en cualquier momento.</p>

                                <div class="flex items-center justify-end space-x-8 mx-4 mt-2">
                                    <button @click="edit = false" type="button" class="text-primary">Ahora no</button>
                                    <PrimaryButton @click="storePayment()" id="btn-bottom"
                                        :disabled="!form.image || form.processing">
                                        Enviar comprobante de pago</PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div v-if="!loading">
                        <div v-if="edit"
                            class="flex items-center space-x-1 *:size-5 *:rounded-full *:flex *:items-center *:justify-center *:border">
                            <el-tooltip content="Cancelar" placement="left">
                                <button @click="edit = false; form.reset()"
                                    class="text-gray-600 text-[9px] hover:bg-gray-100 transition-all duration-150"><i
                                        class="fa-solid fa-x"></i></button>
                            </el-tooltip>
                            <el-tooltip content="Guardar" placement="right">
                                <button @click="store"
                                    class="text-green-600 text-[9px] hover:bg-green-100 transition-all duration-150"><i
                                        class="fa-solid fa-check"></i></button>
                            </el-tooltip>
                        </div>
                        <button @click="edit = true" v-else class="text-primary text-xs">Adquirir más días</button>
                    </div>
                    <p v-else class="text-xs">
                        <i class="fa-solid fa-circle-notch fa-spin text-primary mr-px"></i>
                        Cargando...
                    </p> -->
                </div>
                <!-- <div class="grid grid-cols-2 gap-2 mt-4">
                    <div v-if="!edit" class="flex flex-col space-y-1">
                        <b>Método de pago</b>
                        <div class="flex items-center space-x-2">
                            <figure class="size-8 flex items-center justify-center rounded-full border border-grayD9">
                                <img :src="getInstitutionImage(getDefultCard.institution)" class="size-6 rounded-full">
                            </figure>
                            <div class="flex flex-col text-xs">
                                <span>{{ getDefultCard.name }}</span>
                                <span>{{ getDefultCard.bank }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex flex-col space-y-3 col-span-full mt-3">
                        <h1 class="mx-3">
                            <span>Mis tarjetas guardadas</span>
                        </h1>
                        <div v-for="(item, index) in cards" :key="index" class="flex mt-2">
                            <div class="flex items-center">
                                <input v-model="form.default_card_id" :id="'card-' + index"
                                    :aria-describedby="'card-text-' + index" type="radio" :value="item.id"
                                    class="size-3 text-primary border-gray-300 focus:ring-0">
                            </div>
                            <div class="ms-2 text-sm w-full">
                                <label :for="'card-' + index" class="font-medium text-gray-900">
                                    <div class="flex items-center space-x-3 justify-between">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="flex flex-col">
                                                <div class="flex items-center space-x-2">
                                                    <figure
                                                        class="size-8 flex items-center justify-center rounded-full border border-grayD9">
                                                        <img :src="getInstitutionImage(item.institution)"
                                                            class="size-6 rounded-full">
                                                    </figure>
                                                    <div class="flex flex-col text-xs">
                                                        <span>{{ item.name }}</span>
                                                        <span>{{ item.bank }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <p v-if="$page.props.auth.user.store.default_card_id == item.id"
                                                class="ml-4 text-xs text-green-600">Predeterminado</p>
                                        </div>
                                        <button class="text-primary text-xs">Editar</button>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div @click="$inertia.visit(route('cards.create'))"
                            class="rounded-[5px] border border-grayD9 px-5 py-2 mt-2 cursor-pointer">
                            <div class="flex items-center space-x-3 justify-between">
                                <div class="grid grid-cols-2 gap-2 w-5/6">
                                    <div class="flex flex-col">
                                        <div class="flex items-center space-x-2">
                                            <figure
                                                class="size-8 flex items-center justify-center rounded-full border border-grayD9">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <mask id="mask0_8939_78" style="mask-type:alpha"
                                                        maskUnits="userSpaceOnUse" x="0" y="0" width="14" height="14">
                                                        <rect width="14" height="14" fill="#D9D9D9" />
                                                    </mask>
                                                    <g mask="url(#mask0_8939_78)">
                                                        <path
                                                            d="M2.33464 11.6673C2.0138 11.6673 1.73915 11.5531 1.51068 11.3246C1.2822 11.0961 1.16797 10.8215 1.16797 10.5007V3.50065C1.16797 3.17982 1.2822 2.90516 1.51068 2.67669C1.73915 2.44822 2.0138 2.33398 2.33464 2.33398H11.668C11.9888 2.33398 12.2635 2.44822 12.4919 2.67669C12.7204 2.90516 12.8346 3.17982 12.8346 3.50065V7.00065H2.33464V10.5007H8.16797V11.6673H2.33464ZM2.33464 4.66732H11.668V3.50065H2.33464V4.66732ZM11.0846 12.834V11.084H9.33463V9.91732H11.0846V8.16732H12.2513V9.91732H14.0013V11.084H12.2513V12.834H11.0846Z"
                                                            fill="#999999" />
                                                    </g>
                                                </svg>
                                            </figure>
                                            <div class="flex flex-col text-xs">
                                                <span>Nueva tarjeta</span>
                                                <span>Débito o crédito</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="text-primary text-xs"><i class="fa-solid fa-chevron-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div> -->
            </article>
        </div>
    </section>
</template>
<script>
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import visaImage from "@/../../public/images/visa.jpg";
import mastercardImage from "@/../../public/images/mastercard.png";
import americanExpressImage from "@/../../public/images/american_express.png";

export default {
    data() {
        const form = useForm({
            amount: null,
            suscription_period: null,
            days: null,
            days_gifted: null,
            image: null,
            // default_card_id: this.$page.props.auth.user.store.default_card_id,
        });

        return {
            form,
            edit: false,
            loading: false,
            suscriptions: [
                {
                    name: "Mensual",
                    title: "Mensual (30 días)",
                    amount: 199.00,
                    description: "Pagas $199.00 cada mes",
                    days: 30,
                    daysGifted: 0,
                },
                {
                    name: "Trimestral",
                    title: "Trimestral (3 meses) + 5 días gratis",
                    amount: 597.00,
                    description: "Pagas $192.58 cada mes",
                    days: 90,
                    daysGifted: 5,
                },
                {
                    name: "Semestral",
                    title: "Semestral (6 meses) + 14 días gratis",
                    amount: 1494.00,
                    description: "Pagas $184.64 cada mes",
                    days: 180,
                    daysGifted: 14,
                },
                {
                    name: "Anual",
                    title: "Anual (12 meses) + 65 días gratis",
                    amount: 2421.00,
                    description: "Pagas $157.89 cada mes",
                    days: 365,
                    daysGifted: 65,
                },
            ],
            cards: [
                {
                    id: 1,
                    name: "Tarjeta de débito terminada en 0141",
                    bank: "BBVA",
                    institution: 'visa',
                },
                {
                    id: 2,
                    name: "Tarjeta de crédito terminada en 1187",
                    bank: "BANORTE",
                    institution: 'mastercard',
                },
                {
                    id: 3,
                    name: "Tarjeta de crédito terminada en 1780",
                    bank: "NU",
                    institution: 'american_express',
                },
            ],
            images: {
                visa: visaImage,
                mastercard: mastercardImage,
                american_express: americanExpressImage,
            },
        };
    },
    props: {
        user: Object,
    },
    components: {
        PrimaryButton,
        InputFilePreview
    },
    computed: {
        getDefultCard() {
            return this.cards.find(item => item.id == this.$page.props.auth.user.store.default_card_id);
        },
        amountToPay() {
            if (this.form.suscription_period == 'Mensual') {
                return '$199.00';
            } else if (this.form.suscription_period == 'Trimestral') {
                return '$597.00';
            } else if (this.form.suscription_period == 'Semestral') {
                return '$1,494.00';
            } else if (this.form.suscription_period == 'Anual') {
                return '$2,421.00';
            }
        }
    },
    methods: {
        prepareReUpload() {
            const lastPayment = this.$page.props.auth.user.store.last_payment;
            this.form.suscription_period = lastPayment.suscription_period;
            this.form.amount = lastPayment.amount;
            this.form.days = 0;
            this.form.days_gifted = 0;
            this.edit = true;

            this.$nextTick(() => {
                this.scrollToBottom();
            });
        },
        getStatusStyles() {
            const paymentStatus = this.$page.props.auth.user.store.last_payment.status;

            if (paymentStatus == 'En revisión') {
                return 'text-amber-600';
            } else if (paymentStatus == 'Aprobado') {
                return 'text-green-600';
            } else {
                return 'text-red-600';
            }
        },
        getStatusIcon() {
            const paymentStatus = this.$page.props.auth.user.store.last_payment.status;

            if (paymentStatus == 'En revisión') {
                return `
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                `;
            } else if (paymentStatus == 'Aprobado') {
                return `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                `;
            } else {
                return `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
                `;
            }
        },
        handleChangeSuscriptionPeriod() {
            const suscription = this.suscriptions.find(item => item.name == this.form.suscription_period);
            this.form.amount = suscription.amount;
            this.form.days = suscription.days;
            this.form.days_gifted = suscription.daysGifted;

            this.scrollToBottom();
        },
        scrollToBottom() {
            const btnBottom = document.getElementById('btn-bottom');
            btnBottom.scrollIntoView({ behavior: 'smooth' });
        },
        getSuscriptionAmount() {
            const currentSuscription = this.$page.props.auth.user.store.suscription_period;
            if (currentSuscription == 'Periodo de prueba') {
                return 0.00;
            } else {
                return this.suscriptions.find(item => item.name == currentSuscription).amount;
            }
        },
        getInstitutionImage(institution) {
            return this.images[institution];
        },
        storePayment() {
            this.loading = true;
            this.form.post(route('payments.store'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Comprobante enviado',
                        message: '',
                        type: 'success',
                    });
                    this.edit = false;
                    this.loading = false;
                },
            });
        },
        // store() {
        //     this.loading = true;
        //     this.form.put(route('ezy-profile.update-suscription'), {
        //         onSuccess: () => {
        //             this.$notify({
        //                 title: 'Correcto',
        //                 message: '',
        //                 type: 'success',
        //             });
        //             this.edit = false;
        //             this.loading = false;
        //         },
        //     });
        // },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        saveImage(image) {
            this.form.image = image;
        },
    }
}
</script>