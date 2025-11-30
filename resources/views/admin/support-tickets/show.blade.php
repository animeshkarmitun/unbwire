@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Ticket Details') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('admin.support-tickets.index') }}">{{ __('Support Tickets') }}</a></div>
                <div class="breadcrumb-item active">{{ $ticket->ticket_number }}</div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Ticket Info & Conversation -->
            <div class="col-lg-8">
                <!-- Ticket Header Card -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>
                            <strong>{{ $ticket->ticket_number }}</strong> - {{ $ticket->subject }}
                            @if($ticket->isOverdue())
                                <span class="badge badge-danger ml-2"><i class="fas fa-exclamation-triangle"></i> {{ __('Overdue') }}</span>
                            @elseif($ticket->isDueSoon())
                                <span class="badge badge-warning ml-2"><i class="fas fa-clock"></i> {{ __('Due Soon') }}</span>
                            @endif
                        </h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Ticket Meta Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="40%">{{ __('Status') }}:</th>
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
                                    </tr>
                                    <tr>
                                        <th>{{ __('Priority') }}:</th>
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
                                    </tr>
                                    <tr>
                                        <th>{{ __('Category') }}:</th>
                                        <td>
                                            <span class="badge" style="background-color: {{ $ticket->category->color ?? '#6c757d' }}">
                                                {{ $ticket->category->name }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Created') }}:</th>
                                        <td>{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="40%">{{ __('User') }}:</th>
                                        <td>
                                            @if($ticket->user)
                                                {{ $ticket->user->name }}<br>
                                                <small class="text-muted">{{ $ticket->user->email }}</small>
                                            @else
                                                <span class="text-muted">{{ $ticket->email }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Assigned To') }}:</th>
                                        <td>
                                            @if($ticket->admin)
                                                {{ $ticket->admin->name }}
                                            @else
                                                <span class="text-muted">{{ __('Unassigned') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($ticket->sla_due_at)
                                    <tr>
                                        <th>{{ __('SLA Due') }}:</th>
                                        <td>
                                            {{ $ticket->sla_due_at->format('M d, Y H:i') }}
                                            @if($ticket->isOverdue())
                                                <span class="text-danger">({{ __('Overdue') }})</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @if($ticket->resolved_at)
                                    <tr>
                                        <th>{{ __('Resolved') }}:</th>
                                        <td>{{ $ticket->resolved_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    @endif
                                    @if($ticket->satisfaction_rating)
                                    <tr>
                                        <th>{{ __('Rating') }}:</th>
                                        <td>
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $ticket->satisfaction_rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                            <span class="ml-2">({{ $ticket->satisfaction_rating }}/5)</span>
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <!-- Ticket Description -->
                        <div class="form-group">
                            <label><strong>{{ __('Description') }}</strong></label>
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($ticket->description)) !!}
                            </div>
                        </div>

                        <!-- Initial Attachments -->
                        @if($ticket->attachments->where('reply_id', null)->count() > 0)
                            <div class="form-group">
                                <label><strong>{{ __('Attachments') }}</strong></label>
                                <div class="row">
                                    @foreach($ticket->attachments->where('reply_id', null) as $attachment)
                                        <div class="col-md-4 mb-2">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <i class="fas fa-file"></i>
                                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="ml-2">
                                                        {{ Str::limit($attachment->original_name, 30) }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $attachment->human_readable_size }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Conversation Thread -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ __('Conversation') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($replies as $reply)
                                <div class="timeline-item mb-4">
                                    <div class="timeline-marker {{ $reply->isFromAdmin() ? 'bg-primary' : 'bg-success' }}"></div>
                                    <div class="timeline-content">
                                        <div class="card {{ $reply->isFromAdmin() ? 'border-primary' : 'border-success' }}">
                                            <div class="card-header">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>
                                                            @if($reply->isFromAdmin())
                                                                {{ $reply->admin->name ?? __('Admin') }}
                                                                @if($reply->is_internal)
                                                                    <span class="badge badge-warning ml-2">{{ __('Internal Note') }}</span>
                                                                @endif
                                                            @else
                                                                {{ $reply->user->name ?? __('User') }}
                                                            @endif
                                                        </strong>
                                                        <small class="text-muted ml-2">{{ $reply->created_at->format('M d, Y H:i') }}</small>
                                                    </div>
                                                    @if($reply->is_automated)
                                                        <span class="badge badge-info">{{ __('Automated') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p>{!! nl2br(e($reply->message)) !!}</p>
                                                
                                                <!-- Reply Attachments -->
                                                @if($reply->replyAttachments->count() > 0)
                                                    <div class="mt-3">
                                                        <strong>{{ __('Attachments') }}:</strong>
                                                        <div class="row mt-2">
                                                            @foreach($reply->replyAttachments as $attachment)
                                                                <div class="col-md-4 mb-2">
                                                                    <div class="card">
                                                                        <div class="card-body p-2">
                                                                            <i class="fas fa-file"></i>
                                                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="ml-2">
                                                                                {{ Str::limit($attachment->original_name, 30) }}
                                                                            </a>
                                                                            <br>
                                                                            <small class="text-muted">{{ $attachment->human_readable_size }}</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Add Reply Form -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ __('Add Reply') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.support-tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('Message') }}</label>
                                <textarea name="message" class="form-control" rows="5" required></textarea>
                                @error('message')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>{{ __('Attachments') }} ({{ __('Optional') }})</label>
                                <input type="file" name="attachments[]" class="form-control" multiple>
                                <small class="text-muted">{{ __('You can select multiple files') }}</small>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="is_internal" value="1" class="custom-control-input" id="is_internal">
                                    <label class="custom-control-label" for="is_internal">
                                        {{ __('Internal Note') }} ({{ __('Only visible to admins') }})
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> {{ __('Send Reply') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Actions & Info -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ __('Quick Actions') }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- Assign Ticket -->
                        <div class="form-group">
                            <label>{{ __('Assign To') }}</label>
                            <form action="{{ route('admin.support-tickets.assign', $ticket->id) }}" method="POST" id="assignForm">
                                @csrf
                                <select name="admin_id" class="form-control select2" onchange="document.getElementById('assignForm').submit();">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ $ticket->admin_id == $admin->id ? 'selected' : '' }}>
                                            {{ $admin->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>

                        <!-- Change Status -->
                        <div class="form-group">
                            <label>{{ __('Change Status') }}</label>
                            <form action="{{ route('admin.support-tickets.update', $ticket->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="subject" value="{{ $ticket->subject }}">
                                <input type="hidden" name="category_id" value="{{ $ticket->category_id }}">
                                <input type="hidden" name="priority" value="{{ $ticket->priority }}">
                                <input type="hidden" name="admin_id" value="{{ $ticket->admin_id }}">
                                <select name="status" class="form-control" onchange="this.form.submit();">
                                    <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                                    <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                                    <option value="waiting_customer" {{ $ticket->status == 'waiting_customer' ? 'selected' : '' }}>{{ __('Waiting Customer') }}</option>
                                    <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>{{ __('Resolved') }}</option>
                                    <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                    <option value="cancelled" {{ $ticket->status == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                </select>
                            </form>
                        </div>

                        <!-- Change Priority -->
                        <div class="form-group">
                            <label>{{ __('Change Priority') }}</label>
                            <form action="{{ route('admin.support-tickets.update', $ticket->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="subject" value="{{ $ticket->subject }}">
                                <input type="hidden" name="category_id" value="{{ $ticket->category_id }}">
                                <input type="hidden" name="status" value="{{ $ticket->status }}">
                                <input type="hidden" name="admin_id" value="{{ $ticket->admin_id }}">
                                <select name="priority" class="form-control" onchange="this.form.submit();">
                                    <option value="low" {{ $ticket->priority == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                                    <option value="medium" {{ $ticket->priority == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                    <option value="high" {{ $ticket->priority == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                                    <option value="urgent" {{ $ticket->priority == 'urgent' ? 'selected' : '' }}>{{ __('Urgent') }}</option>
                                </select>
                            </form>
                        </div>

                        <hr>

                        <div class="form-group">
                            <a href="{{ route('admin.support-tickets.edit', $ticket->id) }}" class="btn btn-info btn-block">
                                <i class="fas fa-edit"></i> {{ __('Edit Ticket') }}
                            </a>
                        </div>

                        @can('support tickets delete,admin')
                        <div class="form-group">
                            <form action="{{ route('admin.support-tickets.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this ticket?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash"></i> {{ __('Delete Ticket') }}
                                </button>
                            </form>
                        </div>
                        @endcan
                    </div>
                </div>

                <!-- Activity Log -->
                @if($ticket->activities->count() > 0)
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ __('Activity Log') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="activity">
                            @foreach($ticket->activities->take(10) as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon bg-primary text-white shadow-primary">
                                        <i class="fas fa-circle"></i>
                                    </div>
                                    <div class="activity-detail">
                                        <div class="mb-2">
                                            <span class="text-job text-primary">{{ $activity->created_at->format('M d, Y H:i') }}</span>
                                        </div>
                                        <p class="text-job">{{ $activity->activity_description }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Tags -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ __('Tags') }}</h4>
                    </div>
                    <div class="card-body">
                        @if($ticket->tags->count() > 0)
                            @foreach($ticket->tags as $tag)
                                <span class="badge badge-secondary mr-1 mb-1" style="background-color: {{ $tag->color ?? '#6c757d' }}">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        @else
                            <p class="text-muted">{{ __('No tags assigned') }}</p>
                        @endif
                        <div class="mt-2">
                            <a href="{{ route('admin.support-ticket-tags.index') }}" class="btn btn-sm btn-info">
                                <i class="fas fa-tags"></i> {{ __('Manage Tags') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Satisfaction Rating -->
                @if($ticket->satisfaction_rating)
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ __('Customer Satisfaction') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>{{ __('Rating') }}:</strong>
                            <div class="mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $ticket->satisfaction_rating)
                                        <i class="fas fa-star text-warning" style="font-size: 24px;"></i>
                                    @else
                                        <i class="far fa-star text-muted" style="font-size: 24px;"></i>
                                    @endif
                                @endfor
                                <span class="ml-2"><strong>{{ $ticket->satisfaction_rating }}/5</strong></span>
                            </div>
                        </div>
                        @if($ticket->satisfaction_feedback)
                            <div>
                                <strong>{{ __('Feedback') }}:</strong>
                                <p class="mt-2 p-3 bg-light rounded">{{ $ticket->satisfaction_feedback }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Auto-submit forms on change
        document.querySelectorAll('select[onchange*="submit"]').forEach(function(select) {
            select.addEventListener('change', function() {
                // Show loading indicator
                const form = this.closest('form');
                const originalText = form.querySelector('button[type="submit"]')?.innerHTML;
                if (form.querySelector('button[type="submit"]')) {
                    form.querySelector('button[type="submit"]').innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('Updating...') }}';
                }
            });
        });
    </script>
@endpush

