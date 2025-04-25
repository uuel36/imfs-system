<template>
    
        <el-card class="box-card">

         <div class="text item" id="payrollsummary">
        <div style="width: 100%; padding: 1rem">
      </div>
      <h1 style="font-size: 2rem; font-weight: 600;">
          <img src="/img/company_logo.png" alt="Logo" style="width: 50px;">
          Archive
        </h1>
        <el-select v-model="selectedDate" placeholder="Select Date" clearable :clear-icon="clearIcon">
  <el-option
    v-for="date in uniqueDates"
    :key="date"
    :label="date | formatDate"
    :value="date"
  ></el-option>
</el-select>
                    <el-table
                    :data="filteredPayroll"
                    v-loading="loading"
                    style="width: 100%"
                    :style="{ 'font-size': '13px', 'font-weight': 'bold' }">
                    <el-table-column
        width="70"
        label="No."
        :sortable="true">
        <template slot-scope="scope">
            {{ scope.row.isTitleRow ? '' : scope.$index + 1 }}
        </template>
    </el-table-column>
    <el-table-column
    prop="name"
    label="EMPLOYEE"
    :sortable="true">
    <template slot-scope="scope">
        <span v-if="scope.row.isTitleRow" style="font-weight: bold; color: #2196F3; margin-left: 10px;"> {{ scope.row.title }}</span>
        <span v-else>{{ scope.row.name }}</span>
    </template>
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
                        <el-table-column
            prop="from_date"
            label="FROM"
            :sortable="true">
                <template slot-scope="scope">
                    {{scope.row.from_date | filteDate}}
                </template>
        </el-table-column>
                </el-table>
                <global-pagination
                    :current_page="current_page"
                    :current_size="current_size"
                    :pagination="payrollsPagination"
                    :total="filters.total"
                    @handleSizeChange="handleSizeChange"
                    @handleCurrentChange="handleCurrentChange">
            </global-pagination>

            </div>
            <el-dialog
                title="History" width="45%"
                :before-close="handleClose"
                :visible.sync="dialogHistory">
                    <payroll-archive :model="id"></payroll-archive>
            </el-dialog>
            <div class="p-4">
<el-button @click="printTable"  style="float:right" type="info" size="mini" class="no-print btn-info">Print</el-button>
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
import html2canvas from 'html2canvas';
import moment from 'moment'
export default {
    name: 'Archive',
    mixins: [paginate],
    data() {
        return {
            payroll: [],
            payrollData: [],
            payrollsPagination: [],
            loading: false,
            current_page: 1,
            current_size: 100,
            search: null,
            mode: '',
            model: {},
            dialogTableVisible: false,
            date: '',
            id: null,
            dialogTableVisiblePayslip: false,
            employee: {},
            rate: null,
            rate_ot: null,
            deductions: [],
            overtime: [],
            salaryError: false,
            overtime_rate_per_hour: null,
            is_deduction: false,
            temp : [],
            dialogHistory: false,
            isPrinting: false,
            reportTableId: 'payrollsummary',
            uniqueDates: [], // New property to store unique from_date values
            selectedDate: '', // New property to store the selected date
            clearIcon: 'el-icon-circle-close', // Use the icon of your choice

        }
    },
    created() {
        this.getOvertimeRate();
        this.getPayrolls();
        this.getPayrollsArchive(); 
    },
    filters: {
        filteDate(value) {
            if(value) {
                return moment(value, 'YYYY-MM-DD').format('YYYY-MMM-DD');
            }
        }
    },
    computed: {
    filteredPayroll() {
      // Filter the data based on the selected date
      if (this.selectedDate) {
        return this.payroll.filter(item => {
          if (item.isTitleRow) {
            // Always include title rows
            return true;
          } else {
            // Include regular rows only if the dates match
            return item.from_date === this.selectedDate;
          }
        });
      } else {
        // If no date is selected, return the entire payroll data
        return this.payroll;
      }
    },
  },
    methods: {
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
       
        async getPayrollsArchive() {
      try {
        let params = {
          current_size: this.current_size,
          current_page: this.current_page,
          search: this.search,
          date: this.selectedDate,
        };
        this.loading = true;
        const res = await this.$API.Finance.getArchiveRecords(params);
        this.payrollArchive = res.data.data;
        
        // Extract unique from_date values
        this.uniqueDates = [...new Set(res.data.data.map(item => item.from_date))];

        this.loading = false;
      } catch (error) {
        console.log(error);
      }
    },
        async getPayrolls() {
        try {
            this.date = this.$df.formatDate(this.date, "YYYY-MM-DD");
            const overtime = await this.$API.Finance.getOvertimeRate();
            this.overtime_rate_per_hour = overtime.data
            let params = {
                current_size: this.current_size,
                current_page: this.current_page,
                search: this.search,
                
                date: this.selectedDate,
            };

            const res = await this.$API.Finance.getPayrolls(params);
            
            this.payroll = res.data.data;
            this.payrollsPagination = res.data;

            this.payroll = [];
            let currentFromDate = null;

            for (const pay of res.data.data) {
                let rate = 0;
                let ot = 0;
                let deductions = 0;

                pay.regular.forEach(p => {
                    rate += parseFloat(p.rate);
                    if (p.total_hours_ot != 0) {
                        ot += p.total_hours_ot * this.overtime_rate_per_hour;
                    }
                });

                pay.deductions.forEach(d => {
                    deductions += parseFloat(d.amount);
                });

                pay.rate = rate;
                pay.overtime = ot;
                pay.deduct = deductions;
                pay.salary = (rate + ot) - deductions < 0 ? 0 : (rate + ot) - deductions;
                pay.name = pay.employee.lastname + ", " + pay.employee.firstname + " " + pay.employee.middlename;

                // Check if the from_date value has changed
                if (pay.from_date !== currentFromDate) {
                    currentFromDate = pay.from_date;

                    // Add a special row for the title
                    this.payroll.push({
                        isTitleRow: true, // Set a flag to identify this row as a title
                        title: `From Date: ${pay.from_date}`, // Customize the title content
                        // ... you can add other properties if needed
                    });
                }

                // Add the regular row
                this.payroll.push(pay);
            }

            this.employee = this.payroll.employee;
            this.deductions = this.payroll.deductions;
            this.temp = this.payroll.deductions;
            this.loading = false;

            console.log(this.payroll);
        } catch (error) {
            console.error(error);
        }
    },
       
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
        },
    }
}
</script>