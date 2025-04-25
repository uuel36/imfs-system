<template>
    <el-card class="box-card">
        <div  class="text item">
            <el-input
                size="mini"
                placeholder="Search here....."
                clearable
                style="width:300px; float:right; margin-right: 10px"
                @keyup.enter.native="applySearch"
                v-model="search">
            </el-input>
            <el-date-picker
                v-model="date"
                @change="changeDate"
                type="date"
                :clearable="false"
                placeholder="Pick a day">
            </el-date-picker>
            <el-table
                :data="operations"
                v-loading="loading"
                style="width: 100%"
                :style="{ 'font-size': '13px', 'font-weight': 'bold' }">
                    <el-table-column type="expand">
                        <template slot-scope="props">
                            <el-row :gutter="10">
                                <el-col :span="14">
                                    <div class="img_profile">
                                        <h4>MEMBERS</h4>
                                        <el-table
                                            :data="props.row.daily_operation_team.daily_operation_team_member"
                                            style="width: 100%">
                                            <el-table-column
                                                prop="employee.firstname"
                                                label="FIRSTNAME">
                                            </el-table-column>
                                            <el-table-column
                                                prop="employee.lastname"
                                                label="LASTNAME">
                                            </el-table-column>
                                            <el-table-column
                                                prop="employee.middlename"
                                                label="MIDDLENAME">
                                            </el-table-column>
                                            <el-table-column
                                                prop="employee.position"
                                                label="POSITION">
                                            </el-table-column>
                                        </el-table>
                                    </div>
                                </el-col>
                                <el-col :span="10">
                                    <operation-area-map :area="props.row"></operation-area-map>
                                </el-col>
                            </el-row>
                        </template>
                    </el-table-column>
                    <el-table-column
                        width="70"
                        label="No."
                        :sortable="true">
                            <template slot-scope="scope">
                                {{scope.$index + 1}}
                            </template>
                    </el-table-column>
                    <el-table-column
                        prop="daily_operation_team.name"
                        label="TEAM"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="date"
                        label="DATE"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="task.name"
                        label="TASK"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="area.name"
                        label="AREA"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="is_deploy"
                        label="DEPLOYED"
                        :sortable="true">
                            <template slot-scope="scope">
                                {{scope.row.is_deploy == 1 ? 'Yes' : 'No'}}
                            </template>
                    </el-table-column>
                    <el-table-column
                        fixed="right"
                        width="170"
                        prop="is_deploy"
                        label="ACTION"
                        v-if="!is_deploy" >
                        <template slot-scope="scope">
                             <!-- <button @click="askToDelete(scope.$index, scope.row)" v-if="!scope.row.is_deploy" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            <button @click="handleDeploy(scope.$index, scope.row)" v-if="!scope.row.is_deploy" class="btn btn-info btn-sm"><i class="fas fa-arrow-down"></i> Deploy</button>
                            <button @click="handleAddReport(scope.$index, scope.row)" v-if="!scope.row.showAddReportButton" class="btn btn-primary btn-sm">Add report</button> -->
                           

          <button
            @click="handleDeploy(scope.$index, scope.row)"
            v-if="!scope.row.is_deploy"
            class="btn btn-info btn-sm"
          >
            <i class="fas fa-arrow-down"></i> Deploy
          </button>
<button
            @click="askToDelete(scope.$index, scope.row)"
            v-if="!scope.row.is_deploy"
            class="btn btn-danger btn-sm"
          >
            <i class="fas fa-trash"></i>
          </button>

          <button
            @click="handleAddReport(scope.$index, scope.row)"
            v-if="scope.row.is_deploy && !scope.row.showAddReportButton"
            class="btn btn-primary btn-sm"
          >
            Add report
          </button>
                        </template>
                    </el-table-column>
                    
            </el-table>
            <global-pagination
                :current_page="current_page"
                :current_size="current_size"
                :pagination="operationsPagination"
                :total="filters.total"
                @handleSizeChange="handleSizeChange"
                @handleCurrentChange="handleCurrentChange">
            </global-pagination>
        </div>
        <el-dialog
      :title="mode == 'create' ? 'ADD REPORT' : 'UPDATE REPORT'"
      width="50%"
      :before-close="handleClose"
      :visible.sync="dialogTableVisible"
      custom-class="custom-dialog" >
    <div style="max-height: 400px; overflow-y: auto;">
      <daily-report-form :mode="mode" :model="model"></daily-report-form>
    </div>
    </el-dialog>
    </el-card>
</template>
<script>
import paginate from '../../../mixin/pagination'
import moment from 'moment'
export default {
    name: 'OperationList',
    mixins: [paginate],
    data() {
        return {
            operations: [],
            operationsPagination: [],
            loading: false,
            current_page: 1,
            current_size: 25,
            search: null,
            mode: '',
            model: {},
            dialogTableVisible: false,
            date: null,
            name: '',
            leadman_id: '', 
            leadman_name: '',
            // showAddReportButton: false,
        }
    },
    created() {
        this.date = new Date();
        this.getOperations()
       

        this.$EventDispatcher.listen('NEW_DATA', data => {
            this.operations.unshift(data)
            this.dialogTableVisible = false
            this.mode = ''
            this.showAddReportButton = true;
        })

        this.$EventDispatcher.listen('UPDATE_DATA', data => {
            this.operations.forEach(cat => {
                if(cat.id == data.id) {
                    for(let key in data) {
                        cat[key] = data[key]
                    }
                }
            })

            this.dialogTableVisible = false
            this.mode = ''
        })
        this.getCurrentUserRole();


    },
    filters: {
        timeFormat(value) {
            if(value) {
                return moment(value, 'HH:mm:ss').format('h:mm a')
            }
            return '-'
        },
        isDeploy(value) {

            if(value) return 'Yes'
            return 'No'
        }
    },
    methods: {
        async getCurrentUserRole() {
            try {
                const response = await this.$API.Admin.getCurrentUserRole();
                const { role, id, firstname, lastname } = response.data;
                this.leadman_id = id;
                this.leadman_name = `${firstname} ${lastname}`;
            } catch (error) {
                console.error(error);
            }
        },
         handleAddReport(index, data) {
        this.dialogTableVisible = true;
        this.model = {};
        this.mode = 'create';
        this.disableAddReportButton = true; // Disable the button after clicking
    },

        handleDeploy(index, data) {
           
            this.$confirm('Deploy this team?', 'Warning', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning'
                }).then(() => {
                    this.deploy(index, data);
                    // const deployUrl = '/daily-report#/daily-reports/' // replace with your deploy URL
                    // window.location.href = deployUrl
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'Cancel deploy'
                    });
                }); 
        },
        async deploy(index, data) {
            try {
                data.date = this.$df.formatDate(data.date, "YYYY-MM-DD")
                const res = await this.$API.Deploy.deployTeam(data);
                this.$notify({
                    title: 'Success',
                    message: 'Successfully Deploy',
                    type: 'success'
                });
                this.operations[index].showAddReportButton = true
                this.$EventDispatcher.fire('NEW_DEPLOY_TEAM', res.data)
                this.getOperations();
                

                // this.showAddReportButton = true; // Add this line
            } catch (error) {
                console.log(error);
            }
        },
        async getOperations() {
            try {
                this.date = this.$df.formatDate(this.date, "YYYY-MM-DD")

                let params = {
                    current_size: this.current_size,
                    current_page: this.current_page,
                    search: this.search,
                    date: this.date,
                    user_id: this.leadman_id, // Include leadman_id in the request: this.leadman_id, // Include leadman_id in the request
                };

                this.loading = true;
                const res = await this.$API.Operation.getOperations(params);
                this.operations = res.data.data;
                console.log(this.operations);
                this.operationsPagination = res.data;
                this.loading = false;
            } catch (error) {
                console.log(error);
            }
        },
        addAttendance() {
            this.dialogTableVisible = true;
            this.model = {}
            this.mode = 'create';
        },
        handleOut(data) {
            this.$confirm('Are yous sure you want to time out?', 'Warning', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning'
                    }).then(() => {
                        this.timeOut(data)
                    }).catch(() => {
                        this.$message({
                            type: 'info',
                            message: 'Time out canceled'
                        });
                    });
        },
        handleEdit(data) {
            this.model = {...data};
            this.dialogTableVisible = true;
            this.mode = 'update'
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
                await this.$API.Operation.deleteOperation(data.id);
                // await this.$API.Deploy.deleteDeploy(data.id);
                this.$notify({
                    title: 'Success',
                    message: 'Successfully Deleted',
                    type: 'success'
                });
                this.operations.splice(index, 1)
            } catch (error) {
                console.log(error);
            }
        },
        changeDate(value) {
            this.date = value
            this.getOperations();
        },
        handleClose(done) {
            this.$EventDispatcher.fire('CLOSE_MODAL')
            done();
        },
        handleSizeChange(val) {
            this.current_size = val;
            this.getOperations();
        },
        handleCurrentChange(val) {
            this.current_page = val;
            this.getOperations();
        },
        applySearch() {
            this.getOperations();
        },
    },
    watch: {
        search(val) {
            if( val == '') {
                this.getOperations();
            }
        }
    }
}
</script>