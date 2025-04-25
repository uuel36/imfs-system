<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
<template>
    <div>
      <div class="container-fluid">
        <el-card
        class="box-card"
        style="margin-bottom: 10px; margin-top: 10px; border-bottom: 10px solid #006400;">
        <div class="text item page_title">
          <img src="/img/company_logo.png" alt="Logo" style="width: 60px; margin-right: 10px;">
          <h2>ATTENDANCE SCANNER</h2>
          <div class="time-container" style="position: absolute; top: 0; right: 2;">
            <h1>{{ getFormattedTime() }}</h1>
            <h2 class="date">{{ getFormattedDate() }}</h2>
          </div>
        </div>
      </el-card>
        <el-card class="box-card" style="height: 700px; margin-bottom: 10px; margin-top: 10px;">
          <div class="text item">
            <div class="row">
              <div class="col-md-5" style="position: relative;">
                <input v-if="showSuccessInputOut" class="success-inputout" :value="successMessageOut" readonly />
                <input v-if="showSuccessInput" class="success-input" :value="successMessage" readonly />
                <input v-if="showSuccessInputOutOvertime" class="success-inputoutOT" :value="successMessageOutOvertime" readonly />
                <input v-if="showSuccessInputOvertime" class="success-inputOT" :value="successMessageOvertime" readonly />
                <div class="qrcode-container"
    :style="{
        'width': '60%',
        'height': '50%',
        'position': 'absolute',
        'top': '48%',
        'left': '40%',
        'transform': 'translate(-50%, -50%)',
        'border': '10px solid',
        'border-color':
            qrCodeScanned
                ? '#32cd32'
                : (qrCodeScannedTimeout ? '#e3ff00' : (scannedOvertimeIn ? '#00b7eb' : (scannedOvertimeOut ? '#ff4500' : '#858585'))),
        'border-radius': qrCodeScanned ? '20px' : (qrCodeScannedTimeout ? '15px' : '10px'),
        'overflow': 'hidden'
    }"
>
    <qrcode-stream @decode="onDecode" v-loading="loadingScanner" style="width: 100%; height: 100%;"></qrcode-stream>
</div>

                <div style="position: absolute; top: 77%; left: 14%;">
    <h1 v-if="(qrCodeScanned || qrCodeScannedTimeout) && (scannedTime !== null || scannedTimeOut !== null)">
       Name: {{ fullName }}
       <div></div>
        <!-- Add OverTime In section -->
        <h2 v-if="scannedOvertimeIn">
            OverTime In: {{ scannedOvertimeIn | timeFormat }}
        </h2>
        <!-- Add OverTime Out section -->
        <h2 v-if="scannedOvertimeOut">
            OverTime Out: {{ scannedOvertimeOut | timeFormat }}
        </h2>
        <!-- Add Time In section -->
        <h2 v-if="scannedTime">
            Time In: {{ scannedTime | timeFormat }}
        </h2>
        <!-- Add Time Out section -->
        <h2 v-if="scannedTimeOut">
            Time Out: {{ scannedTimeOut | timeFormat }}
        </h2>
    </h1>
</div>

</div>
              <div class="col-md-7">
                <div class="row">
                  <div class="col-md-12">
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
                      type="date"
                      :clearable="false"
                      placeholder="Pick a day"
                    ></el-date-picker>
  
                    <!-- Add the container div for el-table with a fixed height and scroll -->
                    <div class="table-container" style="height: 511px; overflow-y: auto;">
                      <el-table
                        :data="attendance"
                        v-loading="loading"
             
                        :style="{ 'font-size': '13px', 'font-weight': 'bold', width: '300%' }"
                      >
                      <el-table-column
                     width="70"
                     label="No."
                     :sortable="true"
                      >
                         <template slot-scope="scope">
                             {{scope.$index + 1}}
                         </template>
                 </el-table-column>
                 <el-table-column
                     prop="employee"
                     label="NAME"
                     :sortable="true"
                      >
                         <template slot-scope="scope">
                             {{scope.row.employee.lastname}}, {{scope.row.employee.firstname}} {{scope.row.employee.middlename}}
                         </template>
                 </el-table-column>
                 <el-table-column
                     prop="time_in"
                     label="IN"
                     :sortable="true"
                      >
                         <template slot-scope="scope">
                             {{scope.row.time_in | timeFormat}}
                         </template>
                 </el-table-column>
                 <el-table-column
                 
                     prop="time_out"
                     label="OUT"
                     :sortable="true"
                      >
                         <template slot-scope="scope">
                             {{scope.row.time_out | timeFormat}}
                         </template>
                 </el-table-column>
                      </el-table>
                    </div>
                    <global-pagination
                      :current_page="current_page"
                      :current_size="current_size"
                      :pagination="attendancePagination"
                      @handleSizeChange="handleSizeChange"
                      @handleCurrentChange="handleCurrentChange"
                    ></global-pagination>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </el-card>
      </div>
    </div>
  </template>
  

<style lang="scss">
.time-container {
    display: flex;
    flex-direction: column;
    align-items: flex-end; // Align items to the right
    height: 100%; // Make the container take the full height
  }
  .date {
    font-size: 16px;
    margin-top: 5px; // Add some space between time and date
  }
  .success-input {
        height: 11%;
        width: 60%;
        left: 40%;
    
        font-weight: bold; // Make the text bold
        padding: 10px;
        font-size: 20px;
        color: #141414; // Text color
        background-color: #32cd32; // Green background color
        border: none; // Remove border
        border-radius: 10px; // Adjust the border-radius as needed
        box-sizing: border-box;
        margin-bottom: 10px; // Adjust as needed
        text-align: center; // Center the text horizontally
        margin-left: 55; // Set the desired margin-left
    }
    .success-inputout {
        height: 11%;
        width: 60%;
        left: 40%;
       
        font-weight: bold; // Make the text bold
        padding: 10px;
        font-size: 20px;
        color: #141414; // Text color
        background-color: #e3ff00; // Green background color
        border: none; // Remove border
        border-radius: 5px; // Adjust the border-radius as needed
        box-sizing: border-box;
        margin-bottom: 5px; // Adjust as needed
        text-align: center; // Center the text horizontally
        margin-left: 55; // Set the desired margin-left
    }
    .success-inputOT {
        height: 11%;
        width: 60%;
        left: 40%;
    
        font-weight: bold; // Make the text bold
        padding: 10px;
        font-size: 20px;
        color: #141414; // Text color
        background-color: #00b7eb; // Green background color
        border: none; // Remove border
        border-radius: 10px; // Adjust the border-radius as needed
        box-sizing: border-box;
        margin-bottom: 10px; // Adjust as needed
        text-align: center; // Center the text horizontally
        margin-left: 55; // Set the desired margin-left
    }
    .success-inputoutOT {
        height: 11%;
        width: 60%;
        left: 40%;
       
        font-weight: bold; // Make the text bold
        padding: 10px;
        font-size: 20px;
        color: #141414; // Text color
        background-color: #ff4500; // Green background color
        border: none; // Remove border
        border-radius: 5px; // Adjust the border-radius as needed
        box-sizing: border-box;
        margin-bottom: 5px; // Adjust as needed
        text-align: center; // Center the text horizontally
        margin-left: 55; // Set the desired margin-left
    }
.custom-table {
  width: 200%; // Set the desired width here
}
  .page_title {
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    font-weight: 700;
    display: flex;
    align-items: center; /* Vertically center the content */
  }

  .page_title h2 {
    margin-right: 10px; /* Add some space between h2 and h1 */
  }

  .page_title h1 {
    margin-left: auto; /* Push h1 to the right */
    font-weight: bold; /* Make the text bold */
  }
  /* ... other styles ... */
</style>
<script>
 import moment from 'moment'
import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from 'vue-qrcode-reader'
import { StreamBarcodeReader } from "vue-barcode-reader";

import { ImageBarcodeReader } from "vue-barcode-reader";
import { divIcon } from 'leaflet';
export default {
    name: 'QrCodeScanner',
    components: {
    ImageBarcodeReader,
    StreamBarcodeReader,
    QrcodeStream,
    QrcodeDropZone,
    QrcodeCapture,
    divIcon
},
    data() {
        return {
            employees: [],
             attendance: [],
             attendancePagination: [],
             loading: false,
             current_page: 1,
             current_size: 100,
             search: null,
             mode: '',
             model: {},
             dialogTableVisible: false,
             date: null,
             name: '',
             loadingIndex: false,
             index: null,
             attendanceStatus: '',
              loadingByButton: false,
              qrCodeScanned: false,
              qrCodeScannedTimeout: false,
              successMessage: null,
              showSuccessInput: false,
              successMessageOut: null,
              showSuccessInputOut: false,

              successMessageOvertime: null,
              showSuccessInputOvertime: false,
              successMessageOutOvertime: null,
              showSuccessInputOutOvertime: false,
            profile: {
                    employee: {
                    firstname: '',
                    position: '',
                    middlename: '',
                    lastname: '',
                }
            },
            loadingScanner: false,
            scannedTime: null,
            scannedTimeOut: null,
            scannedOvertimeIn: null,
            scannedOvertimeOut: null,

        }
    },
    computed: {
    // Computed property to concatenate first and last names
    fullName() {
      return `${this.profile.employee.firstname} ${this.profile.employee.lastname}`;
    },
  },
    created() {
        this.hasUserMedia();
        this.date = new Date();
         this.getAttendance();
         this.getEmployees();
    },
    filters: {
         timeFormat(value) {
             if(value) {
                 return moment(value, 'HH:mm:ss').format('h:mm a')
             }
             return '-'
         }
     },
    methods: {
      getFormattedDate() {
      const now = new Date();
      const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      };

      return now.toLocaleString('en-US', options);
    },
        getEmployeeStatus(row) {
   const timeIn = moment(row.time_in, 'HH:mm:ss');
   const timeOut = moment(row.time_out, 'HH:mm:ss');
   const timeLimit = moment('07:00:00', 'HH:mm:ss');
 
   if (timeIn.isValid()) {
     if (timeIn.isBefore(timeLimit)) {
       return 'Early';
     } else if (timeIn.isAfter(timeLimit)) {
       return 'Late';
     } else {
       return 'On time';
     }
   } else {
     return '-';
   }
 },
 
        async getEmployees() {
             try {
                 let params = {
                     current_size: this.current_size,
                     current_page: this.current_page,
                     search: this.search,
                     time_in: this.timeInFilter,
                  time_out: this.timeOutFilter,
                 };
 
                 this.loading = true;
                 const res = await this.$API.Employee.getEmployees(params);
                 this.employees = res.data.data;
                 this.employeesPagination = res.data;
                 this.loading = false;
             } catch (error) {
                 console.log(error);
             }
         },
        async getAttendance() {
   try {
     this.date = this.$df.formatDate(this.date, "YYYY-MM-DD");
 
     let params = {
       current_size: this.current_size,
       current_page: this.current_page,
       search: this.search,
       date: this.date,
     };
 
     this.loading = true;
     const res = await this.$API.Attendance.getAttendance(params);
     this.attendance = res.data.data;
 
     this.attendance.forEach(row => {
       row.status = this.getEmployeeStatus(row);
     });
 
     const currentTime = moment().format('HH:mm:ss');
     this.attendance.forEach(row => {
       if (row.time_in && !row.time_out && currentTime >= row.time_out) {
         row.time_out = currentTime;
       }
     });
 
     this.attendancePagination = res.data;
     this.loading = false;
   } catch (error) {
     console.log(error);
   }
 },
        getFormattedTime() {
      const now = new Date();
      const options = {
        hour: 'numeric',
        minute: 'numeric',
        hour12: true,
        timeZone: 'Asia/Manila', // Change to the Philippine Time Zone
      };

      return now.toLocaleString('en-US', options);
    },
    changeDate(value) {
             this.date = value
             this.getAttendance();
         },
         handleClose(done) {
             this.$EventDispatcher.fire('CLOSE_MODAL')
             done();
         },
         handleSizeChange(val) {
             this.current_size = val;
             this.getAttendance();
         },
         handleCurrentChange(val) {
             this.current_page = val;
             this.getAttendance();
         },
         applySearch() {
             this.getAttendance();
         },

        hasUserMedia() {
        //check if the browser supports the WebRTC
        return !!(navigator.getUserMedia || navigator.webkitGetUserMedia ||
            navigator.mozGetUserMedia);
        } ,
        onDecode (decodedString) {
            console.log(decodedString);
            this.timeIn(decodedString)
        },
        onError() {

        },

        onLoaded() {

        },

        async timeIn(decodedString) {

            try {
                this.loadingByButton = true
                const res = await this.$API.Attendance.qrCode(decodedString);
                console.log(res);
                if(res.data.status == 'no_employee') {
                    this.$notify.error({
                    message: 'Oops. Unauthorized',
                    type: 'danger'
                });
                }
                if(res.data.status == 'success_in') {
                    this.profile.employee.firstname = res.data.profile.employee.firstname;
                    this.profile.employee.middlename = res.data.profile.employee.middlename;
                    this.profile.employee.lastname = res.data.profile.employee.lastname;
                    const position = await this.$API.QrCode.getPositionById(res.data.profile.employee.position_id);
                    this.profile.employee.position = position.data.name;
                     // Set the scanned time
                    this.scannedTime = res.data.profile.time_in;
                    this.qrCodeScanned = true;
                    //console.log(position.data.name);
                    this.successMessage = 'Successfully Time In.';
                    this.showSuccessInput = true;
       
                }
                if(res.data.status == 'success_out') {
                    this.profile.employee.firstname = res.data.profile.employee.firstname;
                    this.profile.employee.middlename = res.data.profile.employee.middlename;
                    this.profile.employee.lastname = res.data.profile.employee.lastname;
                    const position = await this.$API.QrCode.getPositionById(res.data.profile.employee.position_id);
                    this.profile.employee.position = position.data.name;
                    //console.log(position.data.name);
                     // Set the scanned time_out
                   this.qrCodeScannedTimeout = true;
                    this.scannedTimeOut = res.data.profile.time_out;
                   
                    this.successMessageOut = 'Successfully Time Out.';
                    this.showSuccessInputOut = true;
                }

                if(res.data.status == 'success_ot_in') {
                    this.profile.employee.firstname = res.data.profile.employee.firstname;
                    this.profile.employee.middlename = res.data.profile.employee.middlename;
                    this.profile.employee.lastname = res.data.profile.employee.lastname;
                    const position = await this.$API.QrCode.getPositionById(res.data.profile.employee.position_id);
                    //console.log(position.data.name);
                    this.profile.employee.position = position.data.name;
                    this.scannedOvertimeIn = res.data.profile.time_in;
                    this.qrCodeScanned = true;
                    this.successMessageOverTime = 'Successfully OverTime In.';
                    this.showSuccessInputOverTime = true;
                }

                if(res.data.status == 'success_ot_out') {
                    this.profile.employee.firstname = res.data.profile.employee.firstname;
                    this.profile.employee.middlename = res.data.profile.employee.middlename;
                    this.profile.employee.lastname = res.data.profile.employee.lastname;
                    const position = await this.$API.QrCode.getPositionById(res.data.profile.employee.position_id);
                    //console.log(position.data.name);
                    this.profile.employee.position = position.data.name;
                    this.scannedOvertimeOut = res.data.profile.time_in;
                    this.qrCodeScanned = true;
                    this.successMessageOverTime = 'Successfully OverTime Out.';
                    this.showSuccessInputOverTime = true;
                }

                if(res.data.status == 'invalid') {
                    this.profile.employee.firstname = res.data.profile.employee.firstname;
                    this.profile.employee.middlename = res.data.profile.employee.middlename;
                    this.profile.employee.lastname = res.data.profile.employee.lastname;
                    this.$notify.error({
                    message: 'Oops. you are not allowed to overtime',
                    type: 'danger'
                });
                    //const position = await this.$API.QrCode.getPositionById(res.data.profile.employee.position_id);
                    console.log(position.data.name);
                    this.profile.employee.position = position.data.name;
                }

                if(res.data.status == 'already_time_in_out') {
                    this.profile.employee.firstname = res.data.profile.employee.firstname;
                    this.profile.employee.middlename = res.data.profile.employee.middlename;
                    this.profile.employee.lastname = res.data.profile.employee.lastname;
                    //const position = await this.$API.QrCode.getPositionById(res.data.profile.employee.position_id);
                    console.log(position.data.name);
                    this.profile.employee.position = position.data.name;
                    this.$notify({
                    message: 'Oops. Already Time in and out',
                    type: 'danger'
                });
                }
                this.loadingByButton = false

                setTimeout(() => {
                    window.location.reload(true);
                }, 15000)
            } catch (error) {
                console.log(error);
            }

        }
    },
    watch: {
         search(val) {
             if( val == '') {
                 this.getAttendance();
             }
         }
     }
}
</script>
<style lang="scss">
    .page_title {
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-weight:700
    }

    .profileButton {
        height: 50px !important;
    }
</style>
