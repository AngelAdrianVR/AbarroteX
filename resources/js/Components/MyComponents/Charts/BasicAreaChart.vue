<template>
  <div
    class="min-h-[220px] border-grayD9 border rounded-[10px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative"
  >
    <h1 class="font-bold text-center">{{ title }} <span v-html="icon"></span></h1>

    <div id="chart">
      <apexchart
        type="area"
        height="350"
        :options="chartOptions"
        :series="series"
      ></apexchart>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      series: [
        {
          name: "Random Data",
          data: this.generateRandomData()
        }
      ],
      chartOptions: {
        chart: {
          type: 'area',
          height: 300,
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth'
        },
        labels: [
          'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
          'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Novembiembre', 'Deciembre'
        ],
        xaxis: {
          type: 'category'
        },
        yaxis: {
          opposite: false
        },
        legend: {
          horizontalAlign: 'left'
        }
      }
    };
  },
  props: {
    title: String,
    icon: {
      default: '',
      type: String
    }
  },
  methods: {
    generateRandomData() {
      // Generate random data for each month
      return Array.from({ length: 12 }, () => Math.floor(Math.random() * 2500) + 1);
    }
  }
};
</script>

<style scoped>
/* Add any necessary styles here */
</style>
