<template>
    <Head title="Página no encontrada" />
    <div class="min-h-screen bg-gradient-to-br from-pink-100 via-yellow-100 to-blue-100 flex items-center justify-center px-4">
        <div class="text-center max-w-md">
            <img src="@/../../public/images/404_not_found.png" alt="Caja registradora perdida" class="mx-auto w-full mb-6" />
            <!-- <h2 class="text-2xl font-semibold text-gray-800 mt-2">¡Oops! Caja registradora extraviada</h2> -->
            <!-- <p class="text-gray-600 mt-4">
                Parece que esta sección de la tienda no existe.<br>
                Tal vez movieron el estante... o se fue de vacaciones el cajero.
            </p> -->
            <button
                @click="$inertia.visit('/dashboard')"
                class="bg-gray-100 text-center w-48 rounded-full h-14 relative text-black text-xl font-semibold group"
                type="button"
                >
                <div
                    class="bg-primary text-white rounded-full h-12 w-1/4 flex items-center justify-center absolute left-1 top-[4px] group-hover:w-[184px] z-10 duration-500"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </div>
                <p class="translate-x-2">Regresar</p>
            </button>
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';

export default {
data() {
    return {
        
    }
},
components:{
    Head
},
}
</script>

