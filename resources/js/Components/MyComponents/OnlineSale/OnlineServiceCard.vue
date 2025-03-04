<template>
    <div class="py-3 px-5 rounded-2xl border-2 border-gray-100 flex flex-col h-[450px] hover:shadow-2xl relative group transition-all ease-linear duration-200">
        <!-- Imagen -->
        <figure class="h-1/2 text-center rounded-xl bg-[#f9f9f9] flex items-center justify-center">
            <Link
                :href="route('online-sales.show-service', {slug: store.slug, service: service.id})">
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
        <div class="mt-5 h-1/2">
            <div class="mb-4 ml-3 text-left">
                <h1 class="mb-4 font-bold w-96 truncate">{{ service.name }}</h1>
                <p class="h-24 overflow-auto" v-if="service.description">{{ service.description }}</p>
                <p v-else>No hay descripción en este servicio...</p>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <p class="text-am text-gray-700">Cotizar</p>
                <button class="rounded-full size-10 border border-gray-300 flex items-center justify-center hover:border-transparent hover:bg-primary text-gray-500 hover:text-white transition-all ease-linear duration-200">
                    <a :href="whatsappLink" target="_blank" rel="noopener noreferrer">
                        <i class="fa-brands fa-whatsapp size-5"></i>
                    </a>
                </button>
            </div>

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
