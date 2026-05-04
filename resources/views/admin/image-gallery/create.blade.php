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
            <form action="{{ route('admin.image-gallery.store') }}" method="POST" enctype="multipart/form-data" id="galleryForm">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Upload Images (Multiple) <span class="text-danger">*</span></label>
                            <input type="file" name="uploaded_files[]" class="form-control" multiple accept="image/*" required>
                            <small class="form-text text-muted">
                                You can upload multiple image files directly here.
                            </small>
                            @error('uploaded_files')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            @error('uploaded_files.*')
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
                    <a href="{{ route('admin.image-gallery.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>



@push('scripts')
<script>
    $(document).ready(function() {
        // Form validation
        $('#galleryForm').on('submit', function(e) {
            const uploadedFilesInput = $('input[name="uploaded_files[]"]')[0];
            const uploadedFilesCount = uploadedFilesInput && uploadedFilesInput.files ? uploadedFilesInput.files.length : 0;

            if (uploadedFilesCount === 0) {
                e.preventDefault();
                Swal.fire('Error', 'Please upload at least one image file.', 'error');
                return false;
            }
        });
    });
</script>
@endpush
@endsection

