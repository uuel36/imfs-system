<template>

    <el-form :model="form" :rules="rules" ref="form" label-position="top" label-width="120px" class="demo-ruleForm">
        <div class="row">
            
            <div class="col-md-6">
                <el-form-item label="First Name" prop="firstname">
                    <el-input v-model="form.firstname" placeholder="First Name"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-6">
                <el-form-item label="Middle Name" prop="middlename">
                    <el-input v-model="form.middlename" placeholder="Middle Name"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-6">
                <el-form-item label="Last Name" prop="lastname">
                    <el-input v-model="form.lastname" placeholder="Last Name"></el-input>
                </el-form-item>
            </div>

            <div class="col-md-6">
                <el-form-item label="Username" prop="lastname">
                    <el-input v-model="form.username" placeholder="Username"></el-input>
                </el-form-item>
            </div>

            <div class="col-md-6">
                <el-form-item label="Email" prop="lastname">
                    <el-input v-model="form.email" placeholder="Email"></el-input>
                </el-form-item>
            </div>

            <div class="col-md-6">
                <el-form-item label="Password" prop="lastname">
                    <el-input v-model="form.password" type="password" placeholder="Password"></el-input>
                </el-form-item>
            </div>

            <div class="col-md-6">
                <el-form-item label="Position" prop="position_id">
                    <el-select @change="positionChange" style="width:100%" v-model="form.position_id" placeholder="Select Position">
                        <el-option
                            v-for="item in positions"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id">
                        </el-option>
                    </el-select>
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
    name: 'LeadmanForm',
    props: {
        mode: null,
        model: {}
    },
    data() {
        return {
            form: {
                firstname: '',
                middlename: '',
                lastname: '',
                username: '',
                email: '',
                password: '',
                position_id: '',
            },
            rules : {
                firstname: [
                    { required: true, message: 'Please input first name', trigger: 'blur' }
                ],
                middlename: [
                    { required: true, message: 'Please input middle name', trigger: 'blur' }
                ],
                lastname: [
                    { required: true, message: 'Please input last name', trigger: 'blur' }
                ],
                username: [
                    { required: true, message: 'Please input username', trigger: 'blur' }
                ],
                email: [
                    { required: true, message: 'Please input email', trigger: 'blur' }
                ],
                position_id: [
                    { required: true, message: 'Please input position', trigger: 'blur' }
                ],
            },
            fileList: [],
            verify: false,
            loadingVerifed: false,
            positions: []

        }
    },
    created() {
        this.getPositions()
        
        this.$EventDispatcher.listen('CLOSE_LEADMAN', data => {
            this.resetForm('form');
        });

        // this.initializeForm()

    },
    methods: {
        resetForm() {
      this.form = {
        firstname: '',
        middlename: '',
        lastname: '',
        username: '',
        email: '',
        password: '',
        position_id: ''
      };
    },
        positionChange(value) {
        // Your method logic here
    },
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
            if (valid) {
                if(this.mode == 'update') {
                    this.updateEmployee();
                    return;
                }

                this.storeEmployee();
            } else {
                console.log('error submit!!');
                return false;
            }
            });
        },
        async getPositions() {
            try {
                const res = await this.$API.Position.getRoleList();
                this.positions = res.data
            } catch (error) {
                console.log(error);
            }
        },
        resetForm(formName) {
            this.fileList = []
            this.verify = false;
            this.$refs[formName].resetFields();
        },
        initializeForm() {
            this.verify = false;
            this.form = {
                firstname: '',
                middlename: '',
                lastname: '',
                username: '',
                email: '',
                password: '',
            }
        },
        async updateEmployee() {
        try {
            const response = await this.$API.Admin.updateLeadman(this.model.id, this.form);
            if (response.status === 200) {
                this.$notify({
                    title: 'Success',
                    message: 'Leadman updated successfully',
                    type: 'success'
                });
                this.$EventDispatcher.fire('UPDATE_LEADMAN', response.data); // You might want to emit an event after successful update
            } else {
                this.$notify({
                    title: 'Error',
                    message: 'Failed to update leadman',
                    type: 'error'
                });
            }
        } catch (error) {
            console.error(error);
            this.$notify({
                title: 'Error',
                message: 'Failed to update leadman',
                type: 'error'
            });
        }
    },
        async storeEmployee() {
            try {
                this.form.birthday = this.$df.formatDate(this.form.birthday, "YYYY-MM-DD")
                let formData = new FormData();
                if(this.form.firstname)formData.append('firstname', this.form.firstname);
                if(this.form.middlename)formData.append('middlename', this.form.middlename);
                if(this.form.lastname)formData.append('lastname', this.form.lastname);
                if(this.form.username)formData.append('username', this.form.username);
                if(this.form.email)formData.append('email', this.form.email);
                if(this.form.password)formData.append('password', this.form.password);
                if(this.form.position_id)formData.append('position_id', this.form.position_id);

                const res = await this.$API.Admin.createLeadman(formData);

                this.$EventDispatcher.fire('NEW_LEADMAN', res.data);
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
        
        handlePreview(file) {
            console.log(file);
        },
        handleRemove(file, fileList) {
            this.form.remove_file = true
            this.form.file = null
            this.form.file_name = null
            console.log(file, fileList);
        },
        fileChange(file, fileList) {
            this.form.file = file.raw
            this.form.file_name = file.name
        },
    },
    watch: {
  model: {
    handler(newVal) {
      if (newVal) {
        // Populate form fields when model prop changes
        this.form = {
          firstname: newVal.firstname || '',
          middlename: newVal.middlename || '',
          lastname: newVal.lastname || '',
          username: newVal.username || '',
          email: newVal.email || '',
          password: '', // Empty the password field
          position_id: newVal.position_id || ''
        };
      } else {
        // Reset the form fields if model prop is null or undefined
        this.resetForm();
      }
    },
    immediate: true // Trigger the watcher immediately after the component is created
  }
},
}
</script>
