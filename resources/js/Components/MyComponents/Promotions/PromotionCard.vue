<template>
    <article class="bg-[#FAFAFA] rounded-lg border border-grayD9 px-2 py-1" :class="{
        'text-gray99': isExpired(promo.expiration_date),
        'bg-[#DFFECC]': applied,
    }">
        <div>
            <div class="flex items-center justify-between">
                <h2 class="font-bold" :class="isExpired(promo.expiration_date) ? 'text-gray99' : 'text-gray37'">
                    {{ promo.type }}
                </h2>
                <p class="text-[10px] text-[#999999]">
                    {{ promo.expiration_date
                        ? isExpired(promo.expiration_date)
                            ? 'Venció ' + formatDate(promo.expiration_date)
                            : 'Vence ' + formatDate(promo.expiration_date)
                        : 'Sin fecha de vencimiento' }}
                </p>
            </div>
            <p v-if="promo.type == 'Descuento en precio fijo'">
                De ${{
                    product.public_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                        ",") }}
                a <b>${{
                    promo.discounted_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                        ",") }}</b>
            </p>
            <p v-if="promo.type == 'Descuento en porcentaje'">
                <b>{{ promo.discount }}%</b> de descuento
            </p>
            <p v-if="promo.type == 'Precio especial por paquete'">
                <b>{{ promo.pack_quantity }}</b>
                por
                <b>${{
                    promo.pack_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                        ",")
                }}</b>
            </p>
            <p v-if="promo.type == 'Promoción tipo 2x1 o 3x2'">
                Compra
                <b>{{ promo.buy_quantity }}</b>
                paga solo
                <b>{{ promo.pay_quantity }}</b>
            </p>
            <p v-if="promo.type == 'Producto gratis al comprar otro'">
                Compra
                <b>{{ promo.min_quantity_to_gift }}</b>,
                llévate gratis
                <b>
                    {{ promo.quantity_to_gift }}
                    <el-tooltip v-if="showGiftable" placement="right" effect="light">
                        <template #content>
                            <figure class="flex items-center justify-center min-w-40">
                                <img v-if="mediaURL(promo)" class="object-contain h-28 select-none" :draggable="false"
                                    :src="mediaURL(promo)">
                                <div v-else class="mt-2 flex flex-col items-center bg-white text-gray99 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="0.8" stroke="currentColor" class="size-10">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                    <p class="text-[10px]">Producto sin
                                        imagen</p>
                                </div>
                            </figure>
                            <button type="button" @click="handleShowRoute(promo.giftable)"
                                class="underline text-primary flex items-center space-x-1 mt-2 ml-auto">
                                <span class="text-[10px]">Ir al
                                    producto</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                        </template>
                        <span class="text-primary underline">
                            {{ promo.giftable.global_product
                                ? promo.giftable.global_product.name
                                : promo.giftable.name }}
                        </span>
                    </el-tooltip>
                    <span v-else>
                        {{ promo.giftable.global_product
                            ? promo.giftable.global_product.name
                            : promo.giftable.name }}
                    </span>
                </b>
            </p>
            <p v-if="applied" class="text-[#189203] flex itesmcenter space-x-2 justify-end">
                <span>Promoción aplicada</span>
                <svg width="13" height="18" viewBox="0 0 13 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M3.06491 10.2767C2.13723 7.62097 0.120527 5.5086 0.00643846 5.38317C-0.10765 5.25773 1.30629 5.01952 3.30958 7.09591C7.10698 2.79673 10.7883 -0.0115113 12.852 0.000263853C12.9315 -0.00783592 13.0535 0.172705 12.9743 0.244941C8.40514 2.42053 5.87579 6.28608 3.6766 10.2767C3.66333 10.3976 3.05966 10.3736 3.06491 10.2767Z"
                        fill="#189203" />
                </svg>
            </p>
        </div>
    </article>
</template>
<script>
import { isPast, parseISO, format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    name: 'PromotionCard',
    props: {
        promo: {
            type: Object,
            required: true
        },
        product: {
            type: Object,
            required: true
        },
        showGiftable: {
            type: Boolean,
            default: true
        },
        applied: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        mediaURL(product) {
            if (product.giftable.global_product) {
                // Si el producto es un global_product, retorna la URL de la imagen del global_product
                return product.giftable.global_product.media.length > 0
                    ? product.giftable.global_product.media[0].original_url
                    : null;
            } else if (product.giftable.media && product.giftable.media.length > 0) {
                // Si el producto es un producto normal, retorna la URL de la imagen del producto
                return product.giftable.media[0].original_url;
            } else {
                return null; // Retorna null si no hay imagen
            }
        },
        formatDate(date) {
            return format(new Date(date), 'dd MMMM yyyy', { locale: es });
        },
        handleShowRoute(product) {
            // !Por el momento no funciona para redirigir desde punto de venta para productos en indexDB!
            // Ya que no existe global_product_id
            let url;
            const encodedId = btoa(product.id.toString());
            if (product.global_product_id) {
                url = route('global-product-store.show', encodedId);
            } else {
                url = route('products.show', encodedId);
            }

            // ir a ruta en pestaña nueva
            window.open(url, '_blank');
        },
        isExpired(date) {
            if (!date) return false; // Si no hay fecha, no está vencida
            // Convierte la fecha a objeto Date si es string
            const dateObj = typeof date === 'string' ? parseISO(date) : date;
            return isPast(dateObj);
        },
    }
}
</script>