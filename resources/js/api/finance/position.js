import axios from 'axios';

export default {
    getPositions(params) {
        return axios.get(`/position/get-positions?page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    storePosition(data) {
        return axios.post(`/position/store-position`, data)
    },
    updatePosition(id, data) {
        return axios.post(`/position/update-position/${id}`, data)
    },
    deletePosition(id) {
        return axios.post(`/position/delete-position/${id}`)
    },
    getPositionList() {
        return axios.get(`/position/get-position-list`)
    },
    getRoleList(){
        return axios.get(`/position/get-role-list`)
    },
    deletePosition(id){
        return axios.post(`/position/delete-position/${id}`)
    }
}
