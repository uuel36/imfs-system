<template>
    <div class="row">
        <div class="col-md-8">
            <el-table
                :data="records"
                style="width: 100%">
                    <el-table-column label="REGULAR HOURS">
                        <el-table-column
                            prop="date"
                            label="DATE">
                                <template slot-scope="scope">
                                    {{scope.row.date | fileDate}}
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
                        </el-table-column>
                        <el-table-column
                            prop="task"
                            label="TASK">
                        </el-table-column>
                    </el-table-column>
            </el-table>

            <el-table
                :data="records.filter(pay => pay.ot_in != null && pay.ot_out != null && pay.ot_status == 'Approved')"
                style="width: 100%; margin-top: 20px">
                    <el-table-column label="OVERTIME">
                        <el-table-column
                            prop="date"
                            label="DATE">
                                <template slot-scope="scope">
                                    {{scope.row.date | fileDate}}
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
            </el-table>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12" style="padding:10px">
                    <el-button @click="addDeduction" style="float:right;" type="info" v-if="records.length > 0">Add Deductions</el-button>
                </div>
            </div>
            <el-table
                :data="deductions[0].deductions"
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

            <div class="row">
                <div class="col-md-12" style="margin-top:10px">
                    <el-descriptions title="PAYROLL SUMMARY" direction="horisontal" :column="1" border>
                        <el-descriptions-item label="Regular Rate">{{regular_rate}}</el-descriptions-item>
                        <el-descriptions-item label="Overtime Rate">{{overtime_rate}}</el-descriptions-item>
                        <el-descriptions-item label="Deductions">{{total_deductions}}</el-descriptions-item>
                        <el-descriptions-item label="Salary">{{total_salary}}</el-descriptions-item>
                    </el-descriptions>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
export default {
    name: 'EmployeeRegularTime',
    props: {
        records: {
            type: Array,
            default: []
        },
        deductions: []
    },
    data() {
        return {
            employee: {},
        }
    },
    created() {
        console.log("employee regular time")
        
    },
    methods: {

        deleteDeductions(index) {
        this.deductions[0].deductions.splice(index, 1);
    },
        getDeductions() {
            if(this.getEmployeeId) {

            }
        },

        addDeduction() {
            this.deductions[0].deductions.push({
                type: '',
                amount: '',
            })
        },
    },
    computed: {
        getEmployeeId() {
            if(this.records.length > 0) {
                return this.records[0].employee_id
            }
        },
        regular_rate() {
            if(this.records.length > 0) {
                let rate_total = 0
                this.records.forEach(pay => {
                    let rate = pay.hasOwnProperty('rate')
                    if(!rate) {
                        return;
                    }
                    else {
                        if(pay.rate == '') {
                            return 0
                        }
                        else {
                            rate_total = parseFloat(parseFloat(rate_total) + parseFloat(pay.rate)).toFixed(2)
                        }
                    }
                })

                return rate_total
            }
            else {
                return 0;
            }

        },
        overtime_rate() {
            let records = this.records.filter(pay => pay.ot_in != null && pay.ot_out != null && pay.ot_status == 'Approved')

            if(records.length > 0) {
                let overtime = 0
                records.forEach(pay => {
                    overtime = parseFloat(parseFloat(overtime) + parseFloat(parseFloat(pay.overtime_rate) * parseFloat(pay.total_hours_ot))).toFixed(2)
                })

                return overtime
            }
            else {
                return 0
            }
        },
        total_deductions() {
            if(this.deductions[0].deductions.length > 0) {
                let total = 0
                this.deductions[0].deductions.forEach(dec => {
                    let amount = dec.hasOwnProperty('amount')
                    if(!amount) {
                        return 0;
                    }
                    else {
                        if(dec.amount == '') {
                            return 0
                        }
                        else {
                            total = parseFloat(parseFloat(total) + parseFloat(dec.amount)).toFixed(2)
                        }
                    }
                })
                return total
            }
            else {
                return 0
            }
        },
        total_salary() {
            return parseFloat(parseFloat(this.regular_rate) + parseFloat(this.overtime_rate) - parseFloat(this.total_deductions)).toFixed(2) < 0 ? 0 : parseFloat(parseFloat(this.regular_rate) + parseFloat(this.overtime_rate) - parseFloat(this.total_deductions)).toFixed(2)
        }
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
}
</script>
