@extends('layouts.admin')

@section('title', 'Statistika')

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Kategoriyalar bo‘yicha Statistika</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <canvas id="statsChart" width="800" height="400"></canvas>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Kategoriya Nomi ({{ auth()->user()->language ?? 'uz' }})</th>
                                <th>O‘rtacha Ball</th>
                                <th>Urinishlar Soni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categoryStats as $stat)
                                <tr>
                                    <td>{{ $stat->category_name }}</td>
                                    <td>{{ number_format($stat->average_score, 2) }}</td>
                                    <td>{{ $stat->attempts }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Ma‘lumot topilmadi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/chart.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('statsChart').getContext('2d');
            const labels = @json($labels);
            const scores = @json($scores);
            const attempts = @json($attempts);

            const statsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'O‘rtacha Ball',
                            data: scores,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Urinishlar Soni',
                            data: attempts,
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Qiymat'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Kategoriyalar'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Kategoriyalar bo‘yicha Statistika'
                        }
                    }
                }
            });
        });
    </script>
@endpush