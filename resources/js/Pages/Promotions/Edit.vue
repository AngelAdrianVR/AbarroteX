<template>
    <AppLayout title="Editar promociones">
        <div class="px-3 md:px-10 py-7">
            <Back :to="route('products.index')" />
            <form @submit.prevent="update"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto lg:grid lg:grid-cols-2 gap-3">
                <h1 class="font-bold ml-2 text-gray37 col-span-full">Editar promociones</h1>
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
                <div class="col-span-full grid grid-cols-3 gap-6 mt-3">
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
                            Precio actual: <span class="text-black">
                                ${{ product.public_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
                        </p>
                        <p class="text-base text-[#999999]">
                            Existencias actuales: <span class="text-black">
                                {{ product.current_stock }}
                            </span>
                        </p>
                    </div>
                    <section class="col-span-full space-y-2">
                        <p v-if="!form.promos.length" class="text-center text-gray99 text-sm">Se han eliminado todas las
                            promociones de este producto
                        </p>
                        <article v-for="(item, index) in form.promos" :key="index" class="border rounded-[10px] pb-2"
                            :class="isExpired(item.expiration_date) ? 'border-amber-600 bg-amber-50' : 'border-grayD9'">
                            <header class="relative">
                                <div class="absolute -top-2 -right-2">
                                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#373737"
                                        :title="'¿Desea eliminar?'" @confirm="deletePromo(index)">
                                        <template #reference>
                                            <button type="button"
                                                class="size-5 bg-[#EDEDED] flex items-center justify-center rounded-full">
                                                <i class="fa-regular fa-trash-can text-xs text-primary"></i>
                                            </button>
                                        </template>
                                    </el-popconfirm>
                                </div>
                                <div v-if="isExpired(item.expiration_date)"
                                    class="absolute -top-2 left-10 text-xs bg-white text-amber-600 px-3">
                                    <p class="font-semibold">Vencido</p>
                                </div>
                            </header>
                            <div class="grid grid-cols-2 gap-3 px-2 py-1 mt-2">
                                <div>
                                    <InputLabel value="Tipo de promoción*" />
                                    <el-select filterable v-model="item.type" @change="handleTypeChange(index)"
                                        clearable placeholder="Selecciona" no-data-text="No hay opciones registradas"
                                        no-match-text="No se encontraron coincidencias">
                                        <el-option v-for="type in promoTypes" :key="type.name" :label="type.name"
                                            :value="type.name" :disabled="checkDisabledType(type.name, index)">
                                            <span class="text-sm mr-2">{{ type.name }}</span>
                                            <span class="text-xs text-[#999999]">{{ type.example }}</span>
                                        </el-option>
                                    </el-select>
                                    <InputError :message="form.errors[`${index}.type`]" />
                                </div>
                                <div>
                                    <InputLabel value="Fecha de vencimiento (opcional)" />
                                    <el-date-picker v-model="item.expiration_date" type="date" class="!w-full"
                                        placeholder="dd/mm/aaaa" :disabled-date="disabledDate" format="DD/MM/YYYY"
                                        value-format="YYYY-MM-DD" />
                                    <InputError :message="form.errors[`${index}.expiration_date`]" />
                                </div>
                                <div v-if="item.type === 'Descuento en precio fijo'">
                                    <InputLabel value="Precio promocional*" />
                                    <el-input v-model="item.discounted_price"
                                        placeholder="Agrega un precio menor al actual"
                                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                                        <template #prefix>
                                            <i class="fa-solid fa-dollar-sign"></i>
                                        </template>
                                    </el-input>
                                    <InputError :message="form.errors[`promos.${index}.discounted_price`]" />
                                </div>
                                <div v-if="item.type === 'Descuento en porcentaje'">
                                    <InputLabel value="Porcentaje de descuento*" />
                                    <el-input v-model="item.discount" @keyup="handleDiscountPercentageChange(index)"
                                        placeholder="Ej. 15%"
                                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                        :parser="(value) => value.replace(/[^\d.]/g, '')" />
                                    <InputError :message="form.errors[`promos.${index}.discounted_price`]" />
                                </div>
                                <div v-if="item.type === 'Descuento en porcentaje'">
                                    <InputLabel value="Precio con descuento" />
                                    <el-input v-model="item.discounted_price"
                                        placeholder="Ingresa primero el porcentaje de descuento" disabled>
                                        <template #prefix>
                                            <i class="fa-solid fa-dollar-sign"></i>
                                        </template>
                                    </el-input>
                                </div>
                                <div v-if="item.type === 'Precio especial por paquete'">
                                    <InputLabel value="Cantidad de productos*" />
                                    <el-input-number v-model="item.pack_quantity" :min="1" :max="999999"
                                        class="!w-full" />
                                    <InputError :message="form.errors[`promos.${index}.pack_quantity`]" />
                                </div>
                                <div v-if="item.type === 'Precio especial por paquete'">
                                    <InputLabel value="Precio especial del paquete*" />
                                    <el-input v-model="item.pack_price" placeholder="Ingresa el precio total del combo"
                                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                                        <template #prefix>
                                            <i class="fa-solid fa-dollar-sign"></i>
                                        </template>
                                    </el-input>
                                    <InputError :message="form.errors[`promos.${index}.pack_price`]" />
                                </div>
                                <div v-if="item.type === 'Promoción tipo 2x1 o 3x2'">
                                    <InputLabel value="Compra*" />
                                    <el-input-number v-model="item.buy_quantity" :min="2" :max="999999"
                                        class="!w-full" />
                                    <InputError :message="form.errors[`promos.${index}.buy_quantity`]" />
                                </div>
                                <div v-if="item.type === 'Promoción tipo 2x1 o 3x2'">
                                    <InputLabel value="Paga solamente*" />
                                    <el-input-number v-model="item.pay_quantity" :min="1" :max="999999"
                                        class="!w-full" />
                                    <InputError :message="form.errors[`promos.${index}.pay_quantity`]" />
                                </div>
                                <div v-if="item.type === 'Producto gratis al comprar otro'">
                                    <InputLabel value="Compra" />
                                    <el-input v-model="form.product_name" disabled />
                                </div>
                                <div v-if="item.type === 'Producto gratis al comprar otro'">
                                    <InputLabel value="Cantidad mínima de compra*" />
                                    <el-input-number v-model="item.min_quantity_to_gift" :min="1" :max="999999"
                                        class="!w-full" />
                                    <InputError :message="form.errors[`promos.${index}.min_quantity_to_gift`]" />
                                </div>
                                <div v-if="item.type === 'Producto gratis al comprar otro'">
                                    <InputLabel value="Llévate gratis*" />
                                    <el-select v-model="item.giftable_id" @change="handleChangeProduct(index)"
                                        filterable placeholder="Selecciona el producto" remote reserve-keyword
                                        :remote-method="fetchProductsMatch" :loading="fetchingProducts" class="!w-full"
                                        no-match-text="No hay productos coincidentes">
                                        <el-option v-for="product in products" :key="product.id" :label="product.name"
                                            :value="product.id" />
                                    </el-select>
                                    <InputError :message="form.errors[`promos.${index}.giftable_id`]" />
                                </div>
                                <div v-if="item.type === 'Producto gratis al comprar otro'">
                                    <InputLabel value="Cantidad de ragalo*" />
                                    <el-input-number v-model="item.quantity_to_gift" :min="1" :max="999999"
                                        class="!w-full" />
                                    <InputError :message="form.errors[`promos.${index}.quantity_to_gift`]" />
                                </div>
                            </div>
                        </article>
                        <button v-if="!conflicts" @click="addPromo" type="button"
                            class="text-primary text-sm ml-3 mt-4">+ Agregar
                            promoción</button>
                        <p v-else class="text-red-700 text-sm text-center">
                            Existen conflictos entre promociones seleccionadas.
                            Eliminar o cambia el tipo de una de ellas para resolver el conflicto.
                        </p>
                    </section>
                </div>
                <div class="col-span-full text-right mt-3">
                    <PrimaryButton class="!rounded-full" :disabled="form.processing || conflicts">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Guardar cambios
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
import { isPast, parseISO } from 'date-fns';

export default {
    data() {
        const form = useForm({
            promos: this.product.promotions || [],
            product_id: this.product.id,
            product_type: this.product.global_product ? 'global' : 'local',
            product_name: this.product.global_product ? this.product.global_product.name : this.product.name,
        });

        return {
            form,
            fetchingProducts: false,
            conflicts: false,
            products: [],
            promoTypes: [
                { name: 'Descuento en precio fijo', example: '(Ej. De $100 a $85)' },
                { name: 'Descuento en porcentaje', example: '(Ej. 25% menos)' },
                { name: 'Precio especial por paquete', example: '(Ej. Six pack x 90)' },
                { name: 'Promoción tipo 2x1 o 3x2', example: '' },
                { name: 'Producto gratis al comprar otro', example: '(Ej. Café + té gratis)' },
            ],
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
        isExpired(date) {
            if (!date) return false; // Si no hay fecha, no está vencida
            // Convierte la fecha a objeto Date si es string
            const dateObj = typeof date === 'string' ? parseISO(date) : date;
            return isPast(dateObj);
        },
        checkConflictsRules(promo) {
            const conflictMatrix = {
                'Descuento en precio fijo': ['Descuento en precio fijo', 'Descuento en porcentaje', 'Precio especial por paquete', 'Promoción tipo 2x1 o 3x2'],
                'Descuento en porcentaje': ['Descuento en precio fijo', 'Descuento en porcentaje', 'Precio especial por paquete', 'Promoción tipo 2x1 o 3x2'],
                'Precio especial por paquete': ['Descuento en precio fijo', 'Descuento en porcentaje', 'Producto gratis al comprar otro', 'Promoción tipo 2x1 o 3x2'],
                'Promoción tipo 2x1 o 3x2': ['Descuento en precio fijo', 'Descuento en porcentaje', 'Precio especial por paquete', 'Promoción tipo 2x1 o 3x2'],
                'Producto gratis al comprar otro': ['Producto gratis al comprar otro']
            };

            // Obtener promociones excluyendo la actual
            const promoList = this.form.promos.filter(p => p !== promo);

            // Verifica si el tipo actual entra en conflicto con alguno ya seleccionado
            return promoList.some(p => conflictMatrix[p.type]?.includes(promo.type));
        },
        checkDisabledType(typeName, index) {
            if (index === 0) {
                return false; // no hay conflictos
            }

            const conflictMatrix = {
                'Descuento en precio fijo': ['Descuento en precio fijo', 'Descuento en porcentaje', 'Precio especial por paquete', 'Promoción tipo 2x1 o 3x2'],
                'Descuento en porcentaje': ['Descuento en precio fijo', 'Descuento en porcentaje', 'Precio especial por paquete', 'Promoción tipo 2x1 o 3x2'],
                'Precio especial por paquete': ['Descuento en precio fijo', 'Descuento en porcentaje', 'Producto gratis al comprar otro', 'Promoción tipo 2x1 o 3x2'],
                'Promoción tipo 2x1 o 3x2': ['Descuento en precio fijo', 'Descuento en porcentaje', 'Precio especial por paquete', 'Promoción tipo 2x1 o 3x2'],
                'Producto gratis al comprar otro': ['Producto gratis al comprar otro'] // restricciones dinámicas
            };

            // Verifica si el tipo actual entra en conflicto con alguno ya seleccionado
            return this.form.promos.some(promo => conflictMatrix[promo.type]?.includes(typeName));
        },
        checkConflicts(promo) {
            // revisar si hay conflictos y si lo hay, mostrar un mensaje
            this.conflicts = false;

            if (this.checkConflictsRules(promo)) {
                this.$notify({
                    title: "Advertencia",
                    message: "Esta promoción entra en conflicto con otra ya seleccionada.",
                    type: "warning",
                });
                this.conflicts = true;
            }
        },
        handleChangeProduct(index) {
            const promo = this.form.promos[index];
            if (promo.giftable_id) {
                const selectedProduct = this.products.find(p => p.id === promo.giftable_id);
                if (selectedProduct) {
                    promo.giftable_type = selectedProduct.type; // Asignar el tipo del producto seleccionado
                } else {
                    promo.giftable_type = null; // Resetear si no se encuentra el producto
                }
            } else {
                promo.giftable_type = null; // Resetear si no hay producto seleccionado
            }
        },
        handleTypeChange(index) {
            const promo = this.form.promos[index];
            // Resetear campos menos el tipo
            promo.expiration_date = null;
            promo.discount = null;
            promo.discounted_price = null;
            promo.pack_quantity = 0;
            promo.pack_price = null;
            promo.buy_quantity = 2;
            promo.pay_quantity = 1;
            promo.min_quantity_to_gift = 1;
            promo.giftable_id = null;
            promo.giftable_type = null;
            promo.quantity_to_gift = 1;

            this.checkConflicts(promo);
        },
        handleDiscountPercentageChange(index) {
            const promo = this.form.promos[index];
            if (promo.type === 'Descuento en porcentaje' && promo.discount) {
                const originalPrice = this.product.public_price;
                const discountAmount = (originalPrice * promo.discount) / 100;
                promo.discounted_price = originalPrice - discountAmount;
            } else {
                promo.discounted_price = null; // Reset if not applicable
            }
        },
        addPromo() {
            const newPromo = {
                type: null,
                expiration_date: null,
                discount: null,
                pack_quantity: 0,
                pack_price: null,
                buy_quantity: 2,
                min_quantity_to_gift: 1,
                giftable_id: null,
                giftable_type: null,
                quantity_to_gift: 1,
            }

            this.form.promos.push(newPromo);
        },
        deletePromo(index) {
            this.form.promos.splice(index, 1);
            this.conflicts = false;
        },
        disabledDate(time) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return time.getTime() <= today.getTime();
        },
        async fetchProductsMatch(query, overwrite = true) {
            if (!query) {
                this.products = [];
                return;
            }

            this.fetchingProducts = true;
            try {
                const response = await axios.get(route('promotions.get-match', { query }));

                if (response.status === 200) {
                    if (overwrite) {
                        this.products = response.data.items;
                    } else {
                        // Si no se debe sobreescribir, agregar los nuevos productos
                        this.products = [...new Set([...this.products, ...response.data.items])];
                    }
                }
            } catch (error) {
                console.error('Error fetching products:', error);
            } finally {
                this.fetchingProducts = false;
            }
        },
        async update() {
            try {
                this.form.put(route("promotions.update"), {
                    onSuccess: async () => {
                        // guardar promociones a IndexedDB
                        // Obtener producto mas reciente agregado
                        const productId = this.product.global_product ? `global_${this.product.id}` : `local_${this.product.id}`;
                        const response = await axios.get(route('products.get-by-id-for-indexedDB', productId));
                        const product = response.data.product;
                        // actualizar a indexedDB
                        if (product) {
                            addOrUpdateItem('products', product);
                        }
                        this.$notify({
                            title: "Promociones actualizadas",
                            type: "success",
                        });
                    },
                    onError: (errors) => {
                        console.log(errors);
                    },
                });
            } catch (error) {
                console.error(error);
            }
        },
    },
    mounted() {
        // Cargar productos al montar el componente
        this.product.promotions.forEach(element => {
            if (element.giftable_id) {
                let productName;
                if (element.giftable_type === 'App\\Models\\GlobalProductStore') {
                    productName = element.giftable.global_product.name;
                } else {
                    productName = element.giftable.name;
                }
                this.fetchProductsMatch(productName, false);
            }
        });
    },
}
</script>