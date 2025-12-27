@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Edit Support Ticket Tag') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('admin.support-ticket-tags.index') }}">{{ __('Tags') }}</a></div>
                <div class="breadcrumb-item active">{{ __('Edit') }}</div>
            </div>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('Edit Tag') }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.support-ticket-tags.update', $tag->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>{{ __('Tag Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $tag->name) }}" required>
                        @error('name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>{{ __('Color') }} ({{ __('Optional') }})</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="color" name="color" class="form-control" value="{{ old('color', $tag->color ?? '#6c757d') }}" style="height: 50px;">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="color_hex" class="form-control" value="{{ old('color', $tag->color ?? '#6c757d') }}" placeholder="#6c757d" pattern="^#[a-fA-F0-9]{6}$">
                                <small class="text-muted">{{ __('Or enter hex color code') }}</small>
                            </div>
                        </div>
                        @error('color')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Update Tag') }}
                        </button>
                        <a href="{{ route('admin.support-ticket-tags.index') }}" class="btn btn-secondary">
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
            // Sync color picker with hex input
            $('input[name="color"]').on('change', function() {
                $('input[name="color_hex"]').val($(this).val());
            });
            
            $('input[name="color_hex"]').on('change', function() {
                const hex = $(this).val();
                if (/^#[a-fA-F0-9]{6}$/.test(hex)) {
                    $('input[name="color"]').val(hex);
                }
            });
        });
    </script>
@endpush






































