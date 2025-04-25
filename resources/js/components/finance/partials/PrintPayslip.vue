<template>
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
                                <td>Number of Days: {{days}}</td>
                                <td style="text-align: right;">₱ {{regular_salary.toFixed(2)}}</td>
                            </tr>
                            <tr>
                                <td>Number of Overtime: {{overtime}}</td>
                                <td style="text-align: right;">₱ {{overtime_salary.toFixed(2)}}</td>
                            </tr>
                            <tr>
                                <td style="text-align: left;">Gross Salary</td>
                                <td style="text-align: right;">₱ {{gross_salary}}</td>
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
                                <td>{{dec.type}}</td>
                                <td style="text-align: right;">₱ {{dec.amount.toFixed(2)}}</td>
                            </tr>
                            <tr>
                                <td style="text-align: left;">Total Deductions</td>
                                <td style="text-align: right;">₱ {{total_deductions.toFixed(2)}}</td>
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
</template>

<script>
import moment from 'moment'
export default {
    name: 'PrintPayslip',
    props: {
        model: {}
    },
    data() {
        return {
            payslip: {},
            loadingPayslip: false
        }
    },
    created() {
        if(this.model && this.model.id) {
            this.loadingPayslip = true;
            this.payslip = this.model
            this.loadingPayslip = false
        }
    },
    computed: {
        name() {
            return this.payslip.employee.lastname +", "+this.payslip.employee.firstname+" "+this.payslip.employee.middlename
        },
        fromDate() {
            return moment(this.payslip.from_date, 'YYYY-MM-DD').format('YYYY-MMM-DD');
        },
        toDate() {
            return moment(this.payslip.to_date, 'YYYY-MM-DD').format('YYYY-MMM-DD');
        },
        position() {
            return this.payslip.employee.position
        },
        days() {
            let days = 0
            this.payslip.regular.forEach(ref => {
                days++
            })

            return days
        },
        overtime() {
            let days = 0
            this.payslip.overtime.forEach(ref => {
                days++
            })

            return days
        },
        regular_salary() {
            let salary = 0
            this.payslip.regular.forEach(reg => {
                salary += parseFloat(reg.rate)
            })

            return salary
        },
        overtime_salary() {
            let salary = 0
            this.payslip.overtime.forEach(reg => {
                salary = parseFloat(parseFloat(salary) + parseFloat(parseFloat(reg.overtime_rate) * parseFloat(reg.total_hours_ot))).toFixed(2)
            })

            return salary
        },
        total_deductions() {
            let salary = 0
            this.payslip.deductions.forEach(reg => {
                salary += parseFloat(reg.amount)
            })

            return salary
        },
        gross_salary() {
            return parseFloat(parseFloat(this.regular_salary) + parseFloat(this.overtime_salary)).toFixed(2)
        },
        net_salary() {
            return parseFloat(parseFloat(this.gross_salary) - parseFloat(this.total_deductions)).toFixed(2)
        },
        net_salary() {
    const grossSalary = parseFloat(this.gross_salary);
    const totalDeductions = parseFloat(this.total_deductions);
    const netSalary = grossSalary - totalDeductions;

    // Use Math.abs() to get the absolute value
    return "₱ " + Math.abs(netSalary).toFixed(2);
}
    },
    methods: {
        async print() {
            // Pass the element id here
            await this.$htmlToPaper('printMe');
        },
    },
    watch: {
        model(newVal, oldVal) {
            if(newVal != oldVal) {
                this.loadingPayslip = true;
                this.payslip = newVal
                this.loadingPayslip = false
            }
        }
    }

}
</script>
<style lang="scss">
    .payslip-container {
        border-style: solid;
        border-color: grey;
        border-width: 1px;

        .name {
            margin-bottom: -20px;
        }
        .bold-text {
  font-weight: bold;
}
      
    }
</style>
