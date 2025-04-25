<template>
    <el-form :model="form" :rules="rules" ref="form" label-position="top" label-width="120px" class="demo-ruleForm">
        <div class="row">
            <div class="col-md-12">
                <el-form-item label="Name" prop="name">
                    <el-input v-model="form.name" placeholder="Name"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-12">
                <el-form-item label="Rate per day" prop="rate">
                    <el-input v-model="form.rate" type="number" placeholder="Rate per day"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-12">
                <el-checkbox v-model="form.is_deploy">Need to deploy every day?</el-checkbox>
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
    name: 'PositionForm',
    props: {
        model: {},
        mode: null
    },
    data() {
        return {
            form: {
                name: '',
                is_deploy: true,
                rate: '',
            },
            rules : {
                name: [
                    { required: true, message: 'Please input category name', trigger: 'blur' }
                ],
                rate: [
                    { required: true, message: 'Please input rate per day', trigger: 'blur' }
                ],
            },
        }
    },
    created() {
        this.$EventDispatcher.listen('CLOSE_MODAL', () => {
            this.resetForm('form');
        })

        if(this.model && this.model.id) {
            let deploy = this.model.is_deploy == 0 ? false : true

            this.form = {
                name: this.model.name,
                rate: this.model.rate,
                is_deploy: deploy,
            }
        }
    },
    methods: {
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
            if (valid) {
                if(this.mode == 'update') {
                    this.updatePosition();
                    return;
                }

                this.storePosition();
            } else {
                console.log('error submit!!');
                return false;
            }
            });
        },
        resetForm(formName) {
            this.$refs[formName].resetFields();
        },
        async storePosition() {
            try {
                const res = await this.$API.Position.storePosition(this.form)
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
        async updatePosition() {
            try {
                const res = await this.$API.Position.updatePosition(this.model.id, this.form)
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
                let deploy = newVal.is_deploy == 0 ? false : true
                this.form = {
                    id: newVal.id,
                    name: newVal.name,
                    rate: newVal.rate,
                    is_deploy: deploy,
                }
            }
        },
        mode(val) {
            if(val && val == 'create') {
                this.form = {
                    name: '',
                    is_deploy: '',
                    rate: ''
                }
            }
        }
    }
}
</script>
