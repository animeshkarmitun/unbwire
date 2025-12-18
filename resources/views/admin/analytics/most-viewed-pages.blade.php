@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Most Viewed Pages</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></div>
            <div class="breadcrumb-item active">Most Viewed Pages</div>
        </div>
    </div>
    
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Page View Analytics</h4>
                        <div class="card-header-action">
                            <form method="GET" action="{{ route('admin.analytics.most-viewed-pages') }}" class="form-inline d-flex align-items-center">
                                <div class="form-group mb-0 mr-2">
                                    <select name="period" class="form-control" style="height: 38px; border-radius: 0.25rem;">
                                        <option value="all" {{ $period == 'all' ? 'selected' : '' }}>All Time</option>
                                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>This Year</option>
                                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                                        <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today</option>
                                    </select>
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
                            <table class="table table-striped" id="pages-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Page Title</th>
                                        <th>Path</th>
                                        <th>Views</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pages as $index => $page)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $page['title'] }}</strong>
                                        </td>
                                        <td>
                                            <code class="text-primary">{{ $page['path'] }}</code>
                                        </td>
                                        <td>
                                            <span class="badge badge-success badge-lg">{{ number_format($page['view_count']) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ $page['url'] }}" target="_blank" class="btn btn-sm btn-info" title="View Page">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="empty-state" data-height="200">
                                                <div class="empty-state-icon">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <h2>No Page Views Found</h2>
                                                <p class="lead">
                                                    There are no page views recorded for the selected period.
                                                </p>
                                            </div>
                                        </td>
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
<script>
    $(document).ready(function() {
        $('#pages-table').DataTable({
            "order": [[3, "desc"]], // Sort by view count descending
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]]
        });
    });
</script>
@endpush

