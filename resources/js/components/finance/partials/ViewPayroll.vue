<template>
    <div class="row" id="printBreakDown">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <p>Name: <span style="font-weight: 700">{{name}}</span></p>
                    <p>Generate Payroll : <span style="font-weight: 700">{{payroll.date | filteDate}}</span></p>
                </div>
                <div class="col-md-6">
                    <p>From: <span style="font-weight: 700">{{payroll.from_date | filteDate}}</span></p>
                    <p>To: <span style="font-weight: 700">{{payroll.to_date | filteDate}}</span></p>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <el-table
                        :data="payroll.regular"
                        style="width: 100%">
                            <el-table-column label="REGULAR HOURS">
                                <el-table-column
                                    prop="date"
                                    label="DATE">
                                        <template slot-scope="scope">
                                            {{scope.row.date | filteDate}}
                                        </template>
                                </el-table-column>
                                <el-table-column
                                    prop="status"
                                    label="STATUS">
                                        <template slot-scope="scope">
                                            <template v-if="scope.row.status == 'Full'">
                                                <el-tag type="success" effect="dark">{{scope.row.status}}</el-tag>
                                            </template>
                                            <template v-if="scope.row.status == 'Under Time'">
                                                <el-tag type="primary" effect="dark">{{scope.row.status}}</el-tag>
                                            </template>
                                            <template v-if="scope.row.status == 'Half Day'">
                                                <el-tag type="warning" effect="dark">{{scope.row.status}}</el-tag>
                                            </template>
                                        </template>
                                </el-table-column>
                                <el-table-column
                                    prop="time_in"
                                    label="IN">
                                        <template slot-scope="scope">
                                            {{scope.row.time_in | timeFormat}}
                                        </template>
                                </el-table-column>
                                <el-table-column
                                    prop="time_out"
                                    label="OUT">
                                        <template slot-scope="scope">
                                            {{scope.row.time_out | timeFormat}}
                                        </template>
                                </el-table-column>
                                <el-table-column
            prop="rate"
            label="RATE">
            <template slot-scope="scope">
                ₱{{ parseFloat(scope.row.rate).toFixed(2) }}
            </template>
        </el-table-column>
                            </el-table-column>
                    </el-table>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12" style="margin-bottom: 10px;">
                            <el-table
                                :data="payroll.overtime"
                                style="width: 100%;">
                                    <el-table-column label="OVERTIME">
                                        <el-table-column
                                            prop="date"
                                            label="DATE">
                                                <template slot-scope="scope">
                                                    {{scope.row.date | filteDate}}
                                                </template>
                                        </el-table-column>
                                        <el-table-column
                                            prop="time_in"
                                            label="IN">
                                                <template slot-scope="scope">
                                                    {{scope.row.ot_in | timeFormat}}
                                                </template>
                                        </el-table-column>
                                        <el-table-column
                                            prop="time_out"
                                            label="OUT">
                                                <template slot-scope="scope">
                                                    {{scope.row.ot_out | timeFormat}}
                                                </template>
                                        </el-table-column>
                                        <el-table-column
                                            prop="total_hours_ot"
                                            label="HOURS">

                                        </el-table-column>
                                        <el-table-column
                                            prop="overtime_rate"
                                            label="RATE">

                                        </el-table-column>
                                    </el-table-column>
                                        <!--<el-table-column
                                            fixed="right"
                                            width="110"
                                            label="ACTION">
                                            <template slot-scope="scope">
                                                <button :disabled="scope.row.is_double_pay" @click="handleDoublePay(scope.row)" class="btn btn-success btn-sm">Double Pay</button>
                                            </template>
                                        </el-table-column> -->
                            </el-table>
                        </div>
                    </div>
                    <el-table
            :data="payroll.deductions"
            style="width: 100%">
            <el-table-column label="DEDUCTIONS">
              <el-table-column
                prop="type"
                label="TYPE">
              </el-table-column>
              <el-table-column
                prop="amount"
                label="AMOUNT"
                align="right"
                header-align="right">
                <template slot-scope="scope">
                  ₱{{ parseFloat(scope.row.amount).toFixed(2) }}
                </template>
              </el-table-column>
            </el-table-column>
          </el-table>
                    <div class="row">
                        
                        <div class="col-md-12" style="margin-top:10px">
                          <el-descriptions title="PAYROLL SUMMARY" direction="horizontal" :column="1" border>
    <el-descriptions-item label="Regular Rate">
        <span style="float: right;">₱{{ parseFloat(regular_rate).toFixed(2) }}</span>
    </el-descriptions-item>
    <el-descriptions-item label="Overtime Rate">
        <span style="float: right;">₱{{ parseFloat(overtime_rate).toFixed(2) }}</span>
    </el-descriptions-item>
    <el-descriptions-item label="Deductions">
        <span style="float: right;">₱{{ parseFloat(total_deductions).toFixed(2) }}</span>
    </el-descriptions-item>
    <el-descriptions-item label="Salary">
        <span style="float: right;">₱{{ parseFloat(total_salary).toFixed(2) }}</span>
    </el-descriptions-item>
</el-descriptions>
    </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
  <el-button @click="printTable" type="info" size="mini" class="btn-info">Print</el-button>
</div>
        </div>
</template>

<script>
import html2canvas from 'html2canvas';
import moment from "moment"
export default {
    name: 'ViewPayroll',
    props: {
        model: {}
    },
    data() {
        return {

            payroll: {},
            isPrinting: false,
            reportTableId: 'printBreakDown',
        }
    },
    created() {
        this.payroll = this.model
        console.log("view payroll")
        console.log(this.payroll)
    },
    computed: {
        totalSalary() {
            let total = 0
            // this.payroll.item.forEach(pay => {
            //     total += pay.salary
            // })

            return total
        },
        totalHours() {
            let total = 0
            // this.payroll.item.forEach(pay => {
            //     total += (pay.hours - 1)
            // })

            return total
        },
        name() {
            return this.payroll.employee.lastname + ", " + this.payroll.employee.firstname + " - ( "+ this.payroll.employee.position.name + " )"
        },
        regular_rate() {
            let total = 0
            this.payroll.regular.forEach(reg => {
                total = parseFloat(parseFloat(total) + parseFloat(reg.rate)).toFixed(2)
            })
            return total;
        },
        overtime_rate() {
            let total = 0
            this.payroll.overtime.forEach(over => {
                // total = parseFloat(parseFloat(total) + parseFloat(over.overtime_rate)).toFixed(2)
                total = parseFloat(parseFloat(total) + parseFloat(parseFloat(over.overtime_rate) * parseFloat(over.total_hours_ot))).toFixed(2)
            })
            return total;
        },
        total_deductions() {
            let total = 0 //
            this.payroll.deductions.forEach(dec => {
                total = parseFloat(parseFloat(total) + parseFloat(dec.amount)).toFixed(2)
            })

            return total
        },
        total_salary() {
            return parseFloat(parseFloat(this.regular_rate) + parseFloat(this.overtime_rate) - parseFloat(this.total_deductions)).toFixed(2) < 0 ? 0 : parseFloat(parseFloat(this.regular_rate) + parseFloat(this.overtime_rate) - parseFloat(this.total_deductions)).toFixed(2)
        }
    },
    filters: {
        addComma(value) {
            if(value) {
                return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
            else {
                return 0;
            }
        },
        filteDate(value) {
            if(value) {
                return moment(value, 'YYYY-MM-DD').format('YYYY-MMM-DD');
            }
        },
        timeFormat(value) {
            if(value) {
                return moment(value, 'HH:mm:ss').format('h:mm a')
            }
            return '-'
        },
    },
    methods: {
        async printTable() {
  this.isPrinting = true;

  const printButton = document.querySelector('.btn-info');
  if (printButton) {
    printButton.style.display = 'none';
  }

  const printContents = document.getElementById(this.reportTableId);
  const clonedTable = printContents.cloneNode(true);
  const actionColumn = clonedTable.querySelector('.el-table-column--selection');
  if (actionColumn) {
    actionColumn.parentNode.removeChild(actionColumn);
  }

  const style = document.createElement('style');
  style.innerHTML = `
    @media print {
      .btn-info {
        display: none !important;
      }
    }
  `;

  document.head.appendChild(style);

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
                width: 50px;
                margin-right: 10px;
              }
              .title {
                font-size: 24px;
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
            }
            .header-content {
              display: inline-flex;
              align-items: center;
            }
            .small-logo {
              width: 30px;
              margin-right: 10px;
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

          <h1 class="title">Payroll</h1>
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
      printWindow.print();
      printWindow.onafterprint = function () {
        this.isPrinting = false;
        if (printButton) {
          printButton.style.display = 'block';
        }
        printWindow.close();
      }.bind(this);
    }.bind(this);
    });
  },
    },
    watch: {
        model(newVal, oldVal) {
            if(newVal != oldVal) {
                this.payroll = newVal
            }
        }
    }
}
</script>
