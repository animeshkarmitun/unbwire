@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Most Visited IPs</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></div>
            <div class="breadcrumb-item active">Most Visited IPs</div>
        </div>
    </div>
    
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>IP Address Analytics</h4>
                        <div class="card-header-action">
                            <form method="GET" action="{{ route('admin.analytics.most-visited-ips') }}" class="form-inline d-flex align-items-center">
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
                            <table class="table table-striped" id="ips-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>IP Address</th>
                                        <th>Location</th>
                                        <th>Visits</th>
                                        <th>Page Views</th>
                                        <th>First Visit</th>
                                        <th>Last Visit</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ips as $index => $ip)
                                    <tr data-ip="{{ $ip['ip_address'] }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <code>{{ $ip['ip_address'] }}</code>
                                        </td>
                                        <td>
                                            @if($ip['country'])
                                                <i class="fas fa-globe"></i> {{ $ip['country'] }}
                                                @if($ip['city'])
                                                    <br><small class="text-muted">{{ $ip['city'] }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $ip['visit_count'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $ip['page_views'] }}</span>
                                        </td>
                                        <td>
                                            @if($ip['first_visit'])
                                                <small>{{ \Carbon\Carbon::parse($ip['first_visit'])->format('M d, Y H:i') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ip['last_visit'])
                                                <small>{{ \Carbon\Carbon::parse($ip['last_visit'])->format('M d, Y H:i') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ip['is_blocked'])
                                                <span class="badge badge-danger">Blocked</span>
                                            @else
                                                <span class="badge badge-success">Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ip['is_blocked'])
                                                <button class="btn btn-sm btn-success unblock-ip-btn" 
                                                        data-ip="{{ $ip['ip_address'] }}"
                                                        title="Unblock IP">
                                                    <i class="fas fa-unlock"></i> Unblock
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-danger block-ip-btn" 
                                                        data-ip="{{ $ip['ip_address'] }}"
                                                        title="Block IP">
                                                    <i class="fas fa-ban"></i> Block
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No IP addresses found for the selected period.</td>
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
                        <textarea class="form-control" id="block_reason" rows="3" placeholder="Enter reason for blocking this IP address..."></textarea>
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
        $('#block_reason').val('');
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

    // Unblock IP
    $(document).on('click', '.unblock-ip-btn', function() {
        const ip = $(this).data('ip');
        
        if (!confirm('Are you sure you want to unblock this IP address?')) {
            return;
        }
        
        $.ajax({
            url: '{{ route("admin.analytics.unblock-ip") }}',
            method: 'POST',
            data: {
                ip_address: ip,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to unblock IP address';
                toastr.error(message);
            }
        });
    });
});
</script>
@endpush

