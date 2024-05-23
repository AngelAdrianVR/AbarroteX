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

    <div class="md:flex items-center justify-center md:space-x-9 my-5">
      <div class="flex flex-col items-center justify-center">
        <!-- Banner 1  -->
          <InputFilePreview
              v-show="currentImage == 1"
              @imagen="this.bannerForm.banner1 = $event; clearedBanner1 = false"
              :imageUrl="getMediaUrl('banner1')"
              @cleared="bannerForm.clearedBanner1 = true"
          />

        <!-- Banner 2  -->
          <InputFilePreview
              v-show="currentImage == 2"
              @imagen="this.bannerForm.banner2 = $event; clearedBanner2 = false"
              :imageUrl="getMediaUrl('banner2')"
              @cleared="bannerForm.clearedBanner2 = true"
          />

        <!-- Banner 3  -->
          <InputFilePreview
              v-show="currentImage == 3"
              @imagen="this.bannerForm.banner3 = $event; clearedBanner3 = false"
              :imageUrl="getMediaUrl('banner3')"
              @cleared="bannerForm.clearedBanner3 = true"
          />

        <p class="text-center mt-3">
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
      </div>

      <div class="flex flex-col items-center justify-center mt-7 md:mt-0">
        <!-- Logo  -->
        <InputFilePreview
            @imagen="this.logoForm.logoImage = $event; clearedLogo = false"
            :imageUrl="logo?.media[0]?.original_url"
            @cleared="logoForm.clearedLogo = true"
        />
        <p class="mt-3">Logotipo de tu tienda (opcional)</p>
      </div>

    </div>

        <div class="text-right">
            <PrimaryButton :disabled="logoForm.processing || bannerForm.processing" @click="update">Guardar</PrimaryButton>
        </div>
      
  </section>

  <!-- información de tienda -->
  <section class="my-5">
    <h2 class="font-bold mb-5">Contacto de WhatsApp <i class="fa-brands fa-whatsapp"></i></h2>
    <div class="flex items-center">
      <p class="ml-7">Número de teléfono:</p>
      <div v-if="!editWhatsapp" class="flex space-x-5">
        <p class="ml-7">{{ $page.props.auth.user.store.whatsapp ?? 'Sin registro' }}</p>
        <i v-if="!editWhatsapp" @click="editWhatsapp = true" class="fa-solid fa-pen text-xs text-primary cursor-pointer bg-gray-100  rounded-full py-1 px-[5px]"></i>
      </div>
      <div v-else class="flex items-center space-x-2 ml-7">
        <el-input v-model="onlineStoreForm.whatsapp"
        :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
        :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
        placeholder="Escribe el número de teléfono" />
        <InputError :message="onlineStoreForm.errors.whatsapp" />
        <button v-if="editWhatsapp">
          <i @click="updateWhatsapp" class="fa-solid fa-check text-xs text-green-600 cursor-pointer bg-green-100 rounded-full py-1 px-[7px]"></i>
        </button>
        <button @click="editWhatsapp = false;">
            <i class="fa-solid fa-x text-xs text-gray-600 cursor-pointer bg-gray-100 rounded-full py-1 px-[7px]"></i>
          </button>
      </div>
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
    const bannerForm = useForm({
      banner1: null,
      banner2: null,
      banner3: null,
      clearedBanner1: false,
      clearedBanner2: false,
      clearedBanner3: false,
    });

    const logoForm = useForm({
      logoImage: null,
      clearedLogo: false,
    });

    const onlineStoreForm = useForm({
      whatsapp: this.$page.props.auth.user.store.whatsapp ?? null,
    });

    return {
      onlineStoreForm,
      bannerForm,
      logoForm,
      currentImage: 1,

      editWhatsapp: false,
    };
  },
  components: {
    InputFilePreview,
    PrimaryButton,
    ThirthButton,
    Link,
  },
  props: {
    banners: Object,
    logo: Object,
  },
  methods: {
    update() {
      this.updateBanners();
      this.updateLogo();
      this.$notify({
          title: "Correcto",
          message: "Se han actualizado la media",
          type: "success",
      });
      // window.location.reload();
    },
    updateWhatsapp() {
      this.onlineStoreForm.put(route('store.update-whatsapp', $page.props.auth.user.store.id));
    },
    updateBanners() {
        this.bannerForm.post(route("banners.update-with-media", this.banners.id), {
          method: '_put',
        });
    },
    updateLogo() {
        this.logoForm.post(route("logos.update-with-media", 1), {
          method: '_put',
        });
    },
    getMediaUrl(collectionName) {
        const media = this.banners?.media.find(img => img.collection_name === collectionName);
        return media ? media.original_url : null;
    },
  },
};
</script>
