<template>
    <AppLayout title="Suscripción">
        <Back :to="route('profile.show')" />
        <header class="text-center mx-4">
            <h1 class="text-gray37 font-bold text-lg">{{ isExpiredPlan ? 'Modificar módulos de plan' : 'Mejorar suscripción' }}</h1>
            <p class="mt-4 text-sm text-gray77">Más funciones y recursos para potenciar en funcionamiento de tu negocio</p>
            <p class="text-sm text-gray77">¡Paga sólo por lo que utilizas!</p>
        </header>

        <main class="w-full text-sm mx-auto">
            <section class="mt-5 text-left xl:w-[95%] xl:mx-auto p-3">
                <PaymentExpiredPlan v-if="isExpiredPlan" />
                <UpdatePlanModules v-else />
            </section>
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import ToggleButton from "@/Components/MyComponents/ToggleButton.vue";
import UpdatePlanModules from '@/Components/MyComponents/Landing/UpdatePlanModules.vue';
import PaymentExpiredPlan from '@/Components/MyComponents/Landing/PaymentExpiredPlan.vue';
import Month from './Tabs/Month.vue';
import Year from './Tabs/Year.vue';
import Back from "@/Components/MyComponents/Back.vue";

export default {
data() {
    return {
        activeTab: 'Mensual',
        nextPayment: this.$page.props.auth.user.store.next_payment, //próximo pago de la tienda
        isExpiredPlan: false, //bandera que indica si el plan de la tienda ya expiró
    }
},
components:{
    PaymentExpiredPlan,
    UpdatePlanModules,
    ToggleButton,
    AppLayout,
    Month,
    Year,
    Back
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
    //evaluar si ya expiró el plan de la tienda
    const today = new Date();
    const nextPaymentDate = new Date(this.nextPayment);
    // Si la fecha de nextPayment ya pasó, marcar isExpiredPlan como true
    this.isExpiredPlan = nextPaymentDate < today;

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