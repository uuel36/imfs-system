<template>
    <div>
      <el-form :inline="isMobile ? false : true" :model="form" :rules="rules" ref="form" class="demo-form-inline">
        <el-row>
          <el-col :span="24" :sm="8">
            <el-form-item label="Date" prop="date">
              <el-date-picker
                v-model="form.date"
                type="date"
                format="yyyy-MM-dd"
                value-format="yyyy-MM-dd"
                placeholder="Select date"
                :disabled="false"
                class="fixed-width-input"
              ></el-date-picker>
            </el-form-item>
          </el-col>
          <el-col :span="24" :sm="8">
            <el-form-item label="Team" prop="team_id">
              <el-select
                v-model="form.team_id"
                placeholder="Select"
                @change="teamChange"
                :remote-method="remoteMethodTeam"
                :disabled="editmode"
                class="fixed-width-input"
                filterable
              >
                <el-option
                  v-for="item in teams"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                ></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="24" :sm="8">
            <el-form-item label="Employees">
              <el-tags v-model="selectedEmployees" class="fixed-width-input">
                <el-tag
                  style="margin-right: 5px; margin-bottom: 5px"
                  v-for="item in employees"
                  :key="item.id"
                  :value="item.id"
                  effect="plain"
                >{{ item.label }}</el-tag>
              </el-tags>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
            <el-col :span="24" :sm="8">
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
</el-col>
          <el-col :span="24" :sm="8">
            <el-form-item label="Task Info">
              <el-input v-model="form.data" placeholder="Please input task info" class="fixed-width-input"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="24" :sm="8">
          <!-- <el-form-item label="Leadman" prop="leadman_id">
              <el-input v-model="form.leadman_id" placeholder="Leadman's Name" class="fixed-width-input" disabled></el-input>
          </el-form-item> -->
      </el-col>
        </el-row>
        <el-row>
          <el-col :span="24" class="text-center">
            <el-form-item>
              <el-button type="primary" @click="submitForm('form')">Submit</el-button>
              <el-button @click="resetForm('form')">Reset</el-button>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
    </div>
  </template>
  
  <style scoped>
    .fixed-width-input {
      width: 100%; /* Adjust the width as needed */
    }
  
    .tag-container {
      max-height: 100px; /* Set the maximum height you prefer */
      overflow-y: auto;
    }
  
    @media (max-width: 768px) {
      /* Apply responsive styles for screens with a max-width of 768px (adjust as needed) */
      .el-form-item {
        margin-bottom: 10px; /* Add spacing between form items for better readability on mobile */
      }
  
      .el-button {
        margin-right: 10px; /* Add spacing between buttons for better spacing on mobile */
      }
    }
  </style>
  
  <script>
  export default {
    name: 'DailyReportForm',
    props: {
        mode: null,
        model: {}
    },
    data() {
        return {
            isMobile: false, 
            form : {
                date: new Date(),
                team_id: '',
                task_id: '',
                area_id: '',
                employee_id: '',
                data: '',
                leadman_id: '',
            },
            rules : {
                area_id: [
                    { required: true, message: 'Please select area', trigger: 'blur' }
                ],
                date: [
                    { required: true, message: 'Please input date', trigger: 'blur' }
                ],
                team_id: [
                    { required: true, message: 'Please select team', trigger: 'blur' }
                ],
                task_id: [
                    { required: true, message: 'Please select task', trigger: 'blur' }
                ],
                employee_id: [
                    { required: true, message: 'Please select Employee', trigger: 'blur' }
                ],
                data: [
                    { required: true, message: 'Please input Data', trigger: 'blur' }
                ],
                description: [
                    { required: true, message: 'Please select Data', trigger: 'blur' }
                ],
                leadman_id: [
                    { required: true, message: 'Please select Leadman', trigger: 'blur' }
                ],
            },
            areas: [],
            tasks: [],
            leadmans: [],
            task_name: '',
            area_name: '',
            team_name: '',
            leadman_name: '',
            teams: [],
            loading: false,
            loadingEmployee: false,
            employees: [],
            data: "checking",
            date: null,
            editmode: false,
            selectedEmployees: [],
            isLeadman: false,
        }
    },
    created() {
        this.getAreas();
        this.getTask();
        this.getTeams();
        this.getLeadmans();
        this.isMobile = window.innerWidth <= 768; // Adjust the breakpoint as needed
        this.getCurrentUserRole(); 
  
        if(this.model && this.model.id) {
            this.form.task_id = this.model.task_id;
            this.form.area_id = this.model.area_id;
            this.form.team_id = this.model.team_id;
            this.employees = this.model.team_members;
            this.form.data = this.model.data;
            this.form.leadman_id = this.model.leadman_id;
            this.task_name = this.model.task_name;
            this.area_name = this.model.area_name;
            this.form.date = this.model.date;
            this.form.employee_id = this.model.team_members;
            this.editmode = true;
        }
    },
    methods: {
        async getCurrentUserRole() {
  try {
    const response = await this.$API.Admin.getCurrentUserRole();
    const { role, id, firstname, lastname, middlename } = response.data;
    
    // Check if the user's role ID is 2 (representing leadman role)
    this.isLeadman = role === 1;

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


        removeEmployee(employeeId) {
      const index = this.selectedEmployees.indexOf(employeeId);
      if (index !== -1) {
        this.selectedEmployees.splice(index, 1);
      }
    },
        resetForm(formName) {
            this.$refs[formName].resetFields();
            this.employees = [];
        },
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
            if (valid) {
                if(this.mode == 'update') {
                    console.log('update!!');
                    this.updateReport()
                    return
                }
                console.log('submit!!');
                this.storeReport();
                
            } else {
                console.log('error submit!!');
                return false;
                
            }
        
            
            });
         
            
        },
        async storeReport() {
            console.log(this.form);
            this.form.date = this.$df.formatDate(this.form.date, 'YYYY-MM-DD');
            await this.$API.DailyReport.storeReport(this.form)
                .then(response => {
                    if(response.data && typeof response.data != 'object' && response.data.constructor != Object) {
                        this.$notify.error({
                            title: 'Error',
                            message: 'Duplicate Data'
                        });
                        return;
                    }
                    this.$EventDispatcher.fire('NEW_DATA', response.data);
                    this.$notify({
                        title: 'Success',
                        message: 'Successfully added',
                        type: 'success'
                    }); 
  
                    this.resetForm('form');
                    this.$router.go();
  
                })
                .catch(error => {
                    console.log('error in adding report --> ', error);
                });
  
        },
        async updateReport() {
            await this.$API.DailyReport.updateReport(this.model.id, this.form)
                .then(response => {
                    this.$EventDispatcher.fire('UPDATE_DATA', response.data);
                    this.$notify({
                        title: 'Success',
                        message: 'Successfully updated',
                        type: 'success'
                        
                    });
                   
               
                })
                .catch (error => {
                    console.log('error in updating report --> ', error);
                });
      
        },
        async remoteMethodEmployee(query) {
            try {
                if(query !== '') {
  
  
                    let dt = this.$df.formatDate(new Date(), "YYYY-MM-DD");
  
  
                    this.loadingEmployee = true;
                    const res = await this.$API.Deploy.searchDeployByDate(dt);
                    const data = JSON.parse(res.data);
  
                    let temp = [];
  
                    data["employees"].forEach(employee => {
                        temp.push({
                            id: employee.id,
                            label: `${employee.lastname}, ${employee.firstname} ${employee.middlename}`
                        });
                    });
                    this.employees = temp;
                    this.loadingEmployee = false;
  
                }
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
        async getAreas() {
            try {
                // this.resetForm('form');
                const res = await this.$API.Area.getAllAreas();
                this.areas = res.data;
            } catch (error) {
                console.log(error);
            }
        },
        async getTask() {
            try {
                this.loadingTask = true;
                // this.resetForm('form');
                const res = await this.$API.Task.searchTask();
                this.tasks = res.data;
                this.loadingTask = false;
                //console.log(this.tasks);
            } catch (error) {
                console.log(error);
            }
        },
        async getTeams() {
            try {
                
                this.loading = true;
                // const res = await this.$API.Team.getAllTeams();
  
                const res = await this.$API.Operation.getOperationDeployedTeamsByCurrentDate();
                // this.resetForm('form');
                this.teams = res.data;
                this.loading = false;
                console.log(this.teams);
            } catch (error) {
                console.log(error);
            }
        },
        async remoteMethodTeam(query) {
            try {
                if(query !== '') {
                    this.loading = true;
                    const res = await this.$API.Operation.getOperationDeployedTeamsByCurrentDate();
                    this.teams = res.data;
                    this.loading = false;
  
  
                }
            } catch (error) {
                console.log(error);
            }
        },
      
        async teamChange(value) {
            this.selectedEmployees = [];
            console.log(value);
  
            let pt = {
                team_id: value,
                date: this.$df.formatDate(new Date(), "YYYY-MM-DD")
            }
  
            const res = await this.$API.Deploy.searchDeployByDate(pt);
            const data = JSON.parse(res.data);
  
            let temp = [];
            console.log(data);
  
            data["team_members_info"].forEach(employee => {
                temp.push({
                    id: employee.id,
                    label: `${employee.lastname}, ${employee.firstname} ${employee.middlename}`
                });
            });
  
            this.employees = temp;
  
            this.form.employee_id = temp;
  
            this.form.task_id = data["info"][1]["id"]
            this.task_name = data["info"][1]["name"][0];
            this.form.area_id = data["info"][0]["id"]
            this.area_name = data["info"][0]["name"][0];
  
            const harvest = await this.$API.Harvest.getHarvestByAreaNow(data["info"][0]["id"]);
  
            this.data = JSON.parse(harvest.data);
  
            this.form.data = this.data;
        
        },
    },
    watch: {
        model(newVal, oldVal) {
            if(newVal != oldVal) {
                this.form.task_id = newVal.task_id;
                this.form.area_id = newVal.area_id;
                this.form.team_id = newVal.team_id;
                this.employees = newVal.team_members;
                this.form.data = newVal.data;
                this.task_name = newVal.task_name;
                this.area_name = newVal.area_name;
                this.form.date = newVal.date;
                this.form.employee_id = newVal.team_members;
            }
        },
        mode(newVal, oldVal) {
            if(newVal != oldVal) {
                this.mode = newVal
  
                if(this.mode == 'create') {
                    this.form = {
                        date: '',
                        team_id: '',
                        task_id: '',
                        area_id: '',
                        employee_id: '',
                        data: ''
                    }
                    this.editmode = false;
                }
  
                if(this.mode == 'update') {
                    this.form.task_id = this.model.task_id;
                    this.form.area_id = this.model.area_id;
                    this.form.team_id = this.model.team_id;
                    this.employees = this.model.team_members;
                    this.form.data = this.model.data;
                    this.task_name = this.model.task_name;
                    this.area_name = this.model.area_name;
                    this.form.date = this.model.date;
                    this.form.employee_id = this.model.team_members;
                    this.editmode = true;
                }
            }
        }
    }
  }
  </script>
  