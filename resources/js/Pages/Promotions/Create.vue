<template>
    <AppLayout title="Agregar promociones">
        <div class="px-3 md:px-10 py-7">
            <Back :to="route('products.index')" />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-3">
                
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
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";
import { addOrUpdateItem } from "@/dbService.js";
import axios from 'axios';

export default {
    data() {
        const form = useForm({
            name: null,
            code: null,
            public_price: null,
            currency: '$MXN',
            cost: null,
            current_stock: null,
            description: null,
            category_id: null,
            brand_id: null,
            min_stock: null,
            max_stock: null,
            imageCover: null,
            product_on_request: false, //producto bajo pedido
            show_in_online_store: true,
            bulk_product: false, //producto a granel
            measure_unit: null, //en caso de ser a granel
            days_for_delivery: null, //dias hábiles para entregar producto bajo pedido
        });

        const categoryForm = useForm({
            name: null,
        });

        const brandForm = useForm({
            name: null,
        });

        return {
            form,
            brandForm,
            categoryForm,
            brandNameError: false, //error para mostrar si el proveedor ya existe
            localCategories: this.categories,
            localBrands: this.brands,
            showCategoryFormModal: false, //muestra formulario para agregar categoría
            showBrandFormModal: false, //muestra formulario para agregar proveedor
            currencies: [
                { value: "Peso Mexicano", label: "$MXN" },
                { value: "Dolar Americano", label: "$USD" },
            ],
            // Permisos de rol actual
            canSeeCost: ['Administrador', 'Almacenista'].includes(this.$page.props.auth.user.rol),
            productsLimit: this.$page.props.auth.user.store.plan == 'Plan Básico' ? 1500 : 3000
        };
    },
    components: {
        AppLayout,
        InputFilePreview,
        PrimaryButton,
        CancelButton,
        DialogModal,
        InputLabel,
        InputError,
        Back
    },
    props: {
        products_quantity: Number, // para validar los productos limite
        categories: Array,
        brands: Array
    },
    methods: {
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
        async storeCategory() {
            try {
                const response = await axios.post(route('categories.store'), {
                    name: this.categoryForm.name
                });
                if (response.status === 200) {
                    this.$notify({
                        title: "Éxito",
                        message: "Se ha creado una nueva categoría",
                        type: "success",
                    });
                    this.form.category_id = response.data.item.id;
                    this.localCategories.push(response.data.item);
                    this.showCategoryFormModal = false;
                    this.categoryForm.reset();
                }
            } catch (error) {
                console.log(error)
            }
        },
        async storeBrand() {
            try {
                const response = await axios.post(route('brands.store'), {
                    name: this.brandForm.name
                });
                if (response.status === 200) {
                    this.$notify({
                        title: "Éxito",
                        message: "Se ha creado una nueva proveedor",
                        type: "success",
                    });
                    this.localBrands.push(response.data.item);
                    this.form.brand_id = response.data.item.id;
                    this.showBrandFormModal = false;
                    this.brandNameError = false;
                    this.brandForm.reset();
                }
            } catch (error) {
                console.log(error);
                this.brandNameError = true;
            }
        },
        saveImage(image) {
            this.form.imageCover = image;
        },
    }
}
</script>