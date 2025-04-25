
<template>
    
    <!-- Small boxes (Stat box) -->
    <div class="row" >
        <div class="col-sm-3 col-4" v-if=" this.roleId == 1 || this.roleId == 5">
    <!-- small box -->
    <div class="small-box bg-success">
        <div class="icon">
            <i class="ion ion-bag fas fa-users nav-icon"></i>
        </div>
        <div class="inner" @click="push('/employees#/employees')">
            <p>Total Employees</p>
            <h3>{{totalEmployees}}</h3>
           
            <!-- Add a vertical line inside the small box -->
            <div class="vertical-line"></div>
           
        </div>

     

    </div>
</div>



        <!-- Add the calendar component here -->
        
        
        <div class="col-sm-3 col-4" v-if="this.roleId == 1  || this.roleId == 5">
            <!-- small box -->
            <div class="small-box bg-secondary text-white">
                <div class="inner" @click="push('/overtime#/overtime-list')">
                    <p>Total Overtime Employees</p>
                    <h3>{{totalOvertime}}</h3>

                    
                </div>
                <div class="icon">   
            
                    <i class="ion ion-bag fas fa-user-clock"></i>
                </div>
            </div>
        </div>

        <!----------------------------------------------------------------->
        <!-- ./col -->
        <div class="col-sm-3 col-4"  v-if="this.roleId == 2 || this.roleId == 1">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner"  @click="push('/areas#/areas')">
                    <p>Total Areas</p>
                    <h3>{{totalAreas}}</h3>

                    
                </div>
                <div class="icon">
            
                    <i class="ion ion-stats-bars fas fa-map-marker-alt"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-sm-3 col-4" v-if="this.roleId == 2 || this.roleId == 1 ">
            <!-- small box -->
            <div class="small-box bg-secondary text-white">
                <div class="inner" @click="push('/daily-operation#/operations')">
                    <p>Today Operations</p>
                    <h3>{{totalOperations}}</h3>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add fas fa-cogs"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-sm-3 col-4" v-if="this.roleId == 5 || this.roleId == 1 ">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner" @click="push('/attendance#/attendance')">
                    <p>Today Present</p>
                    <h3>{{totalPresent}}</h3>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add fas fa-user-check"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-3 col-4" v-if="this.roleId == 5 || this.roleId == 1 ">
            <!-- small box -->
            <div class="small-box bg-secondary text-white">
                <div class="inner" @click="push('/attendance#/attendance')">
                    <p>Today Absent</p>
                    <h3>{{totalAbsent}}</h3>

                    
                </div>
                <div class="icon">
                    
                    <i class="ion ion-person-add fas fa-user-times"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-3 col-4" v-if="this.roleId == 2 || this.roleId == 1 ">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner" @click="push('/team#/teams')">
                    <p>Total Teams</p>
                    <h3>{{totalTeams}}</h3>

                    
                </div>
                <div class="icon">
                    
                    <i class="ion ion-person-add fas fa-users"></i>
                </div>
            </div>
        </div>
        <!-- warehouse -->

        <div class="col-sm-3 col-4" v-if="this.roleId == 3 || this.roleId == 1 ">
            <!-- small box -->
            <div class="small-box bg-secondary text-white">
                <div class="inner" @click="push('/return-tool#/return-tool')">
                    <p>Total Returned</p>
                    <h3>{{totalReturned}}</h3>

                    
                </div>
                <div class="icon">
                    
                    <i class="ion ion-person-add fas fa-reply"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->
        <!-- <div class="col-sm-3 col-4" v-if="this.roleId == 3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{totalPresent}}</h3>

                    <p>Today Present</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
            </div>
        </div> -->

        <div class="col-sm-3 col-4" v-if="this.roleId == 3 || this.roleId == 1 ">
            <!-- small box -->
            <div class="small-box bg-success">

                <div class="inner" @click="push('/items#/items')">
                    <p>Total Tools</p>
                    <h3>{{totalTools}}</h3>

                    
                </div>
                <div class="icon">

                    

                    <i class="ion ion-person-add fas fa-toolbox"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-3 col-4" v-if="this.roleId == 3 || this.roleId == 1 ">
            <!-- small box -->
            <div class="small-box bg-secondary text-white">
                <div class="inner"  @click="push('/items#/items')">
                    <p>Total Chemicals</p>
                    <h3>{{totalChemicals}}</h3>

                    
                </div>
                <div class="icon">
         
                    <i class="ion ion-person-add fas fa-prescription-bottle"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-3 col-4" v-if="this.roleId == 3 || this.roleId == 1 ">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner"  @click="push('/deploy#/deploy')">
                    <p>Chemicals Deployed</p>
                    <h3>{{chemicalsDeployed}}</h3>

                    
                </div>
                <div class="icon">
                    
                    <i class="ion ion-person-add fas fa-location-arrow"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-3 col-4" v-if="this.roleId == 3 || this.roleId == 1 || this.roleId == 2 ">
            <!-- small box -->
            <div class="small-box bg-secondary text-white">
                <div class="inner" @click="push('/deploy#/deploy')">
                    <p>Tools Deployed</p>
                    <h3>{{toolsDeployed}}</h3>

                    
                </div>
                <div class="icon">
                
                    <i class="ion ion-person-add fas fa-location-arrow"></i>
                </div>
            </div>
        </div>
    <!-- ./col -->
    </div>

</template>
<style scoped>
.small-box {
    position: relative;
}

.small-box .inner {
    position: relative;
    padding-right: 13px; /* Adjust the padding to create space for the vertical line */
}

.vertical-line {
    position: absolute;
    top: 0;
    bottom: 0;
    right: 0;
    width: 23px; /* Adjust the width of the line as needed */
    background: linear-gradient(to right, transparent, gray); /* Use a transparent to gray gradient for the left border */
}



/* Scoped style to apply only to this component */
.custom-calendar .b-calendar {
  width: 100% !important; /* Adjust the width as needed */
  max-width: none !important; /* Override any max-width constraint */
}
</style>
<script>
import { BCalendar } from 'bootstrap-vue';
export default {
    components: {
    BCalendar,
  },
    name: 'RecordsView',
    data() {
        return {
            totalEmployees: 0,
            totalAreas: 0,
            totalOperations: 0,
            totalPresent: 0,
            roleId: null,
            taskName: null,
            leadmanId: null,
            taskColor: null,
            teamName: null,
            totalItems: 0,
            totalReturned: 0,
            totalOvertime: 0,
            totalTeams: 0,
            totalTools: 0,
            leadmanReports: [],
            totalChemicals: 0,
            toolsDeployed: 0,
            chemicalsDeployed: 0,
            reports: [],
            reportsPagination: [],
            current_page: 1,
            current_size: 25,
            totalAbsent: 0,
            calendarValue: '', // Add this line for calendar value
        }
    },
    mounted() {
        this.getTotalEmployees()
        this.getTotalAreas()
        this.getTotalOperations()
        this.getTotalPresent()
        this.getOvertimeEmployees()
        this.getWarehouseData()
        this.getTotalTeams()
        this.getTotalAbsent()
        this.getReports()
    },
    created() {
        this.getCurrentRole()
        this.getTotalEmployees()
        this.getTotalAreas()
        this.getReports()
        this.getTotalOperations()
        this.getTotalPresent()
        this.getOvertimeEmployees()
        this.getWarehouseData()
        this.getTotalTeams()
        this.getTotalAbsent()

        console.log(this.roleId);
    },
    methods: {
        onCalendarContext(ctx) {
      // Handle calendar context if needed
      console.log(ctx);
    },
        push(url){
            // redirect to url
            window.location.href = url;
        },
        async getTotalAbsent(){
            try {
                const employees = await this.$API.Dashboard.getTotalEmployees()
                const present = await this.$API.Dashboard.getTotalPresent()
                this.totalAbsent = employees.data - present.data;
                console.log(this.totalAbsent);
            } catch (error) {
                console.log(error);
            }
        },

        async getReports() {
            try {
                this.date = this.$df.formatDate(this.date, "YYYY-MM-DD")
                let params = {
                    current_size: this.current_size,
                    current_page: this.current_page,
                    search: this.search,
                    date: this.$df.formatDate(new Date(), "YYYY-MM-DD"),
                };

                this.loading = true;
                const res = await this.$API.DailyReport.getReports(params);


                this.reports = res.data.data;
                this.reportsPagination = res.data;
                //console.log(this.reports);

                // reconstruct this.reports data
                this.reports.forEach(async report => {

                    // json parse team_members
                    report.team_members = JSON.parse(report.team_members)

                    // put area_name and area_color in a single new object called area
                    report.task = {
                        name: report.task_name,
                        color: report.area_color
                    }

                    report.leadman_name = report.leadman_lastname + ',' + report.leadman_firstname + ' ' + report.leadman_middlename

                });

                // loop for reports and check if report.leadman_id is equal to this.leadmanId
                // if true, push to this.leadmanReports

                this.leadmanReports = [];

                this.reports.forEach(report => {
                    if (report.leadman_id == this.leadmanId) {
                        this.leadmanReports.push(report)
                    }
                });

                console.log(this.reports);
                console.log(this.leadmanReports);

                this.loading = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getOvertimeEmployees(){
            try {
                const res = await this.$API.Dashboard.getOvertimeEmployees();
                this.totalOvertime = res.data;
            } catch (error) {
                console.log(error);
            }
        },
        async getWarehouseData(){
            try {
                const res = await this.$API.Dashboard.getWarehouseData();
                // this.warehouseData = res.data;
                this.totalItems = res.data.warehouse.length;
                this.totalReturned = res.data.returned.length;
                this.totalTools = res.data.total_tools;
                this.totalChemicals = res.data.total_chemicals;
                this.chemicalsDeployed = res.data.chemicals_deployed;
                this.toolsDeployed = res.data.tools_deployed;

            } catch (error) {
                console.log(error);
            }
        },
        async getFinanceData(){
            try {
                const res = await this.$API.Finance.getFinanceData();
                this.financeData = res.data;
            } catch (error) {
                console.log(error);
            }
        },
        async getCurrentRole(){
            try {
                const res = await this.$API.Admin.getCurrentUserRole();
                this.roleId = res.data.role;
                this.leadmanId = res.data.id;
                console.log(this.roleId);
            } catch (error) {
                console.log(error);
            }
        },
        async getTotalEmployees() {
            try {
                const res = await this.$API.Dashboard.getTotalEmployees()
                this.totalEmployees = res.data
            } catch (error) {
                console.log(error);
            }
        },
        async getTotalAreas() {
            try {
                const res = await this.$API.Dashboard.getTotalAreas()
                this.totalAreas = res.data
            } catch (error) {
                console.log(error);
            }
        },
        async getTotalOperations() {
            try {
                const res = await this.$API.Dashboard.getTotalOperations()
                this.totalOperations = res.data
            } catch (error) {
                console.log(error);
            }
        },
        async getTotalPresent() {
            try {
                const res = await this.$API.Dashboard.getTotalPresent()
                this.totalPresent = res.data
            } catch (error) {
                console.log(error);
            }
        },
        async getTotalTeams() {
            try {
                const res = await this.$API.Dashboard.getTotalTeams()
                this.totalTeams = res.data
            } catch (error) {
                console.log(error);
            }
        }
    },
}
</script>
