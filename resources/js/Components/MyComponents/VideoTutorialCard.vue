<template>
    <main 
        class="relative z-0 bg-white bg-opacity-70 backdrop-blur-lg rounded-2xl shadow-xl overflow-hidden cursor-pointer transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl"
        @click="openVideo(video)"
    >
        <div class="relative">
            <img 
                :src="`https://img.youtube.com/vi/${video.youtube_id}/hqdefault.jpg`" 
                alt="Miniatura del video" 
                class="w-full h-48 object-cover transition-opacity duration-300 hover:opacity-80"
            />
            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 opacity-0 transition-opacity duration-300 hover:opacity-100">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3l14 9-14 9V3z" />
                </svg>
            </div>
        </div>
        <div class="py-3 px-4">
            <h2 class="text-lg font-semibold">{{ video.title }}</h2>
            <p class="flex items-center space-x-1 text-sm text-gray-500 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span>{{ video.views.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            </p>
            <p class="text-gray-700 mt-2 line-clamp-2 text-sm">{{ video.description }}</p>
        </div>
    </main>
</template>

<script>
export default {
props:{
    video: Object
},
emits:['play'],
methods:{
    async openVideo(video) {
        this.$emit('play', video);

        // Llamada a la API para incrementar vistas
        try {
            const response = await axios.get(`/tutorials/${video.id}/increment-views`);
            // if ( response.status === 200 ) {
            //     this.video.views = response.data.views; // Actualiza el contador en el frontend
            // }
        } catch (error) {
            console.error('Error al incrementar vistas:', error);
        }
    },
}
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>