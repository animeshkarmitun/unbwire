@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ __('Edit User Subscription') }}</h1>
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h4>{{ __('Edit Subscription Details') }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.user-subscription.update', $subscription->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('User Name') }}</label>
                    <div class="col-sm-12 col-md-7">
                        <input type="text" name="name" class="form-control" value="{{ $subscription->user->name }}">
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Email') }}</label>
                    <div class="col-sm-12 col-md-7">
                        <input type="email" name="email" class="form-control" value="{{ $subscription->user->email }}">
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Package') }}</label>
                    <div class="col-sm-12 col-md-7">
                        <select class="form-control select2" name="package_id">
                            @foreach ($packages as $package)
                            <option value="{{ $package->id }}" {{ $subscription->subscription_package_id == $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                            @endforeach
                        </select>
                        @error('package_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Status') }}</label>
                    <div class="col-sm-12 col-md-7">
                        <select class="form-control" name="status">
                            <option value="active" {{ $subscription->status == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="expired" {{ $subscription->status == 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                            <option value="cancelled" {{ $subscription->status == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            <option value="pending" {{ $subscription->status == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        </select>
                        @error('status')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Expiry Date') }}</label>
                    <div class="col-sm-12 col-md-7">
                        <input type="text" name="expires_at" class="form-control datetimepicker" value="{{ $subscription->expires_at }}">
                        @error('expires_at')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                    <div class="col-sm-12 col-md-7">
                        <button class="btn btn-primary">{{ __('Update') }}</button>
                    </div>
                </div>
            </form>

    </div>
</section>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function() {
        if(jQuery().daterangepicker) {
            $(".datetimepicker").daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                timePicker: true,
                locale: {
                    format: 'YYYY-MM-DD HH:mm:ss'
                }
            });
        }
    });
</script>
@endpush
