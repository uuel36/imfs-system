<template>
    <el-card class="box-card">
        <div  class="text item">
            <el-button size="mini" type="primary" @click="addSSS" style="float:right;">Add Deduction</el-button>
            <el-input
                size="mini"
                placeholder="Search here....."
                style="width:300px; float:right; margin-right: 10px"
                @keyup.enter.native="applySearch"
                v-model="search">
            </el-input>
            <el-table
                :data="sss"
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
                        prop="from"
                        label="FROM"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="to"
                        label="TO"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="deduction"
                        label="DEDUCTION"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        fixed="right"
                        width="110"
                        label="ACTION">
                        <template slot-scope="scope">
                            <button @click="handleEdit(scope.row)" class="btn btn-success btn-sm"><i class="far fa-edit"></i></button>
                            <button @click="askToDelete(scope.$index, scope.row)" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </template>
                    </el-table-column>
            </el-table>
            <global-pagination
                :current_page="current_page"
                :current_size="current_size"
                :pagination="sssPagination"
                :total="filters.total"
                @handleSizeChange="handleSizeChange"
                @handleCurrentChange="handleCurrentChange">
            </global-pagination>
        </div>
        <el-dialog
            :title="mode == 'create' ? 'ADD SSS' : 'UPDATE SSS'" width="45%"
            :before-close="handleClose"
            :visible.sync="dialogTableVisible">
            <sss-form :mode="mode" :model="model"></sss-form>
        </el-dialog>
    </el-card>
</template>
<script>
import paginate from '../../../mixin/pagination'
export default {
    name: 'SssList',
    mixins: [paginate],
    data() {
        return {
            sss: [],
            sssPagination: [],
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
        this.getSSS()

        this.$EventDispatcher.listen('NEW_DATA', data => {
            this.sss.unshift(data)
            this.dialogTableVisible = false
            this.mode = ''
        })

        this.$EventDispatcher.listen('UPDATE_DATA', data => {
            this.sss.forEach(cat => {
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
    methods: {
        async getSSS() {
            try {
                let params = {
                    current_size: this.current_size,
                    current_page: this.current_page,
                    search: this.search,
                };

                this.loading = true;
                const res = await this.$API.Deduction.getSSS(params);
                this.sss= res.data.data;
                this.sssPagination = res.data;
                this.loading = false;
            } catch (error) {
                console.log(error);
            }
        },
        addSSS() {
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
        await this.$API.Deduction.deleteSSS(data.id);
        this.$notify({
            title: 'Success',
            message: 'Successfully Deleted',
            type: 'success'
        });
        // Assuming SSS is a data property, update it here.
        // For example, if SSS is an array, you can remove the deleted item like this:
        this.sss.splice(index, 1);
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
            this.getSSS();
        },
        handleCurrentChange(val) {
            this.current_page = val;
            this.getSSS();
        },
        applySearch() {
            this.getSSS();
        },
    },
    watch: {
        search(val) {
            if( val == '') {
                this.getSSS();
            }
        }
    }
}
</script>
