<template>
  <div>

    <h1>
      Forecasted Stem cut Graph for the year {{ this.$df.formatDate(this.year, "YYYY") }}
    </h1>

    <el-tabs v-model="activeTab">
      <el-tab-pane label="Forecasted" name="table">
        <canvas id="linearChart"></canvas>
      </el-tab-pane>

      <el-tab-pane label="Data" name="scatter">
        <!-- Content for the second tab (Scatter Chart) -->
        <div>
         
          <h1>MSE (Mean Squared Error)</h1>
    <el-table :data="scatterData" style="width: 100%" border>
      <el-table-column label="Month" prop="month"></el-table-column>
      <el-table-column label="Actual Stem Cuts" prop="actual" class="actual-stem-cuts"></el-table-column>
    <el-table-column label="Forecasted Stem Cuts" prop="forecasted" class="forecasted-stem-cuts"></el-table-column>

      <el-table-column label="Error" prop="error"></el-table-column>
      <el-table-column label="Squared Error" prop="squaredError"></el-table-column>
    </el-table>
    <div style="display: flex; justify-content: center;">

      <div style="margin-right: 200px;">
        <h3>Sum</h3><h5>{{ totalSquaredError }}</h5>
      </div>
      
      <div  style="margin-right: 200px;">
        <h3>MSE </h3><h5>{{ mse.toFixed(4) }}</h5>
      </div>

      <div  style="margin-right: 200px;">
        <h3>MAE </h3><h5>{{ mae.toFixed(4) }}</h5>
      </div>

      <div style="margin-right: 200px;">
  <h3>RMSE</h3>
  <h5>{{ rmse.toFixed(4) }}</h5>
</div>
    </div>

    <span style="color: gray;">
      The Mean Squared Error (MSE) is a statistical measure that calculates the
      average of the squared differences between the actual and forecasted values.
      In the context of this table, it serves as an evaluation metric for the
      forecasting model's performance. Lower MSE values indicate higher accuracy
      in predicting stem cuts, reflecting a better match between predicted and
      observed values.
    </span>
        </div>
      </el-tab-pane>
    </el-tabs>
   
  </div>
</template>
<style>
.actual-stem-cuts .cell {
  color: red; /* Set color for Actual Stem Cuts */
}

.forecasted-stem-cuts .cell {
  color: blue; /* Set color for Forecasted Stem Cuts */
}
</style>


<script>
import Chart from 'chart.js';
import * as tf from '@tensorflow/tfjs';
import { error } from 'jquery';

export default {
  name: 'LinearGraph',

  props: {
    area: {},
    year: {},
    
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
        data: [], 
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1
      },
      {
        label: 'Actual Stem Cuts',
        data: [], 
        borderColor: 'rgb(255, 99, 132)',
        tension: 0.1
      }
    ]
  },
  options: {}
},
      chartDataX2: {
        type: 'line',
        data: {
          labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
          datasets: [
            {
              label: 'Bagging data',
              data: [],
              borderColor: 'rgb(55, 138, 19)',
              tension: 0.1
            }
          ]
        },
        options: {}
      },
      chartDataX1: {
        type: 'line',
        data: {
          labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
          datasets: [
            {
              label: 'Bud injection',
              data: [],
              borderColor: 'rgb(156, 153, 9)',
              tension: 0.1
            }
          ]
        },
        options: {}
      },
      chartDataStem: { 
        type: 'line',
        data: {
          labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
          datasets: [
            {
              label: 'Stem Cuts',
              data: [],
              borderColor: 'rgb(204, 0, 0)',
              tension: 0.1
            }
          ]
        },
        options: {}
      },
      modalVisible: false,
      modalTitle: '',
      scatterData: [],
      totalSquaredError: 0,
      mse: 0,
      mae: 0,
      rmse: 0,  
      pollingInterval: null,
      stemTableData: [],
      linearRegressions: [],
      totalStemCuts: 0,
      baggingTableData: [], 
      budInjectionTableData: [], 
    };
  },

  created() {
    this.getGraphDataA();
    this.getGraphDataStem();
    this.getGraphDataX2();
    this.getGraphDataX1();
    this.calculateAndSetMSE();
    this.calculateAndSetMAE()
    this.fetchAllLinearRegressions()
    this.calculateAndSetRMSE();
   this.startPolling();
    this.activeTab = 'table';
    // // this.activeTab = 'scatter';
  },
   beforeDestroy() {

 clearInterval(this.pollingInterval);
 },

  methods: {
 reloadStemChart() {
      this.getGraphDataStem();
     
      this.getGraphDataA();
      this.calculateAndSetMSE();
      this.calculateAndSetMAE();
      this.calculateAndSetRMSE();
    },

    startPolling() {
      // Set an interval to fetch new data every 3 seconds
      this.pollingInterval = setInterval(async () => {
      
        await this.getGraphDataA(); // Fetch new graph dat
        await this.getGraphDataStem();
        await this.calculateAndSetMSE();
        await this.fetchAllLinearRegressions(); // Fetch new stem graph data
        await this.calculateAndSetMAE(); // Calculate MAE
       await this.calculateAndSetMSE(); // Recalculate MSE
        await this.calculateAndSetRMSE();
      }, 3000); // 3 seconds in milliseconds
    },

   
    async fetchAllLinearRegressions() {
    try {
      const response = await this.$API.Linear.getAllLinearRegressions();
      this.linearRegressions = response.data;
    } catch (error) {
      console.error("Failed to fetch linear regressions:", error);
    }
  },

    

  async getActualData(month) {
    try {
      const params = {
        area_id: this.area,
        year: this.$df.formatDate(month, "YYYY-MM-DD"),
        month: this.$df.formatDate(month, "YYYY-MM-DD"),
      };

      const res = await this.$API.Linear.getStemDataGraphByYear(params);
      return res.data;
    } catch (error) {
      console.log('Error fetching actual data:', error);
      return [];
    }
  },
 

  async getPredictedData(month) {
    try {
      const params = {
        area_id: this.area,
        year: this.$df.formatDate(month, "YYYY-MM-DD"),
        month: this.$df.formatDate(month, "YYYY-MM-DD"),
      };

      const res = await this.$API.Linear.getForecast(params);
      return res.data;
    } catch (error) {
      console.log('Error fetching predicted data:', error);
      return [];
    }
  },

  async calculateAndSetMSE() {
  try {
    // Ensure linearRegressions is loaded, maybe fetch if not already loaded
    if (!this.linearRegressions.length) {
      await this.fetchAllLinearRegressions();
    }

    // Fetch actual and predicted data
    const actualData = await this.getActualData(this.year);
    const predictedData = await this.getPredictedData(this.year);

    const actualTensor = tf.tensor1d(actualData);
    const predictedTensor = tf.tensor1d(predictedData);

    const squaredErrors = actualTensor.sub(predictedTensor).square();

    // Filter out zero values from both actual and predicted data
    const filteredActual = actualData.filter((value, index) => value !== 0 && predictedData[index] !== 0);
    const filteredPredicted = predictedData.filter((value, index) => value !== 0 && actualData[index] !== 0);

    const totalSquaredError = filteredActual.length > 0 ? squaredErrors.sum().dataSync()[0] : 0;
    const mse = filteredActual.length > 0 ? totalSquaredError / this.linearRegressions.length : 0;

    // Prepare scatterData to show in the table
    this.scatterData = actualData.map((actual, index) => ({
      month: this.getMonthName(index + 1),
      actual: actual,
      forecasted: predictedData[index],
      error: actual !== 0 && predictedData[index] !== 0 ? Math.abs(actual - predictedData[index]) : 0,
      squaredError: actual !== 0 && predictedData[index] !== 0 ? Math.pow(actual - predictedData[index], 2) : 0
    }));

    this.totalSquaredError = totalSquaredError;
    this.mse = mse;
  } catch (error) {
    console.error('Error calculating MSE:', error);
  }
},


async calculateAndSetMAE() {
  try {
    // Ensure linearRegressions is loaded, maybe fetch if not already loaded
    if (!this.linearRegressions.length) {
      await this.fetchAllLinearRegressions();
    }

    // Fetch actual and predicted data
    const actualData = await this.getActualData(this.year);
    const predictedData = await this.getPredictedData(this.year);

    // Calculate MAE
    let sumAbsoluteError = 0;
    for (let i = 0; i < actualData.length; i++) {
      sumAbsoluteError += actualData[i] !== 0 && predictedData[i] !== 0 ? Math.abs(actualData[i] - predictedData[i]) : 0;
    }
    const mae = sumAbsoluteError / this.linearRegressions.length;

    // Update component data with MAE
    this.mae = mae;
  } catch (error) {
    console.error('Error calculating MAE:', error);
  }
},

async calculateAndSetRMSE() {
  try {
    // Ensure linearRegressions is loaded, maybe fetch if not already loaded
    if (!this.linearRegressions.length) {
      await this.fetchAllLinearRegressions();
    }

    // Fetch actual and predicted data
    const actualData = await this.getActualData(this.year);
    const predictedData = await this.getPredictedData(this.year);

    // Calculate squared differences
    const squaredDifferences = actualData.map((actual, index) => actual !== 0 && predictedData[index] !== 0 ? Math.pow(actual - predictedData[index], 2) : 0);

    // Calculate total squared differences
    const totalSquaredDifference = squaredDifferences.reduce((sum, squaredDiff) => sum + squaredDiff, 0);

    // Calculate RMSE
    const rmse = Math.sqrt(totalSquaredDifference / this.linearRegressions.length);

    // Update component data with RMSE
    this.rmse = rmse;
  } catch (error) {
    console.error('Error calculating RMSE:', error);
  }
},




getMonthName(monthIndex) {
  const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  return monthNames[monthIndex - 1]; // Adjust monthIndex to be 0-based
},

    async getStemGraphData(month) {
      try {
        const params = {
          area_id: this.area,
          year: this.$df.formatDate(month, "YYYY-MM-DD"),
          month: this.$df.formatDate(month, "YYYY-MM-DD"),
        };

        const res = await this.$API.Linear.getStemDataGraphByYear(params);
        return res.data;
      } catch (error) {
        console.log('Error fetching Stem Graph data:', error);
        return [];
      }
    },

    async getStemGraphData(month) {
      try {
        const params = {
          area_id: this.area,
          year: this.$df.formatDate(month, "YYYY-MM-DD"),
          month: this.$df.formatDate(month, "YYYY-MM-DD"),
        };

        const res = await this.$API.Linear.getStemDataGraphByYear(params);
        return res.data;
      } catch (error) {
        console.log('Error fetching Stem Graph data:', error);
        return [];
      }
    },

    async getGraphDataStem() {
      try {
     
        let dt = new Date();
        dt.setDate(1);
        const year = this.$df.formatDate(this.year, "YYYY");
        dt.setFullYear(year);
        const months = [
          dt.setMonth(0), dt.setMonth(1), dt.setMonth(2), dt.setMonth(3), 
          dt.setMonth(4), dt.setMonth(5), dt.setMonth(6), dt.setMonth(7), 
          dt.setMonth(8), dt.setMonth(9), dt.setMonth(10), dt.setMonth(11)
        ];
        let stem = await this.getStemGraphData(months[1]);

       
        this.totalStemCuts = stem.reduce((total, value) => total + value, 0);

       
        this.stemTableData = months.map((month, index) => ({
          month: this.getMonthName(index + 1), 
          stemCuts: stem[index],
        }));
        
       
        this.stemTableData.push({
          month: 'Total',
          stemCuts: this.totalStemCuts,
        });

       
        this.chartDataStem.data.datasets[0].data = stem;


        const ctx = document.getElementById('stemChartModal');
        const chart = new Chart(ctx, this.chartDataStem);
      } catch (error) {
        console.log('Error fetching graph data:', error);
      }
    },
    async getGraphData() {
  try {
    let dt = new Date();
    dt.setDate(1);
    const year = this.$df.formatDate(this.year, 'YYYY');
    dt.setFullYear(year);
    const months = [
      dt.setMonth(0), dt.setMonth(1), dt.setMonth(2), dt.setMonth(3),
      dt.setMonth(4), dt.setMonth(5), dt.setMonth(6), dt.setMonth(7),
      dt.setMonth(8), dt.setMonth(9), dt.setMonth(10), dt.setMonth(11),
    ];

    // Fetch data using getDataGraphA and getDataGraph
    let forecastedStemCuts = await this.getDataGraphA(months[1]);
    let actualStemCuts = await this.getDataGraph(months[1]);

    // Assign the results to chart data
    this.chartData.data.datasets[0].data = forecastedStemCuts;
    this.chartData.data.datasets[1].data = actualStemCuts;

    // Render the chart
    const ctx = document.getElementById('linearChart');
    const chart = new Chart(ctx, this.chartData);
  } catch (error) {
    console.log('Error fetching graph data:', error);
  }
},




async getGraphDataA() {
  try {
    let dt = new Date();
    dt.setDate(1);
    const year = this.$df.formatDate(this.year, "YYYY");
    dt.setFullYear(year);

    const months = [dt.setMonth(0), dt.setMonth(1), dt.setMonth(2), dt.setMonth(3), dt.setMonth(4), dt.setMonth(5), dt.setMonth(6), dt.setMonth(7), dt.setMonth(8), dt.setMonth(9), dt.setMonth(10), dt.setMonth(11)];

    let forecastedStemCuts = await this.getDataGraphA(months[1]);
    let actualStemCuts = await this.getActualData(months[1]);

    this.chartData.data.datasets[0].data = forecastedStemCuts;
    this.chartData.data.datasets[1].data = actualStemCuts;

    const ctx = document.getElementById('linearChart');
    const chart = new Chart(ctx, this.chartData);
  } catch (error) {
    console.log('Error fetching graph data:', error);
  }
},


    async getGraphDataX2() {
  try {
    // Fetch Bagging data
    let dt = new Date();
    dt.setDate(1);
    const year = this.$df.formatDate(this.year, "YYYY");
    dt.setFullYear(year);

    const months = [dt.setMonth(0), dt.setMonth(1), dt.setMonth(2), dt.setMonth(3), dt.setMonth(4), dt.setMonth(5), dt.setMonth(6), dt.setMonth(7), dt.setMonth(8), dt.setMonth(9), dt.setMonth(10), dt.setMonth(11)];

    let baggingData = await this.getDataGraphX2(months[1]);

    // Calculate total Bagging data
    const totalBagging = baggingData.reduce((total, value) => total + value, 0);

    // Populate baggingTableData with month and Bagging data
    this.baggingTableData = months.map((month, index) => ({
      month: this.getMonthName(index + 1), // Get month name
      bagging: baggingData[index], // Assuming baggingData is an array of Bagging data
    }));

    // Add total to baggingTableData
    this.baggingTableData.push({
      month: 'Total',
      bagging: totalBagging,
    });

    // Update chart with Bagging data
    this.chartDataX2.data.datasets[0].data = baggingData;

    // Render the chart
    const ctx = document.getElementById('linearChartX2');
    const chart = new Chart(ctx, this.chartDataX2);
  } catch (error) {
    console.log('Error fetching Bagging graph data:', error);
  }
},

// Method to fetch Bud injection data
async getGraphDataX1() {
  try {
    // Fetch Bud injection data
    let dt = new Date();
    dt.setDate(1);
    const year = this.$df.formatDate(this.year, "YYYY");
    dt.setFullYear(year);

    const months = [dt.setMonth(0), dt.setMonth(1), dt.setMonth(2), dt.setMonth(3), dt.setMonth(4), dt.setMonth(5), dt.setMonth(6), dt.setMonth(7), dt.setMonth(8), dt.setMonth(9), dt.setMonth(10), dt.setMonth(11)];

    let budInjectionData = await this.getDataGraphX1(months[1]);

    // Calculate total Bud injection data
    const totalBudInjection = budInjectionData.reduce((total, value) => total + value, 0);

    // Populate budInjectionTableData with month and Bud injection data
    this.budInjectionTableData = months.map((month, index) => ({
      month: this.getMonthName(index + 1), // Get month name
      budInjection: budInjectionData[index], // Assuming budInjectionData is an array of Bud injection data
    }));

    // Add total to budInjectionTableData
    this.budInjectionTableData.push({
      month: 'Total',
      budInjection: totalBudInjection,
    });

    // Update chart with Bud injection data
    this.chartDataX1.data.datasets[0].data = budInjectionData;

    // Render the chart
    const ctx = document.getElementById('linearChartX1');
    const chart = new Chart(ctx, this.chartDataX1);
  } catch (error) {
    console.log('Error fetching Bud injection graph data:', error);
  }
},

async getDataGraphA(month) {
      try {
        const year = this.$df.formatDate(month, "YYYY");
        const fullDate = this.$df.formatDate(month, "YYYY-MM-DD");

        const params = {
          area_id: this.area,
          month: fullDate,
        };

        const res = await this.$API.Linear.getForecast(params);

        return res.data;
      } catch (error) {
        console.log(error);
      }
    },

  

    async getDataGraphX2(month) {
      try {
        this.forecasted = [];

        const params = {
          area_id: this.area,
          year: this.$df.formatDate(month, "YYYY-MM-DD"),
          month: this.$df.formatDate(month, "YYYY-MM-DD"),
        };

        const res = await this.$API.Linear.getDataGraphByYearX2(params);

        return res.data;
      } catch (error) {
        console.log('====================================');
        console.log(error);
        console.log('====================================');
      }
    },
    async getDataGraphX1(month) {
      try {
        this.forecasted = [];

        const params = {
          area_id: this.area,
          year: this.$df.formatDate(month, "YYYY-MM-DD"),
          month: this.$df.formatDate(month, "YYYY-MM-DD"),
        };

        const res = await this.$API.Linear.getDataGraphByYearX1(params);

        return res.data;
      } catch (error) {
        console.log('====================================');
        console.log(error);
        console.log('====================================');
      }
    },
  }
}
</script>
