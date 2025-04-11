@extends('layouts.admin')

@section('header-title', 'Admin Dashboard')

@section('content')
    <style>
        /* Styling for charts and widgets */
        .chart-container {
            width: 100%;
            height: 300px;
        }

        .small-box h3 {
            font-size: 2rem;
        }
    </style>
    <div class="app-container-fluid">
        <div class="container mt-5">
            <div class="row">
                <!-- Widgets Section -->
                <div class="col-lg-3 col-sm-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>150</h3>
                            <p>New Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>530</h3>
                            <p>Unique Logins</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="small-box bg-warning text-white">
                        <div class="inner">
                            <h3>75%</h3>
                            <p>Task Completion</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            Detailed Report <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>320</h3>
                            <p>Issues Reported</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Summary <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Additional Widgets -->
                <div class="col-lg-6 col-sm-12">
                    <div class="card bg-light">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Monthly Revenue</h3>
                        </div>
                        <div class="card-body">
                            <h4>Total Revenue: <strong>$12,450</strong></h4>
                            <p>Compared to last month: <span class="text-success"><i class="fas fa-level-up-alt"></i> +12%</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <div class="card bg-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-pie"></i> Top Performing Category</h3>
                        </div>
                        <div class="card-body">
                            <h5><i class="fas fa-arrow-circle-right"></i> Category: Electronics</h5>
                            <p>Revenue Contribution: <span class="text-primary">45%</span></p>
                        </div>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Radar Chart</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="radarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Bar Chart</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Doughnut Chart</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="doughnutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Line Chart</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="lineChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pie Chart -->
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pie Chart</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Radar Chart
            new Chart(document.getElementById('radarChart'), {
                type: 'radar',
                data: {
                    labels: ['Speed', 'Strength', 'Endurance', 'Skill', 'Agility'],
                    datasets: [{
                        label: 'Team A',
                        data: [65, 59, 90, 81, 56],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgb(54, 162, 235)'
                    }]
                },
                options: {responsive: true}
            });

            // Bar Chart
            new Chart(document.getElementById('barChart'), {
                type: 'bar',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                    datasets: [{
                        label: 'Sales',
                        data: [15, 30, 25, 40, 35, 50],
                        backgroundColor: 'rgba(255, 206, 86, 0.6)',
                        borderColor: 'rgb(255, 206, 86)'
                    }]
                },
                options: {responsive: true}
            });

            // Doughnut Chart
            new Chart(document.getElementById('doughnutChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Marketing', 'Development', 'Support'],
                    datasets: [{
                        data: [30, 40, 30],
                        backgroundColor: ['#ff6384', '#36a2eb', '#ff9f40']
                    }]
                },
                options: {responsive: true}
            });

            // Line Chart
            new Chart(document.getElementById('lineChart'), {
                type: 'line',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    datasets: [{
                        label: 'Traffic',
                        data: [200, 350, 280, 450],
                        borderColor: 'rgb(75, 192, 192)',
                        fill: false
                    }]
                },
                options: {responsive: true}
            });

            // Pie Chart
            new Chart(document.getElementById('pieChart'), {
                type: 'pie',
                data: {
                    labels: ['Direct', 'Referral', 'Organic'],
                    datasets: [{
                        data: [50, 30, 20],
                        backgroundColor: ['#4caf50', '#2196f3', '#ff9800']
                    }]
                },
                options: {responsive: true}
            });
        });
    </script>
@endsection
