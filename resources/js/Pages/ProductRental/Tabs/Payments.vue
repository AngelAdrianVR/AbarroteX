<template>
    <Loading v-if="loading" class="mt-20" />
    <div v-else>
        
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ProductTable from '@/Components/MyComponents/Product/ProductTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import FileUploader from '@/Components/MyComponents/FileUploader.vue';
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import DialogModal from "@/Components/DialogModal.vue";
import { useForm } from "@inertiajs/vue3";
import axios from 'axios';
import { addOrUpdateBatchOfItems } from '@/dbService.js';

export default {
    data() {
        return {
            // carga
            loading: false,
        };
    },
    components: {
        PrimaryButton,
        CancelButton,
        ProductTable,
        ThirthButton,
        InputError,
        InputLabel,
        Loading,
        Modal,
        DialogModal,
        FileUploader,
    },
    props: {
    },
    methods: {
        
    },
    mounted() {
       
    }
}
</script>