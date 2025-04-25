import axios from 'axios'

export default {
    getPayrolls(params){
        return axios.get(`/finance/get-payrolls?date=${params.date}&page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    generatePayroll(data) {
        return axios.post('/finance/generate-payroll', data)
    },
    storePayroll(data) {
        return axios.post(`/finance/store-payroll`, data)
    },
    getOvertimeRate() {
        return axios.get(`/finance/overtime-rate`);
    },
    updateFinanceSetting(id, data) {
        return axios.post(`/finance/update-finance-setting/${id}`, data)
    },
    getDeductions(employee_id) {
        return axios.get(`/finance/get-employee-deduction/${employee_id}`)
    },
    getAllPayrolls() {
        return axios.get(`/finance/get-all-payrolls`)
    },

    getCurrentPayrollGenerateDate(){
        return axios.get(`/finance/get-current-payroll-generate-date`)
    },
    getArchivePayrolls(params){
        return axios.get(`/finance/get-archive-payrolls?date=${params.date}&page=${params.current_page}&count=${params.current_size}&search=${params.search}&id=${params.id}`)
    },
    getHistoryPayrolls(params){
        return axios.get(`/finance/get-history-payrolls?date=${params.date}&page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    getArchiveRecords(params) {
        return axios.get(`/finance/get-archive-records?date=${params.date}&page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
}
