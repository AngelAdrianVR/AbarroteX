<template>
    <AppLayout title="Perfil">
        <main class="mx-2 md:mx-56 my-10 text-sm">
            <section>
                <h1 class="ml-3">Información básica</h1>
                <div class="rounded-[5px] border border-grayD9 px-4 py-4 mt-3">
                    <article>
                        <h2>Información de la Tienda</h2>
                        <div class="flex items-center space-x-3 justify-between">
                            <div class="grid grid-cols-2 gap-2 mt-2 w-5/6">
                                <p class="flex flex-col">
                                    <b>Nombre de la Tienda </b>
                                    <el-input v-if="editBasicInfo" v-model="basicForm.store_name"
                                        placeholder="Nombre de tienda *" :disabled="loadingBasicInfo" maxlength="255" />
                                    <span v-else>{{ basicForm.store_name }}</span>
                                </p>
                                <p class="flex flex-col">
                                    <b>Dirección</b>
                                    <el-input v-if="editBasicInfo" v-model="basicForm.store_address"
                                        placeholder="Dirección de tienda" :disabled="loadingBasicInfo"
                                        maxlength="255" />
                                    <span v-else>{{ basicForm.store_address ?? '-' }}</span>
                                </p>
                                <h2 class="col-span-full">Información del contacto</h2>
                                <p class="flex flex-col">
                                    <b> Nombre</b>
                                    <el-input v-if="editBasicInfo" v-model="basicForm.name" placeholder="Nombre *"
                                        :disabled="loadingBasicInfo" maxlength="255" />
                                    <span v-else>{{ basicForm.name }}</span>
                                </p>
                                <p class="flex flex-col">
                                    <b>Teléfono</b>
                                    <el-input v-if="editBasicInfo" v-model="basicForm.contact_phone"
                                        placeholder="Teléfono" :disabled="loadingBasicInfo"
                                        :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                                        :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable />
                                    <span v-else>{{ basicForm.contact_phone ?? '-' }}</span>
                                </p>
                                <p class="flex flex-col">
                                    <b>Correo electrónico</b>
                                    <el-input v-if="editBasicInfo" v-model="basicForm.email" placeholder="Correo *"
                                        :disabled="loadingBasicInfo" maxlength="255" />
                                    <span v-else>{{ basicForm.email }}</span>
                                </p>
                            </div>
                            <div v-if="!loadingBasicInfo">
                                <div v-if="editBasicInfo"
                                    class="flex items-center space-x-1 *:size-5 *:rounded-full *:flex *:items-center *:justify-center *:border">
                                    <el-tooltip content="Cancelar" placement="left">
                                        <button @click="editBasicInfo = false; basicForm.reset()"
                                            class="text-gray-600 text-[9px] hover:bg-gray-100 transition-all duration-150"><i
                                                class="fa-solid fa-x"></i></button>
                                    </el-tooltip>
                                    <el-tooltip content="Guardar" placement="right">
                                        <button @click="storeBasicInfo"
                                            class="text-green-600 text-[9px] hover:bg-green-100 transition-all duration-150"><i
                                                class="fa-solid fa-check"></i></button>
                                    </el-tooltip>
                                </div>
                                <button @click="editBasicInfo = true" v-else
                                    class="text-primary text-xs">Modificar</button>
                            </div>
                            <p v-else class="text-xs">
                                <i class="fa-solid fa-circle-notch fa-spin text-primary mr-px"></i>
                                Cargando...
                            </p>
                        </div>
                    </article>
                </div>
            </section>
            <section class="mt-7">
                <h1 class="mx-3 flex items-center justify-between">
                    <span>Suscripción</span>
                    <el-tag :type="$page.props.auth.user.store.is_active ? 'success' : 'danger'" effect="light">
                        {{ $page.props.auth.user.store.is_active ? 'Activa' : 'Inactiva' }}
                    </el-tag>
                </h1>
                <div class="rounded-[5px] border border-grayD9 px-4 py-4 mt-3">
                    <article>
                        <div class="flex items-center space-x-3 justify-between border-b border-grayD9 pb-5">
                            <div class="grid grid-cols-2 gap-2 w-5/6">
                                <p class="flex flex-col">
                                    <b>Suscripción mensual</b>
                                    <span>Próxima factura: {{ formatDate($page.props.auth.user.store.next_payment)
                                        }}</span>
                                </p>
                                <p class="flex flex-col">
                                    <b>$269.00</b>
                                    <span></span>
                                </p>
                            </div>
                            <button class="text-primary text-xs">Modificar</button>
                        </div>
                    </article>
                    <article class="mt-5">
                        <div class="flex items-center space-x-3 justify-between">
                            <div class="grid grid-cols-2 gap-2 w-5/6">
                                <p class="flex flex-col">
                                    <b>Método de pago</b>
                                <div class="flex items-center space-x-2">
                                    <figure
                                        class="size-8 flex items-center justify-center rounded-full border border-grayD9">
                                        <img src="@/../../public/images/visa.jpg" class="size-6 rounded-full">
                                    </figure>
                                    <div class="flex flex-col text-xs">
                                        <span>Tarjeta de débito terminada en 0141</span>
                                        <span>BBVA</span>
                                    </div>
                                </div>
                                </p>
                            </div>
                            <button class="text-primary text-xs">Modificar</button>
                        </div>
                    </article>
                </div>
            </section>
            <section class="mt-7">
                <h1 class="mx-3">
                    <span>Mis tarjetas guardadas</span>
                </h1>
                <div class="rounded-[5px] border border-grayD9 px-4 py-4 mt-3">
                    <article>
                        <div class="flex items-center space-x-3 justify-between border-b border-grayD9 pb-5">
                            <div class="grid grid-cols-2 gap-2 w-5/6">
                                <div class="flex flex-col">
                                    <div class="flex items-center space-x-2">
                                        <figure
                                            class="size-8 flex items-center justify-center rounded-full border border-grayD9">
                                            <img src="@/../../public/images/visa.jpg" class="size-6 rounded-full">
                                        </figure>
                                        <div class="flex flex-col text-xs">
                                            <span>Tarjeta de débito terminada en 0141</span>
                                            <span>BBVA</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-center">
                                    <p class="text-[10px] leading-snug text-green-600">
                                        {{ defaultCard == 1 ? 'Predeterminado' : '' }}</p>
                                    <button @click="changeDefaultCard(1)"
                                        class="size-4 rounded-full border text-white flex items-center justify-center"
                                        :class="defaultCard == 1 ? 'border-green-600 bg-green-600' : 'border-grayD9'">
                                        <i class="fa-solid fa-check text-[8px]"></i>
                                    </button>
                                </div>
                            </div>
                            <button class="text-primary text-xs">Editar</button>
                        </div>
                    </article>
                    <article class="mt-4">
                        <div class="flex items-center space-x-3 justify-between">
                            <div class="grid grid-cols-2 gap-2 mt-2 w-5/6">
                                <div class="flex flex-col">
                                    <div class="flex items-center space-x-2">
                                        <figure
                                            class="size-8 flex items-center justify-center rounded-full border border-grayD9">
                                            <img src="@/../../public/images/mastercard.png" class="size-6 rounded-full">
                                        </figure>
                                        <div class="flex flex-col text-xs">
                                            <span>Tarjeta de débito terminada en 0177</span>
                                            <span>BANORTE</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-center">
                                    <p class="text-[10px] leading-snug text-green-600">
                                        {{ defaultCard == 2 ? 'Predeterminado' : '' }}</p>
                                    <button @click="changeDefaultCard(2)"
                                        class="size-4 rounded-full border text-white flex items-center justify-center"
                                        :class="defaultCard == 2 ? 'border-green-600 bg-green-600' : 'border-grayD9'">
                                        <i class="fa-solid fa-check text-[8px]"></i>
                                    </button>
                                </div>
                            </div>
                            <button class="text-primary text-xs">Editar</button>
                        </div>
                    </article>
                </div>
            </section>
            <section>
                <div @click="$inertia.visit(route('cards.create'))"
                    class="rounded-[5px] border border-grayD9 px-4 py-4 mt-2 cursor-pointer">
                    <article>
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
                    </article>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import { useForm } from "@inertiajs/vue3";
import axios from 'axios';

export default {
    data() {
        const basicForm = useForm({
            store_name: this.$page.props.auth.user.store.name,
            store_address: this.$page.props.auth.user.store.address,
            name: this.$page.props.auth.user.name,
            contact_phone: this.$page.props.auth.user.store.contact_phone,
            email: this.$page.props.auth.user.email,
        });

        return {
            basicForm,
            editBasicInfo: false,
            loadingBasicInfo: false,
            defaultCard: 1,
        }
    },
    components: {
        AppLayout,
    },
    props: {
        user: Object,
    },
    methods: {
        async storeBasicInfo() {
            try {
                this.loadingBasicInfo = true;
                const response = await axios.put(route('ezy-profile.update-basic'), {
                    store_name: this.basicForm.store_name,
                    store_address: this.basicForm.store_address,
                    name: this.basicForm.name,
                    contact_phone: this.basicForm.contact_phone,
                    email: this.basicForm.email,
                });

                if (response.status === 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: '',
                        type: 'success',
                    });
                    this.editBasicInfo = false;
                    console.log(response)
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loadingBasicInfo = false;
            }
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        changeDefaultCard(cardId) {
            this.defaultCard = cardId;
        },
    }
}
</script>