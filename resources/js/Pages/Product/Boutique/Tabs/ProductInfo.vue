<template>
    <div class="text-sm">
        <div class="lg:flex justify-between items-center">
            <div class="md:flex space-y-1 md:space-x-4 items-center">
                <p class="text-gray37">Categoría: <span class="font-bold">{{ products[0].category?.name ?? 'N/A'
                }}</span></p>
            </div>
        </div>
        <p class="text-gray37 mt-3">Fecha de alta: <strong class="ml-5">
                {{ formatDateTime(products[0].created_at) }}</strong></p>
        <p v-if="$page.props.auth.user.store.activated_modules?.includes('Tienda en línea')"
            class="text-gray37 font-bold mt-3">
            {{ products[0].show_in_online_store
                ? 'Este producto está visible en la tienda en línea'
                : 'Este producto no está visible en la tienda en línea' }}
        </p>
        <h1 class="font-bold text-lg lg:text-xl my-2 lg:mt-4">{{ products[0].name }}</h1>
        <div class="xl:w-1/2 mt-3 lg:mt-3 space-y-2">
            <div v-if="canSeeCost" class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                <p class="text-gray37">Precio de compra:</p>
                <p class="text-right font-bold">{{ products[0].cost ? '$' +
                    products[0].cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '-' }}
                </p>
            </div>
            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                <p class="text-gray37">Precio de venta: </p>
                <p class="text-right font-bold">${{
                    products[0].public_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                        ",") }}</p>
            </div>
            <div v-if="products[0].description">
                <h2 class="mt-3 ml-5 font-bold text-lg">Sobre el producto</h2>
                <div>
                    <p class="whitespace-break-spaces">{{ formattedDescription }}</p>
                </div>
            </div>
        </div>
        <div class="xl:mt-3 overflow-auto">
            <table class="table-fixed w-full border border-grayD9">
                <thead>
                    <tr class="*:text-start *:px-3 bg-grayF2 text-gray37">
                        <th class="w-44 xl:w-[27%]">Colores</th>
                        <th class="w-44 xl:w-[27%]">Tallas</th>
                        <th class="w-44 xl:w-[28%]">Códigos</th>
                        <th class="w-32 xl:w-[15%]">Existencias</th>
                        <th v-if="products[0].has_inventory_control" class="w-32 xl:w-[16%]">Cantidad mínima</th>
                        <th v-if="products[0].has_inventory_control" class="w-32 xl:w-[16%]">Cantidad máxima</th>
                    </tr>
                </thead>
                <tbody class="divide-y-[1px]">
                    <tr v-for="(product, colorIndex) in groupedProductsByColor" :key="colorIndex" class="*:px-3 *:py-1">
                        <!-- Render the color name and first size for each color group -->
                        <td>
                            {{ product.additional.color.name }}
                        </td>
                        <td>
                            <div class="flex space-x-2 items-center">
                                <span>{{ product.additional.size.name }}</span>
                                <span v-if="product.additional.size.short" class="text-gray99 text-xs">({{
                                    product.additional.size.short }})</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center">
                                <span>{{ product.code ?? '-' }}</span>
                                <el-tooltip v-if="product.code" content="Copiar código" placement="right">
                                    <button @click="copyToClipboard(product)"
                                        class="flex items-center justify-center ml-3 text-xs rounded-full text-gray37 bg-[#ededed] hover:bg-gray37 hover:text-grayF2 size-6 transition-all ease-in-out duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                        </svg>
                                    </button>
                                </el-tooltip>
                            </div>
                        </td>
                        <td>
                            {{ product.current_stock }}
                        </td>
                        <td v-if="product.min_stock">{{ product.min_stock }}</td>
                        <td v-if="product.max_stock">{{ product.max_stock }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

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
        products: Array,
    },
    computed: {
        groupedProductsByColor() {
            const grouped = {};
            this.products.forEach(product => {
                const color = product.additional.color.name;
                if (!grouped[color]) {
                    grouped[color] = [];
                }
                grouped[color].push(product);
            });

            // console.log(Object.values(grouped).flat());
            return Object.values(grouped).flat();
        },
        // groupedProductsByColor() {
        //     const grouped = {};
        //     this.products.forEach(product => {
        //         const color = product.additional.color.name;
        //         if (!grouped[color]) {
        //             grouped[color] = {
        //                 color: product.additional.color,
        //                 products: []
        //             };
        //         }
        //         grouped[color].products.push(product);
        //     });

        //     console.log(Object.values(grouped))
        //     return Object.values(grouped);
        // }
    },
    methods: {
        formatDateTime(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy • hh:mm a', { locale: es });
        },
        copyToClipboard(product) {
            const textToCopy = product.code;

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
                message: product.code + " copiado",
                type: "success",
            });
        },
        formatDescription() {
            if (this.products[0].description != null) {
                const text = this.products[0].description;
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
