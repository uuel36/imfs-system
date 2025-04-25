import axios from 'axios';

export default {
    getLeadmans(){
        return axios.get('/admin/get-leadmans');
    },
    getLeadmanById(id){
        return axios.get('/admin/get-leadman-by-id/'+id);
    },
    createLeadman(data){
        return axios.post('/admin/create-leadman', data);
    },
    deleteLeadman($id){
        return axios.delete('/admin/delete-leadman/'+$id);
    },
    getCurrentUserRole(){
        return axios.get('/admin/get-current-user-role');
    },

    approveRequest(id) {
        return axios.post(`/company/approve-request/${id}`);
    },
    disapproveRequest(id) {
        return axios.post(`/company/disapprove-request/${id}`);
    },
}
