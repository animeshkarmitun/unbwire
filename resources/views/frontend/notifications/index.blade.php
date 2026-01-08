@extends('frontend.layouts.master')

@section('title', 'My Notifications')

@section('content')
<section class="pb-80">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Breadcrumb -->
                <ul class="breadcrumbs bg-light mb-4">
                    <li class="breadcrumbs__item">
                        <a href="{{ url('/') }}" class="breadcrumbs__url">
                            <i class="fa fa-home"></i> {{ __('frontend.Home') }}</a>
                    </li>
                    <li class="breadcrumbs__item">
                        <a href="javascript:;" class="breadcrumbs__url">Notifications</a>
                    </li>
                </ul>
                <!-- End breadcrumb -->

                <div class="wrap__about-us">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>My Notifications 
                            @if($unreadCount > 0)
                                <span class="badge bg-primary">{{ $unreadCount }} unread</span>
                            @endif
                        </h2>
                        @if($unreadCount > 0)
                            <button type="button" class="btn btn-secondary" id="markAllReadBtn">
                                <i class="fas fa-check-double"></i> Mark All as Read
                            </button>
                        @endif
                    </div>

                    <!-- Filters -->
                    <div class="mb-4">
                        <div class="btn-group" role="group">
                            <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
                               class="btn {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                                All
                            </a>
                            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
                               class="btn {{ $filter === 'unread' ? 'btn-primary' : 'btn-outline-primary' }}">
                                Unread
                            </a>
                            <a href="{{ route('notifications.index', ['filter' => 'read']) }}" 
                               class="btn {{ $filter === 'read' ? 'btn-primary' : 'btn-outline-primary' }}">
                                Read
                            </a>
                        </div>
                    </div>

                    <!-- Notifications List -->
                    @if($notifications->count() > 0)
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                <div class="list-group-item list-group-item-action {{ !$notification->is_read ? 'list-group-item-primary' : '' }}" 
                                     style="cursor: pointer;"
                                     onclick="window.location.href='{{ route('notifications.view', $notification->id) }}'">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-primary me-2">New</span>
                                                @endif
                                                <h5 class="mb-1">{{ $notification->title }}</h5>
                                            </div>
                                            @if($notification->news)
                                                <div class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-folder"></i> {{ $notification->news->category->name ?? 'News' }} | 
                                                        <i class="fas fa-user"></i> {{ $notification->news->auther->name ?? 'Admin' }} | 
                                                        <i class="fas fa-calendar"></i> {{ $notification->created_at->format('M d, Y H:i') }}
                                                    </small>
                                                </div>
                                                @if($notification->message)
                                                    <p class="mb-1">{{ Str::limit($notification->message, 150) }}</p>
                                                @endif
                                            @else
                                                <p class="mb-1 text-muted">News article no longer available</p>
                                            @endif
                                        </div>
                                        <div class="ms-3">
                                            @if($notification->news && $notification->news->image)
                                                <img src="{{ asset($notification->news->image) }}" 
                                                     alt="{{ $notification->title }}" 
                                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;"
                                                     onerror="this.onerror=null; this.src='{{ asset('frontend/assets/images/placeholder.webp') }}';">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No notifications found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (confirm('Mark all notifications as read?')) {
                fetch('{{ route("notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        });
    }
});
</script>
@endsection
