@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Bot Activity</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></div>
            <div class="breadcrumb-item active">Bot Activity</div>
        </div>
    </div>
    
    <div class="section-body">
        <div class="row">
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
                                    {{ $botStats['humans'] ?? 0 }}
                                    <small class="text-muted">({{ $botStats['human_percentage'] ?? 0 }}%)</small>
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
                                    {{ $botStats['bots'] ?? 0 }}
                                    <small class="text-muted">({{ $botStats['bot_percentage'] ?? 0 }}%)</small>
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
                                    {{ $botStats['scrapers'] ?? 0 }}
                                    <small class="text-muted">({{ $botStats['scraper_percentage'] ?? 0 }}%)</small>
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
                                    {{ $botStats['total'] ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Bot & Scraper Activity</h4>
                        <div class="card-header-action">
                            <form method="GET" action="{{ route('admin.analytics.bot-activity') }}" class="form-inline d-flex align-items-center">
                                <div class="form-group mb-0 mr-2">
                                    <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}" required style="height: 38px; border-radius: 0.25rem;">
                                </div>
                                <div class="form-group mb-0 mr-2">
                                    <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}" required style="height: 38px; border-radius: 0.25rem;">
                                </div>
                                <div class="form-group mb-0 mr-2">
                                    <select name="limit" class="form-control" style="height: 38px; border-radius: 0.25rem;">
                                        <option value="25" {{ $limit == 25 ? 'selected' : '' }}>Top 25</option>
                                        <option value="50" {{ $limit == 50 ? 'selected' : '' }}>Top 50</option>
                                        <option value="100" {{ $limit == 100 ? 'selected' : '' }}>Top 100</option>
                                    </select>
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
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>IP Address</th>
                                        <th>User Agent</th>
                                        <th>Location</th>
                                        <th>Page Views</th>
                                        <th>Duration</th>
                                        <th>Started At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($botActivity as $index => $visit)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($visit->user_type === 'scraper')
                                                <span class="badge badge-danger">Scraper</span>
                                            @else
                                                <span class="badge badge-warning">Bot</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $visit->ip_address }}</code>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ Str::limit($visit->user_agent, 60) }}</small>
                                        </td>
                                        <td>
                                            @if($visit->country)
                                                <i class="fas fa-globe"></i> {{ $visit->country }}
                                                @if($visit->city)
                                                    <br><small class="text-muted">{{ $visit->city }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $visit->page_views_count }}</span>
                                        </td>
                                        <td>
                                            @if($visit->duration)
                                                {{ gmdate('H:i:s', $visit->duration) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $visit->started_at->format('M d, Y H:i:s') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.analytics.most-visited-ips', ['ip' => $visit->ip_address]) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="View IP Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!\App\Models\BlockedIp::isBlocked($visit->ip_address))
                                                <button class="btn btn-sm btn-danger block-ip-btn" 
                                                        data-ip="{{ $visit->ip_address }}"
                                                        title="Block IP">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @else
                                                <span class="badge badge-danger">Blocked</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No bot activity found for the selected period.</td>
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

<!-- Block IP Modal -->
<div class="modal fade" id="blockIpModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Block IP Address</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="blockIpForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>IP Address</label>
                        <input type="text" class="form-control" id="block_ip_address" readonly>
                    </div>
                    <div class="form-group">
                        <label>Reason (Optional)</label>
                        <textarea class="form-control" id="block_reason" rows="3" placeholder="Enter reason for blocking this IP address (e.g., Bot/Scraper activity)..." value="Bot/Scraper activity"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block IP</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Block IP
    $(document).on('click', '.block-ip-btn', function() {
        const ip = $(this).data('ip');
        $('#block_ip_address').val(ip);
        $('#block_reason').val('Bot/Scraper activity');
        $('#blockIpModal').modal('show');
    });

    $('#blockIpForm').on('submit', function(e) {
        e.preventDefault();
        
        const ip = $('#block_ip_address').val();
        const reason = $('#block_reason').val();
        
        $.ajax({
            url: '{{ route("admin.analytics.block-ip") }}',
            method: 'POST',
            data: {
                ip_address: ip,
                reason: reason,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    $('#blockIpModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to block IP address';
                toastr.error(message);
            }
        });
    });
});
</script>
@endpush

