@extends('frontend.layouts.master')

@section('title', 'AP Photo Gallery - ' . config('app.name'))

@section('content')
<!-- Include Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<section class="pb-80">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Breadcrumb -->
                <ul class="breadcrumbs bg-light mb-4">
                    <li class="breadcrumbs__item">
                        <a href="{{ url('/') }}" class="breadcrumbs__url">
                            <i class="fa fa-home"></i> {{ __('frontend.Home') }}</a>
                    </li>
                    <li class="breadcrumbs__item">
                        <a href="{{ route('user.dashboard') }}" class="breadcrumbs__url">{{ __('frontend.My Dashboard') }}</a>
                    </li>
                    <li class="breadcrumbs__item">
                        <a href="javascript:;" class="breadcrumbs__url">AP Photo Gallery</a>
                    </li>
                </ul>
                <!-- End breadcrumb -->

                <div class="wrap__about-us">
                    <div class="row">
                        <!-- Dashboard Sidebar Sidebar -->
                        <div class="col-md-3">
                            <div class="list-group mb-4">
                                <a href="{{ route('user.dashboard') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-chart-line mr-2"></i> Statistics
                                </a>
                                <a href="{{ route('user.news-list') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-list mr-2"></i> News List
                                </a>
                                <a href="{{ route('user.profile') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-user mr-2"></i> Profile
                                </a>
                            </div>

                            <div class="list-group mb-4 text-left">
                                <div class="list-group-item list-group-item-dark font-weight-bold">
                                    Media Access
                                </div>
                                <a href="{{ route('ap-photo.index') }}" class="list-group-item list-group-item-action active">
                                    <i class="fas fa-camera mr-2"></i> AP Photos
                                </a>
                                @if(auth()->user()->hasSubscriptionAccess('images'))
                                    <a href="{{ route('image-gallery.index') }}" class="list-group-item list-group-item-action">
                                        <i class="fas fa-images mr-2"></i> Image Gallery
                                    </a>
                                @endif
                                @if(auth()->user()->hasSubscriptionAccess('videos'))
                                    <a href="{{ route('video-gallery.index') }}" class="list-group-item list-group-item-action">
                                        <i class="fas fa-video mr-2"></i> Video Gallery
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="col-md-9">
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                                <div class="card-body">
                                    <h2 class="mb-1">AP Photo Gallery</h2>
                                    <p class="text-muted small mb-4">Explore high-quality premium photo collections.</p>

                                    <!-- Top Multi-select Filters -->
                                    <form id="filterForm" class="row">
                                        <div class="col-md-5 mb-3">
                                            <label class="small font-weight-bold text-uppercase text-muted">Categories</label>
                                            <select name="categories[]" id="categoryFilter" class="form-control select2-multi" multiple="multiple" data-placeholder="Select Categories">
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->slug }}">{{ $category->name }}</option>
                                                    @foreach($category->children as $child)
                                                        <option value="{{ $child->slug }}">— {{ $child->name }}</option>
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5 mb-3">
                                            <label class="small font-weight-bold text-uppercase text-muted">Tags</label>
                                            <select name="tags[]" id="tagFilter" class="form-control select2-multi" multiple="multiple" data-placeholder="Select Tags">
                                                @foreach($tags as $tag)
                                                    <option value="{{ $tag->slug }}">#{{ $tag->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3 d-flex align-items-end">
                                            <button type="button" id="resetFilters" class="btn btn-outline-secondary btn-block">Reset</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Photo Grid -->
                            <div class="row" id="photoGrid">
                                @include('frontend.ap-photo.partials._photo_items', ['photos' => $photos])
                            </div>

                            <!-- Loading Spinner -->
                            <div id="loader" class="text-center py-4 d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>

                            <!-- End of Gallery Message -->
                            <div id="noMorePhotos" class="text-center py-4 d-none text-muted">
                                <p><i class="fas fa-check-circle mr-1"></i> You've reached the end of the gallery.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Styling for Select2 to match modern look */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 4px;
        min-height: 45px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--colorPrimary);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-color-rgb, 255, 87, 51), 0.25);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: var(--colorPrimary);
        border: none;
        color: white;
        border-radius: 20px;
        padding: 2px 10px 2px 25px;
        margin-top: 6px;
        position: relative;
        font-size: 13px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 0;
        border-right: none;
        position: absolute;
        left: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: bold;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background: rgba(255,255,255,0.2);
        color: #fff;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Photo Card Overlays */
    .photo-card .ratio {
        background: #f8f9fa;
    }
    .photo-card .photo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.3);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .photo-card:hover .photo-overlay {
        opacity: 1;
    }
    .photo-card:hover img {
        transform: scale(1.1);
    }
    .object-fit-cover {
        object-fit: cover;
    }
</style>

@push('content')
<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 with modern settings
    $('.select2-multi').select2({
        width: '100%',
        closeOnSelect: false,
        placeholder: "Select options",
        allowClear: true
    });

    // Custom styling for Select2 chips
    $('.select2-multi').on('select2:select select2:unselect', function (e) {
        $('.select2-selection__choice').css("background-color", "var(--colorPrimary)");
        $('.select2-selection__choice').css("border", "none");
        $('.select2-selection__choice').css("color", "white");
    });

    let page = 1;
    let loading = false;
    let hasMore = {{ $photos->hasMorePages() ? 'true' : 'false' }};

    function loadMorePhotos() {
        if (loading || !hasMore) return;
        
        loading = true;
        $('#loader').removeClass('d-none');
        page++;

        $.ajax({
            url: "{{ route('ap-photo.index') }}",
            type: "GET",
            data: {
                page: page,
                categories: $('#categoryFilter').val(),
                tags: $('#tagFilter').val()
            },
            success: function(data) {
                if (data.trim().length === 0) {
                    hasMore = false;
                    $('#noMorePhotos').removeClass('d-none');
                } else {
                    $('#photoGrid').append(data);
                }
                $('#loader').addClass('d-none');
                loading = false;
            },
            error: function() {
                loading = false;
                $('#loader').addClass('d-none');
            }
        });
    }

    // Filter Change Handler
    $('#categoryFilter, #tagFilter').on('change', function() {
        page = 1;
        hasMore = true;
        $('#noMorePhotos').addClass('d-none');
        $('#photoGrid').html('');
        $('#loader').removeClass('d-none');

        $.ajax({
            url: "{{ route('ap-photo.index') }}",
            type: "GET",
            data: {
                page: page,
                categories: $('#categoryFilter').val(),
                tags: $('#tagFilter').val()
            },
            success: function(data) {
                if (data.trim().length === 0) {
                    $('#photoGrid').html('<div class="col-12 text-center py-5"><h4>No photos found match your filters.</h4></div>');
                    hasMore = false;
                } else {
                    $('#photoGrid').html(data);
                }
                $('#loader').addClass('d-none');
            }
        });
    });

    // Reset Filters
    $('#resetFilters').on('click', function() {
        $('.select2-multi').val(null).trigger('change');
    });

    // Infinite Scroll Implementation with IntersectionObserver
    const observerOptions = {
        root: null,
        rootMargin: '300px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                loadMorePhotos();
            }
        });
    }, observerOptions);

    if (document.querySelector('#loader')) {
        observer.observe(document.querySelector('#loader'));
    }
});
</script>
@endpush
@endsection
