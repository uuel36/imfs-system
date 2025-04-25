<template>
    <el-form :model="form" :rules="rules" ref="form" label-position="top" label-width="120px" class="demo-ruleForm">
        <div class="row">
            <div class="col-md-12">
                <el-form-item label="From " prop="from">
                    <el-input type="number" v-model="form.from" placeholder="Name"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-12">
                <el-form-item label="To " prop="to">
                    <el-input type="number" v-model="form.to" placeholder="Name"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-12">
                <el-form-item label="Deduction" prop="deduction">
                    <el-input v-model="form.deduction" type="number" placeholder="Deduction"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-12">
                <el-form-item style="float:right">
                    <el-button type="primary" @click="submitForm('form')">Submit</el-button>
                    <el-button @click="resetForm('form')">Reset</el-button>
                </el-form-item>
            </div>
        </div>
    </el-form>
</template>
<script>
export default {
    name: 'PhilhealthForm',
    props: {
        model: {},
        mode: null
    },
    data() {
        return {
            form: {
                from: '',
                to: '',
                deduction: '',
            },
            rules : {
                from: [
                    { required: true, message: 'Please input from', trigger: 'blur' }
                ],
                to: [
                    { required: true, message: 'Please input to', trigger: 'blur' }
                ],
                deduction: [
                    { required: true, message: 'Please input deduction', trigger: 'blur' }
                ],
            },
        }
    },
    created() {
        this.$EventDispatcher.listen('CLOSE_MODAL', () => {
            this.resetForm('form');
        })

        if(this.model && this.model.id) {
            this.form = {
                from: this.model.from,
                to: this.model.to,
                deduction: this.model.deduction,
            }
        }
    },
    methods: {
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
            if (valid) {
                if(this.mode == 'update') {
                    this.updatePhilhealth();
                    return;
                }

                this.storePhilhealth();
            } else {
                console.log('error submit!!');
                return false;
            }
            });
        },
        resetForm(formName) {
            this.$refs[formName].resetFields();
        },
        async storePhilhealth() {
            try {
                const res = await this.$API.Deduction.storePhilhealth(this.form)
                this.$EventDispatcher.fire('NEW_DATA', res.data);
                this.$notify({
                    title: 'Success',
                    message: 'Successfully added',
                    type: 'success'
                });
                this.resetForm('form');
            } catch (error) {
                console.log(error);
            }
        },
        async updatePhilhealth() {
            try {
                const res = await this.$API.Deduction.updatePhilhealth(this.model.id, this.form)
                this.$EventDispatcher.fire('UPDATE_DATA', res.data);
                this.$notify({
                    title: 'Success',
                    message: 'Successfully updated',
                    type: 'success'
                });
            } catch (error) {
                console.log(error);
            }
        }
    },
    watch: {
        model(newVal, oldVal) {
            if(newVal != oldVal) {
                this.form = {
                    id: newVal.id,
                    to: newVal.to,
                    from: newVal.from,
                    deduction: newVal.deduction,
                }
            }
        },
        mode(val) {
            if(val && val == 'create') {
                this.form = {
                    from: '',
                    to: '',
                    deduction: '',
                }
            }
        }
    }
}
</script>
