<template>
    <AppLayout :title="product.data.name">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="lg:flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Productos</h1>
                <div class="flex items-center space-x-3 my-2 lg:my-0">
                    <ThirthButton v-if="isInventoryOn" @click="openEntryModal">
                        Entrada de producto
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
                    <Loading2 v-if="searchLoading" class="my-3" />
                    <ul v-else-if="productsFound?.length > 0">
                        <li @click.stop="handleProductSelected(product)" v-for="(product, index) in productsFound"
                            :key="index" class="hover:bg-gray-200 cursor-default text-sm px-5 py-2">{{
                                product.global_product_id ? product.global_product?.name : product.name }}</li>
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
                    <figure class="border size-96 border-grayD9 rounded-lg flex justify-center items-center">
                        <img v-if="product.data.imageCover?.length" class="h-[380px] mx-auto object-contain"
                            :src="product.data.imageCover[0]?.original_url" alt="">
                        <div v-else>
                            <i class="fa-regular fa-image text-9xl text-gray-200"></i>
                            <p class="text-sm text-gray-300">Imagen no disponible</p>
                        </div>
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
                                <p class="text-gray37">Categoría: <span class="font-bold">{{ product.data.category?.name
                                        }}</span></p>
                                <i class="fa-solid fa-circle text-[7px] text-[#9A9A9A]"></i>
                                <p class="text-gray37">Proveedor: <span class="font-bold">{{ product.data.brand?.name
                                        }}</span></p>
                            </div>
                        </div>
                        <p class="text-gray37 mt-3">Fecha de alta: <strong class="ml-5">{{ product.data.created_at
                                }}</strong></p>
                        <h1 class="font-bold text-lg lg:text-xl my-2 lg:mt-4">{{ product.data.name }}</h1>
                        <div class="lg:w-1/2 mt-3 lg:mt-3 -ml-7 space-y-2">
                            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-gray37">Precio de compra:</p>
                                <p class="text-right font-bold">{{ product.data.cost ? '$' + product.data.cost : '-' }}
                                </p>
                            </div>
                            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-gray37">Precio de venta: </p>
                                <p class="text-right font-bold">${{ product.data.public_price }}</p>
                            </div>
                            <div v-if="product.data.current_stock >= product.data.min_stock || !isInventoryOn"
                                class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-gray37">Existencias: </p>
                                <p class="text-right font-bold text-[#5FCB1F]">{{ product.data.current_stock ?? '-' }}
                                </p>
                            </div>
                            <div v-else class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1 relative">
                                <p class="text-gray37">Existencias: </p>
                                <p class="text-right font-bold text-redDanger">
                                    <span>{{ product.data.current_stock ?? '-' }}</span>
                                    <i class="fa-solid fa-arrow-down text-xs ml-2"></i>
                                </p>
                                <p class="absolute top-2 -right-16 text-xs font-bold text-redDanger">Bajo stock</p>
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
                            <div class="flex items-center justify-center space-x-3">
                                <PrimaryButton @click="loadPreviousMonth"><i
                                        class="fa-solid fa-chevron-left text-[9px] py-1"></i></PrimaryButton>
                                <PrimaryButton @click="loadNextMonth"><i
                                        class="fa-solid fa-chevron-right text-[9px] py-1"></i></PrimaryButton>
                            </div>
                            <div v-if="Object?.keys(productHistory)?.length">
                                <div v-for="(history, index) in productHistory" :key="history">

                                    <h2 class="rounded-full text-sm bg-grayD9 font-bold px-3 py-1 my-4 w-36">{{
                                        translateMonth(index) }}</h2>
                                    <p class="mt-1 ml-4 text-sm" v-for="activity in history" :key="activity"><span
                                            class="mr-2" v-html="getIcon(activity.type)"></span>{{ activity.description
                                                + ' ' +
                                                activity.created_at }}
                                    </p>
                                </div>
                            </div>
                            <p v-else class="text-xs text-gray-500 mt-5 text-center">No hay actividad en esta fecha</p>
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
                            :parser="(value) => value.replace(/\D/g, '')">
                        </el-input>
                        <InputError :message="form.errors.quantity" />
                    </div>

                    <div v-if="form.quantity && product.data.cost" class="text-sm mt-2">
                        Total de compra: {{ form.quantity }} x ${{ product.data.cost }} => ${{ (product.data.cost *
                            form.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                    </div>

                    <div class="text-left mt-4 ml-6">
                        <p v-if="!product.data.cost" class="text-xs text-redDanger">
                            <i class="fa-regular fa-hand-point-down mr-2"></i>
                            Para poder descontar de caja, primero se debe especificar un precio de compra al producto.
                        </p>
                        <el-checkbox v-model="form.is_paid_by_cash_register" name="is_paid_by_cash_register"
                            label="Se paga con dinero de caja" size="small" :disabled="!product.data.cost" />
                        <div v-if="form.is_paid_by_cash_register" class="w-1/3 mt-3">
                            <InputLabel value="Dinero a retirar de caja" class="ml-3 mb-1 text-sm" />
                            <el-input v-model="form.cash_amount" @keyup="handleChangeCashAmount" placeholder="Ej. $190"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/[^\d.]/g, '')">
                            </el-input>
                            <InputError :message="form.errors.cash_amount || cashAmountMessage" />
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-7 pb-1 py-2">
                        <CancelButton @click="entryProductModal = false">Cancelar</CancelButton>
                        <PrimaryButton :disabled="form.processing || !form.quantity || cashAmountMessage"
                            @click="entryProduct" class="!rounded-full">Ingresar
                            producto
                        </PrimaryButton>
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
import Loading2 from '@/Components/MyComponents/Loading2.vue';
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
            cash_amount: null,
            is_paid_by_cash_register: false //es pagado con dinero de caja? para hacer el registro de movimiento
        });
        return {
            form,
            currentTab: 1,
            searchQuery: this.product.data.name,
            searchFocus: false,
            productsFound: [this.product.data],
            entryProductModal: false,
            productHistory: null,
            currentMonth: new Date().getMonth() + 1, // El mes actual
            currentYear: new Date().getFullYear(), // El año actual
            // loading
            loading: false,
            entryLoading: false,
            searchLoading: false,
            // control de inventario activado
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
            // validaciones
            cashAmountMessage: null,
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        CancelButton,
        ThirthButton,
        InputLabel,
        InputError,
        Loading2,
        Modal,
        Back
    },
    props: {
        product: Object,
        cash_register: Object,
    },
    methods: {
        handleChangeCashAmount() {
            const total = this.product.data.cost * this.form.quantity;
            if (this.form.cash_amount > this.cash_register.current_cash) {
                this.cashAmountMessage = 
                'El monto no debe superar lo disponible en caja ($' + this.cash_register.current_cash.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ')';
            } else if (this.form.cash_amount > total) {
                this.cashAmountMessage = 
                'El monto no debe superar el total del gato ($' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ')';
            } else {
                this.cashAmountMessage = null;
            }
        },
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
            if (!this.entryLoading) {
                this.entryLoading = true;
                this.form.put(route('products.entry', this.product.data?.id), {
                    onSuccess: () => {
                        this.form.reset();
                        this, this.entryProductModal = false;
                        this.$notify({
                            title: 'Correcto',
                            text: 'Se ha ingresado ' + this.form.quantity + ' unidades de ',
                            type: 'success',
                        });
                        this.fetchHistory();
                        this.entryLoading = false;
                    },
                });
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
        handleProductSelected(product) {
            if (product.global_product_id) {
                this.$inertia.get(route('global-product-store.show', product.id))
            } else {
                this.$inertia.get(route('products.show', product.id))
            }
        },
        async fetchHistory() {
            this.loading = true;
            try {
                const response = await axios.get(route("products.fetch-history", {
                    product_id: this.product.data.id,
                    month: this.currentMonth,
                    year: this.currentYear,
                }));
                if (response.status === 200) {
                    this.productHistory = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async loadPreviousMonth() {
            if (this.currentMonth === 1) {
                this.currentMonth = 12;
                this.currentYear -= 1;
            } else {
                this.currentMonth -= 1;
            }
            await this.fetchHistory();
        },
        async loadNextMonth() {
            if (this.currentMonth === 12) {
                this.currentMonth = 1;
                this.currentYear += 1;
            } else {
                this.currentMonth += 1;
            }
            await this.fetchHistory();
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
    },
    mounted() {
        this.fetchHistory();
    }
}
</script>
