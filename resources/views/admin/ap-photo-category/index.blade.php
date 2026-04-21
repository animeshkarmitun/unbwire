@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>AP Photo {{ __('admin.Categories') }}</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>All AP Photo Categories</h4>
                <div class="card-header-action">
                    @if (canAccess(['ap photo category create', 'admin']))
                        <a href="{{ route('admin.ap-photo-category.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create New
                        </a>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="category-table">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>{{ __('admin.Name') }}</th>
                                <th>{{ __('Parent Category') }}</th>
                                <th>Order</th>
                                <th>{{ __('admin.Status') }}</th>
                                <th>{{ __('admin.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>
                                        @if($category->parent_id)
                                            <i class="fas fa-level-up-alt text-muted mr-1" style="transform: rotate(90deg);"></i>
                                        @endif
                                        {{ $category->name }}
                                    </td>
                                    <td>
                                        @if($category->parent)
                                            <span class="badge badge-secondary">{{ $category->parent->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->order ?? 0 }}</td>
                                    <td>
                                        @if ($category->status == 1)
                                            <span class="badge badge-success">{{ __('admin.Active') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('admin.Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.ap-photo-category.edit', $category->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route('admin.ap-photo-category.destroy', $category->id) }}" class="btn btn-danger delete-item"><i class="fas fa-trash-alt"></i></a>
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
        $("#category-table").dataTable();
    </script>
@endpush
