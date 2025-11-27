@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Image Gallery</h1>
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h4>All Image Galleries</h4>
            <div class="card-header-action">
                <a href="{{ route('admin.image-gallery.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Images
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Search</label>
                        <form method="GET" action="{{ route('admin.image-gallery.index') }}">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       value="{{ request('search') }}" 
                                       placeholder="Search galleries...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Gallery Group</label>
                        <select class="form-control" onchange="window.location.href=this.value">
                            <option value="{{ route('admin.image-gallery.index') }}">All Groups</option>
                            @foreach($gallerySlugs as $slug)
                                <option value="{{ route('admin.image-gallery.index', ['gallery_slug' => $slug]) }}" 
                                        {{ request('gallery_slug') == $slug ? 'selected' : '' }}>
                                    {{ $slug }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" onchange="window.location.href=this.value">
                            <option value="{{ route('admin.image-gallery.index') }}">All Status</option>
                            <option value="{{ route('admin.image-gallery.index', ['status' => 1]) }}" 
                                    {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="{{ route('admin.image-gallery.index', ['status' => 0]) }}" 
                                    {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Gallery Grid -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Gallery Group</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($galleries as $gallery)
                            <tr>
                                <td>
                                    @if($gallery->media)
                                        <img src="{{ $gallery->media->file_url }}" 
                                             alt="{{ $gallery->alt_text }}" 
                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px; border-radius: 4px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $gallery->title ?: 'Untitled' }}</strong>
                                    @if($gallery->caption)
                                        <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($gallery->caption, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $gallery->gallery_slug ?: 'Ungrouped' }}</span>
                                </td>
                                <td>
                                    @if($gallery->isFromMediaLibrary())
                                        <span class="badge badge-success">Media Library</span>
                                    @else
                                        <span class="badge badge-warning">External</span>
                                    @endif
                                </td>
                                <td>
                                    @if($gallery->status)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                    @if($gallery->is_exclusive)
                                        <br><span class="badge badge-warning mt-1">Exclusive</span>
                                    @endif
                                </td>
                                <td>{{ $gallery->sort_order }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.image-gallery.edit', $gallery->id) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.image-gallery.destroy', $gallery->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <p class="text-muted">No image galleries found.</p>
                                    <a href="{{ route('admin.image-gallery.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create First Gallery
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $galleries->links() }}
            </div>
        </div>
    </div>
</section>
@endsection

