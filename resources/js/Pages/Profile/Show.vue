<template>
    <AppLayout title="Perfil">
        <main class="mx-2 md:mx-56 my-10 text-sm">
            <section>
                <h1 class="ml-3">Información básica</h1>
                <div class="rounded-[5px] border border-grayD9 px-4 py-4 mt-3">
                    <article>
                        <h2>Información de la Tienda</h2>
                        <div class="flex items-center space-x-3 justify-between border-b border-grayD9 pb-5">
                            <div class="grid grid-cols-2 gap-2 mt-2 w-5/6">
                                <p class="flex flex-col">
                                    <b>Nombre de la Tienda </b>
                                    <span>{{ $page.props.auth.user.store.name }}</span>
                                </p>
                                <p class="flex flex-col">
                                    <b>Dirección</b>
                                    <span>{{ $page.props.auth.user.store.address ?? '-' }}</span>
                                </p>
                            </div>
                            <button class="text-primary text-xs">Modificar</button>
                        </div>
                    </article>
                    <article class="mt-5">
                        <h2>Información del contacto</h2>
                        <div class="flex items-center space-x-3 justify-between">
                            <div class="grid grid-cols-2 gap-2 mt-2 w-5/6">
                                <p class="flex flex-col">
                                    <b> Nombre</b>
                                    <span>{{ $page.props.auth.user.name }}</span>
                                </p>
                                <p class="flex flex-col">
                                    <b>Teléfono</b>
                                    <span>{{ $page.props.auth.user.store.contact_phone ?? '-' }}</span>
                                </p>
                                <p class="flex flex-col">
                                    <b>Correo electrónico</b>
                                    <span>{{ $page.props.auth.user.email }}</span>
                                </p>
                            </div>
                            <button class="text-primary text-xs">Modificar</button>
                        </div>
                    </article>
                </div>
            </section>
            <section class="mt-7">
                <h1 class="mx-3 flex items-center justify-between">
                    <span>Suscripción</span>
                    <el-tag type="success" effect="light">
                        {{ 'Activa' }}
                    </el-tag>
                </h1>
                <div class="rounded-[5px] border border-grayD9 px-4 py-4 mt-3">
                    <article>
                        <div class="flex items-center space-x-3 justify-between border-b border-grayD9 pb-5">
                            <div class="grid grid-cols-2 gap-2 mt-2 w-5/6">
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
                        <div class="flex items-center space-x-3 justify-between pb-5">
                            <div class="grid grid-cols-2 gap-2 mt-2 w-5/6">
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
                            <div class="grid grid-cols-2 gap-2 mt-2 w-5/6">
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
                        <div class="flex items-center space-x-3 justify-between pb-5">
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
        </main>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    data() {
        return {
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
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        changeDefaultCard(cardId) {
            this.defaultCard = cardId;
        },
    }
}
</script>