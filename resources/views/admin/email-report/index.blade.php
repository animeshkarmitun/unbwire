@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Email Sending Report') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ route('admin.email-report.pending') }}" class="btn btn-warning">
                        <i class="fas fa-clock"></i> View Pending Emails
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Notifications</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['total'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Sent</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['sent'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Pending</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['pending'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Sent Today</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['today_sent'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('All Email Notifications') }}</h4>
            </div>
            <div class="card-body">
                <!-- Search and Filters -->
                <form method="GET" action="{{ route('admin.email-report.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Email Status</label>
                                <select name="email_status" class="form-control">
                                    <option value="">All</option>
                                    <option value="sent" {{ request('email_status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="pending" {{ request('email_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                            <a href="{{ route('admin.email-report.index') }}" class="btn btn-secondary">
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
                                <th>Email Status</th>
                                <th>Email Sent At</th>
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
                                        @if($notification->email_sent)
                                            <span class="badge bg-success">Sent</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($notification->email_sent_at)
                                            {{ $notification->email_sent_at->format('M d, Y H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No email notifications found</td>
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
