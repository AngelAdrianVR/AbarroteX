<template>
    <AppLayout :title="global_product.name">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Productos globales</h1>
                <div class="flex items-center space-x-3 my-2 lg:my-0">
                    <PrimaryButton @click="$inertia.get(route('global-products.edit', global_product.id))" class="!rounded-full">Editar</PrimaryButton>
                </div>
            </div>

            <!-- selector de producto global -->
            <div class="md:w-1/3 mt-2">
                <el-select @change="$inertia.get(route('global-products.show', global_product_id))" v-model="global_product_id" clearable
                    placeholder="Seleccione" no-data-text="No hay opciones registradas"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in global_products" :key="item" :label="item.name" :value="item.id" />
                </el-select>
            </div>

            <div class="mt-5">
                <Back :route="'products.index'" />
            </div>

            <!-- Info de producto -->
            <div class="lg:grid grid-cols-3 gap-x-12 mx-10">
                <!-- fotografia de producto -->
                <section class="mt-7">
                    <figure class="border border-grayD9 rounded-lg">
                        <img class="size-60 lg:size-96 mx-auto object-contain" :src="global_product.media[0]?.original_url" alt="">
                    </figure>
                </section>

                <!-- informacion de producto -->
                <section class="col-span-2 my-3 lg:my-0">
                    <!-- Pestañas -->
                    <div
                        class="lg:w-3/4 w-full flex items-center space-x-7 text-sm border-b border-gray4 lg:mx-16 mx-2 mb-5 contenedor transition-colors ease-linear duration-200">
                        <div @click="currentTab = 1"
                            :class="currentTab == 1 ? 'text-primary border-b-2 border-primary pb-1 px-3 font-bold' : 'px-3 pb-1 text-gray66 font-semibold'"
                            class="flex items-center space-x-2 cursor-pointer text-base">
                            <i class="fa-regular fa-file-lines"></i>
                            <p>Información del producto</p>
                        </div>
                    </div>

                    <!-- pestaña 1 Informacion de producto -->
                    <div v-if="currentTab == 1" class="mt-7 mx-16 text-sm lg:text-base">
                        <div class="lg:flex justify-between items-center">
                            <div class="flex space-x-4 items-center">
                                <p class="text-gray37 flex items-center">
                                    <span class="mr-2">Código</span>
                                    <span class="font-bold">{{ global_product.code ?? 'N/A' }}</span>
                                    <el-tooltip v-if="global_product.code" content="Copiar código" placement="right">
                                        <button @click="copyToClipboard"
                                            class="flex items-center justify-center ml-3 text-xs rounded-full text-gray37 bg-[#ededed] hover:bg-gray37 hover:text-grayF2 size-6 transition-all ease-in-out duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                            </svg>
                                        </button>
                                    </el-tooltip>
                                </p>
                                <i class="fa-solid fa-circle text-[7px] text-[#9A9A9A]"></i>
                                <p class="text-gray37">Categoría: <span class="font-bold">{{ global_product.category?.name }}</span></p>
                            </div>
                            <p class="text-gray37">Fecha de alta: <strong class="ml-5">{{ global_product.created_at
                            }}</strong></p>
                        </div>
                        <h1 class="font-bold text-lg lg:text-xl my-2 lg:my-4">{{ global_product.name }}</h1>

                        <div class="lg:w-1/2 mt-3 lg:mt-10 -ml-7 space-y-2">
                            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-gray37">Precio de venta: </p>
                                <p class="text-right font-bold">${{ global_product.public_price }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- ---------------------------------- -->
                </section>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Back from "@/Components/MyComponents/Back.vue";

export default {
data() {
    return {
        global_product_id: null,
        currentTab: 1,
    }
},
components:{
AppLayout,
PrimaryButton,
Back
},
props:{
global_product: Object,
global_products: Array
},
methods:{
    copyToClipboard() {
            const textToCopy = this.global_product.code;

            // Create a temporary input element
            const input = document.createElement("input");
            input.value = textToCopy;
            document.body.appendChild(input);

            // Select the content of the input element
            input.select();

            // Try to copy the text to the clipboard
            document.execCommand("copy");

            // Remove the temporary input element
            document.body.removeChild(input);

            this.$notify({
                title: "Éxito",
                message: this.global_product.code + " copiado",
                type: "success",
            });
        },
}
}
</script>
