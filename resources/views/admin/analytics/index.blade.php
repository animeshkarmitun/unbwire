@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Visitor Analytics</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Analytics</div>
        </div>
        <div class="section-header-action">
            <a href="{{ route('admin.analytics.most-viewed-pages') }}" class="btn btn-info ml-2">
                <i class="fas fa-file-alt"></i> Most Viewed Pages
            </a>
            <a href="{{ route('admin.analytics.most-visited-ips') }}" class="btn btn-warning ml-2">
                <i class="fas fa-ban"></i> Most Visited IPs
            </a>
            <a href="{{ route('admin.analytics.settings') }}" class="btn btn-primary ml-2">
                <i class="fas fa-cog"></i> Settings
            </a>
        </div>
    </div>
    
    <div class="section-body">
        <!-- Real-Time Stats Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Active Visitors (5 min)</h4>
                        </div>
                        <div class="card-body" id="active-visitors">
                            {{ $todayStats['active_visitors'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Visits Today</h4>
                        </div>
                        <div class="card-body" id="visits-today">
                            {{ $todayStats['visits_today'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Page Views Today</h4>
                        </div>
                        <div class="card-body" id="page-views-today">
                            {{ $todayStats['page_views_today'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Unique Visitors Today</h4>
                        </div>
                        <div class="card-body" id="unique-visitors-today">
                            {{ $todayStats['unique_visitors_today'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Analytics Dashboard</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.analytics.date-wise') }}" class="btn btn-primary">
                                <i class="fas fa-calendar"></i> Date Range
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="analytics-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab">
                                    Overview
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="countries-tab" data-toggle="tab" href="#countries" role="tab">
                                    Countries
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="organic-tab" data-toggle="tab" href="#organic" role="tab">
                                    Organic Traffic
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="repeaters-tab" data-toggle="tab" href="#repeaters" role="tab">
                                    Returning Visitors
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="bot-activity-tab" data-toggle="tab" href="#bot-activity" role="tab">
                                    Bot Activity
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" id="analytics-tab-content">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h5>Last 7 Days Overview</h5>
                                        <canvas id="visits-chart" height="100"></canvas>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Traffic Sources</h5>
                                        <canvas id="traffic-sources-chart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Countries Tab -->
                            <div class="tab-pane fade" id="countries" role="tabpanel">
                                <div class="table-responsive mt-4">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Country</th>
                                                <th>Visits</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(array_slice($topCountries, 0, 10) as $country)
                                            <tr>
                                                <td>
                                                    <strong>{{ $country['country'] ?? 'Unknown' }}</strong>
                                                    <small class="text-muted">({{ $country['country_code'] ?? 'N/A' }})</small>
                                                </td>
                                                <td>{{ $country['visits'] }}</td>
                                                <td>
                                                    @php
                                                        $total = collect($topCountries)->sum('visits');
                                                        $percentage = $total > 0 ? ($country['visits'] / $total) * 100 : 0;
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
                            
                            <!-- Organic Traffic Tab -->
                            <div class="tab-pane fade" id="organic" role="tabpanel">
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5>Organic Traffic Stats</h5>
                                                <p><strong>Total Organic:</strong> {{ $organicStats['total_organic'] ?? 0 }}</p>
                                                <p><strong>Total Visits:</strong> {{ $organicStats['total_visits'] ?? 0 }}</p>
                                                <p><strong>Organic Percentage:</strong> {{ number_format($organicStats['organic_percentage'] ?? 0, 2) }}%</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Top Organic Referrers</h5>
                                        <ul class="list-group">
                                            @forelse(array_slice($organicStats['by_referrer']->toArray(), 0, 5) as $referrer)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $referrer['referrer'] ?? 'Unknown' }}
                                                <span class="badge badge-primary badge-pill">{{ $referrer['visits'] }}</span>
                                            </li>
                                            @empty
                                            <li class="list-group-item">No organic traffic data</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Returning Visitors Tab -->
                            <div class="tab-pane fade" id="repeaters" role="tabpanel">
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5>Visitor Statistics</h5>
                                                <p><strong>Total Visitors:</strong> {{ $repeaterStats['total_visitors'] ?? 0 }}</p>
                                                <p><strong>New Visitors:</strong> {{ $repeaterStats['new_visitors'] ?? 0 }}</p>
                                                <p><strong>Returning Visitors:</strong> {{ $repeaterStats['returning_visitors'] ?? 0 }}</p>
                                                <p><strong>Returning Percentage:</strong> {{ number_format($repeaterStats['returning_percentage'] ?? 0, 2) }}%</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <canvas id="visitor-type-chart" height="150"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bot Activity Tab -->
                            <div class="tab-pane fade" id="bot-activity" role="tabpanel">
                                <div class="row mt-4">
                                    <div class="col-12 mb-4">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="card card-statistic-1">
                                                    <div class="card-icon bg-success">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div class="card-wrap">
                                                        <div class="card-header">
                                                            <h4>Humans</h4>
                                                        </div>
                                                        <div class="card-body">
                                                            {{ $botVsHumanStats['humans'] ?? 0 }}
                                                            <small class="text-muted">({{ $botVsHumanStats['human_percentage'] ?? 0 }}%)</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card card-statistic-1">
                                                    <div class="card-icon bg-warning">
                                                        <i class="fas fa-robot"></i>
                                                    </div>
                                                    <div class="card-wrap">
                                                        <div class="card-header">
                                                            <h4>Bots</h4>
                                                        </div>
                                                        <div class="card-body">
                                                            {{ $botVsHumanStats['bots'] ?? 0 }}
                                                            <small class="text-muted">({{ $botVsHumanStats['bot_percentage'] ?? 0 }}%)</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card card-statistic-1">
                                                    <div class="card-icon bg-danger">
                                                        <i class="fas fa-spider"></i>
                                                    </div>
                                                    <div class="card-wrap">
                                                        <div class="card-header">
                                                            <h4>Scrapers</h4>
                                                        </div>
                                                        <div class="card-body">
                                                            {{ $botVsHumanStats['scrapers'] ?? 0 }}
                                                            <small class="text-muted">({{ $botVsHumanStats['scraper_percentage'] ?? 0 }}%)</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card card-statistic-1">
                                                    <div class="card-icon bg-info">
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                    <div class="card-wrap">
                                                        <div class="card-header">
                                                            <h4>Total</h4>
                                                        </div>
                                                        <div class="card-body">
                                                            {{ $botVsHumanStats['total'] ?? 0 }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <canvas id="bot-vs-human-chart" height="200"></canvas>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Bot Detection Methods</h5>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group">
                                                    <li class="list-group-item">
                                                        <i class="fas fa-check text-success"></i> User Agent Analysis
                                                        <br><small class="text-muted">Detects known bot user agents</small>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <i class="fas fa-check text-success"></i> Behavioral Analysis
                                                        <br><small class="text-muted">Analyzes visit patterns, page views per second, session duration</small>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <i class="fas fa-check text-success"></i> Pattern Analysis
                                                        <br><small class="text-muted">Checks for suspicious headers, referrers, and rate limiting</small>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Recent Bot Activity</h5>
                                                <div class="card-header-action">
                                                    <a href="{{ route('admin.analytics.bot-activity') }}" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-eye"></i> View All
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="text-muted">View detailed bot activity in the dedicated Bot Activity page.</p>
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
    // Real-time updates every 30 seconds
    setInterval(function() {
        fetch('{{ route("admin.analytics.real-time") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('active-visitors').textContent = data.active_visitors || 0;
                document.getElementById('visits-today').textContent = data.visits_today || 0;
                document.getElementById('page-views-today').textContent = data.page_views_today || 0;
                document.getElementById('unique-visitors-today').textContent = data.unique_visitors_today || 0;
            });
    }, 30000);

    // Chart.js initialization
    @if(isset($last7DaysData))
    // Visits Chart
    const visitsCtx = document.getElementById('visits-chart');
    if (visitsCtx) {
        new Chart(visitsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($last7DaysData['visits']->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->toArray()) !!},
                datasets: [{
                    label: 'Visits',
                    data: {!! json_encode($last7DaysData['visits']->pluck('visits')->toArray()) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            }
        });
    }

    // Visitor Type Chart
    const visitorTypeCtx = document.getElementById('visitor-type-chart');
    if (visitorTypeCtx) {
        new Chart(visitorTypeCtx, {
            type: 'doughnut',
            data: {
                // Bot vs Human Chart
                const botVsHumanCtx = document.getElementById('bot-vs-human-chart');
                if (botVsHumanCtx) {
                    new Chart(botVsHumanCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Humans', 'Bots', 'Scrapers'],
                            datasets: [{
                                data: [
                                    {{ $botVsHumanStats['humans'] ?? 0 }},
                                    {{ $botVsHumanStats['bots'] ?? 0 }},
                                    {{ $botVsHumanStats['scrapers'] ?? 0 }}
                                ],
                                backgroundColor: [
                                    'rgba(40, 167, 69, 0.8)',
                                    'rgba(255, 193, 7, 0.8)',
                                    'rgba(220, 53, 69, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(40, 167, 69, 1)',
                                    'rgba(255, 193, 7, 1)',
                                    'rgba(220, 53, 69, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                title: {
                                    display: true,
                                    text: 'Bot vs Human Traffic'
                                }
                            }
                        }
                    });
                }

                labels: ['New Visitors', 'Returning Visitors'],
                datasets: [{
                    data: [
                        {{ $repeaterStats['new_visitors'] ?? 0 }},
                        {{ $repeaterStats['returning_visitors'] ?? 0 }}
                    ],
                    backgroundColor: ['#36A2EB', '#FF6384']
                }]
            }
        });
    }
    @endif
</script>
@endpush

