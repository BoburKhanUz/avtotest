@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </h1>
            <p class="text-muted">Platformaning umumiy ko'rinishi va statistikasi</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Foydalanuvchilar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalUsers) }}</div>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> Aktiv</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Tests -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Testlar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalTests) }}</div>
                            <small class="text-muted">Mavjud testlar soni</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Faol Obunalar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($activeSubscriptions) }}</div>
                            <small class="text-info"><i class="fas fa-sync"></i> Davom etmoqda</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Umumiy Daromad
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRevenue, 0, '.', ' ') }} so'm</div>
                            <small class="text-success"><i class="fas fa-chart-line"></i> Muvaffaqiyatli</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Chart Column -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area"></i> Oylik Statistika
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Filter:</div>
                            <a class="dropdown-item" href="#">Bu oy</a>
                            <a class="dropdown-item" href="#">O'tgan oy</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie"></i> Kategoriya Bo'yicha
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart" style="height: 245px;"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Yo'l qoidalari
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Belgilar
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Boshqalar
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tasks"></i> So'nggi Testlar
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Foydalanuvchi</th>
                                    <th>Test</th>
                                    <th>Natija</th>
                                    <th>Vaqt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-user-circle text-primary"></i> John Doe</td>
                                    <td>Yo'l qoidalari #1</td>
                                    <td><span class="badge badge-success">85%</span></td>
                                    <td>2 daqiqa oldin</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-user-circle text-info"></i> Jane Smith</td>
                                    <td>Belgilar testi</td>
                                    <td><span class="badge badge-warning">65%</span></td>
                                    <td>5 daqiqa oldin</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-user-circle text-success"></i> Bob Johnson</td>
                                    <td>Amaliy test</td>
                                    <td><span class="badge badge-success">90%</span></td>
                                    <td>10 daqiqa oldin</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('admin.stats') }}" class="btn btn-primary btn-sm btn-block">
                        <i class="fas fa-chart-bar"></i> Batafsil Statistika
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bell"></i> Tezkor Amallar
                    </h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.tests.create') }}" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-plus-circle"></i> Yangi Test Qo'shish
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-users"></i> Foydalanuvchilarni Ko'rish
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-folder"></i> Kategoriyalar
                    </a>
                    <a href="{{ route('admin.plans.index') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-star"></i> Tariflar
                    </a>
                    <a href="{{ route('admin.promocodes.index') }}" class="btn btn-secondary btn-block mb-2">
                        <i class="fas fa-tag"></i> Promokodlar
                    </a>
                    <a href="{{ route('admin.api-docs') }}" class="btn btn-dark btn-block">
                        <i class="fas fa-code"></i> API Dokumentatsiya
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Area Chart
var ctx = document.getElementById("myAreaChart");
var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Yan", "Fev", "Mar", "Apr", "May", "Iyun", "Iyul", "Avg", "Sen", "Okt", "Noy", "Dek"],
        datasets: [{
            label: "Testlar",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: [0, 10, 5, 15, 10, 20, 15, 25, 20, 30, 25, 40],
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                ticks: {
                    maxTicksLimit: 5
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Pie Chart
var ctx2 = document.getElementById("myPieChart");
var myPieChart = new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ["Yo'l qoidalari", "Belgilar", "Amaliy", "Boshqalar"],
        datasets: [{
            data: [55, 25, 15, 5],
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endpush
@endsection
