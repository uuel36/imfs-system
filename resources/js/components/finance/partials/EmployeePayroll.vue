<template>
    <div class="row">
        <!-- <div class="col-md-12" style="padding:10px">
            <el-checkbox v-model="is_deduction" v-if="payroll.length > 0">Pay Deduction?</el-checkbox>
            <el-button @click="addDeduction" style="float:right;" type="info" v-if="payroll.length > 0">Add Custom Deductions</el-button>
        </div> -->
        <div class="col-md-12">
            <el-table
                v-loading="loading"
                element-loading-text="Loading..."
                element-loading-spinner="el-icon-loading"
                element-loading-background="rgba(0, 0, 0, 0.8)"
                :data="payroll"
                style="width: 100%"
                :style="{ 'font-size': '13px', 'font-weight': 'bold' }"
                >
                    <el-table-column label="EMPLOYEES">
                        <el-table-column type="expand">
                            <template slot-scope="props">
                                <div class="" style="padding: 10px">
                                    <employee-regular-time :records="props.row.records" :deductions="props.row.records"></employee-regular-time>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column
                            label="NAME" style="font-weight: 600">
                                <template slot-scope="scope">
                                    <p style="font-weight: 600; padding:0">{{scope.row.name}}</p>
                                </template>
                        </el-table-column>
                    </el-table-column>
            </el-table>
        </div>
        <!--<div class="col-md-4">
            <el-table
                :data="deductions"
                style="width: 100%">
                    <el-table-column label="DEDUCTIONS">
                        <el-table-column
                            prop="type"
                            label="TYPE">
                                <template slot-scope="scope">
                                    <template v-if="scope.row.type != 'SSS' && scope.row.type != 'PhilHealth'">
                                        <el-input v-model="scope.row.type"></el-input>
                                    </template>
                                    <template v-else>
                                        {{scope.row.type}}
                                    </template>
                                </template>
                        </el-table-column>
                        <el-table-column
                            prop="amount"
                            label="AMOUNT">
                                <template slot-scope="scope">
                                    <template v-if="scope.row.type != 'SSS' && scope.row.type != 'PhilHealth'">
                                        <el-input v-model="scope.row.amount" type="number"></el-input>
                                    </template>
                                    <template v-else>
                                        {{scope.row.amount}}
                                    </template>
                                </template>
                        </el-table-column>
                        <el-table-column
                            label="Action"
                            fixed="right"
                            width="80">
                                <template slot-scope="scope">
                                    <el-button v-if="scope.row.type != 'SSS' && scope.row.type != 'PhilHealth'" @click="deleteDeductions(scope.$index)"><i class="fas fa-trash"></i></el-button>
                                </template>
                        </el-table-column>
                    </el-table-column>
            </el-table>
        </div> -->
        <div class="col-md-12">

            <div class="row">
                <div class="col-md-12" style="padding:10px">
                    <el-button @click="generatePayroll" style="float:right; marging-top:20px" type="primary" v-if="payroll.length > 0 ">Generate Payrolll</el-button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
export default {
    data() {
        return {
            payroll: [],
            deductions: [],
            temp: [],
            loading: true,
            overtime_rate_per_hour: null,
            deduc: [],
            employee: {},
            is_deduction: false,
            deductions: [],
            att: []
        }
    },
    created() {
        console.log('employee payroll')
        this.getOvertimeRate();
        this.$EventDispatcher.listen('RESET_FORM', data => {

            this.payroll = []
            this.deductions = []
            this.loading = true
        })
        this.$EventDispatcher.listen('NEW_GENERATE_PAYROLL', data => {

            let attendance = [];
            this.rate = 0
            this.rate_ot = 0

            Object.keys(data.attendance).forEach(key => {

                
                attendance.push({
                    name: key,
                    deductions: [],
                    records: data.attendance[key].map(att => {
                        att.overtime_rate = this.overtime_rate_per_hour.overtime_rate_hour
                        att.deductions = [ { type : 'SSS', amount: att.sss },  { type : 'PhilHealth', amount: att.philhealth }];
                        if(att.total_hours < 8) {
                            let rate = att.rate / 8
                            att.rate = parseFloat(parseFloat(rate) * parseFloat(att.total_hours)).toFixed(2)
                        }
                        return att
                    })
                })

                attendance.forEach( att => {
                    att.deductions = [
                        { type : 'SSS', amount: att.records[0].sss },
                        { type : 'PhilHealth', amount:  att.records[0].philhealth}
                    ]
                })
            });

            // // deductions must be an array example : [{type: 'sss', amount: 100}, {type: 'philhealth', amount: 100}]
            // attendance = attendance.map(att => {
            //     // deductions must be an array example : [{type: 'sss', amount: 100}, {type: 'philhealth', amount: 100}]
            //     att.deductions = [
            //         { type : 'SSS', amount: data.attendance[att][0].sss },
            //         { type : 'PhilHealth', amount: data.attendance[att][0].philhealth}
            //     ]
            // })

            this.payroll = attendance.map(att => {
                if(att.records.length > 0) {
                    att.employee_id = att.records[0].employee_id
                }
                else {
                    att.employee_id = ''
                }

                return att
            })

            this.employee = data.employee
            this.deductions = data.deductions
            this.temp = data.deductions
            this.attendance = attendance
            console.log(this.attendance)
            this.loading = false

        });
    },
    methods: {
        async getDeductions(employee_id) {
            try {
                const res = await this.$API.Finance.getDeductions(employee_id)
                return res.data
            } catch (error) {
                console.log(error);
            }
        },
        async getOvertimeRate() {
            try {
                const res = await this.$API.Finance.getOvertimeRate();
                this.overtime_rate_per_hour = res.data
            } catch (error) {
                console.log(error);
            }
        },
        async generatePayroll() {
            try {
                // let overtime = this.records.filter(pay => pay.ot_in != null && pay.ot_out != null && pay.ot_status == 'Approved')
                // if(overtime.length > 0) {
                //     if(!this.rate_ot || this.rate_ot === 0) {
                //         this.$notify.error({
                //             title: 'Error',
                //             message: 'Please input rate per hour for overtime'
                //         });
                //         return;
                //     }
                // }
                // this.employee.regular = this.records
                // this.employee.overtime = overtime
                // if(this.is_deduction) this.employee.deductions = this.deductions
                // else this.employee.deductions = []
                this.payroll.map(pay => {
                    pay.overtime = pay.records.filter(pay => pay.ot_in != null && pay.ot_out != null && pay.ot_status == 'Approved')
                    let temp_de = pay.deductions;
                    if(this.is_deduction) {
                        pay.deductions = temp_de
                        this.deductions.forEach(d => {
                            pay.deductions.push(d);
                        })
                    }
                    else {
                        pay.deductions = []
                    }
                })

                this.employee.payroll = this.payroll
                this.employee.attendance = this.attendance
                console.log(this.employee.attendance);
                console.log(this.employee)
                this.employee.rate = 0;
                const res = await this.$API.Finance.storePayroll(this.employee);
                this.$EventDispatcher.fire('NEW_PAYROLL', res.data)
                this.payroll = []
                this.loading = true
                this.deductions = []
                this.$notify({
                    title: 'Success',
                    message: 'Successfully Generate Payroll',
                    type: 'success'
                });
            } catch (error) {
                console.log(error);
            }
        },
        addDeduction() {
            this.deductions.push({
                type: '',
                amount: '',
            })
        },
        deleteDeductions(index) {
            this.deductions.splice(index, 1)
        },
    },
    filters: {
        timeFormat(value) {
            if(value) {
                return moment(value, 'HH:mm:ss').format('h:mm a')
            }
            return '-'
        },
        fileDate(value) {
            if(value) {
                return moment(value, 'YYYY-MM-DD').format('YYYY-MMM-DD')
            }
        }
    },
    watch: {
        payroll(newVal) {  
            if(newVal.length > 0) {
                newVal.map(pay => {
                    let employee_id = 0;
                    if(pay.records.length > 0) {
                        employee_id = pay.records[0].employee_id
                    }
                        this.getDeductions(employee_id).then((res) => {
                            pay.deductions = res
                        })


                    //
                    return pay
                })

                return newVal;
            }
        }
    }
}
</script>
