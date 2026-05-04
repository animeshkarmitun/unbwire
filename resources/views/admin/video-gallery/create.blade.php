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
            <form action="{{ route('admin.video-gallery.store') }}" method="POST" enctype="multipart/form-data" id="videoGalleryForm">
                @csrf

                <!-- Source Type Selection -->
                <div class="form-group">
                    <label>Video Source <span class="text-danger">*</span></label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="source_type" 
                               id="source_media" value="media" checked onchange="toggleSourceType()">
                        <label class="form-check-label" for="source_media">
                            <i class="fas fa-upload"></i> Upload Video
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
                        <!-- Upload Section -->
                        <div id="mediaSourceSection">
                            <div class="form-group">
                                <label>Upload Videos (Multiple) <span class="text-danger">*</span></label>
                                <input type="file" name="uploaded_files[]" class="form-control" multiple accept="video/*">
                                <small class="form-text text-muted">
                                    You can upload multiple video files directly here.
                                </small>
                                @error('uploaded_files')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @error('uploaded_files.*')
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

                        <div class="form-group">
                            <label>Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-control" required>
                                <option value="UNB" {{ old('category', 'UNB') === 'UNB' ? 'selected' : '' }}>UNB</option>
                                <option value="AP" {{ old('category') === 'AP' ? 'selected' : '' }}>AP</option>
                            </select>
                            @error('category')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
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



@push('scripts')
<script>
    function toggleSourceType() {
        const sourceType = $('input[name="source_type"]:checked').val();
        
        if (sourceType === 'media') {
            $('#mediaSourceSection').show();
            $('#externalSourceSection').hide();
            $('input[name="uploaded_files[]"]').attr('required', true);
            $('input[name="video_urls[]"]').removeAttr('required');
        } else {
            $('#mediaSourceSection').hide();
            $('#externalSourceSection').show();
            $('input[name="uploaded_files[]"]').removeAttr('required');
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

    // Form validation
    $('#videoGalleryForm').on('submit', function(e) {
        const sourceType = $('input[name="source_type"]:checked').val();
        const uploadedFilesInput = $('input[name="uploaded_files[]"]')[0];
        const uploadedFilesCount = uploadedFilesInput && uploadedFilesInput.files ? uploadedFilesInput.files.length : 0;
        
        if (sourceType === 'media' && uploadedFilesCount === 0) {
            e.preventDefault();
            Swal.fire('Error', 'Please upload at least one video file.', 'error');
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
    $(document).ready(function() {
        toggleSourceType();
    });
</script>
@endpush
@endsection

