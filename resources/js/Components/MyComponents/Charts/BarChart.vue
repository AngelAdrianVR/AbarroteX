<template>
    <div
        class="min-h-[220px] border border-gray3 rounded-[10px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <h1 class="font-bold text-center">{{ title }} <span v-html="icon"></span></h1>
        <div id="chart">
            <apexchart type="bar" height="180" :options="chartOptions" :series="series"></apexchart>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            series: this.options.series,
            chartOptions: {
                chart: {
                    type: 'bar',
                    height: 145
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                colors: this.options.colors,
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: this.options.categories,
                },
                yaxis: {
                    title: {
                        text: '$ pesos'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return "$ " + val.toLocaleString('en-US', { minimumFractionDigits: 2 })
                        }
                    }
                }
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
    }
}
</script>