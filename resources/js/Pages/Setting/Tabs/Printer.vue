<template>
    <section class="my-5 space-y-3 md:space-y-0 lg:grid grid-cols-2 gap-3">
        <!-- UUID SERVICE ----------------------------------------->
        <!-- --------------------------------------------------------- -->
        <article class="border border-grayD9 rounded-md p-4 relative">
        <i v-if="!editUUIDService" @click="editUUIDService = true" class="fa-solid fa-pen absolute top-3 right-3 text-xs text-primary cursor-pointer bg-gray-100 rounded-full py-1 px-[5px]"></i>
        <h2 class="font-bold">Código UUID Service<i class="fa-solid fa-print ml-3"></i></h2>
        <p class="text-sm text-gray99 mb-4">Puedes encontrarlo en las especificaciones del fabricante. Si no lo encuentras contactar a soporte de Ezy Ventas</p>
        <div class="flex items-center text-sm">

            <p class="mx-7">UUID Service:</p>
            <p v-if="!editUUIDService" class="mx-7">{{ form.printer_config?.UUIDService ?? '-' }}</p>

            <div v-else class="flex items-center space-x-2 mx-7">
                <el-input v-model="form.printer_config.UUIDService" maxlength="255" clearable placeholder="Escribe el UUID Service de tu impresora" />
                <button v-if="editUUIDService">
                    <i @click="editUUIDService = false" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
                </button>
                <button @click="editUUIDService = false;">
                    <i class="fa-solid fa-x text-xs text-gray-600 cursor-pointer bg-gray-100 rounded-full py-1 px-[7px]"></i>
                    </button>
            </div>
        </div>
        </article>

        <!-- UUID Characteristic ----------------------------------------->
        <!-- --------------------------------------------------------- -->
        <article class="border border-grayD9 rounded-md p-4 relative">
        <i v-if="!editUUIDCharacteristic" @click="editUUIDCharacteristic = true" class="fa-solid fa-pen absolute top-3 right-3 text-xs text-primary cursor-pointer bg-gray-100 rounded-full py-1 px-[5px]"></i>
        <h2 class="font-bold">Código UUID Characteristic<i class="fa-solid fa-print ml-3"></i></h2>
        <p class="text-sm text-gray99 mb-4">Puedes encontrarlo en las especificaciones del fabricante. Si no lo encuentras contactar a soporte de Ezy Ventas</p>
        <div class="flex items-center text-sm">

            <p class="mx-7">UUID Characteristic:</p>
            <p v-if="!editUUIDCharacteristic" class="mx-7">{{ form.printer_config?.UUIDCharacteristic ?? '-' }}</p>

            <div v-else class="flex items-center space-x-2 mx-7">
                <el-input v-model="form.printer_config.UUIDCharacteristic" maxlength="255" clearable placeholder="Escribe el UUID Characteristic de tu impresora" />
                <button v-if="editUUIDCharacteristic">
                    <i @click="editUUIDCharacteristic = false" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
                </button>
                <button @click="editUUIDCharacteristic = false;">
                    <i class="fa-solid fa-x text-xs text-gray-600 cursor-pointer bg-gray-100 rounded-full py-1 px-[7px]"></i>
                    </button>
            </div>
        </div>
        </article>

        <div class="text-right pt-10 md:mx-7 col-span-full">
            <PrimaryButton :disabled="form.processing" @click="updatePrinterConfig">
                <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                Guadar cambios
            </PrimaryButton>
        </div>
    </section>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from "@inertiajs/vue3";

export default {
data() {

    const form = useForm({
      printer_config: {
          UUIDService : this.$page.props.auth.user.store.printer_config?.UUIDService ?? null,
          UUIDCharacteristic : this.$page.props.auth.user.store.printer_config?.UUIDCharacteristic ?? null,
      }
    });

    return {
        form,
        editUUIDService: false, //edita el código UUID service
        editUUIDCharacteristic: false, //edita el código UUID service
    }
},
components:{
PrimaryButton
},
props:{
store: Object,
},
methods:{
    updatePrinterConfig() {
      this.form.put(route('stores.update-printer-config', this.$page.props.auth.user.store.id), {
          onSuccess: () => {
            this.$notify({
                title: "Correcto",
                message: "¡Configuraciones actualizadas!",
                type: "success",
            });
            this.editUUIDService = false;
            this.editUUIDCharacteristic = false;
          },
      });
    },
}
}
</script>
