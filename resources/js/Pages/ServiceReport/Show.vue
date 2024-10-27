<template>
    <Head :title="'RS-' + report.folio" />
    <header class="flex justify-between">
        <figure>
            <img src="@/../../public/images/DMCompresoresLogo.png" alt="Logo de DM compresores">
        </figure>
        <div>
            <p class="flex items-center justify-between w-56">
                <span>Orden de servicio:</span>
                <span>{{ String(report.folio).padStart(4, '0') }}</span>
            </p>
            <p class="flex items-center justify-between">
                <span>Fecha del servicio:</span>
                <span>{{ formatDate(report.service_date) }}</span>
            </p>
        </div>
    </header>
    <main>

    </main>

</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import { Head, Link } from '@inertiajs/vue3';

export default {
    data() {
        return {
            indexEdit: null,
            showEditIcon: true,
        }
    },
    components: {
        PrimaryButton,
        Head,
        Link
    },
    props: {
        report: Object
    },
    methods: {
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        handleEditDescription() {
            this.indexEdit = null;
        },
        print() {
            this.showEditIcon = false;
            setTimeout(() => {
                window.print();
            }, 100);
        },
        handleAfterPrint() {
            this.showEditIcon = true;
        },
    },
    mounted() {
        window.addEventListener('afterprint', this.handleAfterPrint);
    },
    beforeDestroy() {
        window.removeEventListener('afterprint', this.handleAfterPrint);
    }
}
</script>
