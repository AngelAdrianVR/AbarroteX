<template>
    <div
        class="min-h-[100px] max-h-[420px] rounded-lg shadow-xl text-center lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <h1 class="font-bold text-center flex items-center justify-center">{{ title }} <span class="ml-3" v-html="icon"></span></h1>

        <div v-if="this.options.series.length" id="chart">
            <apexchart type="donut" width="500" :options="chartOptions" :series="series"></apexchart>
        </div>
        <p v-else class="flex flex-col items-center space-y-1 text-gray99 text-xs text-center my-5">
            <span>No hay información</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
            </svg>
        </p>
    </div>
</template>

<script>

export default {
    data() {
        return {
            series: this.options.series,
            chartOptions: {
                chart: {
                    width: 500,
                    type: 'donut',
                },
                plotOptions: {
                    pie: {
                        startAngle: -90,
                        endAngle: 270
                    }
                },
                fill: {
                    type: 'gradient',
                },
                colors: this.options.colors,
                labels: this.options.labels,
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 420
                        },
                        legend: {
                            position: 'right'
                        }
                    }
                }]
            },
        };
    },
    props: {
        title: String,
        icon: {
            default: '',
            type: String
        },
        options: Object,
    },
}
</script>