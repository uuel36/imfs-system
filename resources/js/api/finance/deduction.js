import axios from 'axios';

export default {
    getPhilhealth(params) {
        return axios.get(`/deduction/get-philhealth?page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    storePhilhealth(data) {
        return axios.post(`/deduction/store-philhealth`, data)
    },
    updatePhilhealth(id, data) {
        return axios.post(`/deduction/update-philhealth/${id}`, data)
    },
    deletePhilhealth(id) {
        return axios.post(`/deduction/delete-philhealth/${id}`)
    },

    getSSS(params) {
        return axios.get(`/deduction/get-sss?page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    storeSSS(data) {
        return axios.post(`/deduction/store-sss`, data)
    },
    updateSSS(id, data) {
        return axios.post(`/deduction/update-sss/${id}`, data)
    },
    deleteSSS(id) {
        return axios.post(`/deduction/delete-sss/${id}`)
    },


}
