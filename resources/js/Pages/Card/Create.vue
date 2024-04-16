<template>
    <AppLayout title="Nueva tarjeta">
        <div class="mx-2 md:mx-56 my-10 p-5 text-sm border border-grayD9 rounded-[5px]">
            <header>
                <h1 class="font-bold">Agregar tarjeta de crédito o débito</h1>
            </header>
            <main class="mt-4">
                <form @submit.prevent="store">
                    <div>
                        <InputLabel value="Número de tarjeta"/>
                        <el-input v-model="form.number" placeholder="Agregar el número de tarjeta" />
                        <InputError :message="form.errors.number"/>
                    </div>
                    <div class="mt-2">
                        <InputLabel value="Nombre del titular"/>
                        <el-input v-model="form.owner_name" placeholder="Nombre de quien esta la tarjeta" />
                        <InputError :message="form.errors.owner_name"/>
                    </div>
                    <div class="mt-2">
                        <InputLabel value="Vencimiento"/>
                        <el-input v-model="form.expire_date" placeholder="MM/AA" />
                        <InputError :message="form.errors.expire_date"/>
                    </div>
                    <div class="mt-2">
                        <InputLabel value="Código de seguridad "/>
                        <el-input v-model="form.cvv" placeholder="CVV" />
                        <InputError :message="form.errors.cvv"/>
                    </div>
                </form>
            </main>
        </div>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import { useForm } from "@inertiajs/vue3";

export default {
    data() {
        const form = useForm({
            number: null,
            owner_name: null,
            expire_date: null,
            cvv: null,
        });

        return {
            form,
            defaultCard: 1,
        }
    },
    components: {
        AppLayout,
        InputLabel,
        InputError,
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