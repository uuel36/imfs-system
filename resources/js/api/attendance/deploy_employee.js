import axios from 'axios';

export default {

    getDeploy(params) {
        return axios.get(`/deploy-employee/get-deploy?date=${params.date}&page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    deployTeam (data) {
        return axios.post(`/deploy-employee/store-deploy`, data)
    },
    updateDeploy (id, data) {
        return axios.post(`/deploy-employee/update-deploy/${id}`, data)
    },
    deleteDeploy (id) {
        return axios.post(`/deploy-employee/delete-deploy/${id}`)
    },
    getDeployByArea(params) {
        return axios.get(`/deploy-employee/get-deploy-by-area?date=${params.date}&search=${params.search}`)
    },
    searchDeployByDate(search){
        return axios.get(`/deploy-employee/search-deploy-by-date?date=${search.date}&team_id=${search.team_id}`)
    },
    getDeployByTeamIdAndDate(id, date){
        return axios.get(`/deploy-employee/get-deploy-by-team-id-and-date?team_id=${id}&date=${date}`)
    }
}
