<template>
    <div class="overflow-auto">
        <table v-if="Object.keys(products)?.length" class="w-full table-fixed">
            <thead>
                <tr class="*:text-start *:pb-2 *:px-4 *:text-sm border-b border-primary">
                    <th class="w-40 md:w-[10%]"></th>
                    <th class="w-36 md:w-[15%]">Código</th>
                    <th class="w-44 md:w-[20%]">Nombre de producto</th>
                    <th class="w-20 md:w-[15%]">Precio</th>
                    <th class="w-32 md:w-[15%]">Existencias</th>
                    <th class="w-32 md:w-[15%]">Existencias mínimas</th>
                    <th class="w-16 md:w-[10%]"></th>
                </tr>
            </thead>
            <tbody>
                <tr @click="handleShow(product)" v-for="(product, index) in products" :key="product.id"
                    class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight cursor-pointer">
                    <td class="rounded-s-full">
                        <img v-if="product.global_product_id ? product.global_product?.media[0]?.original_url : product.media[0]?.original_url"
                            class="size-10 bg-white object-contain rounded-md"
                            :src="product.global_product_id ? product.global_product?.media[0]?.original_url : product.media[0]?.original_url">
                        <div v-else
                            class="size-10 bg-white text-gray99 rounded-md text-sm flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </div>
                    </td>
                    <td>
                        {{ product.global_product_id ? product.global_product?.code : product.code ?? 'N/A' }}
                    </td>
                    <td>
                        {{ product.global_product_id ? product.global_product?.name : product.name }}
                    </td>
                    <td>
                        ${{ product.public_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                    </td>
                    <td>
                        <p :class="product.current_stock < product.min_stock && isInventoryOn ? 'text-redDanger' : ''">
                            {{ product.current_stock ?? '-' }}
                            <i v-if="product.current_stock < product.min_stock && isInventoryOn"
                                class="fa-solid fa-arrow-down mx-1 text-[11px]"></i>
                            <span v-if="product.current_stock < product.min_stock && isInventoryOn"
                                class="text-[11px]">Bajo
                                stock</span>
                        </p>
                    </td>
                    <td>
                        {{ product.min_stock ?? '-' }}
                    </td>
                    <td class="rounded-e-full text-end">
                        <el-dropdown trigger="click" @command="handleCommand">
                            <button @click.stop
                                class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item :command="'see|' + index">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <span class="text-xs">Ver</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'edit|' + index">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                        <span class="text-xs">Editar</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item v-if="canDelete" :command="'delete|' + index">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-[14px] mr-2 text-red-600">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        <span class="text-xs text-red-600">Eliminar</span>
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </td>
                </tr>
            </tbody>
        </table>
        <el-empty v-else description="No hay productos registrados" />

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
import axios from 'axios';
import { deleteItem, getItemByAttributes } from "@/dbService.js";

export default {
    data() {
        return {
            deleting: false,
            showDeleteConfirm: false,
            itemToDelete: null,
            // control de inventario activado
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
            // Permisos de rol actual
            canDelete: ['Administrador'].includes(this.$page.props.auth.user.rol),
        };
    },
    components: {
        ConfirmationModal,
        PrimaryButton,
        CancelButton,
    },
    props: {
        products: Object
    },
    methods: {
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const index = command.split('|')[1];
            const product = this.products[index];

            if (commandName == 'see') {
                this.handleShow(product);
            } else if (commandName == 'edit') {
                this.handleEdit(product);
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
        handleShow(product) {
            const encodedId = btoa(product.id.toString());
            if (product.global_product_id) {
                this.$inertia.get(route('global-product-store.show', encodedId));
            } else {
                this.$inertia.get(route('products.show', encodedId))
            }
        },
        async deleteItem() {
            let routePage;
            if (this.itemToDelete.global_product_id) {
                routePage = 'global-product-store.destroy';
            } else {
                routePage = 'products.destroy';
            }
            try {
                this.deleting = true;
                const response = await axios.delete(route(routePage, this.itemToDelete.id));
                if (response.status === 200) {
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
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'El servidor no pudo procesar la petición',
                    message: 'No se pudo eliminar el producto. Intente más tarde o si el problema persiste, contacte a soporte',
                    type: 'error',
                });
            } finally {
                this.deleting = false;
            }
        }
    }
}
</script>