@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Edit Image Gallery</h1>
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h4>Update Image Gallery</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.image-gallery.update', $gallery->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        <!-- Current Image -->
                        <div class="form-group">
                            <label>Current Image</label>
                            @if($gallery->media)
                                <div class="mb-3">
                                    <img src="{{ $gallery->media->file_url }}" 
                                         alt="{{ $gallery->alt_text }}" 
                                         class="img-fluid" 
                                         style="max-width: 300px; border-radius: 4px;">
                                </div>
                            @endif
                        </div>

                        <!-- Select New Image -->
                        <div class="form-group">
                            <label>Change Image (Optional)</label>
                            <div class="input-group">
                                <input type="hidden" name="media_id" id="selectedMediaId" value="{{ $gallery->media_id }}">
                                <input type="text" class="form-control" id="selectedMediaName" 
                                       value="{{ $gallery->media ? $gallery->media->title : 'No image selected' }}" 
                                       readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" 
                                            data-toggle="modal" 
                                            data-target="#mediaLibraryModal" 
                                            data-type="image">
                                        <i class="fas fa-images"></i> Select Image
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Leave unchanged to keep current image
                            </small>
                        </div>

                        <!-- Gallery Group -->
                        <div class="form-group">
                            <label>Gallery Group</label>
                            <input type="text" name="gallery_slug" class="form-control" 
                                   value="{{ old('gallery_slug', $gallery->gallery_slug) }}" 
                                   placeholder="e.g., home-slider, featured-gallery">
                        </div>

                        <!-- Title -->
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" 
                                   value="{{ old('title', $gallery->title) }}" 
                                   placeholder="Gallery title">
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3" 
                                      placeholder="Gallery description">{{ old('description', $gallery->description) }}</textarea>
                        </div>
                        
                        <!-- Info about Alt Text and Caption -->
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle"></i> 
                                Alt Text and Caption are automatically taken from the media library when the image was uploaded.
                            </small>
                        </div>
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
    // Handle media selection from modal for edit
    window.selectMediaForGallery = function(media) {
        if (media.file_type !== 'image') {
            Swal.fire('Error', 'Please select an image', 'error');
            return;
        }

        $('#selectedMediaId').val(media.id);
        $('#selectedMediaName').val(media.title || 'Selected Image');
        $('#mediaLibraryModal').modal('hide');
        
        // Update preview
        $('form').prepend(`
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                Image selected: <strong>${media.title || 'Untitled'}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);
    };
</script>
@endpush
@endsection

