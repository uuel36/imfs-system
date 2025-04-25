<template>
    <el-form :model="form" :rules="rules" ref="form" label-position="top" label-width="120px" class="demo-ruleForm">
        <div class="row">
            <div class="col-md-6">
                <el-form-item label="Item" prop="item_id">
                    <el-select
                        v-model="form.item_id"
                        filterable
                        style="width:100%"
                        remote
                        reserve-keyword
                        @change="changeCategory"
                        placeholder="Please enter a keyword to search item"
                        :remote-method="remoteMethodItem"
                        :loading="loading">
                            <el-option
                                v-for="item in items"
                                :key="item.id"
                                :label="`${item.name} (${item.category.name})`"
                                :value="item.id">
                            </el-option>
                    </el-select>
                </el-form-item>
            </div>
            <!-- <div class="col-md-6">
                <el-form-item label="Leadman" prop="leadman_id">
                    <el-select
                        v-model="form.leadman_id"
                        placeholder="select leadman">
                            <el-option
                                v-for="item in leadmans"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                            </el-option>
                    </el-select>
                </el-form-item>
            </div> -->
            <div class="col-md-6">
                <el-form-item label="Quantity" prop="quantity">
                    <el-input v-model="form.quantity" type="number" placeholder="Quantity"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-6">
                <el-form-item label="Unit" prop="unit">
                    <el-input v-model="form.unit" placeholder="Unit"></el-input>
                </el-form-item>
            </div>
            <div class="col-md-12">
                <el-form-item style="float:right">
                    <el-button type="primary" @click="submitForm('form')">Save</el-button>
                    <el-button @click="resetForm('form')">Reset</el-button>
                </el-form-item>
            </div>
        </div>
    </el-form>
</template>
<script>
export default {
    name: 'StockForm',
    props: {
        mode: null,
        model: {}
    },
    data() {
        return {
            form: {
                quantity: '',
                item_id: '',
                leadman_id: '',
                unit: '',
            },
            rules : {
                quantity: [
                    { required: true, message: 'Please input quantity', trigger: 'blur' }
                ],
                unit: [
                    { required: false, message: 'Please input unit', trigger: 'blur' }
                ],
                item_id: [
                    { required: true, message: 'Please select item', trigger: 'blur' }
                ],
            },
            items: [],
            leadmans: [],
            loading: false,
        }
    },
    created() {
        
        this.getLeadmans();

        this.$EventDispatcher.listen('CLOSE_MODAL', () => {
            this.resetForm('form');
        })

        if(this.model && this.model.id) {
            this.form = {
                name: this.model.name,
                item_id: `${this.model.item.name} (${this.model.item.category.name})`,
                item_id_id: this.model.item_id,
                quantity: this.model.quantity,
                unit: this.model.unit,
            }
        }
    },
    methods: {
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
            if (valid) {
                if(this.mode == 'update') {
                    this.updateStock();
                    return;
                }

                this.storeStock();
            } else {
                console.log('error submit!!');
                return false;
            }
            });
        },
        resetForm(formName) {
            this.$refs[formName].resetFields();
        },
        async getLeadmans(){
            try {
                const res = await this.$API.Admin.getLeadmans();
                this.leadmans = res.data
                console.log(this.leadmans);

                let temp = []

                this.leadmans.forEach(leadman => {
                    // get only leadman first name, surname, middle name and id
                    temp.push({
                        id: leadman.id,
                        name: leadman.firstname + ' ' + leadman.middlename + ' ' + leadman.lastname
                    })
                });

                this.leadmans = temp

            } catch (error) {
                console.log(error);
            }
        },
        async remoteMethodItem(query) {
            try {
                if(query !== '') {
                    this.loading = true;
                    const res = await this.$API.Warehouse.searchItem(query);
                    this.items = res.data;
                    this.loading = false;
                }
            } catch (error) {
                console.log(error);
            }
        },
        changeCategory(value) {
            this.form.item_id_id = null;
            this.form.item_id = value;
        },
        async storeStock() {
            try {
                const res = await this.$API.Warehouse.storeStock(this.form);
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
        async updateStock() {
            try {
                const res = await this.$API.Warehouse.updateStock(this.model.id, this.form);
                this.$EventDispatcher.fire('UPDATE_DATA', res.data);
                this.$notify({
                    title: 'Success',
                    message: 'Successfully added',
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
                this.form = {
                    name: newVal.name,
                    item_id: `${newVal.item.name} (${newVal.item.category.name})`,
                    item_id_id: newVal.item_id,
                    quantity: newVal.quantity,
                    unit: newVal.unit,
                }
            }
        },
        mode(val) {
            if(val && val == 'create') {
                this.form = {
                    quantity: '',
                    item_id: '',
                    unit: '',
                }
            }
        }
    }
}
</script>
