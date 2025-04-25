<template>
    <div>
        <el-card class="box-card">
            <div  class="text item">
                <el-input
                    size="mini"
                    placeholder="Search here....."
                    style="width:300px; float:right; margin-right: 10px"
                    @keyup.enter.native="applySearch"
                    v-model="search">
                </el-input>
                <el-date-picker
                    v-model="date"
                    @change="changeDate"
                    type="date"
                    :clearable="false"
                    placeholder="Pick a day">
                </el-date-picker>
                <el-table
                    id="teamlist"
                    :data="reports"
                    v-loading="loading"
                    style="width: 100%"
                    :style="{ 'font-size': '13px', 'font-weight': 'bold' }">
                        <el-table-column
                            width="70"
                            label="No."
                            :sortable="true">
                                <template slot-scope="scope">
                                    {{scope.$index + 1}}
                                </template>
                        </el-table-column>
                        <el-table-column
                            prop="team_name"
                            label="TEAM"
                            
                            :sortable="true">
                            <template slot-scope="scope">
                                {{ scope.row.team_name }}
                            </template>
                        </el-table-column>

                        <el-table-column
                            prop="task_name"
                            label="Task"
                            :sortable="true">
                            <template slot-scope="scope">
                                {{ scope.row.task_name }}
                            </template>
                        </el-table-column>

                        <el-table-column
                            prop="sum"
                            label="DATA"
                        
                            :sortable="true">
                                <template slot-scope="scope">
                                    {{scope.row.sum }}
                                </template>
                        </el-table-column>
                </el-table>
                <global-pagination
                    :current_page="current_page"
                    :current_size="current_size"
                    :pagination="reportsPagination"
                    :total="filters.total"
                    @handleSizeChange="handleSizeChange"
                    @handleCurrentChange="handleCurrentChange">
                </global-pagination>
                <div class="p-4">
                  <el-button @click="printTable" style="float:right"  type="info" size="mini" class="no-print btn-info">Print</el-button>
                </div>
            </div>
        </el-card>
    </div>
</template>
<style>
@media print {
  .el-table .el-table__fixed-right .el-table-column--selection {
    display: none !important;
  }
}
</style>
<script>
import paginate from '../../../mixin/pagination'
import html2canvas from 'html2canvas';
export default {
    name: 'DailyReportTeam',
    mixins: [paginate],
    data() {
        return {
            reports: [],
            reportsPagination: [],
            loading: false,
            current_page: 1,
            current_size: 25,
            search: null,
            date: new Date,
               isPrinting: false,
reportTableId: 'teamlist',
        }
    },
    created() {
        this.getTeams();
    },
    methods: {
        // async print() {
        //     // Pass the element id here
        //     await this.$htmlToPaper('teamlist');
        // },
        async printTable() {
    this.isPrinting = true; // Set the flag
    const printContents = document.getElementById(this.reportTableId);
    const clonedTable = printContents.cloneNode(true);
    const actionColumn = clonedTable.querySelector('.el-table-column--selection');
    if (actionColumn) {
      actionColumn.parentNode.removeChild(actionColumn);
    }
    html2canvas(printContents).then((canvas) => {
      const printWindow = window.open('', '_blank');
      printWindow.document.open();
      const currentDate = new Date().toLocaleDateString(); // Get the current date
      printWindow.document.write(`
      <html>
<head>
  <title>Print</title>
  <style>
    @media print {
      .header {
        padding: 1rem;
        text-align: right;
        font-size: 0.8rem;
        font-weight: bold;
      }
      .header1 {
          
                text-align: right;
                font-size: 0.8rem;
                font-weight: bold;
              }
      .logo {
        width: 50px; /* Adjust the width to your desired size */
        margin-right: 10px; /* Adjust the margin as needed */
      }
      .title {
        font-size: 24px; /* Adjust the font size as needed */
        font-weight: bold; /* Adjust the font weight as needed */
      }
      .footer {
        border-top: 1px solid #ccc;
        padding: 5px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: flex-end;
      }
      .footer img {
        width: 30px;
        margin-right: 10px;
      }
      .footer h1 {
        font-size: 1rem;
        font-weight: 10;
        text-align: right;
      }
    }
    .header-content {
      display: inline-flex;
      align-items: center;
    }
    .small-logo {
      width: 30px; /* Adjust the width to your desired size */
      margin-right: 10px; /* Adjust the margin as needed */
    }
    
  </style>
</head>
<body>
  <header>
    <h5 class="header1" >Date:${currentDate}</h5> <!-- Display the current date -->
    <div class="header-content">
      <img src="/img/company_logo.png" alt="Logo" class="logo">
      <h4>Farm Management System</h4>
    </div>
    
  </header>
   
          
  <h1 class="title">Monthly Report: Employee</h1>
  <div class="print-content">
    <!-- Your report content goes here -->
    <img src="${canvas.toDataURL()}" style="width: 100%">
  </div>

  <div class="footer">
    <h1 style="margin-right: auto; text-align: left;">Section: Production</h1>
    <h1>
      <img src="/img/logo.png" alt="Logo" class="small-logo">
      Farm Management System
    </h1>
  </div>
</body>
</html>
      `);
      printWindow.document.close();
      printWindow.onload = function () {
        // Add an onload event handler to ensure the window is fully loaded before printing
        printWindow.print();
        printWindow.onafterprint = function () {
          // Add an onafterprint event handler to reset the loading state and flag
          // Reset the loading state after printing
          this.isPrinting = false; // Reset the flag
          printWindow.close(); // Close the print window after printing
        }.bind(this);
      }.bind(this);
    });
  },
        async getTeams() {
            this.date = this.$df.formatDate(this.date, "YYYY-MM-DD")
            let params = {
                current_size: this.current_size,
                current_page: this.current_page,
                search: this.search,
                date: this.date,
            };

            this.loading = true;
            await this.$API.DailyReport.getTeamReport(params)
                .then(response => {
                    this.reports = response.data.data
                    this.reportsPagination = response.data
                })
                .catch(error => {
                    console.log(error);
                })
                .finally(() => {
                    this.loading = false
                });

            console.log(this.reports);
        },
        changeDate(value) {
            this.date = value
            this.getTeams();
        },
        handleSizeChange(val) {
            this.current_size = val;
            this.getTeams();
        },
        handleCurrentChange(val) {
            this.current_page = val;
            this.getTeams();
        },
        applySearch() {
            this.getTeams();
        },
    },
    watch: {
        search(val) {
            if( val == '') {
                this.getTeams();
            }
        }
    }
}
</script>
