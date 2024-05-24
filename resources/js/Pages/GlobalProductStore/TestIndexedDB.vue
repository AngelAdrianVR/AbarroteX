<template>
    <div>
        <h1>Products</h1>
        <div v-if="products.length === 0">No products available</div>
        <ul v-else>
            <li v-for="product in products" :key="product.id">
                <h2>{{ product.name }}</h2>
                <p>Code: {{ product.code }}</p>
                <p>Price: {{ product.public_price }}</p>
                <p>Stock: {{ product.current_stock }}</p>
                <div v-if="product.imageUrl">
                    <p>url: {{ product.imageUrl }}</p>
                    <img :src="product.imageUrl" :alt="product.name" style="max-width: 200px;">
                </div>
                <div v-else>
                    <p>No image available</p>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
import { openDatabase, getAll } from '@/dbService';

export default {
    data() {
        return {
            products: []
        };
    },
    methods: {
        async syncProducts() {
            await openDatabase();
            const products = await getAll('products');
            const productsWithImageUrls = products.map(product => {
                if (product.image) {
                    const imageUrl = URL.createObjectURL(product.image);
                    return { ...product, imageUrl };
                }
                return product;
            });
            this.products = productsWithImageUrls;
        }
    },
    mounted() {
        this.syncProducts();
    },
    beforeDestroy() {
        this.products.forEach(product => {
            if (product.imageUrl) {
                URL.revokeObjectURL(product.imageUrl);
            }
        });
    }
};
</script>