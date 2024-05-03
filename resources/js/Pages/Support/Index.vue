<template>
    <AppLayout title="Soporte">
        <header class="text-center mt-20">
            <h1 class="text-gray37 font-bold text-lg">¿Cómo podemos ayudarte?</h1>
            <p class="mt-4 text-sm text-gray77">Seleccione una categoría para encontrar de manera ágil la asistencia que
                requiere</p>
        </header>
        <main class="mx-2 md:mx-56 my-5 text-sm">
            <section class="flex items-center justify-center space-x-4">
                <IconTextButton v-for="(item, index) in categories" :key="index" :icon="item.icon"
                    :routeName="item.routeName" :title="item.title" />
            </section>
        </main>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import IconTextButton from '@/Components/MyComponents/Support/IconTextButton.vue';

export default {
    data() {
        return {
            categories: [
                {
                    icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14" height="28" width="28"
                        id="Help-Chat-2--Streamline-Core">
                        <desc>Help Chat 2 Streamline Icon: https://streamlinehq.com</desc>
                        <g id="help-chat-2--bubble-help-mark-message-query-question-speech-circle">
                            <path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                d="M6.99815 0.551881c-1.16638 0.000363 -2.31081 0.317086 -3.31141 0.916439 -1.0006 0.59935 -1.81989 1.45888 -2.37062 2.48704C0.765388 4.98352 0.503852 6.14181 0.559363 7.30686 0.614874 8.47191 0.985355 9.60009 1.63135 10.5712L0.550049 13.4481l3.620851 -0.6548c0.87189 0.4261 1.82871 0.6499 2.79912 0.6547 0.97041 0.0048 1.92939 -0.2095 2.80546 -0.6269 0.87602 -0.4174 1.64662 -1.0272 2.25422 -1.7838 0.6076 -0.7567 1.0366 -1.64076 1.255 -2.58629 0.2183 -0.94554 0.2205 -1.92817 0.0062 -2.87465 -0.2142 -0.94647 -0.6393 -1.83239 -1.2436 -2.5917 -0.6043 -0.75931 -1.3722 -1.37243 -2.24645 -1.79365C8.92661 0.769778 7.96857 0.551308 6.99815 0.551881v0Z"
                                stroke-width="1"></path>
                            <path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                d="M5.51013 5.51195c0 -0.2943 0.08727 -0.582 0.25078 -0.8267 0.1635 -0.24471 0.3959 -0.43543 0.6678 -0.54805 0.2719 -0.11263 0.57109 -0.1421 0.85974 -0.08468 0.28865 0.05741 0.55379 0.19913 0.7619 0.40724 0.2081 0.2081 0.34982 0.47324 0.40723 0.76189 0.05742 0.28865 0.02795 0.58784 -0.08467 0.85974 -0.11263 0.2719 -0.30335 0.5043 -0.54805 0.6678 -0.24471 0.16351 -0.5324 0.25078 -0.82671 0.25078v0.99202"
                                stroke-width="1"></path>
                            <g id="Group 2575">
                                <path id="Vector_3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    d="M7.00008 10.4721c-0.13697 0 -0.248 -0.1111 -0.248 -0.248 0 -0.137 0.11103 -0.24803 0.248 -0.24803"
                                    stroke-width="1"></path>
                                <path id="Vector_4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.99992 10.4722c0.13697 0 0.248 -0.111 0.248 -0.248s-0.11103 -0.248 -0.248 -0.248"
                                    stroke-width="1"></path>
                            </g>
                        </g>
                    </svg>`,
                    title: "Preguntas",
                    routeName: 'supports.faqs',
                },
                {
                    icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    `,
                    title: "Guía",
                    routeName: 'tutorials.index',
                },
                {
                    icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-1 -1 28 28" height="28" width="28" id="Receipt-Check--Streamline-Core"><desc>Receipt Check Streamline Icon: https://streamlinehq.com</desc><g id="receipt-check--shop-shopping-pay-payment-store-cash-bill-receipt-check-confirm"><path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m5.107142857142858 13 3.7142857142857144 2.7857142857142856 6.5 -7.428571428571429" stroke-width="2"></path><path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M22.285714285714285 0.9285714285714286H4.642857142857143c-0.9850842857142856 0 -1.9298314285714286 0.39132600000000006 -2.6263900000000002 1.0878957142857144C1.3198974285714284 2.7130257142857146 0.9285714285714286 3.657772857142857 0.9285714285714286 4.642857142857143v20.42857142857143L5.571428571428571 22.285714285714285l4.642857142857143 2.7857142857142856L14.857142857142858 22.285714285714285l4.642857142857143 2.7857142857142856V3.7142857142857144c0 -0.7388085714285715 0.2934285714285714 -1.4473828571428573 0.8158428571428572 -1.9697971428571428C20.83825714285714 1.2220650000000002 21.546942857142856 0.9285714285714286 22.285714285714285 0.9285714285714286c0.7387714285714285 0 1.4474571428571428 0.29349357142857146 1.9698714285714285 0.8159171428571429 0.5224142857142857 0.5224142857142857 0.8158428571428572 1.2309885714285715 0.8158428571428572 1.9697971428571428v6.5h-5.571428571428571" stroke-width="2"></path></g></svg>`,
                    title: "Suscripción",
                    routeName: 'supports.index',
                },

            ]
        }
    },
    components: {
        AppLayout,
        IconTextButton,
    },
    props: {

    },
    methods: {

    }
}
</script>