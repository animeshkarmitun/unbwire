@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('admin.Role And Permissions') }}</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('admin.Update Role') }}</h4>

            </div>
            <div class="card-body">
                <form action="{{ route('admin.role.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="">{{__('admin.Role Name')}}</label>
                        <input type="text" class="form-control" name="role" value="{{ $role->name }}">
                        @error('role')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('admin.Permissions') }}</label>
                    </div>
                    <hr>
                    @foreach ($premissions as $groupName => $premission)
                    <div class="form-group mb-4">
                        <h5 class="text-primary mb-3 pb-2 border-bottom">
                            <i class="fas fa-folder-open mr-2"></i> {{ $groupName }}
                        </h5>
                        <div class="row">
                            @foreach ($premission as $item)
                            <div class="col-md-3 col-sm-4 col-6 mb-3">
                                <div class="card border shadow-sm permission-card" style="transition: all 0.3s;">
                                    <div class="card-body p-3">
                                        <label class="custom-switch mb-0 d-flex align-items-center justify-content-between" style="cursor: pointer;">
                                            <span class="custom-switch-description mb-0" style="font-size: 0.9rem; color: #495057; font-weight: 500;">
                                                {{ $item->name }}
                                            </span>
                                            <input
                                            {{ in_array($item->name, $rolesPermissions) ? 'checked' : '' }}
                                            value="{{ $item->name }}" type="checkbox" name="permissions[]" class="custom-switch-input">
                                            <span class="custom-switch-indicator ml-2"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @if (!$loop->last)
                    <hr class="my-4">
                    @endif
                    @endforeach

                    <button type="submit" class="btn btn-primary">{{ __('admin.Update') }}</button>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .permission-card {
        cursor: pointer;
    }
    .permission-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
        border-color: #6777ef !important;
    }
    .permission-card .custom-switch-input:checked ~ .custom-switch-indicator {
        background: #6777ef;
    }
</style>
@endpush
