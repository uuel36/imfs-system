<template>
  <el-card class="box-card">
    <div class="text item">
      <el-button size="mini" type="primary" @click="addReport" style="float:right;">Add Report</el-button>
      <el-input
        size="mini"
        placeholder="Search here....."
        style="width:300px; float:right; margin-right: 10px"
        @keyup.enter.native="applySearch"
        v-model="search"
      ></el-input>
      <el-date-picker
        v-model="date"
        @change="changeDate"
        type="date"
        :clearable="false"
        placeholder="Pick a day"
      ></el-date-picker>
      <el-table
        id="reportTable"
        :data="reports"
        v-loading="loading"
        style="width: 100%"
        :style="{ 'font-size': '13px', 'font-weight': 'bold' }"
      >
        <el-table-column
          width="70"
          label="No."
          :sortable="true"
          :style="{ 'font-size': '20px', 'font-weight': 'bold' }"
        >
          <template slot-scope="scope">
            {{ scope.$index + 1 }}
          </template>
        </el-table-column>
        <el-table-column
          prop="team_members"
          label="EMPLOYEES"
          :sortable="true"
          :style="{ 'font-size': '20px', 'font-weight': 'bold' }"
        >
          <template slot-scope="scope">
            <el-tag
              style="margin-right: 5px; margin-bottom: 5px"
              v-for="member in scope.row.team_members"
              :key="member.id"
              effect="plain"
            >
              {{ member.label }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column
          prop="leadman_name"
          label="LEADMAN"
          show-overflow-tooltip
          :sortable="true"
          :style="{ 'font-size': '20px', 'font-weight': 'bold' }"
        ></el-table-column>
        <el-table-column
          prop="team_name"
          label="TEAM"
          show-overflow-tooltip
          :sortable="true"
          :style="{ 'font-size': '20px', 'font-weight': 'bold' }"
        ></el-table-column>
        <el-table-column
          prop="area_name"
          label="AREA"
          show-overflow-tooltip
          :sortable="true"
          :style="{ 'font-size': '20px', 'font-weight': 'bold' }"
        ></el-table-column>
        <el-table-column
          prop="task"
          label="TASK"
          show-overflow-tooltip
          :sortable="true"
          :style="{ 'font-size': '20px', 'font-weight': 'bold' }"
        >
          <template slot-scope="scope">
            <span v-if="scope.row.task.color != null" :style="{ color: scope.row.task.color }">{{ scope.row.task.name }}</span>
            <span v-if="scope.row.task.color == null">{{ scope.row.task.name }}</span>
          </template>
        </el-table-column>
        <el-table-column
          prop="data"
          label="DATA"
          show-overflow-tooltip
          :sortable="true"
          :style="{ 'font-size': '20px', 'font-weight': 'bold' }"
        >
          <template slot-scope="scope">
            {{ scope.row.data }}
          </template>
        </el-table-column>

        <el-table-column fixed="right" width="110" label="ACTION">
  <template slot-scope="scope">
    <div class="hide-on-print">
      <button @click="askToDelete(scope.$index, scope.row)" class="btn btn-danger btn-sm">
        <i class="fas fa-trash"></i>
      </button>
      <button @click="handleEdit(scope.row)" class="btn btn-primary btn-sm">
        <i class="fas fa-edit"></i>
      </button>
    </div>
  </template>
</el-table-column>

      

      </el-table>
      <global-pagination
        :current_page="current_page"
        :current_size="current_size"
        :pagination="reportsPagination"
        :total="filters.total"
        @handleSizeChange="handleSizeChange"
        @handleCurrentChange="handleCurrentChange"
      ></global-pagination>
      <div class="p-4">
        <el-button @click="printTable" style="float:right"  type="info" size="mini" class="no-print btn-info">Print</el-button>
      </div>
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

<style>
.custom-dialog {
  border-radius: 15px; /* Set the desired border radius */
}
  /* ...your existing styles... */

  /* Add the @media print rules here */
  @media print {
    /* Apply the hide-on-print class to the ACTION column when printing */
    #reportTable .hide-on-print {
      display: none !important;
    }
  }
</style>
<script>
import paginate from '../../../mixin/pagination';
import html2canvas from 'html2canvas';

export default {
  name: 'DailyReportList',
  mixins: [paginate],
  data() {
    return {
      reports: [],
      reportsPagination: [],
      loading: false,
      current_page: 1,
      current_size: 25,
      search: null,
      mode: '',
      date: new Date(),
      model: {},
      dialogTableVisible: false,
      reportTableId: 'reportTable',
      dialogVisible: false
    };
  },
  created() {
    this.getCurrentUserRole();
    this.getReports();
    this.$EventDispatcher.listen('NEW_DATA', (data) => {
      this.reports.unshift(data);
      this.dialogTableVisible = false;
      this.mode = '';
      this.getReports();
      
    });

    this.$EventDispatcher.listen('UPDATE_DATA', (data) => {
      this.reports.forEach((cat) => {
        if (cat.id == data.id) {
          for (let key in data) {
            cat[key] = data[key];
          }
        }
      });

      this.dialogTableVisible = false;
      this.mode = '';
      this.getReports();
    });
  },
  
  methods: {
    async printTable() {
  const printContents = document.getElementById(this.reportTableId);
  const clonedTable = printContents.cloneNode(true);
  html2canvas(printContents).then((canvas) => {
    printWindow.document.open();
    const currentDate = new Date().toLocaleDateString(); // Get the current date
    printWindow.document.write(`
          <html>
            <head>
              <title>Print</title>
              <style>
                @media print {
                  .header {
                    padding: 1rem;
                    text-align: right;
                    font-size: 0.8rem;
                    font-weight: bold;
                  }
                  .header1 {
                    text-align: right;
                    font-size: 0.8rem;
                    font-weight: bold;
                  }
                  .logo {
                    width: 50px; /* Adjust the width to your desired size */
                    margin-right: 10px; /* Adjust the margin as needed */
                  }
                  .title {
                    font-size: 24px; /* Adjust the font size as needed */
                    font-weight: bold; /* Adjust the font weight as needed */
                  }
                  .footer {
                    border-top: 1px solid #ccc;
                    padding: 5px;
                    text-align: center;
                    display: flex;
                    align-items: center;
                    justify-content: flex-end;
                  }
                  .footer img {
                    width: 30px;
                    margin-right: 10px;
                  }
                  .footer h1 {
                    font-size: 1rem;
                    font-weight: 10;
                    text-align: right;
                  }
                }
                .header-content {
                  display: inline-flex;
                  align-items: center;
                }
                .small-logo {
                  width: 30px; /* Adjust the width to your desired size */
                  margin-right: 10px; /* Adjust the margin as needed */
                }
              </style>
            </head>
            <body>
              <header>
                <h5 class="header1" >Date:${currentDate}</h5> <!-- Display the current date -->
                <div class="header-content">
                  <img src="/img/company_logo.png" alt="Logo" class="logo">
                  <h4>Farm Management System</h4>
                </div>
              </header>
              
              <h1 class="title">Monthly Report: Items</h1>
              <div class="print-content">
                <!-- Your report content goes here -->
                <img src="${canvas.toDataURL()}" style="width: 100%">
              </div>
            
              <div class="footer">
                <h1 style="margin-right: auto; text-align: left;">Section: Production</h1>
                <h1>
                  <img src="/img/logo.png" alt="Logo" class="small-logo">
                  Farm Management System
                </h1>
              </div>
            </body>
          </html>
        `);
        printWindow.document.close();
        printWindow.onload = function () {
      // Add an onload event handler to ensure the window is fully loaded before printing
        printWindow.print();
       printWindow.onafterprint = function () {
        // Add an onafterprint event handler to reset the loading state and flag
        // Reset the loading state after printing
        printWindow.close(); // Close the print window after printing
        this.$router.push('/daily-reports');
        this.$router.go();
      }.bind(this);
    }.bind(this);
  });
},
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
        async getReports() {
  try {
    // Get the current user role and id
    await this.getCurrentUserRole();

    this.date = this.$df.formatDate(this.date, 'YYYY-MM-DD');
    let params = {
      current_size: this.current_size,
      current_page: this.current_page,
      search: this.search,
      date: this.date,
      user_id: this.leadman_id,
    };

    this.loading = true;
    
    // Make API request to get reports
    const res = await this.$API.DailyReport.getReports(params);

    // Filter reports based on leadman_id if the user is not an administrator or warehouse staff
    if (this.leadman_id !== 1 && this.leadman_id !== 3) {
      this.reports = res.data.data.filter(report => report.leadman_id === this.leadman_id);
    } else {
      this.reports = res.data.data;
    }
    
    this.reportsPagination = res.data;

    // Reconstruct this.reports data
    this.reports.forEach(async (report) => {
      // JSON parse team_members
      report.team_members = JSON.parse(report.team_members);

      // Put area_name and area_color in a single new object called area
      report.task = {
        name: report.task_name,
        color: report.area_color,
      };

      report.leadman_name = report.leadman_lastname + ', ' + report.leadman_firstname + ' ' + report.leadman_middlename;
    });

    this.loading = false;
  } catch (error) {
    console.log(error);
  }
},

    addReport() {
      this.dialogTableVisible = true;
      this.model = {};
      this.mode = 'create';
    },
    handleEdit(data) {
      console.log(data);
      this.model = { ...data };
      this.dialogTableVisible = true;
      this.mode = 'update';
    },
    askToDelete(index, data) {
      this.$confirm('This will permanently delete the file. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          this.delete(index, data);
        })
        .catch(() => {
          this.$message({
            type: 'info',
            message: 'Delete canceled',
          });
        });
    },
    async delete(index, data) {
      try {
        await this.$API.DailyReport.deleteReport(data.id);
        this.$notify({
          title: 'Success',
          message: 'Successfully Deleted',
          type: 'success',
        });
        this.reports.splice(index, 1);
      } catch (error) {
        console.log(error);
      }
    },
    handleClose(done) {
      this.$EventDispatcher.fire('CLOSE_MODAL');
      this.dialogVisible = false;
      // Reset form
      this.model = {};
      this.mode = '';

      done();
    },
    changeDate(value) {
      this.date = value;
      this.getReports();
    },
    handleSizeChange(val) {
      this.current_size = val;
      this.getReports();
    },
    handleCurrentChange(val) {
      this.current_page = val;
      this.getReports();
    },
    applySearch() {
      this.getReports();
    },
  },
  watch: {
    search(val) {
      if (val == '') {
        this.getReports();
      }
    },
  },
};
</script>
