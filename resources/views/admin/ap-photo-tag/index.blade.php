@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>AP Photo Tags</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>All AP Photo Tags</h4>
                <div class="card-header-action">
                    <a href="{{ route('admin.ap-photo-tag.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Tag
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-tags">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tags as $tag)
                                <tr>
                                    <td>{{ $tag->id }}</td>
                                    <td>{{ $tag->name }}</td>
                                    <td>{{ $tag->slug }}</td>
                                    <td>
                                        <a href="{{ route('admin.ap-photo-tag.edit', $tag->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route('admin.ap-photo-tag.destroy', $tag->id) }}" class="btn btn-danger delete-item"><i class="fas fa-trash-alt"></i></a>
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
        $("#table-tags").dataTable();
    </script>
@endpush
