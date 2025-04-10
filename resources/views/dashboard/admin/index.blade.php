@extends('layouts.app')

@section('header-title', 'Admin Dashboard')

@section('content')
    <style>
        /* Example for custom canvas sizing */
        .chart-container {
            width: 100%;
            height: 300px;
        }
        .small-box h3 {
            font-size: 2rem;
        }
    </style>

    <div class="row">

        <!-- Example Widgets -->
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

        <!-- Chart Examples -->
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
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Radar Chart
            const radarData = {
                labels: ['Speed', 'Strength', 'Endurance', 'Skill', 'Agility'],
                datasets: [{
                    label: 'Team A',
                    data: [65, 59, 90, 81, 56],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgb(54, 162, 235)',
                }]
            };
            new Chart(document.getElementById('radarChart'), {
                type: 'radar',
                data: radarData,
                options: { responsive: true }
            });

            // Bar Chart
            const barData = {
                labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                datasets: [{
                    label: 'Monthly Sales',
                    data: [15, 30, 25, 40, 20, 35],
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgb(75, 192, 192)',
                }]
            };
            new Chart(document.getElementById('barChart'), {
                type: 'bar',
                data: barData,
                options: { responsive: true }
            });

            // Doughnut Chart
            const doughnutData = {
                labels: ['Marketing', 'Development', 'Support'],
                datasets: [{
                    label: 'Department Allocation',
                    data: [30, 50, 20],
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffce56'],
                }]
            };
            new Chart(document.getElementById('doughnutChart'), {
                type: 'doughnut',
                data: doughnutData,
                options: { responsive: true }
            });

            // Line Chart
            const lineData = {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Weekly Visits',
                    data: [200, 300, 250, 400],
                    borderColor: 'rgba(153, 102, 255, 1)',
                    fill: false,
                }]
            };
            new Chart(document.getElementById('lineChart'), {
                type: 'line',
                data: lineData,
                options: { responsive: true }
            });
        });
    </script>
@endsection
