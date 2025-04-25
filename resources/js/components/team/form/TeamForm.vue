<template>
    <el-form :model="form" :rules="rules" ref="form" label-position="top" label-width="120px" class="demo-ruleForm">
        <div class="row">
            <!-- <el-col :span="24" :sm="8">
    <el-form-item label="Leadman" prop="leadman_name">
        <el-input v-model="form.leadman_name" placeholder="Leadman's Name" disabled></el-input>
    </el-form-item>
</el-col> -->
            <div class="col-md-12">
                <el-form-item label="Name" prop="name">
                    <el-input v-model="form.name" placeholder="Name"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-12">
                <el-form-item label="Descritpion" prop="description">
                    <el-input v-model="form.description" type="textarea" placeholder="Description"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-12">
                <el-form-item label="Employee" prop="employee_id">
                    <el-select
                        v-model="form.employee_id"
                        filterable
                        remote
                        style="width:100%"
                        reserve-keyword
                        @change="attendanceChange"
                        placeholder="Please enter a keyword to search employee"
                        :remote-method="remoteMethodEmployee"
                        :loading="loading">
                            <el-option
                                v-for="item in employees"
                                :key="item.id"
                                :label="`${item.lastname}, ${item.firstname} ${item.middlename}`"
                                :value="item.id">
                            </el-option>
                    </el-select>
                </el-form-item>
            </div>
            <div class="col-md-12">
                <el-table
                    :data="selectedMemebers"
                    style="width: 100%; margin-bottom:10px">
                    <el-table-column
                        prop="firstname"
                        label="FIRSTNAME">
                    </el-table-column>
                    <el-table-column
                        prop="lastname"
                        label="LASTNAME">
                    </el-table-column>
                    <el-table-column
                        prop="middlename"
                        label="MIDDLENAME">
                    </el-table-column>
                    <el-table-column>
                        <template slot-scope="scope">
                            <el-button type="danger" @click="deleteMemember(scope.$index, scope.row)">Del</el-button>
                        </template>
                    </el-table-column>
                </el-table>
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
    name: 'TeamForm',
    props: {
        mode: null,
        model: {}
    },
    data() {
        return {
            form: {
                name: '',
                description: '',
                leadman_name: ''
            },
            rules : {
                name: [
                    { required: true, message: 'Please input team name', trigger: 'blur' }
                ],
                // description : [
                //     { required: true, message: 'Please input team description', trigger: 'blur' }
                // ]
            },
            loading: false,
            employees: [],
            selectedMemebers: [],
            remove_members: [],
            remove_members_info: []
        }
    },
    created() {
        this.$EventDispatcher.listen('CLOSE_MODAL', () => {
            this.resetForm('form');
        })

        if(this.model && this.model.id) {
            this.form = {
                name: this.model.name,
                description: this.model.description,
            }
            this.selectedMemebers = this.model.members.map(member => {
                member.employee.status = 'old'
                return member.employee
            })
        }
        this.getCurrentUserRole();
    },
    methods: {
        async getCurrentUserRole() {
            try {
                const response = await this.$API.Admin.getCurrentUserRole();
                const { role, id, firstname, lastname } = response.data;

                // Set leadman's name in your form object
                this.form.leadman_name = `${firstname} ${lastname}`;
            } catch (error) {
                console.error(error);
            }
        },
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
            if (valid) {
                if(this.mode == 'update') {
                    this.updateTeam();
                    return;
                }

                this.storeTeam();
            } else {
                console.log('error submit!!');
                return false;
            }
            });
        },
        resetForm(formName) {
            this.remove_members_info = []
            this.remove_members = []
            this.employees = []
            this.$refs[formName].resetFields();
        },
        async remoteMethodEmployee(query) {
            try {
                if(query !== '') {
                    this.loading = true;
                    const res = await this.$API.Employee.searchEmployeeMember(query);
                    if(this.remove_members_info.length > 0) {
                        this.remove_members_info.forEach(member => {
                            res.data.push(member)
                        })
                    }

                    console.log(res.data);
                    this.employees = res.data.filter(emp => {
                        return !this.selectedMemebers.find(element => {
                            return element.id === emp.id;
                        });
                    })

                    // example data : [{position: {name: "field_worker"}}, {position: {name: "hr"}}]
                    // only get employees with position name field worker lowercase
                    this.employees = res.data.filter(emp => {

                        return emp.position.name.toLowerCase() == 'field worker'
                    })

                    this.loading = false;
                }
            } catch (error) {
                console.log(error);
            }
        },
        attendanceChange(value) {
            let member = this.employees.find(emp => emp.id == value);
            member.status = 'new'
            console.log(member);
            this.selectedMemebers.unshift(member);
            this.employees = []
            this.form.employee_id = null
        },
        deleteMemember(index, data) {
            this.selectedMemebers.splice(index, 1)
            this.remove_members_info.push(data)
            this.remove_members.push(data.id)
        },
        async storeTeam() {
            try {
                this.form.employees = this.selectedMemebers
                if(this.selectedMemebers.length == 0) {
                    this.$notify({
                        title: 'Error',
                        message: 'Please select atleast one employee',
                        type: 'error'
                    });
                    return;
                }

                // check is description is empty
                if(this.form.description == '') {
                    this.form.description = "-";
                }

                // check if team name is already taken
                const res1 = await this.$API.Team.checkTeamName(this.form.name);

                console.log(res1.data);

                if(res1.data.length > 0) {
                    this.$notify({
                        title: 'Error',
                        message: 'Duplicate Team',
                        type: 'error'
                    });
                    return;
                }

                const res = await this.$API.Team.storeTeam(this.form);
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
        async updateTeam() {
            try {
                this.form.employees = this.selectedMemebers
                this.form.remove_members = this.remove_members

                // check if current members and updated members are the same
                // if same, do not update
                let currentMembers = this.model.members.map(member => {
                    return member.employee.id
                })

                let updatedMembers = this.selectedMemebers.map(member => {
                    return member.id
                })

                // if(currentMembers.length == updatedMembers.length && currentMembers.every((val, index) => val === updatedMembers[index])) {
                //     this.$notify({
                //         title: 'Error',
                //         message: 'No changes made',
                //         type: 'error'
                //     });
                //     return;
                // }

                if(this.selectedMemebers.length == 0) {
                    this.$notify({
                        title: 'Error',
                        message: 'Please select atleast one employee',
                        type: 'error'
                    });
                    return;
                }

                const res = await this.$API.Team.updateTeam(this.model.id, this.form)
                this.$EventDispatcher.fire('UPDATE_DATA', res.data);
                this.$notify({
                    title: 'Success',
                    message: 'Successfully updated',
                    type: 'success'
                });
                this.remove_members_info = []
                this.remove_members = []
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
                    name: newVal.name,
                    description: newVal.description,
                }
                this.selectedMemebers = newVal.members.map(member => {
                    member.employee.status = 'old'
                    return member.employee
                })
            }
        },
        mode(val) {
            if(val && val == 'create') {
                this.form = {
                    name: '',
                    description: '',
                }

                this.selectedMemebers = []
            }
        }
    }
}
</script>
