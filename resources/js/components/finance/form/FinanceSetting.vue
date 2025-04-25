<template>
    <div>
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="page_title">MANAGE FINANCE SETTINGS</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/home">Home</a></li>
                            <li class="breadcrumb-item active">Setting</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <el-tabs type="border-card">
                        <el-tab-pane label="Overtime" lazy>
                            <el-form :model="form" :rules="rules" ref="form" label-position="top" label-width="120px" class="demo-ruleForm">
                                <div class="row">
                                    <div class="col-md-12">
                                        <el-form-item label="Overtime Rate per Hour" prop="overtime_rate_hour">
                                            <el-input v-model="form.overtime_rate_hour" type="number" placeholder="Overtime Rate per Hour"></el-input>
                                        </el-form-item>
                                    </div>
                                    <div class="col-md-12">
                                        <el-form-item style="float:right">
                                            <el-button type="primary" @click="submitForm('form')">Submit</el-button>
                                        </el-form-item>
                                    </div>
                                </div>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane label="Position" lazy>
                            <position-list></position-list>
                        </el-tab-pane>
                        <el-tab-pane label="SSS" lazy>
                            <sss-list></sss-list>
                        </el-tab-pane>
                        <el-tab-pane label="Philhealth" lazy>
                            <philhealth-list></philhealth-list>
                        </el-tab-pane>
                    </el-tabs>



                </div>
            </section>
        </div>
    </div>
</template>
<script>
export default {
    name: 'FinanceSetting',
    props: {
        setting: {}
    },
    data() {
        return {
            form: {
                sss: '',
                philhealth: '',
                overtime_rate_hour: ''
            },
            rules : {
                sss: [
                    { required: true, message: 'Please input SSS rate', trigger: 'blur' }
                ],
                philhealth: [
                    { required: true, message: 'Please input Philhealth rate', trigger: 'blur' }
                ],
                overtime_rate_hour: [
                    { required: true, message: 'Please input Overtime rate per hour', trigger: 'blur' }
                ],
            },
        }
    },
    created() {
        this.form = this.setting
    },
    methods: {
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
            if (valid) {
                this.storeSetting();
            } else {
                console.log('error submit!!');
                return false;
            }
            });
        },
        resetForm(formName) {
            this.$refs[formName].resetFields();
        },
        async storeSetting() {
            try {
                const res = await this.$API.Finance.updateFinanceSetting(this.form.id, this.form)
                this.$notify({
                    title: 'Success',
                    message: 'Successfully updated',
                    type: 'success'
                });
            } catch (error) {
                console.log(error);
            }
        }
    }
}
</script>
