<template>
    <el-card class="box-card">
        <div  class="text item">
            <el-button size="mini" type="primary" @click="addEmployee" style="float:right;">Add Employee</el-button>
            <el-input
                size="mini"
                placeholder="Search here....."
                style="width:300px; float:right; margin-right: 10px"
                @keyup.enter.native="applySearch"
                v-model="search">
            </el-input>
            <el-table
                :data="employees"
                v-loading="loading"
                style="width: 100%"
                :style="{ 'font-size': '13px', 'font-weight': 'bold' }">
                    <el-table-column type="expand">
                        <template slot-scope="props">
                            <div class="img_profile">
                                <img class="profile" v-if="props.row.profile" :src="`storage/${props.row.profile_path}/${props.row.profile}`" alt="profile">
                                <img class="profile" v-else :src="`/img/profile-default.png`" alt="profile">
                            </div>
                        </template>
                    </el-table-column>
                    <el-input
  size="mini"
  placeholder="Time In"
  style="width: 150px; margin-right: 10px"
  v-model="timeInFilter"
  @keyup.enter.native="applyFilters"
>
</el-input>

<el-input
  size="mini"
  placeholder="Time Out"
  style="width: 150px; margin-right: 10px"
  v-model="timeOutFilter"
  @keyup.enter.native="applyFilters"
>
</el-input>

<el-button
  size="mini"
  type="primary"
  @click="applyFilters"
>
  Apply Filters
</el-button>
                    <el-table-column
                        width="70"
                        label="No."
                        :sortable="true">
                            <template slot-scope="scope">
                                {{scope.$index + 1}}
                            </template>
                    </el-table-column>
                    <el-table-column
                        prop="qrcode"
                        label="QRCODE"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="firstname"
                        label="FIRST NAME"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                         width="70"
                        prop="middlename"
                        label="M.I"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="lastname"
                        label="LAST NAME"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="gender"
                        label="GENDER"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="birthday"
                        label="BIRTHDAY"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="contact"
                        label="CONTACT"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="position.name"
                        label="POSITION"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        fixed="right"
                        width="170"
                        label="ACTION"
                        prop="qrcode">
                        <template slot-scope="scope">
                            <button @click="handleEdit(scope.row)" class="btn btn-success btn-sm"><i class="far fa-edit"></i></button>
                            <!-- <button @click="handleView(scope.row)" class="btn btn-info btn-sm"><i class="far fa-eye"></i></button> -->
                            <a :href="`/generate-qr/${scope.row.qrcode}`" target="_blank" class="btn btn-info btn-sm"><i class="fas fa-qrcode"></i></a>
                            <button @click="askToDelete(scope.$index, scope.row)" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>
                        </template>
                    </el-table-column>
            </el-table>
            <global-pagination
                :current_page="current_page"
                :current_size="current_size"
                :pagination="employeesPagination"
                :total="filters.total"
                @handleSizeChange="handleSizeChange"
                @handleCurrentChange="handleCurrentChange">
            </global-pagination>
        </div>
        <el-dialog
            :title="mode == 'create' ? 'ADD EMPLOYEE' : 'UPDATE EMPLOYEE'" width="45%"
            :before-close="handleClose"
            :visible.sync="dialogTableVisible"
            custom-class="custom-dialog">
            <employee-form :mode="mode" :model="model"></employee-form>
        </el-dialog>
    </el-card>
</template>
<style>
.custom-dialog {
  border-radius: 15px; /* Set the desired border radius */
}
</style>
<script>
import paginate from '../../../mixin/pagination'
export default {
    name: 'EmployeeList',
    mixins: [paginate],
    data() {
        return {
            employees: [],
            employeesPagination: [],
            loading: false,
            current_page: 1,
            current_size: 25,
            search: null,
            mode: '',
            model: {},
            dialogTableVisible: false,
            timeInFilter: null,
    timeOutFilter: null,
        }
    },
    computed: {
    // ... (other computed properties) ...

    // Computed property to generate the profile image path for each row
    rowProfilePath() {
      return (props) => {
        if (props.row.profile && props.row.profile_path) {
          return `storage/${props.row.profile_path}/${props.row.profile}`;
        } else {
          return this.defaultProfilePath; // Default image path if no profile is available
        }
      };
    },

    // Computed property for the default profile image path
    defaultProfilePath() {
      return '/img/profile-default.png';
    },
  },
    created() {
        this.getEmployees();

        this.$EventDispatcher.listen('NEW_EMPLOYEE', data => {
            this.employees.unshift(data);
            this.dialogTableVisible = false;
        })

        this.$EventDispatcher.listen('UPDATE_EMPLOYEE', data => {
            this.employees.forEach(emp => {
                if(emp.id == data.id) {
                    for(let key in data) {
                        emp[key] = data[key];
                    }
                }
            })

            this.dialogTableVisible = false;
            this.mode = '';
            this.model = {}
        })
    },
    
    methods: {
        async getEmployees() {
            try {
                let params = {
                    current_size: this.current_size,
                    current_page: this.current_page,
                    search: this.search,
                    time_in: this.timeInFilter,
                 time_out: this.timeOutFilter,
                };

                this.loading = true;
                const res = await this.$API.Employee.getEmployees(params);
                this.employees = res.data.data;
                this.employeesPagination = res.data;
                this.loading = false;
            } catch (error) {
                console.log(error);
            }
        },
        applyFilters() {
    this.current_page = 1;
    this.getEmployees();
  },
        addEmployee() {
            this.dialogTableVisible = true;
            this.mode = 'create'
            this.model = {}
        },
        handleEdit(data) {
            this.model = {...data}
            this.mode = 'update'
            this.dialogTableVisible = true;
        },
        addLeadman(){
            this.dialogTableVisible = true;
            this.mode = 'leadman'
            this.model = {}
        }
        ,
        handleView(data) {
            this.$router.push({name: 'Employee Details', params: {id: data.id, model: data} })
        },
        askToDelete(index, data) {
            this.$confirm('This will permanently delete the file. Continue?', 'Warning', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning'
                }).then(() => {
                    this.delete(index, data);
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'Delete canceled'
                    });
                });
        },
        async delete(index, data) {
            try {
                await this.$API.Employee.deleteEmployee(data.id)
                this.$notify({
                    title: 'Success',
                    message: 'Successfully Deleted',
                    type: 'success'
                });
                this.employees.splice(index, 1)
            } catch (error) {
                console.log(error);
            }
        },
        handleClose(done) {
            this.$EventDispatcher.fire('CLOSE_EMPLOYEE')
            this.mode = ''
            this.model = {}
            done();
        },
        handleSizeChange(val) {
            this.current_size = val;
            this.getEmployees();
            this.applyFilters();
        },
        handleCurrentChange(val) {
            this.current_page = val;
            this.getEmployees();
            this.applyFilters();
        },
        applySearch() {
            this.getEmployees();
        },
    },
    watch: {
        search(val) {
            if( val == '') {
                this.getEmployees();
            }
        }
    }
}
</script>
<style lang="scss">
    .profile {
        width: 200px;
    }

    .img_profile {
        padding: 10px;
        text-align: center;
        width: 100%;
    }
</style>
