<template>
    <AppLayout title="Reportar problema">
        <Back class="mx-2 md:mx-20 mt-5" />
        <div class="mx-2 md:mx-56 my-10 p-5 text-sm border border-grayD9 rounded-[5px]">
            <header>
                <h1 class="font-bold">Reportar problema </h1>
            </header>
            <main class="mt-4">
                <form @submit.prevent="store">
                    <div>
                        <InputLabel value="Tipo de problema *" />
                        <el-select class="w-full" v-model="form.type" placeholder="Seleccionar una opción"
                            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="item in options" :key="item" :label="item" :value="item" />
                        </el-select>
                        <InputError :message="form.errors.type" />
                    </div>
                    <div class="mt-2">
                        <InputLabel value="Descripción del problema *" />
                        <el-input v-model="form.description" :autosize="{ minRows: 3, maxRows: 6 }"
                            type="textarea" placeholder="Escribe el problema" />
                        <InputError :message="form.errors.description" />
                    </div>
                    <div class="mt-2">
                        <FileUploader @files-selected="this.form.media = $event" />
                    </div>
                    <div class="mt-4">
                        <el-checkbox v-model="form.first_report" label="Es la primera vez que encuentro o tengo este problema" size="small" />
                    </div>
                    <div class="flex justify-end mt-2">
                        <PrimaryButton :disabled="form.processing">Enviar a soporte técnico</PrimaryButton>
                    </div>
                </form>
            </main>
        </div>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import FileUploader from '@/Components/MyComponents/FileUploader.vue';
import Back from '@/Components/MyComponents/Back.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';

export default {
    data() {
        const form = useForm({
            type: null,
            description: null,
            first_report: true,
            media: [],
        });

        return {
            form,
            options: [
                'Problemas al registrar ventas ',
                'Problemas con el catalogo base ',
                'Problemas con inventario o productos ',
                'Problemas al pagar suscripción',
                'Configuración del sistema ',
                'Otro tipo de problema ',
            ],
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        InputLabel,
        InputError,
        FileUploader,
        Back,
    },
    props: {

    },
    methods: {
        store() {
            this.form.post(route('support-reports.store'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Gracias por tu reporte, se te contactará lo más pronto posible para revisar tu caso',
                        type: 'success',
                    });
                },
            });
        }
    }
}
</script>