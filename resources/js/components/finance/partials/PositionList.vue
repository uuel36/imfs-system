<template>
    <el-card class="box-card">
        <div  class="text item">
            <el-button size="mini" type="primary" @click="addPosition" style="float:right;">Add Position</el-button>
            <el-input
                size="mini"
                placeholder="Search here....."
                style="width:300px; float:right; margin-right: 10px"
                @keyup.enter.native="applySearch"
                v-model="search">
            </el-input>
            <el-table
                :data="positions"
                v-loading="loading"
                style="width: 100%"
                :style="{ 'font-size': '13px', 'font-weight': 'bold' }">
                    <el-table-column
                        width="70"
                        label="No."
                        :sortable="true">
                            <template slot-scope="scope">
                                {{scope.$index + 1}}
                            </template>
                    </el-table-column>
                    <el-table-column
                        prop="name"
                        label="NAME"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="rate"
                        label="RATE PER DAY"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="is_deploy"
                        label="NEED TO DEPLOY EVERDAY?"
                        :sortable="true">
                            <template slot-scope="scope">
                                {{scope.row.is_deploy | isdeploy}}
                            </template>
                    </el-table-column>
                    <el-table-column
                        fixed="right"
                        width="110"
                        label="ACTION">
                        <template slot-scope="scope">
                            <button @click="askToDelete(scope.$index, scope.row)" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </template>
                    </el-table-column>
            </el-table>
            <global-pagination
                :current_page="current_page"
                :current_size="current_size"
                :pagination="positionsPagination"
                :total="filters.total"
                @handleSizeChange="handleSizeChange"
                @handleCurrentChange="handleCurrentChange">
            </global-pagination>
        </div>
        <el-dialog
            :title="mode == 'create' ? 'ADD POSIITON' : 'UPDATE POSITION'" width="45%"
            :before-close="handleClose"
            :visible.sync="dialogTableVisible"
            custom-class="custom-dialog">
            <position-form :mode="mode" :model="model"></position-form>
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
    name: 'PositionList',
    mixins: [paginate],
    data() {
        return {
            positions: [],
            positionsPagination: [],
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
        this.getPositions()

        this.$EventDispatcher.listen('NEW_DATA', data => {
            this.positions.unshift(data)
            this.dialogTableVisible = false
            this.mode = ''
        })

        this.$EventDispatcher.listen('UPDATE_DATA', data => {
            this.positions.forEach(cat => {
                if(cat.id == data.id) {
                    for(let key in data) {
                        cat[key] = data[key]
                    }
                }
            })

            this.dialogTableVisible = false
            this.mode = ''
        })
    },
    filters: {
        isdeploy(value) {
            if(value == 1) return 'Yes'
            return 'No'
        }
    },
    methods: {
        async getPositions() {
            try {
                let params = {
                    current_size: this.current_size,
                    current_page: this.current_page,
                    search: this.search,
                };

                this.loading = true;
                const res = await this.$API.Position.getPositions(params);
                this.positions= res.data.data;
                this.positionsPagination = res.data;
                this.loading = false;
            } catch (error) {
                console.log(error);
            }
        },
        addPosition() {
            this.dialogTableVisible = true;
            this.model = {}
            this.mode = 'create';
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
                await this.$API.Position.deletePosition(data.id)
                this.$notify({
                    title: 'Success',
                    message: 'Successfully Deleted',
                    type: 'success'
                });
                this.Positions.positionse(index, 1)
            } catch (error) {
                console.log(error);
            }
        },
        handleClose(done) {
            this.$EventDispatcher.fire('CLOSE_MODAL')
            done();
        },
        handleSizeChange(val) {
            this.current_size = val;
            this.getPositions();
        },
        handleCurrentChange(val) {
            this.current_page = val;
            this.getPositions();
        },
        applySearch() {
            this.getPositions();
        },
    },
    watch: {
        search(val) {
            if( val == '') {
                this.getPositions();
            }
        }
    }
}
</script>
