<template>
    <OnlineStoreLayout :title="service.name">
        <div class="p-4 md:p-9">
            <Back />

            <section class="xl:w-[60%] md:grid grid-cols-2 gap-x-10 mx-auto mt-9">

                <article>
                    <!-- Imagen del servicio -->
                    <figure class="border border-grayD9 rounded-md flex items-center justify-center h-96">
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

                <!-- Detalles del servicio -->
                <div class="flex flex-col justify-between text-center">
                    <div>
                        <h1 class="font-bold text-2xl my-4">{{ service.name }}</h1>
                        <p class="whitespace-break-spaces">{{ formattedDescription }}</p>
                    </div>

                    <div class="mb-2 mt-8 md:mt-0 flex justify-center">
                        <!-- pedir cotización directo a whatsapp -->
                        <button class="rounded-full bg-primary flex items-center justify-center px-20 py-1 text-white">
                            <a :href="whatsappLink" target="_blank" rel="noopener noreferrer">
                                <i class="fa-brands fa-whatsapp text-gray-100"></i>
                                Cotizar
                            </a>
                        </button>

                        <!-- formulario de solicitud de cotizacion -->
                        <!-- <PrimaryButton @click="$inertia.get(route('online-sales.quote-service', service.id))" class="!px-20">Cotizar</PrimaryButton> -->
                    </div>
                </div>
            </section>

        </div>
    </OnlineStoreLayout>
</template>

<script>
import OnlineStoreLayout from '@/Layouts/OnlineStoreLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Back from "@/Components/MyComponents/Back.vue";

export default {
data() {
    return {
        storeId: null, //id de la tienda obtenido del local storage en mounted
        store: null, //información de la tienda
        encodedIdStore: null,
        currentImage: 0, //imagen actual
        formattedDescription: null, //descripción del producto formateado con viñetas
    }
},
components:{
    OnlineStoreLayout,
    PrimaryButton,
    Back
},
props:{
    service: Object
},
methods:{
    formatDescription() {
        if (this.service.description != null) {
            const text = this.service.description;
            const lines = text.split('\n');
            const formattedLines = lines.map(line => `• ${line.trim()}`);
            this.formattedDescription = formattedLines.join('\n');
        }
    },
    encodeStoreId() {
        const encodedId = btoa(this.storeId.toString());
        this.encodedIdStore = encodedId;
    },
    async fetchStoreInfo() {
        try {
            const response = await axios.get(route('stores.fetch-store-info', this.storeId));
            if (response.status === 200) {
                this.store = response.data.store;
            }
        } catch (error) {
            console.log(error);
        }
    }
},
computed:{
    whatsappLink() {
        const text = encodeURIComponent(`Hola! Me interesa tu servicio "${this.service.name}" que vi en tu página`);
        return `https://api.whatsapp.com/send?phone=${this.store?.online_store_properties?.whatsapp}&text=${text}`;
    },
},
mounted() {
    this.formatDescription();

    // recupera el store_id del localStorage
    this.storeId = localStorage.getItem('storeId');

    // recupera la información de la tienda para tomar las configuraciones de la tienda en linea.
    this.fetchStoreInfo();

    //condifica el id de la tienda
    this.encodeStoreId();
},
}
</script>
