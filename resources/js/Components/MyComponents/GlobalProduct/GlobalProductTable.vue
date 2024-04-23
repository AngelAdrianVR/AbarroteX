<template>
    <div v-if="products.length" class="w-full mx-auto text-[11px] md:text-sm overflow-auto">
        <div class="text-center md:text-base flex items-center space-x-4 mb-2">
            <div class="hidden md:block w-[10%]"></div>
            <div class="font-bold pb-3 pl-2 text-left w-[18%] md:w-[13%]">Código</div>
            <div class="font-bold pb-3 text-left w-[30%] md:w-[20%]">Nombre de producto</div>
            <div class="font-bold pb-3 pl-2 text-left w-[18%] md:w-[13%]">Categoría</div>
            <div class="font-bold pb-3 text-left w-[10%]">Precio</div>
            <div class="w-[17%]"></div>
        </div>
        <div>
            <div v-for="product in products" :key="product.id"
                class="*:px-2 *:py-1 cursor-pointer flex items-center space-x-4 border rounded-full mb-2 hover:border-primary"
                @click="$inertia.get(route('global-products.show', product.id))">
                <div class="hidden md:block w-[10%] h-14 rounded-l-full">
                    <img class="mx-auto h-12 object-contain rounded-lg" :src="product.media[0]?.original_url">
                </div>
                <div class="w-[18%] md:w-[13%]">{{ product.code ?? 'N/A' }}</div>
                <div class="w-[30%] md:w-[20%]">{{ product.name }}</div>
                <div class="w-[18%] md:w-[13%]">{{ product.category?.name }}</div>
                <div class="w-[10%]">${{ product.public_price }}</div>
                <div class="rounded-r-full w-[17%] text-right">
                    <i @click.stop="$inertia.get(route('global-products.edit', product.id))"
                        class="fa-solid fa-pencil text-primary cursor-pointer hover:bg-gray-200 rounded-full mr-1 p-2"></i>
                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303"
                        title="¿Continuar?" @confirm="deleteItem(product.id)">
                        <template #reference>
                            <i @click.stop
                                class="fa-regular fa-trash-can text-primary cursor-pointer hover:bg-gray-200 rounded-full p-2"></i>
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
    components: {

    },
    props: {
        products: Object
    },
    methods: {
        async deleteItem(productId) {
            try {
                const response = await axios.delete(route('global-products.destroy', productId));
                if (response.status == 200) {
                    const indexToDelete = this.products.findIndex(item => item.id == productId);
                    this.products.splice(indexToDelete, 1);

                    this.notify({
                        title: 'Correcto',
                        message: 'Se ha eliminado el producto',
                        type: 'success',
                    });
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'El servidor no pudo procesar la petición',
                    message: 'No se pudo eliminar el producto. Intente más tarde o si el problema persiste, contacte a soporte',
                    type: 'error',
                });
            }
        }
    }
}
</script>