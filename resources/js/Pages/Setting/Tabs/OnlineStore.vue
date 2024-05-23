<template>
  <div class="flex justify-between">
    <h1 class="font-bold">Configuraciones de tienda en línea</h1>
    <Link :href="route('online-sales.client-index', $page.props.auth.user.store_id)">
      <ThirthButton>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="size-4 mr-1"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"
          />
        </svg>
        Ver tienda en línea
      </ThirthButton>
    </Link>
  </div>

  <!-- Portada -->
  <section class="mt-5">
    <h2 class="my-2 font-bold">Portada</h2>
    <p class="text-sm text-gray99">
      En esta sección, puedes subir imágenes que representen tu marca, promociones, o
      cualquier mensaje importante que desees comunicar a tus clientes. Puedes agregar
      hasta 3 imágenes que rotarán en la portada de tu tienda en línea, ayudándote a
      captar la atención de tus visitantes desde el primer momento.
    </p>

    <div class="my-5 flex justify-center">
      <!-- Banner 1  -->
        <InputFilePreview
            v-show="currentImage == 1"
            @imagen="this.form.banner1 = $event; clearedBanner1 = false"
            :imageUrl="getMediaUrl('banner1')"
            @cleared="form.clearedBanner1 = true"
        />

      <!-- Banner 2  -->
        <InputFilePreview
            v-show="currentImage == 2"
            @imagen="this.form.banner2 = $event; clearedBanner2 = false"
            :imageUrl="getMediaUrl('banner2')"
            @cleared="form.clearedBanner2 = true"
        />

      <!-- Banner 3  -->
        <InputFilePreview
            v-show="currentImage == 3"
            @imagen="this.form.banner3 = $event; clearedBanner3 = false"
            :imageUrl="getMediaUrl('banner3')"
            @cleared="form.clearedBanner3 = true"
        />

    </div>
      <p class="text-center mt-2">
          <i
              @click="currentImage = currentImage - 1"
              v-if="currentImage > 1"
              class="fa-solid fa-angle-left text-xs mr-2 cursor-pointer p-1"
          ></i>
          Imagen {{ currentImage }} de 3
          <i
              @click="currentImage = currentImage + 1"
              v-if="currentImage < 3"
              class="fa-solid fa-angle-right text-xs ml-2 cursor-pointer p-1"
          ></i>
      </p>
    <div class="text-right">
        <PrimaryButton :disabled="form.processing" @click="update">Guardar</PrimaryButton>
    </div>

  </section>
</template>

<script>
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      banner1: null,
      banner2: null,
      banner3: null,
      clearedBanner1: false,
      clearedBanner2: false,
      clearedBanner3: false,
    });
    return {
      form,
      currentImage: 1
    };
  },
  components: {
    InputFilePreview,
    PrimaryButton,
    ThirthButton,
    Link,
  },
  props: {
    banners: Object
  },
  methods: {
    update() {
        console.log('edit');
        this.form.post(route("banners.update-with-media", this.banners.id), {
          method: '_put',
          onSuccess: () => {
            this.$notify({
                title: "Correcto",
                message: "Se han actualizado los banners",
                type: "success",
            });
            window.location.reload();
          },
        });
    },
    getMediaUrl(collectionName) {
        const media = this.banners?.media.find(img => img.collection_name === collectionName);
        return media ? media.original_url : null;
    },
  },
};
</script>
