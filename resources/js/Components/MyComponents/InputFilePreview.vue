<template>
  <div class="inline">
    <figure @click="triggerImageInput"
      class="flex items-center justify-center rounded-md border border-dashed border-grayD9 relative" :class="{
        'cursor-not-allowed': disabled,
        'cursor-pointer': !disabled,
        [width]: true,
        [height]: true,
      }">
      <i v-if="image && canDelete && !disabled" @click.stop="clearImage"
        class="fa-solid fa-xmark absolute p-1 top-1 right-1 z-10 text-sm"></i>
      <svg v-if="!image && !loading" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
        stroke-width="1.5" stroke="currentColor" class="size-4 text-[#595959]">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
      </svg>
      <div v-if="loading" class="absolute inset-0 rounded-[10px] flex items-center justify-center">
        <i class="fa-solid fa-circle-notch animate-spin text-xl"></i>
      </div>
      <img v-if="image" :src="image" :alt="alt" @load="loading = false"
        class="w-full h-full object-contain bg-no-repeat rounded-md opacity-50" />
      <input :disabled="disabled" ref="fileInput" type="file" @change="handleImageUpload" class="hidden" />
    </figure>
  </div>
</template>

<script>
export default {
  data() {
    return {
      image: null,
      loading: false,
      formData: {
        file: null,
      },
    };
  },
  props: {
    alt: {
      type: String,
      default: "Vista previa no disponible",
    },
    canDelete: {
      type: Boolean,
      default: true,
    },
    imageUrl: {
      type: String,
      default: null,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    width: {
      type: String,
      default: 'w-24',
    },
    height: {
      type: String,
      default: 'h-24',
    },
  },
  watch: {
    imageUrl: {
      immediate: true,
      handler(newImageUrl) {
        if (newImageUrl) {
          this.image = newImageUrl;
          this.loading = true;
        }
      },
    },
  },
  emits: ['imagen', 'cleared'],
  methods: {
    triggerImageInput() {
      this.$refs.fileInput.click();
    },
    handleImageUpload(event) {
      const file = event.target.files[0];
      this.formData.file = file;

      if (file) {
        const imageURL = URL.createObjectURL(file);
        this.image = imageURL;
        // Emitir evento al componente padre con la imagen
        this.$emit("imagen", file);
      }
    },
    clearImage() {
      this.image = null;
      this.formData.file = null;
      this.$emit("cleared");
    },
  },
};
</script>