<template>
    <Head title="Gracias por tu compra" />
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md text-center">
            <h1 class="text-3xl font-bold text-green-600 mb-4">¡Gracias por tu compra!</h1>
            <p class="text-lg text-gray-700 mb-6">Tu pedido ha sido recibido y está siendo procesado.</p>
            <p class="text-md text-gray-600 mb-8">Nos pondremos en contacto contigo por whatsapp para coordinar la
                entrega.</p>
            <Link :href="route('online-sales.client-index', store?.slug ?? '')" class="text-primary hover:underline">
                Volver a la tienda
            </Link>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import confetti from "canvas-confetti";

export default {
    name: 'Thanks',
    data() {
        return {
            encodedIdStore: null, //id codificado de la tienda
        }
    },
    components: {
        Link,
        Head,
    },
    props: {
        store: Object,
    },
    methods: {
        encodeStoreId() {
            const encodedId = btoa(this.store.id?.toString());
            this.encodedIdStore = encodedId;
        },
    },
    mounted() {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 }
        });

        this.encodeStoreId();
    }
}
</script>