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
                <el-form-item label="Leadmanawdawd" prop="leadman_id">
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
                <!-- Display the leadman's name -->
                <el-form-item label="Leadman Name">
                    <el-input v-model="form.leadman_name" disabled></el-input>
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
                    <el-button type="primary" @click="submitForm('form')">Request</el-button>
                    <el-button @click="resetForm('form')">Reset</el-button>
                </el-form-item>
            </div>
        </div>
    </el-form>
</template>

<script>
import moment from "moment";

export default {
    name: 'RequestOrderForm',
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
                if (quantity > parseFloat(this.model.quantity)) {
                    callback(new Error('Out of stock! (' + this.model.quantity + ')'));
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
                leadman_name: '', // Added leadman_name property
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
            loading: false,
            items: [],
            notifications: [],
        }
    },
    created() {
        this.getLeadmans();
        this.getCurrentUserRole(); // Ensure this method is called to set the leadman details
        this.$EventDispatcher.listen('CLOSE_MODAL', () => {
            this.resetForm('form');
        });
        this.form.date = new Date();
        if (this.model && this.model.id && this.model.from === 'stock') {
            let date = new Date();
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
                const { role, id, firstname, lastname } = response.data;

                this.form.leadman_id = id;
                this.form.leadman_name = `${firstname} ${lastname}`;
            } catch (error) {
                console.error(error);
            }
        },
        async submitForm(formName) {
            this.$refs[formName].validate(async (valid) => {
                if (valid) {
                    if (this.mode === 'update') {
                        this.updateItem();
                        return;
                    }
                    try {
                        const selectedLeadman = this.leadmans.find(leadman => leadman.id === this.form.leadman_id);
                        const leadmanName = selectedLeadman ? selectedLeadman.name : 'N/A';

                        const selectedArea = this.areas.find(area => area.id === this.form.area_id);
                        const areaName = selectedArea ? selectedArea.name : 'N/A';

                        const notificationMessage = `
                            New Request Item <br>
                            Lead Name: ${leadmanName}<br>
                            Area: ${areaName}<br>
                            Item: ${this.form.item_id}<br>
                            Quantity: ${this.form.quantity}<br>
                            Date: ${this.form.date}
                        `;
                        this.notifications.push(notificationMessage);

                        document.getElementById("notification-message").innerHTML = this.notifications.join('<br><br>');
                        document.getElementById("notification-box").style.display = "block";
                        document.getElementById("view-box").innerHTML = this.notifications.join('<br><br>');
                        document.getElementById("view-box").style.display = "block";

                        await this.storeDeploy();
                    } catch (error) {
                        console.error(error);
                    }
                } else {
                    console.log('error submit!!');
                }
            });
        },
        resetForm(formName) {
            this.$refs[formName].resetFields();
        },
        async getAreas() {
            try {
                const res = await this.$API.Area.getAllAreas();
                this.areas = res.data
            } catch (error) {
                console.log(error);
            }
        },
        async getLeadmans() {
            try {
                const res = await this.$API.Admin.getLeadmans();
                this.leadmans = res.data.filter(leadman => leadman.role_name.toLowerCase() === 'leadman').map(leadman => ({
                    id: leadman.id,
                    name: `${leadman.firstname} ${leadman.middlename} ${leadman.lastname}`
                }));
            } catch (error) {
                console.log(error);
            }
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
                this.$notify({
                    title: 'Waiting for Response',
                    message: 'Request Successfully',
                    type: 'warning'
                });
                this.resetForm('form');
            } catch (error) {
                console.log(error);
            }
        },
        changeArea(value) {
            this.form.area_id_id = null;
            this.form.area_id = value;
        },
    },
    watch: {
        model: {
            handler(newVal, oldVal) {
                if (newVal !== oldVal && newVal.from === 'stock') {
                    let date = new Date();
                    this.form = {
                        stock_id: newVal.id,
                        item_id: newVal.item_id,
                        date,
                        area_id: '',
                        quantity: '',
                    };
                    this.items = [{
                        id: newVal.item_id,
                        name: `${newVal.item.name}`
                    }];
                } else {
                    this.items = [];
                }
            },
            deep: true 
        }
    },
}
</script>
