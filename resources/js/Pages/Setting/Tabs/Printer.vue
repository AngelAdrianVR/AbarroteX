<template>
    <section class="my-5">
        <article class="border border-grayD9 rounded-t-md p-4">
            <div class="flex items-center space-x-2">
                <span class="text-base font-bold">Conexión por bluetooth</span>
                <div class="bg-[#CCD8FD] rounded-full py-1 inline-block">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_15673_25" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="14" height="14">
                            <rect width="14" height="14" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_15673_25)">
                            <path d="M6.41406 12.8327V8.39935L3.73073 11.0827L2.91406 10.266L6.18073 6.99935L2.91406 3.73268L3.73073 2.91602L6.41406 5.59935V1.16602H6.9974L10.3224 4.49102L7.81406 6.99935L10.3224 9.50768L6.9974 12.8327H6.41406ZM7.58073 5.59935L8.68906 4.49102L7.58073 3.41185V5.59935ZM7.58073 10.5868L8.68906 9.50768L7.58073 8.39935V10.5868Z" fill="#082FAF"/>
                        </g>
                    </svg>
                </div>
            </div>
            <p class="text-[#575757] text-sm mt-2">Para poder conectar tu impresora de tickets a tu sistema de punto de venta de manera inalámbrica, asegúrate de encender el Bluetooth en el dispositivo desde el que deseas realizar la conexión (ya sea una computadora o un teléfono móvil). Esto permitirá que el dispositivo detecte la impresora y puedas establecer la conexión correctamente utilizando el Código UUID de servicio y el Código UUID characteristic.</p>
        </article>
        <!-- UUID SERVICE ----------------------------------------->
        <!-- --------------------------------------------------------- -->
        <article class="border border-grayD9 p-4 relative">
        <i v-if="!editUUIDService" @click="editUUIDService = true" class="fa-solid fa-pen absolute top-3 right-3 text-xs text-primary cursor-pointer bg-gray-100 rounded-full py-1 px-[5px]"></i>
        <h2 class="font-bold">Código UUID Service</h2>
        <p class="text-sm text-gray99 mb-4">Puedes encontrarlo en las especificaciones del fabricante. Si no lo encuentras contactar a soporte de Ezy Ventas</p>
        <div class="flex items-center text-sm">

            <p class="mx-7">UUID Service:</p>
            <p v-if="!editUUIDService" class="mx-7">{{ form.printer_config?.UUIDService ?? '-' }}</p>

            <div v-else class="flex items-center space-x-2 mx-7">
                <el-input v-model="form.printer_config.UUIDService" maxlength="255" clearable placeholder="Escribe el UUID Service de tu impresora" />
                <button v-if="editUUIDService">
                    <i @click="updatePrinterConfig(); editUUIDService = false" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
                </button>
                <button @click="editUUIDService = false;">
                    <i class="fa-solid fa-x text-xs text-gray-600 cursor-pointer bg-gray-100 rounded-full py-1 px-[7px]"></i>
                    </button>
            </div>
        </div>
        </article>

        <!-- UUID Characteristic ----------------------------------------->
        <!-- --------------------------------------------------------- -->
        <article class="border border-grayD9 p-4 relative">
        <i v-if="!editUUIDCharacteristic" @click="editUUIDCharacteristic = true" class="fa-solid fa-pen absolute top-3 right-3 text-xs text-primary cursor-pointer bg-gray-100 rounded-full py-1 px-[5px]"></i>
        <h2 class="font-bold">Código UUID Characteristic</h2>
        <p class="text-sm text-gray99 mb-4">Puedes encontrarlo en las especificaciones del fabricante. Si no lo encuentras contactar a soporte de Ezy Ventas</p>
        <div class="flex items-center text-sm">

            <p class="mx-7">UUID Characteristic:</p>
            <p v-if="!editUUIDCharacteristic" class="mx-7">{{ form.printer_config?.UUIDCharacteristic ?? '-' }}</p>

            <div v-else class="flex items-center space-x-2 mx-7">
                <el-input v-model="form.printer_config.UUIDCharacteristic" maxlength="255" clearable placeholder="Escribe el UUID Characteristic de tu impresora" />
                <button v-if="editUUIDCharacteristic">
                    <i @click="updatePrinterConfig(); editUUIDCharacteristic = false" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
                </button>
                <button @click="editUUIDCharacteristic = false;">
                    <i class="fa-solid fa-x text-xs text-gray-600 cursor-pointer bg-gray-100 rounded-full py-1 px-[7px]"></i>
                    </button>
            </div>
        </div>
        </article>

        <!-- ACTIVAR IMPRESIÓN AUTOMÁTICA EN PUNTO DE VENTA-------------->
        <!-- --------------------------------------------------------- -->
        <article class="border border-grayD9 rounded-b-md p-4 relative">
        <h2 class="font-bold">Impresión automática</h2>
        <p class="text-sm text-gray99 mb-4">Imprime el ticket de cada venta realizada automáticamente</p>
        <div class="flex items-center text-sm">

            <p class="mx-7">Impresión automática:</p>
            <el-switch v-model="form.printer_config.automaticPrinting" @change="updatePrinterConfig();" class="ml-4" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
        </div>
        </article>

        <!-- <div class="text-right pt-10 md:mx-7 col-span-full">
            <PrimaryButton :disabled="form.processing" @click="updatePrinterConfig">
                <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                Guadar cambios
            </PrimaryButton>
        </div> -->
    </section>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from "@inertiajs/vue3";

export default {
data() {

    const form = useForm({
      printer_config: {
          UUIDService : this.$page.props.auth.user.printer_config?.UUIDService ?? null,
          UUIDCharacteristic : this.$page.props.auth.user.printer_config?.UUIDCharacteristic ?? null,
          automaticPrinting : !! this.$page.props.auth.user.printer_config?.automaticPrinting,
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
      this.form.put(route('users.update-printer-config', this.$page.props.auth.user.id), {
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
