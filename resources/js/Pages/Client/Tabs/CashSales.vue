<template>
    <section>
        <header class="relative flex items-center justify-between space-x-3">
            <!-- Imprimir historial -->
            <button @click.stop="print"
                class="border border-[#D9D9D9] rounded-full py-1 px-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                </svg>
                <p class="text-sm ml-2">Imprimir historial</p>
            </button>

            <!-- filtro -->
            <button @click.stop="showFilter = !showFilter"
                class="border border-[#D9D9D9] rounded-full py-1 px-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="16" width="16"
                    id="Filter-Sort-Lines-Descending--Streamline-Ultimate">
                    <desc>Filter Sort Lines Descending Streamline Icon: https://streamlinehq.com</desc>
                    <defs></defs>
                    <title>filter</title>
                    <path d="M0.73 4.2791H23.27" fill="none" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="1"></path>
                    <path d="M3.131 9.426H20.869" fill="none" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="1"></path>
                    <path d="M8.7141 19.7209H15.2859" fill="none" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="1"></path>
                    <path d="M5.531 14.573H18.469" fill="none" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="1"></path>
                </svg>
                <p class="text-sm ml-2">Filtrar</p>
            </button>
            <div v-if="showFilter"
                class="absolute top-9 right-0 lg:-left-64 border border[#D9D9D9] rounded-md p-4 bg-white shadow-lg z-10 w-80">
                <div class="mb-3">
                    <InputLabel value="Rango de fechas" class="ml-3 mb-1" />
                    <div v-if="isMobile" class="flex items-center space-x-2">
                        <el-date-picker @change="handleStartDateChange" :disabled-date="disabledPrevDays"
                            v-model="startDate" type="date" class="!w-1/2" placeholder="Inicio" size="small" />
                        <el-date-picker @change="handleFinishDateChange" :disabled-date="disabledNextDays"
                            v-model="finishDate" type="date" class="!w-1/2" placeholder="Final" size="small" />
                    </div>
                    <div v-else>
                        <el-date-picker v-model="searchDate" type="daterange" range-separator="A"
                            start-placeholder="Fecha de inicio" end-placeholder="Fecha de fin" class="!w-full" />
                    </div>
                </div>
                <div class="flex space-x-2">
                    <PrimaryButton @click="searchSales" class="!py-1"
                        :disabled="isMobile ? !(startDate && finishDate) : !searchDate">
                        Aplicar
                    </PrimaryButton>
                    <ThirthButton @click="cleanFilter" class="!py-1">Limpiar</ThirthButton>
                </div>
            </div>
        </header>

        <!-- Ventas -->
        <!-- <main class="flex flex-col space-y-5 lg:mx-16 mt-10">
            <SaleDetails v-for="(item, index) in getGroupedSales" :key="index" :groupedSales="item"
                @show-modal="handleShowModal" :folio="index" />
        </main> -->
    </section>
</template>

<script>
export default {
data() {
    return {

    }
},
components:{

},
props:{
clientId: Number
},
methods:{
    print() {
        window.open(route('clients.print-historial', this.clientId), '_blank');
    }
}
}
</script>
