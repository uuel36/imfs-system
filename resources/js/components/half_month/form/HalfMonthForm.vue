<template>
    <div>
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="page_title">GENERATE HALF MONTH REPORT</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/home">Home</a></li>
                            <li class="breadcrumb-item"><a href="/half-month#/reports">Reports</a></li>
                            <li class="breadcrumb-item active">Reports</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <half-month-generate-form></half-month-generate-form>

                    <half-month-generated-list></half-month-generated-list>
                </div>
            </section>
        </div>
    </div>
</template>
<script>
export default {
    name: 'StockIndex',
    data() {
        return {

        }
    },
    created() {

    },
}
</script>
