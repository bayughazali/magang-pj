@extends('layouts.app')

@section('content')
<style>
    .header-bg {
        background: linear-gradient(90deg, #4c6ef5, #6a92ff);
        padding: 30px 0 120px 0;
        border-radius: 0 0 18px 18px;
    }
    .card-stats {
        border-radius: 14px;
        transition: .2s;
    }
    .card-stats:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    }

    /* BIAR KARTU BISA DIKLIK FULL */
    .card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .chart-card {
        border-radius: 14px;
        padding: 20px;
        height: 320px;
    }
    .table-card {
        border-radius: 14px;
    }
</style>

<div class="header-bg">
    <div class="container-fluid">
        <h2 class="text-white mb-3">Dashboard</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home text-white"></i></a></li>
                <li class="breadcrumb-item text-white">Dashboards</li>
                <li class="breadcrumb-item active text-white">Default</li>
            </ol>
        </nav>

        <div class="row mt-4">
            <div class="col-xl-3 col-sm-6">
                <a href="{{ url('/sales') }}" class="card-link">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="text-muted">Sales Report</h5>
                                    <span class="h2 font-weight-bold">2,356</span>
                                </div>
                                <div class="col-auto">
                                    <i class="ni ni-chart-bar-32 text-primary" style="font-size:32px"></i>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-sm"><span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>Since last month</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-sm-6">
                <a href="{{ url('/operational') }}" class="card-link">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="text-muted">Operational Report</h5>
                                    <span class="h2 font-weight-bold">924</span>
                                </div>
                                <div class="col-auto">
                                    <i class="ni ni-laptop text-success" style="font-size:32px"></i>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-sm"><span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>Since last month</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-sm-6">
                <a href="{{ url('/users') }}" class="card-link">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="text-muted">User Management</h5>
                                    <span class="h2 font-weight-bold">156</span>
                                </div>
                                <div class="col-auto">
                                    <i class="ni ni-single-02 text-info" style="font-size:32px"></i>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-sm"><span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 2.15%</span>Since last month</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-sm-6">
                <a href="{{ url('/export') }}" class="card-link">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="text-muted">Export Data</h5>
                                    <span class="h2 font-weight-bold">49,65%</span>
                                </div>
                                <div class="col-auto">
                                    <i class="ni ni-cloud-download-95 text-purple" style="font-size:32px"></i>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-sm"><span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>Since last month</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>

<div class="container-fluid mt-n5">
    <div class="row">
        <div class="col-xl-7">
            <div class="card chart-card">
                <canvas id="chart-line"></canvas>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card chart-card">
                <canvas id="chart-bar"></canvas>
            </div>
        </div>
    </div>

    <div class="card table-card mt-4">
        <div class="table-responsive p-3">
            <table class="table align-items-center">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Arengga</td>
                        <td>Admin</td>
                        <td>2024-10-23</td>
                        <td class="text-right"><a href="#" class="btn btn-sm btn-primary">Details</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('chart-line'), {
        type: 'line',
        data: { labels: ['Jan','Feb','Mar','Apr','May','Jun'], datasets: [{ data: [20,40,30,50,80,65], borderColor:'#4c6ef5', tension:.4 }] },
        options: { responsive:true, maintainAspectRatio:false }
    });
    new Chart(document.getElementById('chart-bar'), {
        type: 'bar',
        data: { labels: ['Mon','Tue','Wed','Thu','Fri'], datasets: [{ data: [12,19,3,5,7], backgroundColor:'#6a92ff' }] },
        options: { responsive:true, maintainAspectRatio:false }
    });
</script>
@endsection
