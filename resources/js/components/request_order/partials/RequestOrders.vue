<template>
    <el-card class="box-card">
        <div  class="text item">

         
            <el-input
                size="mini"
                placeholder="Search here....."
                style="width:300px; float:right; margin-right: 10px"
                @keyup.enter.native="applySearch"
                v-model="search">
            </el-input>
            
            <el-table
                :data="stocks"
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

                    <!-- <el-table-column
                        prop="leadman_name"
                        label="LEADMAN">    
                    </el-table-column> -->
                    <el-table-column
                        prop="item.name"
                        label="ITEM"
                        :sortable="true">
                    </el-table-column>
                    <el-table-column
                        prop="item.category.name"
                        label="CATEGORY"
                        :sortable="true">
                    </el-table-column>
                 
                    <el-table-column
                        fixed="right"
                        width="170"
                        label="ACTION">
                        <template slot-scope="scope">
                            <button @click="handleDeploy(scope.row)" class="btn btn-primary btn-sm"><i class="fas fa-solid fa-reply "></i></button> 
                            
                        </template>
                    </el-table-column>
            </el-table>
            <global-pagination
                :current_page="current_page"
                :current_size="current_size"
                :pagination="stocksPagination"
                :total="filters.total"
                @handleSizeChange="handleSizeChange"
                @handleCurrentChange="handleCurrentChange">
            </global-pagination>
        </div>
        <el-dialog
            :title="mode == 'create' ? 'ADD STOCK' : 'UPDATE STOCK'" width="45%"
            :before-close="handleClose"
            :visible.sync="dialogTableVisible"
            custom-class="custom-dialog">
            <stock-form :mode="mode" :model="model"></stock-form>
        </el-dialog>

        <el-dialog
            title="RESTOCK" width="45%"
            :before-close="handleClose"
            :visible.sync="dialogTableVisibleRestock"
            custom-class="custom-dialog">
            <restock-form :mode="mode" :model="model"></restock-form>
        </el-dialog>
       
        <el-dialog
            title="Request Order" width="40%"
            :before-close="handleClose"
            :visible.sync="dialogTableVisibleDeploy"
            custom-class="custom-dialog">
            <deploy-stock-form :mode="mode" :model="model"></deploy-stock-form>
        </el-dialog>

        <el-dialog
            title="History" width="45%"
            :before-close="handleClose"
            :visible.sync="dialogHistory"
            custom-class="custom-dialog">
                <deploy-list></deploy-list>
        </el-dialog>
    </el-card>
</template>
<style scoped>
.notification-dropdown {
    position: absolute;
    background-color: #fff;
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}
</style>
<style>
.custom-dialog {
  border-radius: 15px; /* Set the desired border radius */
}
</style>
<script>
import paginate from '../../../mixin/pagination'
export default {
    name: 'StockList',
    mixins: [paginate],
    data() {
        return {
            stocks: [],
            stocksPagination: [],
            loading: false,
            current_page: 1,
            current_size: 25,
            search: null,
            mode: '',
            model: {},
            dialogTableVisible: false,
            dialogTableVisibleRestock: false,
            dialogTableVisibleDeploy: false,
            dialogHistory: false,
            showNotification: false,
            userRole: null
        }
    },
    created() {
        this.getStocks();
        this.getCurrentUserRole();
        this.$EventDispatcher.listen('NEW_DATA', data => {
            this.stocks.unshift(data)
            this.dialogTableVisible = false
            this.mode = ''
            this.getStocks();
        })

        this.$EventDispatcher.listen('DEPLOY_STOCK', data => {
            this.stocks.forEach(st => {
                if(st.id == data.stock_id) {
                    st.quantity -= parseFloat(data.quantity)
                    st.used += parseFloat(data.quantity)
                }
            })
            this.dialogTableVisibleDeploy = false
            this.mode = ''
            this.getStocks();
        })

        this.$EventDispatcher.listen('UPDATE_DATA', data => {
            this.stocks.forEach(cat => {
                if(cat.id == data.id) {
                    for(let key in data) {
                        cat[key] = data[key]
                    }
                }
            })

            this.dialogTableVisible = false
            this.dialogTableVisibleRestock = false
            this.mode = ''
            this.getStocks();
        })
    },
    filters: {
        addComma(value) {
            if(value) {
                return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
            else {
                return 0;
            }
        },
    },
    methods: {
        async getCurrentUserRole() {
            try {
                const response = await this.$API.Admin.getCurrentUserRole();
                this.userRole = response.data.role; // Set the user's role ID
                // Set leadman's name in your form object
                this.form.leadman_name = `${response.data.firstname} ${response.data.lastname}`;
            } catch (error) {
                console.error(error);
            }
        },
        formatNumber(value) {
        return parseFloat(Math.abs(value)).toLocaleString();
    },
        async getStocks() {
            try {
                let params = {
                    current_size: this.current_size,
                    current_page: this.current_page,
                    search: this.search,
                };

                this.loading = true;
                const res = await this.$API.Warehouse.getStocks(params);
                this.stocks = res.data.data;
                this.stocksPagination = res.data;

                // add leadman_name and leadman_object to stocks

                let temp = []

                this.stocks.forEach(async stock => {
                    const leadman = await this.$API.Admin.getLeadmanById(stock.leadman_id);
                    stock.leadman_name = leadman.data.lastname + ", " + leadman.data.firstname + " " + leadman.data.middlename;
                    stock.leadman_object = leadman.data;
                    temp.push(stock)
                });

                this.stocks = temp;

                this.loading = false;
            } catch (error) {
                console.log(error);
            }
        },
        addStock() {
            this.mode = 'create';
            this.dialogTableVisible = true;
        },
        handleEdit(data) {
            this.model = {...data};
            this.dialogTableVisible = true;
            this.mode = 'update'
        },
        async handleDeploy(data) {
        this.mode = 'create';
        data.from = 'stock';
        this.model = { ...data };

        // Open the deploy form dialog
        this.dialogTableVisibleDeploy = true;
        this.dialogVisible = true;  // Add this line to set the dialog visibility to true

        // Wait for the dialog to be fully rendered before updating the input field
        await this.$nextTick();

        // Get the input element using a query selector and set its value without rounding off
        const quantityInput = document.querySelector('.deploy-stock-form input[type="number"]');
        if (quantityInput) {
            quantityInput.value = this.model.quantity;
        }
    },
        handleRestock(data) {
            this.model = {...data};
            this.dialogTableVisibleRestock = true;
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
                await this.$API.Warehouse.deleteStock(data.id)
                this.$notify({
                    title: 'Success',
                    message: 'Successfully Deleted',
                    type: 'success'
                });
                this.stocks.splice(index, 1)
            } catch (error) {
                console.log(error);
            }
        },
        showHistory() {
            this.dialogHistory = true
        },
        handleClose(done) {
            this.$EventDispatcher.fire('CLOSE_MODAL')
            done();
        },
        handleSizeChange(val) {
            this.current_size = val;
            this.getStocks();
        },
        handleCurrentChange(val) {
            this.current_page = val;
            this.getStocks();
        },
        applySearch() {
            this.getStocks();
        },
    },
    watch: {
        search(val) {
            if( val == '') {
                this.getStocks();
            }
        }
    }
}
</script>
