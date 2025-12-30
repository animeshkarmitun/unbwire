@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Author</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>Create Author
                    @if(isset($selectedLanguage))
                        <span class="badge badge-{{ $selectedLanguage->lang == 'en' ? 'primary' : 'success' }}">
                            {{ $selectedLanguage->name }}
                        </span>
                    @endif
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.author.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="">Language <span class="text-danger">*</span></label>
                        <select name="language" id="language-select" class="form-control select2" {{ isset($selectedLanguage) ? 'readonly' : '' }} required>
                            <option value="">--Select--</option>
                            @foreach ($languages as $lang)
                                <option value="{{ $lang->lang }}" 
                                    {{ (old('language', isset($selectedLanguage) ? $selectedLanguage->lang : '') == $lang->lang) ? 'selected' : '' }}>
                                    {{ $lang->name }}
                                </option>
                            @endforeach
                        </select>
                        @if(isset($selectedLanguage))
                            <input type="hidden" name="language" value="{{ $selectedLanguage->lang }}">
                        @endif
                        @error('language')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">Name <span class="text-danger">*</span></label>
                        <input name="name" type="text" class="form-control" id="name" value="{{ old('name', '') }}" required>
                        @error('name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">Designation</label>
                        <input name="designation" type="text" class="form-control" id="designation" value="{{ old('designation', '') }}" placeholder="e.g., Senior Reporter, Editor">
                        @error('designation')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">Photo</label>
                        <div id="photo-preview" class="image-preview" style="position: relative; min-height: 200px;">
                            <button type="button" id="select-author-photo" class="btn btn-primary" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10; {{ old('photo') ? 'display: none;' : '' }}">
                                <i class="fas fa-images"></i> {{ __('Select from Media Library') }}
                            </button>
                            <button type="button" id="change-author-photo" class="btn btn-sm btn-warning" style="position: absolute; top: 10px; right: 10px; z-index: 10; {{ old('photo') ? '' : 'display: none;' }}">
                                <i class="fas fa-edit"></i> {{ __('Change Photo') }}
                            </button>
                            <input type="hidden" name="photo" id="author-photo-path" value="{{ old('photo', '') }}">
                            <div id="author-photo-preview" style="{{ old('photo') ? 'background-image: url(' . asset(old('photo')) . '); display: block;' : 'display: none;' }} width: 100%; height: 100%; min-height: 200px; background-size: cover; background-position: center; border-radius: 8px;"></div>
                        </div>
                        @error('photo')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">Status</label>
                        <select name="status" id="" class="form-control">
                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Media Library Modal for Editor -->
    @include('admin.media-library.partials.media-modal')
@endsection

@push('scripts')
    <script>
        (function waitForjQuery(callback) {
            if (window.jQuery) {
                callback(window.jQuery);
            } else {
                setTimeout(function() {
                    waitForjQuery(callback);
                }, 50);
            }
        })(function($) {
            $(document).ready(function() {
                // Restore photo preview if old photo exists
                var oldPhoto = $('#author-photo-path').val();
                if (oldPhoto && oldPhoto.trim() !== '') {
                    $('#author-photo-preview').css('background-image', 'url(' + '{{ asset("") }}' + oldPhoto + ')').show();
                    $('#select-author-photo').hide();
                    $('#change-author-photo').show();
                }

                // Author photo selection button
                $('#select-author-photo, #change-author-photo').on('click', function() {
                    if (typeof window.openMediaLibraryForAuthor === 'function') {
                        window.openMediaLibraryForAuthor();
                    } else if (typeof window.openMediaLibrary === 'function') {
                        window.openMediaLibrary(null, 'author');
                    }
                });

                // Listen for media selection from media library (fallback event listener)
                $(document).on('mediaSelected', function(e, media, context) {
                    if (context === 'author') {
                        // This is handled by setAuthorPhoto in media-modal, but kept as fallback
                        var imagePath = media.file_path || media.file_url;
                        if (imagePath) {
                            $('#author-photo-path').val(imagePath);
                            $('#author-photo-preview').css('background-image', 'url(' + media.file_url + ')').show();
                            $('#select-author-photo').hide();
                            $('#change-author-photo').show();
                        }
                    }
                });
            });
        });
    </script>
@endpush

