@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('admin.Subscription Packages') }}</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('admin.All Subscription Packages') }}</h4>
                <div class="card-header-action">
                    <a href="{{ route('admin.subscription-package.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('admin.Create new') }}
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>{{ __('admin.Name') }}</th>
                                <th>{{ __('admin.Price') }}</th>
                                <th>{{ __('admin.Billing Period') }}</th>
                                <th>{{ __('admin.Access') }}</th>
                                <th>{{ __('admin.Status') }}</th>
                                <th>{{ __('admin.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packages as $package)
                                <tr>
                                    <td>{{ $package->id }}</td>
                                    <td>
                                        <strong>{{ $package->name }}</strong>
                                        @if($package->description)
                                            <br><small class="text-muted">{{ Str::limit($package->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $package->currency }} {{ number_format($package->price, 2) }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst($package->billing_period) }}</span>
                                    </td>
                                    <td>
                                        <small>
                                            @if($package->access_news) <span class="badge badge-success">News</span> @endif
                                            @if($package->access_images) <span class="badge badge-primary">Images</span> @endif
                                            @if($package->access_videos) <span class="badge badge-warning">Videos</span> @endif
                                            @if($package->access_exclusive) <span class="badge badge-danger">Exclusive</span> @endif
                                        </small>
                                    </td>
                                    <td>
                                        @if ($package->is_active)
                                            <span class="badge badge-success">{{ __('admin.Active') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('admin.Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.subscription-package.edit', $package->id) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.subscription-package.destroy', $package->id) }}" 
                                           class="btn btn-danger delete-item">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $("#table").dataTable({
            "columnDefs": [{
                "sortable": false,
                "targets": [4, 6]
            }]
        });
    </script>
@endpush

