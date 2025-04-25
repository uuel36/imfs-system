<template>
    <div>
        <el-card class="box-card">
            <div  class="text item">
                <el-date-picker
                        v-model="date"
                        type="year"
                        placeholder="Pick a year"
                        @change="dateChange"
                />
                <h1>Overall Graph</h1>
                <div  class="text item" >
                    <linear-graph-all :year="date" v-if="showGraph"  v-loading="loading"  ></linear-graph-all>
                </div>
            </div>
        </el-card>
        <el-card class="box-card">
            <div  class="text item">
                <el-select v-model="area_id" @change="areaChange" clearable="" placeholder="Select Area">
                    <el-option
                        v-for="item in areas"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id">
                    </el-option>
                </el-select>
                <el-button type="primary" @click="showDetails">Show Data</el-button>
                <el-button type="primary" @click="exportToCSV">Export to CSV</el-button>
                <el-button size="mini" type="primary" @click="addLinear" style="float:right;">Add Linear Regression</el-button>
                <!-- <el-input
    size="mini"
    placeholder="Search here....."
    @keyup.enter.native="applySearch"
    v-model="search"
>
</el-input> -->
<!-- <div v-if="linearValues">
      <h2>linear Values:</h2>
      <ul>
        <li>Sum X1: {{ linearValues.sum_X1 }}</li>
        <li>Sum X2: {{ linearValues.sum_X2 }}</li>
        <li>B1: {{ linearValues.b1 }}</li>
        <li>B2: {{ linearValues.b2 }}</li>
        <li>M1: {{ linearValues.m1 }}</li>
        <li>M2: {{ linearValues.m2 }}</li>
      </ul>
    </div> -->

                
    <el-table ref="linearsTable"    :data="linears"    v-loading="tableLoading || loading" style="width: 100%">
                    <el-table-column
                        width="70"
                        label="No."
                        :sortable="true">
                            <template slot-scope="scope">
                                {{scope.$index + 1}}
                            </template>
                    </el-table-column>
                    <el-table-column
    prop="area_id"
    label="Area"
    :sortable="true">
    <template slot-scope="scope">
        {{ getAreaName(scope.row.area_id) }}
    </template>
</el-table-column>
                    <el-table-column
                        prop="bud_injection_x1"
                        label="BUD INJECTION (X1)"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="bu_injection_date"
                        label="BUD INJECTION DATE"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="bagging_report_x2"
                        label="BAGGING REPORT (X2)"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="bagging_report_date"
                        label="BAGGING REPORT DATE"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                         prop="y"
                         label="STEM CUT (Y)"
                         :sortable="true">
                    </el-table-column>
                 


                    <el-table-column
                        fixed="right"
                        width="110"
                        label="ACTION">
                        <template slot-scope="scope">
                            <button @click="handleEdit(scope.row)" class="btn btn-success btn-sm"><i class="far fa-edit"></i></button>
                            <button @click="askToDelete(scope.$index, scope.row)" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </template>
                    </el-table-column>
            </el-table>
                <global-pagination
                    :current_page="current_page"
                    :current_size="current_size"
                    :pagination="linearsPagination"
                    :total="filters.total"
                    @handleSizeChange="handleSizeChange"
                    @handleCurrentChange="handleCurrentChange">
                </global-pagination>
            </div>
            
            <el-dialog
                :title="mode == 'create' ? 'ADD LINEAR REGRESSION' : 'UPDATE LINEAR REGRESSION'" width="55%"
                :before-close="handleClose"
                :visible.sync="dialogTableVisible"
                custom-class="custom-dialog">
                <linear-form :mode="mode" :model="model" ></linear-form>
            </el-dialog>
        </el-card>
    </div>
</template>
<style>
.custom-dialog {
  border-radius: 15px; /* Set the desired border radius */
}
.custom-input-border .el-input {
    border: none;
    border-bottom: 1px solid #409EFF; /* You can change the color to your preference */
    border-radius: 0;
  }
</style>
<script>
import paginate from '../../../mixin/pagination'
export default {
    name: 'LinearList',
    mixins: [paginate],
    data() {
        return {
            linears: [],
            linearsPagination: [],
            loading: false,
            current_page: 1,
            current_size: 50,
            search: null,
            mode: '',
            date: new Date(),
            showGraph: false,
            model: {},
            dialogTableVisible: false,
            area_id: '',
            areas: [],
            tableLoading: false,
            linearValues: null,
        }
    },
    created() {
        this.getLinears();
        this.getLinearValues();
        this.$EventDispatcher.listen('NEW_DATA', data => {
            this.linears.unshift(data)
            this.dialogTableVisible = false
            this.mode = ''
            this.loadNewRow();
        })

        this.$EventDispatcher.listen('UPDATE_DATA', data => {
            this.linears.forEach(cat => {
                if(cat.id == data.id) {
                    for(let key in data) {
                        cat[key] = data[key]
                    }
                }
            })

            this.dialogTableVisible = false
            this.mode = ''
        })

        this.getAreas();
    },
   
    methods: {
        getAreaName(areaId) {
        const area = this.areas.find(area => area.id === areaId);
        return area ? area.name : 'Unknown';
    },
        loadNewRow() {
      // Check if there are new data
      const hasNewData = this.linears.length > 0;

      // Set table loading to true if there are new data
      if (hasNewData) {
        this.tableLoading = true;
      }

      // Perform your loading logic here
      this.getLinears();

      // Simulating a delay (you can replace this with your actual loading logic)
      setTimeout(() => {
        // Set table loading back to false after the loading logic is completed
        this.tableLoading = false;
      }, 1000); // Adjust the delay as needed
    },
        formatFloatColumn(row, column, cellValue) {
        // Check if the cellValue is a valid number before formatting
        if (!isNaN(parseFloat(cellValue))) {
            return parseFloat(cellValue).toFixed(2);
        }
        return cellValue;
    },
    exportToCSV() {
    const data = this.linears.map(item => ({
        'Area': item.area.name,
        'BUD INJECTION (X1)': item.bud_injection_x1,
        'BUD INJECTION DATE': item.bu_injection_date,
        'BAGGING REPORT (X2)': item.bagging_report_x2,
        'BAGGING REPORT DATE': item.bagging_report_date,
        'STEM CUT (Y)': item.y,
        'Sum X1': item.linearValues ? item.linearValues.sum_X1 : null,
        'Sum X2': item.linearValues ? item.linearValues.sum_X2 : null,
        'B1': item.linearValues ? item.linearValues.b1 : null,
        'B2': item.linearValues ? item.linearValues.b2 : null,
        'M1': item.linearValues ? item.linearValues.m1 : null,
        'M2': item.linearValues ? item.linearValues.m2 : null,
        'X1M1': item.linearValues ? item.linearValues.x1m1 : null,
        'X2M2': item.linearValues ? item.linearValues.x2m2 : null,
        'X1M12': item.linearValues ? item.linearValues.x1m12 : null,
        'X2M22': item.linearValues ? item.linearValues.x2m22 : null,
    }));

    const csvContent = this.convertToCSV(data);

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');

    link.href = URL.createObjectURL(blob);
    link.setAttribute('download', 'linear_data.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
},

    convertToCSV(data) {
      const header = Object.keys(data[0]).join(',') + '\n';

      const rows = data.map(item =>
        Object.values(item).map(value => this.escapeCSVValue(value)).join(',')
      ).join('\n');

      return header + rows;
    },

    escapeCSVValue(value) {
      return typeof value === 'string' ? `"${value.replace(/"/g, '""')}"` : value;
    },
  
        async getLinearValues() {
      try {
        const response = await this.$API.Linear.calculateLinearValues();
        this.linearValues = response.data;
      } catch (error) {
        console.error('Error fetching linear values:', error);
      }
    },
  


    async getLinears() {
    try {
        this.showGraph = false;
        
        let params = {
            current_size: this.current_size,
            current_page: this.current_page,
            search: this.search,
            area_id: this.area_id
        };

        this.loading = true;
        const [linearsResponse, linearValuesResponse] = await Promise.all([
            this.$API.Linear.getLinears(params),
            this.$API.Linear.calculateLinearValues()
        ]);

        this.linears = linearsResponse.data.data;

        // Create a map for quicker lookup of linear values by area_id
        const linearValuesMap = new Map(linearValuesResponse.data.map(value => [value.area_id, value]));

        this.linears.forEach(linear => {
            if (linear.area && linear.area.id) {
                const linearValue = linearValuesMap.get(linear.area.id);
                if (linearValue) {
                    // Add rounded linear values to the linear object
                    linear.linearValues = {
                        sum_X1: linearValue.sum_X1,
                        sum_X2: linearValue.sum_X2,
                        b1: linearValue.b1,
                        b2: linearValue.b2,
                        m1: linearValue.m1,
                        m2: linearValue.m2,
                        x1m1: linearValue.x1m1 ? linearValue.x1m1.toFixed(2) : null,
                        x2m2: linearValue.x2m2 ? linearValue.x2m2.toFixed(2) : null,
                        x1m12: linearValue.x1m12 ? linearValue.x1m12.toFixed(2) : null,
                        x2m22: linearValue.x2m22 ? linearValue.x2m22.toFixed(2) : null
                    };
                }
            }
        });

        this.linearsPagination = linearsResponse.data;
        this.loading = false;
        if (this.date) {
            this.showGraph = true;
        }
    } catch (error) {
        console.log(error);
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
        },
        dateChange(value) {
            console.log(value);
            this.date = value;
            this.getLinears();
            
        },
        showDetails() {
            this.$router.push({name: 'Linear Details'})
        },
        addLinear() {
            this.dialogTableVisible = true;
            this.model = {}
            this.mode = 'create';
        },
        handleEdit(data) {
            this.model = {...data};
            this.dialogTableVisible = true;
            this.mode = 'update'
        },
        askToDelete(index, data) {
            this.$confirm('This will permanently delete the file. Continue?', 'Warning', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning'
                }).then(() => {
                    this.delete(index, data);
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'Delete canceled'
                    });
                });
        },
        async delete(index, data) {
            try {
                await this.$API.Linear.deleteLinear(data.id)
                this.$notify({
                    title: 'Success',
                    message: 'Successfully Deleted',
                    type: 'success'
                });
                this.linears.splice(index, 1)
            } catch (error) {
                console.log(error);
            }
        },
        handleClose(done) {
            this.$EventDispatcher.fire('CLOSE_MODAL')
            done();
        },
        handleSizeChange(val) {
            this.current_size = val;
            this.getLinears();
        },
        handleCurrentChange(val) {
            this.current_page = val;
            this.getLinears();
        },
        applySearch() {
            this.getLinears();
        },
    },
    watch: {
        search(val) {
            if( val == '') {
                this.getLinears();
            }
        },
        date(val) {
            if( val == '') {
                // update date
                this.getLinears();
            } else {
                this.date = val;
            }
        }
    }
}

</script>