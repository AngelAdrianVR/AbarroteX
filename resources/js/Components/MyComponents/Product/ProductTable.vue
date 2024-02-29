<template>
    <div v-if="products.length" class="w-full mx-auto text-[11px] md:text-sm overflow-auto">
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
            <div v-for="product in products" :key="product.id" class="*:px-2 *:py-1 cursor-pointer flex items-center space-x-4 border rounded-full mb-2" 
            @click="$inertia.get(route('products.show', product.id))">
                <div class="hidden md:block w-[10%] h-14 rounded-l-full">
                    <img class="mx-auto h-12 object-contain rounded-full" :src="product.imageCover[0]?.original_url">
                </div>
                <div class="w-[18%] md:w-[13%]">{{ product.code }}</div>
                <div class="w-[35%] md:w-[30%]">{{ product.name }}</div>
                <div class="w-[10%]">${{ product.public_price }}</div>
                <div :class="product.current_stock < product.min_stock ? 'text-primary' : ''" class="w-[10%]">
                    {{ product.current_stock }}
                    <i v-if="product.current_stock < product.min_stock" class="fa-solid fa-arrow-down mx-1 text-[11px]"></i>
                    <span v-if="product.current_stock < product.min_stock" class="text-[11px]">Bajo stock</span>
                </div>
                <div class="w-[10%]">{{ product.min_stock }}</div>
                <div class="rounded-r-full w-[17%] text-right">
                    <i @click.stop="$inertia.get(route('products.edit', product.id))" class="fa-solid fa-pencil text-primary cursor-pointer hover:bg-gray-200 rounded-full mr-1 p-2"></i>
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