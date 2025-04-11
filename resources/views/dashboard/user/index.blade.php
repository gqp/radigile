@extends('layouts.user')

@section('content')
    <style>
        .chart-container {
            width: 100%;
            height: 300px;
        }

        .small-box h3 {
            font-size: 2rem;
        }
    </style>
    <!-- Include Navbar -->
    @include('layouts.user.navbar')
    <div class="container mt-5">
        <div class="row">
            <!-- Welcome Section -->
            <div class="col-md-12 text-center mb-4">
                <h1>Welcome back, {{ Auth::user()->name }}!</h1>
                <p>Your personalized dashboard is here to keep you on track.</p>
            </div>

            <!-- Widgets Section -->
            <div class="col-lg-3 col-sm-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>8</h3>
                        <p>New Notifications</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        View Details <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>15</h3>
                        <p>Projects</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        Explore Projects <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="small-box bg-warning text-white">
                    <div class="inner">
                        <h3>22</h3>
                        <p>Pending Tasks</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        View Tasks <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>85%</h3>
                        <p>Task Completion</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        See Progress <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Graph Section -->
            <div class="col-lg-6 col-sm-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h4 class="card-title"><i class="fas fa-chart-area"></i> Weekly Activity Overview</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="activityChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="col-lg-6 col-sm-12">
                <div class="card bg-light">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-info-circle"></i> Performance Summary</h4>
                    </div>
                    <div class="card-body">
                        <h5>Tasks Completed This Week: <strong>18</strong></h5>
                        <h5>Total Hours Logged: <strong>42 hrs</strong></h5>
                        <p>Keep up the great work!</p>
                    </div>
                </div>
            </div>

            <!-- Additional Features -->
            <div class="col-lg-12 col-sm-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h4 class="card-title"><i class="fas fa-calendar-check"></i> Upcoming Deadlines</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">Submit Project Report: <strong>2 days left</strong></li>
                            <li class="list-group-item">Attend Weekly Review Meeting: <strong>3 days left</strong></li>
                            <li class="list-group-item">Complete Training Module: <strong>5 days left</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart.js example for the Weekly Activity Overview
        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                datasets: [{
                    label: 'Hours Worked',
                    data: [6, 7, 5, 8, 6, 4, 7],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
@endsection
