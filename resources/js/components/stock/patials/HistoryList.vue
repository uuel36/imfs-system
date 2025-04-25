<template>
    <div>
        <global-pagination
            :current_page="current_page"
            :current_size="current_size"
            :pagination="historyPagination"
            :total="filters.total"
            @handleSizeChange="handleSizeChange"
            @handleCurrentChange="handleCurrentChange">
        </global-pagination>
    </div>
</template>
<script>
import paginate from '../../../mixin/pagination'
export default {
    name: "HistoryList",
    mixins: [paginate],
    data() {
        return {
            history: [],
            historyPagination: [],
            loading: false,
            current_page: 1,
            current_size: 25,
            search: null,
        }
    },
    created() {
        this.getHistory();
    },
    methods: {
        async getHistory() {
            await this.$API.Warehouse.getHistory()
                .then(response => {
                    this.history = response.data.data
                    this.historyPagination = response.data
                }).catch(error => {
                    console.log(error);
                })
        }
    },
}
</script>
