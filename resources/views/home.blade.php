@extends('layouts.company.company')

@section('content')
    <div class="content-wrapper">
    <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/home">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <section class="content">
                <div class="container-fluid">
                    <records-view></records-view>
                    <div class="row">
                        <div class="col-md-12">
                            <area-map-dashboard></area-map-dashboard>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div
@endsection
