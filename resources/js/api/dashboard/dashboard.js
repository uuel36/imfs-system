import axios from 'axios'

export default {
    getTotalEmployees() {
        return axios.get(`/dashboard/total-employees`)
    },
    getTotalAreas() {
        return axios.get(`/dashboard/total-areas`)
    },
    getTotalOperations() {
        return axios.get(`/dashboard/total-operations`)
    },
    getTotalPresent() {
        return axios.get(`/dashboard/total-present`)
    },
    getWarehouseData(){
        return axios.get(`/dashboard/warehouse-data`)
    },
    getOvertimeEmployees(){
        return axios.get(`/dashboard/overtime-employees`)
    },
    // get total teams
    getTotalTeams() {
        return axios.get(`/dashboard/total-teams`)
    },
}
