<template>
    <section>
        <h1 class="mx-3 flex items-center justify-between">
            <span>Suscripción</span>
            <el-tag :type="$page.props.auth.user.store.is_active ? 'success' : 'danger'" effect="light">
                {{ $page.props.auth.user.store.is_active ? 'Activa' : 'Inactiva' }}
            </el-tag>
        </h1>
        <div class="rounded-[5px] border border-grayD9 px-4 py-4 mt-3 relative">
            <!-- etiqueta -->
            <p
                class="absolute top-2 left-0 py-1 px-4 rounded-e-full text-xs text-white bg-gradient-to-r from-[#2e19af] from-10% via-[#670ff7] via-40% to-[#853ff9] to-80%">
                Suscripción desde {{ formatDate($page.props.auth.user.store.created_at) }}
            </p>
            <article class="mt-6">
                <div class="flex items-start space-x-3 justify-between">
                    <div class="w-5/6">
                        <div v-if="!edit" class="grid grid-cols-2 gap-2">
                            <p class="flex flex-col">
                                <b>Suscripción <span class="lowercase">{{ $page.props.auth.user.store.suscription_period
                                        }}</span></b>
                                <span class="text-xs">
                                    Próxima factura: {{ formatDate($page.props.auth.user.store.next_payment) }}
                                </span>
                            </p>
                            <p class="flex flex-col">
                                <b>
                                    {{ $page.props.auth.user.store.suscription_period == 'Mensual' ? '$199.00' :
                                        '$1,990.00' }}
                                </b>
                            </p>
                        </div>
                        <div v-else class="flex flex-col space-y-3 col-span-full mt-3">
                            <div v-for="(item, index) in suscriptions" :key="index" class="flex">
                                <div class="flex items-center h-5">
                                    <input v-model="form.suscription_period" :id="'suscription-' + index"
                                        :aria-describedby="'suscription-text-' + index" type="radio" :value="item.name"
                                        class="size-3 text-primary border-gray-300 focus:ring-0">
                                </div>
                                <div class="ms-2 text-sm">
                                    <label :for="'suscription-' + index" class="font-medium text-gray-900">
                                        {{ item.name }}
                                    </label>
                                    <p :id="'suscription-text-' + index" class="text-xs font-normal text-gray-500">
                                        {{ item.description }}
                                    </p>
                                </div>
                                <p v-if="$page.props.auth.user.store.suscription_period == item.name"
                                    class="ml-4 text-xs text-green-600">Actual</p>
                            </div>
                        </div>
                    </div>
                    <div v-if="!loading">
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
                        <button @click="edit = true" v-else class="text-primary text-xs">Modificar</button>
                    </div>
                    <p v-else class="text-xs">
                        <i class="fa-solid fa-circle-notch fa-spin text-primary mr-px"></i>
                        Cargando...
                    </p>
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
import { useForm } from '@inertiajs/vue3';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import visaImage from "@/../../public/images/visa.jpg";
import mastercardImage from "@/../../public/images/mastercard.png";
import americanExpressImage from "@/../../public/images/american_express.png";

export default {
    data() {
        const form = useForm({
            suscription_period: this.$page.props.auth.user.store.suscription_period,
            default_card_id: this.$page.props.auth.user.store.default_card_id,
        });

        return {
            form,
            edit: false,
            loading: false,
            suscriptions: [
                {
                    name: "Mensual",
                    amount: "$199.00",
                    description: "Pagas $199.00 cada mes",
                },
                {
                    name: "Anual",
                    amount: "$1,990.00",
                    description: "Ahorra dos mensualidades en la suscripción anual. Pagas $165.83 cada mes",
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
    computed: {
        getDefultCard() {
            return this.cards.find(item => item.id == this.$page.props.auth.user.store.default_card_id);
        }
    },
    methods: {
        getInstitutionImage(institution) {
            return this.images[institution];
        },
        store() {
            this.loading = true;
            this.form.put(route('ezy-profile.update-suscription'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Correcto',
                        message: '',
                        type: 'success',
                    });
                    this.edit = false;
                    this.loading = false;
                },
            });
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
    }
}
</script>