@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>AP Photos</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>All AP Photos</h4>
                <div class="card-header-action">
                    <a href="{{ route('admin.ap-photo.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Upload Photo
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-photos">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Image</th>
                                <th>Category</th>
                                <th>Subcategory</th>
                                <th>Photos Count</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($photos as $photo)
                                <tr>
                                    <td>{{ $photo->id }}</td>
                                    <td>
                                        @if($photo->items->first())
                                            <img src="{{ $photo->items->first()->file_url }}" width="100px" class="img-thumbnail" alt="">
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>{{ $photo->category->name ?? '-' }}</td>
                                    <td>{{ $photo->subCategory->name ?? '-' }}</td>
                                    <td><span class="badge badge-info">{{ $photo->items->count() }}</span></td>
                                    <td>
                                        @if($photo->status)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.ap-photo.edit', $photo->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route('admin.ap-photo.destroy', $photo->id) }}" class="btn btn-danger delete-item"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $photos->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $("#table-photos").dataTable({
            "paging": false,
            "info": false
        });
    </script>
@endpush
