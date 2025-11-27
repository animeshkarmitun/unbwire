@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Add Images to Gallery</h1>
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h4>Create Image Gallery</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.image-gallery.store') }}" method="POST" id="galleryForm">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <!-- Media Selection -->
                        <div class="form-group">
                            <label>Select Images from Media Library <span class="text-danger">*</span></label>
                            <div class="border rounded p-3" style="min-height: 200px; background: #f8f9fa;">
                                <div id="selectedMediaContainer" class="row">
                                    <div class="col-12 text-center py-5">
                                        <p class="text-muted">No images selected</p>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mediaLibraryModal" data-type="image">
                                            <i class="fas fa-images"></i> Select from Media Library
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="media_ids" id="mediaIds" value="">
                            @error('media_ids')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gallery Group -->
                        <div class="form-group">
                            <label>Gallery Group (Optional)</label>
                            <input type="text" name="gallery_slug" class="form-control" 
                                   value="{{ old('gallery_slug') }}" 
                                   placeholder="e.g., home-slider, featured-gallery">
                            <small class="form-text text-muted">
                                Leave empty to auto-generate. Use same slug to group multiple images together.
                            </small>
                        </div>

                        <!-- Title -->
                        <div class="form-group">
                            <label>Title (Optional)</label>
                            <input type="text" name="title" class="form-control" 
                                   value="{{ old('title') }}" 
                                   placeholder="Gallery title">
                            <small class="form-text text-muted">
                                If not provided, will use media title
                            </small>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="3" 
                                      placeholder="Gallery description">{{ old('description') }}</textarea>
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
                    <a href="{{ route('admin.image-gallery.index') }}" class="btn btn-secondary">
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

    // Open media library modal for image selection
    $('#mediaLibraryModal').on('show.bs.modal', function(e) {
        const button = $(e.relatedTarget);
        const type = button.data('type') || 'image';
        
        // Set filter to images only
        $('#mediaTypeFilter').val('image').trigger('change');
    });

    // Handle media selection from modal
    window.selectMediaForGallery = function(media) {
        if (media.file_type !== 'image') {
            Swal.fire('Error', 'Please select an image', 'error');
            return;
        }

        // Check if already selected
        if (selectedMedia.find(m => m.id === media.id)) {
            Swal.fire('Info', 'This image is already selected', 'info');
            return;
        }

        selectedMedia.push(media);
        updateSelectedMediaDisplay();
        $('#mediaLibraryModal').modal('hide');
    };

    function updateSelectedMediaDisplay() {
        const container = $('#selectedMediaContainer');
        const mediaIdsInput = $('#mediaIds');

        if (selectedMedia.length === 0) {
            container.html(`
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No images selected</p>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mediaLibraryModal" data-type="image">
                        <i class="fas fa-images"></i> Select from Media Library
                    </button>
                </div>
            `);
            mediaIdsInput.val('');
            return;
        }

        let html = '<div class="col-12 mb-3"><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#mediaLibraryModal" data-type="image"><i class="fas fa-plus"></i> Add More Images</button></div>';
        
        selectedMedia.forEach((media, index) => {
            html += `
                <div class="col-md-3 col-sm-4 col-6 mb-3" data-media-id="${media.id}">
                    <div class="card">
                        <img src="${media.file_url}" class="card-img-top" style="height: 150px; object-fit: cover;">
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
        mediaIdsInput.val(selectedMedia.map(m => m.id).join(','));
    }

    // Remove media from selection
    $(document).on('click', '.remove-media', function() {
        const index = $(this).data('index');
        selectedMedia.splice(index, 1);
        updateSelectedMediaDisplay();
    });

    // Form validation
    $('#galleryForm').on('submit', function(e) {
        if (selectedMedia.length === 0) {
            e.preventDefault();
            Swal.fire('Error', 'Please select at least one image from media library', 'error');
            return false;
        }
    });
</script>
@endpush
@endsection

