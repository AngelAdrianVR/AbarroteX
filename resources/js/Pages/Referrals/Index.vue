<template>
  <AppLayout title="Referidos">
    <div class="px-2 lg:px-10 py-7">
      <figure class="relative w-full">
        <img src="@/../../public/images/referalBanner.png" alt="Banner con anunncio de recomienda y gana"
          :draggable="false" class="object-cover select-none w-full hidden lg:block">
        <img src="@/../../public/images/referalBannerMobile.png" alt="Banner con anunncio de recomienda y gana"
          :draggable="false" class="object-cover select-none w-full lg:hidden">
        <p class="w-[86%] lg:w-[54%] text-white text-xl lg:text-5xl absolute top-5 lg:top-8 left-[7%] lg:left-[23%] text-center tracking-wide" style="font-family: 'LeagueGothic';">
          Comienza a recomendar ahora y gana el 50% del pago a cada referido.
        </p>
        <p class="w-[96%] lg:w-[66%] text-white text-sm lg:text-2xl absolute bottom-5 left-[2%] lg:left-[17%] text-center">
          ¡Copia tu código y compártelo con amigos y negocios!
        </p>
      </figure>
      <section class="mt-12 mx-3 lg:mx-14">
        <h2 class="font-bold">Recomienda a tus amigos y gana</h2>
        <p class="text-sm">Invita a tus amigos a utilizar Ezy Ventas y gana recompensas por cada amigo que se una y
          complete el pago de su suscripción.</p>
        <p class="text-sm mt-6">Copia el código y comparte con tus amigos.</p>
        <article class="flex items-center space-x-3 mt-2">
          <div
            class="border border-primary bg-primarylight border-dashed rounded-full text-sm inline-flex space-x-8 items-center px-1">
            <span class="px-2 text-primary">{{ partner.discount_ticket.code }}</span>
            <button @click="copyText"
              class="flex items-center py-1 my-1 px-4 text-xs bg-primary text-white rounded-full">
              <span>Copiar</span>
            </button>
          </div>
          <span>ó</span>
          <button @click="shareByWhatsapp"
            class="size-7 flex items-center justify-center border rounded-full border-grayD9">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M2.35877 2.33229C3.85857 0.832643 5.88736 -0.0061213 8 3.36363e-05C10.1127 -0.00593527 12.1415 0.832985 13.6413 2.33275C15.141 3.83251 15.9893 5.87063 16 7.99996C15.9874 9.44973 15.5875 10.8693 14.8425 12.1095C14.0974 13.3497 13.0346 14.3647 11.7658 15.0479C10.497 15.731 9.06909 16.0571 7.63206 15.9918C6.19502 15.9265 4.80197 15.4723 3.59931 14.6769L0.525219 15.6619L1.52418 12.6905C0.532915 11.3305 -0.00112401 9.6868 0 7.99926C0.0106787 5.86999 0.858978 3.83194 2.35877 2.33229Z"
                fill="#0D7208" />
              <path
                d="M12.3168 10.5414C12.2584 10.4446 12.1033 10.385 11.8702 10.2694C11.6372 10.1537 10.4894 9.58786 10.2765 9.51073C10.0636 9.43361 9.9064 9.39365 9.75127 9.62712C9.59613 9.8606 9.14883 10.385 9.01248 10.5386C8.87614 10.6921 8.74048 10.7139 8.50744 10.5975C7.81964 10.324 7.18412 9.93237 6.62918 9.43992C6.11978 8.97038 5.68273 8.42686 5.33248 7.82731C5.19335 7.59454 5.31787 7.46833 5.43474 7.35195C5.6793 7.12727 5.87741 6.85611 6.0177 6.55406C6.04886 6.48978 6.06341 6.41861 6.06001 6.34716C6.0566 6.2757 6.03535 6.20627 5.99822 6.1453C5.94187 6.02961 5.47509 4.88115 5.28101 4.417C5.06118 3.88695 4.08101 3.86872 3.68657 4.29921C2.44205 5.66011 2.72935 7.2019 3.8257 8.65675C3.94257 8.81241 5.43961 11.2446 7.8104 12.1785C10.1812 13.1125 10.1812 12.8004 10.609 12.7612C11.8382 12.6497 12.6702 11.1325 12.3196 10.5435"
                fill="white" />
            </svg>
          </button>
        </article>
        <article class="mt-10">
          <h2 class="font-bold">¿Cómo funciona?</h2>
          <ol class="*:flex *:space-x-2 *:my-2">
            <li>
              <span class="shrink-0 bg-[#EDEDED] size-6 rounded-full flex items-center justify-center">1</span>
              <span>Comparte tu código de referencia</span>
            </li>
            <li>
              <span class="shrink-0 bg-[#EDEDED] size-6 rounded-full flex items-center justify-center">2</span>
              <span>Las personas que tengan tu código obtienen 30 días gratis de prueba + 10% de descuento en su primera
                compra.</span>
            </li>
            <li>
              <span class="shrink-0 bg-[#EDEDED] size-6 rounded-full flex items-center justify-center">3</span>
              <span>Tu recompensa se activará cuando tu referido realice su primer pago. Obtendrás el 50% del valor de
                su
                suscripción como pago por tu recomendación.</span>
            </li>
            <li>
              <span class="shrink-0 bg-[#EDEDED] size-6 rounded-full flex items-center justify-center">4</span>
              <span> Tú decides cómo recibir tu recompensa: por transferencia a tu tarjeta o acumulada en tu
                suscripción. Si
                eliges acumularla, te bonificaremos los días correspondientes al monto de tu recompensa más 3 meses
                adicionales de regalo.</span>
            </li>
          </ol>
        </article>
      </section>
      <section class="mt-12 mx-3 lg:mx-14">
        <h2 class="font-bold mt-10">Reporte de invitados</h2>
        <article class="flex space-x-3 mt-4">
          <div class="w-40 h-32 bg-[#DDFFDF] rounded-2xl flex flex-col items-center justify-center space-y-1">
            <span class="font-bold text-xl">${{ partner.earnings }}</span>
            <span class="text-[#333333] text-sm">Monto ganado</span>
          </div>
          <div class="w-40 h-32 bg-[#DDFFDF] rounded-2xl flex flex-col items-center justify-center space-y-1">
            <span class="font-bold text-xl">{{ partner.discount_ticket.times_used }}</span>
            <span class="text-[#333333] text-sm">Registrados</span>
          </div>
        </article>
      </section>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
  data() {
    return {

    }
  },
  components: {
    AppLayout,
  },
  props: {
    partner: Object,
  },
  methods: {
    shareByWhatsapp() {
      const cupon = this.partner.discount_ticket.code;
      const message = `¡Hola! te comparto este cupón de descuento para EzyVentas: ${cupon}`;
      const whatsappUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(message)}`;
      window.open(whatsappUrl, '_blank');
    },
    copyText() {
      navigator.clipboard.writeText(this.partner.discount_ticket.code).then(() => {
        ElMessage({
          message: 'Copiado al portapapeles',
          type: 'success',
        });
      }).catch(err => {
        console.error('Error al copiar texto: ', err);
      });
    },
  }
}
</script>
