<template>
    <div class="py-3 px-5 rounded-lg border-2 border-gayD9 flex flex-col hover:border-primary h-[450px]">
        <!-- Imagen -->
        <figure class="h-1/2 text-center">
            <Link
                :href="route('online-sales.show-service', service.id)">
            <img v-if="service.media?.length"
                :src="service.media[0]?.original_url"
                alt="servicio" class="h-full object-contain mx-auto">
            <div v-else>
                <i class="fa-regular fa-image text-9xl text-gray-200"></i>
                <p class="text-sm text-gray-300">Imagen no disponible</p>
            </div>
            </Link>
        </figure>

        <!-- Detalles -->
        <div class="text-center mt-5 flex flex-col justify-between items-center h-1/2">
            <div class="mb-4">
                <h1 class="mb-4 mx-auto font-bold w-96 truncate">{{ service.name }}</h1>
                <p class="h-24 overflow-auto" v-if="service.description">{{ service.description }}</p>
                <p v-else>No hay descripción en este servicio...</p>
            </div>

            <button class="rounded-full bg-primary flex items-center justify-center px-8 py-1 text-white">
                <a :href="whatsappLink" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-whatsapp text-gray-100"></i>
                    Cotizar
                </a>
            </button>

            <!-- formulario de solicitud de cotizacion -->
            <!-- <PrimaryButton @click="$inertia.get(route('online-sales.quote-service', service.id))" class="!px-9 !py-1 mb-4">Cotizar</PrimaryButton> -->
        </div>
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';

export default {
data(){
    return {
    }   
},
components:{
    PrimaryButton,
    Link
},
props:{
    service: Object,
    store: Object
},
computed:{
    whatsappLink() {
        const text = encodeURIComponent(`Hola! Me interesa tu servicio "${this.service.name}" que vi en tu página`);
        return `https://api.whatsapp.com/send?phone=${this.store?.online_store_properties?.whatsapp}&text=${text}`;
    },
},
}
</script>
