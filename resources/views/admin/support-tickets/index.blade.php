@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Support Tickets') }}</h1>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ __('Open Tickets') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['total_open'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-spinner"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ __('In Progress') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['total_in_progress'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ __('Overdue') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['overdue'] }}
                        </div>
                    </div>
                </div>
            </div>
            @if(isset($stats['due_soon']) && $stats['due_soon'] > 0)
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ __('Due Soon') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['due_soon'] }}
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ __('Unassigned') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['unassigned'] }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('All Tickets') }}</h4>
                <div class="card-header-action">
                    <a href="{{ route('admin.support-tickets.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Create New Ticket') }}
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="{{ route('admin.support-tickets.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('Status') }}</label>
                                <select name="status" class="form-control">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                                    <option value="waiting_customer" {{ request('status') == 'waiting_customer' ? 'selected' : '' }}>{{ __('Waiting Customer') }}</option>
                                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>{{ __('Resolved') }}</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('Priority') }}</label>
                                <select name="priority" class="form-control">
                                    <option value="">{{ __('All Priorities') }}</option>
                                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>{{ __('Urgent') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('Category') }}</label>
                                <select name="category" class="form-control">
                                    <option value="">{{ __('All Categories') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{ __('Tag') }}</label>
                                <select name="tag" class="form-control">
                                    <option value="">{{ __('All Tags') }}</option>
                                    @foreach(\App\Models\SupportTicketTag::all() as $tag)
                                        <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{ __('Assigned To') }}</label>
                                <select name="assigned_to" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="unassigned" {{ request('assigned_to') == 'unassigned' ? 'selected' : '' }}>{{ __('Unassigned') }}</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ request('assigned_to') == $admin->id ? 'selected' : '' }}>
                                            {{ $admin->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('Search') }}</label>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="{{ __('Ticket number, subject, email...') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> {{ __('Filter') }}</button>
                                    <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <a href="{{ route('admin.support-ticket-tags.index') }}" class="btn btn-info">
                                        <i class="fas fa-tags"></i> {{ __('Manage Tags') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Ticket #') }}</th>
                                <th>{{ __('Subject') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Priority') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Assigned To') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td>
                                        <strong>{{ $ticket->ticket_number }}</strong>
                                        @if($ticket->isOverdue())
                                            <span class="badge badge-danger ml-1" title="{{ __('Overdue') }}"><i class="fas fa-exclamation-triangle"></i></span>
                                        @elseif($ticket->isDueSoon())
                                            <span class="badge badge-warning ml-1" title="{{ __('Due Soon') }}"><i class="fas fa-clock"></i></span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($ticket->subject, 50) }}</td>
                                    <td>
                                        @if($ticket->user)
                                            {{ $ticket->user->name }}
                                        @else
                                            <span class="text-muted">{{ $ticket->email }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $ticket->category->color ?? '#6c757d' }}">
                                            {{ $ticket->category->name }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $priorityColors = [
                                                'low' => 'secondary',
                                                'medium' => 'info',
                                                'high' => 'warning',
                                                'urgent' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $priorityColors[$ticket->priority] ?? 'secondary' }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td>
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
                                    </td>
                                    <td>
                                        @if($ticket->admin)
                                            {{ $ticket->admin->name }}
                                        @else
                                            <span class="text-muted">{{ __('Unassigned') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.support-tickets.show', $ticket->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('support tickets update,admin')
                                            <a href="{{ route('admin.support-tickets.edit', $ticket->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('support tickets delete,admin')
                                            <a href="{{ route('admin.support-tickets.destroy', $ticket->id) }}" class="btn btn-danger btn-sm delete-item">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">{{ __('No tickets found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
