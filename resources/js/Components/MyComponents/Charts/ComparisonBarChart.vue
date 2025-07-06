<template>
    <div
        class="min-h-[100px] rounded-lg shadow-xl text-center lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
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
                    height: 180, // Un poco más grande para mejor visibilidad
                    toolbar: { show: false },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%', // Columnas más delgadas
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                colors: this.options.colors, // Mantiene los colores originales
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "vertical",
                        gradientToColors: this.options.colors.map(color => color), // Usa el mismo color base
                        stops: [0, 100],
                        opacityFrom: 0.9, // Un poco más de transparencia al inicio
                        opacityTo: 1
                    }
                },
                stroke: {
                    show: true,
                    width: 3, // Un poco más grueso para mayor contraste
                    colors: ['#fff']
                },
                xaxis: {
                    categories: this.options.categories,
                    labels: {
                        style: {
                            colors: '#666',
                            fontSize: '12px',
                            fontWeight: 600
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: '$ Pesos',
                        style: {
                            color: '#333',
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    },
                    labels: {
                        style: {
                            colors: '#666',
                            fontSize: '12px'
                        }
                    }
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function (val) {
                            return "$ " + val.toLocaleString('en-US', { minimumFractionDigits: 2 })
                        }
                    }
                },
                grid: {
                    borderColor: "#ddd",
                    strokeDashArray: 4, // Líneas punteadas para un look más moderno
                    yaxis: {
                        lines: { show: true }
                    }
                },
                shadow: {
                    enabled: true,
                    color: '#000',
                    top: 10,
                    left: 0,
                    blur: 5,
                    opacity: 0.2
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
            const allZeroValues = this.options?.series?.every(series => {
                return series.data.every(value => {
                    return parseInt(value) === 0;
                });
            });

            return allZeroValues;
        }
    },
}
</script>