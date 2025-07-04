<template>
    <div class="text-sm">
        <div class="lg:flex justify-between items-center">
            <div class="md:flex space-y-1 md:space-x-4 items-center">
                <p class="text-gray37 flex items-center">
                    <span class="mr-2">Código</span>
                    <span class="font-bold">{{ product.code ?? 'N/A' }}</span>
                    <el-tooltip v-if="product.code" content="Copiar código" placement="right">
                        <button @click="copyToClipboard"
                            class="flex items-center justify-center ml-3 text-xs rounded-full text-gray37 bg-[#ededed] hover:bg-gray37 hover:text-grayF2 size-6 transition-all ease-in-out duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                            </svg>
                        </button>
                    </el-tooltip>
                </p>
                <span class="hidden md:block text-lg text-[#9A9A9A]">•</span>
                <p class="text-gray37">Categoría: <span class="font-bold">{{ product.category?.name ?? 'N/A'
                }}</span></p>
                <span class="hidden md:block text-lg text-[#9A9A9A]">•</span>
                <p class="text-gray37">Proveedor: <span class="font-bold">{{ product.brand?.name ?? 'N/A'
                }}</span></p>
            </div>
        </div>
        <p class="text-gray37 mt-3">
            Fecha de alta:
            <strong class="ml-5">{{ formatDate(product.created_at) }}</strong>
        </p>
        <p v-if="product.bulk_product" class="text-gray37 font-bold mt-3">Producto de venta a granel / ({{
            product.measure_unit }})</p>
        <p v-if="product.product_on_request" class="text-gray37 font-bold mt-3">Producto de venta bajo pedido / ({{
            product.days_for_delivery }} días hábiles)</p>
        <p v-if="$page.props.auth.user.store.activated_modules?.includes('Tienda en línea')"
            class="text-gray37 font-bold mt-3">
            {{ product.show_in_online_store
                ? 'Este producto está visible en la tienda en línea'
                : 'Este producto no está visible en la tienda en línea' }}
        </p>
        <h1 class="font-bold text-lg lg:text-xl my-2 lg:mt-4">{{ product.name }}</h1>
        <div class="xl:w-1/2 mt-3 lg:mt-3 space-y-2">
            <div v-if="canSeeCost" class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                <p class="text-gray37">Precio de compra:</p>
                <p class="text-right font-bold">{{ product.cost ? '$' +
                    product.cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '-' }}
                </p>
            </div>
            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                <p class="text-gray37">Precio de venta: </p>
                <p class="text-right font-bold">${{ product.public_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                    ",") }}</p>
            </div>
            <div v-if="product.current_stock >= product.min_stock || !isInventoryOn"
                class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                <p class="text-gray37">Existencias: </p>
                <p class="text-right font-bold text-[#5FCB1F]">{{
                    product.current_stock?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}
                </p>
            </div>
            <div v-else class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1 relative">
                <p class="text-gray37">Existencias: </p>
                <p class="text-right font-bold text-redDanger">
                    <span>{{ product.current_stock ?? '-' }}</span>
                    <i class="fa-solid fa-arrow-down text-xs ml-2"></i>
                </p>
                <p class="absolute top-2 -right-16 text-xs font-bold text-redDanger">Bajo stock</p>
            </div>

            <h2 class="pt-5 ml-5 font-bold text-lg">Cantidades de stock permitidas</h2>

            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                <p class="text-gray37">Cantidad mínima:</p>
                <p class="text-right font-bold">{{ product.min_stock ?? '-' }}</p>
            </div>
            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                <p class="text-gray37">Cantidad máxima:</p>
                <p class="text-right font-bold">{{ product.max_stock ?? '-' }}</p>
            </div>
            <!-- Descripción del producto -->
            <div v-if="product.description" class="md:!w-[600px]">
                <h2 class="pt-5 ml-5 font-bold text-lg">Sobre el producto</h2>
                <div>
                    <p class="whitespace-break-spaces">{{ formattedDescription }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    name: 'ProductInfo',
    data() {
        return {
            // control de inventario activado
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
            // Permisos de rol actual
            canSeeCost: ['Administrador', 'Almacenista'].includes(this.$page.props.auth.user.rol),
            // formateados
            formattedDescription: null,
        };
    },
    components: {
    },
    props: {
        product: Object,
    },
    computed: {

    },
    methods: {
        formatDate(date) {
            return format(new Date(date), 'dd MMMM yyyy • h:mm a', { locale: es });
        },
        copyToClipboard() {
            const textToCopy = this.product.code;

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
                message: this.product.code + " copiado",
                type: "success",
            });
        },
        formatDescription() {
            if (this.product.description != null) {
                const text = this.product.description;
                const lines = text.split('\n');
                const formattedLines = lines.map(line => `• ${line.trim()}`);
                this.formattedDescription = formattedLines.join('\n');
            }
        }
    },
    mounted() {
        this.formatDescription();
    }
};
</script>
<style scoped>
.whitespace-break-spaces {
    white-space: pre-wrap;
    /* Respect line breaks */
}
</style>
