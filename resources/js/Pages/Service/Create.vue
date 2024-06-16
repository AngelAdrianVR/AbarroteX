<template>
    <AppLayout title="Nuevo servicio">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Crear servicio</h1>
                <div class="mt-3">
                    <InputLabel value="Nombre del servicio*" class="ml-3 mb-1" />
                    <el-input v-model="form.name" placeholder="Escribe el nombre del servicio" :maxlength="100" clearable />
                    <InputError :message="form.errors.name" />
                </div>
                
                <div class="mt-3">
                    <InputLabel value="Categoría (opcional)" class="ml-3 mb-1" />
                    <el-input v-model="form.category" placeholder="Ej. Mantenimieno, soporte, Instalación" :maxlength="100" clearable />
                    <InputError :message="form.errors.category" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Precio del servicio (opcional)" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.price" placeholder="ingresa el precio del servicio"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')">
                        <template #prefix>
                            <i class="fa-solid fa-dollar-sign"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.price" />
                </div>

                <div class="mt-3 col-span-full">
                    <InputLabel value="Descripción del servicio (opcional)" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.description" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                        placeholder="Agrega una descripción a tu servicio" :maxlength="800" show-word-limit
                        clearable />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="col-span-2 text-right mt-5">
                    <PrimaryButton :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Crear servicio
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
        name: null,
        category: null,
        description: null,
        price: null,
    });

    return {
        form,
    }
},
components:{
AppLayout,
PrimaryButton,
InputLabel,
InputError,
Back
},
props:{
},
methods:{
    store() {
        this.form.post(route("services.store"), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha creado un nuevo servicio",
                    type: "success",
                });
            },
        });
    },
}
}
</script>
