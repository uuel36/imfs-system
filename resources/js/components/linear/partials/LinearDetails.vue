<template>
  <div>
      <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
              <div class="container-fluid">
                  <div class="row mb-2">
                      <div class="col-sm-6">
                          <h1 class="page_title">LINEAR REGRESSION DETAILS</h1>
                      </div><!-- /.col -->
                      <div class="col-sm-6">
                          <ol class="breadcrumb float-sm-right">
                          <li class="breadcrumb-item"><a href="/home">Home</a></li>
                          <li class="breadcrumb-item"><a href="/linear-regression#/linears">Linears</a></li>
                          <li class="breadcrumb-item active">Details</li>
                          </ol>
                      </div><!-- /.col -->
                  </div><!-- /.row -->
              </div><!-- /.container-fluid -->
          </div>
          <!-- /.content-header -->

          <!-- Main content -->
          <section class="content">
              <div class="container-fluid">
                  <el-select v-model="area_id" @change="areaChange" clearable="" placeholder="Select Area">
                      <el-option
                          v-for="item in areas"
                          :key="item.id"
                          :label="item.name"
                          :value="item.id">
                      </el-option>
                  </el-select>

                  <!-- date picker only month and year -->
                  <el-date-picker
                      v-model="date"
                      type="year"
                      placeholder="Pick a year"
                      @change="dateChange"
                  />
                  <!------------------------------------->
                  
                  <el-card class="box-card">
                      <div  class="text item" >
                          <h3>Yield Forecast</h3>
                          <h1>{{ selectedText }}</h1>
                      </div>
                  </el-card>

                  <el-card class="box-card">
                      
                      <div  class="text item" >
                          <linear-graph :area="area_id" :year="date" v-if="showGraph" ></linear-graph>
                      </div>

                  </el-card>

              </div>
          </section>
      </div>
  </div>
</template>
<script>

import { Line } from 'vue-chartjs'

export default {
  name: 'LinearDetails',
  data() {
      return {
          areas: [],
          area_id: null,
          linears: [],
          loadinglinears: false,
          dataGraph: [],
          graphx1: [],
          year : '',
          date: null,
          graphx2: [],
          graphy: [],
          graph: [],
          showGraph: false,
          graph_year: null,
          dataGraph: [],
          graph: [],
          graphx1: [],
          graphx2: [],
          graphy: [],
          selectedText: ''
      }
  },
  created() {
      this.getAreas();
  },
  methods: {
      dateChange(value) {
          this.date = value;
          this.getDataGraph();
      },
      async getLinears() {
          try {
              if(this.area_id) {
                  this.loadinglinears = true
                  const res = await this.$API.Linear.getData(this.area_id)
                  this.linears = res.data
                  this.loadinglinears = false
                  return
              }

              this.linears = []
          } catch (error) {
              console.log(error);
          }
      },
      async getStemGraphData(month) {
      try {

        this.forecasted = []

        const params = {
          area_id: this.area,
          // convert this.month to year using proper format
          year: this.$df.formatDate(month, "YYYY-MM-DD"),
          month: this.$df.formatDate(month, "YYYY-MM-DD"),
        }

        const res = await this.$API.Linear.getStemDataGraphByYear(params);

        return res.data

      } catch (error) {
        console.log('====================================');
        console.log(error);
        console.log('====================================');
      }
    },
    async getDataGraph(month) {
      try {
        this.forecasted = []

        const params = {
          area_id: this.area,
          year: this.$df.formatDate(month, "YYYY-MM-DD"),
          month: this.$df.formatDate(month, "YYYY-MM-DD"),
        }

        const res = await this.$API.Linear.getDataGraphByYear(params);

        return res.data
      } catch (error) {
        console.log('====================================');
        console.log(error);
        console.log('====================================');
      }
    },
      async getAreas() {
          try {
              const res = await this.$API.Area.getAllAreas();
              this.areas = res.data
          } catch (error) {
              console.log(error);
          }
      },
      areaChange(value) {
      this.area_id = value;
      this.getLinears();
      this.getDataGraph();

      // Update the selected text based on the selected area
      const selectedArea = this.areas.find(area => area.id === value);
      this.selectedText = selectedArea ? `Selected Area: ${selectedArea.name}` : '';
    },
      async getDataGraph() {
          try {

              this.showGraph = false

              if(!this.date){
                  return
              } 
              const params = {
                  area_id: this.area_id,
                  // convert this.month to year using proper format
                  year: this.$df.formatDate(this.date ? this.date : new Date(), "YYYY-MM-DD"),
                  month: this.$df.formatDate(this.date ? this.date : new Date(), "YYYY-MM-DD"),
              }

              const res = await this.$API.Linear.getDataGraph(params);
              console.log(res.data);

              this.dataGraph = [res.data];
              if(this.date){
                  this.showGraph = true
              } 
              // this.year = this.date ? this.$df.formatDate(this.date, "YYYY") : this.$df.formatDate(new Date(), "YYYY")
          } catch (error) {
              console.log('====================================');
              console.log(error);
              console.log('====================================');
          }
      },
  },
}
</script>
