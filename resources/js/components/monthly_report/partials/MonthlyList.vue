<template>
  <el-card class="box-card">
      <div  class="text item">
          <!--<el-button size="mini" type="primary" @click="addDeploy" style="float:right;">Deploy</el-button> -->
          <el-input
              size="mini"
              placeholder="Search here....."
              clearable
              style="width:300px; float:right; margin-right: 10px"
              @keyup.enter.native="applySearch"
              v-model="search">
          </el-input>
          <el-date-picker
              v-model="date"
              @change="changeDate"
              type="month"
              :clearable="false"
              placeholder="Pick a day">
          </el-date-picker>
     


<el-tabs>
        <!-- First tab: "Returned" -->
        <el-tab-pane label="Returned">
          <el-table 
          id="report1234"
              :data="deploy"
              v-loading="loading"
              style="width: 100%; text-size-adjust: 10%;">
                  <el-table-column
                      width="70"
                      label="No."
                      :sortable="true">
                          <template slot-scope="scope">
                              {{scope.$index + 1}}
                          </template>
                  </el-table-column>
                  <el-table-column
                      prop="leadman_name"
                      label="LEADMAN">    
                  </el-table-column>

                  <el-table-column
                      prop="item.name"
                      label="ITEM"
                      :sortable="true">
                  </el-table-column>
                  <el-table-column
                      prop="date"
                      label="DATE"
                      :sortable="true">
                          <template slot-scope="scope">
                              {{scope.row.date | filterDate}}
                          </template>
                  </el-table-column>
                  <el-table-column
                      prop="quantity"
                      label="USED"
                      :sortable="true">
                          <template slot-scope="scope">
                              {{ formatQuantity(scope.row.quantity) }}
                          </template>
                  </el-table-column>

                  <el-table-column prop="is_returned" label="STATUS" :sortable="true">
  <template slot-scope="scope">
    {{ getStatusText(scope.row) }}
  </template>
</el-table-column>



                  <el-table-column
                      prop="area.name"
                      label="AREA"
                      :sortable="true">
                  </el-table-column>
          </el-table>
          <!-- <global-pagination
    :current_page="current_page"
    :current_size="current_size"
    :pagination="deployPagination"
    :total="deployPagination.total"
    @handleSizeChange="handleSizeChange"
    @handleCurrentChange="handleCurrentChange"
  >
  </global-pagination> -->

        </el-tab-pane>

        <!-- Second tab: "Not Returned" -->
        <el-tab-pane label="Not Returned">
          <el-table 
          id="report1234"
          :data="deploy1" 
              v-loading="loading"
              style="width: 100%; text-size-adjust: 10%;">
                  <el-table-column
                      width="70"
                      label="No."
                      :sortable="true">
                          <template slot-scope="scope">
                              {{scope.$index + 1}}
                          </template>
                  </el-table-column>
                  <el-table-column
                      prop="leadman_name"
                      label="LEADMAN">    
                  </el-table-column>

                  <el-table-column
                      prop="item.name"
                      label="ITEM"
                      :sortable="true">
                  </el-table-column>
                  <el-table-column
                      prop="date"
                      label="DATE"
                      :sortable="true">
                          <template slot-scope="scope">
                              {{scope.row.date | filterDate}}
                          </template>
                  </el-table-column>
                  <el-table-column
                      prop="quantity"
                      label="USED"
                      :sortable="true">
                          <template slot-scope="scope">
                              {{ formatQuantity(scope.row.quantity) }}
                          </template>
                  </el-table-column>
               
                  <el-table-column prop="is_returned" label="STATUS" :sortable="true">
  <template slot-scope="scope">
    {{ getStatusTextNotReturned(scope.row) }}
  </template>
</el-table-column>



                  <el-table-column
                      prop="area.name"
                      label="AREA"
                      :sortable="true">
                  </el-table-column>
          </el-table>

          <!-- <global-pagination
              :current_page="current_page1"
              :current_size="current_size1"
              :pagination="deployPagination1"
              :total="filters.total"
              @handleSizeChange="handleSizeChange1"
              @handleCurrentChange="handleCurrentChange1">
          </global-pagination> -->

        </el-tab-pane>
        <el-tab-pane label="Consumed">
          <el-table 
          id="report1234"
          :data="deploy2" 
              v-loading="loading"
              style="width: 100%; text-size-adjust: 10%;">
                  <el-table-column
                      width="70"
                      label="No."
                      :sortable="true">
                          <template slot-scope="scope">
                              {{scope.$index + 1}}
                          </template>
                  </el-table-column>
                  <el-table-column
                      prop="leadman_name"
                      label="LEADMAN">    
                  </el-table-column>

                  <el-table-column
                      prop="item.name"
                      label="ITEM"
                      :sortable="true">
                  </el-table-column>
                  <el-table-column
                      prop="date"
                      label="DATE"
                      :sortable="true">
                          <template slot-scope="scope">
                              {{scope.row.date | filterDate}}
                          </template>
                  </el-table-column>
                  <el-table-column
                      prop="quantity"
                      label="USED"
                      :sortable="true">
                          <template slot-scope="scope">
                              {{ formatQuantity(scope.row.quantity) }}
                          </template>
                  </el-table-column>
               
                  <el-table-column prop="is_returned" label="STATUS" :sortable="true">
  <template slot-scope="scope">
    {{ getStatusTextNotReturned(scope.row) }}
  </template>
</el-table-column>



                  <el-table-column
                      prop="area.name"
                      label="AREA"
                      :sortable="true">
                  </el-table-column>
          </el-table>

          <!-- <global-pagination
              :current_page="current_page1"
              :current_size="current_size1"
              :pagination="deployPagination1"
              :total="filters.total"
              @handleSizeChange="handleSizeChange1"
              @handleCurrentChange="handleCurrentChange1">
          </global-pagination> -->

        </el-tab-pane>
      </el-tabs>

    
          <div class="p-4">
              <el-button @click="printTable" style="float:right"  type="info" size="mini" class="no-print btn-info">Print</el-button>
          </div>
      </div>
      
  </el-card>
</template>
<style>
@media print {
.el-table .el-table__fixed-right .el-table-column--selection {
  display: none !important;
}
}
</style>
<script>
import paginate from '../../../mixin/pagination'
import moment from 'moment'
import html2canvas from 'html2canvas';
export default {
  name: 'MonthlyList',
  mixins: [paginate],
  data() {
      return {
          deploy: [],
          deploy1: [],
          deploy2: [],
          filteredDeploy:[],
          deployPagination: {}, 
          deployPagination1: [],
          deployPagination2: [],
          returnTool: [],
          returnPagination: [],
          loading: false,
          current_page: 1,
          current_size: 25,
          loading1: false,
          loading2: false,
          current_page2: 1,
          current_size2: 25,
          current_page1: 1,
          current_size1: 25,
          search: null,
          search1: null,
          search2: null,
          mode: '',
          model: {},
          selectedCategory: null,
          dialogTableVisible: false,
          date: null,
          date1: null,
          name: '',
          isPrinting: false,
          reportTableId: 'report1234',
          
      }
  },
  created() {
      this.date = new Date();
      this.getDeploy()
      this.getDeploy1()
      this.getDeploy2()
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
      filterDate(value) {
          if(value) {
              return moment(value).format('YYYY-MMM-DD h:mm a');
          }
      }
  },
  computed: {
  // Create a computed property to format the quantity value
  formatQuantity() {
    return (quantity) => {
      // Convert the quantity to a number if it's a string
      const numericQuantity = parseFloat(quantity);

      // Check if the quantity is an integer (no decimal part)
      if (Number.isInteger(numericQuantity)) {
        return numericQuantity.toString(); // Remove trailing .0
      } else {
        return numericQuantity.toFixed(2); // Keep decimal part for non-integer values
      }
    };
  },
},
  methods: {
    getStatusTextNotReturned(row) {
  // Check if the category ID is 2 or 4
  if (row.item.category_id === 2) {
      return 'Consumed'; // Leave the cell blank
  } else if (row.item.category_id === 4) {
      return 'Consumed'; // Replace 'Category 4' with the appropriate text for category_id 4
  } else {
      return row.is_returned ? '' : 'Not Returned';
  }
},

getStatusText(row) {
  // Check if the category ID is 2 or 4
  if (row.item.category_id === 2) {
      return 'Consumed'; // Leave the cell blank
  } else if (row.item.category_id === 4) {
      return 'Consumed'; // Replace 'Category 4' with the appropriate text for category_id 4
  } else {
      return row.is_returned ? 'Returned' : 'Pending';
  }
},

      async printTable() {
  this.isPrinting = true; // Set the flag
  const printContents = document.getElementById(this.reportTableId);
  const clonedTable = printContents.cloneNode(true);
  const actionColumn = clonedTable.querySelector('.el-table-column--selection');
  if (actionColumn) {
    actionColumn.parentNode.removeChild(actionColumn);
  }
  html2canvas(printContents).then((canvas) => {
    const printWindow = window.open('', '_blank');
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
 
        
<h1 class="title">Monthly Report: Item</h1>
<div class="print-content">
  <!-- Your report content goes here -->
  <img src="${canvas.toDataURL()}" style="width: 100%">
</div>

<div class="footer">
  <h1 style="margin-right: auto; text-align: left;">Section: Warehouse</h1>
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
        this.isPrinting = false; // Reset the flag
        printWindow.close(); // Close the print window after printing
      }.bind(this);
    }.bind(this);
  });
},
async getDeploy1() {
  try {
    let params = {
      current_size: this.current_size1,
      current_page: this.current_page1,
      search: this.search,
    };
    this.loading1 = true;
    const res = await this.$API.Warehouse.getDeployNotReturned(params);
    let deploy1 = res.data.data.filter(item => item.is_approved === 1); 
    // Filter items to only include those with category_id 1, is_returned is 0, and not both is_returned and is_approved are 1
    deploy1 = deploy1.filter(item => item.item.category_id === 1 && item.is_returned === 0 && !(item.is_returned === 1 && item.is_approved === 1));

    let temp = [];
    for (const depl of deploy1) {
      const leadman = await this.$API.Admin.getLeadmanById(depl.leadman_id);
      depl.leadman_name = leadman.data.lastname + ", " + leadman.data.firstname + " " + leadman.data.middlename;
      depl.leadman_object = leadman.data;
      temp.push(depl);
    }
    this.deploy1 = temp;
    this.deployPagination1 = res.data;
    this.loading1 = false;
  } catch (error) {
    console.log(error);
  }
},
async getDeploy2() {
  try {
    let params = {
      current_size: this.current_size2,
      current_page: this.current_page2,
      search: this.search,
    };
    this.loading2 = true;
    const res = await this.$API.Warehouse.getDeployNotReturnedConsumed(params);
    let deploy2 = res.data.data.filter(item => item.is_approved === 1); 
    // Correct filter logic to include category_id 2 or 4
    deploy2 = deploy2.filter(item => (item.item.category_id === 2 || item.item.category_id === 4) && item.is_returned === 0 && !(item.is_returned === 1 && item.is_approved === 1));

    let temp = [];
    for (const depl of deploy2) {
      const leadman = await this.$API.Admin.getLeadmanById(depl.leadman_id);
      depl.leadman_name = leadman.data.lastname + ", " + leadman.data.firstname + " " + leadman.data.middlename;
      depl.leadman_object = leadman.data;
      temp.push(depl);
    }
    this.deploy2 = temp;
    this.deployPagination2 = res.data;
    this.loading2 = false;
  } catch (error) {
    console.log(error);
  }
},


async getDeploy() {
  try {
    this.date = this.$df.formatDate(this.date, "YYYY-MM-DD");
    let params = {
      current_size: this.current_size,
      current_page: this.current_page,
      search: this.search,
      date: this.date,
    };
    this.loading = true;
    const res = await this.$API.Warehouse.getDeploy(params);
    let deploy = res.data.data.filter(item => item.is_approved === 1 && item.item.category_id === 1); // Filter by category_id 1
    let temp = [];
    for (const depl of deploy) {
      const leadman = await this.$API.Admin.getLeadmanById(depl.leadman_id);
      depl.leadman_name = leadman.data.lastname + ", " + leadman.data.firstname + " " + leadman.data.middlename;
      depl.leadman_object = leadman.data;
      temp.push(depl);
    }
    this.deploy = temp;
    this.deployPagination = res.data;
    this.loading = false;
  } catch (error) {
    console.log(error);
  }
},

      async getTools(){
          let params1 = {
              current_size: this.current_size1,
              current_page: this.current_page1,
              search: this.search1,
              // date: this.date1,
          };
          const res1 = await this.$API.Warehouse.getTools(params1);
          this.returnTool = res1.data.data;
          console.log(this.returnTool);
          this.returnPagination = res1.data;
      },
      addDeploy() {
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
              await this.$API.Warehouse.deleteDeploy(data.id)
              this.$notify({
                  title: 'Success',
                  message: 'Successfully Deleted',
                  type: 'success'
              });
              this.deploy.splice(index, 1)
          } catch (error) {
              console.log(error);
          }
      },
      changeDate(value) {
          this.date = value
          this.getDeploy();
      },
      changeDate1(value) {
          this.date1 = value
          this.getDeploy();
      },
      handleClose(done) {
          this.$EventDispatcher.fire('CLOSE_MODAL')
          done();
      },
      handleSizeChange(val) {
          this.current_size = val;
          this.getDeploy();
      },
      handleCurrentChange(val) {
          this.current_page = val;
          this.getDeploy();
      },
      handleSizeChange1(val) {
          this.current_size1 = val;
          this.getDeploy1();
      },
      handleCurrentChange1(val) {
          this.current_page1 = val;
          this.getDeploy1();
      },
      applySearch() {
          this.getDeploy();
      },
      applySearch1() {
          this.getTools();
      },
  },
  watch: {
      search(val) {
          if( val == '') {
              this.getDeploy();
          }
      }
  }
}
</script>
