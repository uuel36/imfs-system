import axios from 'axios'

export default {
    getReports(params) {
        return axios.get(`/daily-report/list?date=${params.date}&page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    storeReport(data) {
        console.log(data);
        return axios.post(`/daily-report/store`, data)
    },
    updateReport(id, data) {
        return axios.post(`/daily-report/update/${id}`, data)
    },
    deleteReport(id) {
        return axios.post(`/daily-report/delete/${id}`);
    },
    getTeamReport(params) {
        return axios.get(`/daily-report/team-report?date=${params.date}&page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    
}
