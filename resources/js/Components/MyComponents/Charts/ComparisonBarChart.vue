<template>
    <div
        class="min-h-[100px] border border-gray3 rounded-[10px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <h1 class="font-bold text-center">{{ title }} <span v-html="icon"></span></h1>
        <div v-if="!areSeriesEmpty()" id="chart">
            <apexchart type="bar" height="180" :options="chartOptions" :series="series"></apexchart>
        </div>
        <p v-else class="flex flex-col items-center space-y-1 text-gray99 text-xs text-center my-5">
            <span>No hay información</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
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
    },
    methods: {
        areSeriesEmpty() {
            const allZeroValues = this.options.series.every(series => {
                return series.data.every(value => {
                    return parseInt(value) === 0;
                });
            });

            return allZeroValues;
        }
    },
}
</script>