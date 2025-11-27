@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Video Gallery</h1>
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h4>All Video Galleries</h4>
            <div class="card-header-action">
                <a href="{{ route('admin.video-gallery.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Videos
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Search</label>
                        <form method="GET" action="{{ route('admin.video-gallery.index') }}">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       value="{{ request('search') }}" 
                                       placeholder="Search videos...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Source</label>
                        <select class="form-control" onchange="window.location.href=this.value">
                            <option value="{{ route('admin.video-gallery.index') }}">All Sources</option>
                            <option value="{{ route('admin.video-gallery.index', ['source' => 'media']) }}" 
                                    {{ request('source') == 'media' ? 'selected' : '' }}>Media Library</option>
                            <option value="{{ route('admin.video-gallery.index', ['source' => 'external']) }}" 
                                    {{ request('source') == 'external' ? 'selected' : '' }}>External</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Gallery Group</label>
                        <select class="form-control" onchange="window.location.href=this.value">
                            <option value="{{ route('admin.video-gallery.index') }}">All Groups</option>
                            @foreach($gallerySlugs as $slug)
                                <option value="{{ route('admin.video-gallery.index', ['gallery_slug' => $slug]) }}" 
                                        {{ request('gallery_slug') == $slug ? 'selected' : '' }}>
                                    {{ $slug }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" onchange="window.location.href=this.value">
                            <option value="{{ route('admin.video-gallery.index') }}">All Status</option>
                            <option value="{{ route('admin.video-gallery.index', ['status' => 1]) }}" 
                                    {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="{{ route('admin.video-gallery.index', ['status' => 0]) }}" 
                                    {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Video Gallery Grid -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Video</th>
                            <th>Title</th>
                            <th>Source</th>
                            <th>Platform</th>
                            <th>Gallery Group</th>
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
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 120px; height: 80px; border-radius: 4px;">
                                            <i class="fas fa-video fa-2x text-primary"></i>
                                        </div>
                                    @elseif($gallery->video_url)
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 120px; height: 80px; border-radius: 4px;">
                                            <i class="fab fa-{{ $gallery->video_platform ?? 'youtube' }} fa-2x text-danger"></i>
                                        </div>
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 120px; height: 80px; border-radius: 4px;">
                                            <i class="fas fa-video text-muted"></i>
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
                                    @if($gallery->isFromMediaLibrary())
                                        <span class="badge badge-success">Media Library</span>
                                    @else
                                        <span class="badge badge-info">External</span>
                                    @endif
                                </td>
                                <td>
                                    @if($gallery->video_platform)
                                        <span class="badge badge-secondary">{{ ucfirst($gallery->video_platform) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $gallery->gallery_slug ?: 'Ungrouped' }}</span>
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
                                        <a href="{{ route('admin.video-gallery.edit', $gallery->id) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.video-gallery.destroy', $gallery->id) }}" 
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
                                <td colspan="8" class="text-center">
                                    <p class="text-muted">No video galleries found.</p>
                                    <a href="{{ route('admin.video-gallery.create') }}" class="btn btn-primary">
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

