@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Organic Traffic Analytics</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></div>
            <div class="breadcrumb-item active">Organic Traffic</div>
        </div>
    </div>
    
    <div class="section-body">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Organic</h4>
                        </div>
                        <div class="card-body">
                            {{ $data['total_organic'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Visits</h4>
                        </div>
                        <div class="card-body">
                            {{ $data['total_visits'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Organic %</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($data['organic_percentage'] ?? 0, 2) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Organic Traffic Sources</h4>
                        <div class="card-header-form">
                            <form method="GET" action="{{ route('admin.analytics.organic') }}" class="form-inline">
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
                                <h5>Top Organic Referrers</h5>
                                <canvas id="organic-chart" height="100"></canvas>
                            </div>
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Referrer</th>
                                                <th>Visits</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalOrganic = $data['total_organic'] ?? 0;
                                                $byReferrer = $data['by_referrer'] ?? collect();
                                            @endphp
                                            @forelse($byReferrer->take(10) as $referrer)
                                            <tr>
                                                <td>
                                                    <strong>{{ $referrer['referrer'] ?? 'Unknown' }}</strong>
                                                </td>
                                                <td>{{ $referrer['visits'] }}</td>
                                                <td>
                                                    @php
                                                        $percentage = $totalOrganic > 0 ? ($referrer['visits'] / $totalOrganic) * 100 : 0;
                                                    @endphp
                                                    {{ number_format($percentage, 2) }}%
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No organic traffic data available</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
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
    const organicCtx = document.getElementById('organic-chart');
    if (organicCtx) {
        const referrerData = @json(($data['by_referrer'] ?? collect())->take(10)->toArray());
        new Chart(organicCtx, {
            type: 'bar',
            data: {
                labels: referrerData.map(item => {
                    const url = item.referrer || 'Unknown';
                    return url.length > 30 ? url.substring(0, 30) + '...' : url;
                }),
                datasets: [{
                    label: 'Visits',
                    data: referrerData.map(item => item.visits),
                    backgroundColor: 'rgba(75, 192, 192, 0.8)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
</script>
@endpush


