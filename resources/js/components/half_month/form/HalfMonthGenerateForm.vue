<template>
    <el-card class="box-card">
        <div  class="text item">
            <el-form :inline="true" :model="form" :rules="rules" ref="form" class="demo-form-inline">
                <el-form-item label="From" prop="date_from">
                    <el-date-picker
                        v-model="form.date_from"
                        type="date"
                        style="width:100%"
                        format="yyyy-MM-dd"
                        value-format="yyyy-MM-dd"
                        placeholder="Select date from"
                        :disabled="true">
                    </el-date-picker>
                </el-form-item>
                <el-form-item label="To" prop="date_to">
                    <el-date-picker
                        v-model="form.date_to"
                        type="date"
                        style="width:100%"
                        format="yyyy-MM-dd"
                        value-format="yyyy-MM-dd"
                        placeholder="Select date from"
                        :disabled="true">
                    </el-date-picker>
                </el-form-item>

                <el-form-item label="Area" prop="area_id">
                    <el-select v-model="form.area_id" placeholder="Select" :value="area_name">
                        <el-option
                            v-for="item in areas"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>

                <el-form-item>
                    <el-button :loading="loadingGenerate" type="primary" @click="submitForm('form')">Generate</el-button>
                    <el-button @click="resetForm('form')">Reset</el-button>
                </el-form-item>
            </el-form>
        </div>
    </el-card>
</template>
<script>
export default {
    name: 'HalfMonthGenerateForm',
    data() {
        return {
            form: {
                date_from: '',
                date_to: '',
                area_id: '',
            },
            rules : {
                date_from: [
                    { required: true, message: 'Please input date', trigger: 'blur' }
                ],
                date_to: [
                    { required: true, message: 'Please input date', trigger: 'blur' }
                ],
                area_id: [
                    { required: true, message: 'Please select area', trigger: 'blur' }
                ],
            },
            loadingGenerate: false,
            areas: [],
            area_name: '',
        }
    },
    created() {
        this.form.date_to = new Date()
        
        // deduct 15 days from current date
        this.form.date_from = new Date(new Date().setDate(new Date().getDate() - 15));

        this.getAreas();

        this.$EventDispatcher.listen('NEW_PAYROLL', data => {
            this.resetForm('form');
        })
    },
    methods: {
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
            if (valid) {
                this.generateReport();
            } else {
                console.log('error submit!!');
                return false;
            }
            });
        },
        resetForm(formName) {
            this.$EventDispatcher.fire('RESET_HALFMONT', 'reset')
            this.$refs[formName].resetFields();
        },
        async getAreas() {
            try {
                const res = await this.$API.Area.getAllAreas();
                this.areas = res.data;
            } catch (error) {
                console.log(error);
            }
        },
        async generateReport() {
            try {
                this.loadingGenerate = true
                this.form.date_to = this.$df.formatDate(this.form.date_to, "YYYY-MM-DD")
                this.form.date_from = this.$df.formatDate(this.form.date_from, "YYYY-MM-DD")
                const res = await this.$API.Month.generateReport(this.form);

                if(res.data == 'already') {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Already Generated'
                    });
                    this.loadingGenerate = false

                    return;
                }
                this.loadingGenerate = false

                //console.log(res.data);

                this.$EventDispatcher.fire('NEW_GENERATE_REPORT', res.data);
            } catch (error) {
                console.log(error);
            }
        }
    },
}
</script>
