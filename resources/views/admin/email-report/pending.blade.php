@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Pending Email Report') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ route('admin.email-report.index') }}" class="btn btn-primary">
                        <i class="fas fa-envelope"></i> View All Emails
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Pending</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['total_pending'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Pending Today</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['today_pending'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Oldest Pending</h4>
                        </div>
                        <div class="card-body">
                            @if($stats['oldest_pending'])
                                {{ $stats['oldest_pending']->created_at->diffForHumans() }}
                            @else
                                None
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($stats['oldest_pending'])
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Oldest Pending Email:</strong> 
                Created {{ $stats['oldest_pending']->created_at->format('M d, Y H:i') }} 
                ({{ $stats['oldest_pending']->created_at->diffForHumans() }})
                for news: <strong>{{ $stats['oldest_pending']->news->title ?? 'N/A' }}</strong>
            </div>
        @endif

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('Pending Email Notifications') }}</h4>
            </div>
            <div class="card-body">
                <!-- Search and Filters -->
                <form method="GET" action="{{ route('admin.email-report.pending') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>News Article</label>
                                <select name="news_id" class="form-control">
                                    <option value="">All News</option>
                                    @foreach($recentNews as $news)
                                        <option value="{{ $news->id }}" {{ request('news_id') == $news->id ? 'selected' : '' }}>
                                            {{ Str::limit($news->title, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.email-report.pending') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>News Title</th>
                                <th>Subscriber Email</th>
                                <th>Subscriber Name</th>
                                <th>Notification Created</th>
                                <th>Pending Since</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $notification)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.news.edit', $notification->news_id) }}" target="_blank">
                                            {{ Str::limit($notification->news->title ?? 'N/A', 50) }}
                                        </a>
                                    </td>
                                    <td>{{ $notification->user->email ?? 'N/A' }}</td>
                                    <td>{{ $notification->user->name ?? 'N/A' }}</td>
                                    <td>{{ $notification->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-warning">{{ $notification->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.news.edit', $notification->news_id) }}" 
                                           class="btn btn-sm btn-info" target="_blank">
                                            <i class="fas fa-eye"></i> View News
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No pending email notifications found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
