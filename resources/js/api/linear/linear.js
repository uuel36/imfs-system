import axios from 'axios';

export default {
  getLinears(params) {
    return axios.get(`/linear/get-linears?page=${params.current_page}&count=${params.current_size}&search=${params.search}&area_id=${params.area_id}`);
  },
  storeLinear(data) {
    return axios.post(`/linear/store-linear`, data);
  },
  updateLinear(id, data) {
    return axios.post(`/linear/update-linear/${id}`, data);
  },
  deleteLinear(id) {
    return axios.post(`/linear/delete-linear/${id}`);
  },
  getData(id) {
    return axios.get(`/linear/get-data/${id}`);
  },
  getDataGraph(id) {
    return axios.get(`/linear/get-data-graph?area_id=${id.area_id}&month=${id.month}&year=${id.year}`);
  },

  getDataGraphByYear(id) {
    return axios.get(`/linear/get-data-graph-by-year?area_id=${id.area_id}&month=${id.month}&year=${id.year}`);
  },
  getForecast(id) {
    return axios.get(`/linear/get-forecast?area_id=${id.area_id}&month=${id.month}&year=${id.year}`);
  },
  getDataGraphByYearX1(id) {
    return axios.get(`/linear/get-data-graph-by-year-x1?area_id=${id.area_id}&month=${id.month}&year=${id.year}`);
  },
  getDataGraphByYearY(id) {
    return axios.get(`/linear/get-data-graph-by-year-y?area_id=${id.area_id}&month=${id.month}&year=${id.year}`);
  },
  getDataGraphByYearX2(id) {
    return axios.get(`/linear/get-data-graph-by-year-x2?area_id=${id.area_id}&month=${id.month}&year=${id.year}`);
  },
  getDataGraphByYearAll(id) {
    return axios.get(`/linear/get-data-graph-by-year-all?month=${id.month}&year=${id.year}`);
  },
  getStemDataGraphByYear(id) {
    return axios.get(`/linear/get-stem-data-graph-by-year?area_id=${id.area_id}&month=${id.month}&year=${id.year}`);
  },
  getStemDataGraphByYearP(id) {
    return axios.get(`/linear/get-stem-data-graph-by-yearp?area_id=${id.area_id}&month=${id.month}&year=${id.year}`);
  },
  getStemDataGraphByYearAll(id) {
    return axios.get(`/linear/get-stem-data-graph-by-year-all?month=${id.month}&year=${id.year}`);
  },
  calculateLinearValues() {
    return axios.get(`/linear/calculate-linear-values`);
  },
  getHistoricalDataByYear(year) {
    return axios.get(`/linear/get-historical-data-by-year?year=${year}`);
  },
  forecastLinearValues(areaId) {
    return axios.get(`/linear/forecast-linear-values/${areaId}`);
  },

  predictAndShow(data) {
    return axios.post(`/linear/predict-and-show`, data);
  },

  getAllLinearRegressions() {
    return axios.get('/linear/get-all-linear-regressions');
  },

  calculateMSE(data) {
    return axios.post('/linear/calculate-mse', data);
  },

};
