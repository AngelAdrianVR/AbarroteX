<template>
    <!-- <div @click="$inertia.visit(route('online-sales.show-service', { slug: store.slug, service: service.id }))"
        class="cursor-pointer py-3 px-5 rounded-2xl border-2 border-gray-100 flex flex-col h-[450px] hover:shadow-2xl relative group transition-all ease-linear duration-200">
        Imagen
        <figure class="h-1/2 text-center rounded-xl bg-[#f9f9f9] flex items-center justify-center">
            <img v-if="service.media?.length" :src="service.media[0]?.original_url" alt="servicio"
                class="h-full object-contain mx-auto">
            <div v-else>
                <i class="fa-regular fa-image text-9xl text-gray-200"></i>
                <p class="text-sm text-gray-300">Sin imagen</p>
            </div>
        </figure>
        Detalles
        <div class="mt-5 h-1/2">
            <div class="mb-4 ml-3 text-left">
                <h1 class="mb-4 font-bold w-96 truncate">{{ service.name }}</h1>
                <p class="h-24 overflow-auto" v-if="service.description">{{ service.description }}</p>
                <p v-else>No hay descripción en este servicio...</p>
            </div>
            <div @click.stop="" class="flex items-center justify-end space-x-3  cursor-default">
                <p class="text-am text-gray-700">Cotizar</p>
                <button
                    class="rounded-full size-10 border border-gray-300 flex items-center justify-center hover:border-transparent hover:bg-primary text-gray-500 hover:text-white transition-all ease-linear duration-200">
                    <a :href="whatsappLink" target="_blank" rel="noopener noreferrer">
                        <i class="fa-brands fa-whatsapp size-5"></i>
                    </a>
                </button>
            </div>
        </div>
    </div> -->
    <div @click="$inertia.visit(route('online-sales.show-service', { slug: store.slug, service: service.id }))" class="max-w-sm h-full rounded-[45px] overflow-hidden shadow-lg relative group">
        <!-- Imagen con zoom -->
        <div class="w-full h-[500px] overflow-hidden">
            <img
            :src="service.media[0]?.original_url || defaultImage"
            alt="Servicio"
            class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-110"
            />
        </div>

        <!-- Botón WhatsApp -->
        <a @click.stop=""
            :href="whatsappLink"
            target="_blank"
            rel="noopener noreferrer"
            class="absolute top-4 right-4 bg-gradient-to-t from-green-500 to-green-900 hover:scale-105 text-white text-sm px-4 py-2 rounded-full shadow-lg flex items-center gap-2"
        >
        
            <i class="fa-brands fa-whatsapp"></i>
            Enviar mensaje
        </a>

        <!-- Contenido -->
        <div
            class="text-center absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent text-white p-4 rounded-b-2xl"
        >
            <h3 class="text-base font-bold uppercase">{{ service.name }}</h3>
            <p class="text-sm mt-1">{{ service.description }}</p>
        </div>
    </div>

</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';

export default {
    data() {
        return {
            defaultImage: '/images/default-service.jpg'
        }
    },
    components: {
        PrimaryButton,
        Link
    },
    props: {
        service: Object,
        store: Object
    },
    computed: {
        whatsappLink() {
            const text = encodeURIComponent(`Hola! Me interesa tu servicio "${this.service.name}" que vi en tu página`);
            return `https://api.whatsapp.com/send?phone=${this.store?.online_store_properties?.whatsapp}&text=${text}`;
        },
    },
}
</script>
