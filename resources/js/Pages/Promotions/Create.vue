<template>
    <AppLayout title="Agregar promociones">
        <div class="px-3 md:px-10 py-7">
            <Back :to="route('products.index')" />
            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-3">
                <h1 class="font-bold ml-2 text-gray37 col-span-full">Agregar promociones</h1>
                <div class="col-span-full flex items-start space-x-3 text-[#999999] text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
                    </svg>
                    <p>
                        Ten en cuenta que no todas las promociones se pueden aplicar al mismo tiempo. Al seleccionar
                        una, se desactivarán automáticamente las que no sean compatibles.
                    </p>
                </div>
                <div class="col-span-full grid grid-cols-2 lg:grid-cols-3 gap-6 mt-3">
                    <figure class="border border-grayD9 rounded-lg flex items-center justify-center h-36">
                        <template v-if="product.global_product">
                            <img v-if="product.global_product.media.length" class="object-contain h-full"
                                :src="product.global_product.media[0].original_url"
                                :alt="product.global_product.name" />
                            <div v-else
                                class="mx-auto bg-white border border-grayD9 text-gray99 rounded-md text-sm flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="0.8" stroke="currentColor" class="size-16">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </div>
                        </template>
                        <template v-else>
                            <img v-if="product.media.length" class="object-contain h-full"
                                :src="product.media[0].original_url" :alt="product.name" />
                            <div v-else
                                class="mx-auto bg-white text-gray99 rounded-md text-sm flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="0.8" stroke="currentColor" class="size-16">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </div>
                        </template>
                    </figure>
                    <div class="col-span-2">
                        <h2 class="text-base font-bold text-gray37">{{ product.global_product ?
                            product.global_product.name :
                            product.name }}</h2>
                        <p class="text-base text-[#999999] mt-2">
                            Precio actual: <span class="text-black">${{ product.public_price }}</span>
                        </p>
                        <p class="text-base text-[#999999]">
                            Existencias actuales: <span class="text-black">{{ product.current_stock }}</span>
                        </p>
                    </div>
                    <section class="col-span-full space-y-2">
                        <article v-for="(item, index) in form.promos" :key="index" class="border rounded-[10px] pb-2">
                            <header class="relative">
                                <div class="absolute -top-2 -right-2">
                                    <el-popconfirm v-if="form.promos.length > 1" confirm-button-text="Si"
                                        cancel-button-text="No" icon-color="#373737" :title="'¿Desea eliminar?'"
                                        @confirm="deletePromo(index)">
                                        <template #reference>
                                            <button type="button"
                                                class="size-6 bg-grayF2 flex items-center justify-center rounded-full">
                                                <i class="fa-regular fa-trash-can text-sm text-primary"></i>
                                            </button>
                                        </template>
                                    </el-popconfirm>
                                </div>
                            </header>
                            <div class="grid grid-cols-2 gap-3 mt-2">
                                
                            </div>
                        </article>
                        <button @click="addPromo" type="button" class="text-primary text-sm ml-3 mt-4">+ Agregar promoción</button>
                    </section>
                </div>
                <div class="col-span-full text-right mt-3">
                    <PrimaryButton class="!rounded-full" :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Crear promociones
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";
import { addOrUpdateItem } from "@/dbService.js";
import axios from 'axios';

export default {
    data() {
        const form = useForm({
            promos: [],
            product_id: this.product.id,
        });

        return {
            form,
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        CancelButton,
        DialogModal,
        InputLabel,
        InputError,
        Back
    },
    props: {
        product: Object,
    },
    methods: {
        addPromo() {
            const newPromo = {
                color: null,
                sizes: [],
            }

            this.form.promos.push(newPromo);
        },
        deletePromo(index) {
            this.form.promos.splice(index, 1);
        },
        async store() {
            try {
                this.form.post(route("products.store"), {
                    onSuccess: async () => {
                        // guardar nuevo producto a IndexedDB
                        // Obtener producto mas reciente agregado
                        const response = await axios.get(route('products.get-all-for-indexedDB'));
                        const product = response.data.local_products[0];

                        // agregar a indexedDB
                        await addOrUpdateItem('products', product);

                        // toast
                        this.$notify({
                            title: "Correcto",
                            message: "",
                            type: "success",
                        });
                    },
                });
            } catch (error) {
                console.error(error);
            }
        },
    }
}
</script>