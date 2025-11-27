@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Edit Video Gallery</h1>
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h4>Update Video Gallery</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.video-gallery.update', $gallery->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Source Type Selection -->
                <div class="form-group">
                    <label>Video Source <span class="text-danger">*</span></label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="source_type" 
                               id="source_media" value="media" 
                               {{ $gallery->isFromMediaLibrary() ? 'checked' : '' }} 
                               onchange="toggleSourceType()">
                        <label class="form-check-label" for="source_media">
                            <i class="fas fa-database"></i> Media Library
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="source_type" 
                               id="source_external" value="external" 
                               {{ $gallery->isExternalVideo() ? 'checked' : '' }} 
                               onchange="toggleSourceType()">
                        <label class="form-check-label" for="source_external">
                            <i class="fas fa-link"></i> External Link
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <!-- Media Library Selection -->
                        <div id="mediaSourceSection" style="display: {{ $gallery->isFromMediaLibrary() ? 'block' : 'none' }};">
                            <div class="form-group">
                                <label>Select Video from Media Library</label>
                                @if($gallery->media)
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center justify-content-center bg-light p-3" 
                                             style="border-radius: 4px;">
                                            <i class="fas fa-video fa-3x text-primary"></i>
                                        </div>
                                        <p class="text-center mt-2"><strong>{{ $gallery->media->title }}</strong></p>
                                    </div>
                                @endif
                                <div class="input-group">
                                    <input type="hidden" name="media_id" id="selectedMediaId" value="{{ $gallery->media_id }}">
                                    <input type="text" class="form-control" id="selectedMediaName" 
                                           value="{{ $gallery->media ? $gallery->media->title : 'No video selected' }}" 
                                           readonly>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" 
                                                data-toggle="modal" 
                                                data-target="#mediaLibraryModal" 
                                                data-type="video">
                                            <i class="fas fa-video"></i> Select Video
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- External Video URL -->
                        <div id="externalSourceSection" style="display: {{ $gallery->isExternalVideo() ? 'block' : 'none' }};">
                            <div class="form-group">
                                <label>Video URL</label>
                                @if($gallery->video_url)
                                    <div class="mb-3">
                                        <div class="alert alert-info">
                                            <strong>Current:</strong> 
                                            <a href="{{ $gallery->video_url }}" target="_blank">{{ $gallery->video_url }}</a>
                                            @if($gallery->video_platform)
                                                <br><small>Platform: {{ ucfirst($gallery->video_platform) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <input type="url" name="video_url" class="form-control" 
                                       value="{{ old('video_url', $gallery->video_url) }}" 
                                       placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                                <small class="form-text text-muted">
                                    Supported: YouTube, Vimeo, Facebook, etc.
                                </small>
                            </div>
                        </div>

                        <!-- Gallery Group -->
                        <div class="form-group">
                            <label>Gallery Group</label>
                            <input type="text" name="gallery_slug" class="form-control" 
                                   value="{{ old('gallery_slug', $gallery->gallery_slug) }}" 
                                   placeholder="e.g., featured-videos, home-videos">
                        </div>

                        <!-- Title -->
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" 
                                   value="{{ old('title', $gallery->title) }}" 
                                   placeholder="Video title">
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3" 
                                      placeholder="Video description">{{ old('description', $gallery->description) }}</textarea>
                        </div>
                        
                        <!-- Info about Caption -->
                        @if($gallery->isFromMediaLibrary())
                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-info-circle"></i> 
                                    Caption is automatically taken from the media library when the video was uploaded.
                                </small>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <!-- Sort Order -->
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" 
                                   value="{{ old('sort_order', $gallery->sort_order) }}" 
                                   min="0">
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ old('status', $gallery->status) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $gallery->status) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Is Exclusive -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="is_exclusive" value="1" 
                                       class="custom-control-input" id="is_exclusive"
                                       {{ old('is_exclusive', $gallery->is_exclusive) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_exclusive">
                                    Is Exclusive
                                </label>
                            </div>
                        </div>

                        <!-- Language -->
                        <div class="form-group">
                            <label>Language</label>
                            <select name="language" class="form-control">
                                @foreach(\App\Models\Language::where('status', 1)->get() as $lang)
                                    <option value="{{ $lang->lang }}" 
                                            {{ old('language', $gallery->language) == $lang->lang ? 'selected' : '' }}>
                                        {{ $lang->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Gallery
                    </button>
                    <a href="{{ route('admin.video-gallery.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Media Library Modal -->
@include('admin.media-library.partials.media-modal')

@push('scripts')
<script>
    function toggleSourceType() {
        const sourceType = $('input[name="source_type"]:checked').val();
        
        if (sourceType === 'media') {
            $('#mediaSourceSection').show();
            $('#externalSourceSection').hide();
        } else {
            $('#mediaSourceSection').hide();
            $('#externalSourceSection').show();
        }
    }

    // Handle media selection from modal for edit
    window.selectMediaForGallery = function(media) {
        if (media.file_type !== 'video') {
            Swal.fire('Error', 'Please select a video', 'error');
            return;
        }

        $('#selectedMediaId').val(media.id);
        $('#selectedMediaName').val(media.title || 'Selected Video');
        $('#mediaLibraryModal').modal('hide');
        
        // Update preview
        $('form').prepend(`
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                Video selected: <strong>${media.title || 'Untitled'}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);
    };
</script>
@endpush
@endsection

