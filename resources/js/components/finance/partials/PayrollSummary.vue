<template>
    
    <div class="content-wrapper">

        <el-card class="box-card">

            <div class="text item" id="payrollsummary">
                <div style="width: 100%; padding: 1rem">
        <h1 style="font-size: 2rem; font-weight: 600; text-align: center">
          <img src="/img/company_logo.png" alt="Logo" style="width: 50px; margin-right: 10px;">
          MS Green Gold Ventures Summary Payroll from {{ this.payroll[0] ? this.payroll[0].from_date : " ---- " }}
        </h1>
      </div>
      <el-button type="primary" @click="showHistory">Archive Payroll</el-button>
                    <el-table
                    :data="payroll"
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
                            prop="name"
                            label="EMPLOYEE"
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
                        <el-table-column
                        prop="from_date"
                        label="PAYROLL GENERATE"
                        :sortable="true">
                            <template slot-scope="scope">
                                {{scope.row.from_date | filteDate}}
                            </template>
                    </el-table-column>
                       
                    <!-- <el-table-column label="Employee Payroll History" width="150">
          <template slot-scope="scope">
            <el-button
              type="text"
              size="mini"
              @click="showEmployeeHistoryModal(scope.row)"
            >
              Employee H
            </el-button>
          </template>
        </el-table-column> -->

                </el-table>
  

            </div>
            <el-dialog
            
  title="Employee Payroll History"
  :visible.sync="employeeHistoryModalVisible"
  width="60%"
  @close="handleEmployeeHistoryModalClose"
>



<!-- Additional modal content if needed -->
<p v-if="selectedEmployee"><strong>Employee Name:</strong> {{ selectedEmployee.name }}</p>
<!-- Add more employee information or components as needed -->
  <el-table
  :data="payrollArchive"
  v-loading="loading"
  style="width: 100%"
  :style="{ 'font-size': '13px', 'font-weight': 'bold' }"
  @row-click="handleRowClick"
>
  <!-- Your existing columns go here -->

  <!-- Add the column for from_date_archive -->
  <el-table-column
  prop="from_date"
  label="PAYROLL ARCHIVE"
  :sortable="false" 
>
  <template slot-scope="scope">
    <!-- Check if from_date is available for the current row -->
    <span v-if="scope.row.from_date !== null && scope.row.from_date !== undefined">
      {{ scope.row.from_date | filteDate }}
    </span>
    <span v-else>
      <!-- You can customize this message or leave it empty -->
      No Archive Data
    </span>
  </template>
</el-table-column>

<el-table-column type="expand">
  <template slot-scope="props">
    <!-- Add content for the expandable column here -->
    <div>
      <!-- Content to display when expanded, you can customize this based on your needs -->
      <p v-if="props.row.from_date_archive !== null && props.row.from_date_archive !== undefined">
        <div>

<div class="payslip-container" id="printMe" v-loading="loadingPayslip">

    <div style="width: 100%; padding: 1rem">
<h1 style="font-size: 2rem; font-weight: 600; text-align: center">
  <img src="/img/company_logo.png" alt="Logo" style="width: 50px; margin-right: 10px;">
  MS Green Gold Ventures
</h1>
</div>
    
    <div class="row">
        <div class="col-md-12 name">
            <h3>{{name}}</h3>
            <hr>
        </div>
        <div class="col-md-12 info">
            <el-descriptions border :column="1">
                <el-descriptions-item label="Pay Period">{{fromDate}} - {{toDate}}</el-descriptions-item>
                <el-descriptions-item label="Position">{{position.name}}</el-descriptions-item>
            </el-descriptions>
            <table class="table table-bordered table-sm">
                <thead>
                    <th>Particulars</th>
                    <th></th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            Number of Days: {{days}}
                        </td>
                        <td>
                            P {{regular_salary}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Number of Overtime: {{overtime}}
                        </td>
                        <td>
                            P {{overtime_salary}}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right">
                            Gross Salary
                        </td>
                        <td>
                            P {{gross_salary}}
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table table-bordered table-sm" style="margin-top: -10px">
                <thead>
                    <th>Deductions</th>
                    <th></th>
                </thead>
                <tbody>
                    <tr v-for="(dec, index) in payslip.deductions" :key="index">
                        <td>
                            {{dec.type}}
                        </td>
                        <td>
                            P {{dec.amount}}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right">
                            Total Deductions
                        </td>
                        <td>
                            P {{total_deductions}}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="col-md-12" style="text-align:right; padding-right:10px; padding-right:20px;">
                    <p class="bold-text">Net Salary: <span class="bold-text">{{net_salary}}</span></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12" style="text-align:center; margin-top: 20px;">
<button @click="print" class="btn btn-info btn-sm">Print</button>
</div>
</div>
</div>

      </p>
      <p>
       
      </p>
    </div>
  </template>
</el-table-column>
</el-table>


  <!-- Customize the modal content based on your requirements -->
</el-dialog>


            <el-dialog
            title="" width="90%"
            :before-close="handleClose"
            :visible.sync="dialogHistory"
            custom-class="custom-dialog">
                <Archive></Archive>
        </el-dialog>
            <div class="p-4">
<el-button @click="printTable"  style="float:right" type="info" size="mini" class="no-print btn-info">Print</el-button>
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
import moment from 'moment'
export default {
    name: 'PayrollSummary',
    mixins: [paginate],
    data() {
        return {
            payroll: [],
            payrollData: [],
            payrollsPagination: [],
            loading: false,
            current_page: 1,
            current_size: 25,
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
            employeeHistoryModalVisible: false,
      selectedEmployee: null,
      payrollArchive: [],
    uniqueDates: [],
    payslip: {},
            loadingPayslip: false
        }
    },
    created() {
        this.getOvertimeRate();
        this.getPayrolls();
        this.getPayrollsArchive();
        if(this.model && this.model.id) {
            this.loadingPayslip = true;
            this.payslip = this.model
            this.loadingPayslip = false
        }
    },
    filters: {
        filteDate(value) {
            if(value) {
                return moment(value, 'YYYY-MM-DD').format('YYYY-MMM-DD');
            }
        }
    },
 
    computed: {
    name() {
        return this.payslip.employee ? `${this.payslip.employee.lastname}, ${this.payslip.employee.firstname} ${this.payslip.employee.middlename}` : 'Name Not Available';
    },
    fromDate() {
        return this.payslip.from_date ? this.$df.formatDate(this.payslip.from_date, 'YYYY-MMM-DD') : 'From Date Not Available';
    },
    toDate() {
        return this.payslip.to_date ? this.$df.formatDate(this.payslip.to_date, 'YYYY-MMM-DD') : 'To Date Not Available';
    },
    position() {
        return this.payslip.employee && this.payslip.employee.position ? this.payslip.employee.position : 'Position Not Available';
    },
    days() {
        return this.payslip.regular ? this.payslip.regular.length : 0;
    },
    overtime() {
        return this.payslip.overtime ? this.payslip.overtime.length : 0;
    },
    regular_salary() {
        let salary = 0;
        if (this.payslip.regular) {
            this.payslip.regular.forEach(reg => {
                salary += parseFloat(reg.rate);
            });
        }
        return salary;
    },
    overtime_salary() {
        let salary = 0;
        if (this.payslip.overtime) {
            this.payslip.overtime.forEach(reg => {
                salary = parseFloat(parseFloat(salary) + parseFloat(parseFloat(reg.overtime_rate) * parseFloat(reg.total_hours_ot))).toFixed(2);
            });
        }
        return salary;
    },
    total_deductions() {
        let deductions = 0;
        if (this.payslip.deductions) {
            this.payslip.deductions.forEach(reg => {
                deductions += parseFloat(reg.amount);
            });
        }
        return deductions;
    },
    gross_salary() {
        return (parseFloat(this.regular_salary) + parseFloat(this.overtime_salary)).toFixed(2);
    },
    net_salary() {
        const grossSalary = parseFloat(this.gross_salary);
        const totalDeductions = parseFloat(this.total_deductions);
        const netSalary = grossSalary - totalDeductions;

        return "P " + Math.abs(netSalary).toFixed(2);
    },
    // ... other computed properties
},

    methods: {
      showHistory() {
            this.dialogHistory = true
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
handleRowClick() {
    // Handle row click event here, update selectedEmployee and show modal
    const employeeName = row.name; // Assuming the employee information is nested within the row object
    this.selectedEmployee = { name: employeeName };
    this.employeeHistoryModalVisible = true;
},


        showEmployeeHistoryModal(row) {
      this.selectedEmployee = row;
      this.employeeHistoryModalVisible = true;
    },

    handleEmployeeHistoryModalClose() {
      this.selectedEmployee = null;
      this.employeeHistoryModalVisible = false;
    },
     

        async getPayrolls() {
        try {
            this.date = this.$df.formatDate(this.date, "YYYY-MM-DD");
            const overtime = await this.$API.Finance.getOvertimeRate();
            this.overtime_rate_per_hour = overtime.data
            const params = {
            current_size: 89,
            current_page: 1,
            search: this.search,
        };

        // Order by from_date in descending order to get the latest payroll first
        const res = await this.$API.Finance.getPayrolls(params, '-from_date');
        
            this.payroll = res.data.data;
            this.payrollsPagination = res.data;

            this.payroll.forEach(pay => {
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
            });

            this.payroll.forEach(pay => {
        // Existing code...

        // Add archive data if available
        const archiveRecord = this.payrollArchive.find(record => record.employee_id === pay.employee.id);
        pay.from_date_archive = archiveRecord ? archiveRecord.from_date : null;
      });

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
        }
    }
}
</script>