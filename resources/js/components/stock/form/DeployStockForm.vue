<template>
    <el-form :model="form" :rules="rules" ref="form" label-position="top" label-width="120px" class="demo-ruleForm">
        <div class="row">
            <div class="col-md-6">
                <el-form-item label="Area" prop="area_id">
                    <el-select
                        v-model="form.area_id"
                        placeholder="select area">
                            <el-option
                                v-for="item in areas"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                            </el-option>
                    </el-select>
                </el-form-item>
            </div>
            <!-- add input for lead man-->
            <div class="col-md-6">
                <el-form-item label="Leadman" prop="leadman_id">
                    <el-select v-model="form.leadman_id" placeholder="Select" :value="leadman_name" class="fixed-width-input" :disabled="!isLeadman">
      <el-option
        v-for="item in leadmans"
        :key="item.id"
        :label="item.name"
        :value="item.id"
      ></el-option>
    </el-select>
                </el-form-item>
            </div>


            <div class="col-md-6">
                <el-form-item label="Item" prop="name_id">
                    <el-select v-model="form.item_id" placeholder="" :value="items[0].name" disabled>
                        <el-option v-for="item in items" :key="item.id" :label="item.name" :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
            </div>

            <div class="col-md-6">
                <el-form-item label="Quantity" prop="quantity">
                    <el-input v-model="form.quantity" style="width:100%" type="number" placeholder="Quantity"></el-input>
                </el-form-item>
            </div>

            <div class="col-md-6">
                <el-form-item label="Date" prop="date">
                    <el-date-picker
                        v-model="form.date"
                        type="datetime"
                        style="width:100%"
                        :disabled="true"
                        format="yyyy-MM-dd h:mm a"
                        value-format="yyyy-MM-dd h:mm a"
                        placeholder="Select date">
                    </el-date-picker>
                </el-form-item>
            </div>

            <div class="col-md-12">
                <el-form-item style="float:right">
                    <el-button type="primary" @click="submitForm('form')">Deploy</el-button>
                    <el-button @click="resetForm('form')">Reset</el-button>
                </el-form-item>
            </div>
        </div>
    </el-form>
</template>

<script>
import moment from "moment"
export default {
    name: 'DeployStockForm',
    props: {
        mode: null,
        model: {}
    },
    data() {
        var checkQuantity = (rule, value, callback) => {
    if (!value) {
        return callback(new Error('Please input number'));
    } else {
        let quantity = parseFloat(value.replace(',', '.'));
        if (isNaN(quantity)) {
            return callback(new Error('Invalid quantity'));
        }
        quantity = parseFloat(quantity.toFixed(1));
        if (this.model.quantity <= 0) {
            return callback(new Error('Out of Stock (' + this.model.quantity + ') remaining'));
        }
        if (quantity > parseFloat(this.model.quantity)) {
            callback(new Error('Amount exceeds. (' + this.model.quantity + ') remaining'));
        } else {
            this.form.quantity = quantity.toString();
            callback();
        }
    }
};

        return {
            form: {
                stock_id: '',
                item_id: '',
                leadman_id: '',
                area_id: '',
                quantity: '',
                date: '',
            
            },
            rules: {
                area_id: [
                    { required: true, message: 'Please select area', trigger: 'blur' }
                ],
                quantity: [
                    { validator: checkQuantity, trigger: 'blur' }
                ],
            },
            areas: [],
            leadmans: [],
            leadman_name: '',
            loading: false,
            items: [],
            isLeadman: false,
        }
    },
    created() {
        this.getLeadmans();
        this.getCurrentUserRole();
        this.$EventDispatcher.listen('CLOSE_MODAL', () => {
            this.resetForm('form');
        });
        this.form.date = new Date();

        if (this.model && this.model.id && this.model.from === 'stock') {
            let date = new Date();
            console.log(this.model);
            this.form = {
                stock_id: this.model.id,
                item_id: this.model.item_id,
                date,
                area_id: '',
                quantity: '',
            };
            this.items = [{
                id: this.model.item_id,
                name: `${this.model.item.name}`
            }];
        }

        this.getAreas();
    },
    methods: {


async getCurrentUserRole() {
  try {
    const response = await this.$API.Admin.getCurrentUserRole();
    const { role, id, firstname, lastname, middlename } = response.data;
    
    
    // Check if the user's role ID is 1 or 5
    this.isLeadman = role === 1 || role === 3;


    if (this.isLeadman) {
      // If the user is a leadman, show the leadman ID
      this.form.leadman_id = 'Select';
    } else {
      // If the user is not a leadman, show "Select"
     
      this.form.leadman_id = id;
    }

    // Set the leadman name
    this.leadman_name = `${lastname}, ${firstname} ${middlename}`;
  } catch (error) {
    console.error(error);
  }
},


       

        async getAreas() {
            try {
                const res = await this.$API.Area.getAllAreas();
                this.areas = res.data;
            } catch (error) {
                console.log(error);
            }
        },
        async getLeadmans(){
            try {
                this.loadingLeadman = true;
                const res = await this.$API.Employee.getLeadmans();
                this.leadmans = res.data;
  
                // check if leadmans is empty
                if(this.leadmans.length != 0) {
                    this.leadmans.forEach(leadman => {
                        // add new property to leadman called name
                        leadman.name = `${leadman.lastname}, ${leadman.firstname} ${leadman.middlename}`;
                    });
                }
                
                console.log(this.leadmans);
            } catch (error) {
                console.log(error);
            }
        },
      
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    if (this.mode == 'update') {
                        this.updateItem();
                    } else {
                        this.storeDeploy();
                        this.$emit('form-submitted', this.form);
                    }
                } else {
                    console.log('error submit!!');
                    return false;
                }
            });
        },
        resetForm(formName) {
            this.$refs[formName].resetFields();
        },
        async storeDeploy() {
            try {
                this.form.quantity = parseFloat(this.form.quantity.replace(',', '.'));
                if (isNaN(this.form.quantity) || this.form.quantity <= 0) {
                    this.$notify({
                        title: 'Error',
                        message: 'Invalid quantity',
                        type: 'error'
                    });
                    return;
                }
                this.form.is_approved = 1;
                const usedQuantity = parseFloat(this.form.quantity.toFixed(1));
                const remainingQuantity = parseFloat((this.model.quantity - usedQuantity).toFixed(1));

                if (remainingQuantity < 0) {
                    this.$notify({
                        title: 'Error',
                        message: 'Not enough quantity in stock',
                        type: 'error'
                    });
                    return;
                }

                this.form.quantity = usedQuantity;
                this.form.date = this.$df.formatDate(this.form.date, "YYYY-MM-DD H:mm:ss ");
                const res = await this.$API.Warehouse.storeDeploy(this.form);
                this.$EventDispatcher.fire('DEPLOY_STOCK', {
                    stock_id: this.model.id,
                    quantity: usedQuantity
                });

                const response = await this.$API.Admin.getCurrentUserRole();
                const { role } = response.data;

                if (role === 2) {
                    this.$notify({
                        title: 'Waiting for Response',
                        message: 'Request Successfully',
                        type: 'warning'
                    });
                } else {
                    this.$notify({
                        title: 'Success',
                        message: 'Successfully Deploy',
                        type: 'success'
                    });
                }

                this.resetForm('form');
            } catch (error) {
                console.log(error);
            }
        }
    }
}
</script>
