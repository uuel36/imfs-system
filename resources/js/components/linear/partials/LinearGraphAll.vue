
<template>
    <div>
        <h2>Overall Forecasted Stem cut Graph for the year {{ this.$df.formatDate(this.year, "YYYY") }}</h2>
      <canvas id="linearChart"></canvas>
  
      <!-- <h1>Historical Harvest data </h1>   
    show the date in here  
  
      <canvas id="stemChartModal"></canvas> -->
     
      
    </div>
  </template>
  

<script>
import Chart from 'chart.js';

export default {
    name: 'LinearGraphAll',

    props: {
    area: {},
    year: {}
  },

  data() {
    return {
      chartData: {
        type: 'line',
        data: {
          labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
          datasets: [
            {
              label: 'Forecasted Stem Cuts',
              data: [], // Initialize data as empty array
              borderColor: 'rgb(75, 192, 192)',
              tension: 0.1
            }
          ]
        },
        options: {}
      },
     
 
      modalVisible: false,
      modalTitle: '',

    };
  },

  created() {
    this.getGraphData();

  },

  methods: {


    async getGraphData() {
      try {
        let dt = new Date();
        dt.setDate(1);
        const year = this.$df.formatDate(this.year, "YYYY");
        dt.setFullYear(year);

        const months = [dt.setMonth(0), dt.setMonth(1), dt.setMonth(2), dt.setMonth(3), dt.setMonth(4), dt.setMonth(5), dt.setMonth(6), dt.setMonth(7), dt.setMonth(8), dt.setMonth(9), dt.setMonth(10), dt.setMonth(11)];

        let stem = await this.getDataGraph(months[1]);

        this.chartData.data.datasets[0].data = stem;

        const ctx = document.getElementById('linearChart');
        const chart = new Chart(ctx, this.chartData);
      } catch (error) {
        console.log('Error fetching graph data:', error);
      }
    },


   
    async getDataGraph(month) {
      try {
        const year = this.$df.formatDate(month, "YYYY");
        const fullDate = this.$df.formatDate(month, "YYYY-MM-DD");

        const params = {
          area_id: this.area,
          month: fullDate,
        };

        const res = await this.$API.Linear.getDataGraphByYearAll(params);

        return res.data;
      } catch (error) {
        console.log(error);
      }
    },

 
  }
}
</script>
