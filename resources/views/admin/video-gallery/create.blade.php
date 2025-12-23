@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Add Videos to Gallery</h1>
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h4>Create Video Gallery</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.video-gallery.store') }}" method="POST" id="videoGalleryForm">
                @csrf

                <!-- Source Type Selection -->
                <div class="form-group">
                    <label>Video Source <span class="text-danger">*</span></label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="source_type" 
                               id="source_media" value="media" checked onchange="toggleSourceType()">
                        <label class="form-check-label" for="source_media">
                            <i class="fas fa-database"></i> Media Library
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="source_type" 
                               id="source_external" value="external" onchange="toggleSourceType()">
                        <label class="form-check-label" for="source_external">
                            <i class="fas fa-link"></i> External Link (YouTube, Facebook, Vimeo, etc.)
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <!-- Media Library Selection -->
                        <div id="mediaSourceSection">
                            <div class="form-group">
                                <label>Select Videos from Media Library <span class="text-danger">*</span></label>
                                <div class="border rounded p-3" style="min-height: 200px; background: #f8f9fa;">
                                    <div id="selectedMediaContainer" class="row">
                                        <div class="col-12 text-center py-5">
                                            <p class="text-muted">No videos selected</p>
                                            <button type="button" class="btn btn-primary" 
                                                    data-toggle="modal" 
                                                    data-target="#mediaLibraryModal" 
                                                    data-type="video">
                                                <i class="fas fa-video"></i> Select from Media Library
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="mediaIdsContainer"></div>
                                @error('media_ids')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- External Video URLs -->
                        <div id="externalSourceSection" style="display: none;">
                            <div class="form-group">
                                <label>Video URLs <span class="text-danger">*</span></label>
                                <div id="videoUrlsContainer">
                                    <div class="input-group mb-2">
                                        <input type="url" name="video_urls[]" class="form-control" 
                                               placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-success add-video-url">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    Supported platforms: YouTube, Vimeo, Facebook, etc. Add multiple URLs by clicking the + button.
                                </small>
                                @error('video_urls')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Gallery Group -->
                        <div class="form-group">
                            <label>Gallery Group (Optional)</label>
                            <input type="text" name="gallery_slug" class="form-control" 
                                   value="{{ old('gallery_slug') }}" 
                                   placeholder="e.g., featured-videos, home-videos">
                            <small class="form-text text-muted">
                                Leave empty to auto-generate. Use same slug to group multiple videos together.
                            </small>
                        </div>

                        <!-- Title -->
                        <div class="form-group">
                            <label>Title (Optional)</label>
                            <input type="text" name="title" class="form-control" 
                                   value="{{ old('title') }}" 
                                   placeholder="Video title">
                            <small class="form-text text-muted">
                                If not provided, will use media title or URL
                            </small>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="3" 
                                      placeholder="Video description">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Status -->
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Is Exclusive -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="is_exclusive" value="1" 
                                       class="custom-control-input" id="is_exclusive"
                                       {{ old('is_exclusive') ? 'checked' : '' }}>
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
                                            {{ old('language', getLangauge()) == $lang->lang ? 'selected' : '' }}>
                                        {{ $lang->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Gallery
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
    let selectedMedia = [];

    function toggleSourceType() {
        const sourceType = $('input[name="source_type"]:checked').val();
        
        if (sourceType === 'media') {
            $('#mediaSourceSection').show();
            $('#externalSourceSection').hide();
            $('input[name="media_ids[]"]').attr('required', true);
            $('input[name="video_urls[]"]').removeAttr('required');
        } else {
            $('#mediaSourceSection').hide();
            $('#externalSourceSection').show();
            $('input[name="media_ids[]"]').removeAttr('required');
            $('input[name="video_urls[]"]').attr('required', true);
        }
    }

    // Add more video URL inputs
    $(document).on('click', '.add-video-url', function() {
        const newInput = `
            <div class="input-group mb-2">
                <input type="url" name="video_urls[]" class="form-control" 
                       placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-video-url">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        $('#videoUrlsContainer').append(newInput);
    });

    // Remove video URL input
    $(document).on('click', '.remove-video-url', function() {
        if ($('#videoUrlsContainer .input-group').length > 1) {
            $(this).closest('.input-group').remove();
        } else {
            Swal.fire('Info', 'You must have at least one video URL', 'info');
        }
    });

    // Open media library modal for video selection
    $('#mediaLibraryModal').on('show.bs.modal', function(e) {
        const button = $(e.relatedTarget);
        const type = button.data('type') || 'video';
        
        // Set filter to videos only
        $('#mediaTypeFilter').val('video').trigger('change');
    });

    // Handle media selection from modal
    window.selectMediaForGallery = function(media) {
        if (media.file_type !== 'video') {
            Swal.fire('Error', 'Please select a video', 'error');
            return;
        }

        // Check if already selected
        if (selectedMedia.find(m => m.id === media.id)) {
            Swal.fire('Info', 'This video is already selected', 'info');
            return;
        }

        selectedMedia.push(media);
        updateSelectedMediaDisplay();
        $('#mediaLibraryModal').modal('hide');
    };

    function updateSelectedMediaDisplay() {
        const container = $('#selectedMediaContainer');
        const mediaIdsContainer = $('#mediaIdsContainer');

        if (selectedMedia.length === 0) {
            container.html(`
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No videos selected</p>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mediaLibraryModal" data-type="video">
                        <i class="fas fa-video"></i> Select from Media Library
                    </button>
                </div>
            `);
            mediaIdsContainer.empty();
            return;
        }

        let html = '<div class="col-12 mb-3"><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#mediaLibraryModal" data-type="video"><i class="fas fa-plus"></i> Add More Videos</button></div>';
        
        selectedMedia.forEach((media, index) => {
            html += `
                <div class="col-md-3 col-sm-4 col-6 mb-3" data-media-id="${media.id}">
                    <div class="card">
                        <div class="d-flex align-items-center justify-content-center bg-light" style="height: 150px;">
                            <i class="fas fa-video fa-3x text-primary"></i>
                        </div>
                        <div class="card-body p-2">
                            <p class="card-text small mb-1">${media.title || 'Untitled'}</p>
                            <button type="button" class="btn btn-sm btn-danger btn-block remove-media" data-index="${index}">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        container.html(html);
        
        // Update hidden inputs for media_ids array
        mediaIdsContainer.empty();
        selectedMedia.forEach((media) => {
            mediaIdsContainer.append(`<input type="hidden" name="media_ids[]" value="${media.id}">`);
        });
    }

    // Remove media from selection
    $(document).on('click', '.remove-media', function() {
        const index = $(this).data('index');
        selectedMedia.splice(index, 1);
        updateSelectedMediaDisplay();
    });

    // Form validation
    $('#videoGalleryForm').on('submit', function(e) {
        const sourceType = $('input[name="source_type"]:checked').val();
        
        if (sourceType === 'media' && selectedMedia.length === 0) {
            e.preventDefault();
            Swal.fire('Error', 'Please select at least one video from media library', 'error');
            return false;
        }
        
        if (sourceType === 'external') {
            const videoUrls = $('input[name="video_urls[]"]').filter(function() {
                return $(this).val().trim() !== '';
            });
            
            if (videoUrls.length === 0) {
                e.preventDefault();
                Swal.fire('Error', 'Please add at least one video URL', 'error');
                return false;
            }
        }
    });

    // Initialize
    toggleSourceType();
</script>
@endpush
@endsection

