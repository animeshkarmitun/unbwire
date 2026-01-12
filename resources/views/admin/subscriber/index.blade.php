@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('admin.Subscribers') }}</h1>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Subscribers</h4>
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
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Email Enabled</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['with_notifications'] }}
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
                            <h4>With Subscription</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['with_subscription'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Without Subscription</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['without_subscription'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('admin.All Subscribers') }}</h4>
                <div class="card-header-action">
                    <a href="{{ route('admin.subscriber.export') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> Export CSV
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filters -->
                <form method="GET" action="{{ route('admin.subscriber.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Search Email</label>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by email...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Email Notifications</label>
                                <select name="email_notifications" class="form-control">
                                    <option value="">All</option>
                                    <option value="1" {{ request('email_notifications') == '1' ? 'selected' : '' }}>Enabled</option>
                                    <option value="0" {{ request('email_notifications') == '0' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Subscription</label>
                                <select name="has_subscription" class="form-control">
                                    <option value="">All</option>
                                    <option value="1" {{ request('has_subscription') == '1' ? 'selected' : '' }}>Has Subscription</option>
                                    <option value="0" {{ request('has_subscription') == '0' ? 'selected' : '' }}>No Subscription</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Subscription</th>
                                <th>Email Notif.</th>
                                <th>Send Full News</th>
                                <th>Language</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        @if($user->activeSubscription && $user->activeSubscription->package)
                                            <span class="badge bg-success">{{ $user->activeSubscription->package->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Free</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-email-notifications" 
                                                   id="toggle-email-notifications-{{ $user->id }}" 
                                                   data-id="{{ $user->id }}"
                                                   {{ ($user->email_notifications_enabled ?? true) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="toggle-email-notifications-{{ $user->id }}">
                                                {{ ($user->email_notifications_enabled ?? true) ? 'Enabled' : 'Disabled' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-full-news" 
                                                   id="toggle-full-news-{{ $user->id }}" 
                                                   data-id="{{ $user->id }}"
                                                   {{ ($user->send_full_news_email ?? true) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="toggle-full-news-{{ $user->id }}">
                                                {{ ($user->send_full_news_email ?? true) ? 'Enabled' : 'Disabled' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ $user->language_preference ?? 'All' }}</td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if(canAccess(['subscribers update']) || canAccess(['subscription package update']))
                                            @if($user->activeSubscription)
                                                <a href="{{ route('admin.user-subscription.edit', $user->activeSubscription->id) }}" class="btn btn-primary btn-sm mr-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if(canAccess(['subscribers delete']))
                                            <button type="button" class="btn btn-danger btn-sm delete-subscriber" 
                                                    data-id="{{ $user->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle send full news email
        $('.toggle-full-news').on('change', function() {
            const userId = $(this).data('id');
            const enabled = $(this).is(':checked');
            
            $.ajax({
                url: '{{ url("admin/subscriber") }}/' + userId + '/toggle-full-news',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: enabled
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        location.reload();
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to update send full news email setting');
                }
            });
        });

        // Toggle email notifications
        $('.toggle-email-notifications').on('change', function() {
            const userId = $(this).data('id');
            const enabled = $(this).is(':checked');
            
            $.ajax({
                url: '{{ url("admin/subscriber") }}/' + userId + '/toggle-email',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: enabled
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to update email notification setting');
                }
            });
        });

        // Delete subscriber (user)
        $('.delete-subscriber').on('click', function() {
            const userId = $(this).data('id');
            
            if (confirm('Are you sure you want to delete this user? This will also delete all their notifications.')) {
                $.ajax({
                    url: '{{ url("admin/subscriber") }}/' + userId,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to delete user');
                    }
                });
            }
        });
    });
</script>
@endpush
