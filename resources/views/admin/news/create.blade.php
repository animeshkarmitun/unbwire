@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('News') }}</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('Create News') }} 
                    @if(isset($selectedLanguage))
                        <span class="badge badge-{{ $selectedLanguage->lang == 'en' ? 'primary' : 'success' }}">
                            {{ $selectedLanguage->name }}
                        </span>
                    @endif
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">{{ __('Language') }}</label>
                        <select name="language" id="language-select" class="form-control select2" {{ isset($selectedLanguage) ? 'readonly' : '' }}>
                            <option value="">--{{ __('Select') }}--</option>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">{{ __('Category') }}</label>
                                <select name="category" id="category" class="form-control select2">
                                    <option value="">--{{ __('Select') }}---</option>
                                    @if(old('category'))
                                        @php
                                            $oldCategory = \App\Models\Category::find(old('category'));
                                        @endphp
                                        @if($oldCategory && $oldCategory->parent_id === null)
                                            <option value="{{ $oldCategory->id }}" selected>{{ $oldCategory->name }}</option>
                                        @endif
                                    @endif
                                </select>
                                @error('category')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">{{ __('Sub-Category') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                <select name="subcategory" id="subcategory" class="form-control select2" disabled>
                                    <option value="">--{{ __('Select') }}--</option>
                                    @if(old('subcategory'))
                                        @php
                                            $oldSubcategory = \App\Models\Category::find(old('subcategory'));
                                        @endphp
                                        @if($oldSubcategory)
                                            <option value="{{ $oldSubcategory->id }}" selected>{{ $oldSubcategory->name }}</option>
                                        @endif
                                    @endif
                                </select>
                                @error('subcategory')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                                <small class="form-text text-muted">{{ __('Please select a category first') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Author') }}</label>
                        <select name="author_id" id="author_id" class="form-control select2">
                            <option value="">--{{ __('Select') }}--</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                                    {{ $author->name }}@if($author->designation) - {{ $author->designation }}@endif
                                </option>
                            @endforeach
                        </select>
                        @error('author_id')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>


                    <div class="form-group">
                        <label for="">{{ __('Feature Image') }}</label>
                        <div id="image-preview" class="image-preview" style="position: relative; min-height: 200px;">
                            <button type="button" id="select-featured-image" class="btn btn-primary" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10; {{ old('image') ? 'display: none;' : '' }}">
                                <i class="fas fa-images"></i> {{ __('Select from Media Library') }}
                            </button>
                            <button type="button" id="change-featured-image" class="btn btn-sm btn-warning" style="position: absolute; top: 10px; right: 10px; z-index: 10; {{ old('image') ? '' : 'display: none;' }}">
                                <i class="fas fa-edit"></i> {{ __('Change Image') }}
                            </button>
                            <input type="hidden" name="image" id="featured-image-path" value="{{ old('image', '') }}">
                            <div id="featured-image-preview" style="{{ old('image') ? 'background-image: url(' . asset(old('image')) . '); display: block;' : 'display: none;' }} width: 100%; height: 100%; min-height: 200px; background-size: cover; background-position: center;"></div>
                        </div>
                        @error('image')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Title') }}</label>
                        <input name="title" type="text" class="form-control" id="name" value="{{ old('title', '') }}">
                        @error('title')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Content') }}</label>
                        <textarea name="content" class="summernote-simple">{{ old('content', '') }}</textarea>
                        @error('content')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="">{{ __('Tags') }}</label>
                        <input name="tags" type="text" class="form-control inputtags" value="{{ old('tags', '') }}">
                        @error('tags')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Meta Title') }}</label>
                        <input name="meta_title" type="text" class="form-control" id="name" value="{{ old('meta_title', '') }}">
                        @error('meta_title')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Meta Description') }}</label>
                        <textarea name="meta_description" class="form-control">{{ old('meta_description', '') }}</textarea>
                        @error('meta_description')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="control-label">{{ __('Status') }}</div>
                                <label class="custom-switch mt-2">
                                    <input value="1" type="checkbox" name="status" class="custom-switch-input" {{ old('status') == '1' ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                </label>
                            </div>
                        </div>

                        @if (canAccess(['news status', 'news all-access']))

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="control-label">{{ __('Is Breaking News') }}</div>
                                <label class="custom-switch mt-2">
                                    <input value="1" type="checkbox" name="is_breaking_news"
                                        class="custom-switch-input" {{ old('is_breaking_news') == '1' ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="control-label">{{ __('Show At Slider') }}</div>
                                <label class="custom-switch mt-2">
                                    <input value="1" type="checkbox" name="show_at_slider" class="custom-switch-input" {{ old('show_at_slider') == '1' ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="control-label">{{ __('Show At Popular') }}</div>
                                <label class="custom-switch mt-2">
                                    <input value="1" type="checkbox" name="show_at_popular"
                                        class="custom-switch-input" {{ old('show_at_popular') == '1' ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="control-label">{{ __('Is Exclusive') }}</div>
                                <label class="custom-switch mt-2">
                                    <input value="1" type="checkbox" name="is_exclusive"
                                        class="custom-switch-input" {{ old('is_exclusive') == '1' ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                </label>
                            </div>
                        </div>
                        @endif

                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
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
                // Restore image preview if old image exists
                var oldImage = $('#featured-image-path').val();
                if (oldImage && oldImage.trim() !== '') {
                    $('#featured-image-preview').css('background-image', 'url(' + '{{ asset("") }}' + oldImage + ')').show();
                    $('#select-featured-image').hide();
                    $('#change-featured-image').show();
                }

                // Restore category if old category exists
                var oldCategory = '{{ old("category") }}';
                var oldLanguage = '{{ old("language") }}';
                if (oldLanguage && oldCategory) {
                    // Trigger category load for the selected language
                    $('#language-select').trigger('change');
                    // Wait a bit for categories to load, then select the old category
                    setTimeout(function() {
                        $('#category').val(oldCategory).trigger('change');
                    }, 500);
                }

                $('#language-select').on('change', function() {
                    let lang = $(this).val();
                    $.ajax({
                        method: 'GET',
                        url: "{{ route('admin.fetch-news-category') }}",
                        data: {
                            lang: lang
                        },
                        success: function(data) {
                            $('#category').html("");
                            $('#category').html(
                                `<option value="">---{{ __('Select') }}---</option>`);

                            $.each(data, function(index, data) {
                                var selected = (oldCategory && oldCategory == data.id) ? 'selected' : '';
                                $('#category').append(
                                    `<option value="${data.id}" ${selected}>${data.name}</option>`)
                            })
                            
                            // Reset subcategory when language changes
                            $('#subcategory').html('<option value="">--{{ __('Select') }}--</option>').prop('disabled', true);

                        },
                        error: function(error) {
                            console.log(error);
                        }
                    })
                })

                // Load subcategories when category is selected
                $('#category').on('change', function() {
                    let categoryId = $(this).val();
                    let oldSubcategory = '{{ old("subcategory") }}';
                    
                    if (categoryId) {
                        $.ajax({
                            method: 'GET',
                            url: "{{ route('admin.fetch-news-subcategories') }}",
                            data: {
                                category_id: categoryId
                            },
                            success: function(data) {
                                $('#subcategory').html("");
                                $('#subcategory').html(
                                    `<option value="">--{{ __('Select') }}--</option>`);
                                
                                if (data.length > 0) {
                                    $.each(data, function(index, subcategory) {
                                        var selected = (oldSubcategory && oldSubcategory == subcategory.id) ? 'selected' : '';
                                        $('#subcategory').append(
                                            `<option value="${subcategory.id}" ${selected}>${subcategory.name}</option>`)
                                    })
                                    $('#subcategory').prop('disabled', false);
                                } else {
                                    $('#subcategory').prop('disabled', true);
                                    $('#subcategory').html(
                                        `<option value="">--{{ __('No subcategories available') }}--</option>`);
                                }
                            },
                            error: function(error) {
                                console.log(error);
                                $('#subcategory').html('<option value="">--{{ __('Select') }}--</option>').prop('disabled', true);
                            }
                        })
                    } else {
                        $('#subcategory').html('<option value="">--{{ __('Select') }}--</option>').prop('disabled', true);
                    }
                })

                // Featured image selection button
                $('#select-featured-image, #change-featured-image').on('click', function() {
                    if (typeof window.openMediaLibraryForFeatured === 'function') {
                        window.openMediaLibraryForFeatured();
                    } else if (typeof window.openMediaLibrary === 'function') {
                        // Use editor function but mark as featured image mode
                        window.openMediaLibrary(null, 'featured');
                    }
                });

                // Intercept picture button click to show media library
                $(document).on('click', '.note-btn[data-event="showImageDialog"]', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var $editor = $('.summernote-simple');
                    if ($editor.length) {
                        if (typeof window.openMediaLibrary === 'function') {
                            window.openMediaLibrary($editor);
                        } else if (typeof showImageDialog === 'function') {
                            showImageDialog($editor);
                        }
                    }
                    return false;
                });
                
                // Also intercept after Summernote initializes (more reliable)
                setTimeout(function() {
                    $('.summernote-simple').each(function() {
                        var $editor = $(this);
                        var $toolbar = $editor.summernote('getToolbar');
                        if ($toolbar && $toolbar.length) {
                            $toolbar.find('[data-event="showImageDialog"]').off('click').on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                if (typeof window.openMediaLibrary === 'function') {
                                    window.openMediaLibrary($editor);
                                } else if (typeof showImageDialog === 'function') {
                                    showImageDialog($editor);
                                }
                                return false;
                            });
                        }
                    });
                    
                    // Restore Summernote content if old content exists
                    @if(old('content'))
                    var oldContent = @json(old('content'));
                    if (oldContent && oldContent.trim() !== '') {
                        $('.summernote-simple').summernote('code', oldContent);
                    }
                    @endif
                }, 500);
            }); // End document ready
        });
    </script>
    <style>
        /* Style for image captions */
        figure {
            margin: 1em 0;
            display: inline-block;
        }
        figure img {
            max-width: 100%;
            height: auto;
        }
        figcaption {
            font-size: 0.9em;
            color: #666;
            text-align: center;
            margin-top: 0.5em;
            font-style: italic;
        }
    </style>
@endpush
