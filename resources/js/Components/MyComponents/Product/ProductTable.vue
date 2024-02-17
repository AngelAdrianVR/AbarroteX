<template>
    <div v-if="products.length" class="w-full mx-auto text-[11px] lg:text-sm overflow-auto">
        <div class="text-center lg:text-base grid grid-cols-7 mb-2">
            <div class=""></div>
            <div class="font-bold pb-3 pl-2 text-left w-20">ID</div>
            <div class="font-bold pb-3 text-left">Nombre de producto</div>
            <div class="font-bold pb-3 text-left">Precio</div>
            <div class="font-bold pb-3 text-left">Existencias</div>
            <div class="font-bold pb-3 text-left">Existencias mínimas</div>
            <div class=""></div>
        </div>
        <div>
            <div v-for="product in products" :key="product.id" class="*:px-2 *:py-1 cursor-pointer grid grid-cols-7 border rounded-full items-center mb-2" 
            @click="$inertia.get(route('products.show', product.id))">
                <div class="w-16 lg:w-40 rounded-l-full">
                    <img class="mx-auto w-12 lg:w-16 object-contain" :src="product.imageCover[0]?.original_url" alt="">
                </div>
                <div class="">{{ product.id }}</div>
                <div class="">{{ product.name }}</div>
                <div class="">${{ product.public_price }}</div>
                <div :class="product.current_stock < product.min_stock ? 'text-primary' : ''" class="">
                    {{ product.current_stock }}
                    <i v-if="product.current_stock < product.min_stock" class="fa-solid fa-arrow-down mx-2"></i>
                    <span v-if="product.current_stock < product.min_stock">Bajo stock</span>
                </div>
                <div class="">{{ product.min_stock }}</div>
                <div class="rounded-r-full">
                    <i @click.stop="$inertia.get(route('products.edit', product.id))" class="fa-solid fa-pencil text-primary cursor-pointer hover:bg-gray-200 rounded-full mr-3 p-2"></i>
                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?" @confirm="deleteItem(product.id)">
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
    async deleteItem(productId) {
        try {
            const response = await axios.delete(route('products.destroy', productId));
            if (response.status == 200) {
                const indexToDelete = this.products.findIndex(item => item.id == productId);
                this.products.splice(indexToDelete, 1);

                ElNotification({
                title: 'Success',
                message: 'Se ha eliminado el producto',
                type: 'success',
                position: 'bottom-right',
            });
            }
        } catch (error) {
            console.log(error);
            ElNotification({
                title: 'Error',
                message: 'No se pudo eliminar el producto. Inténte más tarde',
                type: 'error',
                position: 'bottom-right',
            });
        }
    }
}
}
</script>