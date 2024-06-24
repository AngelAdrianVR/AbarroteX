<template>
    <OnlineStoreLayout title="Cotizar servicio">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Cotizar</h1>
                <div class="mt-3">
                    <InputLabel value="Nombre*" class="ml-3 mb-1" />
                    <el-input v-model="form.name" placeholder="Escribe tu nombre" :maxlength="100" clearable />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Medio de contacto preferido" class="ml-3 mb-1" />
                    <el-select filterable v-model="form.favorite_comunication_via" clearable placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in comunicationVias" :key="item" :label="item"
                            :value="item" />
                    </el-select>
                    <InputError :message="form.errors.favorite_comunication_via" />
                </div>

                <div class="mt-3">
                    <InputLabel class="mb-1 ml-2" value="Teléfono*" />
                    <el-input v-model="form.phone"
                    :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                    :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                    placeholder="Escribe el número de teléfono" />
                    <InputError :message="form.errors.phone" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Dirección" class="ml-3 mb-1" />
                    <el-input v-model="form.address" placeholder="Agrega tu dirección" :maxlength="100" clearable />
                    <InputError :message="form.errors.address" />
                </div>

                <div class="mt-3 col-span-full">
                    <InputLabel value="Mensaje (opcional)" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.message" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                        placeholder="Cuéntanos lo que necesitas" :maxlength="255" show-word-limit
                        clearable />
                    <InputError :message="form.errors.message" />
                </div>

                <div class="col-span-2 text-right mt-5">
                    <PrimaryButton :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Enviar
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </OnlineStoreLayout>
</template>

<script>
import OnlineStoreLayout from '@/Layouts/OnlineStoreLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
        name: null,
        favorite_comunication_via: null,
        phone: null,
        address: null,
        message: null,
    });

    return {
       form,
       comunicationVias: [
        'Teléfono',
        'Correo electrónico',
        'WhatsApp',
       ],
    }
},
components:{
    OnlineStoreLayout,
    PrimaryButton,
    InputLabel,
    InputError,
    Back
},
props:{

},
methods:{
    store() {
        this.form.post(route("online-sales.store"), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha creado una nueva caja",
                    type: "success",
                });
            },
        });
    },
}
}
</script>
