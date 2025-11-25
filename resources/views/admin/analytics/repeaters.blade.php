@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Returning Visitors Analytics</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></div>
            <div class="breadcrumb-item active">Returning Visitors</div>
        </div>
    </div>
    
    <div class="section-body">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Visitors</h4>
                        </div>
                        <div class="card-body">
                            {{ $data['total_visitors'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>New Visitors</h4>
                        </div>
                        <div class="card-body">
                            {{ $data['new_visitors'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Returning Visitors</h4>
                        </div>
                        <div class="card-body">
                            {{ $data['returning_visitors'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Returning %</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($data['returning_percentage'] ?? 0, 2) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Visitor Retention Analysis</h4>
                        <div class="card-header-form">
                            <form method="GET" action="{{ route('admin.analytics.repeaters') }}" class="form-inline">
                                <div class="input-group">
                                    <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}" required>
                                    <div class="input-group-btn">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>New vs Returning Visitors</h5>
                                <canvas id="visitor-type-chart" height="150"></canvas>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Visitor Statistics</h5>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Total Visitors:</strong></td>
                                                <td>{{ $data['total_visitors'] ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>New Visitors:</strong></td>
                                                <td>{{ $data['new_visitors'] ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Returning Visitors:</strong></td>
                                                <td>{{ $data['returning_visitors'] ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Returning Percentage:</strong></td>
                                                <td>{{ number_format($data['returning_percentage'] ?? 0, 2) }}%</td>
                                            </tr>
                                        </table>
                                        
                                        <div class="mt-4">
                                            <h6>Retention Rate Analysis</h6>
                                            <div class="progress mb-2">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ $data['returning_percentage'] ?? 0 }}%">
                                                    Returning: {{ number_format($data['returning_percentage'] ?? 0, 1) }}%
                                                </div>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-primary" role="progressbar" 
                                                     style="width: {{ 100 - ($data['returning_percentage'] ?? 0) }}%">
                                                    New: {{ number_format(100 - ($data['returning_percentage'] ?? 0), 1) }}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const visitorTypeCtx = document.getElementById('visitor-type-chart');
    if (visitorTypeCtx) {
        new Chart(visitorTypeCtx, {
            type: 'doughnut',
            data: {
                labels: ['New Visitors', 'Returning Visitors'],
                datasets: [{
                    data: [
                        {{ $data['new_visitors'] ?? 0 }},
                        {{ $data['returning_visitors'] ?? 0 }}
                    ],
                    backgroundColor: ['#36A2EB', '#FF6384'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const total = {{ $data['total_visitors'] ?? 0 }};
                                const value = context.parsed;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(2) : 0;
                                label += value + ' (' + percentage + '%)';
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endpush


