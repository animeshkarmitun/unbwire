@extends('frontend.layouts.master')

@section('title', 'My Support Tickets')

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
                        <a href="{{ route('user.profile') }}" class="breadcrumbs__url">My Profile</a>
                    </li>
                    <li class="breadcrumbs__item">
                        <a href="javascript:;" class="breadcrumbs__url">Support Tickets</a>
                    </li>
                </ul>
                <!-- End breadcrumb -->

                <div class="wrap__about-us">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>My Support Tickets</h2>
                        <a href="{{ route('support-tickets.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create New Ticket
                        </a>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-primary">{{ $stats['total'] }}</h3>
                                    <p class="mb-0">Total Tickets</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-info">{{ $stats['open'] }}</h3>
                                    <p class="mb-0">Open</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-warning">{{ $stats['in_progress'] }}</h3>
                                    <p class="mb-0">In Progress</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-success">{{ $stats['resolved'] }}</h3>
                                    <p class="mb-0">Resolved</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('support-tickets.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="category" class="form-control">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Search</label>
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Ticket number, subject...">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                            <a href="{{ route('support-tickets.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tickets List -->
                    <div class="card">
                        <div class="card-body">
                            @forelse($tickets as $ticket)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h5>
                                                    <a href="{{ route('support-tickets.show', $ticket->id) }}">
                                                        {{ $ticket->ticket_number }} - {{ $ticket->subject }}
                                                    </a>
                                                </h5>
                                                <p class="text-muted mb-2">{{ Str::limit($ticket->description, 100) }}</p>
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="badge" style="background-color: {{ $ticket->category->color ?? '#6c757d' }}">
                                                        {{ $ticket->category->name }}
                                                    </span>
                                                    @php
                                                        $statusColors = [
                                                            'open' => 'primary',
                                                            'in_progress' => 'info',
                                                            'waiting_customer' => 'warning',
                                                            'resolved' => 'success',
                                                            'closed' => 'secondary',
                                                            'cancelled' => 'danger'
                                                        ];
                                                    @endphp
                                                    <span class="badge badge-{{ $statusColors[$ticket->status] ?? 'secondary' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                                    </span>
                                                    @if($ticket->admin)
                                                        <small class="text-muted">Assigned to: {{ $ticket->admin->name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <div class="mb-2">
                                                    <small class="text-muted">Created: {{ $ticket->created_at->format('M d, Y') }}</small>
                                                </div>
                                                <a href="{{ route('support-tickets.show', $ticket->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No tickets found. <a href="{{ route('support-tickets.create') }}">Create your first ticket</a></p>
                                </div>
                            @endforelse

                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $tickets->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection




































