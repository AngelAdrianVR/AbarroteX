<template>
    <AppLayout title="Editar servicio">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="update"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Editar servicio</h1>
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
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\D/g, '')">
                        <template #prefix>
                            <i class="fa-solid fa-dollar-sign"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.price" />
                </div>

                <!-- Está adaptado para seleccionar varias imagenes pero falta la lógica de edición (se eliminan todas si se quiere cambiar una). Se dejó en una sola imagen -->
                <div class="col-span-full w-full overflow-auto flex items-end">
                    <InputFilePreview v-show="index < 1" v-for="(file, index) in form.media" :key="index" :canDelete="index == (form.media.length - 2)"
                        @imagen="saveImage" @cleared="handleCleared(index)" :imageUrl="service?.media[index]?.original_url" class="p-2" />
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
                        Guardar cambios
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
        name: this.service.name,
        category: this.service.category,
        description: this.service.description,
        price: this.service.price,
        media: [...this.service.media, null],
        media_edited: false,
    });

    return {
        form,
    }
},
components:{
InputFilePreview,
AppLayout,
PrimaryButton,
InputLabel,
InputError,
Back
},
props:{
service: Object,
},
methods:{
    update() {
      if (this.form.media_edited === true) {
        this.form.post(route("services.update-with-media", this.service.id), {
          method: '_put',
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Se ha editado tu servicio",
              type: "success",
            });
          },
        });
      } else {
        this.form.put(route("services.update", this.service.id), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha editado tu servicio",
                    type: "success",
                });
            },
        });
      }
    },
    saveImage(image) {
        const currentIndex = this.form.media.length -1;
        this.form.media[currentIndex] = image;
        this.form.media.push(null);
        this.form.media_edited = true;
    },
    handleCleared(index) {
      // Eliminar el componente y su informacion correspondiente cuando se borra la imagen
      this.form.media.splice(index, 1);
      this.form.media_edited = true;
    },
}
}
</script>
