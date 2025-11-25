@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Country-Wise Analytics</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></div>
            <div class="breadcrumb-item active">Countries</div>
        </div>
    </div>
    
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Visitor Distribution by Country</h4>
                        <div class="card-header-form">
                            <form method="GET" action="{{ route('admin.analytics.country-wise') }}" class="form-inline">
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
                                <h5>Top Countries</h5>
                                <canvas id="countries-chart" height="100"></canvas>
                            </div>
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Country</th>
                                                <th>Visits</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalVisits = collect($data)->sum('visits');
                                            @endphp
                                            @forelse(array_slice($data, 0, 10) as $country)
                                            <tr>
                                                <td>
                                                    <strong>{{ $country['country'] ?? 'Unknown' }}</strong>
                                                    <small class="text-muted">({{ $country['country_code'] ?? 'N/A' }})</small>
                                                </td>
                                                <td>{{ $country['visits'] }}</td>
                                                <td>
                                                    @php
                                                        $percentage = $totalVisits > 0 ? ($country['visits'] / $totalVisits) * 100 : 0;
                                                    @endphp
                                                    {{ number_format($percentage, 2) }}%
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No data available</td>
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
    const countriesCtx = document.getElementById('countries-chart');
    if (countriesCtx) {
        const countriesData = @json(array_slice($data, 0, 10));
        new Chart(countriesCtx, {
            type: 'doughnut',
            data: {
                labels: countriesData.map(item => item.country || 'Unknown'),
                datasets: [{
                    data: countriesData.map(item => item.visits),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)',
                        'rgba(255, 99, 255, 0.8)',
                        'rgba(99, 255, 132, 0.8)',
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
    }
</script>
@endpush


