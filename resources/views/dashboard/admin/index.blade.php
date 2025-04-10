@extends('layouts.app')

@section('header-title', 'Admin Dashboard')

@section('content')
    <style>
        #radarChart {
            width: 100%;
            height: 300px; /* Height of the canvas */
        }
    </style>

    <div class="row">
        <!-- Small Widget -->
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-info">
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

        <!-- Chart Section -->
        <div class="col-lg-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Radar Chart Example</h3>
                </div>
                <div class="card-body">
                    <canvas id="radarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const data = {
                labels: ['Speed', 'Strength', 'Endurance', 'Skill', 'Agility'],
                datasets: [{
                    label: 'Team A',
                    data: [65, 59, 90, 81, 56],
                    fill: true,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgb(54, 162, 235)',
                    pointBackgroundColor: 'rgb(54, 162, 235)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(54, 162, 235)'
                }]
            };

            const config = {
                type: 'radar',
                data: data,
                options: {
                    responsive: true,
                    elements: {
                        line: {
                            borderWidth: 3
                        }
                    }
                }
            };

            const radarChart = new Chart(
                document.getElementById('radarChart'),
                config
            );
        });
    </script>
@endsection
