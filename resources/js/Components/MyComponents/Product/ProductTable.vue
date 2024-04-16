<template>
    <div v-if="Object.keys(products)?.length" class="w-full mx-auto text-[11px] md:text-sm overflow-auto">
        <div class="text-center md:text-base flex items-center space-x-4 mb-2">
            <div class="hidden md:block w-[10%]"></div>
            <div class="font-bold pb-3 pl-2 text-left w-[18%] md:w-[13%]">Código</div>
            <div class="font-bold pb-3 text-left w-[35%] md:w-[30%]">Nombre de producto</div>
            <div class="font-bold pb-3 text-left w-[10%]">Precio</div>
            <div class="font-bold pb-3 text-left w-[10%]">Existencias</div>
            <div class="font-bold pb-3 text-left w-[10%]">Existencias mínimas</div>
            <div class="w-[17%]"></div>
        </div>
        <div>
            <div v-for="product in products" :key="product.id" class="*:px-2 *:py-1 cursor-pointer flex items-center hover:border-primary space-x-4 border rounded-full mb-2" 
            @click="handleShow(product)">
                <div class="hidden md:block w-[10%] h-14 rounded-l-full">
                    <img class="mx-auto h-12 object-contain rounded-lg" 
                        :src="product.global_product_id ? product.global_product?.media[0]?.original_url : product.media[0]?.original_url">
                </div>
                <div class="w-[18%] md:w-[13%]">{{ product.global_product_id ? product.global_product?.code : product.code }}</div>
                <div class="w-[35%] md:w-[30%]">{{ product.global_product_id ? product.global_product?.name : product.name }}</div>
                <div class="w-[10%]">${{ product.public_price }}</div>
                <div :class="product.current_stock < product.min_stock ? 'text-primary' : ''" class="w-[10%]">
                    {{ product.current_stock ?? '-' }}
                    <i v-if="product.current_stock < product.min_stock" class="fa-solid fa-arrow-down mx-1 text-[11px]"></i>
                    <span v-if="product.current_stock < product.min_stock" class="text-[11px]">Bajo stock</span>
                </div>
                <div class="w-[10%]">{{ product.min_stock ?? '-' }}</div>
                <div class="rounded-r-full w-[17%] text-right">
                    <i @click.stop="handleEdit(product)" class="fa-solid fa-pencil text-primary cursor-pointer hover:bg-gray-200 rounded-full mr-1 p-2"></i>
                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?" @confirm="deleteItem(product)">
                        <template #reference>
                            <i @click.stop class="fa-regular fa-trash-can text-primary cursor-pointer hover:bg-gray-200 rounded-full p-2"></i>
                        </template>
                    </el-popconfirm>
                </div>
            </div>
        </div>
    </div>
    <el-empty v-else description="No hay productos registrados" />
</template>

<script>
import { ElNotification } from 'element-plus'
import axios from 'axios';

export default {
data() {
    return {

    };
},
components:{

},
props:{
products: Object
},
methods:{
    handleEdit(product) {
        if ( product.global_product_id ) {
            this.$inertia.get(route('global-product-store.edit', product.id ));
        } else {
            this.$inertia.get(route('products.edit', product.id))
        }
    },
    handleShow(product) {
        if ( product.global_product_id ) {
            this.$inertia.get(route('global-product-store.show', product.id ));
        } else {
            this.$inertia.get(route('products.show', product.id))
        }
    },
    async deleteItem(product) {
        let routePage;
    
        if ( product.global_product_id ) {
            routePage = 'global-product-store.show';
        } else {
            routePage = 'products.show';
        }
        try {
            const response = await axios.delete(route(routePage, product.id));
            if (response.status == 200) {
                if ( product.global_product_id ) {
                    const indexToDelete = this.products.findIndex(item => item.global_product?.name == product.global_product?.name);
                    this.products.splice(indexToDelete, 1);
                } else {
                    const indexToDelete = this.products.findIndex(item => item.name == product.id);
                    this.products.splice(indexToDelete, 1);
                }

                ElNotification({
                title: 'Correcto',
                message: 'Se ha eliminado el producto',
                type: 'success',
                position: 'top-right',
            });
            }
        } catch (error) {
            console.log(error);
            ElNotification({
                title: 'Error',
                message: 'No se pudo eliminar el producto. Inténte más tarde',
                type: 'error',
                position: 'top-right',
            });
        }
    }
}
}
</script>