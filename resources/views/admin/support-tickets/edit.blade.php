@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Edit Support Ticket') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('admin.support-tickets.index') }}">{{ __('Support Tickets') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.support-tickets.show', $ticket->id) }}">{{ $ticket->ticket_number }}</a></div>
                <div class="breadcrumb-item active">{{ __('Edit') }}</div>
            </div>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('Edit Ticket') }}: {{ $ticket->ticket_number }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.support-tickets.update', $ticket->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Category') }} <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-control select2" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $ticket->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Priority') }} <span class="text-danger">*</span></label>
                                <select name="priority" class="form-control" required>
                                    <option value="low" {{ $ticket->priority == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                                    <option value="medium" {{ $ticket->priority == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                    <option value="high" {{ $ticket->priority == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                                    <option value="urgent" {{ $ticket->priority == 'urgent' ? 'selected' : '' }}>{{ __('Urgent') }}</option>
                                </select>
                                @error('priority')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Status') }} <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                                    <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                                    <option value="waiting_customer" {{ $ticket->status == 'waiting_customer' ? 'selected' : '' }}>{{ __('Waiting Customer') }}</option>
                                    <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>{{ __('Resolved') }}</option>
                                    <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                    <option value="cancelled" {{ $ticket->status == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                </select>
                                @error('status')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Assign To') }}</label>
                                <select name="admin_id" class="form-control select2">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ $ticket->admin_id == $admin->id ? 'selected' : '' }}>
                                            {{ $admin->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('admin_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ __('Subject') }} <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject', $ticket->subject) }}" required>
                        @error('subject')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tags -->
                    <div class="form-group">
                        <label>{{ __('Tags') }} ({{ __('Optional') }})</label>
                        <select name="tags[]" class="form-control select2" multiple>
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" {{ $ticket->tags->contains($tag->id) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{ __('Select multiple tags') }}</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Update Ticket') }}
                        </button>
                        <a href="{{ route('admin.support-tickets.show', $ticket->id) }}" class="btn btn-secondary">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection







































