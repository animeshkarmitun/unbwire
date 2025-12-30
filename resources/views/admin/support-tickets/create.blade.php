@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Create Support Ticket') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('admin.support-tickets.index') }}">{{ __('Support Tickets') }}</a></div>
                <div class="breadcrumb-item active">{{ __('Create') }}</div>
            </div>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('New Ticket') }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.support-tickets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('User') }} <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-control select2">
                                    <option value="">{{ __('Select User') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ __('Leave empty if creating for non-registered user') }}</small>
                                @error('user_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Email') }} <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                                <small class="text-muted">{{ __('Required if user is not selected') }}</small>
                                @error('email')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Phone') }} ({{ __('Optional') }})</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                                @error('phone')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Assign To') }} ({{ __('Optional') }})</label>
                                <select name="admin_id" class="form-control select2">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ old('admin_id') == $admin->id ? 'selected' : '' }}>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Category') }} <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-control select2" required>
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>{{ __('Urgent') }}</option>
                                </select>
                                @error('priority')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ __('Subject') }} <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
                        @error('subject')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>{{ __('Description') }} <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="8" required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>{{ __('Attachments') }} ({{ __('Optional') }})</label>
                        <input type="file" name="attachments[]" class="form-control" multiple>
                        <small class="text-muted">{{ __('You can select multiple files. Max size: 10MB per file') }}</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Create Ticket') }}
                        </button>
                        <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-secondary">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto-fill email when user is selected
            $('#user_id').on('change', function() {
                const userId = $(this).val();
                if (userId) {
                    // Get email from selected option
                    const selectedOption = $(this).find('option:selected');
                    const email = selectedOption.text().match(/\(([^)]+)\)/);
                    if (email && email[1]) {
                        $('#email').val(email[1]);
                    }
                }
            });
        });
    </script>
@endpush







































