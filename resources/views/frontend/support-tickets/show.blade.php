@extends('frontend.layouts.master')

@section('title', 'Ticket Details')

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
                        <a href="{{ route('support-tickets.index') }}" class="breadcrumbs__url">Support Tickets</a>
                    </li>
                    <li class="breadcrumbs__item">
                        <a href="javascript:;" class="breadcrumbs__url">{{ $ticket->ticket_number }}</a>
                    </li>
                </ul>
                <!-- End breadcrumb -->

                <div class="wrap__about-us">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Ticket: {{ $ticket->ticket_number }}</h2>
                        <a href="{{ route('support-tickets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>

                    <!-- Ticket Header -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>{{ $ticket->subject }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Status:</th>
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
                                            <th>Priority:</th>
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
                                            <th>Category:</th>
                                            <td>
                                                <span class="badge" style="background-color: {{ $ticket->category->color ?? '#6c757d' }}">
                                                    {{ $ticket->category->name }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Created:</th>
                                            <td>{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @if($ticket->admin)
                                        <tr>
                                            <th>Assigned To:</th>
                                            <td>{{ $ticket->admin->name }}</td>
                                        </tr>
                                        @endif
                                        @if($ticket->resolved_at)
                                        <tr>
                                            <th>Resolved:</th>
                                            <td>{{ $ticket->resolved_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label><strong>Description</strong></label>
                                <div class="p-3 bg-light rounded">
                                    {!! nl2br(e($ticket->description)) !!}
                                </div>
                            </div>

                            <!-- Initial Attachments -->
                            @if($ticket->attachments->where('reply_id', null)->count() > 0)
                                <div class="form-group">
                                    <label><strong>Attachments</strong></label>
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
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Conversation</h4>
                        </div>
                        <div class="card-body">
                            @forelse($replies as $reply)
                                <div class="card mb-3 {{ $reply->isFromAdmin() ? 'border-primary' : 'border-success' }}">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>
                                                    @if($reply->isFromAdmin())
                                                        {{ $reply->admin->name ?? 'Admin' }}
                                                    @else
                                                        {{ $reply->user->name ?? 'You' }}
                                                    @endif
                                                </strong>
                                                <small class="text-muted ml-2">{{ $reply->created_at->format('M d, Y H:i') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p>{!! nl2br(e($reply->message)) !!}</p>
                                        
                                        <!-- Reply Attachments -->
                                        @if($reply->replyAttachments->count() > 0)
                                            <div class="mt-3">
                                                <strong>Attachments:</strong>
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
                            @empty
                                <p class="text-muted text-center py-3">No replies yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Add Reply Form -->
                    @if(!in_array($ticket->status, ['closed', 'cancelled']))
                    <div class="card">
                        <div class="card-header">
                            <h4>Add Reply</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('support-tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea name="message" class="form-control" rows="5" required></textarea>
                                    @error('message')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Attachments (Optional)</label>
                                    <input type="file" name="attachments[]" class="form-control" multiple>
                                    <small class="text-muted">You can select multiple files</small>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Send Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- Satisfaction Rating (if resolved) -->
                    @if($ticket->status == 'resolved' && !$ticket->satisfaction_rating)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4>Rate Your Experience</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('support-tickets.rating', $ticket->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>How would you rate your experience?</label>
                                    <div class="rating">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" required>
                                            <label for="rating{{ $i }}" class="fas fa-star"></label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Feedback (Optional)</label>
                                    <textarea name="feedback" class="form-control" rows="3" placeholder="Tell us about your experience..."></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Submit Rating</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.rating input {
    display: none;
}
.rating label {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}
.rating label:hover,
.rating label:hover ~ label,
.rating input:checked ~ label {
    color: #ffc107;
}
</style>
@endsection

















