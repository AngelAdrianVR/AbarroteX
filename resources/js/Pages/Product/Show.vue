<template>
    <AppLayout :title="product.data.name">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="lg:flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Productos</h1>
                <div class="flex items-center space-x-3 my-2 lg:my-0">
                    <ThirthButton @click="openEntryModal">Entrada de producto
                    </ThirthButton>
                    <PrimaryButton @click="$inertia.get(route('products.edit', product.data.id))" class="!rounded-full">
                        Editar</PrimaryButton>
                </div>
            </div>
            <div class="lg:w-1/4 relative">
                <input v-model="searchQuery" @focus="searchFocus = true" @blur="handleBlur" @input="searchProducts"
                    class="input w-full pl-9" placeholder="Buscar código o nombre de producto" type="search">
                <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                <!-- Resultados de la búsqueda -->
                <div v-if="searchFocus && searchQuery"
                    class="absolute mt-1 bg-white border border-gray-300 rounded shadow-lg w-full">
                    <Loading v-if="searchLoading" />
                    <ul v-else-if="productsFound?.length > 0">
                        <li @click.stop="$inertia.get(route('products.show', product.id))"
                            v-for="(product, index) in productsFound" :key="index"
                            class="hover:bg-gray-200 cursor-default text-sm px-5 py-2">{{ product.name }}</li>
                    </ul>
                    <p v-else class="text-center text-sm text-gray-600 px-5 py-2">No se encontraron coincidencias</p>
                </div>
            </div>
            <div class="mt-5">
                <Back :route="'products.index'" />
            </div>

            <!-- Info de producto -->
            <div class="lg:grid grid-cols-3 gap-x-12 mx-10">
                <!-- fotografia de producto -->
                <section class="mt-7">
                    <figure class="border border-grayD9 rounded-lg">
                        <img class="size-96 mx-auto object-contain" :src="product.data.imageCover[0]?.original_url" alt="">
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
                        <div @click="currentTab = 2"
                            :class="currentTab == 2 ? 'text-primary border-b-2 border-primary pb-1 px-3 font-bold' : 'px-3 pb-1 text-gray66 font-semibold'"
                            class="flex items-center space-x-2 cursor-pointer text-base">
                            <i class="fa-regular fa-calendar-check"></i>
                            <p>Historial de movimientos</p>
                        </div>
                    </div>

                    <!-- pestaña 1 Informacion de producto -->
                    <div v-if="currentTab == 1" class="mt-7 md:mx-16 text-sm lg:text-base">
                        <div class="lg:flex justify-between items-center">
                            <div class="flex space-x-4 items-center">
                                <p class="text-gray37 flex items-center">
                                    <span class="mr-2">Código</span>
                                    <span class="font-bold">{{ product.data.code ?? 'N/A' }}</span>
                                    <el-tooltip v-if="product.data.code" content="Copiar código" placement="right">
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
                                <p class="text-gray37">Categoría: <span class="font-bold">{{ product.data.category?.name }}</span></p>
                                <i class="fa-solid fa-circle text-[7px] text-[#9A9A9A]"></i>
                                <p class="text-gray37">Marca: <span class="font-bold">{{ product.data.brand?.name }}</span></p>
                            </div>
                            <p class="text-gray37 mt-3 lg:mt-0">Fecha de alta: <strong class="ml-5">{{ product.data.created_at
                            }}</strong></p>
                        </div>
                        <h1 class="font-bold text-lg lg:text-xl my-2 lg:my-4">{{ product.data.name }}</h1>

                        <div class="lg:w-1/2 mt-3 lg:mt-10 -ml-7 space-y-2">
                            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-gray37">Precio de compra:</p>
                                <p class="text-right font-bold">${{ product.data.cost ?? '-' }}</p>
                            </div>
                            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-gray37">Precio de venta: </p>
                                <p class="text-right font-bold">${{ product.data.public_price }}</p>
                            </div>
                            <div v-if="product.data.current_stock >= product.data.min_stock"
                                class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-gray37">Existencias: </p>
                                <p class="text-right font-bold text-[#5FCB1F]">{{ product.data.current_stock ?? '-' }}</p>
                            </div>
                            <div v-else class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1 relative">
                                <p class="text-gray37">Existencias: </p>
                                <p class="text-right font-bold text-primary">{{ product.data.current_stock ?? '-' }}<i
                                        class="fa-solid fa-arrow-down text-xs ml-2"></i></p>
                                <p class="absolute top-2 -right-16 text-xs font-bold text-primary">Bajo stock</p>
                            </div>

                            <h2 class="pt-5 ml-5 font-bold text-lg">Cantidades de stock permitidas</h2>

                            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-gray37">Cantidad mínima:</p>
                                <p class="text-right font-bold">{{ product.data.min_stock ?? '-' }}</p>
                            </div>
                            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-gray37">Cantidad máxima:</p>
                                <p class="text-right font-bold">{{ product.data.max_stock ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- ---------------------------------- -->


                    <!-- pestaña 2 historial de producto -->
                    <div v-if="currentTab == 2" class="mt-7 mx-16">
                        <!-- estado de carga -->
                        <div v-if="loading" class="flex justify-center items-center py-10">
                            <i class="fa-solid fa-square fa-spin text-4xl text-primary"></i>
                        </div>
                        <div v-else>
                            <div v-for="(history, index) in productHistory" :key="history">
                                <h2 class="rounded-full text-sm bg-gray99 font-bold px-3 py-1 my-4 w-36">{{
                                    translateMonth(index) }}</h2>
                                <p class="mt-1 ml-4 text-sm" v-for="activity in history" :key="activity"><span class="mr-2"
                                        v-html="getIcon(activity.type)"></span>{{ activity.description + ' ' +
                                            activity.created_at }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- ---------------------------------- -->
                </section>
            </div>
        </div>


        <!-- -------------- Modal starts----------------------- -->
        <Modal :show="entryProductModal" @close="entryProductModal = false">
            <div class="p-4 relative">
                <i @click="entryProductModal = false"
                    class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

                <h1 class="font-bold my-4">Ingresar producto a almacén</h1>
                <section class="text-center mt-5 mb-2 mx-5">
                    <div class="mt-3">
                        <InputLabel value="Cantidad" class="ml-3 mb-1 text-sm" />
                        <el-input v-model="form.quantity" ref="quantityInput" @keydown.enter="entryProduct"
                            placeholder="Cantidad que entra a almacén"
                            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            :parser="(value) => value.replace(/\$\s?|(,*)/g, '')">
                            <template #prefix>
                                <i class="fa-solid fa-hashtag"></i>
                            </template>
                        </el-input>
                        <InputError :message="form.errors.quantity" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-7 pb-1 py-2">
                        <PrimaryButton @click="entryProduct" class="!rounded-full">Ingresar producto</PrimaryButton>
                        <CancelButton @click="entryProductModal = false">Cancelar</CancelButton>
                    </div>
                </section>
            </div>
        </Modal>
        <!-- --------------------------- Modal ends ------------------------------------ -->
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import Back from "@/Components/MyComponents/Back.vue";
import axios from 'axios';
import { useForm } from "@inertiajs/vue3";

export default {
    data() {
        const form = useForm({
            quantity: null,
        });
        return {
            form,
            currentTab: 1,
            searchQuery: null,
            searchFocus: false,
            productsFound: null,
            entryProductModal: false,
            productHistory: null,
            loading: null,
            searchLoading: false,
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        CancelButton,
        ThirthButton,
        InputLabel,
        InputError,
        Loading,
        Modal,
        Back
    },
    props: {
        product: Object
    },
    methods: {
        copyToClipboard() {
            const textToCopy = this.product.data.code;

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
                message: this.product.data.code + " copiado",
                type: "success",
            });
        },
        async searchProducts() {
            this.searchLoading = true;
            try {
                const response = await axios.get(route('products.search'), { params: { query: this.searchQuery } });
                if (response.status == 200) {
                    this.productsFound = response.data.items;
                }

            } catch (error) {
                console.log(error);
            } finally {
                this.searchLoading = false;
            }
        },
        handleBlur() {
            // Introducir un retraso para dar tiempo al evento click de ejecutarse antes del blur
            setTimeout(() => {
                this.searchFocus = false;
            }, 100);
        },
        openEntryModal() {
            this.entryProductModal = true;
            this.$nextTick(() => {
                this.$refs.quantityInput.focus(); // Enfocar el input de código cuando se abre el modal
            });
        },
        entryProduct() {
            this.form.put(route('products.entry', this.product.data?.id), {
                onSuccess: () => {
                    this.form.reset();
                    this, this.entryProductModal = false;
                    this.$notify({
                        title: 'Correcto',
                        text: 'Se ha ingresado ' + this.form.quantity + ' unidades de ',
                        type: 'success',
                    });
                },
            });
        },
        async fetchHistory() {
            this.loading = true;
            try {
                const response = await axios.get(route("products.fetch-history", this.product.data.id));
                if (response.status === 200) {
                    this.productHistory = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        getIcon(type) {
            if (type === 'Precio') {
                return '<i class="fa-solid fa-dollar-sign"></i>';
            } else if (type === 'Entrada') {
                return '<i class="fa-regular fa-square-plus"></i>';
            } else if (type === 'Venta') {
                return '<i class="fa-solid fa-hand-holding-dollar"></i>';
            }
        },
        translateMonth(dateString) {
            const [month, year] = dateString.split(' ');

            const monthsTranslation = {
                'January': 'Enero',
                'February': 'Febrero',
                'March': 'Marzo',
                'April': 'Abril',
                'May': 'Mayo',
                'June': 'Junio',
                'July': 'Julio',
                'August': 'Agosto',
                'September': 'Septiembre',
                'October': 'Octubre',
                'November': 'Noviembre',
                'December': 'Diciembre',
            };

            const translatedMonth = monthsTranslation[month] || month;

            return `${translatedMonth} ${year}`;
        },
    },
    mounted() {
        this.fetchHistory();
    }
}
</script>
