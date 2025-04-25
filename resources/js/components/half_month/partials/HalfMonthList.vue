<template>
    <el-card class="box-card">
        <div  class="text item">
            <el-button size="mini" type="primary" @click="generateHalfMonth" style="float:right;">Add Report</el-button>
            <el-input
                size="mini"
                placeholder="Search here....."
                style="width:300px; float:right; margin-right: 10px"
                @keyup.enter.native="applySearch"
                v-model="search">
            </el-input>
            <el-table
                id="halfmonth"
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
                        prop="from_date"
                        label="FROM"
                        :sortable="true">
                            <template slot-scope="scope">
                                {{scope.row.from_date | filterDate}}
                            </template>
                    </el-table-column>
                    <el-table-column
                        prop="to_date"
                        label="TO"
                        :sortable="true">
                            <template slot-scope="scope">
                                {{scope.row.to_date | filterDate}}
                            </template>
                    </el-table-column>
                    <el-table-column
                        prop="date"
                        label="REPORT GENERATED"
                        :sortable="true">
                            <template slot-scope="scope">
                                {{scope.row.date | filterDate}}
                            </template>
                    </el-table-column>
                    <el-table-column
                        fixed="right"
                        width="120"
                        label="ACTION">
                        <template slot-scope="scope">
                            <!--a :href="`finance/payslip/${scope.row.id}`" class="btn btn-info btn-sm" target="_blank">Payslip</!--a-->
                            <button @click="handleView(scope.row)" class="btn btn-success btn-sm">View</button>
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
import moment from 'moment'
import html2canvas from 'html2canvas';
export default {
    name: 'HalfMonthList',
    mixins: [paginate],
    data() {
        return {
            reports: [],
            reportsPagination: [],
            loading: false,
            current_page: 1,
            current_size: 25,
            search: null,
            mode: '',
            model: {},
            dialogTableVisible: false,
            date: '',
            isPrinting: false,
reportTableId: 'halfmonth',
        }
    },
    created() {
        this.date = new Date();
        this.getReports()
    },
    filters: {
        filterDate(value) {
            if(value) {
                return moment(value, 'YYYY-MM-DD').format('YYYY-MMM-DD');
            }
        }
    },
    methods: {
        // async print() {
        //     // Pass the element id here
        //     await this.$htmlToPaper('halfmonth');
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
   
          
  <h1 class="title">Half Month Report</h1>
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
        async getReports() {
            try {
                this.date = this.$df.formatDate(this.date, "YYYY-MM-DD")

                let params = {
                    current_size: this.current_size,
                    current_page: this.current_page,
                    search: this.search,
                    date: this.date,
                };

                this.loading = true;
                const res = await this.$API.Month.getReports(params);
                this.reports = res.data.data;
                this.reportsPagination = res.data;
                this.loading = false;
            } catch (error) {
                console.log(error);
            }
        },
        generateHalfMonth() {
            this.$router.push({name: 'Generate Half Month'})
        },
        handleView(data) {
            this.$router.push({name : 'Report Details', params: {model: data, id: data.id} })
        },
        handleClose(done) {
            this.$EventDispatcher.fire('CLOSE_MODAL')
            done();
        },
        handleSizeChange(val) {
            this.current_size = val;
            this.getReports();
        },
        handleCurrentChange(val) {
            this.current_page = val;
            this.getReports();
        },
        applySearch() {
            this.getReports();
        },
        changeDate(value) {
            this.date = value
            this.getReports();
        },
    },
    watch: {
        search(val) {
            if( val == '') {
                this.getReports();
            }
        }
    }

}
</script>
