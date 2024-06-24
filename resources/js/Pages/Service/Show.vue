<template>
    <AppLayout :title="'Detalles del servicio'">
        <section class="mx-2 lg:mx-10 mt-7">
            <Back :to="route('services.index')" />

            <h1 class="font-bold mt-4">Detalles del servicio</h1>
            <article class="flex items-center space-x-3 justify-between mt-5">
                <el-select @change="handleSelect()" class="!w-40 md:!w-80" filterable
                    v-model="ServiceId" clearable placeholder="Buscar servicio" no-data-text="No hay opciones registradas"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in services" :key="item" :label="item.name" :value="item.id" />
                </el-select>
                <div class="flex items-center space-x-3">
                    <PrimaryButton @click="$inertia.get(route('services.edit', encodedId))">Editar</PrimaryButton>
                </div>
            </article>

            <section class="md:flex space-x-5 mt-7 md:mx-5">
                <!-- imágenes -->
                <article>
                    <figure class="border border-grayD9 rounded-md flex items-center justify-center w-96 h-72">
                        <img v-if="service.media?.length" :src="service.media[currentImage]?.original_url" alt="servicio"
                            class="h-full mx-auto object-contain">
                        <div v-else>
                            <i class="fa-regular fa-image text-9xl text-gray-200"></i>
                            <p class="text-sm text-gray-300">Imagen no disponible</p>
                        </div>
                    </figure>

                    <!-- Selector de imagen -->
                    <div v-if="service.media?.length > 1" class="mt-3 flex items-center justify-center space-x-3">
                    <i @click="currentImage = index" v-for="(image, index) in service.media?.length" :key="index" 
                        :class="index == currentImage ? 'text-primary' : 'text-gray-300'" 
                        class="fa-solid fa-circle text-xs cursor-pointer"></i>
                    </div>
                </article>

                <!-- Información del servicio -->
                <header class="mt-7 lg:mx-8 text-sm lg:text-base space-y-2 w-full">
                    <p class="text-[#373737]">Nombre: <span class="font-bold ml-10">{{
                        service.name }}</span></p>
                    <p class="text-[#373737]">Categoría: <span class="font-bold ml-8">{{
                        service.category ?? '-' }}</span></p>
                    <div class="flex space-x-5 md:w-2/3">
                        <p class="text-[#373737]">Descripción:</p>
                        <span class="font-bold ml-5" style="white-space: pre-line;">{{service.description ?? '-' }}</span>
                    </div>
                    <p class="text-[#373737]">Precio: <span class="font-bold ml-14">${{
                        service.price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
                    <p class="text-[#373737]">Creado el: <span class="font-bold ml-8">{{
                        formatDate(service.created_at) }}</span></p>
                </header>
            </section>
        </section>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import Back from "@/Components/MyComponents/Back.vue";

export default {
data() {
    return {
        activeTab: '1',
        serviceId: this.service.id, //guarda el id del servicio seleccionado para ingresar a sus detalles desde el select
        encodedId: null, //id codificado
        currentImage: 0, //imagen de servicio actual
    }
},
components:{
    AppLayout,
    PrimaryButton,
    ThirthButton,
    Back
},
props:{
    service: Object,
    services: Array
},
methods:{
    encodeId(id) {
        const encodedId = btoa(id.toString());
        this.encodedId = encodedId;
    },
    handleSelect() {
        const encodedId = btoa(this.ServiceId.toString());
        this.$inertia.get(route('services.show', encodedId))
    },
    formatDate(dateString) {
        if (!dateString) {
            return '--';  // Retorna '--' si dateString es null o undefined
        }
        try {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        } catch (error) {
            console.error('Error formatting date:', error);
            return '--';  // Retorna '--' si ocurre un error al formatear la fecha
        }
    },
},
mounted() {
    this.encodeId(this.service.id);
}
}
</script>