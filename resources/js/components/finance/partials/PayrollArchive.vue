<template>
    <div>

        <el-card class="box-card">

            <div class="text item" id="payrollsummary">
            
                <!--<el-date-picker
                    v-model="date"
                    @change="changeDate"
                    type="date"
                    :clearable="false"
                    placeholder="Pick a day">
                </el-date-picker> -->
                <el-table
                    :data="payroll"
                    v-loading="loading"
                    style="width: 100%">
                        <el-table-column
                            width="70"
                            label="No."
                            :sortable="true">
                                <template slot-scope="scope">
                                    {{scope.$index + 1}}
                                </template>
                        </el-table-column>
                        <el-table-column
                            prop="name"
                            label="EMPLOYEE"
                            :sortable="true">
                        </el-table-column>
                        <!-- date -->
                        <el-table-column
                            prop="date"
                            label="DATE"
                            :sortable="true">
                        </el-table-column>
                        <el-table-column
                            prop="rate"
                            label="GROSS SALARY"
                            :sortable="true">
                        </el-table-column>
                        <el-table-column
                            prop="deduct"
                            label="DEDUCTIONS"
                            :sortable="true">
                        </el-table-column>
                        <el-table-column
                            prop="overtime"
                            label="OVERTIME"
                            :sortable="true">
                        </el-table-column>
                        <el-table-column
                            prop="salary"
                            label="NET SALARY"
                            :sortable="true">
                                
                        </el-table-column>
                </el-table>
                
                
            </div>
            <global-pagination
                    :current_page="current_page"
                    :current_size="current_size"
                    :pagination="payrollsPagination"
                    :total="filters.total"
                    @handleSizeChange="handleSizeChange"
                    @handleCurrentChange="handleCurrentChange">
            </global-pagination>
            <div class="p-4">
                <el-button @click="printTable" style="float:right"  type="info" size="mini" class="btn-info">Print</el-button>
            </div>
        </el-card>
    </div>

</template>
<script>
import paginate from '../../../mixin/pagination'
import moment from 'moment'
import html2canvas from 'html2canvas';
export default {
    name: 'PayrollArchive',
    props: {
        model: {}
    },
    mixins: [paginate],
    data() {
        return {
            payroll: [],
            payrollsPagination: [],
            loading: false,
            current_page: 1,
            current_size: 25,
            search: null,
            mode: '',
            dialogTableVisible: false,
            date: '',
            dialogTableVisiblePayslip: false,
            employee: {},
            rate: null,
            rate_ot: null,
            deductions: [],
            overtime: [],
            date: new Date(),
            salaryError: false,
            overtime_rate_per_hour: null,
            is_deduction: false,
            temp : [],
            dialogHistory: false,
            isPrinting: false,
            reportTableId: 'payrollsummary',
        }
    },
    created() {
        this.getOvertimeRate();
        this.initData();
        console.log("model : " . this.model);
    },
    filters: {
        filteDate(value) {
            if(value) {
                return moment(value, 'YYYY-MM-DD').format('YYYY-MMM-DD');
            }
        }
    },
    methods: {
        // async print() {
        //     // Pass the element id here
        //     await this.$htmlToPaper('payrollsummary');
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
    .header1 {
          
          text-align: right;
          font-size: 0.8rem;
          font-weight: bold;
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
      .small-logo {
      width: 30px; /* Adjust the width to your desired size */
      margin-right: 10px; /* Adjust the margin as needed */
    }
  }
</style>

      </head>
    
      <body>
  <header>
    <h5 class="header1" >Date:${currentDate}</h5> <!-- Display the current date -->
     </header>
        <div class="print-content">
          <!-- Your report content goes here -->
          <img src="${canvas.toDataURL()}" style="width: 100%">
        </div>
        <div class="footer">
    <h1 style="margin-right: auto; text-align: left;">Section: Finance</h1>
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

        async initData(){
            
            const overtime = await this.$API.Finance.getOvertimeRate();
            this.overtime_rate_per_hour = overtime.data

            this.date = this.$df.formatDate(this.date, "YYYY-MM-DD")

            let params = {
                current_size: this.current_size,
                current_page: this.current_page,
                search: this.search,
                date: this.$df.formatDate(this.date, "YYYY-MM-DD"),
                id: this.model,
            };

            this.loading = true;

            const res = await this.$API.Finance.getPayrolls(params);
            this.payroll = res.data.data;
            this.payrollsPagination = res.data;

            console.log(this.payroll);

            this.payroll.forEach(pay => {

                let rate = 0;
                let ot = 0;
                let deductions = 0;

                JSON.parse(pay.regular).forEach(p => {
                    rate += parseFloat(p.rate);
                    // // overtime
                    console.log(this.overtime_rate_per_hour);
                    console.log(p.total_hours_ot)
                    if ( p.total_hours_ot != 0 ? ot += p.total_hours_ot * this.overtime_rate_per_hour : ot += 0 ) ;
                    
                    console.log(ot);
                })


                JSON.parse(pay.deductions).forEach(d => {
                    deductions += parseFloat(d.amount);
                })

                pay.rate = rate;
                pay.overtime = ot;
                pay.deduct = deductions;

                // if negative return 0
                pay.salary = (rate + ot) - deductions < 0 ? 0 : (rate + ot) - deductions;
                pay.name = pay.lastname + ", " + pay.firstname + " " + pay.middlename;
            })


    
            this.employee = this.payroll.employee;
            this.deductions = this.payroll.deductions;
            this.temp = this.payroll.deductions;
            this.loading = false;

            console.log(this.payroll)
            
        }
        ,
       
        generatePayroll() {
            this.$router.push({name : 'Generate Payroll'})
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
                await this.$API.Warehouse.deleteCategory(data.id)
                this.$notify({
                    title: 'Success',
                    message: 'Successfully Deleted',
                    type: 'success'
                });
                this.payroll.splice(index, 1)
            } catch (error) {
                console.log(error);
            }
        },
        handlePayslip(data) {
            this.dialogTableVisiblePayslip = true;
            this.model = {...data}
        },
        handleView(data) {
            this.model = {...data}
            this.dialogTableVisible = true;
        },
        handleClose(done) {
            this.$EventDispatcher.fire('CLOSE_MODAL')
            done();
        },
        handleSizeChange(val) {
            this.current_size = val;
            this.getPayrolls();
        },
        handleCurrentChange(val) {
            this.current_page = val;
            this.getPayrolls();
        },
        applySearch() {
            this.getPayrolls();
        },
        changeDate(value) {
            this.date = value
            this.getPayrolls();
        },
        checkDeduction(value, index) {
            if(value > this.total_salary) {
                this.deductions.splice(index, 1)
            }
            if(this.salaryError) {
                this.deductions.splice(index, 1)
            }
        },
        async getOvertimeRate() {
            try {
                const res = await this.$API.Finance.getOvertimeRate();
                console.log(res.data);
                this.overtime_rate_per_hour = res.data
                console.log(this.overtime_rate_per_hour);
            } catch (error) {
                console.log(error);
            }
        }
    },
    watch: {
        search(val) {
            if( val == '') {
                this.getPayrolls();
            }
        }
    }
}
</script>

