<template>
    <div class="overflow-auto">
        <div class="lg:flex items-center lg:space-x-2">
            <el-pagination v-if="showPagination" v-model:current-page="currentPage" v-model:page-size="pageSize"
                @size-change="handleSizeChange" @current-change="handlePagination"
                layout="total, sizes, prev, pager, next" :page-sizes="[100, 200, 400, 800]" :total="pagination.total"
                size="small" />
            <p class="text-xs text-gray37 mt-1 lg:mt-0">
                {{ products.length }} {{ products.length == 1 ? 'elemento' : 'elementos' }} en la tabla
            </p>
        </div>
        <el-table ref="tableRef" :data="products" @row-click="handleRowClick" max-height="500"
            :row-class-name="tableRowClassName" class="!w-full mx-auto">
            <el-table-column fixed label="Imagen" width="75">
                <template #default="scope">
                    <img v-if="scope.row.global_product_id ? scope.row.global_product?.media[0]?.original_url : scope.row.media[0]?.original_url"
                        class="size-10 bg-white object-contain rounded-md"
                        :src="scope.row.global_product_id ? scope.row.global_product?.media[0]?.original_url : scope.row.media[0]?.original_url">
                    <div v-else
                        class="size-10 bg-white text-gray99 rounded-md text-sm flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </div>
                </template>
            </el-table-column>
            <el-table-column fixed label="Nombre del producto" width="120">
                <template #default="scope">
                    <p>
                        {{ scope.row.global_product_id ? scope.row.global_product?.name : scope.row.name ?? '-' }}
                    </p>
                </template>
            </el-table-column>
            <el-table-column label="Código" width="118">
                <template #default="scope">
                    <div class="flex items-center space-x-2">
                        <p>
                            {{ scope.row.global_product_id ? scope.row.global_product?.code : scope.row.code ?? '-' }}
                        </p>
                        <el-tooltip v-if="scope.row.global_product_id ? scope.row.global_product?.code : scope.row.code"
                            content="Imprimir código" placement="right">
                            <button @click.stop="handleTicketPrinting(scope.row)"
                                class="flex items-center justify-center text-xs rounded-full text-gray37 bg-[#ededed] hover:bg-gray37 hover:text-grayF2 size-5 transition-all ease-in-out duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                </svg>
                            </button>
                        </el-tooltip>
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="Precio">
                <template #default="scope">
                    <div class="flex items-center">
                        <span>
                            ${{ scope.row.public_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                        </span>
                        <el-dropdown v-if="scope.row.promotions?.length" trigger="click">
                            <button type="button" @click.stop
                                class="flex items-center justify-center text-[#AE080B] hover:bg-[#F2F2F2] rounded-full size-6 transition-all duration-200 ease-in-out">
                                <svg width="11" height="16" viewBox="0 0 11 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4.28963 0C5.57502 1.0732 8.30754 3.47811 8.10018 8.09863C8.68001 7.68802 8.88776 7.19533 8.93416 5.7168C12.1304 10.8078 8.65907 14.7061 5.54061 15.1846C5.18681 15.2388 4.52926 15.2601 4.1119 15.125C-0.115441 13.7554 -1.60456 10.0636 2.14607 5.24023C4.57869 2.25074 4.74354 1.28426 4.28963 0ZM4.82479 7.44336C2.7427 10.1791 2.08598 12.5045 5.0035 14.0527C6.92255 13.582 7.62367 12.4453 7.80232 10.4805C7.42197 11.0109 7.17028 11.2522 6.49178 11.373C6.67028 9.76553 6.13695 8.6427 4.82479 7.44336Z"
                                        fill="currentColor" />
                                </svg>
                            </button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <main class="px-3 py-2 w-72 lg:w-[410px]">
                                        <section class="space-y-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <h1 class="font-semibold lg:text-sm ml-2">Producto con promoción
                                                </h1>
                                                <button type="button"
                                                    class="flex items-center justify-center size-[22px] rounded-full bg-[#F2F2F2] text-primary"
                                                    @click="handleEditPromo(scope.row)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <PromotionCard
                                                v-for="(promo, index) in scope.row.promotions.filter(p => !isExpired(p.expiration_date))"
                                                :key="index" :promo="promo" :product="scope.row" />
                                        </section>
                                        <section
                                            v-if="scope.row.promotions.filter(p => isExpired(p.expiration_date)).length"
                                            class="mt-4 space-y-1">
                                            <h1 class="text-[#6E6E6E] font-semibold lg:text-sm ml-2">
                                                Promociones vencidas
                                            </h1>
                                            <PromotionCard
                                                v-for="(promo, index) in scope.row.promotions.filter(p => isExpired(p.expiration_date))"
                                                :key="index" :promo="promo" :product="scope.row" />
                                        </section>
                                    </main>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="Existencias">
                <template #default="scope">
                    <p :class="scope.row.current_stock < scope.row.min_stock && isInventoryOn ? 'text-redDanger' : ''">
                        {{ scope.row.current_stock ?? '-' }}
                        <i v-if="scope.row.current_stock < scope.row.min_stock && isInventoryOn"
                            class="fa-solid fa-arrow-down mx-1 text-[11px]"></i>
                        <span v-if="scope.row.current_stock < scope.row.min_stock && isInventoryOn"
                            class="text-[11px]">Bajo
                            stock</span>
                    </p>
                </template>
            </el-table-column>
            <el-table-column prop="min_stock" label="Existencias minimas" width="190" />
            <el-table-column label="Categoría" :filters="categoryFilters" :filter-method="filterCategory">
                <template #default="scope">
                    <p>{{ scope.row.global_product_id ? scope.row.global_product?.category?.name :
                        scope.row.category?.name }}</p>
                </template>
            </el-table-column>
            <el-table-column label="Proveedor" :filters="brandFilters" :filter-method="filterBrand">
                <template #default="scope">
                    <p>{{ scope.row.global_product_id ? scope.row.global_product?.brand?.name : scope.row.brand?.name }}
                    </p>
                </template>
            </el-table-column>
            <el-table-column align="right">
                <template #default="scope">
                    <el-dropdown trigger="click" @command="handleCommand">
                        <button @click.stop
                            class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item :command="'see|' + scope.$index">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span class="text-xs">Ver</span>
                                </el-dropdown-item>
                                <el-dropdown-item :command="'edit|' + scope.$index">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                    </svg>
                                    <span class="text-xs">Editar</span>
                                </el-dropdown-item>
                                <el-dropdown-item :command="'promo|' + scope.$index">
                                    <svg width="11" height="15" viewBox="0 0 11 15" fill="none" class="size-[14px] mr-1"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.78461 1C7.07287 2.9909 7.84124 4.68937 8.14654 8.14518C8.6581 7.78291 8.84139 7.34822 8.88234 6.04375C11.7169 10.7181 8.58141 14.1213 5.88831 14.3969C5.57309 14.4162 4.99601 14.4636 4.6278 14.3443C0.89815 13.136 -0.415655 9.8788 2.89342 5.6233C5.03965 2.98575 5.18509 2.13307 4.78461 1ZM5.25676 7.56705C3.4198 9.98071 2.8404 12.0323 5.41443 13.3983C7.10749 12.983 7.72612 11.9801 7.88375 10.2466C7.54818 10.7146 7.32611 10.9275 6.7275 11.0341C6.885 9.61583 6.4144 8.62518 5.25676 7.56705Z"
                                            stroke="currentColor" stroke-width="0.882269" stroke-linejoin="round" />
                                    </svg>
                                    <span class="text-xs">Agregar promoción</span>
                                </el-dropdown-item>
                                <el-dropdown-item v-if="canDelete" :command="'delete|' + scope.$index">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-1 text-red-600">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    <span class="text-xs text-red-600">Eliminar</span>
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </template>
            </el-table-column>
        </el-table>
        <PrintingModal :show="showPrintingModal" @close="showPrintingModal = false" ref="printingModal" />
        <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
            <template #title>
                <h1>Eliminar producto</h1>
            </template>
            <template #content>
                <p>
                    Se eliminará de tu tienda el producto seleccionado, esto es un proceso irreversible. ¿Continuar
                    de todas formas?
                </p>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="showDeleteConfirm = false" :disabled="deleting">Cancelar</CancelButton>
                    <PrimaryButton @click="deleteItem" :disabled="deleting">Eliminar</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
    </div>
</template>

<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import { deleteItem, getItemByAttributes } from "@/dbService.js";
import { useForm } from '@inertiajs/vue3';
import emitter from '@/eventBus.js';
import PromotionCard from '../Promotions/PromotionCard.vue';
import { isPast, parseISO } from 'date-fns';
import PrintingModal from '../Sale/PrintingModal.vue';

export default {
    data() {
        const form = useForm({});

        return {
            form,
            showPrintingModal: false,
            deleting: false,
            showDeleteConfirm: false,
            itemToDelete: null,
            // control de inventario activado
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
            // Permisos de rol actual
            canDelete: ['Administrador'].includes(this.$page.props.auth.user.rol),
            currentPage: parseInt(this.pagination.current_page),
            pageSize: parseInt(this.pagination.per_page),
        };
    },
    emits: ['refresh-data'],
    components: {
        ConfirmationModal,
        PrimaryButton,
        CancelButton,
        PromotionCard,
        PrintingModal,
    },
    props: {
        products: Object,
        pagination: Object,
        showPagination: {
            type: Boolean,
            default: true,
        },
    },
    computed: {
        categoryFilters() {
            const categories = this.products
                .map(product => product.global_product_id ? product.global_product?.category?.name : product.category?.name)
                .filter(category => category); // Filtrar categorías vacías o null
            return [...new Set(categories), 'Sin categoría'].map(category => ({ text: category, value: category }));
        },
        brandFilters() {
            const categories = this.products
                .map(product => product.global_product_id ? product.global_product?.brand?.name : product.brand?.name)
                .filter(brand => brand); // Filtrar categorías vacías o null
            return [...new Set(categories), 'Sin proveedor'].map(brand => ({ text: brand, value: brand }));
        },
    },
    methods: {
        handleTicketPrinting(product) {
            const code = product.global_product_id ? product.global_product?.code : product.code;
            const name = product.global_product_id ? product.global_product?.name : product.name;
            if (code) {
                this.$refs.printingModal.setLabelMode();
                this.$refs.printingModal.customData = this.generateTSPLLabelCommands(code, name);
                this.showPrintingModal = true;
            }
        },
        generateTSPLLabelCommands(code, name) {
            // --- 1. Configuración de la Etiqueta ---
            // Define aquí las dimensiones de tu etiqueta en milímetros.
            const labelConfig = {
                widthMM: this.$page.props.auth.user.printer_config?.labelWidth,  // Ancho de la etiqueta en mm
                heightMM: this.$page.props.auth.user.printer_config?.labelHeight, // Alto de la etiqueta en mm
                gapMM: this.$page.props.auth.user.printer_config?.labelGap,     // Espacio entre etiquetas en mm
                dotsPerMM: Math.round(this.$page.props.auth.user.printer_config?.labelResolution / 25.4)  // Resolución de la impresora (203 dpi = 8 dots/mm)
            };

            // --- 2. Comandos Iniciales (¡La parte más importante!) ---
            // SIZE: Define el tamaño de la etiqueta.
            // GAP: Define la separación entre etiquetas. Esto corrige el problema de sobreimpresión.
            // CODEPAGE: Define la tabla de caracteres. 1252 es para Latin-1 (incluye acentos, ñ).
            // CLS: Limpia el búfer de la impresora antes de empezar a dibujar.
            let commands = '';
            commands += `SIZE ${labelConfig.widthMM} mm, ${labelConfig.heightMM} mm\n`;
            commands += `GAP ${labelConfig.gapMM} mm, 0 mm\n`;
            // commands += `COUNTRY 003\n`;
            // commands += `CODEPAGE 1252\n`;
            commands += `CLS\n`;

            // --- 3. Coordenadas y Diseño ---
            let currentY = 15; // Posición Y inicial (margen superior en dots)
            const startX = 15; // Posición X inicial (margen izquierdo en dots)
            const rightMargin = 15;
            const lineHeight = this.$page.props.auth.user.printer_config?.labelLineHeight; // Espacio entre líneas
            const font = `"${this.$page.props.auth.user.printer_config?.labelFont}"`; // Fuente a utilizar. Las comillas dobles son importantes.
            const fontAvgCharWidth = 12; // Ancho promedio de un carácter en dots. Ajusta según la fuente.

            const addTextLine = (label, value) => {
                if (!value) return; // No añadir si el valor está vacío

                let fullText = `${label} ${value}`;

                // --- CÁLCULO DINÁMICO DEL MÁXIMO DE CARACTERES ---
                // 1. Calcula el ancho total disponible para el texto en dots.
                const availableWidth = (labelConfig.widthMM * labelConfig.dotsPerMM) - startX - rightMargin;
                // 2. Calcula cuántos caracteres caben en ese espacio. Usamos Math.floor para redondear hacia abajo.
                const maxLength = Math.floor(availableWidth / fontAvgCharWidth);

                const textParts = [];
                while (fullText.length > maxLength) {
                    let chunk = fullText.substring(0, maxLength);
                    let lastSpace = chunk.lastIndexOf(' ');
                    if (lastSpace > 0) {
                        chunk = chunk.substring(0, lastSpace);
                    }
                    textParts.push(chunk);
                    fullText = fullText.substring(chunk.length).trim();
                }
                textParts.push(fullText);

                // Imprime cada parte del texto
                textParts.forEach(part => {
                    // Se usa TEXT y se escapa el contenido para evitar conflictos con comillas
                    commands += `TEXT ${startX},${currentY},${font},0,1,1,"${part.replace(/"/g, '\\"')}"\n`;
                    currentY += lineHeight;
                });
            };

            // --- Contenido de la Etiqueta ---
            addTextLine("", this.removeAccents(name));

            // --- Código de Barras ---
            const humanReadable = this.$page.props.auth.user.printer_config?.labelBarCodeHumanReadable || 0;

            // BARCODE X,Y,"TIPO",ALTURA,LEER_HUMANO,ROTACION,ANCHO_ESTRECHO,ANCHO_ANCHO,"CONTENIDO"
            const barcodeHeight = 22;    // Altura del código en dots
            const narrowWidth = 2;     // Ancho de la barra más estrecha
            const wideWidth = 5;       // Ancho de la barra más ancha

            // Centrar el código de barras (opcional)
            const barcodeX = startX;

            commands += `BARCODE ${barcodeX},${currentY},"128",${barcodeHeight},${humanReadable},0,${narrowWidth},${wideWidth},"${code}"\n`;
            currentY += barcodeHeight + 20; // Actualizar Y después del barcode

            // Usamos PRINT 1 para imprimir una sola copia de la etiqueta diseñada.
            commands += 'PRINT 1\n';

            // console.log("Comandos TSPL Generados:\n", commands); // Útil para depuración
            return commands;
        },
        removeAccents(text = '') {
            if (!text) return '';
            // cambiar ñ por n
            text = text.replace(/ñ/g, 'n').replace(/Ñ/g, 'N');
            // Normalizar el texto y eliminar los acentos
            return text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        },
        handleSizeChange() {
            // reiniciar la pagina a 1
            this.currentPage = 1;
            this.$emit('refresh-data', this.currentPage, this.pageSize);
        },
        handlePagination(val) {
            this.$emit('refresh-data', val, this.pageSize);
        },
        filterCategory(category, row) {
            let productCategory = row.global_product_id ? row.global_product?.category?.name : row.category?.name;
            if (!productCategory) {
                productCategory = 'Sin categoría';
            }
            return productCategory == category;
        },
        filterBrand(brand, row) {
            let productBrand = row.global_product_id ? row.global_product?.brand?.name : row.brand?.name;
            if (!productBrand) {
                productBrand = 'Sin proveedor';
            }
            return productBrand == brand;
        },
        handleRowClick(row) {
            this.handleShow(row);
        },
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-[10px] lg:text-xs';
        },
        isExpired(date) {
            if (!date) return false; // Si no hay fecha, no está vencida
            // Convierte la fecha a objeto Date si es string
            const dateObj = typeof date === 'string' ? parseISO(date) : date;
            return isPast(dateObj);
        },
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const index = command.split('|')[1];
            const product = this.products[index];

            if (commandName == 'see') {
                this.handleShow(product);
            } else if (commandName == 'edit') {
                this.handleEdit(product);
            } else if (commandName == 'promo') {
                if (!product.promotions.length) {
                    this.handleCreatePromo(product);
                } else {
                    this.handleEditPromo(product);
                }
            } else if (commandName == 'delete') {
                this.showDeleteConfirm = true;
                this.itemToDelete = product;
            }
        },
        handleEdit(product) {
            const encodedId = btoa(product.id.toString());
            if (product.global_product_id) {
                this.$inertia.get(route('global-product-store.edit', encodedId));
            } else {
                this.$inertia.get(route('products.edit', encodedId))
            }
        },
        handleCreatePromo(product) {
            const encodedId = btoa(product.id.toString());
            if (product.global_product_id) {
                this.$inertia.get(route('promotions.global.create', encodedId));
            } else {
                this.$inertia.get(route('promotions.local.create', encodedId));
            }
        },
        handleEditPromo(product) {
            const encodedId = btoa(product.id.toString());
            if (product.global_product_id) {
                this.$inertia.get(route('promotions.global.edit', encodedId));
            } else {
                this.$inertia.get(route('promotions.local.edit', encodedId));
            }
        },
        handleShow(product) {
            const encodedId = btoa(product.id.toString());
            if (product.global_product_id) {
                this.$inertia.get(route('global-product-store.show', encodedId));
            } else {
                this.$inertia.get(route('products.show', encodedId))
            }
        },
        deleteItem() {
            let routePage;
            if (this.itemToDelete.global_product_id) {
                routePage = 'global-product-store.destroy';
            } else {
                routePage = 'products.destroy';
            }
            this.deleting = true;
            this.form.delete(route(routePage, this.itemToDelete.id), {
                onSuccess: async () => {
                    // Emitir el evento personalizado
                    emitter.emit('product-deleted');

                    let productName;
                    if (this.itemToDelete.global_product_id) {
                        productName = this.itemToDelete.global_product.name;
                        const indexToDelete = this.products.findIndex(item => item.global_product?.name == this.itemToDelete.global_product?.name);
                        this.products.splice(indexToDelete, 1);
                    } else {
                        productName = this.itemToDelete.name;
                        const indexToDelete = this.products.findIndex(item => item.id == this.itemToDelete.id);
                        this.products.splice(indexToDelete, 1);
                    }
                    // buscar producto en indexedDB
                    const products = await getItemByAttributes('products', { name: productName });
                    // eliminar de indexedDB
                    await deleteItem('products', products[0].id);
                    this.showDeleteConfirm = false;
                    this.$notify({
                        title: 'Correcto',
                        message: '',
                        type: 'success',
                    });
                },
                onError: () => {
                    console.log(error);
                    this.$notify({
                        title: 'El servidor no pudo procesar la petición',
                        message: 'No se pudo eliminar el producto. Intente más tarde o si el problema persiste, contacte a soporte',
                        type: 'error',
                    });
                },
                onFinish: () => {
                    this.deleting = false;
                }
            });
        },
    },

}
</script>