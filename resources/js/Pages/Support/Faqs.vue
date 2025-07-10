<template>
    <AppLayout title="Preguntas frecuentes">
        <header class="mt-8">
            <Back class="mx-2 md:mx-20" />
            <h1 class="mx-2 md:mx-56 text-gray37 font-bold">PREGUNTAS FRECUENTES</h1>
        </header>
        <main class="mx-2 md:mx-56 mt-11 text-sm text-gray37">
            <div>
                <h2 class="font-bold">¿Cómo puedo agregar mis productos al sistema? </h2>
                <p class="mt-3">Puedes registrar tus productos manualmente ingresando la información correspondiente,
                    como nombre,
                    precio, y cantidad en stock (opcional). También ofrecemos la opción de buscar tus productos en
                    nuestro <a :href="route('products.index')" target="_blank" class="text-primary">“Catalogo
                        base”</a> y agregarlos a tus productos. </p>
            </div>
            <div class="mt-8">
                <h2 class="font-bold">¿Cómo puedo recibir notificaciones sobre bajo stock o ventas importantes?</h2>
                <p class="mt-3">Ofrecemos la opción de configurar notificaciones automáticas por mensajes dentro del
                    sistema para alertarte sobre productos con bajo stock, ventas importantes, o cualquier otro evento
                    relevante para tu negocio. <a :href="route('settings.index')" target="_blank" class="text-primary">
                        Ve al módulo "Configuraciones", en la pestaña de "Punto de venta”
                    </a> 
                </p>
            </div>
            <div class="mt-8">
                <h2 class="font-bold">¿Cuál es el proceso de renovación de la suscripción?</h2>
                <!-- <p class="mt-3">La suscripción se renueva automáticamente al final del período de facturación
                    seleccionado (mensual o anual). Te enviaremos un aviso por correo electrónico antes de la
                    renovación, y puedes cancelar en cualquier momento desde tu cuenta.</p> -->
                <p class="mt-3">Se te notificará una semana antes de tu siguiente periodo de facturación para que
                    procedas a realizar el pago. Puedes agregar más tiempo a tu plan en cualquier momento.</p>
            </div>
            <div class="mt-8">
                <h2 class="font-bold">¿Qué sucede si necesito ayuda adicional o tengo un problema técnico?</h2>
                <p class="mt-3">Estamos aquí para ayudarte. Puedes ponerte en contacto con nuestro equipo de soporte
                    técnico a través del formulario de contacto en nuestra página de soporte. Nos esforzamos por
                    responder a todas las consultas en un plazo de 24 horas hábiles.</p>
            </div>

            <div class="flex items-center space-x-3 mt-20">
                <PrimaryButton @click="$inertia.visit(route('support-reports.create'))">
                    Reportar a soporte
                </PrimaryButton>

                <el-tooltip class="" content="Contactar con soporte" placement="right">
                    <a href="https://api.whatsapp.com/send?phone=523321705650" target="_blank" class="flex items-center justify-center rounded-full size-9 bg-primary text-white">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                    </a>
                </el-tooltip>
            </div>
        </main>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Back from '@/Components/MyComponents/Back.vue';

export default {
    data() {
        return {

        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        Back,
    },
    props: {

    },
    methods: {

    }
}
</script>