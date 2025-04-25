<template>
    <el-card class="box-card">
        <div  class="text item">
            <el-button size="mini" type="primary" @click="addLeadman" style="float:right;">Add Account</el-button>
            <el-input
                size="mini"
                placeholder="Search here....."
                style="width:300px; float:right; margin-right: 10px"
                @keyup.enter.native="applySearch"
                v-model="search">
            </el-input>
            <el-table
                :data="leadmans"
                v-loading="loading"
                style="width: 100%">
                    <el-table-column
                        width="70"
                        label="No."
                        :sortable="true">
                            <template slot-scope="scope">
                                {{scope.$index + 1}}
                            </template>
                    </el-table-column>
                    <el-table-column
                        prop="firstname"
                        label="FIRST NAME"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="middlename"
                        label="MIDDLE NAME"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="lastname"
                        label="LAST NAME"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="role_name"
                        label="POSITION"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
        prop="username"
        label="Username"
        :sortable="true">
    </el-table-column>
                    <el-table-column
                        fixed="right"
                        width="110"
                        label="ACTION">
                        <template slot-scope="scope">
                            <button @click="handleEdit(scope.$index, scope.row)" class="btn btn-primary btn-sm">
            <i class="far fa-edit"></i>
        </button>
             
                            <button @click="askToDelete(scope.$index, scope.row)" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>
                        </template>
                    </el-table-column>
            </el-table>
            <global-pagination
                :current_page="current_page"
                :current_size="current_size"
                :pagination="leadmansPagination"
                :total="filters.total"
                @handleSizeChange="handleSizeChange"
                @handleCurrentChange="handleCurrentChange">
            </global-pagination>
        </div>
        <el-dialog
            :title="mode == 'create' ? 'ADD EMPLOYEE' : 'UPDATE EMPLOYEE'" width="45%"
            :before-close="handleClose"
            :visible.sync="dialogTableVisible">
            <leadman-form :mode="mode" :model="model"></leadman-form>
        </el-dialog>
    </el-card>

</template>
<script>
import paginate from '../../../mixin/pagination'
export default {
    name: 'LeadmanList',
    mixins: [paginate],
    data() {
        return {
            leadmans: [],
            leadmansPagination: [],
            loading: false,
            current_page: 1,
            current_size: 25,
            search: null,
            mode: '',
            model: {},
            dialogTableVisible: false,
        }
    },
    created() {
        this.getLeadmans();

        // this.$EventDispatcher.listen('NEW_LEADMAN', data => {
        //     this.leadmans.unshift(data);
        //     this.dialogTableVisible = false;
        // })

        this.$EventDispatcher.listen('NEW_LEADMAN', data => {
            this.leadmans.forEach(emp => {
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
        this.$EventDispatcher.listen('UPDATE_LEADMAN', data => {
            this.leadmans.forEach(emp => {
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
        async getLeadmans() {
            try {
                this.loading = true;
                const res = await this.$API.Admin.getLeadmans();
                this.leadmans = res.data;
                //console.log(res.data);
                this.leadmansPagination = res.data;
                this.loading = false;
            } catch (error) {
                console.log(error);
            }
        },
        addLeadman() {
            this.dialogTableVisible = true;
            this.mode = 'create'
            this.model = {}
        },
        handleEdit(index, data) {
        console.log(data); // Add this line to check the data received
        this.model = { ...data };
        this.mode = 'update';
        this.dialogTableVisible = true;
    },
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
                console.log(data);
                await this.$API.Admin.deleteLeadman(data.id)
                this.$notify({
                    title: 'Success',
                    message: 'Successfully Deleted',
                    type: 'success'
                });
                this.leadmans.splice(index, 1)
            } catch (error) {
                console.log(error);
            }
        },
        handleClose(done) {
            this.$EventDispatcher.fire('CLOSE_LEADMAN')
            this.mode = ''
            this.model = {}
            done();
        },
        handleSizeChange(val) {
            this.current_size = val;
            this.getLeadmans();
        },
        handleCurrentChange(val) {
            this.current_page = val;
            this.getLeadmans();
        },
        applySearch() {
            this.getLeadmans();
        },
    },
    watch: {
        search(val) {
            if( val == '') {
                this.getLeadmans();
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
