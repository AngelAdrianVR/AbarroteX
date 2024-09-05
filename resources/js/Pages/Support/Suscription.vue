<template>
    <AppLayout title="Suscripción">
        <header class="text-center mt-5 mx-4">
            <h1 class="text-gray37 font-bold text-lg">Modulos de suscripción</h1>
            <p class="mt-4 text-sm text-gray77">Puedes agregar o quitar cualquier modulo según las necesidades de tu negocio</p>
            <p class="text-sm text-gray77">¡Paga sólo por lo que utilizas!</p>
        </header>

        <main class="w-full  my-5 text-sm mx-auto">
            <!-- <div class="flex justify-center items-center mb-7 relative">
                <ToggleButton ref="togglebutton" @update="handleToggle" :labels="['Mensual', 'Anual']"
                    class="w-3/4 md:w-[45%] lg:w-[35%] xl:w-[20%]" />
                <span class="hidden md:block border rounded-full px-4 absolute top-1 lg:right-16 right-4 text-sm"
                        :class="activeTab == 'Mensual' ? 'text-gray-400 border-grayD9' : 'text-primary border-primary'">
                    Te regalamos 2 meses
                </span>
            </div> -->

            <!-- <div class="md:hidden text-center mb-4">
                <span :class="activeTab == 'Mensual' ? 'text-gray-400 border-grayD9' : 'text-primary border-primary'" 
                    class="border rounded-full px-4 right-4 text-sm">Te regalamos 2 meses</span>
            </div> -->

            <!-- <section class="mx-auto" v-if="activeTab === 'Mensual'">
                <Month />
            </section>
            <section v-else>
                <Year />
            </section> -->

            <section class="mt-10 text-left xl:w-2/3 xl:mx-auto p-3">
                <InternalSimulator />
            </section>
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import ToggleButton from "@/Components/MyComponents/ToggleButton.vue";
import InternalSimulator from '@/Components/MyComponents/Landing/InternalSimulator.vue';
import Month from './Tabs/Month.vue';
import Year from './Tabs/Year.vue';

export default {
data() {
    return {
        activeTab: 'Mensual',
    }
},
components:{
    AppLayout,
    ToggleButton,
    InternalSimulator,
    Month,
    Year
},
props:{

},
methods:{
    handleToggle(active) {
        this.activeTab = active;

        const tab = active == 'Mensual' ? 'moth' : 'year';
        this.updateURL(tab);
    },
    updateURL(tab) {
        const params = new URLSearchParams(window.location.search);
        params.set('tab', tab);
        window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
    },
},
mounted() {
    // Obtener la URL actual
    const currentURL = new URL(window.location.href);
    // Extraer el valor de 'activeTab' de los parámetros de búsqueda
    const activeTabFromURL = currentURL.searchParams.get('tab');
    if (activeTabFromURL) {
        if (activeTabFromURL == 'year') {
            const tab = 'Year';
            this.$refs.togglebutton.toggle();
        }
    }
},
}
</script>