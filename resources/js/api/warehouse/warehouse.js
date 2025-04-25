import axios from 'axios';

export default {

    // categories
    getCategories(params) {
        return axios.get(`/warehouse/get-categories?page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    storeCategory(data) {
        return axios.post('/warehouse/store-category', data)
    },
    updateCategory(id, data) {
        return axios.post(`/warehouse/update-category/${id}`, data)
    },
    deleteCategory(id) {
        return axios.post(`/warehouse/delete-category/${id}`)
    },
    searchCategory(search) {
        return axios.get(`/warehouse/search-categories?search=${search}`)
    },

    // items
    getItems(params) {
        return axios.get(`/warehouse/get-items?page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    storeItem(data) {
        return axios.post('/warehouse/store-item', data)
    },
    updateItem(id, data) {
        return axios.post(`/warehouse/update-item/${id}`, data)
    },
    deleteItem(id) {
        return axios.post(`/warehouse/delete-item/${id}`)
    },
    searchItem(search) {
        return axios.get(`/warehouse/search-items?search=${search}`)
    },

    // stocks
    getStocks(params) {
        return axios.get(`/warehouse/get-stocks?page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    storeStock(data) {
        return axios.post('/warehouse/store-stock', data)
    },
    updateStock(id, data) {
        return axios.post(`/warehouse/update-stock/${id}`, data)
    },
    deleteStock(id) {
        return axios.post(`/warehouse/delete-stock/${id}`)
    },
    reStock(id, data) {
        return axios.post(`/warehouse/re-stock/${id}`, data)
    },
    getReturnTools(){
        return axios.get('/deploy/get-return-tools')
    },

    getTools(params){
        return axios.get(`/deploy/get-tools?date=${params.date}&page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },

    returnTool(id) {
        return axios.post(`/deploy/return-tool/${id}`)
    },

    getDeploy(params) {
        return axios.get(`/deploy/get-deploy?date=${params.date}&page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },

    getDeployNotReturned(params) {
        return axios.get(`/deploy/get-deploy-not-returned?date=${params.date}&page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    getDeployNotReturnedConsumed(params) {
        return axios.get(`/deploy/get-deploy-not-returned-consumed?date=${params.date}&page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    getRequesto(params) {
        return axios.get(`/deploy/get-requesto?date=${params.date}&page=${params.current_page}&count=${params.current_size}&search=${params.search}`)
    },
    storeDeploy(data) {
        return axios.post('/deploy/store-deploy', data)
    },
    updateDeploy(id, data) {
        return axios.post(`/deploy/update-deploy/${id}`, data)
    },
    deleteDeploy(id) {
        return axios.post(`/deploy/delete-deploy/${id}`)
    },
    getHistory() {
        return axios.get('/deploy/get-history')
    },

    getWarehouseData(){
        return axios.get('/warehouse/get-warehouse-data')
    }
}
