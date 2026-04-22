@extends('frontend.layouts.master')

@section('title', ($photo->category->name ?? 'AP Photo') . ' - AP Photo Gallery')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ap-photo.index') }}">AP Photo Gallery</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $photo->category->name ?? 'Photo Details' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Photo Player / Gallery -->
        <div class="col-lg-8">
            <div id="apPhotoCarousel" class="carousel slide bg-dark rounded overflow-hidden shadow-lg" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($photo->items as $item)
                        <div class="carousel-item {{ $item->id == $activeItem->id ? 'active' : '' }}" data-item-id="{{ $item->id }}">
                            <div class="d-flex align-items-center justify-content-center min-vh-50">
                                <img src="{{ $item->file_url }}" class="d-block w-100 img-fluid" alt="{{ $photo->category->name }}">
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- Removed carousel controls over image as requested --}}
            </div>

            <!-- Thumbnails -->
            <div class="row mt-3 g-2">
                @foreach($photo->items as $item)
                    <div class="col-3 col-md-2">
                        <div class="thumbnail-wrapper rounded overflow-hidden cursor-pointer {{ $item->id == $activeItem->id ? 'active' : '' }}" 
                             onclick="$('#apPhotoCarousel').carousel({{ $loop->index }})">
                            <img src="{{ $item->file_url }}" class="img-fluid w-100 h-100 object-fit-cover" alt="thumb">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Details -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card border-0 shadow-sm border-top-primary">
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ $photo->category->name ?? 'Uncategorized' }}</span>
                        @if($photo->subCategory)
                            <span class="badge bg-info">{{ $photo->subCategory->name }}</span>
                        @endif
                    </div>
                    <h3 class="mb-3">{{ $photo->category->name }}</h3>
                    <div class="text-muted small mb-3">
                        <i class="far fa-calendar-alt me-1"></i> {{ $photo->created_at->format('M d, Y') }}
                        <span class="mx-2">|</span>
                        <i class="far fa-user me-1"></i> {{ $photo->user->name ?? 'Admin' }}
                    </div>
                    
                    <div class="description mb-4">
                        <p>{{ $photo->description ?? 'No description available for this photo set.' }}</p>
                    </div>

                    @if($photo->tags->isNotEmpty())
                        <div class="tags mb-4">
                            <h6 class="mb-2">Tags:</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($photo->tags as $tag)
                                    <a href="{{ route('ap-photo.index', ['tag' => $tag->slug]) }}" class="badge rounded-pill bg-light text-dark border text-decoration-none shadow-sm">#{{ $tag->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="d-grid gap-2 mb-4">
                        <a href="{{ route('ap-photo.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Gallery
                        </a>
                    </div>

                    <!-- Navigation Between Photos -->
                    <div class="d-flex justify-content-between gap-2 mb-4">
                        @if($prev)
                            <a href="{{ route('ap-photo.show', $prev->id) }}" class="btn btn-outline-primary flex-grow-1">
                                <i class="fas fa-chevron-left me-1"></i> Prev Photo
                            </a>
                        @else
                            <button class="btn btn-outline-secondary flex-grow-1" disabled>
                                <i class="fas fa-chevron-left me-1"></i> Prev Photo
                            </button>
                        @endif

                        @if($next)
                            <a href="{{ route('ap-photo.show', $next->id) }}" class="btn btn-outline-primary flex-grow-1">
                                Next Photo <i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        @else
                            <button class="btn btn-outline-secondary flex-grow-1" disabled>
                                Next Photo <i class="fas fa-chevron-right ms-1"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Download Section -->
                    <div class="btn-group d-grid mb-3">
                        <a href="#" class="btn btn-success download-link" data-format="jpg" id="mainDownloadBtn">
                            <i class="fas fa-download me-1"></i> Download JPG
                        </a>
                        <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right shadow-sm w-100">
                            <li><a class="dropdown-item download-link" href="#" data-format="png"><i class="far fa-file-image me-2 text-info"></i> Download as PNG</a></li>
                            <li><a class="dropdown-item download-link" href="#" data-format="webp"><i class="far fa-file-image me-2 text-success"></i> Download as WebP</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a class="dropdown-item download-link" href="#" data-format="pdf"><i class="far fa-file-pdf me-2 text-danger"></i> Download as PDF</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .min-vh-50 {
        min-height: 50vh;
    }
    .object-fit-cover {
        object-fit: cover;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .thumbnail-wrapper {
        border: 2px solid transparent;
        transition: border-color 0.3s;
        aspect-ratio: 1 / 1;
    }
    .thumbnail-wrapper.active, .thumbnail-wrapper:hover {
        border-color: var(--colorPrimary);
    }
    .border-top-primary {
        border-top: 4px solid var(--colorPrimary) !important;
    }
</style>

@push('content')
<script>
$(document).ready(function() {
    function updateDownloadLinks() {
        var activeItem = $('.carousel-item.active');
        var activeItemId = activeItem.data('item-id');
        
        $('.download-link').each(function() {
            var format = $(this).data('format');
            var url = "{{ route('ap-photo.download', [':itemId', ':format']) }}";
            url = url.replace(':itemId', activeItemId).replace(':format', format);
            $(this).attr('href', url);
        });
    }

    // Initialize links
    updateDownloadLinks();

    // Thumbnails active state sync
    $('#apPhotoCarousel').on('slid.bs.carousel', function () {
        updateDownloadLinks();
        
        var idx = $('.carousel-item.active').index();
        $('.thumbnail-wrapper').removeClass('active');
        $('.thumbnail-wrapper').eq(idx).addClass('active');
    });
});
</script>
@endpush
@endsection
