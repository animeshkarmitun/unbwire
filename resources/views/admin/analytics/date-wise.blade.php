@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Date-Wise Analytics</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></div>
            <div class="breadcrumb-item active">Date-Wise</div>
        </div>
    </div>
    
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Date Range Analytics</h4>
                        <div class="card-header-action">
                            <form method="GET" action="{{ route('admin.analytics.date-wise') }}" class="form-inline d-flex align-items-center">
                                <div class="form-group mb-0 mr-2">
                                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required style="height: 38px; border-radius: 0.25rem;">
                                </div>
                                <div class="form-group mb-0 mr-2">
                                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required style="height: 38px; border-radius: 0.25rem;">
                                </div>
                                <div class="form-group mb-0">
                                    <button class="btn btn-primary" type="submit" style="height: 38px; border-radius: 0.25rem;">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Visits Over Time</h5>
                                <div style="position: relative; height: 300px;">
                                    <canvas id="visits-chart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5>Visitors Over Time</h5>
                                <div style="position: relative; height: 300px;">
                                    <canvas id="visitors-chart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5>Page Views Over Time</h5>
                                <div style="position: relative; height: 300px;">
                                    <canvas id="page-views-chart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive mt-4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Visits</th>
                                        <th>Visitors</th>
                                        <th>Page Views</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $visitsData = $data['visits'] ?? collect();
                                        $visitorsData = $data['visitors'] ?? collect();
                                        $pageViewsData = $data['page_views'] ?? collect();
                                        
                                        // Combine all dates
                                        $allDates = collect();
                                        foreach([$visitsData, $visitorsData, $pageViewsData] as $dataset) {
                                            if ($dataset && is_iterable($dataset)) {
                                                foreach($dataset as $item) {
                                                    $dateValue = is_object($item) ? $item->date : (is_array($item) ? $item['date'] : null);
                                                    if ($dateValue) {
                                                        $allDates->push($dateValue);
                                                    }
                                                }
                                            }
                                        }
                                        $allDates = $allDates->unique()->sort()->values();
                                    @endphp
                                    
                                    @forelse($allDates as $date)
                                    @php
                                        $visit = $visitsData->first(function($item) use ($date) {
                                            $itemDate = is_object($item) ? $item->date : (is_array($item) ? $item['date'] : null);
                                            return $itemDate == $date;
                                        });
                                        $visitor = $visitorsData->first(function($item) use ($date) {
                                            $itemDate = is_object($item) ? $item->date : (is_array($item) ? $item['date'] : null);
                                            return $itemDate == $date;
                                        });
                                        $pageView = $pageViewsData->first(function($item) use ($date) {
                                            $itemDate = is_object($item) ? $item->date : (is_array($item) ? $item['date'] : null);
                                            return $itemDate == $date;
                                        });
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</td>
                                        <td>{{ (is_object($visit) ? $visit->visits : ($visit['visits'] ?? 0)) ?? 0 }}</td>
                                        <td>{{ (is_object($visitor) ? $visitor->visitors : ($visitor['visitors'] ?? 0)) ?? 0 }}</td>
                                        <td>{{ (is_object($pageView) ? $pageView->page_views : ($pageView['page_views'] ?? 0)) ?? 0 }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No data available for the selected date range</td>
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
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Visits Chart
            const visitsCtx = document.getElementById('visits-chart');
            if (visitsCtx) {
                const visitsData = @json(($data['visits'] ?? collect())->toArray());
                
                if (visitsData.length > 0) {
                    new Chart(visitsCtx, {
                        type: 'line',
                        data: {
                            labels: visitsData.map(item => {
                                try {
                                    const date = new Date(item.date + 'T00:00:00');
                                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                                } catch(e) {
                                    return item.date;
                                }
                            }),
                            datasets: [{
                                label: 'Visits',
                                data: visitsData.map(item => parseInt(item.visits) || 0),
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.1,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                } else {
                    visitsCtx.parentElement.innerHTML = '<p class="text-center text-muted">No visits data available for the selected date range</p>';
                }
            }

            // Visitors Chart
            const visitorsCtx = document.getElementById('visitors-chart');
            if (visitorsCtx) {
                const visitorsData = @json(($data['visitors'] ?? collect())->toArray());
                
                if (visitorsData.length > 0) {
                    new Chart(visitorsCtx, {
                        type: 'line',
                        data: {
                            labels: visitorsData.map(item => {
                                try {
                                    const date = new Date(item.date + 'T00:00:00');
                                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                                } catch(e) {
                                    return item.date;
                                }
                            }),
                            datasets: [{
                                label: 'Visitors',
                                data: visitorsData.map(item => parseInt(item.visitors) || 0),
                                borderColor: 'rgb(54, 162, 235)',
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                tension: 0.1,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                } else {
                    visitorsCtx.parentElement.innerHTML = '<p class="text-center text-muted">No visitors data available for the selected date range</p>';
                }
            }

            // Page Views Chart
            const pageViewsCtx = document.getElementById('page-views-chart');
            if (pageViewsCtx) {
                const pageViewsData = @json(($data['page_views'] ?? collect())->toArray());
                
                if (pageViewsData.length > 0) {
                    new Chart(pageViewsCtx, {
                        type: 'line',
                        data: {
                            labels: pageViewsData.map(item => {
                                try {
                                    const date = new Date(item.date + 'T00:00:00');
                                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                                } catch(e) {
                                    return item.date;
                                }
                            }),
                            datasets: [{
                                label: 'Page Views',
                                data: pageViewsData.map(item => parseInt(item.page_views) || 0),
                                borderColor: 'rgb(255, 99, 132)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                tension: 0.1,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                } else {
                    pageViewsCtx.parentElement.innerHTML = '<p class="text-center text-muted">No page views data available for the selected date range</p>';
                }
            }
        } catch(error) {
            console.error('Error initializing charts:', error);
        }
    });
</script>
@endpush

