<template>
    <AppLayout title="Tutoriales">
        <main class="p-10">
            <h1 class="text-2xl font-bold mb-6">Videos Tutoriales</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Tarjetas de videos -->
                <VideoTutorialCard @play="selectedVideo=$event" v-for="video in videos" :key="video.id" :video="video" />
            </div>

            <!-- Modal para reproducir el video -->
            <div v-if="selectedVideo" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4">
                <div class="bg-[#232323] rounded-lg p-6 w-full max-w-3xl relative text-white z-50">
                    <button class="absolute top-3 right-4 text-gray-300 hover:text-red-500" @click="selectedVideo = null">✕</button>
                    <h2 class="text-xl font-bold mb-4">{{ selectedVideo.title }}</h2>
                    <iframe 
                        :src="`https://www.youtube.com/embed/${selectedVideo.youtube_id}?autoplay=1`" 
                        frameborder="0" 
                        allowfullscreen 
                        class="w-full h-[500px]"
                    ></iframe>
                    <p class="text-gray-300 mt-4">{{ selectedVideo.description }}</p>
                </div>
            </div>
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import VideoTutorialCard from '@/Components/MyComponents/VideoTutorialCard.vue';

export default {
data() {
    return {
        selectedVideo: null
    }
},
components: {
    VideoTutorialCard,
    AppLayout
},
props: {
    videos: Array
},
};
</script>