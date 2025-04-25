<template>

    <el-form :model="form" :rules="rules" ref="form" label-position="top" label-width="120px" class="demo-ruleForm">
        <div class="row">
            <div class="col-md-12" v-if="mode != 'update'">
                <el-button style="float:right" @click="addQRCode" :loading="loadingVerifed" :disabled="verify">Generate Qrcode</el-button>
                <el-form-item label="QRCode" prop="qrcode">
                    <el-input v-model="form.qrcode" :disabled="true" placeholder="QRCode"></el-input>
                </el-form-item>
            </div>
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
            <div class="col-md-2">
                <el-form-item label="Suffix" prop="suffix">
                    <el-input v-model="form.suffix" placeholder="Suffix"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-4">
                <el-form-item label="Birthday" prop="birthday">
                    <el-date-picker type="date" placeholder="Pick a date" v-model="form.birthday" style="width: 100%;"></el-date-picker>
                </el-form-item>
            </div>
            <div class="col-md-6">
                <el-form-item label="Gender" prop="gender">
                    <el-select v-model="form.gender" placeholder="please select gender" style="width:100%">
                        <el-option label="Female" value="Female"></el-option>
                        <el-option label="Male" value="Male"></el-option>
                    </el-select>
                </el-form-item>
            </div>
            <div class="col-md-6">
                <el-form-item label="Contact No." prop="contact">
                    <el-input @keypress.native="isNumber($event)" v-model="form.contact" placeholder="Contact No."></el-input>
                </el-form-item>
            </div>
            <div class="col-md-12">
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
            <div class="col-md-6">
                <el-checkbox v-model="form.is_contribution">The company will pay the contribution?</el-checkbox>
            </div>
            <div class="col-md-6">
                <el-form-item label="Daily Salary" prop="salary">
                    <el-input type="number" v-model="form.salary" placeholder="Monthly Salary." disabled></el-input>
                </el-form-item>
            </div>
            <div class="col-md-6">
                <el-form-item label="SSS ID" prop="sss">
                    <el-input @keypress.native="isNumber($event)" v-model="form.sss" placeholder="SSS"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-6">
                <el-form-item label="Philhealth ID" prop="philhealth">
                    <el-input @keypress.native="isNumber($event)" v-model="form.philhealth" placeholder="Philhealth"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-12">
                <el-form-item label="Address" prop="address">
                    <el-input v-model="form.address" type="textarea" placeholder="Address"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-12">
                <el-form-item label="Profile" prop="file">
                    <el-upload
                        class="upload-demo"
                        action=""
                        ref="uploadDocument"
                        name="projectDocument"
                        :on-preview="handlePreview"
                        :on-remove="handleRemove"
                        :on-change="fileChange"
                        :file-list="fileList"
                        :auto-upload="false"
                        accept="image/png, image/gif, image/jpeg, image/jpg"
                        list-type="picture"
                        :limit="1">
                        <el-button size="small" type="primary">Click to upload Picture</el-button>
                    </el-upload>
                </el-form-item>
            </div>
            <div class="col-md-12">
                <el-form-item style="float:right">
                    <el-button type="primary" @click="submitForm('form')">Submit</el-button>
                    <el-button @click="resetForm('form')">Reset</el-button>
                </el-form-item>
            </div>
        </div>
        
        <div v-if="mode == 'leadman' ">
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
    name: 'EmployeeForm',
    props: {
        mode: null,
        model: {}
    },
    data() {
        var checkQrCode = (rule, value, callback) => {
            if (!value) {
                return callback(new Error('Please input the qrcode'));
            }
            else {
                if(this.form.qrcode) {
                    if(this.verify) {
                        return callback();
                    }
                    if(!this.verify) {
                        return callback(new Error('Please the qrcode is not registed'));
                    }
                    else {
                        return callback();
                    }
                }
                if(this.verify) {
                    return callback();
                }
                if(!this.verify) {
                    return callback(new Error('Please the qrcode is not registed'));
                }
                else {
                    callback();
                }
            }
        };
        return {
            form: {
                firstname: '',
                middlename: '',
                lastname: '',
                suffix: '',
                gender: '',
                birthday: '',
                address: '',
                contact: '',
                position_id: '',
                sss: '',
                philhealth: '',
                qrcode: '',
                salary: '',
                is_contribution: true,
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
                qrcode: [
                    { validator: checkQrCode, trigger: 'blur' }
                ],
                gender: [
                    { required: true, message: 'Please select gender', trigger: 'blur' }
                ],
                birthday: [
                    { required: true, message: 'Please input birthday', trigger: 'blur' }
                ],
                address: [
                    { required: true, message: 'Please input address', trigger: 'blur' }
                ],
                contact: [
                    { required: true, message: 'Please input contact', trigger: 'blur' },
                    {min: 11, max: 11, message: 'Length should be 11 ', trigger: 'blur' },
                ],
                position_id: [
                    { required: true, message: 'Please input position', trigger: 'blur' }
                ],
                salary: [
                    { required: true, message: 'Please input month salary', trigger: 'blur' }
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
        this.$EventDispatcher.listen('CLOSE_EMPLOYEE', data => {
            this.resetForm('form');
        })
        if(this.model && this.model.id && this.mode == 'update') {
            if(this.model.profile) {
                this.fileList = [{name: this.model.profile_name, url: `storage/${this.model.profile_path}${this.model.profile}`}]
            }
            else {
                this.fileList = []
            }
            this.form = {
                firstname: this.model.firstname,
                middlename: this.model.middlename,
                lastname: this.model.lastname,
                suffix: this.model.suffix,
                gender: this.model.gender,
                birthday: this.model.birthday,
                position_id: this.model.position.name,
                position_id_id: this.model.position_id,
                address: this.model.address,
                contact: this.model.contact,
                is_contribution: this.model.is_contribution == 1 ? true : false,
                salary: this.model.salary,
                sss: this.model.sss,
                philhealth: this.model.philhealth,
                qrcode: this.model.qrcode
            }
        }
        else {
            this.initializeForm()
        }
    },
    methods: {
        async addQRCode() {
            try {
                this.loadingGenerate = true;
                const res = await this.$API.QrCode.storeQrCode();
                console.log(res.data.name);
                this.form.qrcode = res.data.name;
                this.verify = true;
            } catch (error) {
                console.log(error);
            }
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
                suffix: '',
                gender: '',
                position: '',
                birthday: '',
                address: '',
                contact: '',
                is_contribution: true,
                salary: '',
                position_id: '',
            }
        },
        async getPositions() {
            try {
                const res = await this.$API.Position.getPositionList();
                // example data [{name: 'position', id: 1}]
                // remove all data except with the index having the name of 'field worker' lowercase
                //res.data = res.data.filter((item, index) => item.name.toLowerCase() == 'field worker')
                //console.log(res.data);
                this.positions = res.data
            } catch (error) {
                console.log(error);
            }
        },
        positionChange(value) {
            this.form.position_id_id = null
            this.form.position_id = value
            this.form.salary = value
            let position = this.positions.find(p => p.id == value);
            this.form.salary = position.rate
            // this.form.salary = position.rate * 25
        },
        async storeEmployee() {
            try {
                this.form.birthday = this.$df.formatDate(this.form.birthday, "YYYY-MM-DD")
                let formData = new FormData();
                if(this.form.firstname)formData.append('firstname', this.form.firstname);
                if(this.form.middlename)formData.append('middlename', this.form.middlename);
                if(this.form.lastname)formData.append('lastname', this.form.lastname);
                if(this.form.suffix)formData.append('suffix', this.form.suffix);
                if(this.form.gender)formData.append('gender', this.form.gender);
                if(this.form.birthday)formData.append('birthday', this.form.birthday);
                if(this.form.position_id)formData.append('position_id', this.form.position_id);
                if(this.form.address)formData.append('address', this.form.address);
                if(this.form.contact)formData.append('contact', this.form.contact);
                if(this.form.sss)formData.append('sss', this.form.sss);
                if(this.form.is_contribution)formData.append('is_contribution', this.form.is_contribution);
                if(this.form.salary)formData.append('salary', this.form.salary);
                if(this.form.philhealth)formData.append('philhealth', this.form.philhealth);
                if(this.form.qrcode)formData.append('qrcode', this.form.qrcode);
                if(this.form.file)formData.append('file', this.form.file);
                if(this.form.file_name)formData.append('file_name', this.form.file_name);

                const res = await this.$API.Employee.storeEmployee(formData);

                this.$EventDispatcher.fire('NEW_EMPLOYEE', res.data);
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
        async updateEmployee() {
            try {
                this.form.birthday = this.$df.formatDate(this.form.birthday, "YYYY-MM-DD")

                let formData = new FormData();
                if(this.form.firstname)formData.append('firstname', this.form.firstname);
                if(this.form.middlename)formData.append('middlename', this.form.middlename);
                if(this.form.lastname)formData.append('lastname', this.form.lastname);
                if(this.form.suffix)formData.append('suffix', this.form.suffix);
                if(this.form.gender)formData.append('gender', this.form.gender);
                if(this.form.birthday)formData.append('birthday', this.form.birthday);
                if(this.form.position_id)formData.append('position_id', this.form.position_id);
                if(this.form.position_id_id)formData.append('position_id_id', this.form.position_id_id);
                if(this.form.address)formData.append('address', this.form.address);
                if(this.form.contact)formData.append('contact', this.form.contact);
                if(this.form.is_contribution)formData.append('is_contribution', this.form.is_contribution);
                if(this.form.salary)formData.append('salary', this.form.salary);
                if(this.form.sss)formData.append('sss', this.form.sss);
                if(this.form.philhealth)formData.append('philhealth', this.form.philhealth);
                if(this.form.file)formData.append('file', this.form.file);
                if(this.form.file_name)formData.append('file_name', this.form.file_name);
                if(this.form.remove_file)formData.append('remove_file', this.form.remove_file);

                const res = await this.$API.Employee.updateEmployee(this.model.id, formData);
                this.$EventDispatcher.fire('UPDATE_EMPLOYEE', res.data);
                this.verify = false
                this.$notify({
                    title: 'Success',
                    message: 'Successfully updated',
                    type: 'success'
                });
            } catch (error) {
                console.log(error);
            }
        },
        async verfiedQr() {
            if(this.form.qrcode) {
                try {
                    this.loadingVerifed = true;
                    const res = await this.$API.QrCode.verifiedQrCode(this.form.qrcode);
                    if(res.data == 'ready_use') {
                        this.verify = true;
                        this.$notify({
                            title: 'Success',
                            message: 'Verified!',
                            type: 'success'
                        });
                    }
                    else if(res.data == 'already_use') {
                        this.$notify.error({
                            title: 'Error',
                            message: 'The QR Code already used!'
                        });
                        this.verify = false;
                    }
                    else {
                        this.$notify.error({
                            title: 'Error',
                            message: 'The QR Code is not yet registered!'
                        });
                        this.verify = false;
                    }

                    this.loadingVerifed = false;
                } catch (error) {
                    console.log(error);
                }
            }
        },
        isNumber(evt) {
            evt = (evt) ? evt : window.event;
            let charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode > 31 && (charCode < 48 || charCode > 57))) {
                evt.preventDefault();;
            } else {
                return true;
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
        mode(value) {
            if(value == 'create') {
                this.verify = false
                this.initializeForm()
            }
        },
        model(newVal, oldVal) {
            if(newVal != oldVal) {
                if(newVal.profile) {
                    this.fileList = [{name: newVal.profile_name, url: `storage/${newVal.profile_path}${newVal.profile}`}]
                }
                else {
                    this.fileList = []
                }
                this.form = {
                    firstname: newVal.firstname,
                    middlename: newVal.middlename,
                    lastname: newVal.lastname,
                    suffix: newVal.suffix,
                    gender: newVal.gender,
                    birthday: newVal.birthday,
                    address: newVal.address,
                    contact: newVal.contact,
                    is_contribution: newVal.is_contribution == 1 ? true : false,
                    salary: newVal.salary,
                    sss: newVal.sss,
                    philhealth: newVal.philhealth,
                    position_id: newVal.position.name,
                    position_id_id: newVal.position_id,
                    qrcode: newVal.qrcode,
                    // is_contribution: true,
                }
            }
            else {

                this.verify = true;
            }
        },
        verify(newVal, oldVal) {
            if(newVal != oldVal) {
                return newVal;
            }
        }
    }
}
</script>
